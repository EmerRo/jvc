<?php
require_once "app/models/GuiaRemision.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $guiaRemision = new GuiaRemision();
    $guia = $guiaRemision->getById($id);

    if ($guia) {
        $newGuia = $guia;
        unset($newGuia['id']); // Eliminar el ID para crear una nueva entrada
        $newGuia['fecha'] = date('Y-m-d'); // Actualizar la fecha a la actual

        $newId = $guiaRemision->create($newGuia);

        if ($newId) {
            header("Location: guia-remision-view.php?id=$newId");
            exit();
        } else {
            echo "Error al duplicar la guía de remisión.";
        }
    } else {
        echo "Guía de remisión no encontrada.";
    }
} else {
    echo "ID de guía de remisión no proporcionado.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Duplicar Guía de Remisión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Estilos para el dropdown de autocompletado */
        .ui-autocomplete {
            position: fixed !important;
            z-index: 9999 !important;
            max-height: 300px;
            overflow-y: auto;
            overflow-x: hidden;
            width: calc(100% - 40px) !important;
            max-width: 800px;
            border: 1px solid rgba(0, 0, 0, .15);
            border-radius: 0.25rem;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, .1);
            padding: 0.5rem 0;
            margin: 0 20px;
        }

        .ui-menu-item {
            margin: 0;
            padding: 8px 16px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ui-menu-item:last-child {
            border-bottom: none;
        }

        .ui-menu-item:hover,
        .ui-state-active,
        .ui-widget-content .ui-state-active {
            background-color: #0d6efd !important;
            color: white !important;
            border: none !important;
            margin: 0 !important;
        }

        .autocomplete-item {
            display: flex;
            align-items: center;
            gap: 8px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Estilos adicionales para mejorar la visibilidad */
        .card {
            border: none;
            border-radius: 8px;
            margin-bottom: 2rem;
        }

        .card-header {
            border-radius: 8px 8px 0 0;
        }

        .form-control,
        .form-select {
            padding: 0.625rem 1rem;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }

        .input-group .btn {
            padding: 0.625rem 1rem;
            border-radius: 0 6px 6px 0;
        }

        /* Estilos para los modales */
        .modal-content {
            border-radius: 8px;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .modal-header {
            border-bottom: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
            border-radius: 0 0 8px 8px;
        }

        /* Estilos para las listas */
        .list-group-item {
            border: 1px solid rgba(0, 0, 0, .125);
            margin-bottom: -1px;
            padding: 0.75rem 1.25rem;
            transition: all 0.2s ease-in-out;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        .list-group-item:first-child {
            border-top-left-radius: 0.25rem;
            border-top-right-radius: 0.25rem;
        }

        .list-group-item:last-child {
            border-bottom-left-radius: 0.25rem;
            border-bottom-right-radius: 0.25rem;
            margin-bottom: 0;
        }

        /* Estilos para los botones de acción */
        .btn-group .btn {
            padding: 0.375rem 0.75rem;
        }

        .btn-group .btn i {
            margin-right: 0;
        }

        /* Estilos para los formularios */
        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
        }

        .input-group {
            margin-bottom: 1rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #dee2e6;
        }

        /* Estilos para las tablas */
        .table {
            margin-bottom: 0;
        }

        .table th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }

        .table td {
            vertical-align: middle;
        }

        /* Estilos para los mensajes de alerta */
        .alert {
            border-radius: 0.25rem;
            margin-bottom: 1rem;
        }

        /* Estilos para los tooltips */
        .tooltip {
            font-size: 0.875rem;
        }

        /* Estilos para los spinners de carga */
        .spinner-border {
            width: 1.5rem;
            height: 1.5rem;
            border-width: 0.2em;
        }
    </style>
</head>
<body>
    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="page-title">Duplicar Guía de Remisión</h6>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Ventas</a></li>
                    <li class="breadcrumb-item"><a href="/ventas" class="button-link">Guía Remisión</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Duplicar</li>
                </ol>
            </div>
            <div class="col-md-4">
                <div class="float-end d-none d-md-block">
                    <button id="backbuttonvp" href="/guias/remision" type="button" class="btn btn-warning button-link">
                        <i class="fa fa-arrow-left"></i> Regresar
                    </button>
                    <button id="duplicateButton" type="button" class="btn btn-primary">
                        <i class="fa fa-copy"></i> Duplicar Guía
                    </button>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('duplicateButton').addEventListener('click', function() {
            window.location.href = 'guia-remision-duplicate.php?id=<?php echo $id; ?>';
        });
    </script>
</body>
</html>