SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_all_products;

CREATE PROCEDURE get_all_products(
	IN v_business_id INT, 
	IN v_location_id INT, 
	IN v_clasification VARCHAR(10), 
	IN v_search VARCHAR(255), 
	IN v_start_record INT, 
	IN v_page_size INT, 
	IN v_order_column INT, 
	IN v_order_dir VARCHAR(4)
)
BEGIN
	SELECT 
		product.id AS id,
        product.name AS product_name,
	    product.clasification AS clasification,
	    product.type,
	    product.dai,
	    c1.name AS category,
	    c2.name AS sub_category,
	    product.status,
	    unit_group.description AS unit_group,
	    brands.name AS brand,
	    tax_groups.description AS tax,
	    product.sku AS sku,
	    product.image,
	    tax_groups.description AS applicable_tax,
        vld.qty_available AS stock,
        variation.default_purchase_price AS cost
	FROM products AS product
    LEFT JOIN brands ON brands.id = product.brand_id
    LEFT JOIN unit_groups AS unit_group ON unit_group.id = product.unit_group_id
    LEFT JOIN units AS unit ON unit.id = product.unit_id
    LEFT JOIN categories AS c1 ON c1.id = product.category_id
    LEFT JOIN categories AS c2 ON c2.id = product.sub_category_id
    LEFT JOIN tax_groups ON tax_groups.id = product.tax
    LEFT JOIN variations AS variation ON product.id = variation.product_id
    LEFT JOIN variation_location_details AS vld ON product.id = vld.product_id
    INNER JOIN business_locations AS bl ON bl.id = vld.location_id
	WHERE product.business_id = v_business_id
        AND product.type != 'modifier'
        AND (v_location_id = 0 OR bl.location_id = v_location_id)
        AND (v_clasification = '' OR clasification = v_clasification)
        AND (
            sku LIKE CONCAT('%', v_search, '%') OR
            product.name LIKE CONCAT('%', v_search, '%') OR
            vld.qty_available LIKE CONCAT('%', v_search, '%') OR
            variation.default_purchase_price LIKE CONCAT('%', v_search, '%') OR
            clasification LIKE CONCAT('%', v_search, '%')
        )
    GROUP BY product.id
    ORDER BY
        CASE WHEN v_order_column = 0 AND v_order_dir = 'asc' THEN sku END ASC,
        CASE WHEN v_order_column = 0 AND v_order_dir = 'desc' THEN sku END DESC,
        CASE WHEN v_order_column = 1 AND v_order_dir = 'asc' THEN product.name END ASC,
        CASE WHEN v_order_column = 1 AND v_order_dir = 'desc' THEN product.name END DESC,
        CASE WHEN v_order_column = 2 AND v_order_dir = 'asc' THEN vld.qty_available END ASC,
        CASE WHEN v_order_column = 2 AND v_order_dir = 'desc' THEN vld.qty_available END DESC,
        CASE WHEN v_order_column = 3 AND v_order_dir = 'asc' THEN variation.default_purchase_price END ASC,
        CASE WHEN v_order_column = 3 AND v_order_dir = 'desc' THEN variation.default_purchase_price END DESC,
        CASE WHEN v_order_column = 4 AND v_order_dir = 'asc' THEN clasification END ASC,
        CASE WHEN v_order_column = 4 AND v_order_dir = 'desc' THEN clasification END DESC
    LIMIT v_start_record, v_page_size;
END