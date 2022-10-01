DROP PROCEDURE IF EXISTS customer_balance;

DELIMITER $$

CREATE PROCEDURE customer_balance(
	v_business_id INT,
	IN start_date DATE,
	IN end_date DATE,
	IN seller INT
	)
BEGIN
    SELECT
        c.id,
        c.is_taxpayer,
        IFNULL(c.business_name, c.name) AS full_name,
        SUM(t.payment_balance) AS total_paid,
        SUM(t.final_total) AS final_total,
        c.credit_limit
    FROM transactions AS t
    INNER JOIN customers AS c ON t.customer_id = c.id
    WHERE t.business_id = v_business_id
    	AND DATE(t.transaction_date) BETWEEN start_date AND end_date
        AND t.`type` IN ('sell', 'opening_balance')
        AND t.status = 'final'
        AND t.payment_status IN ('due', 'partial')
        AND (c.customer_portfolio_id = seller OR seller = 0)
    GROUP BY c.id, c.is_taxpayer, c.name, c.business_name
    ORDER BY c.name;
END; $$

DELIMITER ;

CALL customer_balance(3)