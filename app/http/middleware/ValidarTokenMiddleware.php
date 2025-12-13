<?php

class ValidarTokenMiddleware extends Middleware
{

    public function valid()
    {
        $headers = apache_request_headers();

        if (isset($headers['token-app'])){
            $token = json_decode(Tools::decryptText($headers['token-app']),true);
            if ($token){
                // VALIDAR TIMEOUT ABSOLUTO (12 horas = 43200 segundos)
                $ahora = time();
                $sesionValida = true;

                $TIMEOUT_ABSOLUTO = 12 * 60 * 60; // 12 horas
                if (isset($token['login_time'])) {
                    $tiempoDesdeLogin = $ahora - $token['login_time'];
                    if ($tiempoDesdeLogin > $TIMEOUT_ABSOLUTO) {
                        $sesionValida = false;
                    }
                }

                // TIMEOUT POR INACTIVIDAD DESACTIVADO (solo validación de 12 horas)

                // Si la sesión es válida, actualizar última actividad y establecer sesión
                if ($sesionValida) {
                    $token['last_activity'] = $ahora;
                    $_SESSION = $token;
                } else {
                    // Sesión expirada, no establecer $_SESSION
                    // Esto causará que las rutas protegidas fallen
                    $_SESSION = [];
                }
            }
        }

    }
}