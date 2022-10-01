DROP PROCEDURE IF EXISTS payment_report;

DELIMITER $$

CREATE PROCEDURE payment_report(
    v_business_id INT,
    v_seller INT,
    v_location_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10)
)

BEGIN

    SELECT
        tp.paid_on,
        IF (c.is_default = 1, t.customer_name, c.name) AS customer_name,
        t.correlative,
        tp.amount / 1.13 AS amount
    FROM transaction_payments AS tp
    INNER JOIN transactions AS t
        ON tp.transaction_id = t.id
    INNER JOIN customers AS c
        ON t.customer_id = c.id
    INNER JOIN business_locations AS bl
        ON t.location_id = bl.id
    WHERE t.business_id = v_business_id
        AND tp.created_by = v_seller
        AND t.type = 'sell'
        AND t.status IN ('final', 'annulled')
        AND (v_location_id = 0 OR t.location_id = v_location_id)
        AND ((v_start = '' AND v_end = '') OR (DATE(tp.paid_on) BETWEEN v_start AND v_end))
    GROUP BY tp.id
    ORDER BY tp.paid_on DESC;
END; $$

DELIMITER ;