DROP PROCEDURE IF EXISTS all_lab_orders;

DELIMITER $$

CREATE PROCEDURE all_lab_orders(
    v_location_id INT,
    v_status_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10),
    v_search VARCHAR(255),
    v_start_record INT,
    v_page_size INT,
    v_order_column INT,
    v_order_dir VARCHAR(4)
)

BEGIN

	SELECT
        lo.no_order,
        t.correlative,
        dt.document_name AS document,
        IF (lo.transaction_id IS NULL, blo.name, bl.name) AS location,
        IF (c.is_default = 1, t.customer_name, c.name) AS customer,
        p.full_name AS patient,
        slo.name AS status,
        lo.delivery,
        lo.created_at,
        slo.color,
        lo.id,
        lo.number_times,
        lo.is_annulled,
        gc.document AS download_document,
        lo.status_lab_order_id
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
    GROUP BY lo.id
    ORDER BY
        CASE WHEN v_order_column = 1 AND v_order_dir = 'asc' THEN slo.name END ASC,
        CASE WHEN v_order_column = 1 AND v_order_dir = 'desc' THEN slo.name END DESC,
        CASE WHEN v_order_column = 2 AND v_order_dir = 'asc' THEN lo.no_order END ASC,
        CASE WHEN v_order_column = 2 AND v_order_dir = 'desc' THEN lo.no_order END DESC,
        CASE WHEN v_order_column = 3 AND v_order_dir = 'asc' THEN t.correlative END ASC,
        CASE WHEN v_order_column = 3 AND v_order_dir = 'desc' THEN t.correlative END DESC,
        CASE WHEN v_order_column = 4 AND v_order_dir = 'asc' THEN IF (lo.transaction_id IS NULL, blo.name, bl.name) END ASC,
        CASE WHEN v_order_column = 4 AND v_order_dir = 'desc' THEN IF (lo.transaction_id IS NULL, blo.name, bl.name) END DESC,
        CASE WHEN v_order_column = 5 AND v_order_dir = 'asc' THEN IF (c.is_default = 1, t.customer_name, c.name) END ASC,
        CASE WHEN v_order_column = 5 AND v_order_dir = 'desc' THEN IF (c.is_default = 1, t.customer_name, c.name) END DESC,
        CASE WHEN v_order_column = 6 AND v_order_dir = 'asc' THEN lo.created_at END ASC,
        CASE WHEN v_order_column = 6 AND v_order_dir = 'desc' THEN lo.created_at END DESC,
        CASE WHEN v_order_column = 7 AND v_order_dir = 'asc' THEN lo.delivery END ASC,
        CASE WHEN v_order_column = 7 AND v_order_dir = 'desc' THEN lo.delivery END DESC
    LIMIT v_start_record, v_page_size;

END; $$

DELIMITER ;