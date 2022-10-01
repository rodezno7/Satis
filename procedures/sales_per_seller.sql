DROP PROCEDURE IF EXISTS sales_per_seller;

DELIMITER $$

CREATE PROCEDURE sales_per_seller(
    v_business_id INT,
    v_seller INT,
    v_location_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10)
)

BEGIN

    SELECT
        t.transaction_date,
        IF (c.is_default = 1, t.customer_name, c.name) AS customer_name,
        t.correlative,
        IF (t.status = 'annulled', 0.00, t.total_before_tax) AS total_before_tax,
        IF (t.status = 'annulled', 0.00, t.discount_amount) AS discount_amount,
        IF (t.status = 'annulled', 0.00, t.tax_amount) AS tax_amount,
        IF (t.status = 'annulled', 0.00, t.final_total) AS final_total
    FROM transactions AS t
    INNER JOIN customers AS c
        ON t.customer_id = c.id
    INNER JOIN business_locations AS bl
        ON t.location_id = bl.id
    WHERE t.business_id = v_business_id
        AND t.commission_agent = v_seller
        AND t.type = 'sell'
        AND t.status IN ('final', 'annulled')
        AND (v_location_id = 0 OR t.location_id = v_location_id)
        AND ((v_start = '' AND v_end = '') OR (DATE(t.transaction_date) BETWEEN v_start AND v_end))
    GROUP BY t.id
    ORDER BY t.transaction_date DESC;
END; $$

DELIMITER ;