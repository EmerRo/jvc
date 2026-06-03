-- =====================================================
-- MIGRACIÓN: Agregar venta_numero a view_cotizaciones
-- Proyecto: JVC - Sistema de Facturación Electrónica
-- Fecha: 2026-05-31
-- Autor: EmerRo
-- =====================================================
-- PROPÓSITO:
--   Agregar la columna venta_numero a la vista view_cotizaciones
--   mediante una subquery a la tabla ventas.
--   Esto permite mostrar en la tabla de cotizaciones el número
--   de venta generada cuando una cotización ya fue vendida,
--   apareciendo como tooltip en el ícono "Vender" bloqueado.
--
-- ARCHIVOS FRONTEND AFECTADOS:
--   - app/http/controllers/ConsultaDelcontroller.php
--     → columna "venta_numero" agregada al array del serverside (índice 13)
--   - resources/views/fragment-views/cliente/cotizaciones.php
--     → targets: 8 render usa row[13] para el tooltip
--
-- CÓMO REVERTIR:
--   Ejecutar el bloque marcado como ROLLBACK al final de este archivo.
-- =====================================================

-- VERIFICAR estado actual antes de aplicar (opcional)
-- SELECT COUNT(*) FROM view_cotizaciones LIMIT 1;

-- =====================================================
-- APLICAR CAMBIO
-- =====================================================

ALTER VIEW view_cotizaciones AS
SELECT
    v.cotizacion_id,
    v.numero,
    v.fecha,
    v.moneda,
    v.cm_tc,
    v.id_tido,
    CONCAT(c.documento, ' | ', c.datos)                             AS documento,
    c.datos,
    v.total,
    v.estado,
    v.aplicar_igv,
    CASE
        WHEN (u.nombres IS NOT NULL AND u.apellidos IS NOT NULL) THEN CONCAT(u.nombres, ' ', u.apellidos)
        WHEN u.nombres IS NOT NULL                               THEN u.nombres
        ELSE u.usuario
    END                                                             AS vendedor,
    v.id_usuario                                                    AS usuario,
    (
        SELECT vt.numero
        FROM   ventas vt
        WHERE  vt.id_coti = v.cotizacion_id
        LIMIT  1
    )                                                               AS venta_numero
FROM       cotizaciones    v
LEFT JOIN  documentos_sunat ds ON v.id_tido    = ds.id_tido
LEFT JOIN  clientes         c  ON v.id_cliente = c.id_cliente
LEFT JOIN  usuarios         u  ON u.usuario_id = v.id_usuario
WHERE  v.id_empresa = '12'
AND    v.estado     <> '2'
ORDER BY v.fecha DESC;


-- =====================================================
-- VERIFICAR que la columna quedó disponible
-- =====================================================
-- SELECT cotizacion_id, numero, estado, venta_numero
-- FROM   view_cotizaciones
-- LIMIT  10;


-- =====================================================
-- ROLLBACK — ejecutar esto para revertir el cambio
-- =====================================================
/*
ALTER VIEW view_cotizaciones AS
SELECT
    v.cotizacion_id,
    v.numero,
    v.fecha,
    v.moneda,
    v.cm_tc,
    v.id_tido,
    CONCAT(c.documento, ' | ', c.datos)                             AS documento,
    c.datos,
    v.total,
    v.estado,
    v.aplicar_igv,
    CASE
        WHEN (u.nombres IS NOT NULL AND u.apellidos IS NOT NULL) THEN CONCAT(u.nombres, ' ', u.apellidos)
        WHEN u.nombres IS NOT NULL                               THEN u.nombres
        ELSE u.usuario
    END                                                             AS vendedor,
    v.id_usuario                                                    AS usuario
FROM       cotizaciones    v
LEFT JOIN  documentos_sunat ds ON v.id_tido    = ds.id_tido
LEFT JOIN  clientes         c  ON v.id_cliente = c.id_cliente
LEFT JOIN  usuarios         u  ON u.usuario_id = v.id_usuario
WHERE  v.id_empresa = '12'
AND    v.estado     <> '2'
ORDER BY v.fecha DESC;
*/
