DROP PROCEDURE IF EXISTS sales_tracking_report;

DELIMITER $$

CREATE PROCEDURE sales_tracking_report(
    v_business_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10),
    v_customer_id INT,
    v_invoiced INT,
    v_delivery_type VARCHAR(15),
    v_employee_id INT
)

BEGIN

    SELECT
        q.quote_ref_no AS code,
        q.quote_date,
        q.customer_name AS customer,
        q.delivery_type,
        IF (q.invoiced >= 1, 'yes', 'no') AS invoiced,
        q.total_final AS quoted_amount,
        t.final_total AS invoiced_amount,
        CONCAT(e.first_name, ' ', e.last_name) AS seller,
        q.customer_id
    FROM quotes AS q
    LEFT JOIN transactions AS t
        ON t.id = q.transaction_id
    INNER JOIN employees AS e
        ON e.id = q.employee_id
    WHERE q.business_id = v_business_id
        AND q.type = 'order'
        AND ((v_start = '' AND v_end = '') OR (DATE(q.quote_date) BETWEEN v_start AND v_end))
        AND (v_customer_id = 0 OR q.customer_id = v_customer_id)
        AND (v_invoiced = -1 OR q.invoiced = v_invoiced)
        AND (v_delivery_type = '' OR q.delivery_type = v_delivery_type)
        AND (v_employee_id = 0 OR q.employee_id = v_employee_id)
        AND q.deleted_at IS NULL
    ORDER BY q.quote_ref_no;

END; $$

DELIMITER ;