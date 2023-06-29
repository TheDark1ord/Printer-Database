CREATE TRIGGER upd_printer_id AFTER INSERT ON printer_models
    FOR EACH ROW
        UPDATE parts_association
            SET PrinterID = NEW.ID
            WHERE PrinterModel = NEW.Model;

