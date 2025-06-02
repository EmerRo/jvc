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
            background: linear-gradient(135deg, #f5f5f5 0%, #e0e0e0 100%);
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
            background-image: url('<?= URL::to("public/login/images/pattern.png") ?>');
            opacity: 0.05;
            z-index: 0;
        }

        .wrap-login100 {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
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
                        <span class="label-input100">Usuario / Email</span>
                        <input class="input100" type="text" required name="user"
                            placeholder="Escribe tu usuario o correo electrónico">
                        <span class="focus-input100" data-symbol="&#xf206;"></span>
                    </div>
                    <div class="wrap-input100 validate-input" data-validate="Se requere contraseña">
                        <span class="label-input100">Contraseña</span>
                        <input class="input100" type="password" required name="clave" id="password-field"
                            placeholder="Escribe tu contraseña">
                        <span class="focus-input100" data-symbol="&#xf190;"></span>
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
                        <a href="#">
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
                        console.log(resp);
                        resp = JSON.parse(resp);
                        if (resp.res) {
                            $("#loader-init").hide();
                            $("#loader-barrido").hide();
                            location.href = _URL
                        } else {
                            localStorage.removeItem("_token")
                            $("#loader-init").hide();
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