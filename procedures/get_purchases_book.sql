SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_purchases_book;

DELIMITER $$

CREATE PROCEDURE get_purchases_book(
	initial_date DATE,
	final_date DATE,
	business INT,
	IN location_id INT)
BEGIN
	SELECT
		t.id,
        DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,
        t.ref_no AS correlative,
        REPLACE(c.tax_number, '-', '') AS nrc,
        REPLACE(c.nit, '-', '') AS nit,
        REPLACE(c.dni, '-', '') AS dui,
        c.supplier_business_name AS supplier,
        IF (
            t.`type` = 'expense',
            IF(
                c.tax_number IS NOT NULL,
                t.exempt_amount,
                NULL
            ),
            IF(
                t.purchase_type = 'national',
                IF(
                    t.`type` = 'purchase_return',
                    (t.exempt_amount) * -1,
                    t.exempt_amount
                ),
                NULL
            )
        ) AS internal_exempt,
        IF(
            t.type = 'expense',
            IF(
                c.tax_number IS NULL,
                t.exempt_amount,
                NULL
            ),
            IF(
                t.purchase_type = 'international',
                IF(
                    t.`type` = 'purchase_return',
                    (t.exempt_amount) * -1,
                    t.exempt_amount
                ),
                NULL
            )
        ) AS imports_exempt,
        IF(
            t.type = 'expense',
            IF(
                c.tax_number IS NOT NULL,
                t.total_before_tax,
                NULL
            ),
            IF(
                t.purchase_type = 'national',
                IF(
                    t.`type` = 'purchase_return',
                    (t.total_before_tax) * -1,
                    t.total_before_tax
                ),
                NULL
            )
        ) AS internal,
        IF(
            t.type = 'expense',
            IF(
                c.tax_number IS NULL,
                t.total_before_tax,
                NULL
            ),
            IF(
                t.purchase_type = 'international',
                IF(
                    t.`type` = 'purchase_return',
                    (t.total_before_tax) * -1,
                    t.total_before_tax
                ),
                NULL
            )
        ) AS imports,
        IF(
            t.type = 'expense',
            ((t.final_total - t.tax_amount - t.exempt_amount) / 1.13) * 0.13,
            IF(
                t.purchase_type = 'national',
                IF(
                    t.`type` = 'purchase_return',
                    ((((t.final_total - t.tax_amount) / 1.13) * 0.13)) * -1,
                    (((t.final_total - t.tax_amount) / 1.13) * 0.13)
                ),
                IF(
                    t.`type` = 'purchase_return',
                    t.tax_amount * -1,
                    t.tax_amount
                )
            )
        ) AS fiscal_credit,
        IF(t.`type` = 'purchase_return', (t.tax_amount) * -1, t.tax_amount) AS withheld_amount,
        IF(t.`type` = 'purchase_return', (t.final_total) * -1, t.final_total) AS total_purchases
    FROM transactions AS t
    JOIN contacts AS c
    ON t.contact_id = c.id
    WHERE DATE(transaction_date) BETWEEN initial_date AND final_date
        AND t.business_id = business
        AND (t.`type` IN ('purchase', 'purchase_return')
            OR (t.`type` = 'expense'
                AND t.document_types_id = (
                    SELECT id
				    FROM document_types
				    WHERE business_id = business
					    AND is_active = 1
					    AND short_name = 'CCF'
                )
            )
        )
        AND t.status IN ('received', 'final')
        AND (t.location_id = location_id OR location_id = 0)
        AND t.is_closed = 0
    ORDER BY DATE(transaction_date), t.id;

END; $$
DELIMITER ;