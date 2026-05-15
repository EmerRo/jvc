-- =====================================================
-- MIGRACIÓN: Productos y Repuestos al Almacén Principal
-- Empresa: JVC (id_empresa = 12)
-- Almacén Principal: jikbio (id_almacen = 4)
-- Fecha: 2026-05-15
-- =====================================================

-- -----------------------------------------------------
-- PASO 1: Ver estado ANTES de la migración
-- -----------------------------------------------------

-- Ver productos por almacén (empresa 12)
SELECT 
    'ANTES - Productos por almacen:' as info,
    almacen, 
    COUNT(*) as cantidad 
FROM productos 
WHERE id_empresa = 12 
GROUP BY almacen;

-- Ver repuestos por almacén (empresa 12)
SELECT 
    'ANTES - Repuestos por almacen:' as info,
    almacen, 
    COUNT(*) as cantidad 
FROM repuestos 
WHERE id_empresa = 12 
GROUP BY almacen;

-- -----------------------------------------------------
-- PASO 2: Migrar PRODUCTOS al almacén principal
-- -----------------------------------------------------

UPDATE productos 
SET almacen = 4 
WHERE id_empresa = 12;

-- -----------------------------------------------------
-- PASO 3: Migrar REPUESTOS al almacén principal
-- -----------------------------------------------------

UPDATE repuestos 
SET almacen = '4' 
WHERE id_empresa = 12;

-- -----------------------------------------------------
-- PASO 4: Recrear/Actualizar la vista view_productos_4
-- -----------------------------------------------------

DROP VIEW IF EXISTS view_productos_4;

CREATE VIEW view_productos_4 AS 
SELECT 
    p.id_producto, 
    p.cod_barra, 
    p.nombre, 
    p.precio, 
    p.costo, 
    p.cantidad, 
    p.iscbp, 
    p.id_empresa, 
    p.sucursal, 
    p.ultima_salida, 
    p.codsunat, 
    p.usar_barra, 
    p.precio_mayor, 
    p.precio_menor, 
    p.razon_social, 
    p.ruc, 
    p.estado, 
    p.almacen, 
    p.precio2, 
    p.precio3, 
    p.precio4, 
    p.precio_unidad, 
    p.codigo, 
    p.imagen, 
    p.detalle, 
    c.nombre AS categoria, 
    u.nombre AS unidad, 
    p.moneda 
FROM productos p 
LEFT JOIN categorias c ON c.id = p.categoria 
LEFT JOIN unidades u ON u.id = p.unidad 
WHERE 
    p.id_empresa = '12' 
    AND p.sucursal = '1' 
    AND p.estado = '1' 
    AND p.almacen = '4' 
ORDER BY 
    CASE WHEN p.codigo LIKE 'JVC%' THEN 0 ELSE 1 END, 
    p.codigo ASC;

-- -----------------------------------------------------
-- PASO 5: Verificar DESPUÉS de la migración
-- -----------------------------------------------------

-- Resumen de productos y repuestos por almacén
SELECT 
    'DESPUES - Productos:' as tipo, 
    almacen, 
    COUNT(*) as cantidad 
FROM productos 
WHERE id_empresa = 12 
GROUP BY almacen

UNION ALL 

SELECT 
    'DESPUES - Repuestos:' as tipo, 
    almacen, 
    COUNT(*) as cantidad 
FROM repuestos 
WHERE id_empresa = 12 
GROUP BY almacen;

-- Verificar que NO hay productos huérfanos
SELECT 
    'Verificacion - Productos sin almacen:' as info,
    COUNT(*) as productos_sin_almacen 
FROM productos p 
WHERE p.almacen NOT IN (
    SELECT id_almacen 
    FROM almacenes 
    WHERE id_empresa = p.id_empresa
);

-- Estado final de todos los almacenes
SELECT 
    'Estado final de almacenes:' as info,
    a.id_almacen, 
    a.nombre, 
    a.principal, 
    COUNT(p.id_producto) as productos 
FROM almacenes a 
LEFT JOIN productos p ON p.almacen = a.id_almacen 
GROUP BY a.id_almacen, a.nombre, a.principal 
ORDER BY a.id_empresa, a.principal DESC;

-- =====================================================
-- NOTAS:
-- - Este script migra TODOS los productos de la empresa 12
--   al almacén principal 'jikbio' (id_almacen = 4)
-- - El mismo proceso aplica para repuestos
-- - Antes de ejecutar en producción, verifica que el
--   id_almacen 4 sea el almacén principal de la empresa
-- =====================================================