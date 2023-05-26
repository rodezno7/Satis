SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS product_price_lists;

DELIMITER $$
CREATE PROCEDURE product_price_lists (
	IN business_id INT,
	IN location_id INT,
	IN category_id INT,
	IN brand_id INT)
BEGIN
	/** Price lists */
	DROP TEMPORARY TABLE IF EXISTS price_lists;
	CREATE TEMPORARY TABLE price_lists AS
		SELECT
			spg.id,
			spg.name,
			vgp.variation_id,
			vgp.price_inc_tax
		FROM selling_price_groups AS spg
		INNER JOIN variation_group_prices AS vgp ON spg.id = vgp.price_group_id
		WHERE spg.business_id = business_id
			AND spg.name IN ('PRECIO 1', 'PRECIO 2', 'PRECIO 3')
		GROUP BY vgp.variation_id, spg.id;
	
	/** Stock */
	DROP TEMPORARY TABLE IF EXISTS stock;
	CREATE TEMPORARY TABLE stock AS
		SELECT
			k.variation_id,
			SUM(IFNULL(k.inputs_quantity, 0) - IFNULL(k.outputs_quantity, 0)) AS stock
		FROM kardexes AS k
		WHERE k.business_id = business_id
			AND k.business_location_id = location_id
		GROUP BY k.variation_id;

	SELECT
		p.sku,
		p.name AS product,
		c.name AS category,
		sc.name AS sub_category,
		b.name AS brand,
		v.default_purchase_price AS cost,
		SUM(IF(pl.name = 'PRECIO 1', pl.price_inc_tax, 0)) AS 'price_1',
		SUM(IF(pl.name = 'PRECIO 2', pl.price_inc_tax, 0)) AS 'price_2',
		SUM(IF(pl.name = 'PRECIO 3', pl.price_inc_tax, 0)) AS 'price_3',
		IFNULL(s.stock, 0) AS stock,
		p.status
	FROM products AS p
	INNER JOIN variations AS v ON p.id = v.product_id
	LEFT JOIN brands AS b ON p.brand_id = b.id
	LEFT JOIN categories AS c ON p.category_id = c.id
	LEFT JOIN categories AS sc ON p.sub_category_id = sc.id
	LEFT JOIN price_lists AS pl ON v.id = pl.variation_id
	LEFT JOIN stock AS s ON v.id = s.variation_id
	WHERE p.business_id = business_id
		AND (p.category_id = category_id OR category_id = 0)
		AND (p.brand_id = brand_id OR brand_id = 0)
	GROUP BY p.sku;

	DROP TEMPORARY TABLE IF EXISTS price_lists;
	DROP TEMPORARY TABLE IF EXISTS stock;
END; $$
DELIMITER ;

CALL product_price_lists(3, 1, 6, 0);