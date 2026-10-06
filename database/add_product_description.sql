-- Run this on an existing jablestore/store database.
-- Safe for databases where product_description has not been added yet.
ALTER TABLE product
ADD COLUMN product_description TEXT NULL AFTER product_name;
