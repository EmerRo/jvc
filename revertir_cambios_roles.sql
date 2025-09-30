-- ======================================================================
-- REVERTIR CAMBIOS DEL SISTEMA DE ROLES - JVC SYSTEM
-- Fecha: 29-09-2025
-- Descripción: Script para revertir todos los cambios realizados
-- ======================================================================

-- ======================================================================
-- 1. ELIMINAR EL ROL ADMINISTRADOR CREADO (si existe)
-- ======================================================================

-- Primero eliminar los permisos del rol ADMINISTRADOR
DELETE FROM rol_permisos WHERE rol_id IN (
    SELECT rol_id FROM roles WHERE nombre = 'ADMINISTRADOR'
);

-- Luego eliminar el rol ADMINISTRADOR
DELETE FROM roles WHERE nombre = 'ADMINISTRADOR';

-- ======================================================================
-- 2. REVERTIR EL NOMBRE DE SUPERADMINISTRADOR A ADMIN
-- ======================================================================

UPDATE roles SET nombre = 'ADMIN' WHERE nombre = 'SUPERADMINISTRADOR';

-- ======================================================================
-- 3. ELIMINAR LAS COLUMNAS AGREGADAS A LA TABLA ROLES
-- ======================================================================

-- Eliminar las columnas que se agregaron
ALTER TABLE roles DROP COLUMN IF EXISTS puede_crear_usuarios;
ALTER TABLE roles DROP COLUMN IF EXISTS puede_gestionar_roles;
ALTER TABLE roles DROP COLUMN IF EXISTS puede_acceder_configuracion;

-- ======================================================================
-- 4. RESTAURAR VALORES ORIGINALES DE PERMISOS
-- ======================================================================

-- Restaurar todos los roles existentes a sus valores por defecto
UPDATE roles SET ver_precios = 1, puede_eliminar = 1 WHERE rol_id = 1; -- ADMIN
UPDATE roles SET ver_precios = 1, puede_eliminar = 1 WHERE rol_id = 2; -- USUARIO
UPDATE roles SET ver_precios = 1, puede_eliminar = 1 WHERE rol_id = 3; -- VENDEDOR
UPDATE roles SET ver_precios = 1, puede_eliminar = 1 WHERE rol_id = 4; -- CAJERO
UPDATE roles SET ver_precios = 1, puede_eliminar = 1 WHERE rol_id = 5; -- CONTADOR
UPDATE roles SET ver_precios = 1, puede_eliminar = 1 WHERE rol_id = 6; -- ALMACEN

-- ======================================================================
-- 5. CONSULTAS DE VERIFICACIÓN
-- ======================================================================

-- Verificar que la tabla roles está como antes
SELECT * FROM roles ORDER BY rol_id;

-- Verificar que las columnas fueron eliminadas
DESCRIBE roles;

-- ======================================================================
-- 6. COMENTARIOS FINALES
-- ======================================================================

/*
CAMBIOS REVERTIDOS:
1. ✅ Rol ADMINISTRADOR eliminado
2. ✅ SUPERADMINISTRADOR renombrado de vuelta a ADMIN
3. ✅ Columnas puede_crear_usuarios, puede_gestionar_roles, puede_acceder_configuracion eliminadas
4. ✅ Valores de permisos restaurados a los originales

ESTRUCTURA FINAL:
- Tabla roles con solo: rol_id, nombre, ver_precios, puede_eliminar
- Rol ADMIN (ID=1) con permisos completos
- Otros roles mantienen sus valores originales

NOTA: Después de ejecutar este script, también verifica que los archivos PHP
estén restaurados a su estado original con Ctrl+Z
*/