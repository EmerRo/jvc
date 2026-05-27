-- =====================================================
-- MIGRACIÓN: Crear Vistas para Repuestos por Almacén
-- Proyecto: JVC - Sistema de Facturación Electrónica
-- Fecha: 2026-05-19
-- Propósito: DataTables server-side funciona con views
--           view_repuestos_1, view_repuestos_2, view_repuestos_3
-- =====================================================

-- =====================================================
-- PASO 1: Verificar vistas existentes
-- =====================================================

SHOW TABLES LIKE 'view_repuestos%';

-- =====================================================
-- PASO 2: Crear vistas para almacenes 1, 2, 3
-- =====================================================

-- Vista para almacén 1
CREATE OR REPLACE VIEW view_repuestos_1 AS
SELECT 
    r.*,
    COALESCE(c.nombre, '') AS categoria,
    COALESCE(u.nombre, '') AS unidad,
    '1' AS almacen_nombre
FROM repuestos r
LEFT JOIN categorias c ON c.id = r.categoria
LEFT JOIN unidades u ON u.id = r.unidad
WHERE r.id_empresa = 12 
  AND r.sucursal = '1' 
  AND r.estado = '1' 
  AND r.almacen = '1'
ORDER BY 
    CASE WHEN r.codigo LIKE 'JVC%' THEN 0 ELSE 1 END,
    r.codigo ASC;

-- Vista para almacén 2
CREATE OR REPLACE VIEW view_repuestos_2 AS
SELECT 
    r.*,
    COALESCE(c.nombre, '') AS categoria,
    COALESCE(u.nombre, '') AS unidad,
    '2' AS almacen_nombre
FROM repuestos r
LEFT JOIN categorias c ON c.id = r.categoria
LEFT JOIN unidades u ON u.id = r.unidad
WHERE r.id_empresa = 12 
  AND r.sucursal = '1' 
  AND r.estado = '1' 
  AND r.almacen = '2'
ORDER BY 
    CASE WHEN r.codigo LIKE 'JVC%' THEN 0 ELSE 1 END,
    r.codigo ASC;

-- Vista para almacén 3
CREATE OR REPLACE VIEW view_repuestos_3 AS
SELECT 
    r.*,
    COALESCE(c.nombre, '') AS categoria,
    COALESCE(u.nombre, '') AS unidad,
    '3' AS almacen_nombre
FROM repuestos r
LEFT JOIN categorias c ON c.id = r.categoria
LEFT JOIN unidades u ON u.id = r.unidad
WHERE r.id_empresa = 12 
  AND r.sucursal = '1' 
  AND r.estado = '1' 
  AND r.almacen = '3'
ORDER BY 
    CASE WHEN r.codigo LIKE 'JVC%' THEN 0 ELSE 1 END,
    r.codigo ASC;

-- =====================================================
-- PASO 3: Verificar que las vistas se crearon
-- =====================================================

SHOW FULL TABLES LIKE 'view_repuestos%';

-- Probar cada vista
SELECT COUNT(*) FROM view_repuestos_1;
SELECT COUNT(*) FROM view_repuestos_2;
SELECT COUNT(*) FROM view_repuestos_3;

-- =====================================================
-- NOTAS:
-- - Estas vistas deben crearse automáticamente cuando
--   se crea un nuevo almacén (similar a Almacen::crearVista)
-- - El código RepuestosController.php:43 busca 
--   view_repuestos_{almacen} dinámicamente
-- =====================================================