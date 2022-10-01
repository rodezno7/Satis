DROP PROCEDURE IF EXISTS get_all_sales_with_utility;

DELIMITER $$

CREATE PROCEDURE get_all_sales_with_utility(
	IN business_id INT,
	IN location_id INT,
	IN document_type_id INT,
    IN start_date DATE,
    IN end_date DATE
)
BEGIN
	/** GET COSTS */
	DROP TEMPORARY TABLE IF EXISTS transaction_sell_lines_tmp;
	CREATE TEMPORARY TABLE transaction_sell_lines_tmp AS
		SELECT
			tsl.transaction_id,
			SUM(tsl.unit_price_exc_tax) AS cost_amount
		FROM transactions AS t
		INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
		INNER JOIN variations AS v ON tsl.variation_id = v.id
		WHERE t.business_id = business_id
			AND (t.location_id = location_id OR location_id = 0)
			AND (t.document_types_id = document_type_id OR document_type_id = 0)
			AND DATE(t.transaction_date) BETWEEN start_date AND end_date
		GROUP BY t.id;
	
	/** GET TRANSACTIONS */
	DROP TEMPORARY TABLE IF EXISTS transactions_tmp;
	CREATE TEMPORARY TABLE transactions_tmp AS
		SELECT
			t.id,
			t.transaction_date,
			t.status,
			dt.short_name AS doc_type,
			CONVERT(t.correlative, UNSIGNED INTEGER) AS correlative,
			IF(t.status = 'annulled', 'A  N  U  L  A  D  A',
				IF(c.is_default = 1, t.customer_name, c.name)) AS customer_name,
			(SELECT COUNT(id)
				FROM transaction_payments AS tp_tmp
				WHERE tp_tmp.transaction_id = t.id AND tp_tmp.is_return = 0
					AND DATE(tp_tmp.paid_on) = DATE(t.transaction_date)) AS payment_count,
			IF(t.status = 'annulled', '-', tp.`method`) AS payment_method,
			IF(t.status = 'annulled', 0, tslt.cost_amount) AS cost_total,
			IF(t.status = 'annulled', 0, t.final_total) AS final_total
		FROM transactions AS t
		INNER JOIN transaction_sell_lines_tmp AS tslt ON t.id = tslt.transaction_id
		INNER JOIN customers AS c ON t.customer_id = c.id
		INNER JOIN document_types AS dt ON t.document_types_id = dt.id 
		LEFT JOIN transaction_payments AS tp ON t.id = tp.transaction_id
		LEFT JOIN quotes AS q ON t.id = q.transaction_id
		WHERE t.status IN ('final', 'annulled')
			AND t.business_id = business_id
			AND (t.location_id = location_id OR location_id = 0)
			AND (t.document_types_id = document_type_id OR document_type_id = 0)
			AND (DATE(t.transaction_date) = DATE(tp.paid_on) OR tp.id IS NULL)
			AND DATE(t.transaction_date) BETWEEN start_date AND end_date
		GROUP BY t.id
		
		UNION ALL
		
		/** SELL RETURNS */
		SELECT
			rt.id,
			rt.transaction_date,
			rt.status,
			dt.short_name AS doc_type,
			CONVERT(rt.correlative, UNSIGNED INTEGER) AS correlative,
			IF(c.is_default = 1, t.customer_name, c.name) AS customer_name,
			1 AS payment_count,
			'sell_return' AS payment_method,
			SUM(tsl.quantity_returned * tsl.unit_price_before_discount) AS cost_total,
			rt.final_total AS final_total
		FROM transactions AS t
		INNER JOIN transactions AS rt ON t.id = rt.return_parent_id
		INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
		INNER JOIN document_types AS dt ON t.document_types_id = dt.id
		INNER JOIN customers AS c ON t.customer_id = c.id
		WHERE rt.business_id = business_id
			AND (rt.location_id = location_id OR location_id = 0)
			AND (rt.document_types_id = document_type_id OR document_type_id = 0)
			AND DATE(rt.transaction_date) BETWEEN start_date AND end_date
		GROUP BY t.id;
	
	SELECT
		id,
		status,
		transaction_date,
		correlative,
		doc_type,
		customer_name,
		IF(status = 'final',
			IF(payment_count = 1, payment_method,
				IF(payment_count > 0, 'multiple', 'credit')),
				payment_method) AS payment_method,
		IF(payment_method = 'sell_return', cost_total * -1, cost_total) AS cost_total,
		IF(payment_method = 'sell_return', final_total * -1, final_total) AS final_total,
		IF(payment_method = 'sell_return',  (final_total - cost_total) * -1, (final_total - cost_total)) AS utility
	FROM transactions_tmp
	GROUP BY id
	ORDER BY DATE(transaction_date) ASC, correlative ASC;
	
	DROP TEMPORARY TABLE IF EXISTS transaction_sell_lines_tmp;
	DROP TEMPORARY TABLE IF EXISTS transactions_tmp;
END; $$

DELIMITER ;

CALL get_all_sales_with_utility(3, 0, 0, '2021-09-01', '2021-09-30');