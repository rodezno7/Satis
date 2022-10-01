/** CASH REGISTER REPORT **/
CREATE PROCEDURE getNewCashRegisterReport(IN cashier_closure_id INT)
BEGIN
	/** GET TRANSACTIONS SELL LINES */
	DROP TEMPORARY TABLE IF EXISTS transaction_sell_lines_tmp;
	CREATE TEMPORARY TABLE transaction_sell_lines_tmp AS
		SELECT
			tsl.transaction_id,
			SUM(tsl.tax_amount) AS tax_amount
		FROM transactions AS t
		INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
		WHERE t.cashier_closure_id = cashier_closure_id
		GROUP BY tsl.transaction_id;
	
	/** PAYMENTS */
	DROP TEMPORARY TABLE IF EXISTS cashier_closure_transactions;
	CREATE TEMPORARY TABLE cashier_closure_transactions AS
		SELECT
			t.id,
			t.transaction_date,
			dt.short_name AS doc_type,
			CONVERT(t.correlative, UNSIGNED INTEGER) AS correlative,
			IF(t.status = 'annulled', 'A  N  U  L  A  D  A',
				IF(c.is_default = 1, t.customer_name, c.name)) AS customer_name,
			IF(t.status = 'annulled', '-', q.delivery_type) AS delivery_type,
			SUM(IF(tp.`method` = 'cash', IF(tp.is_return, tp.amount * -1, tp.amount), 0)) AS cash_amount,
			SUM(IF(tp.`method` = 'card', IF(tp.is_return, tp.amount * -1, tp.amount), 0)) AS card_amount,
			SUM(IF(tp.`method` = 'check', IF(tp.is_return, tp.amount * -1, tp.amount), 0)) AS check_amount,
			SUM(IF(tp.`method` = 'bank_transfer', IF(tp.is_return, tp.amount * -1, tp.amount), 0)) AS bank_transfer_amount,
			0 AS return_amount,
			(SELECT COUNT(id)
				FROM transaction_payments AS tm_tp
				WHERE transaction_id = t.id AND tm_tp.is_return = 0
					AND DATE(t.transaction_date) = DATE(tm_tp.paid_on)) AS payment_count,
			IF(t.status = 'annulled', '-', t.payment_condition) AS payment_condition,
			IF(t.status = 'annulled', '-',
				IF(dt.tax_inc = 0,
					((t.total_before_tax - t.discount_amount) - tslt.tax_amount),
					(t.total_before_tax - t.discount_amount))) AS subtotal,
			IF(t.status = 'annulled', 0, t.discount_amount) AS discount_amount,
			IF(t.status = 'annulled', 0, t.tax_amount) AS withheld_amount,
			IF(t.status = 'annulled', 0, tslt.tax_amount) AS tax_amount,
			IF(t.status = 'annulled', 0, t.final_total) AS final_total
		FROM transactions AS t
		INNER JOIN transaction_sell_lines_tmp AS tslt ON t.id = tslt.transaction_id
		INNER JOIN customers AS c ON t.customer_id = c.id
		INNER JOIN document_types AS dt ON t.document_types_id = dt.id 
		LEFT JOIN transaction_payments AS tp ON t.id = tp.transaction_id
		LEFT JOIN quotes AS q ON t.id = q.transaction_id
		WHERE t.cashier_closure_id = cashier_closure_id
			AND (DATE(t.transaction_date) = DATE(tp.paid_on) OR tp.id IS NULL)
		GROUP BY t.id
		
		UNION ALL
	
		/** SELL RETURNS */
		SELECT
			rt.id,
			rt.transaction_date,
			dt.short_name AS doc_type,
			CONVERT(rt.correlative, UNSIGNED INTEGER) AS correlative,
			IF(c.is_default = 1, t.customer_name, c.name) AS customer_name,
			NULL AS delivered_type,
			NULL AS cash_amount,
			NULL AS card_amount,
			NULL AS check_amount,
			NULL AS bank_transfer_amount,
			rt.final_total AS return_amount,
			1 AS payment_count,
			'sell_return' AS payment_condition,
			(rt.final_total / 1.13) AS subtotal,
			0 AS discount_amount,
			0 AS withheld_amount,
			(rt.final_total - (rt.final_total / 1.13)) AS tax_amount,
			rt.final_total AS final_total
		FROM transactions AS t
		INNER JOIN transactions AS rt ON t.id = rt.return_parent_id
		INNER JOIN document_types AS dt ON t.document_types_id = dt.id
		INNER JOIN customers AS c ON t.customer_id = c.id
		WHERE rt.cashier_closure_id = cashier_closure_id;
	
	SELECT
		id,
		transaction_date,
		doc_type,
		correlative,
		customer_name,
		delivery_type,
		cash_amount,
		card_amount,
		check_amount,
		bank_transfer_amount,
		return_amount,
		(final_total - (cash_amount + card_amount + check_amount + bank_transfer_amount) - return_amount) AS credit_amount,
		payment_count,
		payment_condition,
		subtotal,
		discount_amount,
		withheld_amount,
		tax_amount,
		final_total
	FROM cashier_closure_transactions
	GROUP BY id
	ORDER BY correlative ASC, doc_type ASC;
	
	DROP TEMPORARY TABLE IF EXISTS transaction_sell_lines_tmp;
	DROP TEMPORARY TABLE IF EXISTS cashier_closure_transactions;
END;

CALL getNewCashRegisterReport(84);
DROP PROCEDURE IF EXISTS getNewCashRegisterReport;


/** OLD CASH REGISTER REPORT **/
CREATE PROCEDURE getCashRegisterReport(IN business_id INT, IN cashier_id INT, IN transaction_date DATE)
BEGIN
	SELECT
		t.id,
		crt.cash_register_id,
		t.transaction_date,
		dt.short_name AS doc_type,
		CONVERT(t.correlative, UNSIGNED INTEGER) AS correlative,
		IF(t.status = 'annulled', 'A  N  U  L  A  D  A',
			IF(c.is_default = 1, t.customer_name, c.name)) AS customer_name,
		IF(t.status = 'annulled', '-', q.delivery_type) AS delivery_type,
		IF(t.status = 'annulled', '-', crt.pay_method) AS pay_method,
		(SELECT
			IFNULL(SUM(tmp_crt1.amount), '-')
		FROM cash_register_transactions AS tmp_crt1
		WHERE tmp_crt1.transaction_id = t.id
			AND t.status != 'annulled'
			AND tmp_crt1.pay_method = crt.pay_method) AS payment_amount,
		crt.created_at,
		crt.updated_at,
		(SELECT
			COUNT(tmp_crt2.id)
		FROM cash_register_transactions AS tmp_crt2
		WHERE tmp_crt2.amount > 0
			AND tmp_crt2.transaction_id = t.id
			AND t.status != 'annulled') AS payment_count,
		IF(t.status = 'annulled', '-', t.payment_condition) AS payment_condition,
		IF(t.status = 'annulled', '-',
			IF(dt.tax_inc = 0,
				((t.total_before_tax - t.discount_amount) - SUM(IF(crt.amount > 0, tsl.tax_amount, 0))),
				(t.total_before_tax - t.discount_amount))) AS subtotal,
		IF(t.status = 'annulled', 0, t.discount_amount) AS discount_amount,
		IF(t.status = 'annulled', 0, t.tax_amount) AS withheld_amount,
		IF(t.status = 'annulled', 0, SUM(IF(crt.amount > 0, tsl.tax_amount, 0))) AS tax_amount,
		IF(t.status = 'annulled', 0, t.final_total) AS final_total
	FROM transactions AS t
	INNER JOIN transaction_sell_lines AS tsl ON t.id = tsl.transaction_id
	INNER JOIN cash_register_transactions AS crt ON t.id = crt.transaction_id
	INNER JOIN customers AS c ON t.customer_id = c.id
	INNER JOIN document_types AS dt ON t.document_types_id = dt.id 
	LEFT JOIN quotes AS q ON t.id = q.transaction_id
	WHERE t.`type` = 'sell'
		AND t.status IN ('final', 'annulled')
		AND DATE(t.transaction_date) = transaction_date
		AND t.business_id = business_id
		AND t.cashier_id = cashier_id
	GROUP BY t.id, crt.pay_method
	ORDER BY t.correlative ASC, dt.short_name ASC;
END;

CALL getCashRegisterReport(3, 1, '2021-06-21');
DROP PROCEDURE IF EXISTS getCashRegisterReport;