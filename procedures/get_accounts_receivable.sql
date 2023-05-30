SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_accounts_receivable;

DELIMITER $$
CREATE PROCEDURE get_accounts_receivable(
	IN business_id INT,
	IN customer_id INT,
	IN seller_id INT,
	IN location_id INT,
	IN start_date DATE,
	IN end_date DATE,
	IN due_only INT)
BEGIN
	/** Get sell returns */
	DROP TEMPORARY TABLE IF EXISTS sell_returns;
	CREATE TEMPORARY TABLE sell_returns as
		SELECT
			t.id,
			rt.final_total
		FROM transactions AS t
		INNER JOIN transactions AS rt ON t.id = rt.return_parent_id
		INNER JOIN customers AS c ON t.customer_id = c.id
		WHERE t.business_id = business_id
			AND DATE(t.transaction_date) BETWEEN start_date AND end_date
			AND (t.customer_id = customer_id OR customer_id = 0)
			AND (c.customer_portfolio_id = seller_id OR seller_id = 0)
			AND (t.location_id = location_id OR location_id = 0)
			AND t.payment_condition = 'credit';
		
	/** Get accounts receivable data */
	DROP TEMPORARY table IF EXISTS accounts_receivable;
	CREATE TEMPORARY TABLE accounts_receivable AS
		SELECT
			t.id,
			t.customer_id,
			IFNULL(t.correlative, t.ref_no) AS correlative,
			t.transaction_date,
    		IFNULL(c.business_name, c.name) AS customer_name,
			IF(t.pay_term_number IS NOT NULL, DATE_ADD(DATE(t.transaction_date), INTERVAL t.pay_term_number DAY), DATE_ADD(DATE(t.transaction_date), INTERVAL 3 DAY)) AS expire_date,
			IF(t.pay_term_number IS NOT NULL, DATEDIFF(DATE(NOW()), DATE(t.transaction_date)), '3') AS days,
			IF(sr.id IS NULL, t.final_total, (t.final_total - sr.final_total)) AS final_total
		FROM transactions AS t
		INNER JOIN customers AS c ON t.customer_id = c.id
		LEFT JOIN sell_returns AS sr ON t.id = sr.id
		WHERE t.business_id = business_id
			AND t.`type` IN ('sell', 'opening_balance')
			AND t.status = 'final'
			AND DATE(t.transaction_date) BETWEEN start_date AND end_date
			AND (t.customer_id = customer_id OR customer_id = 0)
			AND (c.customer_portfolio_id = seller_id OR seller_id = 0)
			AND (t.location_id = location_id OR location_id = 0)
			AND t.payment_condition = 'credit'
		GROUP BY t.id;
	
	/** Get payments and return data */
	SELECT
		ar.customer_id,
		ar.correlative,
		ar.transaction_date,
		ar.customer_name,
		ar.expire_date,
		ar.days,
		ar.final_total,
		SUM(tp.amount) AS payments,
		IF(ar.days <= 30, (ar.final_total - IFNULL((SUM(tp.amount)), 0)), 0) AS days_30,
		IF(ar.days > 30 AND ar.days <= 60, (ar.final_total - IFNULL((SUM(tp.amount)), 0)), 0) AS days_60,
		IF(ar.days > 60 AND ar.days <= 90, (ar.final_total - IFNULL((SUM(tp.amount)), 0)), 0) AS days_90,
		IF(ar.days > 90 AND ar.days <= 120, (ar.final_total - IFNULL((SUM(tp.amount)), 0)), 0) AS days_120,
		IF(CAST(ar.days AS UNSIGNED) > 120, (ar.final_total - IFNULL((SUM(tp.amount)), 0)), 0) AS more_than_120
	FROM accounts_receivable AS ar 
	LEFT JOIN transaction_payments AS tp ON ar.id = tp.transaction_id
	WHERE (tp.paid_on IS NULL OR DATE(tp.paid_on) BETWEEN start_date AND end_date)
	GROUP BY ar.id
	HAVING ((ar.final_total - IFNULL(payments, 0)) > 0.01 OR due_only = 0)
	ORDER BY ar.customer_id ASC, ar.transaction_date;
		
	/** Drop temporary table */
	DROP TEMPORARY TABLE IF EXISTS sell_returns;
	DROP TEMPORARY TABLE IF EXISTS accounts_receivable;
END; $$
DELIMITER ;

CALL get_accounts_receivable(3, 0, 0, 0, '2023-01-01', DATE(NOW()), 1);