SELECT
    `part_use_log`.`UseTime`,
    `parts`.`PartName`,
    `parts`.`Manufacturer`,
    `part_types`.`PartType`,
    `parts`.`Description`,
    `printers`.`SerialNumber`,
    `printers`.`Description`,
    `printer_models`.`Model`
FROM
    `part_use_log`
    INNER JOIN `parts` ON `part_use_log`.`PartID` = `parts`.`ID`
    INNER JOIN `part_types` ON `parts`.`PartType` = `part_types`.`ID`
    INNER JOIN `printers` ON `part_use_log`.`PrinterSerial` = `printers`.`SerialNumber`
    INNER JOIN `printer_models` ON `printers`.`Model` = `printer_models`.`ID`
WHERE
    (`part_use_log`.`PartID` = ?) OR
    (`parts`.`PartName` = ? AND `parts`.`Manufacturer` = ?) AND
    (`part_use_log`.`UseTime` BETWEEN ? AND ?)
ORDER BY `part_use_log`.`UseTime` DESC;