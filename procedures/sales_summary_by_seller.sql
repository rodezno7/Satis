DROP PROCEDURE IF EXISTS get_sales_summary_by_seller;

DELIMITER $$
CREATE PROCEDURE get_sales_summary_by_seller(IN start_date DATE, IN end_date DATE, IN location_id INT)
BEGIN
	SELECT
		p.sku,
		p.name AS product_name,
		c.name AS category,
		sc.name AS sub_category,
		b.name AS brand,
		ROUND(SUM(tsl.quantity - tsl.quantity_returned), 2) AS quantity,
		ROUND(tsl.unit_price_before_discount, 4) AS unit_price,
		ROUND((tsl.unit_price_before_discount * (SUM(tsl.quantity - tsl.quantity_returned))), 4) AS total_sale,
		e.id AS employee_id,
		CONCAT(e.first_name, " ", e.last_name) AS employee_name,
		t.payment_condition,
		ROUND(v.default_purchase_price, 4) AS cost,
		ROUND((v.default_purchase_price * (SUM(tsl.quantity - tsl.quantity_returned))), 4) AS total_cost,
		ROUND((tsl.unit_price_before_discount * (SUM(tsl.quantity - tsl.quantity_returned))) -
			(v.default_purchase_price * (SUM(tsl.quantity - tsl.quantity_returned))), 4)  AS utility
	FROM transactions AS t
	INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
	INNER JOIN quotes AS q ON t.id = q.transaction_id
	LEFT JOIN employees AS e ON q.employee_id = e.id
	INNER JOIN variations AS v ON tsl.variation_id = v.id
	INNER JOIN products AS p ON v.product_id = p.id
	LEFT JOIN brands AS b ON p.brand_id = b.id
	LEFT JOIN categories AS c ON p.category_id = c.id
	LEFT JOIN categories AS sc ON p.sub_category_id = sc.id
	WHERE DATE(t.transaction_date) BETWEEN start_date AND end_date
		AND t.`type` = 'sell'
		AND t.status = 'final'
		AND t.location_id = location_id
	GROUP BY p.sku, e.id, t.payment_condition, t.status
	ORDER BY p.name ASC;
END; $$
DELIMITER ;

CALL get_sales_summary_by_seller('2021-07-01', '2021-07-31', 1);