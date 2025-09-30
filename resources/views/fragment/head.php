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

    <!-- Estilos para Sistema de Notificaciones -->
    <style>
        /* === SISTEMA DE NOTIFICACIONES === */
        .notification-bell {
            position: relative;
            cursor: pointer;
            display: inline-block;
        }

        .notification-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            min-width: 20px;
            height: 20px;
            font-size: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            line-height: 1;
            border: 2px solid white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #e3e6f0;
            border-radius: 8px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            width: 380px;
            max-height: 500px;
            z-index: 1050;
            display: none;
            overflow: hidden;
        }

        .notification-header {
            padding: 15px 20px;
            border-bottom: 1px solid #e3e6f0;
            background: #f8f9fc;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        .notification-header h6 {
            margin: 0;
            color: #5a5c69;
            font-weight: 600;
        }

        .notification-list {
            max-height: 350px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #e3e6f0;
            cursor: pointer;
            transition: background-color 0.2s ease;
            position: relative;
        }

        .notification-item:hover {
            background-color: #f8f9fc;
        }

        .notification-item.unread {
            background-color: #e3f2fd;
            border-left: 4px solid #C1272D;
        }

        .notification-item.read {
            opacity: 0.7;
        }

        .notification-content {
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .notification-icon {
            flex-shrink: 0;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #f8f9fc;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #e3e6f0;
        }

        .notification-body {
            flex: 1;
            min-width: 0;
        }

        .notification-message {
            font-size: 14px;
            line-height: 1.4;
            color: #5a5c69;
            margin-bottom: 5px;
            word-wrap: break-word;
        }

        .notification-meta {
            display: flex;
            gap: 10px;
            font-size: 12px;
        }

        .notification-mark-read {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #C1272D;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s ease;
            font-size: 12px;
        }

        .notification-item:hover .notification-mark-read {
            opacity: 1;
        }

        .notification-empty {
            padding: 40px 20px;
            text-align: center;
            color: #858796;
        }

        .notification-footer {
            padding: 10px 20px;
            background: #f8f9fc;
            border-top: 1px solid #e3e6f0;
            text-align: center;
        }

        /* Animación de pulso para el badge */
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        /* Toast personalizado */
        .notification-toast {
            background: white !important;
            border-left: 4px solid #C1272D !important;
        }

        /* Colores para diferentes tipos */
        .text-purple { color: #6f42c1 !important; }

        /* Responsivo */
        @media (max-width: 768px) {
            .notification-dropdown {
                width: 320px;
                right: -20px;
            }
        }

        @media (max-width: 480px) {
            .notification-dropdown {
                width: 280px;
                right: -40px;
            }
        }

        /* Scrollbar personalizada */
        .notification-list::-webkit-scrollbar {
            width: 6px;
        }

        .notification-list::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .notification-list::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .notification-list::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
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

