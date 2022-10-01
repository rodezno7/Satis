DROP PROCEDURE IF EXISTS print_stock_report_by_location;

DELIMITER $$

CREATE PROCEDURE print_stock_report_by_location(
    v_business_id INT
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
    GROUP BY v.id, bl.id
    ORDER BY v.sub_sku, bl.name;

END; $$

DELIMITER ;