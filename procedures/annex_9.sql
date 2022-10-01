DROP PROCEDURE IF EXISTS annex_9;

DELIMITER $$

CREATE PROCEDURE annex_9(
    v_initial_date DATE,
    v_final_date DATE,
    v_location_id INT,
    v_business_id INT,
    v_no_annex INT
)

BEGIN

	SELECT
        -- COLUMNA A: NIT SUJETO
		SUBSTRING(REPLACE(c.tax_number, '-', ''), 1, 14) AS nit,

        -- COLUMNA B: FECHA DE EMISION DEL DOCUMENTO
        DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,

        -- COLUMNA C: TIPO DOCUMENTO
        SUBSTRING(dt.document_type_number, 1, 2) AS document_type,

        -- COLUMNA D: NUMERO DE RESOLUCION
        SUBSTRING(UPPER(REPLACE(t.resolution, '-', '')), 1, 100) AS resolution,

        -- COLUMNA E: SERIE DE DOCUMENTO
        SUBSTRING(UPPER(t.serie), 1, 100) AS serie,

        -- COLUMNA F: NUMERO DE DOCUMENTO
		SUBSTRING(t.correlative, 1, 100) AS no_document,

        -- COLUMNA G: MONTO SUJETO
		t.total_before_tax AS amount,

        -- COLUMNA H: MONTO DE LA PERCEPCION 1% DE IVA
        t.total_before_tax * 0.01 AS perception,

        -- COLUMNA I: NUMERO DE ANEXO
        v_no_annex AS no_annex
	FROM transactions AS t
    LEFT JOIN customers AS c
        ON t.customer_id = c.id
    LEFT JOIN document_types AS dt
		ON dt.id = t.document_types_id
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
        AND ((dt.tax_inc = 0 AND t.final_total < t.total_before_tax)
            OR (dt.tax_inc = 1 AND t.final_total < t.total_before_tax * 1.13))
	ORDER BY DATE(t.transaction_date), CONVERT(t.correlative, UNSIGNED INTEGER);

END; $$

DELIMITER ;