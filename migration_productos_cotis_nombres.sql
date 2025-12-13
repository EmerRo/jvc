-- =====================================================
-- Migración: Agregar campos para nombres personalizados
-- en productos de cotizaciones
-- Fecha: 2025-10-11
-- =====================================================

-- Agregar columnas para permitir nombres personalizados de productos en cotizaciones
ALTER TABLE `productos_cotis`
ADD COLUMN `nombre_producto` VARCHAR(255) NULL AFTER `id_producto`,
ADD COLUMN `descripcion_producto` TEXT NULL AFTER `nombre_producto`;

-- Nota: Estos campos permitirán editar los nombres de productos
-- específicamente para cada cotización sin modificar el producto original.
-- Si son NULL, se usará el nombre del producto desde la tabla 'productos'.
ALTER TABLE historial_stock ADD COLUMN costo_compra DECIMAL(10,2) NULL AFTER cantidad
ALTER TABLE historial_stock ADD COLUMN costo_compra DECIMAL(10,2) NULL AFTER cantidad
