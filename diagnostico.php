<?php
session_start();
header('Content-Type: application/json');

// Información de sesión
$session_info = $_SESSION;

// Información del usuario
$usuario_id = isset($_SESSION['usuario_fac']) ? $_SESSION['usuario_fac'] : null;
$rol_id = isset($_SESSION['rol']) ? $_SESSION['rol'] : null;

$db_info = [];
if ($usuario_id && $rol_id) {
    try {
        // Conexión directa a la base de datos
        require_once 'src/bin/Conexion.php'; // Ajusta la ruta según tu estructura
        
        $conexion = new Conexion();
        $conectar = $conexion->getConexion();
        
        // Verifica conexión
        if ($conectar) {
            // Verifica módulos para el rol
            $sql = "SELECT COUNT(*) as total FROM rol_modulo WHERE rol_id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bind_param("i", $rol_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            $db_info['modulos_count'] = $result['total'];
            
            // Verifica usuario
            $sql = "SELECT id_rol, usuario FROM usuarios WHERE usuario_id = ?";
            $stmt = $conectar->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            $db_info['usuario'] = $result;
            
            // Verifica rutas de módulos
            $sql = "SELECT * FROM modulos";
            $result = mysqli_query($conectar, $sql);
            $modulos = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $modulos[] = $row;
            }
            $db_info['modulos'] = $modulos;
        } else {
            $db_info['error'] = "No hay conexión a la base de datos";
        }
    } catch (Exception $e) {
        $db_info['error'] = $e->getMessage();
    }
}

// Información del servidor
$server_info = [
    'php_version' => phpversion(),
    'server' => $_SERVER['SERVER_SOFTWARE'],
    'request_uri' => $_SERVER['REQUEST_URI'],
    'script_name' => $_SERVER['SCRIPT_NAME'],
    'document_root' => $_SERVER['DOCUMENT_ROOT'],
    'include_path' => get_include_path()
];

echo json_encode([
    'session' => $session_info,
    'database' => $db_info,
    'server' => $server_info
], JSON_PRETTY_PRINT);