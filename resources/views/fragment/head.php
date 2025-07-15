<!--resources\views\fragment\head.php -->
<?php
$id_role = 0;
if (isset($_SESSION['rol'])) {
    $id_role = $_SESSION['rol'];
}
$device_app = Tools::getInfoDeviceConect();

?><!doctype html>
<html lang="es">

<head>

    <meta charset="utf-8">
    <title>Facturación Eletrónica JVC</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description">
    <meta content="Themesbrand" name="author">
    <link rel="shortcut icon" href="<?= URL::to('public/assets/images/favicon.ico') ?>?v=<?= time() ?>">

    <link href="<?= URL::to('public/assets/css/bootstrap.min.css') ?>" id="bootstrap-style" rel="stylesheet"
        type="text/css">
    <link href="<?= URL::to('public/assets/css/icons.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="<?= URL::to('public/assets/css/app.min.css') ?>?v=<?= time() ?>" id="app-style" rel="stylesheet"
        type="text/css">
    <link href="<?= URL::to('public/assets/css/header.css') ?>?v=<?= time() ?>" id="app-style" rel="stylesheet"
        type="text/css">
    <!-- Tus otros estilos -->
    <link href="<?= URL::to('public/assets/css/sidebar.css') ?>?v=<?= time() ?>" id="app-style" rel="stylesheet"
        type="text/css">
    <!-- Agregar el nuevo CSS de fixes después -->
    <link href="<?= URL::to('public/assets/css/sidebar-fixes.css') ?>?v=<?= time() ?>" id="app-style" rel="stylesheet"
        type="text/css">
    <link href="<?= URL::to('public/assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') ?>"
        rel="stylesheet" type="text/css">
    <link href="<?= URL::to('public/assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css') ?>"
        rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= URL::to('public/plugin/sweetalert2/sweetalert2.min.css') ?>">

    <link href="<?= URL::to('public/plugin/jquery-ui/jquery-ui.css') ?>?v=<?= time() ?>" rel="stylesheet" type="text/css">
<script src="<?=URL::to('public/assets/libs/jquery/jquery.min.js') ?>?v=<?= time() ?>"> </script>

    <link href="<?= URL::to('public/assets/libs/%40fullcalendar/core/main.min.css') ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?= URL::to('public/assets/libs/%40fullcalendar/daygrid/main.min.css') ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?= URL::to('public/assets/libs/%40fullcalendar/bootstrap/main.min.css') ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?= URL::to('public/assets/libs/%40fullcalendar/timegrid/main.min.css') ?>" rel="stylesheet"
        type="text/css" />
    <link href="<?= URL::to('public/plugin/font-wesome/css/all.css') ?>" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css')  ?>?v=<?= time() ?>">

    <script>
        const _URL = '<?= URL::base() ?>';
    </script>
    <style>
        .loader-dots-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            background-color: #ff0000; /* Rojo */
            margin: 0 5px;
            animation: bounce 0.6s infinite alternate;
        }

        .dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes bounce {
            to {
                transform: translateY(-10px);
            }
        }
    </style>
    <style>
         /* body {
        background-color: #f7fafc;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    } */
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
            background-color: #eafcfb;
            line-height: 100vh;
            text-align: center;
        }
    </style>