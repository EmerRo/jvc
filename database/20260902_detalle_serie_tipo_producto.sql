-- Permite que el "Producto del almacén" vinculado a un equipo de numero_series
-- sea un producto o un repuesto (antes solo podía ser producto).
ALTER TABLE detalle_serie
    ADD COLUMN tipo_producto ENUM('producto', 'repuesto') NOT NULL DEFAULT 'producto'
    AFTER id_producto;
