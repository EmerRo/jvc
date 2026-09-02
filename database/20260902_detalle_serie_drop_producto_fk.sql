-- id_producto ahora puede apuntar a `productos` O a `repuestos` según tipo_producto
-- (ver 20260902_detalle_serie_tipo_producto.sql). MySQL no soporta FKs condicionales/
-- polimórficas, así que se retira la constraint que solo permitía productos.
-- La integridad se resuelve en la app (LEFT JOIN condicional por tipo_producto en
-- app/models/DetalleSerie.php); un id huérfano simplemente no resuelve nombre/stock.
ALTER TABLE detalle_serie
    DROP FOREIGN KEY detalle_serie_producto_fk;
