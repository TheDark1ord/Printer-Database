CREATE TABLE IF NOT EXISTS printers (
    Model VARCHAR(255) NOT NULL,
    Position VARCHAR(255) NOT NULL,
    PRIMARY KEY (Model, Position)
);

CREATE TABLE IF NOT EXISTS part_types (
    PartType VARCHAR(255) NOT NULL PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS parts (
    PartName VARCHAR(255) NOT NULL PRIMARY KEY,
    ShipmentDate DATETIME NOT NULL,
    PartType varchar(255) NOT NULL,
    Count int NOT NULL,
    FOREIGN KEY (PartType) REFERENCES part_types(PartType)
);

CREATE TABLE IF NOT EXISTS original_parts (
    PrinterModel VARCHAR(255),
    PrinterPosition VARCHAR(255),
    Part VARCHAR(255),
    FOREIGN KEY (PrinterModel,PrinterPosition) REFERENCES printers(Model,Position),
    FOREIGN KEY (Part) REFERENCES parts(PartName)
);

CREATE TABLE IF NOT EXISTS non_original_parts (
    PrinterModel VARCHAR(255),
    PrinterPosition VARCHAR(255),
    Part VARCHAR(255),
    FOREIGN KEY (PrinterModel,PrinterPosition) REFERENCES printers(Model,Position),
    FOREIGN KEY (Part) REFERENCES parts(PartName)
);