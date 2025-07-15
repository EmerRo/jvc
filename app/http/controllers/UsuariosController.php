<?php
require_once   'app/models/ModulosHelper.php';

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
                    ROW_NUMBER() OVER (ORDER BY usuario_id) as item,
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
                INNER JOIN roles r ON r.rol_id = u.id_rol
                ORDER BY usuario_id";
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
        if (isset($_POST["claveu"]) && !empty($_POST["claveu"])) {
            $clave = sha1($_POST["claveu"]);
            $udp = "clave='$clave',";
        }
        
        // Valores por defecto para campos opcionales
        $tienda = isset($_POST["tiendau"]) ? $_POST["tiendau"] : 1;
        $rotativo = isset($_POST["rotativou"]) ? $_POST["rotativou"] : 0;
        
        $sql = "UPDATE usuarios SET 
                    id_rol='{$_POST["rol"]}',
                    nombres='{$_POST["datosEditar"]}',
                    num_doc='{$_POST["doc"]}',
                    usuario='{$_POST["usuariou"]}',
                    $udp
                    telefono='{$_POST["telefonoEditar"]}',
                    email='{$_POST["emailEditar"]}',
                    sucursal=$tienda,
                    rotativo=$rotativo
                WHERE usuario_id = {$_POST["idCliente"]}";
        
        if (mysqli_query($this->conectar, $sql)) {
            return json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
        } else {
            return json_encode(['success' => false, 'error' => 'Error al actualizar: ' . mysqli_error($this->conectar)]);
        }
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
    
    // Nuevos métodos para manejar submodulos
    
    public function getModulosYSubmodulos() {
        // Obtener todos los módulos desde el helper
        $modulos = ModulosHelper::obtenerTodosLosModulos();
        return json_encode($modulos);
    }
    
   public function getRolPermisos() {
    $rol_id = isset($_POST['id']) ? $_POST['id'] : null;
    
    if (!$rol_id) {
        return json_encode(['error' => 'ID de rol no proporcionado']);
    }
    
    try {
        // Obtener información del rol
        $sql = "SELECT * FROM roles WHERE rol_id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $rol_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $rol = $result->fetch_assoc();
        
        if (!$rol) {
            return json_encode(['error' => 'Rol no encontrado']);
        }
        
        // Obtener permisos del rol
        $sql = "SELECT modulo_id, submodulo_id FROM rol_permisos WHERE rol_id = ?";
        $stmt = $this->conectar->prepare($sql);
        $stmt->bind_param("i", $rol_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $modulos = [];
        $submodulos = [];
        while ($row = $result->fetch_assoc()) {
            if ($row['submodulo_id'] === null) {
                $modulos[] = $row['modulo_id'];
            } else {
                // CAMBIO AQUÍ: Formatear submódulos como "modulo_id|submodulo_id"
                $submodulos[] = $row['modulo_id'] . '|' . $row['submodulo_id'];
            }
        }
        
        return json_encode([
            'rol' => $rol,
            'modulos' => $modulos,
            'submodulos' => $submodulos
        ]);
    } catch (Exception $e) {
        error_log("Error en getRolPermisos: " . $e->getMessage());
        return json_encode(['error' => $e->getMessage()]);
    }
}

    // Método para verificar si un usuario tiene permiso para acceder a un módulo
    public function verificarPermiso($usuario_id = null, $ruta_actual = null) {
        // Si se llama desde AJAX
        if (isset($_POST['usuario_id']) && isset($_POST['ruta'])) {
            $usuario_id = $_POST['usuario_id'];
            $ruta_actual = $_POST['ruta'];
        }
        
        $tienePermiso = ModulosHelper::verificarPermiso($usuario_id, $ruta_actual, $this->conectar);
        return json_encode(['permiso' => $tienePermiso]);
    }
}
