DROP PROCEDURE IF EXISTS get_sales_book_to_taxpayer;

DELIMITER $$

CREATE PROCEDURE get_sales_book_to_taxpayer(
 	initial_date DATE,
    final_date DATE,
    location INT,
    business INT
)
BEGIN
    SET @ccf := (
        SELECT id
        FROM document_types
		WHERE business_id = business
			AND is_active = 1
			AND short_name = 'CCF'
        LIMIT 1
    );

	SELECT
        DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,
		t.correlative,
        t.serie,
        REPLACE(t.resolution, '-', '') AS resolution,
        REPLACE(c.reg_number, '-', '') AS nrc,
        REPLACE(c.tax_number, '-', '') AS nit,
        c.name AS customer,
        IF (t.document_types_id = @ccf, t.final_total / 1.13, t.final_total / 1.13 * -1) AS internal,
        IF (t.document_types_id = @ccf, t.final_total - t.final_total / 1.13, t.final_total - t.final_total / 1.13 * -1) AS fiscal_debit,
        IF (t.document_types_id = @ccf, t.final_total, t.final_total * -1) AS total_sales,
        t.status,
        IF (t.document_types_id = @ccf, t.tax_amount, t.tax_amount * -1) AS tax_amount
    FROM transactions AS t
    JOIN customers AS c
        ON t.customer_id = c.id
    WHERE DATE(transaction_date) BETWEEN initial_date AND final_date
        AND t.location_id = location
        AND t.business_id = business
        AND t.document_types_id = ANY(
            SELECT id
			FROM document_types
			WHERE business_id = business
				AND is_active = 1
				AND (short_name IN ('CCF', 'NC', 'NOTA DE CR'))
        )
        AND (t.type = 'sell' OR t.type = 'sell_return')
        AND (t.status = 'final' OR t.status = 'annulled')
    ORDER BY DATE(t.transaction_date), CONVERT(t.correlative, UNSIGNED INTEGER) ASC;
END; $$

DELIMITER ;