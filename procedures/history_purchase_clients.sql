DELIMITER //

CREATE PROCEDURE get_purchases_clients(
    `initial_date` DATE,
    `final_date` DATE,
    `business` INT,
    `product` INT,
    `customer_id` INT
)
BEGIN
	IF `customer_id` IS NULL THEN
		SELECT
            p.id,
            DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,
            IF(cus.is_taxpayer = 1, cus.business_name, cus.`name`) AS name_customer,
            CONCAT(t.correlative, ' - ', dt.short_name) AS `document`,
            CONCAT(p.`name`,' (', v.sub_sku, ' )') AS product_name,
            FORMAT(tsl.quantity, 4) AS quantity,
            FORMAT(tsl.unit_price, 4) AS unit_price,
            FORMAT((tsl.quantity * tsl.unit_price), 4) AS total,
            t.payment_status AS `status`

        FROM transactions AS t
        JOIN business_locations AS bl ON t.location_id = bl.id
        JOIN document_types AS dt ON dt.id = t.document_types_id
        LEFT JOIN transaction_sell_lines AS tsl ON tsl.transaction_id = t.id
        JOIN products AS p ON p.id = tsl.product_id
        JOIN categories AS c ON c.id = p.category_id
        JOIN variations AS v ON v.product_id = p.id
        JOIN customers AS cus ON cus.id = t.customer_id
	
    WHERE DATE(transaction_date) BETWEEN initial_date AND final_date
        AND t.business_id = business
        AND t.type = 'sell'
        AND p.id = `product`
            
    GROUP BY DATE(transaction_date)
    ORDER BY DATE(transaction_date), t.id;
	ELSE
		SELECT
            DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,
            IF(cus.is_taxpayer = 1, cus.business_name, cus.`name`) AS name_customer,
            CONCAT(t.correlative, '-', dt.short_name) AS `document`,
            CONCAT(p.`name`,' (', v.sub_sku, ' )') AS product_name,
            FORMAT(tsl.quantity, 4) AS quantity,
            FORMAT(tsl.unit_price, 4) AS unit_price,
            FORMAT((tsl.quantity * tsl.unit_price), 4) AS total,
            t.payment_status AS `status`
	
        FROM transactions AS t
        JOIN business_locations AS bl ON t.location_id = bl.id
        LEFT JOIN document_types AS dt ON dt.id = t.document_types_id
        LEFT JOIN transaction_sell_lines AS tsl ON tsl.transaction_id = t.id
        JOIN products AS p ON p.id = tsl.product_id
        JOIN categories AS c ON c.id = p.category_id
        JOIN variations AS v ON v.product_id = p.id
        LEFT JOIN customers AS cus ON cus.id = t.customer_id

        WHERE DATE(transaction_date) BETWEEN initial_date AND final_date
            AND t.business_id = business
            AND t.type = 'sell'
            AND p.id = `product`
            AND cus.id = `customer_id`
            
        GROUP BY DATE(transaction_date)
        ORDER BY DATE(transaction_date), t.id;
	END IF;
END;
//
DELIMITER ;

DROP PROCEDURE IF EXISTS get_purchases_clients;
CALL get_purchases_clients('2021-03-15','2021-04-15',3, 502, 2168);    
