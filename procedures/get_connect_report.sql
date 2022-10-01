SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_connect_report;

DELIMITER $$;
CREATE PROCEDURE get_connect_report(IN start_date DATE, IN end_date DATE, IN location_id INT, IN employee_id INT)
BEGIN
	SELECT
		IFNULL(c.business_name, c.name) AS customer_name,
		c.latitude,
		c.`length`,
		c.`from`,
		c.`to`,
		c.cost,
		SUM(tsl.quantity * IFNULL(p.weight, 0)) AS weight,
		SUM(tsl.quantity * IFNULL(p.volume, 0)) AS volume,
		SEC_TO_TIME(SUM(tsl.quantity * TIME_TO_SEC (IFNULL(p.download_time, 0)))) AS download_time
	FROM transactions AS t
	INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
	INNER JOIN products AS p ON tsl.product_id = p.id
	INNER JOIN customers AS c ON t.customer_id = c.id
	LEFT JOIN quotes AS q ON t.id = q.transaction_id
	WHERE DATE(t.transaction_date) BETWEEN start_date AND end_date
		AND (t.location_id = location_id OR location_id = 0)
		AND (q.employee_id = employee_id OR employee_id = 0)
	GROUP BY c.id;
END; $$
DELIMITER

CALL get_connect_report('2022-04-01', '2022-04-30', 0, 0);
