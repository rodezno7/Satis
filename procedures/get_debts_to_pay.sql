SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_debts_to_pay;

DELIMITER $$
CREATE PROCEDURE get_debts_to_pay(
	IN business_id INT,
	IN contact_id INT,
	IN location_id INT,
	IN start_date DATE,
	IN end_date DATE
	)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS debts_to_pay;
	CREATE TEMPORARY TABLE debts_to_pay AS
		SELECT
			t.id,
			c.id AS supplier_id,
			t.ref_no AS reference,
			t.transaction_date,
			c.supplier_business_name AS supplier_name,
			IF(pt.days IS NOT NULL, DATE_ADD(DATE(t.transaction_date), INTERVAL pt.days DAY), '') AS expire_date,
			IF(pt.days IS NOT NULL, DATEDIFF(DATE(NOW()), DATE(t.transaction_date)), '') AS days,
			IF(t.purchase_type = 'international', t.total_after_expense, t.final_total) AS final_total
		FROM transactions AS t
		INNER JOIN contacts AS c ON t.contact_id = c.id
		LEFT JOIN payment_terms AS pt ON t.payment_term_id = pt.id
		WHERE t.`type` = 'purchase'
			AND DATE(t.transaction_date) BETWEEN start_date AND end_date
			AND (t.business_id = business_id OR business_id = 0)
			AND (t.contact_id = contact_id OR contact_id = 0)
			AND (t.location_id = location_id OR location_id = 0)
			AND t.payment_condition = 'credit'
		GROUP BY t.id;

	SELECT
		dtp.supplier_id,
		dtp.reference,
		dtp.transaction_date,
		dtp.supplier_name,
		dtp.expire_date,
		dtp.days,
		dtp.final_total,
		SUM(tp.amount) AS payments,
		IF(dtp.days <= 30, (dtp.final_total - IFNULL((SUM(tp.amount)), 0)), 0) AS days_30,
		IF(dtp.days > 30 AND dtp.days <= 60, (dtp.final_total - IFNULL((SUM(tp.amount)), 0)), 0) AS days_60,
		IF(dtp.days > 60 AND dtp.days <= 90, (dtp.final_total - IFNULL((SUM(tp.amount)), 0)), 0) AS days_90,
		IF(dtp.days > 90 AND dtp.days <= 120, (dtp.final_total - IFNULL((SUM(tp.amount)), 0)), 0) AS days_120,
		IF(CAST(dtp.days AS UNSIGNED) > 120, (dtp.final_total - IFNULL((SUM(tp.amount)), 0)), 0) AS more_than_120
	FROM debts_to_pay AS dtp
	LEFT JOIN transaction_payments AS tp ON dtp.id = tp.transaction_id
	GROUP BY dtp.id
	ORDER BY dtp.supplier_id ASC, dtp.transaction_date ASC;

	DROP TEMPORARY TABLE IF EXISTS debts_to_pay;
END; $$
DELIMITER ;

CALL get_debts_to_pay(3, 0, 0, '2021-06-01', DATE(NOW()));