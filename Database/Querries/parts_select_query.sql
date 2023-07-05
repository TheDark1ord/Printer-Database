SELECT
    `parts`.`PartName`,
    `parts`.`ID`,
    `part_types`.`PartType`,
    `parts`.`Count`,
    `isOriginal`
FROM
    `printer_models`
    INNER JOIN `parts_association` ON (`parts_association`.`PrinterID` = `printer_models`.`ID`)
    INNER JOIN `parts` ON (`parts`.`ID` = `parts_association`.`PartID`)
    INNER JOIN `part_types` ON (`part_types`.`ID` = `parts`.`PartType`)
WHERE
    (`printer_models`.`Model` = ?) AND (`part_types`.`ID` = ? OR ? = "") AND (`parts`.`Count` > 0)
ORDER BY
    `parts`.`PartName` ASC;