DROP PROCEDURE IF EXISTS get_glasses_consumption_report;

DELIMITER $$

CREATE PROCEDURE get_glasses_consumption_report(
    v_business_id INT,
    v_warehouse_id INT,
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
    ORDER BY
        CASE WHEN v_order_column = 0 AND v_order_dir = 'asc' THEN lo.created_at END ASC,
        CASE WHEN v_order_column = 0 AND v_order_dir = 'desc' THEN lo.created_at END DESC,
        CASE WHEN v_order_column = 1 AND v_order_dir = 'asc' THEN lo.no_order END ASC,
        CASE WHEN v_order_column = 1 AND v_order_dir = 'desc' THEN lo.no_order END DESC,
        CASE WHEN v_order_column = 2 AND v_order_dir = 'asc' THEN IF (t.correlative IS NULL, lo.correlative, t.correlative) END ASC,
        CASE WHEN v_order_column = 2 AND v_order_dir = 'desc' THEN IF (t.correlative IS NULL, lo.correlative, t.correlative) END DESC,
        CASE WHEN v_order_column = 3 AND v_order_dir = 'asc' THEN dt.document_name END ASC,
        CASE WHEN v_order_column = 3 AND v_order_dir = 'desc' THEN dt.document_name END DESC,
        CASE WHEN v_order_column = 4 AND v_order_dir = 'asc' THEN v.sub_sku END ASC,
        CASE WHEN v_order_column = 4 AND v_order_dir = 'desc' THEN v.sub_sku END DESC,
        CASE WHEN v_order_column = 5 AND v_order_dir = 'asc' THEN p.name END ASC,
        CASE WHEN v_order_column = 5 AND v_order_dir = 'desc' THEN p.name END DESC,
        CASE WHEN v_order_column = 6 AND v_order_dir = 'asc' THEN IF (p.name LIKE '% L', gc.base_os, gc.base_od) END ASC,
        CASE WHEN v_order_column = 6 AND v_order_dir = 'desc' THEN IF (p.name LIKE '% L', gc.base_os, gc.base_od) END DESC,
        CASE WHEN v_order_column = 7 AND v_order_dir = 'asc' THEN IF (p.name LIKE '% L', gc.addition_os, gc.addition_od) END ASC,
        CASE WHEN v_order_column = 7 AND v_order_dir = 'desc' THEN IF (p.name LIKE '% L', gc.addition_os, gc.addition_od) END DESC,
        CASE WHEN v_order_column = 8 AND v_order_dir = 'asc' THEN lod.quantity END ASC,
        CASE WHEN v_order_column = 8 AND v_order_dir = 'desc' THEN lod.quantity END DESC
    LIMIT v_start_record, v_page_size;

END; $$

DELIMITER ;