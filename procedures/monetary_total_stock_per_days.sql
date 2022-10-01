DROP PROCEDURE IF EXISTS monetary_total_stock_per_days;

DELIMITER $$

CREATE PROCEDURE monetary_total_stock_per_days(
    v_business_id INT,
    v_location_id VARCHAR(255),
    v_start DATE
)

BEGIN
    DECLARE c_counter INT DEFAULT 1;
    DECLARE c_day INT DEFAULT 30;
    DECLARE c_current_date DATE DEFAULT v_start;
    DECLARE c_total_stock DECIMAL(20, 6);

    DROP TEMPORARY TABLE IF EXISTS total_stocks_per_days;

    CREATE TEMPORARY TABLE total_stocks_per_days (
        full_date DATE UNIQUE,
        total DECIMAL(20, 6) NULL DEFAULT '0.000000'
    );

    WHILE c_counter <= c_day DO
        SET c_total_stock = monetary_total_stock(v_business_id, v_location_id, c_current_date);
        INSERT INTO total_stocks_per_days(full_date, total)
            VALUES(c_current_date, c_total_stock);
        SET c_counter = c_counter + 1;
        SET c_current_date = DATE_ADD(c_current_date, INTERVAL 1 day);
    END WHILE;

    SELECT * FROM total_stocks_per_days ORDER BY full_date;

    DROP TEMPORARY TABLE IF EXISTS total_stocks_per_days;
END; $$

DELIMITER ;