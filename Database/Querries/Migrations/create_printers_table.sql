CREATE TABLE IF NOT EXISTS `printers` (
    SerialNumber varchar(32) NOT NULL,
    Model INT NOT NULL,
    Description varchar(512),
    PRIMARY KEY (SerialNumber),
    FOREIGN KEY (Model) REFERENCES printer_models(ID)
)