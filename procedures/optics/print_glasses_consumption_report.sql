DROP PROCEDURE IF EXISTS print_glasses_consumption_report;

DELIMITER $$

CREATE PROCEDURE print_glasses_consumption_report(
    v_business_id INT,
    v_warehouse_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10)
)

BEGIN

	SELECT
        lo.created_at AS date,
        lo.no_order,
        IF (t.correlative IS NULL, lo.correlative, t.correlative) AS correlative,
        dt.document_name AS document_type,
        v.sub_sku AS sku,
        p.name AS product,
        IF (p.name LIKE '%L', gc.addition_os, IF (p.name LIKE '%R', gc.addition_od, IF (gc.addition_os IS NULL, gc.addition_od, gc.addition_os))) AS addition,
        lod.quantity
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
    ORDER BY lo.created_at DESC;

END; $$

DELIMITER ;