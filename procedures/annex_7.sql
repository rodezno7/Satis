DROP PROCEDURE IF EXISTS annex_7;

DELIMITER $$

CREATE PROCEDURE annex_7(
    v_initial_date DATE,
    v_final_date DATE,
    v_business_id INT,
    v_no_annex INT
)

BEGIN

    SELECT
        -- COLUMNA A: NIT DEL AGENTE
		SUBSTRING(REPLACE(c.tax_number, '-', ''), 1, 14) AS nit,

        -- COLUMNA B: FECHA DE EMISION
        DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,

        -- COLUMNA C: TIPO DE DOCUMENTO
        '07' AS document_type,

        -- COLUMNA D: SERIE
		SUBSTRING(UPPER(t.serie), 1, 100) AS serie,

        -- COLUMNA E: NUMERO DE DOCUMENTO
        SUBSTRING(t.ref_no, 1, 100) AS no_document,

        -- COLUMNA F: MONTO SUJETO
        t.final_total AS amount,

        -- COLUMNA G: MONTO DE RETENCION 1%
        t.final_total * 0.01 AS withheld,

        -- COLUMNA H: NUMERO DE ANEXO
        v_no_annex AS no_annex
	FROM transactions AS t
    JOIN customers AS c
        ON t.customer_id = c.id
	WHERE DATE(t.transaction_date) BETWEEN v_initial_date AND v_final_date
		AND t.business_id = v_business_id
		AND t.type = 'retention'
	ORDER BY DATE(t.transaction_date), CONVERT(t.ref_no, UNSIGNED INTEGER);

END; $$

DELIMITER ;