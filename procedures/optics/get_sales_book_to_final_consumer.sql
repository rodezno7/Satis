DROP PROCEDURE IF EXISTS get_sales_book_to_final_consumer;

DELIMITER $$

CREATE PROCEDURE get_sales_book_to_final_consumer(
    initial_date DATE,
    final_date DATE,
    location INT,
    business INT
)

BEGIN

	SELECT
		-- MySQL:
		ANY_VALUE(t.transaction_date) AS transaction_date,
		-- MariaDB:
		-- t.transaction_date AS transaction_date,
		t.document_types_id,
		MIN(t.correlative) AS initial_correlative,
		MAX(t.correlative) AS final_correlative,
		IF (t.document_types_id = (
				SELECT id
				FROM document_types
				WHERE business_id = business
					AND is_active = 1
					AND short_name = 'FACTURA'
			),
			SUM(t.final_total),
			NULL
		) AS taxed_sales,
		IF (t.document_types_id = (
				SELECT id
				FROM document_types
				WHERE business_id = business
					AND is_active = 1
					AND short_name = 'EXP'
			),
			SUM(t.final_total),
			NULL
		) AS exports,
        t.serie,
        t.resolution,
        dt.short_name
	FROM transactions AS t
    LEFT JOIN document_types AS dt
        ON dt.id = t.document_types_id
	WHERE DATE(t.transaction_date) BETWEEN initial_date AND final_date
		AND t.location_id = location
		AND t.business_id = business
		AND t.document_types_id = ANY(
			SELECT id
			FROM document_types
			WHERE business_id = business
				AND is_active = 1
				AND (short_name = 'FACTURA' OR short_name = 'EXP')
		)
		AND type = 'sell'
		AND status = 'final'
	GROUP BY DATE(t.transaction_date), t.document_types_id
	ORDER BY DATE(t.transaction_date);

END; $$

DELIMITER ;