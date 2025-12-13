-- =====================================================
-- MIGRACIONES DE BASE DE DATOS
-- Fecha: 2024-12-13
-- Descripción: Gestión de Stock para Productos y Repuestos
-- Autor: Sistema JVC
-- =====================================================

USE factura_jvc;

-- =====================================================
-- 1. AGREGAR CAMPO costo_compra A historial_stock
-- =====================================================
-- Este campo almacena el costo de compra específico de cada ingreso
-- Permite llevar un historial de precios de compra

-- Verificar si la columna ya existe antes de agregarla
SET @dbname = DATABASE();
SET @tablename = 'historial_stock';
SET @columnname = 'costo_compra';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      (table_name = @tablename)
      AND (table_schema = @dbname)
      AND (column_name = @columnname)
  ) > 0,
  'SELECT 1', -- La columna ya existe, no hacer nada
  CONCAT('ALTER TABLE ', @tablename, ' ADD COLUMN ', @columnname, ' DECIMAL(10,2) NULL AFTER cantidad')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- =====================================================
-- 2. CREAR TABLA historial_stock_repuestos
-- =====================================================
-- Tabla para registrar movimientos de stock de repuestos
-- Estructura idéntica a historial_stock pero para repuestos

CREATE TABLE IF NOT EXISTS historial_stock_repuestos (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_repuesto INT NOT NULL,
    tipo_movimiento ENUM('INGRESO','EGRESO') NOT NULL,
    cantidad INT NOT NULL,
    costo_compra DECIMAL(10,2) NULL,
    fecha_movimiento DATETIME NOT NULL,
    usuario VARCHAR(100) NULL,
    observaciones TEXT NULL,
    INDEX idx_repuesto (id_repuesto),
    INDEX idx_fecha (fecha_movimiento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- NOTAS IMPORTANTES:
-- =====================================================
-- 
-- 1. historial_stock: Para PRODUCTOS
--    - id_producto: referencia a tabla productos
--    - costo_compra: costo específico de cada compra
--    - observaciones: notas del movimiento
--
-- 2. historial_stock_repuestos: Para REPUESTOS
--    - id_repuesto: referencia a tabla repuestos
--    - Misma estructura que historial_stock
--
-- 3. tipo_movimiento:
--    - INGRESO: Aumentar stock, Traslado (destino)
--    - EGRESO: Disminuir stock, Traslado (origen)
--
-- =====================================================
-- FUNCIONALIDADES IMPLEMENTADAS:
-- =====================================================
--
-- PRODUCTOS:
-- - Aumentar Stock (con costo de compra y observaciones)
-- - Disminuir Stock (con observaciones)
-- - Traslado Entre Almacenes (con observaciones)
-- - Historial de Stock (muestra costo y observaciones)
--
-- REPUESTOS:
-- - Aumentar Stock (con costo de compra y observaciones)
-- - Disminuir Stock (con observaciones)
-- - Traslado Entre Almacenes (con observaciones)
-- - Historial de Stock (muestra costo y observaciones)
--
-- =====================================================
-- VERIFICAR CAMBIOS:
-- =====================================================
-- 
-- Ver estructura de historial_stock:
-- DESCRIBE historial_stock;
--
-- Ver estructura de historial_stock_repuestos:
-- DESCRIBE historial_stock_repuestos;
--
-- Ver registros de productos:
-- SELECT * FROM historial_stock ORDER BY fecha_movimiento DESC LIMIT 10;
--
-- Ver registros de repuestos:
-- SELECT * FROM historial_stock_repuestos ORDER BY fecha_movimiento DESC LIMIT 10;
--
-- =====================================================
