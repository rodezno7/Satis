SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_journal_book;

DELIMITER $$
CREATE PROCEDURE get_journal_book(
	IN start_date DATE,
	IN end_date DATE,
	IN business_id INT,
	IN sorted INT)
BEGIN
	/** Get debits */
	DROP TEMPORARY TABLE IF EXISTS debits;
	CREATE TEMPORARY TABLE debits AS
		SELECT
			ae.`date`,
			DAY(ae.`date`) AS `day`, 
			ae.correlative,
			c.code AS account_code,
			c.name AS account_name,
			aed.debit,
			aed.credit
		FROM accounting_entries AS ae
		INNER JOIN accounting_entries_details AS aed ON ae.id = aed.entrie_id
		INNER JOIN catalogues AS c ON aed.account_id = c.id
		WHERE ae.business_id = business_id
			AND ae.`date` BETWEEN start_date AND end_date
			AND aed.debit > 0
		GROUP BY aed.id
		ORDER BY ae.correlative ASC, DATE(ae.`date`) ASC;
	
	/** Get credits */
	DROP TEMPORARY TABLE IF EXISTS credits;
	CREATE TEMPORARY TABLE credits AS
		SELECT
			ae.`date`,
			DAY(ae.`date`) AS `day`,
			ae.correlative,
			c.code AS account_code,
			c.name AS account_name,
			aed.debit,
			aed.credit
		FROM accounting_entries AS ae
		INNER JOIN accounting_entries_details AS aed ON ae.id = aed.entrie_id
		INNER JOIN catalogues AS c ON aed.account_id = c.id
		WHERE ae.business_id = business_id
			AND ae.`date` BETWEEN start_date AND end_date
			AND aed.credit > 0
		GROUP BY aed.id
		ORDER BY ae.correlative ASC, DATE(ae.`date`) ASC;
	
	/** UNION debits and credits */
	IF sorted = 1 THEN
		SELECT * FROM debits
		UNION ALL
		SELECT * FROM credits
		ORDER BY `date`;
	ELSE
		SELECT * FROM debits
		UNION ALL
		SELECT * FROM credits;
	END IF;

	/** Drop temporary tables */
	DROP TEMPORARY TABLE IF EXISTS debits;
	DROP TEMPORARY TABLE IF EXISTS credits;
END; $$
DELIMITER ;

CALL get_journal_book('2023-05-01', '2023-05-31', 3, 1);