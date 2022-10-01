DROP PROCEDURE IF EXISTS payment_note_report;

DELIMITER $$

CREATE PROCEDURE payment_note_report(
    v_business_id INT,
    v_location_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10)
)

BEGIN

	SELECT
        tp.paid_on,
        tp.note,
        IF (c.is_default = 1, t.customer_name, c.name) AS customer_name,
        dt.document_name,
        t.correlative,
        tp.amount,
        t.final_total
        -
        (
            SELECT SUM(tp1.amount)
            FROM transaction_payments AS tp1
            WHERE tp1.transaction_id = t.id
                AND tp1.paid_on <= tp.paid_on
        ) AS balance,
        t.final_total
    FROM transaction_payments AS tp
    LEFT JOIN transactions AS t
        ON t.id = tp.transaction_id
    LEFT JOIN customers AS c
        ON c.id = t.customer_id
    LEFT JOIN document_types AS dt
        ON dt.id = t.document_types_id
    WHERE tp.business_id = v_business_id
        AND (v_location_id = 0 OR t.location_id = v_location_id)
        AND ((v_start = '' AND v_end = '') OR (DATE(tp.paid_on) BETWEEN v_start AND v_end))
    ORDER BY tp.id;

END; $$

DELIMITER ;