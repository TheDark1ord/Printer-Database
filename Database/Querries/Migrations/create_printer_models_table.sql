CREATE TABLE IF NOT EXISTS printer_models (
    ID BIGINT NOT NULL AUTO_INCREMENT,
    Model VARCHAR(255) NOT NULL UNIQUE,
    PRIMARY KEY (ID)
);