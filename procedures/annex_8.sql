DROP PROCEDURE IF EXISTS annex_8;

DELIMITER $$

CREATE PROCEDURE annex_8(
    v_initial_date DATE,
    v_final_date DATE,
    v_location_id INT,
    v_business_id INT,
    v_no_annex INT
)

BEGIN

    SELECT
        -- COLUMNA A: NIT AGENTE
		SUBSTRING(REPLACE(c.nit, '-', ''), 1, 14) AS nit,

        -- COLUMNA B: FECHA DE EMISION
        DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,

        -- COLUMNA C: TIPO DE DOCUMENTO
        SUBSTRING(dt.document_type_number, 1, 2) AS document_type,

        -- COLUMNA D: SERIE DE DOCUMENTO
		SUBSTRING(UPPER(t.serie), 1, 100) AS serie,

        -- COLUMNA E: NUMERO DE DOCUMENTO
        SUBSTRING(t.ref_no, 1, 100) AS no_document,

        -- COLUMNA F: MONTO SUJETO
        t.total_before_tax AS amount,

        -- COLUMNA G: MONTO DE LA PERCEPCION
        t.total_before_tax * 0.01 AS perception,

        -- COLUMNA H: NUMERO DE ANEXO
        v_no_annex AS no_annex
	FROM transactions AS t
    JOIN contacts AS c
        ON t.contact_id = c.id
    LEFT JOIN document_types AS dt
		ON dt.id = t.document_types_id
	WHERE DATE(t.transaction_date) BETWEEN v_initial_date AND v_final_date
		AND t.business_id = v_business_id
        AND (v_location_id = 0 OR t.location_id = v_location_id)
		AND (t.type = 'purchase' OR t.type = 'expense')
        AND (t.final_total - (t.total_before_tax * 1.13)) > 0
        AND t.document_types_id IN (
			SELECT id
			FROM document_types
			WHERE business_id = v_business_id
				AND is_active = 1
				AND document_type_number IN ('03', '05', '06', '12')
		)
	ORDER BY DATE(t.transaction_date), CONVERT(t.ref_no, UNSIGNED INTEGER);

END; $$

DELIMITER ;