DROP PROCEDURE IF EXISTS get_lab_order_products;

DELIMITER $$

CREATE PROCEDURE get_lab_order_products(
    v_variation_id INT
)

BEGIN

	SELECT
        CONCAT(COALESCE(p.name, ''), ' - ', COALESCE(v.sub_sku, '')) AS product_name
    FROM variations AS v
    LEFT JOIN products AS p
        ON p.id = v.product_id
    WHERE v.id = v_variation_id
    LIMIT 1;

END; $$

DELIMITER ;