<?php

class ModulosHelper
{
    private static $modulos = null;
    
    /**
     * Cargar módulos desde el archivo JSON
     */
    private static function cargarModulos()
    {
        if (self::$modulos === null) {
            $jsonPath = __DIR__ . '/../../ServerSide/modulos.json';
            if (file_exists($jsonPath)) {
                $jsonContent = file_get_contents($jsonPath);
                $data = json_decode($jsonContent, true);
                self::$modulos = $data['modulos'] ?? [];
            } else {
                self::$modulos = [];
            }
        }
        return self::$modulos;
    }
    
    /**
     * Obtener todos los módulos
     */
    public static function obtenerTodosLosModulos()
    {
        return self::cargarModulos();
    }
    
    /**
     * Obtener módulos permitidos para un rol específico
     */
    public static function obtenerModulosParaRol($rol_id, $conectar)
    {
        $todosLosModulos = self::cargarModulos();
        
        // Si es ADMIN, devolver todos los módulos
        if ($rol_id == 1) {
            return $todosLosModulos;
        }
        
        // Obtener permisos del rol desde la nueva tabla rol_permisos
        $sql = "SELECT modulo_id, submodulo_id FROM rol_permisos WHERE rol_id = ?";
        $stmt = mysqli_prepare($conectar, $sql);
        mysqli_stmt_bind_param($stmt, "i", $rol_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        $permisos = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $permisos[] = $row;
        }
        
        // Filtrar módulos según permisos
        $modulosPermitidos = [];
        foreach ($todosLosModulos as $modulo) {
            $moduloPermitido = false;
            $submodulosPermitidos = [];
            
            // Verificar si el módulo completo está permitido
            foreach ($permisos as $permiso) {
                if ($permiso['modulo_id'] === $modulo['id'] && $permiso['submodulo_id'] === null) {
                    $moduloPermitido = true;
                    $submodulosPermitidos = $modulo['submodulos'];
                    break;
                }
            }
            
            // Si no está permitido el módulo completo, verificar submódulos individuales
            if (!$moduloPermitido && !empty($modulo['submodulos'])) {
                foreach ($modulo['submodulos'] as $submodulo) {
                    foreach ($permisos as $permiso) {
                        if ($permiso['modulo_id'] === $modulo['id'] && $permiso['submodulo_id'] === $submodulo['id']) {
                            $submodulosPermitidos[] = $submodulo;
                            $moduloPermitido = true;
                        }
                    }
                }
            }
            
            // Si el módulo tiene permisos, agregarlo a la lista
            if ($moduloPermitido) {
                $moduloFiltrado = $modulo;
                $moduloFiltrado['submodulos'] = $submodulosPermitidos;
                $modulosPermitidos[] = $moduloFiltrado;
            }
        }
        
        return $modulosPermitidos;
    }
    
    /**
     * Verificar si un usuario tiene permiso para acceder a una ruta
     */
    public static function verificarPermiso($usuario_id, $ruta_actual, $conectar)
    {
        // Obtener rol del usuario
        $sql = "SELECT id_rol FROM usuarios WHERE usuario_id = ?";
        $stmt = mysqli_prepare($conectar, $sql);
        mysqli_stmt_bind_param($stmt, "i", $usuario_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $usuario = mysqli_fetch_assoc($result);
        
        if (!$usuario) {
            return false;
        }
        
        // Si es administrador, siempre tiene permiso
        if ($usuario['id_rol'] == 1) {
            return true;
        }
        
        // Obtener módulos permitidos para el rol
        $modulosPermitidos = self::obtenerModulosParaRol($usuario['id_rol'], $conectar);
        
        // Verificar si la ruta está permitida
        foreach ($modulosPermitidos as $modulo) {
            // Verificar ruta del módulo principal
            if (strpos($ruta_actual, $modulo['ruta']) !== false) {
                return true;
            }
            
            // Verificar rutas de submódulos
            foreach ($modulo['submodulos'] as $submodulo) {
                if (strpos($ruta_actual, $submodulo['ruta']) !== false) {
                    return true;
                }
            }
        }
        
        return false;
    }
    
    /**
     * Obtener un módulo específico por ID
     */
    public static function obtenerModuloPorId($modulo_id)
    {
        $modulos = self::cargarModulos();
        foreach ($modulos as $modulo) {
            if ($modulo['id'] === $modulo_id) {
                return $modulo;
            }
        }
        return null;
    }
    
    /**
     * Obtener submódulo específico por ID de módulo y submódulo
     */
    public static function obtenerSubmoduloPorId($modulo_id, $submodulo_id)
    {
        $modulo = self::obtenerModuloPorId($modulo_id);
        if ($modulo && !empty($modulo['submodulos'])) {
            foreach ($modulo['submodulos'] as $submodulo) {
                if ($submodulo['id'] === $submodulo_id) {
                    return $submodulo;
                }
            }
        }
        return null;
    }
}