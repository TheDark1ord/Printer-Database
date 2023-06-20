-- Данный скрипт использует переменные, которые должны быть заменены в коде php
--
SELECT * FROM (
    SELECT
        parts.PartName,
        ShipmentDate,
        PartType,
        Count,
        PrinterModel,
        'original' as isOriginal
    FROM
        parts
        INNER JOIN original_parts op ON parts.PartName = op.PartName
    WHERE
    	parts.PartName = %1$s
) t1
UNION (
    SELECT
        parts.PartName,
        ShipmentDate,
        PartType,
        Count,
        PrinterModel,
        'non-original' as isOriginal
    FROM
        parts
        INNER JOIN non_original_parts nop ON parts.PartName = nop.PartName
    WHERE
    	parts.PartName = %1$s
);