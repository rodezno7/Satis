SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_input_output;

DELIMITER $$
CREATE PROCEDURE get_input_output(
	IN start_date DATE,
	IN end_date DATE,
	IN location INT,
	IN brand INT,
	IN category INT,
	IN transactions INT,
	IN stock INT)
BEGIN
	# stock and transactions vars
	SET @stock := 0;
	SET @transactions := 0;
	IF stock = 1 THEN
		SET @stock := -1; END IF;
	IF transactions = 1 THEN
		SET @transactions := -1; END IF;

	# Initial inventory
	DROP TEMPORARY TABLE IF EXISTS initial_inventories;
	CREATE TEMPORARY TABLE initial_inventories AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id,
			SUM(k.inputs_quantity - k.outputs_quantity) AS stock
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		INNER JOIN kardexes AS k ON v.id = k.variation_id
		INNER JOIN movement_types AS mt ON k.movement_type_id = mt.id
		WHERE ((DATE(k.date_time) < start_date)
			AND (k.business_location_id = location OR location = 0)
			OR  mt.name = 'opening_stock')
		GROUP BY v.id;
	
	/** INPUTS */
	# Purchases
	DROP TEMPORARY TABLE IF EXISTS purchases;
	CREATE TEMPORARY TABLE purchases AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id,
			SUM(k.inputs_quantity - k.outputs_quantity) AS stock
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		INNER JOIN kardexes AS k ON v.id = k.variation_id
		INNER JOIN movement_types AS mt ON k.movement_type_id = mt.id
		WHERE DATE(k.date_time) BETWEEN start_date AND end_date
			AND (k.business_location_id = location OR location = 0)
			AND mt.name = 'purchase'
		GROUP BY v.id;
	
	# Purchase transfer
	DROP TEMPORARY TABLE IF EXISTS purchase_transfers;
	CREATE TEMPORARY TABLE purchase_transfers AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id,
			SUM(k.inputs_quantity) AS stock
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		INNER JOIN kardexes AS k ON v.id = k.variation_id
		INNER JOIN movement_types AS mt ON k.movement_type_id = mt.id
		WHERE DATE(k.date_time) BETWEEN start_date AND end_date
			AND (k.business_location_id = location OR location = 0)
			AND mt.name = 'purchase_transfer'
		GROUP BY v.id;
	
	# Input stock adjustment
	DROP TEMPORARY TABLE IF EXISTS input_stock_adjustments;
	CREATE TEMPORARY TABLE input_stock_adjustments AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id,
			SUM(k.inputs_quantity) AS stock
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		INNER JOIN kardexes AS k ON v.id = k.variation_id
		INNER JOIN movement_types AS mt ON k.movement_type_id = mt.id
		WHERE DATE(k.date_time) BETWEEN start_date AND end_date
			AND mt.name = 'stock_adjustment'
			AND (k.business_location_id = location OR location = 0)
			AND mt.`type` = 'input'
		GROUP BY v.id;
	
	# Sell return
	DROP TEMPORARY TABLE IF EXISTS sell_returns;
	CREATE TEMPORARY TABLE sell_returns AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id,
			SUM(k.inputs_quantity) AS stock
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		INNER JOIN kardexes AS k ON v.id = k.variation_id
		INNER JOIN movement_types AS mt ON k.movement_type_id = mt.id
		WHERE DATE(k.date_time) BETWEEN start_date AND end_date
			AND (k.business_location_id = location OR location = 0)
			AND mt.name = 'sell_return'
		GROUP BY v.id;
	
	/** OUTPUTS */
	# Sales
	DROP TEMPORARY TABLE IF EXISTS sales;
	CREATE TEMPORARY TABLE sales AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id,
			SUM(k.outputs_quantity) AS stock
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		INNER JOIN kardexes AS k ON v.id = k.variation_id
		INNER JOIN movement_types AS mt ON k.movement_type_id = mt.id
		WHERE DATE(k.date_time) BETWEEN start_date AND end_date
			AND (k.business_location_id = location OR location = 0)
			AND mt.name = 'sell'
		GROUP BY v.id;
		
	# Sell transfer
	DROP TEMPORARY TABLE IF EXISTS sell_transfers;
	CREATE TEMPORARY TABLE sell_transfers AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id,
			SUM(k.outputs_quantity) AS stock
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		INNER JOIN kardexes AS k ON v.id = k.variation_id
		INNER JOIN movement_types AS mt ON k.movement_type_id = mt.id
		WHERE DATE(k.date_time) BETWEEN start_date AND end_date
			AND (k.business_location_id = location OR location = 0)
			AND mt.name = 'sell_transfer'
		GROUP BY v.id;
	
	# Output stock adjustment
	DROP TEMPORARY TABLE IF EXISTS output_stock_adjustments;
	CREATE TEMPORARY TABLE output_stock_adjustments AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id,
			SUM(k.outputs_quantity) AS stock
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		INNER JOIN kardexes AS k ON v.id = k.variation_id
		INNER JOIN movement_types AS mt ON k.movement_type_id = mt.id
		WHERE DATE(k.date_time) BETWEEN start_date AND end_date
			AND mt.name = 'stock_adjustment'
			AND (k.business_location_id = location OR location = 0)
			AND mt.`type` = 'output'
		GROUP BY v.id;
	
	# Purchase return
	DROP TEMPORARY TABLE IF EXISTS purchase_returns;
	CREATE TEMPORARY TABLE purchase_returns AS
		SELECT
			p.id AS product_id,
			v.id AS variation_id,
			SUM(k.outputs_quantity) AS stock
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		INNER JOIN kardexes AS k ON v.id = k.variation_id
		INNER JOIN movement_types AS mt ON k.movement_type_id = mt.id
		WHERE DATE(k.date_time) BETWEEN start_date AND end_date
			AND (k.business_location_id = location OR location = 0)
			AND mt.name = 'purchase_return'
		GROUP BY v.id;
	
	# Inventory
	DROP TEMPORARY TABLE IF EXISTS inventory;
	CREATE TEMPORARY TABLE inventory AS
		SELECT
			c.id AS category_id,
			c.name AS category_name,
			v.sub_sku AS sku,
			IF(v.name != 'DUMMY', CONCAT(p.name, ' ', v.name), p.name) AS product_name,
			SUM(IFNULL(ii.stock, 0)) AS initial_inventory,
			SUM(IFNULL(pc.stock, 0)) AS purchases,
			SUM(IFNULL(pt.stock, 0)) AS purchase_transfers,
			SUM(IFNULL(isa.stock, 0)) AS input_stock_adjustments,
			SUM(IFNULL(sr.stock, 0)) AS sell_returns,
			SUM(IFNULL(s.stock, 0)) AS sales,
			SUM(IFNULL(st.stock, 0)) AS sell_transfers,
			SUM(IFNULL(osa.stock, 0)) AS output_stock_adjustments,
			SUM(IFNULL(pr.stock, 0)) AS purchase_returns,
			SUM(IFNULL(pc.stock, 0) + IFNULL(pt.stock, 0)
				+ IFNULL(isa.stock, 0) + IFNULL(sr.stock, 0)
				+ IFNULL(s.stock, 0) + IFNULL(st.stock, 0)
				+ IFNULL(osa.stock, 0) + IFNULL(pr.stock, 0)) AS transactions,
			SUM((IFNULL(ii.stock, 0) + IFNULL(pc.stock, 0)
				+ IFNULL(pt.stock, 0) + IFNULL(isa.stock, 0)
				+ IFNULL(sr.stock, 0)) - (IFNULL(s.stock, 0)
				+ IFNULL(st.stock, 0) + IFNULL(osa.stock, 0)
				+ IFNULL(pr.stock, 0))) AS stock
		FROM products AS p
		INNER JOIN variations AS v ON p.id = v.product_id
		LEFT JOIN categories AS c ON p.category_id = c.id
		LEFT JOIN initial_inventories AS ii ON v.id = ii.variation_id
		LEFT JOIN purchases AS pc ON v.id = pc.variation_id
		LEFT JOIN purchase_transfers AS pt ON v.id = pt.variation_id
		LEFT JOIN input_stock_adjustments AS isa ON v.id = isa.variation_id
		LEFT JOIN sell_returns AS sr ON v.id = sr.variation_id
		LEFT JOIN sales AS s ON v.id = s.variation_id
		LEFT JOIN sell_transfers AS st ON v.id = st.variation_id
		LEFT JOIN output_stock_adjustments AS osa ON v.id = osa.variation_id
		LEFT JOIN purchase_returns AS pr ON v.id = pr.variation_id
		WHERE p.clasification = 'product'
			AND p.status = 'active'
			AND (p.brand_id = brand OR brand = 0)
			AND (p.category_id = category OR category = 0)
		GROUP BY v.id;
	
	/** Filter and return data */
	SELECT
		inv.category_id,
		inv.category_name,
		inv.sku,
		inv.product_name,
		ROUND(inv.initial_inventory, 1) AS initial_inventory,
		ROUND(inv.purchases, 1) AS purchases,
		ROUND(inv.purchase_transfers, 1) AS purchase_transfers,
		ROUND(inv.input_stock_adjustments, 1) AS input_stock_adjustments,
		ROUND(inv.sell_returns, 1) AS sell_returns,
		ROUND(inv.sales, 1) AS sales,
		ROUND(inv.sell_transfers, 1) AS sell_transfers,
		ROUND(inv.output_stock_adjustments, 1) AS output_stock_adjustments,
		ROUND(inv.purchase_returns, 1) AS purchase_returns,
		ROUND(inv.stock, 1) AS stock
	FROM inventory AS inv
	WHERE inv.transactions > @transactions
		AND (inv.initial_inventory > @stock OR inv.stock > @stock)
	ORDER BY inv.category_name ASC, inv.category_id ASC, inv.sku ASC;
	
	DROP TEMPORARY TABLE IF EXISTS initial_inventories;
	DROP TEMPORARY TABLE IF EXISTS purchases;
	DROP TEMPORARY TABLE IF EXISTS purchase_transfers;
	DROP TEMPORARY TABLE IF EXISTS input_stock_adjustments;
	DROP TEMPORARY TABLE IF EXISTS sell_returns;
	DROP TEMPORARY TABLE IF EXISTS sales;
	DROP TEMPORARY TABLE IF EXISTS sell_transfers;
	DROP TEMPORARY TABLE IF EXISTS output_stock_adjustments;
	DROP TEMPORARY TABLE IF EXISTS purchase_returns;
	DROP TEMPORARY TABLE IF EXISTS inventory;
END; $$
DELIMITER ;

CALL get_input_output('2022-01-27', '2022-02-28', 0, 0, 0, 0, 0);

