DROP PROCEDURE IF EXISTS get_all_sales_optics;

DELIMITER $$

CREATE PROCEDURE get_all_sales_optics(
    v_business_id INT,
    v_location_id INT,
    v_document_type_id INT,
    v_created_by INT,
    v_customer_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10),
    v_is_direct_sale INT,
    v_commission_agent INT,
    v_payment_status VARCHAR(20),
    v_search VARCHAR(255),
    v_start_record INT,
    v_page_size INT,
    v_order_column INT,
    v_order_dir VARCHAR(4)
)

BEGIN

    SELECT
        t.id AS id,
        t.transaction_date,
        t.is_direct_sale,
        t.status,
        t.correlative,
        dt.document_name,
        dt.tax_inc,
        c.id AS customer_id,
        t.id AS idt,
        c.name AS customer_b_name,
        t.correlative AS invoice_no,
        IF (c.is_default = 1, t.customer_name, c.name) AS customer_name,
        t.payment_status,
        -- MySQL
        ANY_VALUE(tp.method) AS method,
        -- MariaDB
        -- tp.method,
        t.payment_condition,
        bl.name AS location,
        IF (t.status = 'annulled', 0.00, t.final_total) AS final_total,
        IF (t.status = 'annulled', 0.00, t.total_before_tax) AS total_before_tax,
        IF (t.status = 'annulled', 0.00, t.tax_amount) AS tax_amount,
        IF (t.status = 'annulled', 0.00, SUM(IF (tp.is_return = 1, -1 * tp.amount, tp.amount))) AS total_paid,
        COALESCE(t.total_amount_recovered, 0) AS amount_return,
        (SELECT COUNT(tp1.id) FROM transaction_payments AS tp1 WHERE tp1.transaction_id = t.id) AS count_payments,
        IF (t.status = "annulled", 0.00, t.discount_amount) AS discount_amount,
        t.discount_type,
        t.tax_group_amount,
        DATE_ADD(DATE(t.transaction_date), INTERVAL t.pay_term_number DAY) AS due_date,
        GROUP_CONCAT(DISTINCT tp.note ORDER BY tp.note SEPARATOR ', ') as note
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
            ((SELECT GROUP_CONCAT(DISTINCT tp1.note ORDER BY tp1.note SEPARATOR ', ') FROM transaction_payments AS tp1 JOIN transactions AS t1 ON t1.id = tp1.transaction_id WHERE t1.id = t.id) LIKE CONCAT('%', v_search, '%')) OR
            t.payment_condition LIKE CONCAT('%', v_search, '%') OR
            tp.method LIKE CONCAT('%', v_search, '%')
        )
    GROUP BY t.id
    ORDER BY
        CASE WHEN v_order_column = 0 AND v_order_dir = 'asc' THEN t.transaction_date END ASC,
        CASE WHEN v_order_column = 0 AND v_order_dir = 'desc' THEN t.transaction_date END DESC,
        CASE WHEN v_order_column = 1 AND v_order_dir = 'asc' THEN t.correlative END ASC,
        CASE WHEN v_order_column = 1 AND v_order_dir = 'desc' THEN t.correlative END DESC,
        CASE WHEN v_order_column = 2 AND v_order_dir = 'asc' THEN dt.document_name END ASC,
        CASE WHEN v_order_column = 2 AND v_order_dir = 'desc' THEN dt.document_name END DESC,
        CASE WHEN v_order_column = 3 AND v_order_dir = 'asc' THEN c.name END ASC,
        CASE WHEN v_order_column = 3 AND v_order_dir = 'desc' THEN c.name END DESC,
        CASE WHEN v_order_column = 4 AND v_order_dir = 'asc' THEN bl.name END ASC,
        CASE WHEN v_order_column = 4 AND v_order_dir = 'desc' THEN bl.name END DESC,
        CASE WHEN v_order_column = 5 AND v_order_dir = 'asc' THEN t.payment_status END ASC,
        CASE WHEN v_order_column = 5 AND v_order_dir = 'desc' THEN t.payment_status END DESC,
        CASE WHEN v_order_column = 6 AND v_order_dir = 'asc' THEN t.final_total END ASC,
        CASE WHEN v_order_column = 6 AND v_order_dir = 'desc' THEN t.final_total END DESC,
        CASE WHEN v_order_column = 7 AND v_order_dir = 'asc' THEN tp.note END ASC,
        CASE WHEN v_order_column = 7 AND v_order_dir = 'desc' THEN tp.note END DESC,
        CASE WHEN v_order_column = 8 AND v_order_dir = 'asc' THEN tp.amount END ASC,
        CASE WHEN v_order_column = 8 AND v_order_dir = 'desc' THEN tp.amount END DESC,
        CASE WHEN v_order_column = 9 AND v_order_dir = 'asc' THEN (t.final_total - tp.amount) END ASC,
        CASE WHEN v_order_column = 9 AND v_order_dir = 'desc' THEN (t.final_total - tp.amount) END DESC
    LIMIT v_start_record, v_page_size;
END; $$

DELIMITER ;