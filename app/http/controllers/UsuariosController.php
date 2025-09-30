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
                    END AS rotativo,
                    COALESCE(foto_perfil, 'public/assets/images/users/user-4.jpg') as foto_perfil
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
                    rotativo,
                    foto_perfil
                FROM
                    usuarios u
                where u.usuario_id = {$_POST["id"]}";
        $fila = mysqli_query($this->conectar, $sql);
        $respuesta = mysqli_fetch_all($fila, MYSQLI_ASSOC);
        return json_encode($respuesta);
    }

    public function insertar()
    {
        try {
            // Manejar la subida de foto
            $fotoPerfil = null;
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $fotoPerfil = $this->subirFoto($_FILES['foto_perfil']);
            }

            // Preparar datos del usuario
            $rol = mysqli_real_escape_string($this->conectar, $_POST['rol']);
            $nombres = mysqli_real_escape_string($this->conectar, $_POST['nombres']);
            $ndoc = mysqli_real_escape_string($this->conectar, $_POST['ndoc']);
            $usuario = mysqli_real_escape_string($this->conectar, $_POST['usuario']);
            $clave = sha1($_POST['clave']);
            $telefono = mysqli_real_escape_string($this->conectar, $_POST['telefono']);
            $email = mysqli_real_escape_string($this->conectar, $_POST['email']);
            $rotativo = isset($_POST['rotativo']) ? (int)$_POST['rotativo'] : 0;
            $idEmpresa = $_SESSION['id_empresa'];

            $sql = "INSERT INTO usuarios (
                        id_empresa, id_rol, num_doc, usuario, clave,
                        email, nombres, telefono, sucursal, rotativo, foto_perfil
                    ) VALUES (
                        '$idEmpresa', '$rol', '$ndoc', '$usuario', '$clave',
                        '$email', '$nombres', '$telefono', 1, '$rotativo', '$fotoPerfil'
                    )";

            if (mysqli_query($this->conectar, $sql)) {
                return json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
            } else {
                return json_encode(['success' => false, 'error' => 'Error al crear usuario: ' . mysqli_error($this->conectar)]);
            }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
        }
    }

    private function subirFoto($archivo)
    {
        $directorioDestino = 'public/uploads/usuarios/';

        // Crear directorio si no existe
        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        // Generar nombre único
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'usuario_' . time() . '_' . uniqid() . '.' . $extension;
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        // Validar tipo de archivo
        $tiposPermitidos = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array(strtolower($extension), $tiposPermitidos)) {
            throw new Exception('Tipo de archivo no permitido');
        }

        // Validar tamaño (2MB máximo)
        if ($archivo['size'] > 2 * 1024 * 1024) {
            throw new Exception('El archivo es muy grande. Máximo 2MB');
        }

        // Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return $rutaCompleta;
        } else {
            throw new Exception('Error al subir el archivo');
        }
    }

    public function editar()
    {
        try {
            $udp = "";
            if (isset($_POST["claveu"]) && !empty($_POST["claveu"])) {
                $clave = sha1($_POST["claveu"]);
                $udp = "clave='$clave',";
            }

            // Manejar actualización de foto
            $fotoUpdate = "";
            if (isset($_FILES['foto_perfil_edit']) && $_FILES['foto_perfil_edit']['error'] === UPLOAD_ERR_OK) {
                $fotoPerfil = $this->subirFoto($_FILES['foto_perfil_edit']);
                $fotoUpdate = "foto_perfil='$fotoPerfil',";
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
                        $fotoUpdate
                        telefono='{$_POST["telefonoEditar"]}',
                        email='{$_POST["emailEditar"]}',
                        sucursal=$tienda,
                        rotativo=$rotativo
                    WHERE usuario_id = {$_POST["idCliente"]}";

            if (mysqli_query($this->conectar, $sql)) {
                // Si se editó el usuario actual, actualizar la sesión
                if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $_POST["idCliente"]) {
                    // Obtener los datos actualizados del usuario
                    $sqlUsuario = "SELECT foto_perfil, nombres FROM usuarios WHERE usuario_id = {$_POST["idCliente"]}";
                    $resultUsuario = mysqli_query($this->conectar, $sqlUsuario);
                    if ($resultUsuario && $rowUsuario = mysqli_fetch_assoc($resultUsuario)) {
                        // Actualizar la sesión con los nuevos datos
                        $_SESSION['foto_perfil'] = $rowUsuario['foto_perfil'];
                        $_SESSION['nombres'] = $rowUsuario['nombres'];
                    }
                }

                return json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
            } else {
                return json_encode(['success' => false, 'error' => 'Error al actualizar: ' . mysqli_error($this->conectar)]);
            }
        } catch (Exception $e) {
            return json_encode(['success' => false, 'error' => 'Error: ' . $e->getMessage()]);
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
