CREATE TABLE IF NOT EXISTS parts (
    ID BIGINT NOT NULL UNIQUE AUTO_INCREMENT,
    PartName VARCHAR(255) NOT NULL,
    Manufacturer VARCHAR(255) NOT NULL,
    PartType INT NOT NULL,
    Count INT NOT NULL,
    Description VARCHAR(511),
    PRIMARY KEY (ID),
    FOREIGN KEY (PartType) REFERENCES part_types(ID)
);

ALTER TABLE `parts` CREATE INDEX `unique_index`(`PartName`, `Manufacturer`);