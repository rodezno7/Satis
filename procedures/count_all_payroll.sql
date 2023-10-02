SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS count_all_payroll;

CREATE PROCEDURE count_all_payroll(IN v_business_id INT, IN v_year INT, IN v_search VARCHAR(255))
BEGIN
	SELECT COUNT(*) AS count
	FROM (
		SELECT 
	        payroll.id 
	    FROM payrolls AS payroll
		LEFT join payroll_statuses AS ps ON ps.id = payroll.payroll_status_id
		LEFT join payroll_types AS pt ON pt.id = payroll.payroll_type_id
		LEFT join payment_periods AS pp ON pp.id = payroll.payment_period_id
		LEFT join payment_periods AS pp2 ON pp2.id = payroll.isr_id
		WHERE payroll.business_id = v_business_id
	        AND (v_year = 0 OR payroll.year = v_year)
		    AND (
		        pt.name  LIKE CONCAT('%', v_search, '%') OR
		        payroll.name LIKE CONCAT('%', v_search, '%') OR
		            payroll.start_date LIKE CONCAT('%', v_search, '%') OR
		        payroll.end_date LIKE CONCAT('%', v_search, '%') OR
		        pp.name LIKE CONCAT('%', v_search, '%') OR
		        pp2.name LIKE CONCAT('%', v_search, '%')
		    )
	    ORDER BY payroll.id ASC
	) AS count;
END