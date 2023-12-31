-- Defines many to many relationship between printers and parts
CREATE TABLE IF NOT EXISTS parts_association (
    ID BIGINT NOT NULL AUTO_INCREMENT,
    PartID BIGINT,
    PrinterID BIGINT,
    PrinterModel varchar(255) NOT NULL,
    IsOriginal BOOLEAN NOT NULL DEFAULT 0,
    PRIMARY KEY (ID),
    FOREIGN KEY (PrinterID) REFERENCES printer_models(ID) ON DELETE SET NULL,
    FOREIGN KEY (PartID) REFERENCES parts(ID) ON DELETE CASCADE
);