DROP PROCEDURE IF EXISTS get_sales_n_adjustments_report;

DELIMITER $$

CREATE PROCEDURE get_sales_n_adjustments_report(
    v_location_id INT,
    v_month INT,
    v_business_id INT
)

BEGIN

	SELECT
        p.sku AS sku,
        p.name AS product,
        v.default_purchase_price AS unit_cost,
        v.sell_price_inc_tax AS unit_price,
        u.short_name as unit,
        (
            SELECT SUM(tsl.quantity - tsl.quantity_returned)
            FROM transactions AS t
            JOIN transaction_payments  AS tp
                ON t.id = tp.transaction_id
            LEFT JOIN transaction_sell_lines AS tsl
                ON t.id = tsl.transaction_id
            WHERE t.status = 'final'
                AND t.type = 'sell'
                AND t.location_id = v_location_id
                AND MONTH(t.transaction_date) = v_month
            	AND YEAR(t.transaction_date) = YEAR(NOW())
            	AND v.id = tsl.variation_id
        ) AS total_sold,
        (
            SELECT SUM(pl.quantity)
            FROM transactions AS t
            LEFT JOIN purchase_lines AS pl
                ON t.id = pl.transaction_id
            WHERE t.status = 'received'
                AND t.type = 'stock_adjustment'
            	AND t.adjustment_type = 'normal'
                AND t.location_id = v_location_id
                AND MONTH(t.transaction_date) = v_month
            	AND YEAR(t.transaction_date) = YEAR(NOW())
            	AND pl.variation_id = v.id
        ) AS input_adjustment,
        (
            SELECT SUM(tsl.quantity)
            FROM transactions AS t
            LEFT JOIN transaction_sell_lines AS tsl
                ON t.id = tsl.transaction_id
            WHERE t.status = 'received'
                AND t.type = 'stock_adjustment'
            	AND t.adjustment_type = 'abnormal'
                AND t.location_id = v_location_id
                AND MONTH(t.transaction_date) = v_month
            	AND YEAR(t.transaction_date) = YEAR(NOW())
            	AND tsl.variation_id = v.id
        ) AS output_adjustment
    FROM variations AS v
    LEFT JOIN products AS p
        ON p.id = v.product_id
    LEFT JOIN units AS u
        ON p.unit_id = u.id
    WHERE p.business_id = v_business_id
    GROUP BY v.id
    ORDER BY p.sku;

END; $$

DELIMITER ;