DROP PROCEDURE IF EXISTS count_glasses_consumption_report;

DELIMITER $$

CREATE PROCEDURE count_glasses_consumption_report(
    v_business_id INT,
    v_warehouse_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10),
    v_search VARCHAR(255)
)

BEGIN

	SELECT
        COUNT(*) AS count
    FROM lab_order_details AS lod
    LEFT JOIN lab_orders AS lo ON lo.id = lod.lab_order_id
    LEFT JOIN transactions AS t ON t.id = lo.transaction_id
    LEFT JOIN document_types AS dt ON dt.id = t.document_types_id
    LEFT JOIN variations AS v ON v.id = lod.variation_id
    LEFT JOIN products AS p ON p.id = v.product_id
    LEFT JOIN graduation_cards AS gc ON gc.id = lo.graduation_card_id
    WHERE lo.business_id = v_business_id
        AND (v_warehouse_id = 0 OR lod.warehouse_id = v_warehouse_id)
        AND ((v_start = '' AND v_end = '') OR (DATE(lo.created_at) BETWEEN v_start AND v_end))
        AND (
            lo.created_at LIKE CONCAT('%', v_search, '%') OR
            lo.no_order LIKE CONCAT('%', v_search, '%') OR
            t.correlative LIKE CONCAT('%', v_search, '%') OR
            dt.document_name LIKE CONCAT('%', v_search, '%') OR
            v.sub_sku LIKE CONCAT('%', v_search, '%') OR
            p.name LIKE CONCAT('%', v_search, '%') OR
            gc.base_os LIKE CONCAT('%', v_search, '%') OR
            gc.base_od LIKE CONCAT('%', v_search, '%') OR
            gc.addition_os LIKE CONCAT('%', v_search, '%') OR
            gc.addition_od LIKE CONCAT('%', v_search, '%') OR
            lod.quantity LIKE CONCAT('%', v_search, '%')
        )
    ORDER BY lo.created_at DESC
    LIMIT 1;

END; $$

DELIMITER ;