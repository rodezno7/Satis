DROP PROCEDURE IF EXISTS get_all_sales_report;

DELIMITER $$

CREATE PROCEDURE get_all_sales_report(
    v_business_id INT,
    v_location_id INT,
    v_document_type_id INT,
    v_created_by INT,
    v_customer_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10),
    v_is_direct_sale INT,
    v_commission_agent INT,
    v_payment_status VARCHAR(20)
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
        tp.method,
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
    GROUP BY t.id
    ORDER BY t.transaction_date, t.correlative;

END; $$

DELIMITER ;