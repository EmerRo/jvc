<?php include('fragment/head.php') ?>
</head>
<body data-sidebar="dark">

<div id="loader-init">
    <div class="loadingio-spinner-double-ring-8kmkrab6ncg">
        <div class="ldio-407auvblvok">
            <div></div>
            <div></div>
            <div><div></div></div>
            <div><div></div></div>
        </div>
    </div>
</div>

<div style="display:none" id="loader-menor">
    <div class="loader-dots-container">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</div>

<div id="layout-wrapper">
    <?php include('fragment/header.php') ?>
    <?php include('fragment/nav-bar.php') ?>

    <div class="main-content">
        <div class="page-content" style="padding:0;margin:0;">
            <div id="contenedor-app" class="container-fluid" style="padding:0;margin:0;"></div>
        </div>
    </div>
</div>

<?php include("fragment/footer.php") ?>

<script>
$(document).ready(function() {

    let verificadorInterval = null;

    function verificarSesion() {
        if (!localStorage.getItem("_token")) {
            clearInterval(verificadorInterval);
            location.href = _URL + "/login";
            return;
        }
        $.ajax({
            url: _URL + "/ajs/verificador/token",
            type: "POST",
            data: { token: localStorage.getItem("_token"), s: true },
            success(resp) {
                resp = JSON.parse(resp);
                if (resp.res) {
                    if (resp.token) localStorage.setItem("_token", resp.token);
                } else {
                    clearInterval(verificadorInterval);
                    localStorage.removeItem("_token");
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sesión Expirada',
                        text: 'Tu sesión ha expirado después de 12 horas por seguridad.',
                        confirmButtonText: 'Iniciar Sesión',
                        allowOutsideClick: false
                    }).then(() => { location.href = _URL + "/login"; });
                }
            },
            error() { console.error("Error verificando sesión"); }
        });
    }

    // Se ejecuta inmediatamente al cargar: valida el token y arranca el ciclo de verificación periódica
    (function verificadorInicial() {
        if (!localStorage.getItem("_token")) {
            location.href = _URL + "/login";
            return;
        }
        $.ajax({
            url: _URL + "/ajs/verificador/token",
            type: "POST",
            data: { token: localStorage.getItem("_token"), s: true },
            success(resp) {
                resp = JSON.parse(resp);
                if (resp.res) {
                    if (resp.token) localStorage.setItem("_token", resp.token);
                    $("#loader-init").hide();
                    _ajaxDOM(getPathURL(), 'contenedor-app');
                    $('body').on('click', '#toggle-sidebar', function() {
                        $(".page-wrapper").toggleClass("toggled");
                    });
                    verificadorInterval = setInterval(verificarSesion, 60000);
                } else {
                    localStorage.removeItem("_token");
                    Swal.fire({
                        icon: 'warning',
                        title: 'Sesión Expirada',
                        text: 'Tu sesión ha expirado después de 12 horas por seguridad.',
                        confirmButtonText: 'Iniciar Sesión',
                        allowOutsideClick: false
                    }).then(() => { location.href = _URL + "/login"; });
                }
            }
        });
    })();

    $("#logout").click(function(evt) {
        evt.preventDefault();
        clearInterval(verificadorInterval);
        localStorage.removeItem("_token");
        location.href = _URL + "/logout";
    });

});
</script>
</body>
</html>
