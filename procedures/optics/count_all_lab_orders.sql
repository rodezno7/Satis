DROP PROCEDURE IF EXISTS count_all_lab_orders;

DELIMITER $$

CREATE PROCEDURE count_all_lab_orders(
    v_location_id INT,
    v_status_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10),
    v_search VARCHAR(255)
)

BEGIN

	SELECT
        COUNT(*) AS count
    FROM lab_orders AS lo
    LEFT JOIN transactions AS t
        ON t.id = lo.transaction_id
    LEFT JOIN document_types AS dt
        ON dt.id = t.document_types_id
    LEFT JOIN business_locations AS bl
        ON bl.id = t.location_id
    LEFT JOIN business_locations AS blo
        ON blo.id = lo.business_location_id
    LEFT JOIN customers AS c
        ON c.id = lo.customer_id
    LEFT JOIN patients AS p
        ON p.id = lo.patient_id
    LEFT JOIN status_lab_orders AS slo
        ON slo.id = lo.status_lab_order_id
    LEFT JOIN graduation_cards AS gc
        ON gc.id = lo.graduation_card_id
    WHERE (v_location_id = 0 OR t.location_id = v_location_id OR lo.business_location_id = v_location_id)
        AND (v_status_id = 0 OR lo.status_lab_order_id = v_status_id)
        AND ((v_start = '' AND v_end = '') OR (DATE(lo.created_at) BETWEEN v_start AND v_end))
        AND lo.deleted_at IS NULL
        AND (
            lo.no_order LIKE CONCAT('%', v_search, '%') OR
            t.correlative LIKE CONCAT('%', v_search, '%') OR
            dt.document_name LIKE CONCAT('%', v_search, '%') OR
            blo.name LIKE CONCAT('%', v_search, '%') OR
            bl.name LIKE CONCAT('%', v_search, '%') OR
            t.customer_name LIKE CONCAT('%', v_search, '%') OR
            c.name LIKE CONCAT('%', v_search, '%') OR
            p.full_name LIKE CONCAT('%', v_search, '%') OR
            slo.name LIKE CONCAT('%', v_search, '%')
        )
    LIMIT 1;

END; $$

DELIMITER ;