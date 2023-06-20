CREATE TABLE IF NOT EXISTS original_parts (
    PrinterID int,
    PrinterModel varchar(255) NOT NULL,
    PartName VARCHAR(255) NOT NULL,
    PartShipmentDate DATETIME NOT NULL,
    FOREIGN KEY (PrinterID) REFERENCES printers(ID) ON DELETE SET NULL,
    FOREIGN KEY (PartName, PartShipmentDate) REFERENCES parts(PartName, ShipmentDate) ON DELETE CASCADE
);