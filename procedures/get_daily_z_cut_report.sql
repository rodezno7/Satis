/** GET CASHIER CLOSURE REPORT Z **/
DELIMITER $$
CREATE PROCEDURE getDailyZCutReport(IN cashier_closure_id INT)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS daily_z_cut;
	CREATE TEMPORARY TABLE IF NOT EXISTS daily_z_cut AS
		SELECT
			t.cashier_closure_id,
			t.correlative,
			0 AS return_amount,
			IF(tp.`method` = 'cash', tp.amount, 0) AS cash_amount,
			IF(tp.`method` = 'card', tp.amount, 0) AS card_amount,
			IF(tp.`method` = 'check', tp.amount, 0) AS check_amount,
			IF(tp.`method` = 'bank_transfer', tp.amount, 0) AS bank_transfer_amount,
			t.final_total AS final_total
		FROM transactions AS t
		INNER JOIN cashier_closures AS cc ON t.cashier_closure_id = cc.id
		INNER JOIN document_types AS dt ON t.document_types_id = dt.id
		LEFT JOIN transactions AS rt ON t.id = rt.return_parent_id
		LEFT JOIN transaction_payments AS tp ON t.id = tp.transaction_id
		WHERE cc.id = cashier_closure_id AND dt.short_name = 'Ticket'
		
		UNION ALL
			
		SELECT
			rt.cashier_closure_id,
			rt.correlative,
			rt.final_total AS return_amount,
			0 AS cash_amount,
			0 AS card_amount,
			0 AS check_amount,
			0 AS bank_transfer_amount,
			rt.final_total
		FROM transactions AS t
		INNER JOIN transactions AS rt ON t.id = rt.return_parent_id
		INNER JOIN document_types AS dt ON t.document_types_id = dt.id
		WHERE rt.cashier_closure_id = cashier_closure_id;
	
	SELECT
		MIN(correlative) AS min_correlative,
		MAX(correlative) AS max_correlative,
		SUM(return_amount) AS return_amount,
		SUM(cash_amount) AS cash_amount,
		SUM(card_amount) AS card_amount,
		SUM(check_amount) AS check_amount,
		SUM(bank_transfer_amount) AS bank_transfer_amount
	FROM daily_z_cut;
END; $$
DELIMITER ;

CALL getDailyZCutReport(14);
DROP PROCEDURE IF EXISTS getDailyZCutReport;