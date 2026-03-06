<?php

require_once "app/models/Usuario.php";

class UsuarioController extends Controller
{
    private $usuario;

    public function __construct(){
        $this->usuario = new Usuario();
    }

    public function login(){
        $this->usuario->setUsuario($_POST['user']);
        $this->usuario->setClave($_POST['clave']);
        $this->usuario->setSucursal($_POST['sucursal']);
        return json_encode($this->usuario->login());
    }

    public function logout(){
        session_destroy();
        header("Location: ".URL::to("/login"));
    }

    /**
     * Envía código de verificación al email del usuario
     */
    public function enviarCodigoRecuperacion(){
        $respuesta = ["res" => false];
        
        try {
            if (!isset($_POST['email']) || empty(trim($_POST['email']))) {
                $respuesta['msg'] = "El correo electrónico es requerido";
                return json_encode($respuesta);
            }

            $email = trim($_POST['email']);
            $this->usuario->setEmail($email);
            
            $resultado = $this->usuario->generarCodigoRecuperacion();
            
            if ($resultado['res']) {
                // Enviar email con el código
                require_once "app/clases/EnvioEmail.php";
                
                $asunto = "Código de Recuperación de Contraseña - JVC";
                $mensaje = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .header { background-color: #C1272D; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
                            .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; }
                            .code { font-size: 32px; font-weight: bold; color: #C1272D; text-align: center; padding: 20px; background: white; border: 2px dashed #C1272D; border-radius: 5px; margin: 20px 0; letter-spacing: 5px; }
                            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
                            .warning { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin: 15px 0; }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <div class='header'>
                                <h1>Recuperación de Contraseña</h1>
                            </div>
                            <div class='content'>
                                <p>Hola <strong>{$resultado['nombres']}</strong>,</p>
                                <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en el sistema de Facturación Electrónica JVC.</p>
                                <p>Tu código de verificación es:</p>
                                <div class='code'>{$resultado['codigo']}</div>
                                <div class='warning'>
                                    <strong>⚠️ Importante:</strong> Este código es válido por <strong>15 minutos</strong>.
                                </div>
                                <p>Si no solicitaste este cambio, puedes ignorar este correo. Tu contraseña permanecerá sin cambios.</p>
                            </div>
                            <div class='footer'>
                                <p>Este es un correo automático, por favor no responder.</p>
                                <p>&copy; " . date('Y') . " JVC - Facturación Electrónica</p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";
                
                try {
                    $envioEmail = new EnvioEmail();
                    $emailEnviado = $envioEmail
                        ->de(USER_SMTP, "JVC - Facturación Electrónica")
                        ->addEmail($email, $resultado['nombres'])
                        ->setasunto($asunto)
                        ->cuerpo($mensaje)
                        ->enviar();
                    
                    if ($emailEnviado) {
                        $respuesta['res'] = true;
                        $respuesta['msg'] = "Código enviado correctamente";
                    } else {
                        $respuesta['msg'] = "Error al enviar el correo electrónico";
                        error_log("Error PHPMailer: " . $envioEmail->error());
                    }
                } catch (Exception $e) {
                    error_log("Error al enviar email: " . $e->getMessage());
                    $respuesta['msg'] = "Error al enviar el correo electrónico";
                }
            } else {
                $respuesta['msg'] = $resultado['msg'];
            }
            
        } catch (Exception $e) {
            error_log("Error en enviarCodigoRecuperacion: " . $e->getMessage());
            $respuesta['msg'] = "Error al procesar la solicitud";
        }
        
        return json_encode($respuesta);
    }

    /**
     * Verifica el código sin cambiar la contraseña
     */
    public function verificarCodigo(){
        $respuesta = ["res" => false];
        
        try {
            if (!isset($_POST['email']) || !isset($_POST['code'])) {
                $respuesta['msg'] = "Datos incompletos";
                return json_encode($respuesta);
            }

            $email = trim($_POST['email']);
            $codigo = trim($_POST['code']);

            if (empty($email) || empty($codigo)) {
                $respuesta['msg'] = "Todos los campos son requeridos";
                return json_encode($respuesta);
            }

            if (strlen($codigo) !== 6) {
                $respuesta['msg'] = "El código debe tener 6 dígitos";
                return json_encode($respuesta);
            }

            // Obtener conexión del modelo Usuario
            $conexion = $this->usuario->getConexion();
            
            // Buscar usuario por email
            $sql = "SELECT usuario_id, token_reset FROM usuarios WHERE email = ? AND estado = '1'";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                if (empty($row['token_reset'])) {
                    $respuesta['msg'] = "No hay solicitud de recuperación activa";
                    return json_encode($respuesta);
                }

                // Separar código y timestamp
                $partes = explode('|', $row['token_reset']);
                if (count($partes) !== 2) {
                    $respuesta['msg'] = "Código de recuperación inválido";
                    return json_encode($respuesta);
                }

                $codigoGuardado = $partes[0];
                $expiracion = intval($partes[1]);

                // Verificar que el código coincida
                if ($codigo !== $codigoGuardado) {
                    $respuesta['msg'] = "Código de verificación incorrecto";
                    return json_encode($respuesta);
                }

                // Verificar que no haya expirado
                if (time() > $expiracion) {
                    $respuesta['msg'] = "El código ha expirado. Solicita uno nuevo";
                    return json_encode($respuesta);
                }

                // Código válido
                $respuesta['res'] = true;
                $respuesta['msg'] = "Código verificado correctamente";
            } else {
                $respuesta['msg'] = "Usuario no encontrado";
            }
            
        } catch (Exception $e) {
            error_log("Error en verificarCodigo: " . $e->getMessage());
            $respuesta['msg'] = "Error al procesar la solicitud";
        }
        
        return json_encode($respuesta);
    }

    /**
     * Resetea la contraseña del usuario
     */
    public function resetearPassword(){
        $respuesta = ["res" => false];
        
        try {
            if (!isset($_POST['email']) || !isset($_POST['code']) || !isset($_POST['new_password'])) {
                $respuesta['msg'] = "Datos incompletos";
                return json_encode($respuesta);
            }

            $email = trim($_POST['email']);
            $codigo = trim($_POST['code']);
            $nuevaPassword = $_POST['new_password'];

            if (empty($email) || empty($codigo) || empty($nuevaPassword)) {
                $respuesta['msg'] = "Todos los campos son requeridos";
                return json_encode($respuesta);
            }

            if (strlen($nuevaPassword) < 4) {
                $respuesta['msg'] = "La contraseña debe tener al menos 4 caracteres";
                return json_encode($respuesta);
            }

            $this->usuario->setEmail($email);
            $resultado = $this->usuario->resetearPassword($codigo, $nuevaPassword);
            
            $respuesta = $resultado;
            
        } catch (Exception $e) {
            error_log("Error en resetearPassword: " . $e->getMessage());
            $respuesta['msg'] = "Error al procesar la solicitud";
        }
        
        return json_encode($respuesta);
    }

}