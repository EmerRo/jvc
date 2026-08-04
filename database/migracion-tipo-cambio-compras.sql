-- ============================================
-- MIGRACIÓN: Agregar columna `tipo_cambio` a tabla compras
-- ============================================
-- Uso: compras en moneda USD guardan la tasa de cambio
-- (campo "Tasa de cambio" en las vistas de agregar/editar compra).
-- Ejecutar en producción con: mysql -u USUARIO -p magusqao_jvc_factura < migracion-tipo-cambio-compras.sql
-- o copiar y pegar en el gestor de BD.

ALTER TABLE compras
    ADD COLUMN tipo_cambio VARCHAR(20) NULL AFTER moneda;

-- Verificación: debe mostrar la columna `tipo_cambio` entre `moneda` y `sucursal`
-- DESCRIBE compras;
