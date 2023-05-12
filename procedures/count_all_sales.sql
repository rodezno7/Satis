SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS count_all_sales;

DELIMITER $$

CREATE PROCEDURE count_all_sales(
    v_business_id INT,
    v_location_id INT,
    v_document_type_id INT,
    v_created_by INT,
    v_customer_id INT,
   	IN v_seller_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10),
    v_is_direct_sale INT,
    v_commission_agent INT,
    v_payment_status VARCHAR(20),
    v_search VARCHAR(255)
)

BEGIN

    SELECT
        COUNT(DISTINCT(t.id)) AS count
    FROM transactions AS t
    INNER JOIN customers AS c
        ON t.customer_id = c.id
    INNER JOIN document_types AS dt
        ON t.document_types_id = dt.id
    LEFT JOIN transaction_payments AS tp
        ON t.id = tp.transaction_id
    INNER JOIN business_locations AS bl
        ON t.location_id = bl.id
    WHERE t.business_id = v_business_id
        AND t.type = 'sell'
        AND t.status IN ('final', 'annulled')
        AND (v_created_by = 0 OR t.created_by = v_created_by)
        AND (v_location_id = 0 OR t.location_id = v_location_id)
        AND (v_customer_id = 0 OR t.customer_id = v_customer_id)
        AND ((v_start = '' AND v_end = '') OR (DATE(t.transaction_date) BETWEEN v_start AND v_end))
        AND (v_is_direct_sale <> 0 OR t.is_direct_sale = 0)
        AND (v_commission_agent = 0 OR t.commission_agent = v_commission_agent)
        AND (v_document_type_id = 0 OR t.document_types_id = v_document_type_id)
        AND (v_payment_status = '' OR IF (v_payment_status = 'paid', t.payment_status = 'paid', t.payment_status <> 'paid'))
        AND (
            t.correlative LIKE CONCAT('%', v_search, '%') OR
            c.name LIKE CONCAT('%', v_search, '%') OR
            t.customer_name LIKE CONCAT('%', v_search, '%') OR
            tp.note LIKE CONCAT('%', v_search, '%') OR
            t.payment_condition LIKE CONCAT('%', v_search, '%') OR
            tp.method LIKE CONCAT('%', v_search, '%')
        )
        AND (v_seller_id = 0 OR c.customer_portfolio_id = v_seller_id)
        LIMIT 1;

END; $$

DELIMITER ;

CALL count_all_sales (3, 1, 0, 0, 0, 0, '2023-05-01', '2023-05-31', 0, 0, '', '')