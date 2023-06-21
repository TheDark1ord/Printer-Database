-- Defines many to many relationship between printers and parts
CREATE TABLE IF NOT EXISTS parts_association (
    PartID int,
    PrinterID int,
    PrinterModel varchar(255) NOT NULL,
    PartName VARCHAR(255) NOT NULL,
    PartShipmentDate DATETIME NOT NULL,
    IsOriginal BOOLEAN NOT NULL DEFAULT 0,
    FOREIGN KEY (PrinterID) REFERENCES printers(ID) ON DELETE SET NULL,
    FOREIGN KEY (PartID) REFERENCES parts(ID) ON DELETE CASCADE
);