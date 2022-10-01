DROP PROCEDURE IF EXISTS annex_6;

DELIMITER $$

CREATE PROCEDURE annex_6(
    v_initial_date DATE,
    v_final_date DATE,
    v_location_id INT,
    v_business_id INT,
    v_no_annex INT
)

BEGIN

    SELECT
        -- COLUMNA A: NIT AGENTE
        IF (c.nit IS NOT NULL, 1, 3) AS nit,

        -- COLUMNA B: FECHA DE EMISION DEL DOCUMENTO
        DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,

        -- COLUMNA C: SERIE DE DOCUMENTO
        SUBSTRING(UPPER(t.serie), 1, 100) AS serie,

        -- COLUMNA D: NUMERO DE DOCUMENTO
		SUBSTRING(t.ref_no, 1, 100) AS no_document,

        -- COLUMNA E: MONTO SUJETO
        t.final_total AS total,

        -- COLUMNA F: MONTO DEL ANTICIPO A CUENTA 2% DE IVA
        t.total_before_tax * 0.02 AS percent,

        -- COLUMNA G: NUMERO DE ANEXO
        v_no_annex AS no_annex
	FROM transactions AS t
    JOIN contacts AS c
        ON t.contact_id = c.id
	WHERE DATE(t.transaction_date) BETWEEN v_initial_date AND v_final_date
		AND t.business_id = v_business_id
        AND (v_location_id = 0 OR t.location_id = v_location_id)
		AND (t.type = 'purchase' OR t.type = 'expense')
	ORDER BY DATE(t.transaction_date), CONVERT(t.ref_no, UNSIGNED INTEGER);

END; $$

DELIMITER ;