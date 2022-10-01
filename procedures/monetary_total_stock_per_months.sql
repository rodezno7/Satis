DROP PROCEDURE IF EXISTS monetary_total_stock_per_months;

DELIMITER $$

CREATE PROCEDURE monetary_total_stock_per_months(
    v_business_id INT,
    v_location_id VARCHAR(255),
    v_start DATE
)

BEGIN
    DECLARE c_counter INT DEFAULT 1;
    DECLARE c_month INT DEFAULT 12;
    DECLARE c_current_date DATE DEFAULT LAST_DAY(v_start);
    DECLARE c_total_stock DECIMAL(20, 6);

    DROP TEMPORARY TABLE IF EXISTS total_stocks_per_months;

    CREATE TEMPORARY TABLE total_stocks_per_months (
        full_date DATE UNIQUE,
        total DECIMAL(20, 6) NULL DEFAULT '0.000000'
    );

    WHILE c_counter <= c_month DO
        SET c_total_stock = IF(
            c_current_date <= CURDATE(),
            monetary_total_stock(v_business_id, v_location_id, c_current_date),
            IF(
                MONTH(c_current_date) = MONTH(CURDATE()),
                monetary_total_stock(v_business_id, v_location_id, CURDATE()),
                0
            )
        );
        INSERT INTO total_stocks_per_months(full_date, total)
            VALUES(c_current_date, c_total_stock);
        SET c_counter = c_counter + 1;
        SET c_current_date = LAST_DAY(DATE_ADD(c_current_date, INTERVAL 1 MONTH));
    END WHILE;

    SELECT DATE_FORMAT(full_date, '%m-%Y') AS full_date, total FROM total_stocks_per_months ORDER BY full_date;

    DROP TEMPORARY TABLE IF EXISTS total_stocks_per_months;
END; $$

DELIMITER ;