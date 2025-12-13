<?php include('fragment/head.php') ?>
</head>
<!-- resources\views\index.php -->
<body data-sidebar="dark">
<!-- <input value="<?=$device_app?>" id="device-app"> -->
<div  id="loader-init">
    <div class="loadingio-spinner-double-ring-8kmkrab6ncg">
        <div class="ldio-407auvblvok">
            <div></div>
            <div></div>
            <div><div></div></div>
            <div><div></div></div>
        </div>
    </div>
</div>
<div style="display: none" id="loader-menor">
    <div class="loader-dots-container">
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</div>
<!-- Begin page -->
<div id="layout-wrapper">


    <?php include('fragment/header.php')?>

    <!-- ========== Left Sidebar Start ========== -->
    <?php include('fragment/nav-bar.php')?>
    <!-- Left Sidebar End -->


    <!-- ============================================================== -->
    <!-- Start right Content here -->
    <!-- ============================================================== -->
    <div class="main-content">
             
         
        <div class="page-content"  >
            <div id="contenedor-app" class="container-fluid"  style=" padding:0; margin: 0;" >

            </div> <!-- container-fluid -->
        </div>   
 <!-- End Page-content -->
            
            <!-- <footer class="text-center">
        © <script>document.write(new Date().getFullYear())</script> Facturación Hatuna 1.0 <span class="d-none d-sm-inline-block"> - Creado por <a href="https://magustechnologies.com/" target="_blank"> Magus Technologies</a></span>
        </footer> -->
       

    </div>
    <!-- end main content-->

</div>
<!-- END layout-wrapper -->
<style>
    .page-content{
       
    padding: 0 !important; /* Elimina el padding */
    margin: 0 !important;  /* Elimina el margen */
}
</style>
<!-- Right Sidebar -->
<div class="right-bar">
    <div data-simplebar class="h-100">
        <div class="rightbar-title px-3 py-4">
            <a href="javascript:void(0);" class="right-bar-toggle float-end">
                <i class="mdi mdi-close noti-icon"></i>
            </a>
            <h5 class="m-0">Settings</h5>
        </div>

        <!-- Settings -->
        <hr class="mt-0" />
        <h6 class="text-center">Choose Layouts</h6>

        <div class="p-4">
            <div class="mb-2">
                <img src="assets/images/layouts/layout-1.jpg" class="img-fluid img-thumbnail" alt="">
            </div>
            <div class="form-check form-switch mb-3">
                <input type="checkbox" class="form-check-input theme-choice" id="light-mode-switch" checked />
                <label class="form-check-label" for="light-mode-switch">Light Mode</label>
            </div>

            <div class="mb-2">
                <img src="assets/images/layouts/layout-2.jpg" class="img-fluid img-thumbnail" alt="">
            </div>
            <div class="form-check form-switch mb-3">
                <input type="checkbox" class="form-check-input theme-choice" id="dark-mode-switch" data-bsStyle="assets/css/bootstrap-dark.min.css"
                       data-appStyle="assets/css/app-dark.min.css" />
                <label class="form-check-label" for="dark-mode-switch">Dark Mode</label>
            </div>

            <div class="mb-2">
                <img src="assets/images/layouts/layout-3.jpg" class="img-fluid img-thumbnail" alt="">
            </div>
            <div class="form-check form-switch mb-5">
                <input type="checkbox" class="form-check-input theme-choice" id="rtl-mode-switch" data-appStyle="assets/css/app-rtl.min.css" />
                <label class="form-check-label" for="rtl-mode-switch">RTL Mode</label>
            </div>
            <div class="d-grid">
                <a href="https://1.envato.market/grNDB" class="btn btn-primary mt-3" target="_blank"><i class="mdi mdi-cart me-1"></i> Purchase Now</a>
            </div>
        </div>

    </div> <!-- end slimscroll-menu-->
</div>
<!-- /Right-bar -->

<!-- Right bar overlay-->
<div class="rightbar-overlay"></div>

<!-- JAVASCRIPT-->

<?php include("fragment/footer.php")?>

<script>
    $(document).ready(function (){

        // SISTEMA DE CONTROL DE SESIÓN - TIMEOUT DE 12 HORAS
        let verificadorInterval = null;

        // Función para verificar sesión
        function verificarSesion() {
            if (localStorage.getItem("_token")){
                $.ajax({
                    url: _URL+"/ajs/verificador/token",
                    type: "POST",
                    data: {
                        token:localStorage.getItem("_token"),
                        s:true
                    },
                    success(resp){
                        console.log(resp);
                        resp=JSON.parse(resp);
                        if (resp.res){
                            // Actualizar token con última actividad
                            if (resp.token) {
                                localStorage.setItem("_token", resp.token);
                            }
                        }else{
                            // Sesión expirada
                            clearInterval(verificadorInterval);
                            localStorage.removeItem("_token");

                            Swal.fire({
                                icon: 'warning',
                                title: 'Sesión Expirada',
                                text: 'Tu sesión ha expirado después de 12 horas por seguridad.',
                                confirmButtonText: 'Iniciar Sesión',
                                allowOutsideClick: false
                            }).then(() => {
                                location.href = _URL+"/login";
                            });
                        }
                    },
                    error() {
                        console.error("Error verificando sesión");
                    }
                })
            }else{
                clearInterval(verificadorInterval);
                location.href=_URL+"/login"
            }
        }

        // Verificador inicial
        (function verificadorInicial() {
            if (localStorage.getItem("_token")){
                $.ajax({
                    url: _URL+"/ajs/verificador/token",
                    type: "POST",
                    data: {
                        token:localStorage.getItem("_token"),
                        s:true
                    },
                    success(resp){
                        console.log(resp);
                        resp=JSON.parse(resp);
                        if (resp.res){
                            // Actualizar token con última actividad
                            if (resp.token) {
                                localStorage.setItem("_token", resp.token);
                            }
                            $("#loader-init").hide();
                            _ajaxDOM(getPathURL(),'contenedor-app')
                            $('body').on('click','#toggle-sidebar',function(){
                                $(".page-wrapper").toggleClass("toggled");
                            })

                            // Iniciar verificación periódica cada 60 segundos (1 minuto)
                            verificadorInterval = setInterval(verificarSesion, 60000);

                        }else{
                            localStorage.removeItem("_token")

                            Swal.fire({
                                icon: 'warning',
                                title: 'Sesión Expirada',
                                text: 'Tu sesión ha expirado después de 12 horas por seguridad.',
                                confirmButtonText: 'Iniciar Sesión',
                                allowOutsideClick: false
                            }).then(() => {
                                location.href = _URL+"/login";
                            });
                        }
                    }
                })
            }else{
                location.href=_URL+"/login"
            }
        })()

        // Detección de actividad desactivada (solo timeout de 12 horas)

        $("#logout").click(function (evt) {
            evt.preventDefault();
            clearInterval(verificadorInterval);
            localStorage.removeItem("_token")
            location.href=_URL+"/logout"
        })

    })


</script>
</body>
</html>
