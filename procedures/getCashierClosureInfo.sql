/** GET CASHIER CLOSURE INFO **/
DELIMITER $$
CREATE PROCEDURE getCashierClosureInfo(IN cashier_closure_id INT)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS cashier_closure;
	CREATE TEMPORARY TABLE cashier_closure AS
		SELECT
			SUM(IF(tp.`method` = 'cash', IF(tp.is_return, tp.amount * -1, tp.amount), 0)) AS cash_amount,
			SUM(IF(tp.`method` = 'card', IF(tp.is_return, tp.amount * -1, tp.amount), 0)) AS card_amount,
			SUM(IF(tp.`method` = 'check', IF(tp.is_return, tp.amount * -1, tp.amount), 0)) AS check_amount,
			SUM(IF(tp.`method` = 'bank_transfer', IF(tp.is_return, tp.amount * -1, tp.amount), 0)) AS bank_transfer_amount,
			t.final_total
		FROM transactions AS t
		LEFT JOIN transaction_payments AS tp ON t.id = tp.transaction_id
		WHERE t.cashier_closure_id = cashier_closure_id
			AND t.status = 'final'
			AND t.`type` = 'sell'
		GROUP BY t.id;

	SET @initial_cash_amount :=
		IFNULL((SELECT
			initial_cash_amount
		FROM cashier_closures
		WHERE id = cashier_closure_id), 0);
	
	SET @return_amount :=
		IFNULL((SELECT
			SUM(rt.final_total)
		FROM transactions AS t
		INNER JOIN transactions AS rt ON t.id = rt.return_parent_id
		WHERE rt.cashier_closure_id = cashier_closure_id), 0);
		
	SELECT
		(SELECT @initial_cash_amount) AS initial_cash_amount,
		(SELECT @return_amount) AS return_amount,
		SUM(cash_amount) AS cash_amount,
		SUM(card_amount) AS card_amount,
		SUM(check_amount) AS check_amount,
		SUM(bank_transfer_amount) AS bank_transfer_amount,
		SUM(final_total - (cash_amount + card_amount + check_amount + bank_transfer_amount)) AS credit_amount,
		SUM(final_total) - @return_amount AS final_total
	FROM cashier_closure;
END; $$
DELIMITER ;

CALL getCashierClosureInfo(123);
DROP PROCEDURE IF EXISTS getCashierClosureInfo;