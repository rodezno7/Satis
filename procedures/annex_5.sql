DROP PROCEDURE IF EXISTS annex_5;

DELIMITER $$

CREATE PROCEDURE annex_5(
    v_initial_date DATE,
    v_final_date DATE,
    v_location_id INT,
    v_business_id INT,
    v_no_annex INT
)

BEGIN

    SELECT
        -- COLUMNA A: TIPO DE DOCUMENTO
        IF (c.nit IS NOT NULL, 1, 3) AS document_type,

        -- COLUMNA B: NUMERO DE NIT, DUI, U OTRO DOCUMENTO
        SUBSTRING(REPLACE(c.nit, '-', ''), 1, 14) AS nit,
        SUBSTRING(REPLACE(c.tax_number, '-', ''), 1, 14) AS nrc,

        -- COLUMNA C: NOMBRE, RAZON SOCIAL O DENOMINACION
        c.name,
        c.supplier_business_name,

        -- COLUMNA D: FECHA DE EMISION DEL DOCUMENTO
		DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,

        -- COLUMNA E: NUMERO DE SERIE DEL DOCUMENTO
        SUBSTRING(UPPER(t.serie), 1, 100) AS serie,

        -- COLUMNA F: NUMERO DE DOCUMENTO
        SUBSTRING(t.ref_no, 1, 100) AS no_document,

        -- COLUMNA G: MONTO DE LA OPERACION
        t.final_total AS total,

        -- COLUMNA H: MONTO DE LA RETENCION IVA 13%
        t.total_before_tax * 0.13 AS tax_amount,

        -- COLUMNA I: NUMERO DE ANEXO
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
        AND c.is_exempt = 1
	ORDER BY DATE(t.transaction_date), CONVERT(t.ref_no, UNSIGNED INTEGER);

END; $$

DELIMITER ;