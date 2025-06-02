<?php

class RolesController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function render()
    {
        $sql = "SELECT rol_id, nombre FROM roles";
        $fila = mysqli_query($this->conectar, $sql);
        $respuesta = mysqli_fetch_all($fila, MYSQLI_ASSOC);
        return json_encode($respuesta);
    }

    public function getOne()
{
    $sql = "SELECT rol_id, nombre, ver_precios, puede_eliminar FROM roles WHERE rol_id = {$_POST['id']}";
    $fila = mysqli_query($this->conectar, $sql);
    $rol = mysqli_fetch_assoc($fila);
    
    // Obtener los módulos asignados a este rol
    $sql = "SELECT modulo_id FROM rol_modulo WHERE rol_id = {$_POST['id']}";
    $fila = mysqli_query($this->conectar, $sql);
    $modulos = [];
    while ($row = mysqli_fetch_assoc($fila)) {
        $modulos[] = $row['modulo_id'];
    }
    
    // Obtener los submodulos asignados a este rol
    $sql = "SELECT submodulo_id FROM rol_submodulo WHERE rol_id = {$_POST['id']}";
    $fila = mysqli_query($this->conectar, $sql);
    $submodulos = [];
    while ($row = mysqli_fetch_assoc($fila)) {
        $submodulos[] = $row['submodulo_id'];
    }
    
    $respuesta = [
        'rol' => $rol,
        'modulos' => $modulos,
        'submodulos' => $submodulos
    ];
    
    return json_encode($respuesta);
}

public function crear()
{
    // Validar que el nombre no esté vacío
    if (empty($_POST['nombre'])) {
        return json_encode(['error' => 'El nombre del rol es obligatorio']);
    }
    
    // Iniciar transacción
    mysqli_begin_transaction($this->conectar);
    
    try {
        // Obtener el siguiente ID disponible
        $sql = "SELECT MAX(rol_id) as max_id FROM roles";
        $result = mysqli_query($this->conectar, $sql);
        $row = mysqli_fetch_assoc($result);
        $next_id = ($row['max_id'] ?? 0) + 1;

        // Obtener los valores de los permisos
        $ver_precios = isset($_POST['ver_precios']) ? 1 : 0;
        $puede_eliminar = isset($_POST['puede_eliminar']) ? 1 : 0;

        // Insertar el nuevo rol con ID explícito
        $nombre = mysqli_real_escape_string($this->conectar, $_POST['nombre']);
        $sql = "INSERT INTO roles (rol_id, nombre, ver_precios, puede_eliminar) VALUES ($next_id, '$nombre', $ver_precios, $puede_eliminar)";
        
        if (!mysqli_query($this->conectar, $sql)) {
            throw new Exception("Error al crear el rol: " . mysqli_error($this->conectar));
        }
        
        $rol_id = mysqli_insert_id($this->conectar);
        
        // Asignar los módulos seleccionados
        if (isset($_POST['modulos']) && is_array($_POST['modulos'])) {
            foreach ($_POST['modulos'] as $modulo_id) {
                $modulo_id = (int)$modulo_id;
                $sql = "INSERT INTO rol_modulo (rol_id, modulo_id) VALUES ($rol_id, $modulo_id)";
                if (!mysqli_query($this->conectar, $sql)) {
                    throw new Exception("Error al asignar módulo: " . mysqli_error($this->conectar));
                }
            }
        }
        
        // Asignar los submodulos seleccionados
        if (isset($_POST['submodulos']) && is_array($_POST['submodulos'])) {
            foreach ($_POST['submodulos'] as $submodulo_id) {
                $submodulo_id = (int)$submodulo_id;
                $sql = "INSERT INTO rol_submodulo (rol_id, submodulo_id) VALUES ($rol_id, $submodulo_id)";
                if (!mysqli_query($this->conectar, $sql)) {
                    throw new Exception("Error al asignar submódulo: " . mysqli_error($this->conectar));
                }
            }
        }
        
        // Confirmar transacción
        mysqli_commit($this->conectar);
        return json_encode(['success' => true, 'message' => 'Rol creado correctamente']);
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($this->conectar);
        return json_encode(['error' => $e->getMessage()]);
    }
}

public function editar()
{
    // Validar que el nombre no esté vacío
    if (empty($_POST['nombre'])) {
        return json_encode(['error' => 'El nombre del rol es obligatorio']);
    }
    
    // Iniciar transacción
    mysqli_begin_transaction($this->conectar);
    
    try {
        $rol_id = (int)$_POST['rol_id'];
        $nombre = mysqli_real_escape_string($this->conectar, $_POST['nombre']);
        
        // Obtener los valores de los permisos
        $ver_precios = isset($_POST['ver_precios']) ? 1 : 0;
        $puede_eliminar = isset($_POST['puede_eliminar']) ? 1 : 0;

        // Actualizar el rol
        $sql = "UPDATE roles SET nombre = '$nombre', ver_precios = $ver_precios, puede_eliminar = $puede_eliminar WHERE rol_id = $rol_id";
        if (!mysqli_query($this->conectar, $sql)) {
            throw new Exception("Error al actualizar el rol: " . mysqli_error($this->conectar));
        }
        
        // Eliminar los módulos actuales
        $sql = "DELETE FROM rol_modulo WHERE rol_id = $rol_id";
        if (!mysqli_query($this->conectar, $sql)) {
            throw new Exception("Error al eliminar módulos: " . mysqli_error($this->conectar));
        }
        
        // Eliminar los submodulos actuales
        $sql = "DELETE FROM rol_submodulo WHERE rol_id = $rol_id";
        if (!mysqli_query($this->conectar, $sql)) {
            throw new Exception("Error al eliminar submódulos: " . mysqli_error($this->conectar));
        }
        
        // Asignar los módulos seleccionados
        if (isset($_POST['modulos']) && is_array($_POST['modulos'])) {
            foreach ($_POST['modulos'] as $modulo_id) {
                $modulo_id = (int)$modulo_id;
                $sql = "INSERT INTO rol_modulo (rol_id, modulo_id) VALUES ($rol_id, $modulo_id)";
                if (!mysqli_query($this->conectar, $sql)) {
                    throw new Exception("Error al asignar módulo: " . mysqli_error($this->conectar));
                }
            }
        }
        
        // Asignar los submodulos seleccionados
        if (isset($_POST['submodulos']) && is_array($_POST['submodulos'])) {
            foreach ($_POST['submodulos'] as $submodulo_id) {
                $submodulo_id = (int)$submodulo_id;
                $sql = "INSERT INTO rol_submodulo (rol_id, submodulo_id) VALUES ($rol_id, $submodulo_id)";
                if (!mysqli_query($this->conectar, $sql)) {
                    throw new Exception("Error al asignar submódulo: " . mysqli_error($this->conectar));
                }
            }
        }
        
        // Confirmar transacción
        mysqli_commit($this->conectar);
        return json_encode(['success' => true, 'message' => 'Rol actualizado correctamente']);
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        mysqli_rollback($this->conectar);
        return json_encode(['error' => $e->getMessage()]);
    }
}

    public function borrar()
    {
        $rol_id = (int)$_POST['id'];
        
        // Verificar que no sea el rol ADMIN (rol_id = 1)
        if ($rol_id === 1) {
            return json_encode(['error' => 'No se puede eliminar el rol de Administrador']);
        }
        
        // Verificar si hay usuarios con este rol
        $sql = "SELECT COUNT(*) as total FROM usuarios WHERE id_rol = $rol_id";
        $result = mysqli_query($this->conectar, $sql);
        $row = mysqli_fetch_assoc($result);
        
        if ($row['total'] > 0) {
            return json_encode(['error' => 'No se puede eliminar el rol porque hay usuarios asignados a él']);
        }
        
        // Iniciar transacción
        mysqli_begin_transaction($this->conectar);
        
        try {
            // Eliminar permisos de módulos
            $sql = "DELETE FROM rol_modulo WHERE rol_id = $rol_id";
            if (!mysqli_query($this->conectar, $sql)) {
                throw new Exception("Error al eliminar permisos de módulos: " . mysqli_error($this->conectar));
            }
            
            // Eliminar permisos de submodulos
            $sql = "DELETE FROM rol_submodulo WHERE rol_id = $rol_id";
            if (!mysqli_query($this->conectar, $sql)) {
                throw new Exception("Error al eliminar permisos de submódulos: " . mysqli_error($this->conectar));
            }
            
            // Eliminar el rol
            $sql = "DELETE FROM roles WHERE rol_id = $rol_id";
            if (!mysqli_query($this->conectar, $sql)) {
                throw new Exception("Error al eliminar el rol: " . mysqli_error($this->conectar));
            }
            
            // Confirmar transacción
            mysqli_commit($this->conectar);
            return json_encode(['success' => true, 'message' => 'Rol eliminado correctamente']);
        } catch (Exception $e) {
            // Revertir transacción en caso de error
            mysqli_rollback($this->conectar);
            return json_encode(['error' => $e->getMessage()]);
        }
    }
    
    public function getModulos()
    {
        $sql = "SELECT modulo_id, nombre, descripcion, icono FROM modulos ORDER BY nombre";
        $fila = mysqli_query($this->conectar, $sql);
        $respuesta = mysqli_fetch_all($fila, MYSQLI_ASSOC);
        return json_encode($respuesta);
    }
    
    public function getModulosYSubmodulos()
    {
        try {
            $sql = "SELECT 
                        m.*,
                        (SELECT COUNT(*) FROM submodulos WHERE modulo_id = m.modulo_id) as tiene_submodulos
                    FROM modulos m
                    ORDER BY m.modulo_id";
                    
            $result = mysqli_query($this->conectar, $sql);
            if (!$result) {
                throw new Exception("Error en consulta: " . mysqli_error($this->conectar));
            }
            
            $modulos = [];
            while ($row = mysqli_fetch_assoc($result)) {
                // Si el módulo tiene submodulos, obtenerlos
                if ($row['tiene_submodulos'] > 0) {
                    $sql_sub = "SELECT * FROM submodulos WHERE modulo_id = {$row['modulo_id']} ORDER BY nombre";
                    $result_sub = mysqli_query($this->conectar, $sql_sub);
                    if (!$result_sub) {
                        throw new Exception("Error en consulta de submodulos: " . mysqli_error($this->conectar));
                    }
                    
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
}

