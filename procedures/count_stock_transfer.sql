DROP PROCEDURE IF EXISTS count_stock_transfer;

DELIMITER $$

CREATE PROCEDURE count_stock_transfer(
    v_sell_transfer_id INT
)

BEGIN
    START TRANSACTION;

    -- Entrie
    INSERT INTO
        accounting_entries (
            date,
            number,
            description,
            accounting_period_id,
            type_entrie_id,
            business_location_id,
            created_at,
            updated_at
        )
    SELECT
        t1.transaction_date,
        (SELECT MAX(ae.number) + 1 FROM accounting_entries AS ae) AS number,
        CONCAT(
            'Traslado de ',
            (SELECT w1.name FROM warehouses AS w1 WHERE w1.id = t1.warehouse_id),
            ' a ',
            (SELECT w2.name FROM transactions AS t2 JOIN warehouses AS w2 ON w2.id = t2.warehouse_id WHERE t2.transfer_parent_id = v_sell_transfer_id LIMIT 1)
        ) As description,
        (
            SELECT
                ap.id
            FROM accounting_periods AS ap
            WHERE ap.fiscal_year_id = (SELECT fy.id FROM fiscal_years AS fy WHERE fy.year = YEAR(t1.transaction_date) LIMIT 1)
                AND ap.month = MONTH(t1.transaction_date)
        ) AS accounting_period_id,
        (SELECT te.id FROM type_entries AS te WHERE te.name = 'Diario' LIMIT 1) AS type_entrie_id,
        t1.location_id AS business_location_id,
        NOW() AS created_at,
        NOW() AS updated_at
    FROM transactions AS t1
    WHERE t1.id = v_sell_transfer_id;

    SET @entrie_id := LAST_INSERT_ID();

    -- Entrie line (from warehouse)
    INSERT INTO
        accounting_entries_details (
            entrie_id,
            account_id,
            debit,
            credit,
            description,
            created_at,
            updated_at
        )
    SELECT
        @entrie_id AS entrie_id,
        (SELECT w3.catalogue_id FROM warehouses AS w3 WHERE w3.id = t3.warehouse_id) AS account_id,
        0 AS debit,
        t3.final_total AS credit,
        CONCAT(
            'Salida de productos para ',
            (SELECT w4.name FROM warehouses AS w4 WHERE w4.id = (SELECT t4.warehouse_id FROM transactions AS t4 WHERE t4.transfer_parent_id = v_sell_transfer_id)),
            ' Traslado #',
            t3.ref_no
        ) AS description,
        NOW() AS created_at,
        NOW() AS updated_at
    FROM transactions AS t3
    WHERE t3.id = v_sell_transfer_id;

    -- Entrie line (to warehouse)
    INSERT INTO
        accounting_entries_details (
            entrie_id,
            account_id,
            debit,
            credit,
            description,
            created_at,
            updated_at
        )
    SELECT
        @entrie_id AS entrie_id,
        (SELECT w5.catalogue_id FROM warehouses AS w5 WHERE w5.id = t5.warehouse_id) AS account_id,
        t5.final_total AS debit,
        0 AS credit,
        CONCAT(
            'Entrada de productos de ',
            (SELECT w6.name FROM warehouses AS w6 WHERE w6.id = (SELECT t6.warehouse_id FROM transactions AS t6 WHERE t6.id = v_sell_transfer_id)),
            ' Traslado #',
            t5.ref_no
        ) AS description,
        NOW() AS created_at,
        NOW() AS updated_at
    FROM transactions AS t5
    WHERE t5.transfer_parent_id = v_sell_transfer_id
    LIMIT 1;

    COMMIT;

END; $$

DELIMITER ;