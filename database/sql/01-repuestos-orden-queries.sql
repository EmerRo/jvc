-- =====================================================
-- DOCUMENTACIÓN: Consultas SQL de /orden/repuestos
-- Proyecto: JVC - Sistema de Facturación Electrónica
-- Página: /orden/repuestos (FragmentController@repuestos)
-- Fecha: 2026-05-19
-- =====================================================

-- =====================================================
-- SECCIÓN 1: CONSULTAS DE LECTURA (SELECT)
-- =====================================================

-- ---------------------------------------------------------
-- Query 1: Lista de repuestos (DataTables server-side)
-- Archivo: RepuestosController.php:43 (listaRepuestoServerSide)
-- Tabla: view_repuestos_{almacen}
-- Tipo: LECTURA con paginación
-- Efficiency: ✅ EFICIENTE - usa PRIMARY del view
-- ---------------------------------------------------------
SELECT SQL_CALC_FOUND_ROWS 
    id_repuesto, codigo, nombre, detalle, precio, costo, 
    cantidad, almacen, unidad, iscbp, cod_barra, 
    unidad_nombre, categoria
FROM view_repuestos_{almacen}
WHERE id_empresa = ? AND sucursal = ?
ORDER BY codigo ASC
LIMIT ? OFFSET ?;

-- ---------------------------------------------------------
-- Query 2: Ver todas las filas de repuestos (sin paginación)
-- Archivo: Repuesto.php:209 (verFilas)
-- Tabla: repuestos
-- Tipo: LECTURA
-- Efficiency: ❌ INEFICIENTE - full table scan
-- Issue: No hay índice compuesto en (id_empresa, sucursal, estado, almacen)
-- ---------------------------------------------------------
SELECT * 
FROM repuestos 
WHERE id_empresa = ? 
  AND sucursal = ? 
  AND estado = '1' 
  AND almacen = ? 
ORDER BY id_repuesto DESC;

-- ---------------------------------------------------------
-- Query 3: Buscar repuesto por código (importación Excel)
-- Archivo: RepuestosController.php:119
-- Tabla: repuestos
-- Tipo: LECTURA (verificar si existe)
-- Efficiency: ❌ INEFICIENTE - no usa índice en codigo
-- Issue: No hay índice en (codigo, id_empresa, sucursal, almacen)
-- ---------------------------------------------------------
SELECT * 
FROM repuestos 
WHERE codigo = ? 
  AND id_empresa = ? 
  AND sucursal = ? 
  AND almacen = ?;

-- ---------------------------------------------------------
-- Query 4: Buscar repuesto por código (con TRIM)
-- Archivo: RepuestosController.php:261 (informacionPorCodigo)
-- Tabla: repuestos
-- Tipo: LECTURA
-- Efficiency: ❌ INEFICIENTE - TRIM() previene uso de índice
-- Issue: TRIM(codigo) = ? no puede usar índice
-- ---------------------------------------------------------
SELECT * 
FROM repuestos 
WHERE TRIM(codigo) = ? 
  AND almacen = ? 
  AND sucursal = ?;

-- ---------------------------------------------------------
-- Query 5: Buscar repuesto por ID
-- Archivo: RepuestosController.php:273
-- Tabla: repuestos
-- Tipo: LECTURA
-- Efficiency: ✅ EFICIENTE - usa PRIMARY (id_repuesto)
-- ---------------------------------------------------------
SELECT * 
FROM repuestos 
WHERE id_repuesto = ?;

-- ---------------------------------------------------------
-- Query 6: Autocomplete buscar repuesto
-- Archivo: Consultas.php:116 (buscarRepuesto)
-- Tabla: repuestos
-- Tipo: LECTURA (búsqueda con LIKE)
-- Efficiency: ❌ INEFICIENTE - LIKE con comodín al inicio
-- Issue: LIKE '%term%' no puede usar índice en nombre/descripcion/codigo
-- ---------------------------------------------------------
SELECT r.id_repuesto, r.codigo, r.nombre, r.detalle, 
       r.precio, r.precio2, r.precio3, r.precio4,
       r.precio_unidad, r.costo, r.cantidad, 
       r.descripcion, r.usar_multiprecio, 
       r.precio_mayor, r.precio_menor, r.unidad
FROM repuestos r
WHERE r.id_empresa = ?
  AND (r.nombre LIKE ? OR r.descripcion LIKE ? OR r.codigo LIKE ?)
  AND r.sucursal = ?
  AND r.almacen = ?
  AND r.estado = '1'
ORDER BY r.nombre ASC
LIMIT 500;

-- ---------------------------------------------------------
-- Query 7: Obtener próximo ID de repuesto
-- Archivo: Repuesto.php:186
-- Tabla: repuestos
-- Tipo: LECTURA (generar ID siguiente)
-- Efficiency: ✅ EFICIENTE - usa PRIMARY
-- Risk: Race condition bajo inserts concurrentes
-- ---------------------------------------------------------
SELECT IFNULL(MAX(id_repuesto) + 1, 1) AS codigo 
FROM repuestos;

-- ---------------------------------------------------------
-- Query 8: Cargar precios múltiples de repuesto
-- Archivo: RepuestosController.php:621
-- Tabla: repuesto_precios
-- Tipo: LECTURA
-- Efficiency: ⚠️ POSIBLE ISSUE - no hay índice explícito en id_repuesto
-- ---------------------------------------------------------
SELECT * 
FROM repuesto_precios 
WHERE id_repuesto = ?;

-- ---------------------------------------------------------
-- Query 9: Ver historial de stock de repuesto
-- Archivo: RepuestosController.php:839
-- Tabla: historial_stock_repuestos
-- Tipo: LECTURA
-- ⚠️ IMPORTANTE: Esta tabla NO existe en la BD
-- ---------------------------------------------------------
SELECT h.*, r.nombre AS repuesto_nombre, r.codigo 
FROM historial_stock_repuestos h
INNER JOIN repuestos r ON h.id_repuesto = r.id_repuesto
WHERE h.id_repuesto = ?
ORDER BY h.fecha_movimiento DESC;

-- ---------------------------------------------------------
-- Query 10: Grid paginado de repuestos
-- Archivo: RepuestosController.php:909,914
-- Tablas: repuestos, unidades
-- Tipo: LECTURA con COUNT
-- Efficiency: ❌ INEFICIENTE - COUNT(*) full table scan
-- Issue: No hay índice en (id_empresa, sucursal, estado, almacen)
-- ---------------------------------------------------------
SELECT COUNT(*) AS total 
FROM repuestos r 
WHERE r.id_empresa = ? 
  AND r.sucursal = ? 
  AND r.estado = '1' 
  AND r.almacen = ?;

SELECT r.*, r.unidad_nombre 
FROM repuestos r
LEFT JOIN unidades u ON r.unidad = u.id
WHERE r.id_empresa = ? 
  AND r.sucursal = ? 
  AND r.estado = '1' 
  AND r.almacen = ?
ORDER BY r.codigo ASC
LIMIT ? OFFSET ?;

-- ---------------------------------------------------------
-- Query 11: Listar todos los almacenes (para productos)
-- Archivo: Almacen.php:20
-- Tabla: almacenes
-- Tipo: LECTURA
-- Efficiency: ✅ EFICIENTE - usa PRIMARY
-- ---------------------------------------------------------
SELECT * 
FROM almacenes 
WHERE id_empresa = ? 
  AND estado = '1' 
ORDER BY principal DESC, id_almacen ASC;

-- ---------------------------------------------------------
-- Query 12: Verificar productos por almacén (antes de eliminar)
-- Archivo: Almacen.php:38
-- Tabla: productos
-- Tipo: LECTURA
-- Efficiency: ❌ INEFICIENTE - no hay índice en (almacen, estado)
-- ---------------------------------------------------------
SELECT COUNT(*) AS cnt 
FROM productos 
WHERE almacen = ? 
  AND estado = '1';

-- =====================================================
-- SECCIÓN 2: CONSULTAS DE ESCRITURA (INSERT/UPDATE)
-- =====================================================

-- ---------------------------------------------------------
-- Query 13: Importar repuesto desde Excel - UPDATE
-- Archivo: RepuestosController.php:128
-- Tabla: repuestos
-- Tipo: UPDATE
-- Efficiency: ✅ EFICIENTE - usa PRIMARY
-- ---------------------------------------------------------
UPDATE repuestos 
SET nombre = ?,
    detalle = ?,
    precio = ?,
    precio2 = ?,
    almacen = ?,
    precio_unidad = ?,
    costo = ?,
    cantidad = ?,
    moneda = ?
WHERE codigo = ? 
  AND id_empresa = ? 
  AND sucursal = ? 
  AND almacen = ?;

-- ---------------------------------------------------------
-- Query 14: Importar repuesto desde Excel - INSERT
-- Archivo: RepuestosController.php:162
-- Tabla: repuestos
-- Tipo: INSERT
-- Efficiency: ✅ EFICIENTE - usa PRIMARY
-- ---------------------------------------------------------
INSERT INTO repuestos (
    nombre, detalle, precio, precio2, almacen, 
    precio_unidad, costo, cantidad, iscbp, 
    id_empresa, sucursal, codigo, ultima_salida, 
    codsunat, moneda
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1000-01-01', ?, ?);

-- ---------------------------------------------------------
-- Query 15: Restock (aumentar stock)
-- Archivo: RepuestosController.php:253
-- Tabla: repuestos
-- Tipo: UPDATE
-- ⚠️ ISSUE: Interpolación directa de $_POST
-- ---------------------------------------------------------
UPDATE repuestos 
SET cantidad = cantidad + ? 
WHERE id_repuesto = ?;

-- ---------------------------------------------------------
-- Query 16: Agregar nuevo repuesto
-- Archivo: RepuestosController.php:314
-- Tabla: repuestos
-- Tipo: INSERT
-- ⚠️ CRITICAL: SQL injection vulnerability
-- Issue: $_POST directamente interpolado en SQL string
-- ---------------------------------------------------------
INSERT INTO repuestos SET 
    nombre = ?,
    precio = ?,
    costo = ?,
    almacen = ?,
    cantidad = ?,
    iscbp = ?,
    sucursal = ?,
    id_empresa = ?,
    ultima_salida = '1000-01-01',
    codsunat = ?,
    precio_mayor = ?,
    precio_menor = ?,
    precio2 = ?,
    precio3 = ?,
    precio4 = ?,
    precio_unidad = ?,
    razon_social = ?,
    ruc = ?,
    detalle = ?,
    categoria = ?,
    subcategoria = ?,
    unidad = ?,
    moneda = ?,
    usar_multiprecio = ?,
    usar_barra = ?,
    cod_barra = ?,
    codigo = ?;

-- ---------------------------------------------------------
-- Query 17: Insertar precios múltiples
-- Archivo: RepuestosController.php:358
-- Tabla: repuesto_precios
-- Tipo: INSERT
-- Efficiency: ✅ EFICIENTE
-- ---------------------------------------------------------
INSERT INTO repuesto_precios (
    id_repuesto, nombre, precio
) VALUES (?, ?, ?);

-- ---------------------------------------------------------
-- Query 18: Actualizar repuesto (por descripcion)
-- Archivo: RepuestosController.php:401
-- Tabla: repuestos
-- Tipo: UPDATE
-- ⚠️ ISSUE: Usa descripcion como filtro (no hay índice)
-- ---------------------------------------------------------
UPDATE repuestos 
SET ... 
WHERE descripcion = ? 
  AND almacen = ?;

-- ---------------------------------------------------------
-- Query 19: Actualizar repuesto (por ID)
-- Archivo: RepuestosController.php:437
-- Tabla: repuestos
-- Tipo: UPDATE
-- Efficiency: ✅ EFICIENTE - usa PRIMARY
-- ---------------------------------------------------------
UPDATE repuestos 
SET ... 
WHERE id_repuesto = ?;

-- ---------------------------------------------------------
-- Query 20: Actualizar precios de repuesto
-- Archivo: RepuestosController.php:483
-- Tabla: repuestos
-- Tipo: UPDATE
-- ⚠️ ISSUE: Interpolación directa de $_POST
-- ---------------------------------------------------------
UPDATE repuestos 
SET precio = ?,
    precio_unidad = ?,
    precio2 = ?,
    precio3 = ?,
    precio4 = ?
WHERE id_repuesto = ?;

-- ---------------------------------------------------------
-- Query 21: Soft delete repuesto (en lote)
-- Archivo: RepuestosController.php:559
-- Tabla: repuestos
-- Tipo: UPDATE (soft delete)
-- ⚠️ ISSUE: Loop con queries individuales - debería ser batch
-- ---------------------------------------------------------
UPDATE repuestos 
SET estado = 0 
WHERE id_repuesto = ?;

-- =====================================================
-- SECCIÓN 3: CONSULTAS DE TRASLADO Y MOVIMIENTOS
-- =====================================================

-- ---------------------------------------------------------
-- Query 22: Confirmar traslado de repuesto
-- Archivo: RepuestosController.php:509
-- Tabla: ingreso_egreso
-- Tipo: SELECT + INSERT
-- ⚠️ ISSUE: Interpolación directa de $_POST
-- ---------------------------------------------------------
SELECT id_repuesto, almacen_ingreso, almacen_egreso, cantidad 
FROM ingreso_egreso 
WHERE intercambio_id = ?;

-- ---------------------------------------------------------
-- Query 23: Aumentar stock + registrar historial
-- Archivo: RepuestosController.php:654,662
-- Tablas: repuestos, historial_stock_repuestos
-- ⚠️ IMPORTANTE: historial_stock_repuestos NO existe en BD
-- ---------------------------------------------------------
UPDATE repuestos 
SET cantidad = cantidad + ? 
WHERE id_repuesto = ?;

INSERT INTO historial_stock_repuestos (
    id_repuesto, tipo_movimiento, cantidad, 
    fecha_movimiento, usuario
) VALUES (?, 'INGRESO', ?, NOW(), ?);

-- ---------------------------------------------------------
-- Query 24: Disminuir stock + registrar historial
-- Archivo: RepuestosController.php:692,705,713
-- Tablas: repuestos, historial_stock_repuestos
-- ⚠️ IMPORTANTE: historial_stock_repuestos NO existe en BD
-- ---------------------------------------------------------
SELECT cantidad 
FROM repuestos 
WHERE id_repuesto = ?;

UPDATE repuestos 
SET cantidad = cantidad - ? 
WHERE id_repuesto = ?;

INSERT INTO historial_stock_repuestos (
    id_repuesto, tipo_movimiento, cantidad, 
    fecha_movimiento, usuario
) VALUES (?, 'EGRESO', ?, NOW(), ?);

-- ---------------------------------------------------------
-- Query 25: Transferencia entre almacenes
-- Archivo: RepuestosController.php:756-816
-- Tablas: repuestos, historial_stock_repuestos
-- ⚠️ IMPORTANTE: historial_stock_repuestos NO existe en BD
-- Issue: No hay transacción - posible ejecución parcial
-- ---------------------------------------------------------
-- EGRESO del almacén origen:
SELECT cantidad FROM repuestos WHERE id_repuesto = ? AND almacen = ?;
UPDATE repuestos SET cantidad = cantidad - ? WHERE id_repuesto = ? AND almacen = ?;
INSERT INTO historial_stock_repuestos (...) VALUES (...,'EGRESO',...);

-- INGRESO al almacén destino:
SELECT id_repuesto FROM repuestos WHERE id_repuesto = ? AND almacen = ?;
UPDATE repuestos SET cantidad = cantidad + ? WHERE id_repuesto = ? AND almacen = ?;
INSERT INTO historial_stock_repuestos (...) VALUES (...,'INGRESO',...);

-- =====================================================
-- SECCIÓN 4: VISTAS (VIEWS)
-- =====================================================

-- ---------------------------------------------------------
-- Vista: view_repuestos_{id}
-- Propósito: Filtrar repuestos por almacén para DataTables
-- Creación: Almacen.php:151 (similar a view_productos_*)
-- Estado: Existe para almacenes 1, 2, 3 (pero NO se auto-crean)
-- ⚠️ ISSUE: No hay auto-creación cuando se agrega nuevo almacén
-- ---------------------------------------------------------

CREATE OR REPLACE VIEW view_repuestos_1 AS
SELECT r.*, c.nombre AS categoria, u.nombre AS unidad
FROM repuestos r
LEFT JOIN categorias c ON c.id = r.categoria
LEFT JOIN unidades u ON u.id = r.unidad
WHERE r.id_empresa = 12 
  AND r.sucursal = '1' 
  AND r.estado = '1' 
  AND r.almacen = '1'
ORDER BY r.codigo ASC;

-- =====================================================
-- RESUMEN DE ISSUES ENCONTRADOS
-- =====================================================
-- 
-- 🔴 CRITICAL (SQL Injection):
--    - RepuestosController.php:314 (INSERT agregar)
--    - RepuestosController.php:253 (UPDATE restock)
--    - RepuestosController.php:483 (UPDATE precios)
--    - RepuestosController.php:509 (SELECT traslado)
--
-- 🟡 EFFICIENCY (Missing Indexes):
--    - No índice en (id_empresa, sucursal, estado, almacen)
--    - No índice en (codigo, id_empresa, sucursal, almacen)
--    - No índice en repuesto_precios(id_repuesto)
--    - No índice en almacenes(id_empresa, estado)
--    - TRIM(codigo) previene uso de índice
--
-- 🟡 DATA INTEGRITY:
--    - repuestos.almacen es CHAR(1), no FK a almacenes.id_almacen
--    - historial_stock_repuestos NO existe en BD
--
-- 🟡 ARCHITECTURE:
--    - Views view_repuestos_* no se auto-crean con nuevo almacén
--    - No hay método Almacen::crearVistaRepuestos equivalente
--    - Soft delete en loop (debería ser batch)
--
-- =====================================================