DROP PROCEDURE IF EXISTS annex_3;

DELIMITER $$

CREATE PROCEDURE annex_3(
    v_initial_date DATE,
    v_final_date DATE,
    v_location_id INT,
    v_business_id INT,
    v_no_annex INT
)

BEGIN

    SELECT
        -- COLUMNA A: FECHA DE EMISION
		DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,

        -- COLUMNA B: CLASE DE DOCUMENTO
        dc.code AS document_class,

        -- COLUMNA C: TIPO DE DOCUMENTO
        SUBSTRING(dt.document_type_number, 1, 2) AS document_type,

        -- COLUMNA D: NUMERO DE DOCUMENTO
		SUBSTRING(t.ref_no, 1, 100) AS no_document,

        -- COLUMNA E: NIT O NRC DEL PROVEEDOR
        SUBSTRING(REPLACE(c.nit, '-', ''), 1, 14) AS nit,
        SUBSTRING(REPLACE(c.tax_number, '-', ''), 1, 14) AS nrc,

        -- COLUMNA F: NOMBRE DEL PROVEEDOR
        c.name,
        c.supplier_business_name,

        -- COLUMNA G: COMPRAS INTERNAS EXENTAS Y/O NO SUJETAS
        IF (
            c.tax_number IS NOT NULL,
            IF (c.is_exempt = 1, t.total_before_tax, 0),
            0
        ) AS exempt_internals,

        -- COLUMNA H: INTERNACIONES EXENTAS Y/O NO SUJETAS

        -- COLUMNA I: IMPORTACIONES EXENTAS Y/O NO SUJETAS

        -- COLUMNA J: COMPRAS INTERNAS GRAVADAS
        IF (
            c.tax_number IS NOT NULL,
            IF (c.is_exempt = 0, t.total_before_tax, 0),
            0
        ) AS taxed_internals,

        -- COLUMNA K: INTERNACIONES GRAVADAS DE BIENES

        -- COLUMNA L: IMPORTACIONES GRAVADAS DE BIENES
        IF (c.tax_number IS NULL, t.total_before_tax, 0) AS taxed_imports,

        -- COLUMNA M: IMPORTACIONES GRAVADAS DE SERVICIOS

        -- COLUMNA N: CREDITO FISCAL
        t.total_before_tax * 0.13 AS fiscal_credit,

        -- COLUMNA O: TOTAL DE COMPRAS
        t.final_total AS total,

        -- COLUMNA P: NUMERO DE ANEXO
        v_no_annex AS no_annex
	FROM transactions AS t
    JOIN contacts AS c
        ON t.contact_id = c.id
    LEFT JOIN document_types AS dt
		ON dt.id = t.document_types_id
	LEFT JOIN document_classes AS dc
		ON dc.id = dt.document_class_id
	WHERE DATE(t.transaction_date) BETWEEN v_initial_date AND v_final_date
		AND t.business_id = v_business_id
        AND (v_location_id = 0 OR t.location_id = v_location_id)
		AND (t.type = 'purchase' OR t.type = 'expense')
        AND t.document_types_id IN (
			SELECT id
			FROM document_types
			WHERE business_id = v_business_id
				AND is_active = 1
				AND document_type_number IN ('03', '05', '06', '11', '12', '13')
		)
	ORDER BY DATE(t.transaction_date), CONVERT(t.ref_no, UNSIGNED INTEGER);

END; $$

DELIMITER ;