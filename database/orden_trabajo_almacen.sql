-- Agregar columnas de almacén y producto a orden_trabajo_detalles
-- Ejecutar en producción antes de desplegar los cambios de la versión correspondiente

ALTER TABLE orden_trabajo_detalles
    ADD COLUMN id_almacen INT NULL DEFAULT NULL,
    ADD COLUMN id_producto INT NULL DEFAULT NULL;
