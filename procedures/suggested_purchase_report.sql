SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS suggested_purchase;

DELIMITER $$

CREATE PROCEDURE suggested_purchase(
	IN business_id INT,
	IN location_id INT,
	IN transaction_id DATE,
	IN brand INT)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS tmp_products;
	CREATE TEMPORARY TABLE tmp_products AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		WHERE p.brand_id = brand_id
		GROUP BY p.id, v.id;
	
	/** Return result */
	SELECT
		p.sky,
		p.name AS product,
		c.name AS category,
		sc.name AS sub_category,
		b.name AS brand
	FROM products AS p
	INNER JOIN brands AS b ON p.brand_id = b.id
	LEFT JOIN categories AS c ON p.category_id = c.id
	LEFT JOIN categories AS sc ON p.sub_category_id = sc.id
	WHERE p.id IN (SELECT tp.product_id FROM tmp_products AS tp);
	
	/** Drop temporary tables */ 
	DROP TEMPORARY TABLE IF EXISTS tmp_products;
END; $$

DELIMITER ;