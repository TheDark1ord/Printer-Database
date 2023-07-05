CREATE TABLE IF NOT EXISTS `printers` (
    SerialNumber varchar(32) NOT NULL,
    Model BIGINT NOT NULL,
    Description varchar(512),
    PRIMARY KEY (SerialNumber),
    FOREIGN KEY (Model) REFERENCES printer_models(ID)
)