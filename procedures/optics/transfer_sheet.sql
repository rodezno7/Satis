DROP PROCEDURE IF EXISTS transfer_sheet;

DELIMITER $$

CREATE PROCEDURE transfer_sheet(
    v_business_id INT,
    v_transfer_date VARCHAR(10),
    v_warehouse_id INT
)

BEGIN

	SELECT
        w.name AS location,
        -- MySQL:
        ANY_VALUE(t.correlative) AS reference,
        -- MariaDB:
        -- t.correlative AS reference,
        SUM(tsl.quantity) AS quantity,
        -- MySQL:
        ANY_VALUE(p.name) AS description
        -- MariaDB:
        -- p.name AS description
    FROM
        transaction_sell_lines AS tsl
    LEFT JOIN transactions AS t
        ON t.id = tsl.transaction_id
    LEFT JOIN lab_orders AS lo
        ON lo.transaction_id = t.id
    LEFT JOIN products AS p
        ON p.id = tsl.product_id
    LEFT JOIN warehouses AS w
        ON w.id = t.warehouse_id
    WHERE lo.transfer_date = v_transfer_date
        AND t.warehouse_id = v_warehouse_id
        AND (
            lo.hoop = tsl.variation_id
            OR
            lo.glass = tsl.variation_id
            OR
            lo.glass_os = tsl.variation_id
            OR
            lo.glass_od = tsl.variation_id
        )
    GROUP BY tsl.variation_id, t.correlative;

END; $$

DELIMITER ;