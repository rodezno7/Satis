DROP PROCEDURE IF EXISTS get_purchases_book;

DELIMITER $$

CREATE PROCEDURE get_purchases_book(initial_date DATE, final_date DATE, business INT)
BEGIN
	SELECT
		t.id,
        DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,
        t.ref_no AS correlative,
        REPLACE(c.tax_number, '-', '') AS nrc,
        REPLACE(c.nit, '-', '') AS nit,
        c.supplier_business_name AS supplier,
        IF (c.tax_number IS NOT NULL, t.final_total / 1.13, NULL) AS internal,
        IF (c.tax_number IS NULL, t.final_total / 1.13, NULL) AS imports,
        t.final_total - t.final_total / 1.13 AS fiscal_credit,
        t.tax_amount AS withheld_amount,
        t.final_total AS total_purchases
    FROM transactions AS t
    JOIN contacts AS c
    ON t.contact_id = c.id
    WHERE DATE(transaction_date) BETWEEN initial_date AND final_date
        AND t.business_id = business
        AND (t.type = 'purchase'
            OR (t.type = 'expense'
                AND t.document_types_id = (
                    SELECT id
				    FROM document_types
				    WHERE business_id = business
					    AND is_active = 1
					    AND short_name = 'CCF'
                )
            )
        )
        AND t.status = 'received'
        AND t.is_closed = 0
    ORDER BY DATE(transaction_date), t.id;

END; $$
DELIMITER ;