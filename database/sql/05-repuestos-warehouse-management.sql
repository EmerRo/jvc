-- =====================================================
-- MIGRACIÓN: Agregar gestión de almacenes para repuestos
-- Proyecto: JVC - Sistema de Facturación Electrónica
-- Fecha: 2026-05-19
-- Propósito: Permitir crear/editar/eliminar almacenes desde
--            la UI de repuestos (/orden/repuestos)
-- =====================================================

-- =====================================================
-- VERIFICACIÓN: Estado antes de cambios
-- =====================================================

-- Verificar que las tablas existen
SELECT 'almacenes' as tabla, COUNT(*) as total FROM almacenes;
SELECT 'repuestos' as tabla, COUNT(*) as total FROM repuestos;

-- Verificar vistas existentes para repuestos
SHOW TABLES LIKE 'view_repuestos_%';

-- Verificar índices en repuestos (del migration anterior)
SHOW INDEX FROM repuestos WHERE Key_name LIKE 'idx_%';

-- =====================================================
-- PASO 1: Crear vistas faltantes para repuestos
-- =====================================================

-- Las vistas view_repuestos_1, view_repuestos_2, view_repuestos_3
-- deben crearse automáticamente cuando se crea un nuevo almacén.
-- Aquí creamos las que pudieran faltar:

-- Verificar si existe view_repuestos_1
SELECT COUNT(*) as existe FROM information_schema.views 
WHERE table_schema = DATABASE() AND table_name = 'view_repuestos_1';

-- Si no existe, crearla:
/*
CREATE OR REPLACE VIEW view_repuestos_1 AS
SELECT r.*, COALESCE(c.nombre, '') AS categoria, COALESCE(u.nombre, '') AS unidad_nombre
FROM repuestos r
LEFT JOIN categorias c ON c.id = r.categoria
LEFT JOIN unidades u ON u.id = r.unidad
WHERE r.id_empresa = 12 AND r.sucursal = '1' AND r.estado = '1' AND r.almacen = '1'
ORDER BY CASE WHEN r.codigo LIKE 'JVC%' THEN 0 ELSE 1 END, r.codigo ASC;
*/

-- =====================================================
-- PASO 2: Verificar estructura de repuestos.almacen
-- =====================================================

-- Importante: repuestos.almacen es CHAR(1), no INT
-- Esto es diferente de productos.almacen (INT FK a almacenes.id_almacen)
DESCRIBE repuestos;

-- Los valores actuales son '1', '2', '3', '4' (strings, no integers)
SELECT DISTINCT almacen FROM repuestos ORDER BY almacen;

-- =====================================================
-- CONSULTAS DE VERIFICACIÓN POST-CREACIÓN
-- =====================================================

-- Probar endpoint listar almacenes
-- POST /ajs/data/repuesto/almacen/listar
-- Esperado: JSON con { estado: true, almacenes: [...] }

-- Probar crear almacén
-- POST /ajs/data/repuesto/almacen/agregar
-- Body: { nombre: "Nuevo Almacén" }
-- Esperado: { estado: true, mensaje: "Almacén agregado", id: N }

-- Probar que se crea la vista view_repuestos_N
SELECT COUNT(*) as existe FROM information_schema.views 
WHERE table_schema = DATABASE() AND table_name = 'view_repuestos_N';

-- =====================================================
-- NOTAS IMPORTANTES
-- =====================================================

-- 1. Los endpoints usan Almacen model (compartido con productos)
--    para CRUD de almacenes en tabla 'almacenes'

-- 2. Al crear un almacén, se llama Repuesto::crearVista($id)
--    que crea view_repuestos_{id} automáticamente

-- 3. La vista repuestos.php tiene Vue con:
--    - almacenes: [] (cargado desde mounted())
--    - modal #modal-agregar-almacen-repuesto
--    - métodos: abrirModalAlmacen, seleccionarAlmacen,
--      agregarNuevoAlmacen, guardarAlmacenEdicion, eliminarAlmacen

-- 4. Las rutas añadidas en ajax2.php:
--    POST /ajs/data/repuesto/almacen/listar
--    POST /ajs/data/repuesto/almacen/agregar
--    POST /ajs/data/repuesto/almacen/editar
--    POST /ajs/data/repuesto/almacen/eliminar

-- =====================================================