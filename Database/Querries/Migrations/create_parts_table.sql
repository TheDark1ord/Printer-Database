CREATE TABLE IF NOT EXISTS parts (
    ID int NOT NULL UNIQUE,
    PartName VARCHAR(255) NOT NULL,
    ShipmentDate DATE NOT NULL DEFAULT NOW(),
    PartType int NOT NULL,
    Count int NOT NULL,
    PRIMARY KEY (PartName, ShipmentDate),
    FOREIGN KEY (PartType) REFERENCES part_types(ID)
);