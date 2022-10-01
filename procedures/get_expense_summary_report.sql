/* Expense summary report */
DROP PROCEDURE IF EXISTS get_expense_summary_report;

DELIMITER $$
CREATE PROCEDURE get_expense_summary_report (IN transaction_year INT, IN location_id INT)
BEGIN
	SELECT
		ex.name AS description,
		SUM(IF(MONTH(t.transaction_date) = 1, t.final_total, 0)) AS jan, -- January
		SUM(IF(MONTH(t.transaction_date) = 2, t.final_total, 0)) AS feb, -- February
		SUM(IF(MONTH(t.transaction_date) = 3, t.final_total, 0)) AS mar, -- March
		SUM(IF(MONTH(t.transaction_date) = 4, t.final_total, 0)) AS apr, -- April
		SUM(IF(MONTH(t.transaction_date) = 5, t.final_total, 0)) AS may, -- May
		SUM(IF(MONTH(t.transaction_date) = 6, t.final_total, 0)) AS jun, -- June
		SUM(IF(MONTH(t.transaction_date) = 7, t.final_total, 0)) AS jul, -- July
		SUM(IF(MONTH(t.transaction_date) = 8, t.final_total, 0)) AS aug, -- August
		SUM(IF(MONTH(t.transaction_date) = 9, t.final_total, 0)) AS sep, -- September
		SUM(IF(MONTH(t.transaction_date) = 10, t.final_total, 0)) AS `oct`, -- October
		SUM(IF(MONTH(t.transaction_date) = 11, t.final_total, 0)) AS nov, -- November
		SUM(IF(MONTH(t.transaction_date) = 12, t.final_total, 0)) AS `dec`, -- December
		SUM(t.final_total) AS total
	FROM transactions AS t
	INNER JOIN expense_categories AS ex ON t.expense_category_id = ex.id
	WHERE t.`type` = 'expense'
		AND YEAR(t.transaction_date) = transaction_year
		AND (t.location_id = location_id OR location_id = 0)
	GROUP BY t.expense_category_id;
END; $$
DELIMITER ;

CALL get_expense_summary_report(2021, 0);