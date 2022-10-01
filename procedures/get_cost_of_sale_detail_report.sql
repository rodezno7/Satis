DROP PROCEDURE IF EXISTS get_cost_of_sale_detail_report;

DELIMITER $$

CREATE PROCEDURE get_cost_of_sale_detail_report(
    v_warehouse_id INT,
    v_business_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10)
)

BEGIN

	SELECT
        id,
        transaction_date,
        code,
        description,
        observation,
        document_type,
        reference,
        input,
        output,
        annulled,
        document_type_id,
        type
    FROM
    (
        -- Purchases
        SELECT
            t.id AS id,
            DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,
            p.sku AS code,
            p.name AS description,
            t.additional_notes AS observation,
            dt.short_name AS document_type,
            IF (t.type = 'purchase', t.ref_no, IF (t.type = 'sell_return', t.invoice_no, t.ref_no)) AS reference,
            IF (t.status <> 'annulled', pl.quantity, 0) AS input,
            0 AS output,
            IF (t.status = 'annulled', pl.quantity, 0) AS annulled,
            dt.id AS document_type_id,
            'input' AS type
        FROM transactions AS t
        JOIN purchase_lines AS pl
            ON t.id = pl.transaction_id
        LEFT JOIN products AS p
            ON p.id = pl.product_id
        LEFT JOIN document_types AS dt
            ON dt.id = t.document_types_id
        WHERE ((v_start = '' AND v_end = '') OR (DATE(t.transaction_date) BETWEEN v_start AND v_end))
            AND t.warehouse_id = v_warehouse_id
            AND t.business_id = v_business_id
            AND t.status = 'received'

        UNION

        -- Sales
        SELECT
            t.id AS id,
            DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,
            p.sku AS code,
            p.name AS description,
            t.additional_notes AS observation,
            dt.short_name AS document_type,
            IF (t.type = 'sell', t.correlative, IF (t.type = 'purchase_return', t.invoice_no, t.ref_no)) AS reference,
            0 AS input,
            IF (t.status <> 'annulled', tsl.quantity, 0) AS output,
            IF (t.status = 'annulled', tsl.quantity, 0) AS annulled,
            dt.id AS document_type_id,
            'output' AS type
        FROM transactions AS t
        JOIN transaction_sell_lines AS tsl
            ON t.id = tsl.transaction_id
        LEFT JOIN products AS p
            ON p.id = tsl.product_id
        LEFT JOIN document_types AS dt
            ON dt.id = t.document_types_id
        WHERE ((v_start = '' AND v_end = '') OR (DATE(t.transaction_date) BETWEEN v_start AND v_end))
            AND t.warehouse_id = v_warehouse_id
            AND t.business_id = v_business_id
            AND t.status = 'final'
    ) AS results

    ORDER BY code;

END; $$

DELIMITER ;