<?php

class Usuario
{
    private $usuario_id;
    private $id_empresa;
    private $num_doc;
    private $usuario;
    private $clave;
    private $email;
    private $nombres;
    private $apellidos;
    private $token_reset;
    private $estado;
    private $sucursal;

    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    /**
     * Obtener la conexión a la base de datos
     * @return mysqli
     */
    public function getConexion()
    {
        return $this->conectar;
    }

    /**
     * @return mixed
     */
    public function getUsuarioId()
    {
        return $this->usuario_id;
    }

    /**
     * @param mixed $usuario_id
     */
    public function setUsuarioId($usuario_id)
    {
        $this->usuario_id = $usuario_id;
    }

    /**
     * @return mixed
     */
    public function getIdEmpresa()
    {
        return $this->id_empresa;
    }

    /**
     * @param mixed $id_empresa
     */
    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }

    /**
     * @return mixed
     */
    public function getNumDoc()
    {
        return $this->num_doc;
    }

    /**
     * @param mixed $num_doc
     */
    public function setNumDoc($num_doc)
    {
        $this->num_doc = $num_doc;
    }

    /**
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param mixed $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * @return mixed
     */
    public function getClave()
    {
        return $this->clave;
    }

    /**
     * @param mixed $clave
     */
    public function setClave($clave)
    {
        $this->clave = $clave;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getNombres()
    {
        return $this->nombres;
    }

    /**
     * @param mixed $nombres
     */
    public function setNombres($nombres)
    {
        $this->nombres = $nombres;
    }

    /**
     * @return mixed
     */
    public function getApellidos()
    {
        return $this->apellidos;
    }

    /**
     * @param mixed $apellidos
     */
    public function setApellidos($apellidos)
    {
        $this->apellidos = $apellidos;
    }

    /**
     * @return mixed
     */
    public function getTokenReset()
    {
        return $this->token_reset;
    }

    /**
     * @param mixed $token_reset
     */
    public function setTokenReset($token_reset)
    {
        $this->token_reset = $token_reset;
    }

    /**
     * @return mixed
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param mixed $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @return mixed
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * @param mixed $sucursal
     */
    public function setSucursal($sucursal)
    {
        $this->sucursal = $sucursal;
    }

    public function login()
    {
        $respuesta = ["res" => false];
        try {
            $sql = "select u.*, r.rol_id, r.nombre as rol_nombre, r.puede_eliminar, r.ver_precios
                from usuarios u
                inner join roles r on u.id_rol = r.rol_id
                where u.email=? or u.usuario=?";

            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("ss", $this->usuario, $this->usuario);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if ($row['clave'] == sha1($this->clave)) {
                    if ($row["estado"] == 1) {
                        // Verificar sucursal
                        if (
                            $row["id_rol"] != 1 && $row["rotativo"] == 0 &&
                            intval($row["sucursal"]) != intval($this->sucursal)
                        ) {
                            $respuesta['msg'] = "Sucursal incorrecta, su sucursal es {$row["sucursal"]}";
                            return $respuesta;
                        }

                        // Obtener datos de empresa
                        $sql = "select * from empresas where id_empresa = ?";
                        $stmt = $this->conectar->prepare($sql);
                        $stmt->bind_param("i", $row['id_empresa']);
                        $stmt->execute();
                        $empr = $stmt->get_result()->fetch_assoc();

                        // Crear token de sesión - Mantener nombres originales
                        $token_u = [
                            "usuario_fac" => $row['usuario_id'],
                            "usuario_id" => $row['usuario_id'], // Agregar también usuario_id para compatibilidad
                            'rol' => $row['rol_id'], // Mantener 'rol' en lugar de 'id_rol'
                            'id_rol' => $row['rol_id'], // Agregar también 'id_rol' para compatibilidad
                            'nombres' => $row['nombres'],
                            'apellidos' => $row['apellidos'],
                            'rol_nombre' => $row['rol_nombre'],
                            'puede_eliminar' => (bool) $row['puede_eliminar'],
                            'ver_precios' => (bool) $row['ver_precios'],
                            'foto_perfil' => $row['foto_perfil'], // Agregar foto de perfil a la sesión
                            'id_empresa' => $empr['id_empresa'],
                            'nombre_empresa' => $empr['razon_social'],
                            'logo_empresa' => $empr['logo'],
                            'sucursal' => intval($this->sucursal),
                            'ruc_empr' => $empr['ruc'],
                            // TIMESTAMPS PARA CONTROL DE SESIÓN
                            'login_time' => time(), // Hora de inicio de sesión (timeout absoluto de 12h)
                            'last_activity' => time() // Última actividad (timeout de inactividad de 2h)
                        ];

                        $_SESSION = $token_u;
                        $respuesta['res'] = true;
                        $respuesta['token'] = Tools::encryptText(json_encode($token_u));
                        $respuesta['ruta'] = "/";

                    } else {
                        $respuesta['msg'] = "Usuario Bloqueado";
                    }
                } else {
                    $respuesta['msg'] = "Contraseña incorrecta";
                }
            } else {
                $respuesta['msg'] = "Usuario no encontrado";
            }
        } catch (Exception $e) {
            error_log("Error en login: " . $e->getMessage());
            $respuesta['msg'] = "Error al iniciar sesión";
        }
        return $respuesta;
    }


    /**
     * Genera y guarda un código de verificación para recuperación de contraseña
     * @return array
     */
    public function generarCodigoRecuperacion()
    {
        $respuesta = ["res" => false];
        try {
            // Verificar que el email existe
            $sql = "SELECT usuario_id, nombres, email FROM usuarios WHERE email = ? AND estado = '1'";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("s", $this->email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                // Generar código de 6 dígitos
                $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                
                // Guardar código con timestamp (válido por 15 minutos)
                $token = $codigo . '|' . (time() + 900); // 900 segundos = 15 minutos
                
                $sql = "UPDATE usuarios SET token_reset = ? WHERE usuario_id = ?";
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("si", $token, $row['usuario_id']);
                
                if ($stmt->execute()) {
                    $respuesta['res'] = true;
                    $respuesta['codigo'] = $codigo;
                    $respuesta['nombres'] = $row['nombres'];
                    $respuesta['email'] = $row['email'];
                } else {
                    $respuesta['msg'] = "Error al generar el código";
                }
            } else {
                $respuesta['msg'] = "No se encontró un usuario activo con ese correo electrónico";
            }
        } catch (Exception $e) {
            error_log("Error en generarCodigoRecuperacion: " . $e->getMessage());
            $respuesta['msg'] = "Error al procesar la solicitud";
        }
        return $respuesta;
    }

    /**
     * Verifica el código y actualiza la contraseña
     * @param string $codigo
     * @param string $nuevaClave
     * @return array
     */
    public function resetearPassword($codigo, $nuevaClave)
    {
        $respuesta = ["res" => false];
        try {
            // Buscar usuario por email
            $sql = "SELECT usuario_id, token_reset FROM usuarios WHERE email = ? AND estado = '1'";
            $stmt = $this->conectar->prepare($sql);
            $stmt->bind_param("s", $this->email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if (empty($row['token_reset'])) {
                    $respuesta['msg'] = "No hay solicitud de recuperación activa";
                    return $respuesta;
                }

                // Separar código y timestamp
                $partes = explode('|', $row['token_reset']);
                if (count($partes) !== 2) {
                    $respuesta['msg'] = "Código de recuperación inválido";
                    return $respuesta;
                }

                $codigoGuardado = $partes[0];
                $expiracion = intval($partes[1]);

                // Verificar que el código coincida
                if ($codigo !== $codigoGuardado) {
                    $respuesta['msg'] = "Código de verificación incorrecto";
                    return $respuesta;
                }

                // Verificar que no haya expirado
                if (time() > $expiracion) {
                    $respuesta['msg'] = "El código ha expirado. Solicita uno nuevo";
                    return $respuesta;
                }

                // Actualizar contraseña y limpiar token
                $claveEncriptada = sha1($nuevaClave);
                $sql = "UPDATE usuarios SET clave = ?, token_reset = NULL WHERE usuario_id = ?";
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("si", $claveEncriptada, $row['usuario_id']);

                if ($stmt->execute()) {
                    $respuesta['res'] = true;
                    $respuesta['msg'] = "Contraseña actualizada correctamente";
                } else {
                    $respuesta['msg'] = "Error al actualizar la contraseña";
                }
            } else {
                $respuesta['msg'] = "Usuario no encontrado";
            }
        } catch (Exception $e) {
            error_log("Error en resetearPassword: " . $e->getMessage());
            $respuesta['msg'] = "Error al procesar la solicitud";
        }
        return $respuesta;
    }
}
