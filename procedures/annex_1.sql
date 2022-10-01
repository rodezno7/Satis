DROP PROCEDURE IF EXISTS annex_1;

DELIMITER $$

CREATE PROCEDURE annex_1(
    v_initial_date DATE,
    v_final_date DATE,
    v_location_id INT,
    v_business_id INT,
    v_no_annex INT
)

BEGIN

	SELECT
        -- COLUMNA A: FECHA DE EMISION DEL DOCUMENTO
		DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,

        -- COLUMNA B: CLASE DE DOCUMENTO
        dc.code AS document_class,

        -- COLUMNA C: TIPO DE DOCUMENTO
        SUBSTRING(dt.document_type_number, 1, 2) AS document_type,

        -- COLUMNA D: NUMERO DE RESOLUCION
        SUBSTRING(UPPER(REPLACE(t.resolution, '-', '')), 1, 100) AS resolution,

        -- COLUMNA E: NUMERO DE SERIE DE DOCUMENTO
        SUBSTRING(UPPER(t.serie), 1, 100) AS serie,

        -- COLUMNA F: NUMERO DE DOCUMENTO
		SUBSTRING(t.correlative, 1, 100) AS no_correlative,

        -- COLUMNA G: NUMERO DE CONTROL INTERNO
		SUBSTRING(t.id, 1, 100) AS no_id,

        -- COLUMNA H: NIT O NRC
        SUBSTRING(REPLACE(c.tax_number, '-', ''), 1, 14) AS nit,
        SUBSTRING(REPLACE(c.reg_number, '-', ''), 1, 14) AS nrc,

        -- COLUMNA I: NOMBRE, RAZON SOCIAL O DENOMINACION
        c.name,
        c.business_name,

        -- COLUMNA J: VENTAS EXENTAS
        IF (c.is_exempt = 1, t.total_before_tax, 0) AS exempt_sales,

        -- COLUMNA K: VENTAS NO SUJETAS

        -- COLUMNA L: VENTAS GRAVADAS LOCALES
        IF (c.is_exempt = 0, t.total_before_tax, 0) AS taxed_sales,

        -- COLUMNA M: DEBITO FISCAL
        t.total_before_tax * 0.13 AS fiscal_debit,

        -- COLUMNA N: VENTAS A CUENTA DE TERCEROS NO DOMICILIADOS

        -- COLUMNA O: DEBITO FISCAL POR VENTA A CUENTA DE TERCEROS

        -- COLUMNA P: TOTAL VENTAS
        t.final_total AS total,

        -- COLUMNA Q: NUMERO DE ANEXO
        v_no_annex AS no_annex
	FROM transactions AS t
    LEFT JOIN customers AS c
        ON t.customer_id = c.id
    LEFT JOIN document_types AS dt
		ON dt.id = t.document_types_id
	LEFT JOIN document_classes AS dc
		ON dc.id = dt.document_class_id
	WHERE DATE(t.transaction_date) BETWEEN v_initial_date AND v_final_date
		AND t.business_id = v_business_id
        AND (v_location_id = 0 OR t.location_id = v_location_id)
		AND t.document_types_id IN (
			SELECT id
			FROM document_types
			WHERE business_id = v_business_id
				AND is_active = 1
				AND document_type_number IN ('03', '05', '06')
		)
		AND t.type = 'sell'
        AND t.status = 'final'
	ORDER BY DATE(t.transaction_date), CONVERT(t.correlative, UNSIGNED INTEGER);

END; $$

DELIMITER ;