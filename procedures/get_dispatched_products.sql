SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_dispatched_products;

DELIMITER $$
CREATE PROCEDURE get_dispatched_products(IN start_date DATE, IN end_date DATE, IN location INT, IN seller INT)
BEGIN
	/* local variables */
	DECLARE start_count, end_count INT DEFAULT 0;

	/* dispatched products */
	DROP TEMPORARY TABLE IF EXISTS dispatched_products;
	CREATE TEMPORARY TABLE dispatched_products AS
		SELECT
			v.id AS variation_id,
			IF(v.name != 'DUMMY', CONCAT(p.name, ' ', v.name), p.name) AS product_name,
			IF(p.weight > 0, p.weight, 0) AS weight
		FROM transactions AS t
		INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
		INNER JOIN variations AS v ON tsl.variation_id = v.id
		INNER JOIN products AS p ON v.product_id = p.id
		INNER JOIN quotes AS q ON t.id = q.transaction_id
		WHERE DATE(t.transaction_date) BETWEEN start_date AND end_date
			AND (q.employee_id = seller OR seller = 0)
			AND t.`type` = 'sell'
			AND t.status = 'final'
		GROUP BY v.id
		ORDER BY v.id ASC;
	
	/* create temporary table for customers */
	DROP TEMPORARY TABLE IF EXISTS customer_transactions;
	CREATE TEMPORARY TABLE customer_transactions AS
		SELECT
			c.id AS customer_id,
			t.id AS transaction_id,
			c.customer_portfolio_id,
			CONCAT(dt.short_name, t.correlative) AS doc,
			IF(c.business_name IS NOT NULL, c.business_name, c.name) AS customer_name
		FROM transactions AS t
		INNER JOIN customers AS c ON t.customer_id = c.id
		INNER JOIN document_types AS dt ON t.document_correlative_id = dt.id
		LEFT JOIN quotes AS q ON t.id = q.transaction_id
		WHERE DATE(t.transaction_date) BETWEEN start_date AND end_date
			AND t.`type` = 'sell'
			AND t.status = 'final'
			AND (t.location_id = location OR location = 0) 
			AND (q.employee_id = seller OR seller = 0)
		GROUP BY c.id, t.id
		ORDER BY c.name ASC;
	
	/* create temporary table to store product transactions by customer */
	DROP TEMPORARY TABLE IF EXISTS product_transactions;
	CREATE TEMPORARY TABLE product_transactions AS
		SELECT
			t.id,
			t.customer_id,
			tsl.variation_id,
			SUM(tsl.quantity - tsl.quantity_returned) AS quantity,
			(t.final_total) AS final_total
		FROM transactions AS t
		INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
		INNER JOIN quotes AS q ON t.id = q.transaction_id
		WHERE DATE(t.transaction_date) BETWEEN start_date AND end_date
			AND t.`type` = 'sell'
			AND t.status = 'final'
			AND (t.location_id = location OR location = 0) 
			AND (q.employee_id = seller OR seller = 0)
		GROUP BY t.customer_id, tsl.variation_id, t.id
		ORDER BY t.id ASC;

	/* add product columns to customer transactions */
	SET end_count = (SELECT COUNT(*) FROM dispatched_products);
	WHILE start_count < end_count DO
	
		SET @column_name := (SELECT dp.variation_id FROM dispatched_products AS dp LIMIT start_count, 1);
	
		SET @query := CONCAT('ALTER TABLE customer_transactions ADD COLUMN product_', @column_name, ' DECIMAL(7, 1)');
	
		PREPARE stmt FROM @query;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
	
		SET start_count = start_count + 1;
	END WHILE;

	/* add weight and final total to customer transactions */
	ALTER TABLE customer_transactions ADD COLUMN weight_total DECIMAL(8, 2);
	ALTER TABLE customer_transactions ADD COLUMN final_total DECIMAL(12, 4);

	/* merge information to return */
	SET start_count = 0;
	SET end_count = (SELECT COUNT(*) FROM product_transactions);
	SET @transaction_id := 0;

	WHILE start_count < end_count DO
		SET @id := (SELECT pt.id FROM product_transactions AS pt LIMIT start_count, 1);
		SET @variation_id := (SELECT pt.variation_id FROM product_transactions AS pt LIMIT start_count, 1);
		SET @customer_id := (SELECT pt.customer_id FROM product_transactions AS pt LIMIT start_count, 1);
		SET @quantity := (SELECT pt.quantity FROM product_transactions AS pt LIMIT start_count, 1);
		SET @weight := (SELECT dp.weight FROM dispatched_products AS dp WHERE dp.variation_id = @variation_id LIMIT 1);
		SET @final_total := (SELECT pt.final_total FROM product_transactions AS pt LIMIT start_count, 1);
	
		SET @query := CONCAT('
				UPDATE customer_transactions AS ct
				SET ct.product_', @variation_id, ' = ', @quantity,
					', ct.weight_total = ((', @weight, ' * ', @quantity,') + IFNULL(ct.weight_total, 0))',
					', ct.final_total = IF(', @id, ' != ', @transaction_id, ', IFNULL(ct.final_total, 0) + ', @final_total, ', ct.final_total) ',
				'WHERE ct.customer_id = ', @customer_id,
					' AND ct.transaction_id = ', @id);
		
		PREPARE stmt FROM @query;
		EXECUTE stmt;
		DEALLOCATE PREPARE stmt;
	
		SET @transaction_id := @id;
		SET start_count = start_count + 1;
	END WHILE;

	SELECT
		ct.*,
		CONCAT(e.first_name, ' ', e.last_name) AS seller_name
	FROM customer_transactions AS ct
	LEFT JOIN customer_portfolios AS cp ON ct.customer_portfolio_id = cp.id
	LEFT JOIN employees AS e ON cp.seller_id = e.id;

	DROP TEMPORARY TABLE IF EXISTS dispatched_products;
	DROP TEMPORARY TABLE IF EXISTS customer_transactions;
	DROP TEMPORARY TABLE IF EXISTS product_transactions;
END; $$
DELIMITER ;

CALL get_dispatched_products('2022-10-17', '2022-10-17', 0, 3);

