<?php
// app/http/middleware/VerificarPermisosMiddleware.php

class VerificarPermisosMiddleware extends Middleware
{
    public function valid()
    {
        // Rutas que no requieren verificación
        $rutas_publicas = [
            '/jvc/login',
            '/jvc/logout',
            '/jvc/acceso-denegado',
            '/jvc/assets/',
            '/jvc/public/',
            '/jvc/r/',
            '/jvc/ge/',
            '/jvc/reporte/'
        ];
        
        $uri = $_SERVER['REQUEST_URI'];
        
        // Verificar rutas públicas
        foreach ($rutas_publicas as $ruta) {
            if (strpos($uri, $ruta) === 0) {
                return true;
            }
        }
        
        // Verificar autenticación
        if (!isset($_SESSION['usuario_fac'])) {
            header('Location: /jvc/login');
            exit;
        }
        
        $rol_id = $_SESSION['rol'];
        
        // Si es administrador, permitir acceso
        if ($rol_id == 1) {
            return true;
        }
        
        try {
            $conexion = new Conexion();
            $conectar = $conexion->getConexion();
            
            // Verificar permisos
            $sql = "SELECT COUNT(*) as tiene_permiso 
                    FROM rol_modulo rm 
                    INNER JOIN modulos m ON rm.modulo_id = m.modulo_id 
                    WHERE rm.rol_id = ? AND ? LIKE CONCAT('%', m.ruta, '%')";
                    
            $stmt = $conectar->prepare($sql);
            $stmt->bind_param("is", $rol_id, $uri);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            if ($result['tiene_permiso'] == 0) {
                header('Location: /jvc/acceso-denegado');
                exit;
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error en verificación de permisos: " . $e->getMessage());
            header('Location: /jvc/acceso-denegado');
            exit;
        }
    }
}