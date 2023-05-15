SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DELIMITER //

DROP PROCEDURE IF EXISTS getQuotesTransactions;

CREATE PROCEDURE getQuotesTransactions(IN start_date DATE, IN end_date DATE, IN business_id INT)
BEGIN
	SELECT
		q.id,
		q.delivery_date,
		q.status,
		q.customer_id,
		q.employee_id AS seller_id,
		CONCAT(e.first_name, ' ', e.last_name) as seller_name,
		q.customer_name,
		s.name AS state_name,
		c.name AS city_name,
		q.address,
		q.landmark,
		q.contact_name,
		q.mobile AS contact_mobile,
		q.delivery_type,
		q.quote_ref_no AS order_number,
		dt.document_name AS doc_type,
		q.total_final AS order_total,
		t.final_total,
		(SELECT
			COUNT(tp_temp.id)
		FROM transaction_payments AS tp_temp
		WHERE tp_temp.transaction_id = t.id
			AND tp_temp.is_return = 0
			AND DATE(tp_temp.paid_on) = DATE(t.transaction_date)) AS payment_counts,
		tp.`method` AS pay_method
	FROM quotes AS q
	LEFT JOIN transactions AS t ON t.id = q.transaction_id
	LEFT JOIN transaction_payments AS tp ON t.id = tp.transaction_id
	INNER JOIN employees AS e ON e.id = q.employee_id
	INNER JOIN document_types dt ON q.document_type_id = dt.id
	LEFT JOIN states AS s ON q.state_id = s.id
	LEFT JOIN cities AS c ON q.city_id = c.id
	WHERE DATE(q.delivery_date) BETWEEN start_date  AND end_date
		AND q.business_id = business_id
		AND q.`type` = 'order'
	GROUP BY q.id
	ORDER BY q.delivery_date DESC;
END;
//
DELIMITER ;

CALL getQuotesTransactions('2023-01-01', '2023-05-31', 3);
