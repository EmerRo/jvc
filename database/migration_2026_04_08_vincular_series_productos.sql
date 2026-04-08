-- =====================================================================
-- Migración: Vincular detalle_serie con productos (almacén/kardex)
-- Fecha: 2026-04-08
-- Autor: Tarea 1 — Mapeo flujo Series ↔ Almacén
-- =====================================================================
-- Objetivo:
--   1) Permitir que cada equipo registrado en un lote NS quede vinculado
--      a un producto del almacén (id_producto). Esto es la base para que
--      el stock se actualice automáticamente cuando el lote se completa.
--   2) Agregar un estado al lote NS (borrador / completado / anulado)
--      para controlar cuándo se impacta el kardex.
--   3) Marcar lotes que originalmente eran externos y luego pasaron a
--      internos (caso "cliente anuló la compra").
--
-- Compatibilidad: NO toca registros existentes. Los lotes ya creados
--   quedan en estado_lote='borrador' con id_producto=NULL.
-- =====================================================================

-- 1) Vincular cada serie/equipo con un producto del almacén
ALTER TABLE detalle_serie
  ADD COLUMN id_producto INT NULL AFTER equipo_id,
  ADD CONSTRAINT detalle_serie_producto_fk
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
    ON DELETE SET NULL ON UPDATE CASCADE;

CREATE INDEX idx_detalle_serie_producto ON detalle_serie(id_producto);

-- 2) Agregar estado del lote NS y campos de auditoría
ALTER TABLE numero_series
  ADD COLUMN estado_lote ENUM('borrador','completado','anulado')
    NOT NULL DEFAULT 'borrador' AFTER tiene_cliente,
  ADD COLUMN fecha_completado DATETIME NULL AFTER estado_lote,
  ADD COLUMN convertido_de_externo TINYINT(1) NOT NULL DEFAULT 0
    AFTER fecha_completado,
  ADD COLUMN usuario_completo VARCHAR(100) NULL AFTER convertido_de_externo;

CREATE INDEX idx_numero_series_estado ON numero_series(estado_lote);

-- =====================================================================
-- Verificación rápida (descomentar para correr a mano)
-- =====================================================================
-- SHOW CREATE TABLE detalle_serie\G
-- SHOW CREATE TABLE numero_series\G
-- SELECT id, numero, tipo_maquina, tiene_cliente, estado_lote
--   FROM numero_series ORDER BY id DESC LIMIT 10;
