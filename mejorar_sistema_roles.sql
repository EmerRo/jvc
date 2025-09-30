-- ======================================================================
-- MEJORA DEL SISTEMA DE ROLES - JVC SYSTEM
-- Fecha: 29-09-2025
-- Descripción: Script para mejorar el sistema de permisos basado en roles
-- ======================================================================

-- ======================================================================
-- 1. AGREGAR NUEVOS CAMPOS A LA TABLA ROLES
-- ======================================================================

-- Agregar columnas de permisos específicos
ALTER TABLE roles ADD COLUMN puede_crear_usuarios tinyint(1) DEFAULT 0 COMMENT 'Permiso para crear nuevos usuarios';
ALTER TABLE roles ADD COLUMN puede_gestionar_roles tinyint(1) DEFAULT 0 COMMENT 'Permiso para gestionar roles del sistema';
ALTER TABLE roles ADD COLUMN puede_acceder_configuracion tinyint(1) DEFAULT 0 COMMENT 'Permiso para acceder a configuración del sistema';

-- ======================================================================
-- 2. RENOMBRAR ROL ADMIN A SUPERADMINISTRADOR
-- ======================================================================

UPDATE roles SET nombre = 'SUPERADMINISTRADOR' WHERE rol_id = 1;

-- ======================================================================
-- 3. ACTUALIZAR PERMISOS DEL SUPERADMINISTRADOR
-- ======================================================================

-- El SUPERADMINISTRADOR tendrá TODOS los permisos
UPDATE roles SET
    ver_precios = 1,
    puede_eliminar = 1,
    puede_crear_usuarios = 1,
    puede_gestionar_roles = 1,
    puede_acceder_configuracion = 1
WHERE rol_id = 1;

-- ======================================================================
-- 4. CREAR NUEVO ROL ADMINISTRADOR
-- ======================================================================

-- Insertar nuevo rol ADMINISTRADOR con permisos limitados
INSERT INTO roles (nombre, ver_precios, puede_eliminar, puede_crear_usuarios, puede_gestionar_roles, puede_acceder_configuracion)
VALUES ('ADMINISTRADOR', 1, 0, 1, 0, 1);

-- Obtener el ID del nuevo rol ADMINISTRADOR para asignar permisos de módulos
SET @admin_rol_id = LAST_INSERT_ID();

-- ======================================================================
-- 5. ASIGNAR PERMISOS DE MÓDULOS AL NUEVO ROL ADMINISTRADOR
-- ======================================================================

-- Copiar la mayoría de permisos del SUPERADMINISTRADOR, excepto usuarios y algunos críticos
INSERT INTO rol_permisos (rol_id, modulo_id, submodulo_id)
SELECT @admin_rol_id, modulo_id, submodulo_id
FROM rol_permisos
WHERE rol_id = 1
AND modulo_id NOT IN ('usuarios'); -- El ADMINISTRADOR no puede gestionar usuarios por defecto

-- Agregar permisos específicos para el ADMINISTRADOR
INSERT INTO rol_permisos (rol_id, modulo_id, submodulo_id) VALUES
(@admin_rol_id, 'dashboard', NULL),
(@admin_rol_id, 'facturacion', NULL),
(@admin_rol_id, 'cotizaciones', NULL),
(@admin_rol_id, 'clientes', NULL),
(@admin_rol_id, 'almacen', NULL),
(@admin_rol_id, 'cajas', NULL),
(@admin_rol_id, 'garantia', NULL),
(@admin_rol_id, 'numero_series', NULL),
(@admin_rol_id, 'documentos', NULL);

-- ======================================================================
-- 6. ACTUALIZAR OTROS ROLES EXISTENTES (OPCIONAL)
-- ======================================================================

-- Actualizar rol VENDEDOR
UPDATE roles SET
    puede_crear_usuarios = 0,
    puede_gestionar_roles = 0,
    puede_acceder_configuracion = 0
WHERE rol_id = 3;

-- Actualizar rol CAJERO
UPDATE roles SET
    puede_crear_usuarios = 0,
    puede_gestionar_roles = 0,
    puede_acceder_configuracion = 0
WHERE rol_id = 4;

-- Actualizar rol CONTADOR
UPDATE roles SET
    puede_crear_usuarios = 0,
    puede_gestionar_roles = 0,
    puede_acceder_configuracion = 0
WHERE rol_id = 5;

-- Actualizar rol ALMACEN
UPDATE roles SET
    puede_crear_usuarios = 0,
    puede_gestionar_roles = 0,
    puede_acceder_configuracion = 0
WHERE rol_id = 6;

-- ======================================================================
-- 7. CONSULTAS DE VERIFICACIÓN
-- ======================================================================

-- Verificar la estructura actualizada de roles
SELECT * FROM roles ORDER BY rol_id;

-- Verificar permisos asignados al nuevo ADMINISTRADOR
SELECT rp.*, r.nombre as rol_nombre
FROM rol_permisos rp
INNER JOIN roles r ON r.rol_id = rp.rol_id
WHERE r.nombre = 'ADMINISTRADOR';

-- Contar usuarios por rol
SELECT r.nombre, COUNT(u.usuario_id) as total_usuarios
FROM roles r
LEFT JOIN usuarios u ON u.id_rol = r.rol_id
GROUP BY r.rol_id, r.nombre
ORDER BY r.rol_id;

-- ======================================================================
-- 8. COMENTARIOS Y NOTAS IMPORTANTES
-- ======================================================================

/*
EXPLICACIÓN DE LOS NUEVOS PERMISOS:

1. puede_crear_usuarios: Permite crear y editar usuarios del sistema
2. puede_gestionar_roles: Permite crear, editar y eliminar roles
3. puede_acceder_configuracion: Permite acceder a configuraciones del sistema

ROLES DESPUÉS DE LA ACTUALIZACIÓN:
- SUPERADMINISTRADOR: Acceso completo a todo el sistema
- ADMINISTRADOR: Acceso amplio pero sin gestión de usuarios ni roles
- Otros roles: Mantienen sus permisos actuales + nuevos campos en 0

PRÓXIMOS PASOS:
1. Ejecutar este script en la base de datos
2. Actualizar UsuariosController.php para validar nuevos permisos
3. Actualizar la vista usuarios.php para mostrar nuevos campos
4. Actualizar formularios de roles para incluir nuevos permisos
*/