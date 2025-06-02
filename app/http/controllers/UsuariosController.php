<?php

class UsuariosController extends Controller
{
    private $cliente;
    public $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function render()
    {
        $sql = "SELECT
                    usuario_id,
                    r.nombre,
                    usuario,
                    email,
                    nombres,
                    telefono,
                    CASE 
                        WHEN sucursal = 1 THEN 'Tienda 435'
                        ELSE 'Tienda 426'
                    END AS tienda,
                    CASE 
                        WHEN rotativo = 0 THEN 'No'
                        ELSE 'Si'
                    END AS rotativo 
                FROM
                    usuarios u
                INNER JOIN roles r ON r.rol_id = u.id_rol";
        $fila = mysqli_query($this->conectar, $sql);
        $respuesta = mysqli_fetch_all($fila, MYSQLI_ASSOC);
        return json_encode($respuesta);
    }

    public function getOne()
    {
        $sql = "SELECT
                    usuario_id,
                    num_doc,
                    id_rol,
                    usuario,
                    email,
                    nombres,
                    telefono,
                    sucursal,
                    rotativo
                FROM
                    usuarios u
                where u.usuario_id = {$_POST["id"]}";
        $fila = mysqli_query($this->conectar, $sql);
        $respuesta = mysqli_fetch_all($fila, MYSQLI_ASSOC);
        return json_encode($respuesta);
    }

    public function editar()
    {
        $udp = "";
        if (isset($_POST["clave"]) && !empty($_POST["clave"])) {
            $clave = sha1($_POST["clave"]);
            $udp = "clave='$clave',";
        }
        $sql = "update usuarios set 
                    id_rol='{$_POST["rol"]}',
                    nombres='{$_POST["datosEditar"]}',
                    num_doc='{$_POST["doc"]}',
                    usuario='{$_POST["usuariou"]}',
                    $udp
                    telefono='{$_POST["telefonoEditar"]}',
                    email='{$_POST["emailEditar"]}',
                    sucursal={$_POST["tiendau"]},
                    rotativo={$_POST["rotativou"]}
                    where usuario_id = {$_POST["idCliente"]}";
        mysqli_query($this->conectar, $sql);
        return true;
    }

    public function borrar()
    {
        // Verificar que el usuario existe antes de intentar eliminarlo
        $sql = "SELECT id_rol FROM usuarios WHERE usuario_id = {$_POST["value"]}";
        $result = mysqli_query($this->conectar, $sql);
        $usuario = mysqli_fetch_assoc($result);
        
        // Si el usuario no existe, devolver un mensaje
        if (!$usuario) {
            return json_encode(['success' => false, 'message' => 'El usuario no existe o ya fue eliminado']);
        }
        
        // Si es el último usuario con rol ADMIN, no permitir eliminarlo
        if ($usuario['id_rol'] == 1) {
            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE id_rol = 1";
            $result = mysqli_query($this->conectar, $sql);
            $count = mysqli_fetch_assoc($result);
            
            if ($count['total'] <= 1) {
                return json_encode(['error' => 'No se puede eliminar el último usuario administrador']);
            }
        }
        
        $sql = "DELETE FROM usuarios WHERE usuario_id = {$_POST["value"]}";
        if (mysqli_query($this->conectar, $sql)) {
            return json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
        } else {
            return json_encode(['success' => false, 'error' => 'Error al eliminar el usuario: ' . mysqli_error($this->conectar)]);
        }
    }
    
    // Método para verificar si un usuario tiene permiso para acceder a un módulo
    public function verificarPermiso($usuario_id, $ruta_actual) {
        try {
            // Si es administrador, siempre tiene permiso
            $sql = "SELECT id_rol FROM usuarios WHERE usuario_id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $usuario = $result->fetch_assoc();
    
            if ($usuario['id_rol'] == 1) {
                return true;
            }
    
            // Verificar si la ruta está en los módulos permitidos
            $sql = "SELECT m.ruta 
                    FROM modulos m 
                    INNER JOIN rol_modulo rm ON m.modulo_id = rm.modulo_id 
                    INNER JOIN usuarios u ON rm.rol_id = u.id_rol 
                    WHERE u.usuario_id = ?";
                    
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            while ($row = $result->fetch_assoc()) {
                if (strpos($ruta_actual, $row['ruta']) !== false) {
                    return true;
                }
            }
            
            // Verificar si la ruta está en los submodulos permitidos
            $sql = "SELECT s.ruta 
                    FROM submodulos s 
                    INNER JOIN rol_submodulo rs ON s.submodulo_id = rs.submodulo_id 
                    INNER JOIN usuarios u ON rs.rol_id = u.id_rol 
                    WHERE u.usuario_id = ?";
                    
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $usuario_id);
            $stmt->execute();
            $result = $stmt->get_result();
    
            while ($row = $result->fetch_assoc()) {
                if (strpos($ruta_actual, $row['ruta']) !== false) {
                    return true;
                }
            }
    
            return false;
        } catch (Exception $e) {
            error_log("Error en verificarPermiso: " . $e->getMessage());
            // Si hay un error, permitir el acceso pero registrar el error
            return true;
        }
    }
    
    // Nuevos métodos para manejar submodulos
    
    public function getModulosYSubmodulos() {
        try {
            $sql = "SELECT 
                        m.*,
                        (SELECT COUNT(*) FROM submodulos WHERE modulo_id = m.modulo_id) as tiene_submodulos
                    FROM modulos m
                    ORDER BY m.modulo_id";
                    
            $result = mysqli_query($this->conectar, $sql);
            $modulos = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                // Si el módulo tiene submodulos, obtenerlos
                if ($row['tiene_submodulos'] > 0) {
                    $sql_sub = "SELECT * FROM submodulos WHERE modulo_id = {$row['modulo_id']} ORDER BY nombre";
                    $result_sub = mysqli_query($this->conectar, $sql_sub);
                    $submodulos = [];
                    
                    while ($sub = mysqli_fetch_assoc($result_sub)) {
                        $submodulos[] = $sub;
                    }
                    
                    $row['submodulos'] = $submodulos;
                } else {
                    $row['submodulos'] = [];
                }
                
                unset($row['tiene_submodulos']);
                $modulos[] = $row;
            }
            
            return json_encode($modulos);
        } catch (Exception $e) {
            return json_encode(['error' => $e->getMessage()]);
        }
    }
    
    public function getRolPermisos($rol_id) {
        try {
            // Obtener módulos permitidos
            $sql = "SELECT modulo_id FROM rol_modulo WHERE rol_id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $rol_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $modulos = [];
            while ($row = $result->fetch_assoc()) {
                $modulos[] = $row['modulo_id'];
            }
            
            // Obtener submodulos permitidos
            $sql = "SELECT submodulo_id FROM rol_submodulo WHERE rol_id = ?";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("i", $rol_id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $submodulos = [];
            while ($row = $result->fetch_assoc()) {
                $submodulos[] = $row['submodulo_id'];
            }
            
            return [
                'modulos' => $modulos,
                'submodulos' => $submodulos
            ];
        } catch (Exception $e) {
            error_log("Error en getRolPermisos: " . $e->getMessage());
            return [
                'modulos' => [],
                'submodulos' => []
            ];
        }
    }
}