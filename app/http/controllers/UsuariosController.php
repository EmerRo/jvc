<?php
require_once   'app/models/ModulosHelper.php';
require_once   'app/helpers/ImageStorage.php';

class UsuariosController extends Controller
{
    private $cliente;
    public $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * Genera un código único de 3 dígitos para el usuario
     * El código va desde 001 hasta 999
     */
    private function generarCodigoUnico()
    {
        // Obtener el último código usado
        $sql = "SELECT MAX(CAST(codigo AS UNSIGNED)) as ultimo_codigo FROM usuarios WHERE codigo IS NOT NULL";
        $result = mysqli_query($this->conectar, $sql);
        $row = mysqli_fetch_assoc($result);

        $ultimoCodigo = $row['ultimo_codigo'] ? (int)$row['ultimo_codigo'] : 0;
        $nuevoCodigo = $ultimoCodigo + 1;

        // Verificar que no exceda 999
        if ($nuevoCodigo > 999) {
            // Buscar un código disponible (por si hay huecos)
            $sql = "SELECT codigo FROM usuarios WHERE codigo IS NOT NULL ORDER BY CAST(codigo AS UNSIGNED)";
            $result = mysqli_query($this->conectar, $sql);
            $codigosUsados = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $codigosUsados[] = (int)$row['codigo'];
            }

            for ($i = 1; $i <= 999; $i++) {
                if (!in_array($i, $codigosUsados)) {
                    $nuevoCodigo = $i;
                    break;
                }
            }
        }

        // Formatear a 3 dígitos con ceros a la izquierda
        return str_pad($nuevoCodigo, 3, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        $sql = "SELECT
                    ROW_NUMBER() OVER (ORDER BY usuario_id) as item,
                    usuario_id,
                    u.codigo,
                    r.nombre,
                    usuario,
                    email,
                    nombres,
                    telefono,
                    u.sueldo,
                    CASE
                        WHEN sucursal = 1 THEN 'Tienda 435'
                        ELSE 'Tienda 426'
                    END AS tienda,
                    CASE
                        WHEN rotativo = 0 THEN 'No'
                        ELSE 'Si'
                    END AS rotativo,
                    COALESCE(foto_perfil, '" . DEFAULT_USER_AVATAR . "') as foto_perfil
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
                    codigo,
                    num_doc,
                    id_rol,
                    usuario,
                    email,
                    nombres,
                    telefono,
                    sucursal,
                    rotativo,
                    sueldo,
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
            // Preparar datos para validación
            $ndoc = mysqli_real_escape_string($this->conectar, $_POST['ndoc']);
            $email = mysqli_real_escape_string($this->conectar, $_POST['email']);
            $usuario = mysqli_real_escape_string($this->conectar, $_POST['usuario']);

            // Validar si el número de documento ya existe
            $sqlCheckDoc = "SELECT usuario_id FROM usuarios WHERE num_doc = '$ndoc'";
            $resultDoc = mysqli_query($this->conectar, $sqlCheckDoc);
            if (mysqli_num_rows($resultDoc) > 0) {
                return json_encode(['success' => false, 'error' => 'El número de documento ya está registrado']);
            }

            // Validar si el email ya existe
            $sqlCheckEmail = "SELECT usuario_id FROM usuarios WHERE email = '$email'";
            $resultEmail = mysqli_query($this->conectar, $sqlCheckEmail);
            if (mysqli_num_rows($resultEmail) > 0) {
                return json_encode(['success' => false, 'error' => 'El correo electrónico ya está registrado']);
            }

            // Validar si el nombre de usuario ya existe
            $sqlCheckUsuario = "SELECT usuario_id FROM usuarios WHERE usuario = '$usuario'";
            $resultUsuario = mysqli_query($this->conectar, $sqlCheckUsuario);
            if (mysqli_num_rows($resultUsuario) > 0) {
                return json_encode(['success' => false, 'error' => 'El nombre de usuario ya está registrado']);
            }

            // Manejar la subida de foto
            $fotoPerfil = null;
            if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
                $fotoPerfil = ImageStorage::save($_FILES['foto_perfil'], 'usuarios');
            }

            // Generar código único de 3 dígitos
            $codigo = $this->generarCodigoUnico();

            // Preparar datos del usuario
            $rol = mysqli_real_escape_string($this->conectar, $_POST['rol']);
            $nombres = mysqli_real_escape_string($this->conectar, $_POST['nombres']);
            $clave = sha1($_POST['clave']);
            $telefono = mysqli_real_escape_string($this->conectar, $_POST['telefono']);
            $rotativo = isset($_POST['rotativo']) ? (int)$_POST['rotativo'] : 0;
            $idEmpresa = $_SESSION['id_empresa'];

            $sueldo = isset($_POST['sueldo']) ? floatval($_POST['sueldo']) : 0;

            $sql = "INSERT INTO usuarios (
                        codigo, id_empresa, id_rol, num_doc, usuario, clave,
                        email, nombres, telefono, sucursal, rotativo, sueldo, foto_perfil
                    ) VALUES (
                        '$codigo', '$idEmpresa', '$rol', '$ndoc', '$usuario', '$clave',
                        '$email', '$nombres', '$telefono', 1, '$rotativo', '$sueldo', '$fotoPerfil'
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

    public function editar()
    {
        try {
            $idUsuario = intval($_POST["idCliente"]);
            $doc = mysqli_real_escape_string($this->conectar, $_POST["doc"]);
            $email = mysqli_real_escape_string($this->conectar, $_POST["emailEditar"]);
            $usuarioNombre = mysqli_real_escape_string($this->conectar, $_POST["usuariou"]);

            // Validar si el número de documento ya existe en OTRO usuario
            $sqlCheckDoc = "SELECT usuario_id FROM usuarios WHERE num_doc = '$doc' AND usuario_id != $idUsuario";
            $resultDoc = mysqli_query($this->conectar, $sqlCheckDoc);
            if (mysqli_num_rows($resultDoc) > 0) {
                return json_encode(['success' => false, 'error' => 'El número de documento ya está registrado en otro usuario']);
            }

            // Validar si el email ya existe en OTRO usuario
            $sqlCheckEmail = "SELECT usuario_id FROM usuarios WHERE email = '$email' AND usuario_id != $idUsuario";
            $resultEmail = mysqli_query($this->conectar, $sqlCheckEmail);
            if (mysqli_num_rows($resultEmail) > 0) {
                return json_encode(['success' => false, 'error' => 'El correo electrónico ya está registrado en otro usuario']);
            }

            // Validar si el nombre de usuario ya existe en OTRO usuario
            $sqlCheckUsuario = "SELECT usuario_id FROM usuarios WHERE usuario = '$usuarioNombre' AND usuario_id != $idUsuario";
            $resultUsuario = mysqli_query($this->conectar, $sqlCheckUsuario);
            if (mysqli_num_rows($resultUsuario) > 0) {
                return json_encode(['success' => false, 'error' => 'El nombre de usuario ya está registrado en otro usuario']);
            }

            $udp = "";
            if (isset($_POST["claveu"]) && !empty($_POST["claveu"])) {
                $clave = sha1($_POST["claveu"]);
                $udp = "clave='$clave',";
            }

            // Manejar actualización de foto
            $fotoUpdate = "";
            if (isset($_FILES['foto_perfil_edit']) && $_FILES['foto_perfil_edit']['error'] === UPLOAD_ERR_OK) {
                $fotoPerfil = ImageStorage::save($_FILES['foto_perfil_edit'], 'usuarios');
                $fotoUpdate = "foto_perfil='$fotoPerfil',";
            }

            // Valores por defecto para campos opcionales
            $tienda = isset($_POST["tiendau"]) ? $_POST["tiendau"] : 1;
            $rotativo = isset($_POST["rotativou"]) ? $_POST["rotativou"] : 0;

            $sueldo = isset($_POST['sueldou']) ? floatval($_POST['sueldou']) : 0;

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
                        rotativo=$rotativo,
                        sueldo=$sueldo
                    WHERE usuario_id = {$_POST["idCliente"]}";

            if (mysqli_query($this->conectar, $sql)) {
                $response = ['success' => true, 'message' => 'Usuario actualizado correctamente'];

                // Si se editó el usuario actual, actualizar la sesión Y generar nuevo token
                if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $_POST["idCliente"]) {
                    // Obtener los datos actualizados del usuario
                    $sqlUsuario = "SELECT foto_perfil, nombres FROM usuarios WHERE usuario_id = {$_POST["idCliente"]}";
                    $resultUsuario = mysqli_query($this->conectar, $sqlUsuario);
                    if ($resultUsuario && $rowUsuario = mysqli_fetch_assoc($resultUsuario)) {
                        // Actualizar la sesión con los nuevos datos
                        $_SESSION['foto_perfil'] = $rowUsuario['foto_perfil'];
                        $_SESSION['nombres'] = $rowUsuario['nombres'];

                        // Generar nuevo token con los datos actualizados para el cliente
                        $response['new_token'] = Tools::encryptText(json_encode($_SESSION));
                        $response['foto_perfil'] = $rowUsuario['foto_perfil'];
                    }
                }

                return json_encode($response);
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
