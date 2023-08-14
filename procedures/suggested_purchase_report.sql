SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS suggested_purchase;

DELIMITER $$
CREATE PROCEDURE suggested_purchase(
	IN business_id INT,
	IN location_id INT,
	IN warehouse_id INT,
	IN brand_id INT,
	IN end_date DATE,
	IN months INT)
BEGIN
	/** Calculate start date */
	SET @first_of_month = (SELECT DATE_FORMAT(end_date, '%Y-%m-01'));
	SET @start_date = (SELECT DATE_SUB(@first_of_month, INTERVAL (months -1) MONTH));

	/** Filter products */
	DROP TEMPORARY TABLE IF EXISTS tmp_products;
	CREATE TEMPORARY TABLE tmp_products AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		WHERE p.brand_id = brand_id
			AND p.clasification = 'product'
			and p.status = 'active'
		GROUP BY p.id, v.id;
	
	/** Get sell from products */
	DROP TEMPORARY TABLE IF EXISTS sells;
	CREATE TEMPORARY TABLE sells AS
		SELECT
			DATE_FORMAT(t.transaction_date, '%Y-%m') AS trans_month,
			tsl.product_id,
			tsl.variation_id,
			SUM(tsl.quantity - tsl.quantity_returned) AS quantity
		FROM transactions AS t
		INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
		WHERE t.business_id = business_id
			AND t.location_id = location_id
			AND (t.warehouse_id = warehouse_id OR warehouse_id = 0)
			AND t.`type` = 'sell'
			AND t.status = 'final'
			AND tsl.quantity > 0
			AND DATE(t.transaction_date) BETWEEN @start_date AND end_date
			AND tsl.product_id IN (SELECT tp.product_id FROM tmp_products AS tp)
		GROUP BY tsl.variation_id, DATE_FORMAT(t.transaction_date, '%Y-%m');
	
	/** Get product's stock */
	DROP TEMPORARY TABLE IF EXISTS kardex_stock;
	CREATE TEMPORARY TABLE kardex_stock AS
		SELECT
			k.variation_id,
			SUM(k.inputs_quantity - k.outputs_quantity) AS qty
		FROM kardexes AS k
		WHERE k.business_location_id = location_id
			AND (k.warehouse_id = warehouse_id OR warehouse_id = 0)
			AND k.variation_id IN (SELECT tp.variation_id FROM tmp_products AS tp)
		GROUP BY k.variation_id;
	
	/** Return result */
	SELECT
		v.sub_sku AS sku,
		IF(v.name != 'DUMMY', p.name, CONCAT(p.name, ' - ', v.name))  AS product,
		c.name AS category,
		sc.name AS sub_category,
		b.name AS brand,
		IFNULL(SUM(s.quantity), 0) AS total,  
		IFNULL(MIN(s.quantity), 0) AS min_val,
		IFNULL(MAX(s.quantity), 0) AS max_val,
		IFNULL(ROUND(AVG(s.quantity)), 0) AS avg_val,
		IFNULL(ks.qty, 0) AS stock
	FROM products AS p
	INNER JOIN variations AS v ON p.id = v.product_id
	INNER JOIN brands AS b ON p. brand_id = b.id
	LEFT JOIN categories AS c ON p.category_id = c.id
	LEFT JOIN categories AS sc ON p.sub_category_id = sc.id
	LEFT JOIN sells AS s ON v.id = s.variation_id
	LEFT JOIN kardex_stock AS ks ON v.id = ks.variation_id
	WHERE p.id IN (SELECT tp.product_id FROM tmp_products AS tp)
	GROUP BY s.variation_id;
	
	/** Drop temporary tables */ 
	DROP TEMPORARY TABLE IF EXISTS tmp_products;
	DROP TEMPORARY TABLE IF EXISTS sells;
	DROP TEMPORARY TABLE IF EXISTS kardex_stock;
END; $$
DELIMITER ;

CALL suggested_purchase(3, 1, 27, '2023-08-10');
