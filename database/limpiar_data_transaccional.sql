-- =====================================================================
-- LIMPIEZA DE DATA TRANSACCIONAL - JVC
-- =====================================================================
-- PRESERVA:
--   * Maestros pedidos: productos, repuestos, empresas, usuarios
--   * Maestros tipo maquina: maquina, equipos, tecnicos
--   * Catalogos: categorias, marcas, modelos, unidades, ubigeo, sucursales,
--     roles, metodos/tipos pago, plantillas, tipos, metas, etc.
--   * Vistas (view_*) - no se tocan
--
-- LIMPIA: Ventas, compras, cotizaciones, clientes, proveedores, garantias,
--   guias, notas, series, caja, ordenes, taller, informes, cartas, gestion,
--   historial, etc.
--
-- IMPORTANTE: Hacer backup ANTES con mysqldump.
-- Tolerante: si una tabla no existe en el servidor, la salta sin error.
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

DROP PROCEDURE IF EXISTS jvc_truncate_si_existe;

DELIMITER $$
CREATE PROCEDURE jvc_truncate_si_existe(IN p_tabla VARCHAR(100))
BEGIN
    IF (SELECT COUNT(*) FROM information_schema.tables
        WHERE table_schema = DATABASE() AND table_name = p_tabla) > 0 THEN
        SET @sql := CONCAT('TRUNCATE TABLE `', p_tabla, '`');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END$$
DELIMITER ;

-- ---------- VENTAS ----------
CALL jvc_truncate_si_existe('ventas');
CALL jvc_truncate_si_existe('ventas_pagos');
CALL jvc_truncate_si_existe('ventas_anuladas');
CALL jvc_truncate_si_existe('ventas_equipos');
CALL jvc_truncate_si_existe('ventas_referencias');
CALL jvc_truncate_si_existe('ventas_servicios');
CALL jvc_truncate_si_existe('ventas_sunat');
CALL jvc_truncate_si_existe('venta_anexo');
CALL jvc_truncate_si_existe('cliente_venta');
CALL jvc_truncate_si_existe('productos_ventas');
CALL jvc_truncate_si_existe('dias_ventas');

-- ---------- COMPRAS ----------
CALL jvc_truncate_si_existe('compras');
CALL jvc_truncate_si_existe('productos_compras');
CALL jvc_truncate_si_existe('repuestos_compras');
CALL jvc_truncate_si_existe('observaciones_compra');
CALL jvc_truncate_si_existe('dias_compras');

-- ---------- COTIZACIONES ----------
CALL jvc_truncate_si_existe('cotizaciones');
CALL jvc_truncate_si_existe('productos_cotis');
CALL jvc_truncate_si_existe('cuotas_cotizacion');

-- ---------- CLIENTES Y PROVEEDORES ----------
CALL jvc_truncate_si_existe('clientes');
CALL jvc_truncate_si_existe('proveedores');

-- ---------- GARANTIAS ----------
CALL jvc_truncate_si_existe('garantia');
CALL jvc_truncate_si_existe('detalle_garantia');

-- ---------- GUIAS DE REMISION ----------
CALL jvc_truncate_si_existe('guia_remision');
CALL jvc_truncate_si_existe('guia_detalles');
CALL jvc_truncate_si_existe('guia_equipos');
CALL jvc_truncate_si_existe('guia_choferes');
CALL jvc_truncate_si_existe('guia_vehiculos');
CALL jvc_truncate_si_existe('guia_licencias');
CALL jvc_truncate_si_existe('guia_conductor_configuraciones');
CALL jvc_truncate_si_existe('guia_sunat');

-- ---------- NOTAS ELECTRONICAS ----------
CALL jvc_truncate_si_existe('notas_electronicas');
CALL jvc_truncate_si_existe('notas_electronicas_sunat');

-- ---------- SERIES E HISTORIAL DE STOCK ----------
CALL jvc_truncate_si_existe('numero_series');
CALL jvc_truncate_si_existe('detalle_serie');
CALL jvc_truncate_si_existe('historial_stock');
CALL jvc_truncate_si_existe('ingreso_egreso');

-- ---------- CAJA ----------
CALL jvc_truncate_si_existe('caja_chica');
CALL jvc_truncate_si_existe('caja_empresa');
CALL jvc_truncate_si_existe('resumen_diario');

-- ---------- INFORMES, CARTAS, ARCHIVOS ----------
CALL jvc_truncate_si_existe('informes');
CALL jvc_truncate_si_existe('cartas');
CALL jvc_truncate_si_existe('constancias');
CALL jvc_truncate_si_existe('archivos_internos');
CALL jvc_truncate_si_existe('otros_archivos');

-- ---------- GESTION DOCUMENTAL ----------
CALL jvc_truncate_si_existe('gestion_activos');
CALL jvc_truncate_si_existe('gestion_adjuntos');
CALL jvc_truncate_si_existe('gestion_archivos');
CALL jvc_truncate_si_existe('gestion_metadatos');
CALL jvc_truncate_si_existe('gestion_versiones');

-- ---------- ORDENES DE TRABAJO Y SERVICIO ----------
CALL jvc_truncate_si_existe('orden_trabajo_pre');
CALL jvc_truncate_si_existe('orden_trabajo_detalles');
CALL jvc_truncate_si_existe('orden_trabajo_repuestos');
CALL jvc_truncate_si_existe('orden_servicio_pre');
CALL jvc_truncate_si_existe('orden_servicio_detalles');
CALL jvc_truncate_si_existe('pre_alerta');
CALL jvc_truncate_si_existe('pre_alerta_detalles');

-- ---------- TALLER ----------
CALL jvc_truncate_si_existe('taller_cotizaciones');
CALL jvc_truncate_si_existe('taller_cotizaciones_equipos');
CALL jvc_truncate_si_existe('taller_cotizaciones_fotos');
CALL jvc_truncate_si_existe('taller_condiciones_cotizacion');
CALL jvc_truncate_si_existe('taller_diagnosticos_cotizacion');
CALL jvc_truncate_si_existe('taller_observaciones_cotizacion');
CALL jvc_truncate_si_existe('taller_repuestos_cotis');
CALL jvc_truncate_si_existe('diagnostico_repuestos');

-- ---------- OBSERVACIONES / TRANSPORTE ----------
CALL jvc_truncate_si_existe('observacion');
CALL jvc_truncate_si_existe('tamsporte_persona');

DROP PROCEDURE IF EXISTS jvc_truncate_si_existe;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
-- FIN
-- =====================================================================
