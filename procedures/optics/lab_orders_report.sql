DROP PROCEDURE IF EXISTS lab_orders_report;

DELIMITER $$

CREATE PROCEDURE lab_orders_report(
    v_business_id INT,
    v_location_id INT,
    v_status_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10)
)

BEGIN

	SELECT
        lo.no_order,
        t.correlative,
        dt.document_name AS document,
        IF (lo.transaction_id IS NULL, blo.name, bl.name) AS location,
        IF (c.is_default = 1, t.customer_name, c.name) AS customer,
        p.full_name AS patient,
        slo.name AS status,
        lo.delivery,
        lo.created_at,
        slo.color
    FROM lab_orders AS lo
    LEFT JOIN transactions AS t
        ON t.id = lo.transaction_id
    LEFT JOIN document_types AS dt
        ON dt.id = t.document_types_id
    LEFT JOIN business_locations AS bl
        ON bl.id = t.location_id
    LEFT JOIN business_locations AS blo
        ON blo.id = lo.business_location_id
    LEFT JOIN customers AS c
        ON c.id = lo.customer_id
    LEFT JOIN patients AS p
        ON p.id = lo.patient_id
    LEFT JOIN status_lab_orders AS slo
        ON slo.id = lo.status_lab_order_id
    WHERE lo.business_id = v_business_id
        AND (v_location_id = 0 OR t.location_id = v_location_id OR lo.business_location_id = v_location_id)
        AND (v_status_id = 0 OR lo.status_lab_order_id = v_status_id)
        AND ((v_start = '' AND v_end = '') OR (DATE(lo.created_at) BETWEEN v_start AND v_end))
    GROUP BY lo.id
    ORDER BY lo.created_at DESC;

END; $$

DELIMITER ;