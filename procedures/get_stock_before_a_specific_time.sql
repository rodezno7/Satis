DROP PROCEDURE IF EXISTS get_stock_before_a_specific_time;

DELIMITER $$

CREATE PROCEDURE get_stock_before_a_specific_time(
    v_business_id INT,
    v_variation_id INT,
    v_purchase_line_id INT,
    v_date VARCHAR(19),
    v_flag INT
)

BEGIN
    SELECT
        IFNULL(
            (
                (
                    SELECT
                        IFNULL(SUM(k1.inputs_quantity), 0)
                    FROM kardexes AS k1
                    WHERE k1.business_id = v_business_id
                        AND k1.variation_id = v_variation_id
                        AND k1.movement_type_id IN (SELECT id FROM movement_types AS m1 WHERE m1.type = 'input')
                        AND ((v_flag = 1 AND k1.line_reference < v_purchase_line_id) OR (v_flag = 0))
                        AND ((v_flag = 1 AND k1.date_time <= v_date) OR (v_flag = 0 AND k1.date_time < v_date))
                )
                -
                (
                    SELECT
                        IFNULL(SUM(k2.outputs_quantity), 0)
                    FROM kardexes AS k2
                    WHERE k2.business_id = v_business_id
                        AND k2.variation_id = v_variation_id
                        AND k2.date_time < v_date
                        AND k2.movement_type_id IN (SELECT id FROM movement_types AS m2 WHERE m2.type = 'output')
                )
            ),
            0
        ) AS stock;

END; $$

DELIMITER ;