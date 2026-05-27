-- =====================================================
-- MIGRACIÓN: Índices faltantes para repuestos
-- Proyecto: JVC - Sistema de Facturación Electrónica
-- Fecha: 2026-05-19
-- Propósito: Mejorar performance de consultas en /orden/repuestos
-- =====================================================

-- =====================================================
-- PASO 1: Verificar estado actual de índices
-- =====================================================

-- Ver índices actuales de repuestos
SHOW INDEX FROM repuestos;

-- Ver índices actuales de almacenes
SHOW INDEX FROM almacenes;

-- Ver índices actuales de repuesto_precios
SHOW INDEX FROM repuesto_precios;

-- =====================================================
-- PASO 2: Crear índices faltantes
-- =====================================================

-- ---------------------------------------------------------
-- Índice 1: Composite para consultas de listado y búsqueda
-- Impacto: Alto - mejora TODAS las consultas de repuestos
-- Queries afectadas: verFilas, listaRepuestoServerSide, COUNT(*)
-- ---------------------------------------------------------
ALTER TABLE repuestos 
ADD INDEX idx_repuestos_filtro (
    id_empresa, 
    sucursal, 
    estado, 
    almacen
);

-- ---------------------------------------------------------
-- Índice 2: Para búsqueda por código (importación Excel)
-- Impacto: Alto - acelera verificarCodigo() y agregarPorLista()
-- ---------------------------------------------------------
ALTER TABLE repuestos 
ADD INDEX idx_repuestos_codigo_empresa (
    codigo, 
    id_empresa, 
    sucursal, 
    almacen
);

-- ---------------------------------------------------------
-- Índice 3: Para búsqueda por nombre en autocomplete
-- Impacto: Medio - mejora búsqueda de usuarios
-- Nota: LIKE '%term%' no usa índice, pero prefix LIKE 'term%' sí
-- ---------------------------------------------------------
ALTER TABLE repuestos 
ADD INDEX idx_repuestos_nombre (
    nombre(50), 
    id_empresa, 
    sucursal, 
    almacen, 
    estado
);

-- ---------------------------------------------------------
-- Índice 4: Para autocomplete de repuestos
-- Impacto: Medio - mejora buscarRepuesto()
-- ---------------------------------------------------------
ALTER TABLE repuestos 
ADD INDEX idx_repuestos_busqueda (
    id_empresa, 
    sucursal, 
    almacen, 
    estado
);

-- ---------------------------------------------------------
-- Índice 5: Para repuesto_precios (relación con repuestos)
-- Impacto: Alto - acelera carga de precios múltiples
-- ---------------------------------------------------------
ALTER TABLE repuesto_precios 
ADD INDEX idx_repuesto_precios_repuesto (
    id_repuesto
);

-- ---------------------------------------------------------
-- Índice 6: Para almacenes (filtro por empresa)
-- Impacto: Medio - mejora listar almacenes
-- ---------------------------------------------------------
ALTER TABLE almacenes 
ADD INDEX idx_almacenes_empresa_estado (
    id_empresa, 
    estado
);

-- =====================================================
-- PASO 3: Verificar que los índices se crearon
-- =====================================================

SHOW INDEX FROM repuestos;
SHOW INDEX FROM repuesto_precios;
SHOW INDEX FROM almacenes;

-- =====================================================
-- NOTAS:
-- - MySQL puede usar online DDL para ALTER sin downtime
-- - Los índices pueden aumentar tiempo de INSERT/UPDATE
--   pero con 355 filas es negligible
-- - Para verificar mejora: usar EXPLAIN antes y después
-- =====================================================