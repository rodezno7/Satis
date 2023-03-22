DROP PROCEDURE IF EXISTS detailed_commissions_report;

DELIMITER $$

CREATE PROCEDURE detailed_commissions_report(
    v_business_id INT,
    v_location_id INT,
    v_commission_agent INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10),
    v_search VARCHAR(255),
    v_start_record INT,
    v_page_size INT
)

BEGIN

    SELECT
        DATE_FORMAT(t.transaction_date, '%Y') AS `year`,
        DATE_FORMAT(t.transaction_date, '%m') AS `month`,
        DATE_FORMAT(t.transaction_date, '%d') AS `day`,
        t.transaction_date AS transaction_date,
        t.correlative AS doc_no,
        dt.short_name AS doc_type,
        IF (t.payment_condition = 'cash', 'Contado', 'Crédito') AS payment_condition,
        c.id AS customer_id,
        IF(c.is_default = 1, t.customer_name, c.name) AS customer_name,
        cat.name AS category,
        sub_cat.name AS sub_category,
        b.name AS brand_name,
        p.sku,
        p.name AS product_name,
        tsl.quantity,
        IF (dt.tax_exempt = 1, tsl.unit_price_before_discount * tsl.quantity, tsl.unit_price * tsl.quantity) AS price_inc,
        (tsl.unit_price_before_discount * tsl.quantity) AS price_exc,
        IF (dt.tax_exempt = 1, tsl.unit_price_before_discount, tsl.unit_price) AS unit_price,
        CONCAT(e.first_name, ' ', e.last_name) AS seller_name,
        v.default_purchase_price AS unit_cost,
        (v.default_purchase_price * tsl.quantity) AS total_cost,
        cp.name AS portfolio_name,
        s.name AS state,
        ct.name AS city,
        bl.name as location
    FROM transactions AS t
    INNER JOIN transaction_sell_lines AS tsl
        ON t.id = tsl.transaction_id
    INNER JOIN customers AS c
        ON t.customer_id = c.id
    INNER JOIN document_types AS dt
        ON t.document_types_id = dt.id
    INNER JOIN variations AS v
        ON tsl.variation_id = v.id 
    INNER JOIN products AS p
        ON tsl.product_id = p.id
    LEFT JOIN categories AS cat
        ON p.category_id = cat.id
    LEFT JOIN categories AS sub_cat
        ON p.sub_category_id = sub_cat.id
    LEFT JOIN brands AS b
        ON p.brand_id = b.id
    LEFT JOIN customer_portfolios AS cp
        ON c.customer_portfolio_id = cp.id
    LEFT JOIN employees AS e
        ON cp.seller_id = e.id
    LEFT JOIN states AS s
        ON c.state_id = s.id
    LEFT JOIN cities AS ct
        ON c.city_id = ct.id
    LEFT JOIN business_locations AS bl
        ON t.location_id = bl.id
    WHERE t.business_id = v_business_id
        AND t.`type` = 'sell'
        AND t.status = 'final'
        AND (v_location_id = 0 OR t.location_id = v_location_id)
        AND (v_commission_agent = 0 OR cp.seller_id = v_commission_agent)
        AND ((v_start = '' AND v_end = '') OR (DATE(t.transaction_date) BETWEEN v_start AND v_end))
        AND (
            t.correlative LIKE CONCAT('%', v_search, '%') OR
            dt.short_name LIKE CONCAT('%', v_search, '%') OR
            t.customer_name LIKE CONCAT('%', v_search, '%') OR
            c.name LIKE CONCAT('%', v_search, '%') OR
            cat.name LIKE CONCAT('%', v_search, '%') OR
            sub_cat.name LIKE CONCAT('%', v_search, '%') OR
            b.name LIKE CONCAT('%', v_search, '%') OR
            p.sku LIKE CONCAT('%', v_search, '%') OR
            p.name LIKE CONCAT('%', v_search, '%') OR
            CONCAT(e.first_name, ' ', e.last_name) LIKE CONCAT('%', v_search, '%') OR
            cp.name LIKE CONCAT('%', v_search, '%') OR
            s.name LIKE CONCAT('%', v_search, '%') OR
            ct.name LIKE CONCAT('%', v_search, '%') OR
            bl.name LIKE CONCAT('%', v_search, '%')
        )
    ORDER BY t.transaction_date DESC
    LIMIT v_start_record, v_page_size;

END; $$

DELIMITER ;