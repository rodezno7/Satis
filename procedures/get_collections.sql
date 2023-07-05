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
			tp.transaction_id,
			tp.paid_on AS transaction_date,
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
		p.transaction_id,
		p.transaction_date,
		p.`method`,
		p.amount,
		b.amount AS balance
	FROM payments AS p
	LEFT JOIN balance AS b ON p.transaction_id = b.transaction_id
	ORDER BY DATE(p.transaction_date);

	DROP TEMPORARY TABLE IF EXISTS payments;
	DROP TEMPORARY TABLE IF EXISTS balance;
END; $$
DELIMITER ;

CALL get_collections(3, 0, 0, '2023-06-15', '2023-06-30');


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
	
END; $$
DELIMITER ;

