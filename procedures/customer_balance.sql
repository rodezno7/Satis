SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS customer_balance;

DELIMITER $$

CREATE PROCEDURE customer_balance(
	IN business_id INT,
	IN start_date DATE,
	IN end_date DATE,
	IN seller INT
	)
BEGIN
	/** sum transaction payments */
	DROP TEMPORARY TABLE IF EXISTS trans_pays;
	CREATE TEMPORARY TABLE trans_pays AS
		SELECT
			t.id AS transaction_id,
			t.customer_id,
			t.correlative,
			SUM(tp.amount) AS total_paid
		FROM transactions AS t
		INNER JOIN transaction_payments AS tp ON t.id = tp.transaction_id
		WHERE t.business_id = business_id
			AND DATE(tp.paid_on) BETWEEN start_date AND end_date
			AND t.`type` IN ('sell', 'opening_balance')
			AND (ISNULL(t.payment_condition) OR t.payment_condition = 'credit')
			AND t.status = 'final'
		GROUP BY t.id;
	
	/** Temp transaction table */
	DROP TEMPORARY TABLE IF EXISTS temp_trans;
	CREATE TEMPORARY TABLE temp_trans
		SELECT
			t.id,
			t.customer_id,
			t.correlative,
			SUM(tp.total_paid) AS total_paid,
			SUM(t.final_total) AS final_total
		FROM transactions AS t
		LEFT JOIN trans_pays AS tp ON t.id = tp.transaction_id
		WHERE t.business_id = business_id
			AND DATE(t.transaction_date) BETWEEN start_date AND end_date
			AND t.`type` IN ('sell', 'opening_balance')
			AND (ISNULL(t.payment_condition) OR t.payment_condition = 'credit')
			AND t.status = 'final'
		GROUP BY t.customer_id;
		
    SELECT
    	tt.*,
    	c.id,
    	c.is_taxpayer,
    	IFNULL(c.business_name, c.name)AS full_name,
    	c.credit_limit
	FROM temp_trans AS tt
    INNER JOIN customers AS c ON tt.customer_id = c.id
    WHERE (c.customer_portfolio_id = seller OR seller = 0)
    GROUP BY c.id, c.is_taxpayer, c.name, c.business_name;
   
	/** Drop temporary tables */
	DROP TEMPORARY TABLE IF EXISTS trans_pays;
	DROP TEMPORARY TABLE IF EXISTS temp_trans;
END; $$

DELIMITER

CALL customer_balance(3, '2022-10-12', '2022-10-18', 0)