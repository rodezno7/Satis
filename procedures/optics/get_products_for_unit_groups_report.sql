DROP PROCEDURE IF EXISTS get_products_for_unit_groups_report;

DELIMITER $$

CREATE PROCEDURE get_products_for_unit_groups_report(
    v_business_id INT,
    v_clasification VARCHAR(20),
    v_category_id INT,
    v_sub_category_id INT,
    v_brand_id INT,
    is_material INT
)

BEGIN

	SELECT
        p.id AS id,
        p.name AS name,
        p.clasification AS clasification,
        c1.name AS category,
        c2.name AS sub_category,
        ug.description AS unit,
        b.name AS brand,
        p.sku AS sku,
        p.status AS status
    FROM products AS p
    LEFT JOIN brands AS b
        ON b.id = p.brand_id
    LEFT JOIN unit_groups AS ug
        ON ug.id = p.unit_group_id
    LEFT JOIN categories AS c1
        ON c1.id = p.category_id
    LEFT JOIN categories AS c2
        ON c2.id = p.sub_category_id
    LEFT JOIN tax_groups AS tg
        ON tg.id = p.tax
    WHERE p.business_id = v_business_id
        AND p.type <> 'modifier'
        AND p.status = 'active'
        AND (v_clasification = '' OR p.clasification = v_clasification)
        AND (v_category_id = 0 OR p.category_id = v_category_id)
        AND (v_sub_category_id = 0 OR p.sub_category_id = v_sub_category_id)
        AND (v_brand_id = 0 OR p.brand_id = v_brand_id)
        AND IF (
            is_material = 1,
            IF (p.clasification = 'material', 1, 0),
            IF (p.clasification <> 'material', 1, 0)
        )
    ORDER BY p.name, p.sku;

END; $$

DELIMITER ;