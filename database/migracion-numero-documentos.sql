-- ============================================
-- MIGRACIÓN: Agregar columna `numero` a tablas de documentos
-- ============================================

-- 1. informes
ALTER TABLE informes
    ADD COLUMN numero INT NOT NULL DEFAULT 0 AFTER id_informe;

UPDATE informes SET numero = id_informe WHERE numero = 0;

-- 2. cartas
ALTER TABLE cartas
    ADD COLUMN numero INT NOT NULL DEFAULT 0 AFTER id;

UPDATE cartas SET numero = id WHERE numero = 0;

-- 3. constancias
ALTER TABLE constancias
    ADD COLUMN numero INT NOT NULL DEFAULT 0 AFTER id;

UPDATE constancias SET numero = id WHERE numero = 0;

-- 4. archivos_internos
ALTER TABLE archivos_internos
    ADD COLUMN numero INT NOT NULL DEFAULT 0 AFTER id;

UPDATE archivos_internos SET numero = id WHERE numero = 0;

-- 5. otros_archivos
ALTER TABLE otros_archivos
    ADD COLUMN numero INT NOT NULL DEFAULT 0 AFTER id;

UPDATE otros_archivos SET numero = id WHERE numero = 0;

-- 6. gestion_archivos (fichas técnicas)
ALTER TABLE gestion_archivos
    ADD COLUMN numero INT NOT NULL DEFAULT 0 AFTER id_archivo;

UPDATE gestion_archivos SET numero = id_archivo WHERE numero = 0;
