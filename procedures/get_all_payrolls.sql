SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_all_payrolls;

CREATE get_all_payrolls(IN v_business_id INT, IN v_year INT, IN v_search VARCHAR(255), IN v_start_record INT, IN v_page_size INT, IN v_order_column INT, IN v_order_dir VARCHAR(4))
begin
	SELECT 
		payroll.id AS id,
	    pt.name AS payrollType,
		payroll.name AS payrollName,
		payroll.isr_id AS isr_id,
		payroll.payment_period_id AS payment_period_id,
		payroll.start_date AS start_date,
		payroll.end_date AS end_date,
		pp.name AS payment_period,
		ps.name AS status,
		pp2.name AS isr,
		payroll.month AS month
	FROM payrolls AS payroll
    LEFT join payroll_statuses AS ps ON ps.id = payroll.payroll_status_id
    LEFT join payroll_types AS pt ON pt.id = payroll.payroll_type_id
    LEFT join payment_periods AS pp ON pp.id = payroll.payment_period_id
    LEFT join payment_periods AS pp2 ON pp2.id = payroll.isr_id
    WHERE payroll.business_id = v_business_id
        AND (v_year = 0 OR payroll.year = v_year)
        AND (
            pt.name LIKE CONCAT('%', v_search, '%') OR
            payroll.name LIKE CONCAT('%', v_search, '%') OR
	           payroll.start_date LIKE CONCAT('%', v_search, '%') OR
            payroll.end_date LIKE CONCAT('%', v_search, '%') OR
            pp.name LIKE CONCAT('%', v_search, '%') OR
            pp2.name LIKE CONCAT('%', v_search, '%')
        )
    GROUP BY payroll.id
    ORDER BY
        CASE WHEN v_order_column = 0 AND v_order_dir = 'asc' THEN pt.name END ASC,
        CASE WHEN v_order_column = 0 AND v_order_dir = 'desc' THEN pt.name END DESC,
        CASE WHEN v_order_column = 1 AND v_order_dir = 'asc' THEN payroll.name END ASC,
	    CASE WHEN v_order_column = 1 AND v_order_dir = 'desc' THEN payroll.name END DESC,
        CASE WHEN v_order_column = 2 AND v_order_dir = 'asc' THEN payroll.start_date END ASC,
        CASE WHEN v_order_column = 2 AND v_order_dir = 'desc' THEN payroll.start_date END DESC,
        CASE WHEN v_order_column = 3 AND v_order_dir = 'asc' THEN payroll.end_date END ASC,
        CASE WHEN v_order_column = 3 AND v_order_dir = 'desc' THEN payroll.end_date END DESC,
        CASE WHEN v_order_column = 4 AND v_order_dir = 'asc' THEN pp.name END ASC,
        CASE WHEN v_order_column = 4 AND v_order_dir = 'desc' THEN pp.name END DESC,
        CASE WHEN v_order_column = 5 AND v_order_dir = 'asc' THEN pp2.name END ASC,
        CASE WHEN v_order_column = 5 AND v_order_dir = 'desc' THEN pp2.name END DESC
    LIMIT v_start_record, v_page_size;
END