DROP PROCEDURE IF EXISTS get_stock_report_by_location;

DELIMITER $$

CREATE PROCEDURE get_stock_report_by_location(
    v_business_id INT,
    v_search VARCHAR(255),
    v_start_record INT,
    v_page_size INT
)

BEGIN

    SELECT
        v.sub_sku AS sku,
        bl.name AS location,
        SUM(vld.qty_available) AS quantity
    FROM variation_location_details AS vld
    JOIN variations AS v ON vld.variation_id = v.id
    JOIN business_locations AS bl ON bl.id = vld.location_id
    WHERE bl.business_id = v_business_id
        AND vld.qty_available > 0
        AND (
            v.sub_sku LIKE CONCAT('%', v_search, '%') OR
            bl.name LIKE CONCAT('%', v_search, '%')
        )
    GROUP BY v.id, bl.id
    ORDER BY v.sub_sku, bl.name
    LIMIT v_start_record, v_page_size;

END; $$

DELIMITER ;