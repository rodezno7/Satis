SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));
SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

DROP PROCEDURE IF EXISTS generate_stock_report;

DELIMITER $$
CREATE PROCEDURE generate_stock_report(
    v_business_id INT,
    v_location_id INT,
    v_warehouse_id INT,
    v_category_id INT,
    v_sub_category_id INT,
    v_brand_id INT,
    v_unit_id INT,
    v_contact_id INT,
    v_start VARCHAR(10),
    v_end VARCHAR(10),
    v_stock INT
)

BEGIN
	SET @stock := 0;
	IF v_stock = 1 THEN
		SET @stock := -1000000000;
	END IF;
	
    DROP TEMPORARY TABLE IF EXISTS stock;
	CREATE TEMPORARY TABLE stock AS
        SELECT
        	v.id AS variation_id,
            IFNULL((
                SELECT SUM(tsl1.quantity - tsl1.quantity_returned)
                FROM transactions AS t1
                INNER JOIN transaction_sell_lines AS tsl1
                    ON t1.id = tsl1.transaction_id
                WHERE t1.status = 'final'
                    AND t1.type = 'sell'
                    AND (v_location_id = 0 OR t1.location_id = v_location_id)
                    AND (v_warehouse_id = 0 OR t1.warehouse_id = v_warehouse_id)
                    AND ((v_start = '' AND v_end = '') OR (DATE(t1.transaction_date) BETWEEN v_start AND v_end))
                    AND tsl1.variation_id = v.id
            ), 0) AS total_sold,
            IFNULL((
                (
                    SELECT IFNULL(SUM(k1.inputs_quantity), 0)
                    FROM kardexes AS k1
                    WHERE k1.business_id = v_business_id
                        AND k1.variation_id = v.id
                        AND (v_location_id = 0 OR k1.business_location_id = v_location_id)
                        AND (v_warehouse_id = 0 OR k1.warehouse_id = v_warehouse_id)
                        AND ((v_end = '') OR (DATE(k1.date_time) <= DATE(v_end)))
                        AND k1.movement_type_id IN (SELECT id FROM movement_types AS m1 WHERE m1.type = 'input')
                )
                -
                (
                    SELECT IFNULL(SUM(k2.outputs_quantity), 0)
                    FROM kardexes AS k2
                    WHERE k2.business_id = v_business_id
                        AND k2.variation_id = v.id
                        AND (v_location_id = 0 OR k2.business_location_id = v_location_id)
                        AND (v_warehouse_id = 0 OR k2.warehouse_id = v_warehouse_id)
                        AND ((v_end = '') OR (DATE(k2.date_time) <= DATE(v_end)))
                        AND k2.movement_type_id IN (SELECT id FROM movement_types AS m2 WHERE m2.type = 'output')
                )
            ),0 ) AS stock,
            v.sub_sku as sku,
            IF(p.`type` = 'variable', CONCAT(p.name, "-", v.name), p.name) AS product,
            u.short_name AS unit,
            p.enable_stock AS enable_stock,
            IFNULL(v.sell_price_inc_tax, 0) AS unit_price,
            pv.name AS product_variation,
            IFNULL(v.default_purchase_price, 0) AS unit_cost,
            b.name AS brand,
            c.name AS category,
            sc.name AS sub_category
        FROM variations AS v
        LEFT JOIN products AS p ON p.id = v.product_id
        LEFT JOIN units AS u ON p.unit_id = u.id
        LEFT JOIN product_variations AS pv ON v.product_variation_id = pv.id
        LEFT JOIN product_has_suppliers AS phs ON p.id = phs.product_id
        LEFT JOIN brands AS b ON b.id = p.brand_id
        LEFT JOIN categories AS c ON c.id = p.category_id
        LEFT JOIN categories AS sc ON sc.id = p.sub_category_id
        WHERE p.business_id = v_business_id
            AND p.type IN ('single', 'variable')
            AND (v_category_id = 0 OR p.category_id = v_category_id)
            AND (v_sub_category_id = 0 OR p.sub_category_id = v_sub_category_id)
            AND (v_brand_id = 0 OR p.brand_id = v_brand_id)
            AND (v_unit_id = 0 OR p.unit_id = v_unit_id)
            AND (v_contact_id = 0 OR phs.contact_id = v_contact_id)
        GROUP BY v.id
        HAVING stock > @stock
        ORDER BY p.name;

    SELECT
		s.*,
		SUM(vld.qty_available) AS vld_stock
	FROM stock AS s
	LEFT JOIN variation_location_details AS vld ON s.variation_id = vld.variation_id
	WHERE (vld.location_id = v_location_id OR v_location_id = 0)
		AND (vld.warehouse_id = v_warehouse_id OR v_warehouse_id = 0)
	GROUP BY s.variation_id;
	
	DROP TEMPORARY TABLE IF EXISTS stock;
END; $$

DELIMITER ;

CALL generate_stock_report(3, 2, 5, 0, 0, 0, 0, 0, '', '', 1);