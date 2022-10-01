DROP FUNCTION IF EXISTS monetary_total_stock;

DELIMITER $$

CREATE FUNCTION monetary_total_stock(
    v_business_id INT,
    v_location_id INT,
    v_date VARCHAR(10)
)
RETURNS DECIMAL(20, 6)

READS SQL DATA
DETERMINISTIC

BEGIN
    DECLARE c_total_stock DECIMAL(20, 6);

    DROP TEMPORARY TABLE IF EXISTS total_stocks;

    CREATE TEMPORARY TABLE total_stocks AS
        SELECT
            k.variation_id,
            (SUM(k.inputs_quantity) - SUM(k.outputs_quantity)) AS stock
        FROM kardexes AS k
        JOIN warehouses AS w ON w.id = k.warehouse_id
            AND (v_location_id = 0 OR k.business_location_id = v_location_id)
            AND DATE(k.date_time) <= v_date
        WHERE w.business_id = v_business_id
        GROUP BY k.variation_id;

    SET c_total_stock = (
        SELECT
            SUM(v.default_purchase_price * ts.stock) AS total
        FROM total_stocks AS ts
        JOIN variations AS v ON v.id = ts.variation_id
        LIMIT 1
    );

    DROP TEMPORARY TABLE IF EXISTS total_stocks;

    RETURN c_total_stock;
END; $$

DELIMITER ;