SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));


DROP PROCEDURE IF EXISTS get_sales_by_seller;

DELIMITER $$
CREATE PROCEDURE get_sales_by_seller (IN start_date DATE, IN end_date DATE, IN location_id INT)
BEGIN
	/** Get sell lines transactions */
	DROP  TEMPORARY TABLE IF EXISTS trans_lines;
	CREATE TEMPORARY TABLE trans_lines AS
		SELECT
			t.id,
			SUM(tsl.tax_amount) AS tax_amount
		FROM transactions AS t
		INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
		WHERE t.`type` = 'sell'
			AND t.status = 'final'
			AND DATE(t.transaction_date) BETWEEN start_date AND end_date
			AND (t.location_id = location_id OR location_id = 0)
		GROUP BY t.id;
	
	/** Get transactions */ 
	DROP TEMPORARY TABLE IF EXISTS trans;
	CREATE TEMPORARY TABLE trans AS
		SELECT
			t.id,
			t.location_id,
			t.transaction_date,
			SUM(IF(rt.id IS NULL, tl.tax_amount, tl.tax_amount - (rt.final_total - rt.total_before_tax))) AS tax_amount,
			SUM(IF(rt.id IS NULL, (t.final_total - t.tax_amount), (t.final_total - rt.final_total))) AS final_total
		FROM transactions AS t
		INNER JOIN trans_lines AS tl ON t.id = tl.id
		LEFT JOIN transactions AS rt ON t.id = rt.return_parent_id
		WHERE t.`type` IN ('sell', 'sell_return')
			AND t.status = 'final'
			AND DATE(t.transaction_date) BETWEEN start_date AND end_date
			AND (t.location_id = location_id OR location_id = 0)
		GROUP BY t.id;
	
	SELECT
		e.id AS seller_code,
		CONCAT(e.first_name, ' ', e.last_name) AS seller_name,
		t.location_id,
		bl.name AS location_name,
		SUM(t.final_total - t.tax_amount) AS total_before_tax,
		SUM(t.final_total) AS total_amount
	FROM trans AS t
	INNER JOIN business_locations AS bl ON t.location_id = bl.id
	INNER JOIN quotes AS q ON t.id = q.transaction_id
	INNER JOIN employees AS e ON q.employee_id = e.id
	GROUP BY e.id
	ORDER BY t.location_id ASC, e.id ASC;

	/** Drop temporary tables */
	DROP  TEMPORARY TABLE IF EXISTS trans_lines;
	DROP TEMPORARY TABLE IF EXISTS trans;
END; $$
DELIMITER ;

CALL get_sales_by_seller('2022-01-01', '2022-01-31', 0);