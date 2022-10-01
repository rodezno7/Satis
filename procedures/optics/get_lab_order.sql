DROP PROCEDURE IF EXISTS get_lab_order;

DELIMITER $$

CREATE PROCEDURE get_lab_order(
    v_lab_order_id INT,
    v_second_time INT
)

BEGIN

	SELECT
        gc.*,
        lo.*,
        lo.id AS loid,
        DATE_FORMAT(lo.delivery, '%d/%m/%Y %H:%i') AS delivery_value,
        IF (slo.id = v_second_time, true, false) AS show_fields,
        t.location_id,
        t.correlative,
        t.final_total,
        slo.save_and_print,
        p.full_name AS patient_name,
        c.name AS customer_name
    FROM lab_orders AS lo
    LEFT JOIN graduation_cards AS gc
        ON gc.id = lo.graduation_card_id
    LEFT JOIN status_lab_orders AS slo
        ON slo.id = lo.status_lab_order_id
    LEFT JOIN transactions AS t
        ON t.id = lo.transaction_id
    LEFT JOIN patients AS p
        ON p.id = lo.patient_id
    LEFT JOIN customers AS c
        ON c.id = lo.customer_id
    WHERE lo.id = v_lab_order_id
    LIMIT 1;

END; $$

DELIMITER ;