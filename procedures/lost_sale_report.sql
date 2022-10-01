DROP PROCEDURE IF EXISTS getLostSalesReport;
DELIMITER //
CREATE PROCEDURE getLostSalesReport(
    IN business_id INT,
    IN start_date VARCHAR(20),
    IN end_date VARCHAR(20),
    IN employee_id INT,
    IN reason_id INT,
    IN customer_id INT
)
BEGIN
SELECT
    q.id,
    q.quote_ref_no AS ref_no,
    q.quote_date,
    q.due_date,
    ls.lost_date AS lost_date,
    CONCAT(e.first_name, ' ', e.last_name) AS seller_name,
    q.customer_name AS customer_name,
    q.customer_id,
    dt.short_name AS document,
    r.reason AS reason,
    ls.comments AS lose_comment,
    q.`status`,
    q.total_final
	
	FROM quotes AS q 
	JOIN customers AS c ON c.id = q.customer_id
	JOIN document_types AS dt ON dt.id = q.document_type_id
	LEFT JOIN employees AS e ON e.id = q.employee_id
	LEFT JOIN lost_sales AS ls ON ls.quote_id = q.id
	LEFT JOIN reasons AS r ON r.id = ls.reason_id

	WHERE  q.business_id = business_id
		AND ((start_date = '' AND end_date = '') OR (DATE(lost_date) BETWEEN start_date AND end_date))
		AND q.`type` = 'quote'
		AND q.lost_sale_id IS NOT NULL 
		AND (employee_id = 0 OR q.employee_id = employee_id)
		AND (reason_id = 0 OR r.id = reason_id)
		AND (customer_id = 0 OR q.customer_id = customer_id)
	ORDER BY q.id DESC;
END;
//
DELIMITER ;

CALL getLostSalesReport(3,'2021-04-09', '2021-05-31', 0, 0, 0);