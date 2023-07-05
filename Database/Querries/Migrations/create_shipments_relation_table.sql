-- Задает отношение one-to-many между таблицей `shipments` и таблицей `parts` --
CREATE TABLE IF NOT EXISTS `shipments_relation` (
    ID BIGINT NOT NULL AUTO_INCREMENT,
    ShipmentID BIGINT NOT NULL,
    PartId BIGINT NOT NULL,
    Count INT NOT NULL,
    PRIMARY KEY (ID),
    FOREIGN KEY (ShipmentID) REFERENCES shipments(ID) ON DELETE CASCADE,
    FOREIGN KEY (PartId) REFERENCES parts(ID) ON DELETE CASCADE
)