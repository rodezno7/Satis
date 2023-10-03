SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS count_all_payroll;

CREATE PROCEDURE count_all_products(
	IN v_business_id INT, 
	IN v_location_id INT, 
	IN v_clasification VARCHAR(10), 
	IN v_search VARCHAR(255)
)
BEGIN
    SELECT COUNT(*) AS count
    FROM (
        SELECT 
            product.id 
        FROM products AS product 
            LEFT JOIN brands ON product.brand_id = brands.id
            LEFT JOIN unit_groups AS unit_group ON product.unit_group_id = unit_group.id
            LEFT JOIN units AS unit ON product.unit_id = unit.id
            LEFT JOIN categories AS c1 ON product.category_id = c1.id
            LEFT JOIN categories AS c2 ON product.sub_category_id = c2.id
            LEFT JOIN tax_groups ON product.tax = tax_groups.id
            LEFT JOIN variations AS variation ON product.id = variation.product_id
            LEFT JOIN variation_location_details AS vld ON product.id = vld.product_id
            INNER JOIN business_locations AS bl ON bl.id = vld.location_id
        WHERE product.business_id = v_business_id
            AND product.type COLLATE utf8mb4_unicode_ci != 'modifier'
            AND (v_location_id = 0 OR vld.location_id = v_location_id)
            AND (v_clasification = '' OR product.clasification = v_clasification)
            AND (
                product.sku LIKE CONCAT('%', v_search, '%') OR
                product.name LIKE CONCAT('%', v_search, '%') OR
                vld.qty_available LIKE CONCAT('%', v_search, '%') OR
                variation.default_purchase_price LIKE CONCAT('%', v_search, '%') OR
                product.clasification LIKE CONCAT('%', v_search, '%')
            )
        ORDER BY product.id ASC
    ) AS count;    
END