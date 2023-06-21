CREATE TABLE IF NOT EXISTS parts (
    ID int NOT NULL UNIQUE AUTO_INCREMENT,
    PartName VARCHAR(255) NOT NULL,
    ShipmentDate DATE NOT NULL DEFAULT (CURDATE()),
    PartType int NOT NULL,
    Count int NOT NULL,
    PRIMARY KEY (ID),
    FOREIGN KEY (PartType) REFERENCES part_types(ID)
);

ALTER TABLE `parts` ADD UNIQUE `unique_index`(`PartName`, `ShipmentDate`);