<!DOCTYPE html>
<html lang="es">

<head>
    <title>JVC - Facturación Electrónica</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="<?= URL::to('public/login/images/icons/favicon.ico') ?>" />
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/fonts/font-awesome-4.7.0/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/fonts/iconic/css/material-design-iconic-font.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/vendor/animate/animate.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/vendor/css-hamburgers/hamburgers.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/vendor/animsition/css/animsition.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/vendor/select2/select2.min.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/vendor/daterangepicker/daterangepicker.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/css/util.css') ?>">
    <link rel="stylesheet" type="text/css" href="<?= URL::to('public/login/css/main.css') ?>">
    <link rel="stylesheet" href="<?= URL::to('public/plugin/sweetalert2/sweetalert2.min.css') ?>">

    <meta name="robots" content="noindex, follow">
    <script>
        const _URL = '<?= URL::base() ?>';
    </script>
    <style>
        /* Estilos para los loaders originales */
        @keyframes ldio-407auvblvok {
            0% { transform: rotate(0) }
            100% { transform: rotate(360deg) }
        }

        .ldio-407auvblvok div {
            box-sizing: border-box !important
        }

        .ldio-407auvblvok>div {
            position: absolute;
            width: 79.92px;
            height: 79.92px;
            top: 15.540000000000001px;
            left: 15.540000000000001px;
            border-radius: 50%;
            border: 8.88px solid #000;
            border-color: #626ed4 transparent #626ed4 transparent;
            animation: ldio-407auvblvok 1s linear infinite;
        }

        .ldio-407auvblvok>div:nth-child(2),
        .ldio-407auvblvok>div:nth-child(4) {
            width: 59.940000000000005px;
            height: 59.940000000000005px;
            top: 25.53px;
            left: 25.53px;
            animation: ldio-407auvblvok 1s linear infinite reverse;
        }

        .ldio-407auvblvok>div:nth-child(2) {
            border-color: transparent #02a499 transparent #02a499
        }

        .ldio-407auvblvok>div:nth-child(3) {
            border-color: transparent
        }

        .ldio-407auvblvok>div:nth-child(3) div {
            position: absolute;
            width: 100%;
            height: 100%;
            transform: rotate(45deg);
        }

        .ldio-407auvblvok>div:nth-child(3) div:before,
        .ldio-407auvblvok>div:nth-child(3) div:after {
            content: "";
            display: block;
            position: absolute;
            width: 8.88px;
            height: 8.88px;
            top: -8.88px;
            left: 26.64px;
            background: #626ed4;
            border-radius: 50%;
            box-shadow: 0 71.04px 0 0 #626ed4;
        }

        .ldio-407auvblvok>div:nth-child(3) div:after {
            left: -8.88px;
            top: 26.64px;
            box-shadow: 71.04px 0 0 0 #626ed4;
        }

        .ldio-407auvblvok>div:nth-child(4) {
            border-color: transparent;
        }

        .ldio-407auvblvok>div:nth-child(4) div {
            position: absolute;
            width: 100%;
            height: 100%;
            transform: rotate(45deg);
        }

        .ldio-407auvblvok>div:nth-child(4) div:before,
        .ldio-407auvblvok>div:nth-child(4) div:after {
            content: "";
            display: block;
            position: absolute;
            width: 8.88px;
            height: 8.88px;
            top: -8.88px;
            left: 16.650000000000002px;
            background: #02a499;
            border-radius: 50%;
            box-shadow: 0 51.06px 0 0 #02a499;
        }

        .ldio-407auvblvok>div:nth-child(4) div:after {
            left: -8.88px;
            top: 16.650000000000002px;
            box-shadow: 51.06px 0 0 0 #02a499;
        }

        .loadingio-spinner-double-ring-8kmkrab6ncg {
            width: 111px;
            height: 111px;
            display: inline-block;
            overflow: hidden;
            background: rgba(255, 255, 255, 0);
        }

        .ldio-407auvblvok {
            width: 100%;
            height: 100%;
            position: relative;
            transform: translateZ(0) scale(1);
            backface-visibility: hidden;
            transform-origin: 0 0;
        }

        .ldio-407auvblvok div {
            box-sizing: content-box;
        }

        #loader-menor {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            width: 100%;
            height: 100%;
            display: none;
            background-color: #ffffff96;
            line-height: 100vh;
            text-align: center;
        }

        #loader-init {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            width: 100%;
            height: 100%;
            background-color: #ffffff;
            line-height: 100vh;
            text-align: center;
        }

        /* Estilos para el nuevo loader con barrido - Versión 3 */
        .loader-barrido {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            overflow: hidden;
        }

        .loader-contenedor-barrido {
            position: relative;
            width: 250px;
            height: 200px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .loader-logo-contenedor {
            position: relative;
            width: 150px;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin-bottom: 25px;
        }

        .loader-logo-barrido {
            width: 120px;
            height: auto;
            position: relative;
            z-index: 2;
            opacity: 0;
            transform: scale(0.9);
            animation: aparecerLogo 0.8s ease-out 0.6s forwards;
        }

        .barrido-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #C1272D;
            transform: translateX(-100%);
            z-index: 1;
        }

        .barrido-overlay-1 {
            animation: barridoHorizontal 1.2s cubic-bezier(0.65, 0, 0.35, 1) forwards;
        }

        .barrido-overlay-2 {
            background-color:rgb(154, 154, 155);
            animation: barridoHorizontal 1.2s cubic-bezier(0.65, 0, 0.35, 1) 0.2s forwards;
        }

        .lineas-decorativas {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
        }

        .linea {
            position: absolute;
            background-color: #C1272D;
            opacity: 0;
        }

        .linea-h-top {
            top: 0;
            left: 0;
            width: 0;
            height: 3px;
            animation: expandirLineaH 0.6s ease-out 1.4s forwards;
        }

        .linea-h-bottom {
            bottom: 0;
            right: 0;
            width: 0;
            height: 3px;
            animation: expandirLineaH 0.6s ease-out 1.4s forwards;
        }

        .linea-v-left {
            top: 0;
            left: 0;
            width: 3px;
            height: 0;
            animation: expandirLineaV 0.6s ease-out 1.4s forwards;
        }

        .linea-v-right {
            top: 0;
            right: 0;
            width: 3px;
            height: 0;
            animation: expandirLineaV 0.6s ease-out 1.4s forwards;
        }

        .loader-texto-barrido {
            color: #58595B;
            font-size: 16px;
            font-weight: 500;
            letter-spacing: 0.5px;
            opacity: 0;
            animation: aparecerTexto 0.8s ease-out 1.8s forwards;
            position: relative;
            z-index: 2;
        }

        @keyframes barridoHorizontal {
            0% {
                transform: translateX(-100%);
            }
            50% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(100%);
            }
        }

        @keyframes aparecerLogo {
            0% {
                opacity: 0;
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes expandirLineaH {
            0% {
                width: 0;
                opacity: 0;
            }
            100% {
                width: 100%;
                opacity: 1;
            }
        }

        @keyframes expandirLineaV {
            0% {
                height: 0;
                opacity: 0;
            }
            100% {
                height: 100%;
                opacity: 1;
            }
        }

        @keyframes aparecerTexto {
            0% {
                opacity: 0;
                transform: translateY(10px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Estilos para el login */
        .container-login100 {
            background-image: url('<?= URL::to("public/img/fondo.jpg") ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
            overflow: hidden;
        }

        .container-login100::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.3);
            z-index: 0;
        }

        .wrap-login100 {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
            animation: appearForm 1s ease-out forwards;
            opacity: 0;
            transform: translateY(20px);
        }

        @keyframes appearForm {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login100-form-btn {
            background-color: #C1272D !important;
            transition: all 0.3s ease;
        }

        .login100-form-btn:hover {
            background-color: #9a1f24 !important;
            box-shadow: 0 5px 15px rgba(193, 39, 45, 0.3);
        }

        .login100-form-bgbtn {
            color:rgb(121, 121, 121);

        }

        .input100:focus+.focus-input100::before {
            color: #C1272D;
        }

        .input100:focus+.focus-input100::after {
            background-color: #C1272D;
        }

        .label-input100 {
            color:rgb(121, 121, 121);
            font-weight: 600;
        }

        a {
            color: #C1272D;
        }

        a:hover {
            color: #9a1f24;
            text-decoration: none;
        }

        /* Estilo para el botón de mostrar contraseña */
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color:rgb(121, 121, 121);

            z-index: 10;
        }

        .password-toggle:hover {
            color: #C1272D;
        }

        .wrap-input100 {
            position: relative;
        }

        /* Rediseño de inputs del login */
        .login100-form .wrap-input100 {
            border-bottom: none;
            background: #f5f5f5;
            border-radius: 8px;
            padding: 0;
            transition: all 0.3s ease;
        }

        .login100-form .wrap-input100:focus-within {
            background: #fff;
            box-shadow: 0 0 0 2px #C1272D;
        }

        .login100-form .input100 {
            height: 50px;
            padding: 0 45px 0 48px;
            font-size: 14px;
            background: transparent;
            border: none;
            outline: none;
        }

        .login100-form .input100::placeholder {
            color: #999;
            font-size: 14px;
        }

        .login100-form .focus-input100 {
            display: none;
        }

        .login100-form .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
            z-index: 2;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .login100-form .wrap-input100:focus-within .input-icon {
            color: #C1272D;
        }

        .login100-form .password-toggle {
            right: 15px;
            color: #999;
            font-size: 16px;
        }

        .login100-form .password-toggle:hover {
            color: #C1272D;
        }

        /* Ocultar validación con cuadrado rojo */
        .login100-form .alert-validate::before {
            display: none;
        }

        .login100-form .alert-validate::after {
            display: none;
        }
    </style>
</head>

<body>
    <div id="loader-init">
        <div class="loadingio-spinner-double-ring-8kmkrab6ncg">
            <div class="ldio-407auvblvok">
                <div></div>
                <div></div>
                <div>
                    <div></div>
                </div>
                <div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="display: none" id="loader-menor">
        <div class="loadingio-spinner-double-ring-8kmkrab6ncg">
            <div class="ldio-407auvblvok">
                <div></div>
                <div></div>
                <div>
                    <div></div>
                </div>
                <div>
                    <div></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Nuevo loader con barrido - Versión 3 -->
    <div id="loader-barrido" class="loader-barrido">
        <div class="loader-contenedor-barrido">
            <div class="loader-logo-contenedor">
                <div class="lineas-decorativas">
                    <div class="linea linea-h-top"></div>
                    <div class="linea linea-h-bottom"></div>
                    <div class="linea linea-v-left"></div>
                    <div class="linea linea-v-right"></div>
                </div>
                <div class="barrido-overlay barrido-overlay-1"></div>
                <div class="barrido-overlay barrido-overlay-2"></div>
                <img src="<?= URL::to('public/login/images/logoJVC.png') ?>" alt="JVC Logo" class="loader-logo-barrido">
            </div>
            <div class="loader-texto-barrido">Facturación Electrónica JVC</div>
        </div>
    </div>

    <div class="limiter">
        <div class="container-login100">
            
            <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
                <form class="login100-form validate-form">
                    <span class="login100-form-title p-b-49">
                        <img src="<?= URL::to('public/login/images/JVC.png') ?>" style="max-width: 235px;">
                    </span>
                    <div class="wrap-input100 validate-input m-b-23"
                        data-validate="Se requiere usuario o correo electrónico">
                        <span class="input-icon"><i class="fa fa-user"></i></span>
                        <input class="input100" type="text" required name="user"
                            placeholder="Usuario o correo electrónico">
                    </div>
                    <div class="wrap-input100 validate-input" data-validate="Se requere contraseña">
                        <span class="input-icon"><i class="fa fa-lock"></i></span>
                        <input class="input100" type="password" required name="clave" id="password-field"
                            placeholder="Contraseña">
                        <i class="fa fa-eye password-toggle" id="toggle-password"></i>
                    </div>
                    <div class="wrap-input100" data-validate="" hidden>
                        <span class="label-input100">Sucursal</span>
                        <select class="input100" name="sucursal" id="sucursal" required>
                            <option value="1">Tienda 435</option>
                            <option value="2">Tienda 426</option>
                        </select>
                        <span class="focus-input100" data-symbol="&#xf190;"></span>
                    </div>
                    <div class="text-right p-t-8 p-b-31">
                        <a href="#" id="forgot-password-link">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                    <div class="container-login100-form-btn">
                        <div class="wrap-login100-form-btn">
                            <div class="login100-form-bgbtn"></div>
                            <button type="submit" class="login100-form-btn">
                                Ingresar
                            </button>
                        </div>
                    </div>
                    <div class="txt1 text-center p-t-54 p-b-20">
                        <span>
                            Desarrollado por:<br>
                        </span>
                        <a href="https://magustechnologies.com/" target="_blank"><img class="magus"
                                src="<?= URL::to('public/login/images/magus.png') ?>" style="max-width: 150px"></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div id="dropDownSelect1"></div>

    <!-- Modal Recuperar Contraseña -->
    <div id="modal-recuperar-password" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 10px; padding: 30px; max-width: 450px; width: 90%; position: relative; box-shadow: 0 5px 20px rgba(0,0,0,0.3);">
            <button id="close-modal" style="position: absolute; top: 15px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: #999;">&times;</button>
            
            <!-- Paso 1: Ingresar Email -->
            <div id="step-email" class="recovery-step">
                <h3 style="text-align: center; color: #C1272D; margin-bottom: 10px;">Recuperar Contraseña</h3>
                <p style="text-align: center; color: #666; margin-bottom: 25px; font-size: 14px;">Introduce tu correo electrónico para recibir un código de verificación</p>
                <div class="wrap-input100 validate-input m-b-23">
                    <span class="label-input100">Correo Electrónico</span>
                    <input class="input100" type="email" id="recovery-email" placeholder="ejemplo@correo.com" required>
                    <span class="focus-input100" data-symbol="&#xf0e0;"></span>
                </div>
                <button id="btn-send-code" class="login100-form-btn" style="width: 100%; margin-top: 10px;">
                    Enviar Código
                </button>
            </div>

            <!-- Paso 2: Verificar Código -->
            <div id="step-verify-code" class="recovery-step" style="display: none;">
                <h3 style="text-align: center; color: #C1272D; margin-bottom: 10px;">Verificar Código</h3>
                <p style="text-align: center; color: #666; margin-bottom: 25px; font-size: 14px;">Ingresa el código de 6 dígitos que recibiste en tu correo</p>
                
                <div class="wrap-input100 validate-input m-b-23">
                    <span class="label-input100">Código de Verificación</span>
                    <input class="input100" type="text" id="verification-code" placeholder="Ingresa el código de 6 dígitos" maxlength="6" required>
                    <span class="focus-input100" data-symbol="&#xf084;"></span>
                </div>

                <button id="btn-verify-code" class="login100-form-btn" style="width: 100%; margin-top: 10px;">
                    Verificar Código
                </button>
                <button id="btn-back-email" style="width: 100%; margin-top: 10px; background: #6c757d; border: none; padding: 12px; color: white; border-radius: 5px; cursor: pointer;">
                    Volver
                </button>
            </div>

            <!-- Paso 3: Ingresar Nueva Contraseña -->
            <div id="step-new-password" class="recovery-step" style="display: none;">
                <h3 style="text-align: center; color: #C1272D; margin-bottom: 10px;">Nueva Contraseña</h3>
                <p style="text-align: center; color: #666; margin-bottom: 25px; font-size: 14px;">
                    <i class="fa fa-check-circle" style="color: #28a745;"></i> Código verificado correctamente
                </p>

                <div class="wrap-input100 validate-input m-b-23">
                    <span class="label-input100">Nueva Contraseña</span>
                    <input class="input100" type="password" id="new-password" placeholder="Ingresa tu nueva contraseña" required>
                    <span class="focus-input100" data-symbol="&#xf190;"></span>
                    <i class="fa fa-eye password-toggle" id="toggle-new-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: rgb(121, 121, 121); z-index: 10;"></i>
                </div>

                <div class="wrap-input100 validate-input">
                    <span class="label-input100">Confirmar Contraseña</span>
                    <input class="input100" type="password" id="confirm-password" placeholder="Confirma tu nueva contraseña" required>
                    <span class="focus-input100" data-symbol="&#xf190;"></span>
                    <i class="fa fa-eye password-toggle" id="toggle-confirm-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer; color: rgb(121, 121, 121); z-index: 10;"></i>
                </div>

                <button id="btn-reset-password" class="login100-form-btn" style="width: 100%; margin-top: 20px;">
                    Cambiar Contraseña
                </button>
            </div>
        </div>
    </div>

    <style>
        /* Ajustar z-index de SweetAlert2 para que aparezca sobre el modal */
        .swal2-container {
            z-index: 20000 !important;
        }
    </style>

    <script src="<?= URL::to('public/login/vendor/jquery/jquery-3.2.1.min.js') ?>"></script>
    <script src="<?= URL::to('public/login/vendor/animsition/js/animsition.min.js') ?>"></script>
    <script src="<?= URL::to('public/login/vendor/bootstrap/js/popper.js') ?>"></script>
    <script src="<?= URL::to('public/login/vendor/bootstrap/js/bootstrap.min.js') ?>"></script>
    <script src="<?= URL::to('public/login/vendor/select2/select2.min.js') ?>"></script>
    <script src="<?= URL::to('public/login/vendor/daterangepicker/moment.min.js') ?>"></script>
    <script src="<?= URL::to('public/login/vendor/daterangepicker/daterangepicker.js') ?>"></script>
    <script src="<?= URL::to('public/login/vendor/countdowntime/countdowntime.js') ?>"></script>
    <script src="<?= URL::to('public/login/js/main.js?v=2') ?>"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <script>
        (function verificador() {
            if (localStorage.getItem("_token")) {
                $.ajax({
                    url: _URL + "/ajs/verificador/token",
                    type: "POST",
                    data: {
                        token: localStorage.getItem("_token"),
                        s: false
                    },
                    success(resp) {
                        // console.log(resp);
                        resp = JSON.parse(resp);
                        if (resp.res) {
                            // Actualizar token con última actividad
                            if (resp.token) {
                                localStorage.setItem("_token", resp.token);
                            }
                            $("#loader-init").hide();
                            $("#loader-barrido").hide();
                            location.href = _URL
                        } else {
                            localStorage.removeItem("_token")
                            $("#loader-init").hide();
                            // Mostrar mensaje si la sesión expiró
                            if (resp.msg && resp.msg === "session_expired_12h") {
                                // No mostrar alerta aquí, solo limpiar
                                console.log("Sesión expirada después de 12 horas");
                            }
                            // Mostrar el loader por 4 segundos
                            setTimeout(function() {
                                $("#loader-barrido").fadeOut(800);
                            }, 4000);
                        }
                    }
                })
            } else {
                $("#loader-init").hide();
                // Mostrar el loader por 4 segundos
                setTimeout(function() {
                    $("#loader-barrido").fadeOut(800);
                }, 4000);
            }
        })()

        $(document).ready(function () {
            // Funcionalidad para mostrar/ocultar contraseña
            $("#toggle-password").click(function() {
                const passwordField = $("#password-field");
                const passwordFieldType = passwordField.attr("type");
                
                if (passwordFieldType === "password") {
                    passwordField.attr("type", "text");
                    $(this).removeClass("fa-eye").addClass("fa-eye-slash");
                } else {
                    passwordField.attr("type", "password");
                    $(this).removeClass("fa-eye-slash").addClass("fa-eye");
                }
            });

            // Toggle para nueva contraseña
            $(document).on("click", "#toggle-new-password", function() {
                const passwordField = $("#new-password");
                const passwordFieldType = passwordField.attr("type");
                
                if (passwordFieldType === "password") {
                    passwordField.attr("type", "text");
                    $(this).removeClass("fa-eye").addClass("fa-eye-slash");
                } else {
                    passwordField.attr("type", "password");
                    $(this).removeClass("fa-eye-slash").addClass("fa-eye");
                }
            });

            // Toggle para confirmar contraseña
            $(document).on("click", "#toggle-confirm-password", function() {
                const passwordField = $("#confirm-password");
                const passwordFieldType = passwordField.attr("type");
                
                if (passwordFieldType === "password") {
                    passwordField.attr("type", "text");
                    $(this).removeClass("fa-eye").addClass("fa-eye-slash");
                } else {
                    passwordField.attr("type", "password");
                    $(this).removeClass("fa-eye-slash").addClass("fa-eye");
                }
            });

            // Abrir modal de recuperación
            $("#forgot-password-link").click(function(e) {
                e.preventDefault();
                $("#modal-recuperar-password").css("display", "flex");
                $("#step-email").show();
                $("#step-verify-code").hide();
                $("#step-new-password").hide();
                $("#recovery-email").val("");
                $("#verification-code").val("");
                $("#new-password").val("");
                $("#confirm-password").val("");
            });

            // Cerrar modal
            $("#close-modal").click(function() {
                $("#modal-recuperar-password").hide();
                $("#step-verify-code").hide();
                $("#step-new-password").hide();
                $("#step-email").show();
                $("#recovery-email").val("");
                $("#verification-code").val("");
                $("#new-password").val("");
                $("#confirm-password").val("");
            });

            // Cerrar modal al hacer click fuera
            $("#modal-recuperar-password").click(function(e) {
                if (e.target.id === "modal-recuperar-password") {
                    $(this).hide();
                    $("#step-verify-code").hide();
                    $("#step-new-password").hide();
                    $("#step-email").show();
                    $("#recovery-email").val("");
                    $("#verification-code").val("");
                    $("#new-password").val("");
                    $("#confirm-password").val("");
                }
            });

            // Enviar código de verificación
            $("#btn-send-code").click(function() {
                const email = $("#recovery-email").val().trim();
                
                if (!email) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Por favor ingresa tu correo electrónico',
                        confirmButtonColor: '#C1272D'
                    });
                    return;
                }

                // Validar formato de email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Por favor ingresa un correo electrónico válido',
                        confirmButtonColor: '#C1272D'
                    });
                    return;
                }

                // Mostrar loader de SweetAlert2
                Swal.fire({
                    title: 'Enviando código...',
                    html: 'Por favor espera mientras enviamos el código a tu correo',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    type: "POST",
                    url: _URL + "/ajs/password/send-code",
                    data: { email: email },
                    success: function(resp) {
                        resp = JSON.parse(resp);
                        
                        if (resp.res) {
                            // Mostrar alerta de éxito y ESPERAR a que se cierre antes de cambiar de paso
                            Swal.fire({
                                icon: 'success',
                                title: '¡Código Enviado!',
                                text: 'Revisa tu correo electrónico',
                                confirmButtonColor: '#C1272D',
                                confirmButtonText: 'Continuar'
                            }).then(() => {
                                // Cambiar al paso 2 DESPUÉS de cerrar la alerta
                                $("#step-email").hide();
                                $("#step-verify-code").show();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: resp.msg || 'Error al enviar el código',
                                confirmButtonColor: '#C1272D'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo conectar con el servidor',
                            confirmButtonColor: '#C1272D'
                        });
                    }
                });
            });

            // Volver al paso de email
            $("#btn-back-email").click(function() {
                $("#step-verify-code").hide();
                $("#step-email").show();
                $("#verification-code").val("");
            });

            // Verificar código (NUEVO PASO)
            $("#btn-verify-code").click(function() {
                const email = $("#recovery-email").val().trim();
                const code = $("#verification-code").val().trim();

                if (!code) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Por favor ingresa el código',
                        confirmButtonColor: '#C1272D'
                    });
                    return;
                }

                if (code.length !== 6) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'El código debe tener 6 dígitos',
                        confirmButtonColor: '#C1272D'
                    });
                    return;
                }

                // Mostrar loader
                Swal.fire({
                    title: 'Verificando código...',
                    html: 'Por favor espera un momento',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Verificar código con el backend
                $.ajax({
                    type: "POST",
                    url: _URL + "/ajs/password/verify-code",
                    data: {
                        email: email,
                        code: code
                    },
                    success: function(resp) {
                        resp = JSON.parse(resp);
                        
                        if (resp.res) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Código Verificado!',
                                text: 'Ahora puedes ingresar tu nueva contraseña',
                                confirmButtonColor: '#C1272D',
                                confirmButtonText: 'Continuar'
                            }).then(() => {
                                // Cambiar al paso 3
                                $("#step-verify-code").hide();
                                $("#step-new-password").show();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: resp.msg || 'Código incorrecto o expirado',
                                confirmButtonColor: '#C1272D'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo conectar con el servidor',
                            confirmButtonColor: '#C1272D'
                        });
                    }
                });
            });

            // Resetear contraseña (ahora solo valida las contraseñas)
            $("#btn-reset-password").click(function() {
                const email = $("#recovery-email").val().trim();
                const code = $("#verification-code").val().trim();
                const newPassword = $("#new-password").val();
                const confirmPassword = $("#confirm-password").val();

                if (!newPassword || !confirmPassword) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Por favor completa todos los campos',
                        confirmButtonColor: '#C1272D'
                    });
                    return;
                }

                if (newPassword.length < 4) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'La contraseña debe tener al menos 4 caracteres',
                        confirmButtonColor: '#C1272D'
                    });
                    return;
                }

                if (newPassword !== confirmPassword) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Las contraseñas no coinciden',
                        confirmButtonColor: '#C1272D'
                    });
                    return;
                }

                // Mostrar loader de SweetAlert2
                Swal.fire({
                    title: 'Actualizando contraseña...',
                    html: 'Por favor espera un momento',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    type: "POST",
                    url: _URL + "/ajs/password/reset",
                    data: {
                        email: email,
                        code: code,
                        new_password: newPassword
                    },
                    success: function(resp) {
                        resp = JSON.parse(resp);
                        
                        if (resp.res) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Contraseña Actualizada!',
                                text: 'Ya puedes iniciar sesión con tu nueva contraseña',
                                confirmButtonColor: '#C1272D',
                                confirmButtonText: 'Entendido'
                            }).then(() => {
                                $("#modal-recuperar-password").hide();
                                $("#recovery-email").val("");
                                $("#verification-code").val("");
                                $("#new-password").val("");
                                $("#confirm-password").val("");
                                $("#step-new-password").hide();
                                $("#step-email").show();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: resp.msg || 'Error al cambiar la contraseña',
                                confirmButtonColor: '#C1272D'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error de conexión',
                            text: 'No se pudo conectar con el servidor',
                            confirmButtonColor: '#C1272D'
                        });
                    }
                });
            });

            $("form").submit(function (evt) {
                evt.preventDefault();
                $("#loader-menor").show();
                $.ajax({
                    type: "POST",
                    url: _URL + "/login",
                    data: $("form").serialize(),
                    success: function (resp) {
                        $("#loader-menor").hide();
                        //console.log(resp);
                        resp = JSON.parse(resp);
                        if (resp.res) {
                            localStorage.setItem("_token", resp.token)
                            location.href = _URL
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: resp.msg
                            })
                        }
                    },
                    error() {
                        $("#loader-menor").hide();
                    }
                });
            });
        });

    </script>
</body>
</html>