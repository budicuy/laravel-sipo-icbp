-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS=0;

-- 1. Perbaiki foreign key di tabel rekam_medis_emergency
-- Drop existing foreign keys if any
SELECT CONCAT('ALTER TABLE rekam_medis_emergency DROP FOREIGN KEY ', CONSTRAINT_NAME, ';') AS drop_fk_sql
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'rekam_medis_emergency' 
AND REFERENCED_TABLE_NAME = 'external_employees';

-- Change column type to match external_employees.id (bigint unsigned)
-- First, drop any existing foreign key constraints
ALTER TABLE rekam_medis_emergency DROP FOREIGN KEY IF EXISTS rekam_medis_emergency_id_external_employee_foreign;

-- Then change column type
ALTER TABLE rekam_medis_emergency MODIFY COLUMN id_external_employee BIGINT UNSIGNED;

-- Add correct foreign key
ALTER TABLE rekam_medis_emergency
ADD CONSTRAINT rekam_medis_emergency_id_external_employee_foreign
FOREIGN KEY (id_external_employee) REFERENCES external_employees(id)
ON UPDATE CASCADE ON DELETE RESTRICT;

-- 2. Tambahkan kolom id_emergency ke tabel keluhan dengan tipe data yang sama dengan rekam_medis_emergency
-- Check if column exists
SELECT COUNT(*) AS column_exists
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'keluhan'
AND COLUMN_NAME = 'id_emergency';

-- Add column if it doesn't exist
ALTER TABLE keluhan ADD COLUMN id_emergency BIGINT UNSIGNED NULL AFTER id_rekam;

-- Check if column type needs to be updated
SELECT COLUMN_TYPE, DATA_TYPE
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'keluhan'
AND COLUMN_NAME = 'id_emergency';

-- Update column type to match rekam_medis_emergency.id_emergency
-- First, drop any existing foreign key constraints
ALTER TABLE keluhan DROP FOREIGN KEY IF EXISTS keluhan_id_emergency_foreign;

-- Then change column type
ALTER TABLE keluhan MODIFY COLUMN id_emergency BIGINT UNSIGNED NULL;

-- Add foreign key
ALTER TABLE keluhan
ADD CONSTRAINT keluhan_id_emergency_foreign
FOREIGN KEY (id_emergency) REFERENCES rekam_medis_emergency(id_emergency)
ON UPDATE CASCADE ON DELETE CASCADE;

-- 3. Tambahkan kolom id_diagnosa_emergency ke tabel keluhan
-- Check if column exists
SELECT COUNT(*) AS column_exists
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_NAME = 'keluhan' 
AND COLUMN_NAME = 'id_diagnosa_emergency';

-- Add column if it doesn't exist
ALTER TABLE keluhan ADD COLUMN id_diagnosa_emergency INT UNSIGNED NULL AFTER id_diagnosa;

-- Add foreign key
ALTER TABLE keluhan 
ADD CONSTRAINT keluhan_id_diagnosa_emergency_foreign 
FOREIGN KEY (id_diagnosa_emergency) REFERENCES diagnosa_emergency(id_diagnosa_emergency) 
ON UPDATE CASCADE ON DELETE CASCADE;

-- 4. Buat pivot table diagnosa_emergency_obat
CREATE TABLE IF NOT EXISTS diagnosa_emergency_obat (
    id_diagnosa_emergency INT UNSIGNED NOT NULL,
    id_obat INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id_diagnosa_emergency, id_obat),
    CONSTRAINT fk_diagnosa_emergency_obat_diagnosa FOREIGN KEY (id_diagnosa_emergency) REFERENCES diagnosa_emergency (id_diagnosa_emergency) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_diagnosa_emergency_obat_obat FOREIGN KEY (id_obat) REFERENCES obat (id_obat) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;

-- Verification queries
-- Check foreign keys
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND REFERENCED_TABLE_NAME IS NOT NULL
AND TABLE_NAME IN ('rekam_medis_emergency', 'keluhan', 'diagnosa_emergency_obat')
ORDER BY TABLE_NAME, COLUMN_NAME;

-- Check table structures
DESCRIBE rekam_medis_emergency;
DESCRIBE keluhan;
DESCRIBE diagnosa_emergency_obat;