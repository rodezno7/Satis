SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS get_sales_book_to_final_consumer;

DELIMITER $$
CREATE PROCEDURE get_sales_book_to_final_consumer (
	initial_date DATE,
	final_date DATE,
	location INT,
	business INT)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS sales;
	CREATE TEMPORARY TABLE sales AS
		/** sales */
		SELECT
			t.transaction_date,
			t.document_types_id,
			t.location_id,
			dt.short_name,
			t.correlative,
			t.serie,
			REPLACE(t.resolution, '-', '') AS resolution,
			IF(t.status = 'annulled', 0,
				IF(dt.short_name IN ('FCF', 'Ticket'), t.final_total, 0)) AS taxed_sales,
			IF(t.status = 'annulled', 0,
				IF(dt.short_name = 'EXP', t.final_total, 0)) AS exports,
			t.status
		FROM transactions AS t
		INNER JOIN document_types AS dt ON t.document_types_id = dt.id
		LEFT JOIN transactions AS rt ON t.id = rt.return_parent_id
		WHERE DATE(t.transaction_date) BETWEEN initial_date AND final_date
			AND (t.location_id = location OR location = 0)
			AND t.business_id = business
			AND dt.short_name IN ('FCF', 'Ticket', 'EXP')
			AND t.`type` = 'sell'
			AND t.status IN ('final', 'annulled')
			
		UNION ALL
		
		/** sale returns */
		SELECT
			rt.transaction_date,
			t.document_types_id,
			t.location_id,
			dt.short_name,
			rt.correlative,
			rt.serie,
			rt.resolution,
			(rt.final_total * -1) AS taxed_sales,
			0 AS exports,
			t.status
		FROM transactions AS t
		INNER JOIN transactions AS rt ON t.id = rt.return_parent_id
		INNER JOIN document_types AS dt ON rt.document_types_id = dt.id
		WHERE DATE(rt.transaction_date) BETWEEN initial_date AND final_date
			AND (t.location_id = location OR location = 0)
			AND t.business_id = business
			-- AND YEAR(t.transaction_date) > 2021 #exclude 2021 sell return for Nuves
	
		UNION ALL
		
		/** opening cash register */
		SELECT
			cc.close_date AS transaction_date,
			9 AS document_types_id,
			c.business_location_id AS location_id,
			'Ticket' AS short_name,
			cc.open_correlative,
			NULL AS serie,
			NULL AS resolution,
			0 AS taxed_sales,
			0 AS exports,
			'final' AS status
		FROM cashier_closures AS cc
		INNER JOIN cashiers AS c ON cc.cashier_id = c.id
		WHERE DATE(cc.close_date) BETWEEN initial_date AND final_date
			AND cc.open_correlative <> ''
			AND (c.business_location_id = location OR location = 0)
		
		UNION ALL
		
		/** cashier closures */
		SELECT
			cc.close_date AS transaction_date,
			9 AS document_types_id,
			c.business_location_id AS location_id,
			'Ticket' AS short_name,
			cc.close_correlative,
			NULL AS serie,
			NULL AS resolution,
			0 AS taxed_sales,
			0 AS exports,
			'final' AS status
		FROM cashier_closures AS cc
		INNER JOIN cashiers AS c ON cc.cashier_id = c.id
		WHERE DATE(cc.close_date) BETWEEN initial_date AND final_date
			AND cc.close_correlative <> ''
			AND (c.business_location_id = location OR location = 0);
	
	SELECT
		DATE(transaction_date) AS transaction_date,
		location_id,
		short_name,
		MIN(CONVERT(correlative, UNSIGNED INTEGER)) AS initial_correlative,
		MAX(CONVERT(correlative, UNSIGNED INTEGER)) AS final_correlative,
		serie,
		resolution,
		SUM(taxed_sales) AS taxed_sales,
		SUM(exports) AS exports,
		status
	FROM sales
	GROUP BY DATE(transaction_date), document_types_id, location_id -- , short_name, serie, resolution
	ORDER BY location_id, document_types_id, DATE(transaction_date);

	DROP TEMPORARY TABLE IF EXISTS sales;
END; $$
DELIMITER ;

CALL get_sales_book_to_final_consumer('2023-04-01', '2023-04-30', 1, 3);