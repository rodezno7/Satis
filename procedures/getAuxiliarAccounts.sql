DELIMITER $$

USE `envex-erp-nuves`$$

DROP PROCEDURE IF EXISTS `getAuxiliarAccounts`$$

CREATE PROCEDURE `getAuxiliarAccounts`(IN start_date DATE, IN end_date DATE, IN _business_id INT)
BEGIN
	## JANUARY 1ST OF CURRENT YEAR
	SET @initial_date := (SELECT CAST(DATE_FORMAT(start_date, '%Y-01-01') AS DATE));
	## DEBIT INITIAL
	CREATE TEMPORARY TABLE debit_initial AS
		SELECT
			c.id,
			SUM(aed.debit) AS amount
		FROM catalogues AS c
		LEFT JOIN accounting_entries_details AS aed ON c.id = aed.account_id
		LEFT JOIN accounting_entries AS ae ON aed.entrie_id = ae.id
		WHERE 	ae.status = 1
			AND ae.`date` >= @initial_date
			AND ae.`date` < start_date
			AND c.`business_id` = _business_id
			AND (aed.debit <> 0)
		GROUP BY c.id;
		
	## CREDIT INITIAL
	CREATE TEMPORARY TABLE credit_initial AS
		SELECT
			c.id,
			SUM(aed.credit) AS amount
		FROM catalogues AS c
		LEFT JOIN accounting_entries_details AS aed ON c.id = aed.account_id
		LEFT JOIN accounting_entries AS ae ON aed.entrie_id = ae.id
		WHERE 	ae.status = 1
			AND ae.`date` >= @initial_date
			AND ae.`date` < start_date
			AND c.`business_id` = _business_id
			AND (aed.credit <> 0)
		GROUP BY c.id;
		
	## DEBIT FINAL
	CREATE TEMPORARY TABLE debit_final AS
		SELECT
			c.id,
			SUM(aed.debit) AS amount
		FROM catalogues AS c
		LEFT JOIN accounting_entries_details AS aed ON c.id = aed.account_id
		LEFT JOIN accounting_entries AS ae ON aed.entrie_id = ae.id
		WHERE 	ae.status = 1
			AND ae.`date` >= @initial_date
			AND ae.`date` <= end_date
			AND c.`business_id` = _business_id
			AND (aed.debit <> 0)
		GROUP BY c.id;
		
	## CREDIT FINAL
	CREATE TEMPORARY TABLE credit_final AS
		SELECT
			c.id,
			SUM(aed.credit) AS amount
		FROM catalogues AS c
		LEFT JOIN accounting_entries_details AS aed ON c.id = aed.account_id
		LEFT JOIN accounting_entries AS ae ON aed.entrie_id = ae.id
		WHERE 	ae.status = 1
			AND ae.`date` >= @initial_date
			AND ae.`date` <= end_date
			AND c.`business_id` = _business_id
			AND (aed.credit <> 0)
		GROUP BY c.id;
		
	## DEBIT RANGE
	CREATE TEMPORARY TABLE debit_range AS
		SELECT
			c.id,
			SUM(aed.debit) AS amount
		FROM catalogues AS c
		LEFT JOIN accounting_entries_details AS aed ON c.id = aed.account_id
		LEFT JOIN accounting_entries AS ae ON aed.entrie_id = ae.id
		WHERE 	ae.status = 1
			AND ae.`date` >= start_date
			AND ae.`date` <= end_date
			AND c.`business_id` = _business_id
			AND (aed.debit <> 0)
		GROUP BY c.id;
		
	## CREDIT RANGE
	CREATE TEMPORARY TABLE credit_range AS
		SELECT
			c.id,
			SUM(aed.credit) AS amount
		FROM catalogues AS c
		LEFT JOIN accounting_entries_details AS aed ON c.id = aed.account_id
		LEFT JOIN accounting_entries AS ae ON aed.entrie_id = ae.id
		WHERE 	ae.status = 1
			AND ae.`date` >= start_date
			AND ae.`date` <= end_date
			AND c.`business_id` = _business_id
			AND (aed.credit <> 0)
		GROUP BY c.id;
	
	## SHOW RESULT
	SELECT
		c.*,
		c.id AS account_query,
		(SELECT
			SUM(di.amount)
		FROM debit_initial AS di
		WHERE di.id = account_query) AS debit_initial,
		
		(SELECT
			SUM(ci.amount)
		FROM credit_initial AS ci
		WHERE ci.id = account_query) AS credit_initial,
		
		(SELECT
			SUM(df.amount)
		FROM debit_final AS df
		WHERE df.id = account_query) AS debit_final,
		
		(SELECT
			SUM(cf.amount)
		FROM credit_final AS cf
		WHERE cf.id = account_query) AS credit_final,
		
		(SELECT
			SUM(dr.amount)
		FROM debit_range AS dr
		WHERE dr.id = account_query) AS debit_range,
		
		(SELECT
			SUM(cr.amount)
		FROM credit_range AS cr
		WHERE cr.id = account_query) AS credit_range
		
	FROM catalogues AS c
	WHERE c.id NOT IN (SELECT parent FROM catalogues)
	AND c.`business_id` = _business_id
	GROUP BY c.code
	ORDER BY CONVERT(c.code, CHAR) ASC;
	
	DROP TEMPORARY TABLE IF EXISTS debit_initial;
	DROP TEMPORARY TABLE IF EXISTS credit_initial;
	DROP TEMPORARY TABLE IF EXISTS debit_final;
	DROP TEMPORARY TABLE IF EXISTS credit_final;
	DROP TEMPORARY TABLE IF EXISTS debit_range;
	DROP TEMPORARY TABLE IF EXISTS credit_range;
	
END$$

DELIMITER ;