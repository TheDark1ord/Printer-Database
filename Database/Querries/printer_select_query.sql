SELECT
    parts.PartName,
    part_types.PartType,
    parts.ShipmentDate,
    parts.Count,
    isOriginal
FROM
    printers
    LEFT JOIN parts_association ON (parts_association.PrinterID = printers.ID)
    LEFT JOIN parts ON (parts.ID = parts_association.PartID)
    LEFT JOIN part_types ON (part_types.ID = parts.PartType)
WHERE
    (printers.Model = ?) AND (part_types.PartType = ? OR ? = "")
ORDER BY
    parts.PartName ASC;