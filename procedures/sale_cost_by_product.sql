SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS;

DELIMITER $$
CREATE PROCEDURE sale_cost_by_product(
	IN business_id INT,
	IN location_id INT,
	IN brand_id INT,
	IN start_date DATE,
	IN end_date DATE)
BEGIN
	/** Filter products */
	DROP TEMPORARY TABLE IF EXISTS tmp_products;
	CREATE TEMPORARY TABLE tmp_products AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id,
			v.sub_sku AS sku,
			IF(v.name = 'DUMMY', p.name, CONCAT(p.name, ' - ', v.name))  AS product
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		WHERE p.business_id = business_id
			AND (p.brand_id = brand_id OR brand_id = 0)
			AND p.clasification = 'product'
			and p.status = 'active'
		GROUP BY p.id, v.id;
	
	/** Get sell from products */
	DROP TEMPORARY TABLE IF EXISTS sells;
	CREATE TEMPORARY TABLE sells AS
		SELECT
			t.id,
			tsl.product_id,
			tsl.variation_id,
			tsl.unit_cost_exc_tax AS cost,
			SUM(tsl.quantity - tsl.quantity_returned) AS quantity
		FROM transactions AS t
		INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
		WHERE t.business_id = business_id
			AND (t.location_id = location_id OR location_id = 0)
			AND t.`type` = 'sell'
			AND t.status = 'final'
			AND DATE(t.transaction_date) BETWEEN start_date AND end_date
			AND tsl.product_id IN (SELECT tp.product_id FROM tmp_products AS tp)
		GROUP BY tsl.variation_id;
	
	/** Return data */
	SELECT
		tp.sku,
		tp.product,
		s.quantity,
		s.cost,
		SUM(s.quantity * s.cost) AS total
	FROM tmp_products AS tp
	LEFT JOIN sells AS s ON tp.variation_id = s.variation_id
	GROUP BY tp.variation_id
	ORDER BY tp.sku;
	
	/** Drop temporary tables */
	DROP TEMPORARY TABLE IF EXISTS tmp_products;
	DROP TEMPORARY TABLE IF EXISTS sells;
END; $$
DELIMITER;

CALL sale_cost_by_product(3, 0, 0, '2023-09-05', '2023-09-05'); 
