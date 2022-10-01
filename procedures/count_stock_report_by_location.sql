DROP PROCEDURE IF EXISTS count_stock_report_by_location;

DELIMITER $$

CREATE PROCEDURE count_stock_report_by_location(
    v_business_id INT,
    v_search VARCHAR(255)
)

BEGIN

SELECT COUNT(*) AS count
FROM (
    SELECT
        v.id
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
) AS total;

END; $$

DELIMITER ;