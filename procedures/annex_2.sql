DROP PROCEDURE IF EXISTS annex_2;

DELIMITER $$

CREATE PROCEDURE annex_2(
    v_initial_date DATE,
    v_final_date DATE,
    v_location_id INT,
    v_business_id INT,
    v_no_annex INT
)

BEGIN

    SELECT
        -- COLUMNA A: FECHA DE EMISION DEL DOCUMENTO
        -- MySQL:
        ANY_VALUE(DATE_FORMAT(t.transaction_date, '%d/%m/%Y')) AS transaction_date,
        -- MariaDB:
        -- DATE_FORMAT(t.transaction_date, '%d/%m/%Y') AS transaction_date,

        -- COLUMNA B: CLASE DE DOCUMENTO
        dc.code AS document_class,

        -- COLUMNA C: TIPO DE DOCUMENTO
        SUBSTRING(dt.document_type_number, 1, 2) AS document_type,

        -- COLUMNA D: NUMERO DE RESOLUCION
        SUBSTRING(UPPER(REPLACE(t.resolution, '-', '')), 1, 100) AS resolution,

        -- COLUMNA E: SERIE DE DOCUMENTO
        SUBSTRING(UPPER(t.serie), 1, 100) AS serie,

        -- COLUMNA F: NUMERO DE CONTROL INTERNO (DEL)
        SUBSTRING(MIN(t.id), 1, 100) AS initial_id,

        -- COLUMNA G: NUMERO DE CONTROL INTERNO (AL)
        SUBSTRING(MAX(t.id), 1, 100) AS final_id,

        -- COLUMNA H: NUMERO DE DOCUMENTO (DEL)
        MIN(CONVERT(SUBSTRING(t.correlative, 1, 100), UNSIGNED INTEGER)) AS initial_correlative,

        -- COLUMNA I: NUMERO DE DOCUMENTO (AL)
        MAX(CONVERT(SUBSTRING(t.correlative, 1, 100), UNSIGNED INTEGER)) AS final_correlative,

        -- COLUMNA J: NO. MAQUINA REGISTRADORA

        -- COLUMNA K: VENTAS EXENTAS
        IF (
            c.is_exempt = 1,
            IF (
                dt.document_type_number IN ('01', '02', '10'),
                SUM(IF(t.status = 'annulled', 0, t.total_before_tax)),
                0
            ),
            0
        ) AS exempt_sales,

        -- COLUMNA L: VENTAS INTERNAS EXENTAS NO SUJETAS A PROPORCIONALIDAD

        -- COLUMNA M: VENTAS NO SUJETAS

        -- COLUMNA N: VENTAS GRAVADAS LOCALES
        IF (
            c.is_exempt = 0,
            IF (
                dt.document_type_number IN ('01', '02', '10'),
                SUM(IF(t.status = 'annulled', 0, t.total_before_tax)),
                0
            ),
            0
        ) AS taxed_sales,

        -- COLUMNA O: EXPORTACIONES DENTRO DEL AREA CENTROAMERICANA

        -- COLUMNA P: EXPORTACIONES FUERA DEL AREA CENTROAMERICANA

        -- COLUMNA Q: EXPORTACIONES DE SERVICIOS
        IF (
            c.is_exempt = 0,
            IF (
                dt.document_type_number = '11',
                SUM(IF(t.status = 'annulled', 0, t.total_before_tax)),
                0
            ),
            0
        ) AS exports,

        -- COLUMNA R: VENTAS A ZONAS FRANCAS Y DPA (TASA CERO)

        -- COLUMNA S: VENTAS A CUENTA DE TERCEROS NO DOMICILIADOS

        -- COLUMNA T: TOTAL VENTAS
        SUM(IF(t.status = 'annulled', 0, t.total_before_tax)) AS total,

        -- COLUMNA U: NUMERO DE ANEXO
        v_no_annex AS no_annex,

        t.location_id,
        t.document_types_id,
        t.document_correlative_id
    FROM transactions AS t
    LEFT JOIN customers AS c
        ON c.id = t.customer_id
    LEFT JOIN document_types AS dt
        ON dt.id = t.document_types_id
    LEFT JOIN document_classes AS dc
        ON dc.id = dt.document_class_id
    WHERE DATE(t.transaction_date) BETWEEN v_initial_date AND v_final_date
        AND t.business_id = v_business_id
        AND (v_location_id = 0 OR t.location_id = v_location_id)
        AND t.document_types_id IN (
            SELECT id
            FROM document_types
            WHERE business_id = v_business_id
                AND is_active = 1
                AND document_type_number IN ('01', '02', '10', '11')
        )
        AND t.type = 'sell'
        AND (t.status = 'final' OR t.status = 'annulled')
    GROUP BY t.document_types_id, t.document_correlative_id, t.location_id, DATE(t.transaction_date)
    ORDER BY CONVERT(t.correlative, UNSIGNED INTEGER), t.location_id;

END; $$

DELIMITER ;