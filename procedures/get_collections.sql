SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_collections;

DELIMITER $$
CREATE PROCEDURE get_collections(
	IN business_id INT,
	IN location_id INT,
	IN start_date DATE,
	IN end_date DATE
	)
BEGIN	
	/** Get transaction payments */
	DROP TEMPORARY TABLE IF EXISTS payments;
	CREATE TEMPORARY TABLE payments AS
		SELECT
			tp.id,
			tp.transaction_id,
			tp.paid_on AS transaction_date,
			t.tax_amount AS withheld,
			t.final_total,
			tp.`method`,
			SUM(IF(tp.is_return = 0, tp.amount, tp.amount * -1)) AS amount
		FROM transactions AS t
		INNER JOIN transaction_payments AS tp ON t.id = tp.transaction_id
		WHERE t.business_id = business_id
			AND t.`type` IN ('sell', 'opening_balance')
			AND (t.location_id = location_id OR location_id = 0)
			AND DATE(tp.paid_on) BETWEEN start_date AND end_date
			AND (ISNULL(t.payment_condition) OR t.payment_condition = 'credit')
			AND t.status = 'final'
		GROUP BY tp.id, tp.transaction_id, tp.`method`;
	
	/** Get sell returns */
	DROP TEMPORARY TABLE IF EXISTS sell_returns;
	CREATE TEMPORARY TABLE sell_returns as
		SELECT
			t.id,
			rt.final_total
		FROM transactions AS t
		INNER JOIN transactions AS rt ON t.id = rt.return_parent_id
		WHERE t.business_id = business_id
			AND (t.location_id = location_id OR location_id = 0)
			AND (ISNULL(t.payment_condition) OR t.payment_condition = 'credit')
			AND t.id IN (SELECT p.transaction_id FROM payments AS p)
		GROUP BY t.id;
	
	/** Get transaction balances before start date */
	DROP TEMPORARY TABLE IF EXISTS balance;
	CREATE TEMPORARY TABLE balance AS
		SELECT
			tp.transaction_id,
			SUM(IF(tp.is_return = 0, tp.amount, tp.amount * -1)) AS amount
		FROM transaction_payments AS tp
		WHERE DATE(tp.paid_on) < start_date
			AND tp.transaction_id IN (SELECT p.transaction_id FROM payments AS p)
		GROUP BY tp.transaction_id;
	
	SELECT
		p.id,
		p.transaction_id,
		p.transaction_date,
		p.`method`,
		p.amount,
		p.withheld,
		b.amount AS balance,
		sr.final_total AS sell_return,
		p.final_total
	FROM payments AS p
	LEFT JOIN balance AS b ON p.transaction_id = b.transaction_id
	LEFT JOIN sell_returns AS sr ON p.transaction_id = sr.id
	ORDER BY DATE(p.transaction_date);

	DROP TEMPORARY TABLE IF EXISTS payments;
	DROP TEMPORARY TABLE IF EXISTS balance; 
END; $$
DELIMITER ;

CALL get_collections(3, 0, '2023-01-01', '2023-06-01');


DROP PROCEDURE IF EXISTS get_collection_transactions;

DELIMITER $$
CREATE PROCEDURE get_collection_transactions(
	IN business_id INT,
	IN location_id INT,
	IN seller_id INT,
	IN start_date DATE,
	IN end_date DATE
	)
BEGIN		
	/** Get collections */
	DROP TEMPORARY TABLE IF EXISTS collections;
	CREATE TEMPORARY TABLE collections AS
		SELECT
			tp.transaction_id
		FROM transactions AS t
		INNER JOIN transaction_payments AS tp ON t.id = tp.transaction_id
		WHERE t.business_id = business_id
			AND t.`type` IN ('sell', 'opening_balance')
			AND (t.location_id = location_id OR location_id = 0)
			AND DATE(tp.paid_on) BETWEEN start_date AND end_date
			AND (ISNULL(t.payment_condition) OR t.payment_condition = 'credit')
			AND t.status = 'final'
		GROUP BY tp.transaction_id;
	
	SELECT
		t.id AS transaction_id,
		t.transaction_date,
		t.correlative,
		IF(c.business_name IS NOT NULL, c.business_name, c.name) AS customer,
		p.sku,
		p.name AS product,
		(tsl.quantity - tsl.quantity_returned) AS quantity,
		tsl.unit_price_before_discount * (tsl.quantity - tsl.quantity_returned) AS unit_price_exc_tax,
		tsl.unit_price * (tsl.quantity - tsl.quantity_returned) AS unit_price_inc_tax,
		t.payment_status,
		CONCAT(e.first_name, ' ', IFNULL(e.last_name, '')) AS seller,
		cp.name AS portfolio,
		ct.name AS city,
		s.name AS state
	FROM transactions AS t
	INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
	INNER JOIN products AS p ON tsl.product_id = p.id
	INNER JOIN variations AS v ON p.id = v.product_id
	INNER JOIN customers AS c ON t.customer_id = c.id
	LEFT JOIN cities AS ct ON c.city_id = ct.id
	LEFT JOIN states AS s ON c.state_id = s.id
	LEFT JOIN customer_portfolios AS cp ON c.customer_portfolio_id = cp.id
	LEFT JOIN employees AS e ON cp.seller_id = e.id
	WHERE t.business_id = business_id
		AND t.`type` IN ('sell', 'opening_balance')
		AND (t.location_id = location_id OR location_id = 0)
		AND (c.customer_portfolio_id = seller_id OR seller_id = 0)
		AND (ISNULL(t.payment_condition) OR t.payment_condition = 'credit')
		AND t.status = 'final'
		AND t.id IN (SELECT cl.transaction_id FROM collections AS cl)
	ORDER BY t.transaction_date;
	
	DROP TEMPORARY TABLE IF EXISTS collections;
END; $$
DELIMITER ;

CALL get_collection_transactions(3, 0, 0, '2023-06-01', '2023-06-30');


