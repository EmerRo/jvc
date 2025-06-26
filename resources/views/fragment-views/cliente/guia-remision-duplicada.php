<!--resources\views\fragment-views\cliente\guia-remision-duplicada.php -->
<?php
require_once "app/models/Ubigeo.php";
$c_ubigeo = new Ubigeo();
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?= URL::to('/public/css/guia-remision/styles.css') ?>">

</head>

<body>

    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="page-title">Duplicar Guía de Remisión</h6>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Ventas</a></li>
                    <li class="breadcrumb-item"><a href="/ventas" class="button-link">Duplicar Guía de Remisión</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Productos</li>
                </ol>
            </div>
            <div class="col-md-4">
                <div class="float-end d-none d-md-block">
                    <button id="backbuttonvp" href="/guias/remision" type="button" class="btn btn-warning button-link">
                        <i class="fa fa-arrow-left"></i> Regresar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row" id="container-vue">
        <div>
            <input type="hidden" id="fecha-now-app" value="<?php echo date("Y-m-d"); ?>">
            <input type="hidden" id="cotizacion" name="cotizacion"
                value="<?php echo isset($_GET['coti']) ? $_GET['coti'] : ''; ?>">
            <div class="row">
                <!-- Columna Izquierda -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="col-md-12">
                                <form role="form" class="form-horizontal">
                                    <h5>Datos de la Guía</h5>
                                    <div class="form-group row mb-3">
                                        <label class="col-md-4 control-label text-end">Doc.</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control text-center"
                                                :value="guia.tipo_documento || 'GUIA DE REMISION'" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group row mb-3">
                                        <label class="col-lg-4 control-label text-end">Ser | Num</label>
                                        <div class="col-lg-4">
                                            <input v-model="guia.serie_g" type="text" class="form-control text-center"
                                                readonly>
                                        </div>
                                        <div class="col-lg-4">
                                            <input v-model="guia.numero_g" type="text" class="form-control text-center"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-lg-4 control-label text-end">Fecha</label>
                                        <div class="col-lg-6">
                                            <input type="date" v-model="guia.fecha_emision"
                                                class="form-control text-center">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-md-4 control-label text-end">Motivo</label>
                                        <div class="col-md-8 d-flex gap-2">
                                            <select class="form-control" v-model="guia.motivo" id="select_motivo">
                                                <option value="">Seleccione un motivo</option>
                                            </select>
                                            <button type="button" class="btn bg-rojo btn-sm px-2" data-bs-toggle="modal"
                                                data-bs-target="#motivoModal">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-lg-4 control-label text-end">Peso total</label>
                                        <div class="col-lg-6">
                                            <input v-model="guia.peso" type="text" class="form-control text-center">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-lg-4 control-label text-end">Nro Bultos</label>
                                        <div class="col-lg-6">
                                            <input v-model="guia.num_bultos" type="text"
                                                class="form-control text-center">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-rojo text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt me-2"></i>Detalle de Guía de Remisión
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form class="form-horizontal">
                                    <!-- Agregar Destinatario -->
                                    <div class="mb-4 row">
                                        <label class="col-lg-2 col-form-label">
                                            <i class="fas fa-user-plus me-2"></i>Agregar Destinatario
                                        </label>
                                        <div class="col-lg-10">
                                            <div class="input-group">
                                                <input type="text" class="form-control" v-model="guia.doc_cli"
                                                    placeholder="Ingrese Documento" maxlength="11">
                                                <button class="btn bg-rojo" type="button" @click="buscarDocumentSS">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Destinatario -->
                                    <div class="mb-4 row">
                                        <label class="col-lg-2 col-form-label">
                                            <i class="fas fa-user me-2"></i>Destinatario
                                        </label>
                                        <div class="col-lg-10">
                                            <input v-model="guia.nom_cli" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <!-- Punto Partida -->
                                    <div class="mb-4 row">
                                        <label class="col-lg-2 col-form-label">
                                            <i class="fas fa-map-marker-alt me-2"></i>Punto Partida
                                        </label>
                                        <div class="col-lg-10">
                                            <input type="text" class="form-control" v-model="guia.dir_part"
                                                value="AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE">
                                        </div>
                                    </div>

                                    <!-- Punto Llegada -->
                                    <div class="mb-4 row">
                                        <label class="col-lg-2 col-form-label">
                                            <i class="fas fa-flag-checkered me-2"></i>Punto Llegada
                                        </label>
                                        <div class="col-lg-10">
                                            <input type="text" class="form-control" v-model="guia.dir_cli">
                                        </div>
                                    </div>

                                    <!-- Ubigeo -->
                                    <div class="mb-4 row">
                                        <label class="col-lg-2 col-form-label">
                                            <i class="fas fa-map me-2"></i>Ubigeo
                                        </label>
                                        <div class="col-lg-10">
                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <select class="form-select" name="select_departamento"
                                                        id="select_departamento" onchange="obtenerProvincias()">
                                                        <?php
                                                        foreach ($c_ubigeo->verDepartamentos() as $fila) {
                                                            echo "<option value='{$fila["departamento"]}'>{$fila['nombre']}</option>";
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <select class="form-select" name="select_provincia"
                                                        id="select_provincia" onchange="obtenerProvincias()">
                                                    </select>
                                                </div>
                                                <div class="col-lg-4">
                                                    <select class="form-select" name="select_distrito"
                                                        id="select_distrito">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                  <!-- Datos del Conductor -->
<div class="mb-4">
    <div class="section-header bg-rojo text-white p-3 rounded">
        <h5 class="mb-0">
            <i class="fas fa-id-card me-2"></i>Datos del Conductor
        </h5>
    </div>

    <div class="mt-4">
        <!-- Transportista -->
        <div class="row mb-3 align-items-center">
            <div class="col-lg-2">
                <label class="form-label mb-0">
                    <i class="fas fa-truck me-2"></i>Transportista
                </label>
            </div>
            <div class="col-lg-4">
                <select v-model="transporte.tipo_trans" class="form-select"
                    name="select_tipo_transporte" id="select_tipo_transporte">
                    <option value="1">Propio</option>
                    <option value="2">Externo</option>
                </select>
            </div>
        </div>

        <!-- Tipo y Número de Documento -->
        <div v-show="transporte.tipo_trans === '2'" class="row mb-3 align-items-center">
            <div class="col-lg-2">
                <label class="form-label mb-0">
                    <i class="fas fa-file-alt me-2"></i>Tipo DOC
                </label>
            </div>
            <div class="col-lg-3">
                <select v-model="transporte.tipo_documento" class="form-select">
                    <option value="DNI">DNI</option>
                    <option value="RUC">RUC</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label mb-0">
                    <i class="fas fa-hashtag me-2"></i>N° DOC
                </label>
            </div>
            <div class="col-lg-5">
                <div class="input-group">
                    <input type="text" class="form-control"
                        v-model="transporte.num_docu" maxlength="11">
                    <button class="btn bg-rojo d-flex align-items-center gap-1"
                        type="button" @click="buscarDocumentoTransporte">
                        <i class="fas fa-check"></i>
                        Verificar
                    </button>
                </div>
            </div>
        </div>

        <!-- Chofer -->
        <div class="row mb-3 align-items-center">
            <div class="col-lg-2">
                <label class="form-label mb-0">
                    <i class="fas fa-user-tie me-2"></i>Chofer
                </label>
            </div>
            <div class="col-lg-10">
                <div class="input-group">
                    <select class="form-select" id="select_chofer">
                        <option value="">Seleccione un chofer</option>
                    </select>
                    <button class="btn bg-rojo" type="button"
                        data-bs-toggle="modal" data-bs-target="#choferModal">
                        <i class="fas fa-plus text-white"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Vehículo -->
        <div class="row mb-3 align-items-center">
            <div class="col-lg-2">
                <label class="form-label mb-0">
                    <i class="fas fa-truck me-2"></i>Vehículo
                </label>
            </div>
            <div class="col-lg-5">
                <select class="form-select" v-model="transporte.veiculo" id="select_vehiculo">
                    <option value="">Seleccione un vehículo</option>
                </select>
            </div>
            <div class="col-lg-5">
                <select class="form-select" v-model="transporte.chofer_dni" id="select_licencia">
                    <option value="">Seleccione una licencia</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Observaciones -->
<div class="mb-4 row align-items-center">
    <div class="col-lg-2">
        <label class="form-label mb-0">
            <i class="fas fa-comment-alt me-2"></i>Observaciones
        </label>
    </div>
    <div class="col-lg-10">
        <textarea v-model="guia.observacion" class="form-control" rows="3"></textarea>
    </div>
</div>

<!-- Doc. de Referencia -->
<div class="mb-4 row align-items-center">
    <div class="col-lg-2">
        <label class="form-label mb-0">
            <i class="fas fa-file me-2"></i>Doc. de Referencia
        </label>
    </div>
    <div class="col-lg-10">
        <input type="text" class="form-control" v-model="guia.doc_referencia">
    </div>
</div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos -->
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Detalle Venta</h5>
                            <button type="button" class="btn bg-rojo" data-bs-toggle="modal"
                                data-bs-target="#modalBuscarProductos">
                                <i class="fas fa-plus me-2"></i>Agregar Producto
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table text-center table-sm">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>P. Unit.</th>
                                    <th>Parcial</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in productos">
                                    <td>{{index+1}}</td>
                                    <td>{{item.nombre}}</td>
                                    <td>{{item.cantidad}}</td>
                                    <td>{{parseFloat(item.precio).toFixed(2)}}</td>
                                    <td>{{subTotalPro(item.cantidad,item.precio)}}</td>
                                    <td>
                                        <div class="d-flex gap-2 justify-content-center">
                                            <button @click="editarProducto(index)" class="btn btn-warning btn-sm"
                                                title="Editar">
                                                <i class="fa fa-edit"></i>
                                            </button>
                                            <button @click="eliminarProducto(index)" class="btn btn-danger btn-sm"
                                                title="Eliminar">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button @click="duplicarGuia" class="btn btn-success btn-lg">
                            <i class="fas fa-copy me-2"></i>Duplicar Guía
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal Motivo -->
            <div class="modal fade" id="motivoModal" tabindex="-1" aria-labelledby="motivoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="motivoModalLabel">Gestionar Motivos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="motivoForm">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nombreMotivo"
                                            placeholder="Nombre del Motivo">
                                        <button type="submit" class="btn bg-rojo">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <h6>Motivos Existentes</h6>
                            <ul id="listaMotivos" class="list-group mt-3">
                                <!-- Los motivos se cargarán aquí dinámicamente -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Chofer Mejorado -->
            <div class="modal fade" id="choferModal" tabindex="-1" aria-labelledby="choferModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-xl"> <!-- Cambié a modal-xl para más espacio -->
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="choferModalLabel">Gestionar Conductores</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Formulario para nuevo chofer/configuración -->
                            <form id="conductorForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">DNI</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="dniChofer" placeholder="DNI"
                                                maxlength="8" required>
                                            <button type="button" class="btn border-rojo text-rojo bg-white"
                                                onclick="buscarDniChofer($('#dniChofer').val())">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Nombre Completo</label>
                                        <input type="text" class="form-control" id="nombreCompleto"
                                            placeholder="Nombre del Chofer" required>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Placa</label>
                                        <input type="text" class="form-control" id="placaVehiculo" placeholder="Placa"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Marca</label>
                                        <input type="text" class="form-control" id="marcaVehiculo" placeholder="Marca"
                                            required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Licencia</label>
                                        <input type="text" class="form-control" id="numeroLicencia"
                                            placeholder="N° Licencia" required>
                                    </div>
                                </div>
                                <div class="mt-3 text-end">
                                    <button type="submit" class="btn bg-rojo text-white">
                                        <i class="fas fa-save"></i> Guardar Configuración
                                    </button>
                                </div>
                                <input type="hidden" id="choferConfigId">
                            </form>

                            <hr>

                            <!-- Lista de configuraciones existentes -->
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="fw-bold">Configuraciones de Conductores</h6>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Nota:</strong> Un conductor puede tener múltiples vehículos.
                                        Selecciona la configuración específica que necesites para esta guía.
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-hover">
                                            <thead class="bg-rojo">
                                                <tr>
                                                    <th>Conductor</th>
                                                    <th>DNI</th>
                                                    <th>Vehículo</th>
                                                    <th>Marca</th>
                                                    <th>Licencia</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tablaConfiguraciones">
                                                <!-- Las configuraciones se cargarán aquí dinámicamente -->
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Lista alternativa para cuando hay pocos registros -->
                                    <ul id="listaConfiguraciones" class="list-group mt-3" style="display: none;">
                                        <!-- Las configuraciones se cargarán aquí dinámicamente -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Modal para buscar productos -->
            <div class="modal fade" id="modalBuscarProductos" tabindex="-1" aria-labelledby="modalBuscarProductosLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalBuscarProductosLabel">Buscar Productos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form v-on:submit.prevent="addProduct" class="form-horizontal">
                                <div class="form-group row mb-3">
                                    <label class="col-lg-2 control-label">Buscar</label>
                                    <div class="col-lg-10">
                                        <div class="input-group">
                                            <input type="text" placeholder="Buscar por código o nombre del producto"
                                                class="form-control" id="input_buscar_productos" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-3">
                                    <label class="col-lg-2 control-label">Descripción</label>
                                    <div class="col-lg-10">
                                        <input required v-model="producto.descripcion" type="text"
                                            placeholder="Descripción" class="form-control" readonly>
                                    </div>
                                </div>

                                <div class="form-group row mb-3">
                                    <div class="col-lg-12 d-flex align-items-end gap-3">
                                        <div class="flex-grow-1">
                                            <label class="col-form-label">Stock Actual</label>
                                            <input disabled v-model="producto.stock" class="form-control text-center"
                                                type="text" placeholder="0">
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="col-form-label">Cantidad</label>
                                            <input @keypress="onlyNumber" required v-model="producto.cantidad"
                                                class="form-control text-center" type="text" placeholder="0">
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="col-form-label">Precio</label>
                                            <select class="form-control" v-model="producto.precio">
                                                <option v-for="(value,key) in precioProductos" :value="value.precio"
                                                    :key="key">
                                                    {{ value.precio}}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Cerrar</button>
                                    <button type="button" class="btn btn-success" @click="addProduct">
                                        <i class="fa fa-check me-2"></i> Agregar Producto
                                    </button>
                                </div>
                        </div>
                    </div>
                </div>


            </div>
            <!-- Modal de Edición -->
            <div class="modal fade" id="modalEditarProducto" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Producto</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Código del producto -->
                            <div class="mb-3">
                                <label class="form-label">Código</label>
                                <input type="text" class="form-control" v-model="productoEdit.codigo_pp" readonly>
                            </div>
                            <!-- Nombre del producto -->
                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" class="form-control" v-model="productoEdit.nom_prod">
                            </div>
                            <!-- Descripción/Detalle -->
                            <div class="mb-3">
                                <label class="form-label">Descripción</label>
                                <textarea class="form-control" v-model="productoEdit.detalle" rows="5"></textarea>
                            </div>
                            <!-- Cantidad -->
                            <div class="mb-3">
                                <label class="form-label">Cantidad</label>
                                <input type="number" class="form-control" v-model="productoEdit.cantidad"
                                    @keypress="onlyNumber">
                            </div>
                            <!-- Precio -->
                            <div class="mb-3">
                                <label class="form-label">Precio</label>
                                <select class="form-control" v-model="productoEdit.precio">
                                    <option v-for="(value, key) in precioProductos" :value="value.precio" :key="key">
                                        {{ value.precio }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn bg-rojo" @click="actualizarProducto">
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>


        </div>

        <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14"></script>
        <script>
            // Función para obtener provincias
            function obtenerProvincias() {
                var select_provincia = $("#select_provincia");
                var parametros = {
                    "departamento": $("#select_departamento").val()
                };

                $.ajax({
                    data: parametros,
                    url: _URL + '/ajs/consulta/lista/provincias',
                    type: 'post',
                    beforeSend: function () {
                        select_provincia.find('option').remove();
                    },
                    success: function (response) {
                        var json_response = JSON.parse(response);
                        select_provincia.find('option').remove();
                        $(json_response).each(function (i, v) {
                            select_provincia.append('<option value="' + v.provincia + '">' + v.nombre + '</option>');
                        });
                        select_provincia.prop('disabled', false);
                        obtenerDistritos();
                    },
                    error: function () {
                        alertAdvertencia("Error al obtener provincias");
                    }
                });
            }

            // Función para obtener distritos
            function obtenerDistritos() {
                var select_distrito = $("#select_distrito");
                var parametros = {
                    "departamento": $("#select_departamento").val(),
                    "provincia": $("#select_provincia").val()
                };

                $.ajax({
                    data: parametros,
                    url: _URL + '/ajs/consulta/lista/distrito',
                    type: 'post',
                    beforeSend: function () {
                        select_distrito.find('option').remove();
                    },
                    success: function (response) {
                        var json_response = JSON.parse(response);
                        select_distrito.find('option').remove();
                        $(json_response).each(function (i, v) {
                            select_distrito.append('<option value="' + v.ubigeo + '">' + v.nombre + '</option>');
                        });
                        select_distrito.prop('disabled', false);
                    },
                    error: function () {
                        alertAdvertencia("Error al obtener distritos");
                    }
                });
            }

            $(document).ready(function () {
                // Inicialización de Vue
                var appguia = new Vue({
                    el: "#container-vue",
                    data: {
                        guia: {
                            fecha_emision: $("#fecha-now-app").val(),
                            tipo_doc: '2',
                            serie: '',
                            numero: '',
                            total: '',
                            serie_g: '',
                            numero_g: '',
                            venta: '',
                            doc_cli: '',
                            nom_cli: '',
                            dir_cli: '',
                            dir_part: 'AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE',
                            observacion: '',
                            doc_referencia: '',
                            peso: '1',
                            num_bultos: '1',
                        },
                        producto: {
                            editable: false,
                            productoid: "",
                            descripcion: "",
                            nom_prod: "",
                            cantidad: "",
                            stock: "",
                            precio: "1",
                            codigo: "",
                            codigo_pp: "",
                            costo: "",
                            codsunat: "",
                            almacen: '<?php echo $_SESSION["sucursal"] ?>',
                            precio2: '',
                            precio_unidad: '',
                            precioVenta: '',
                            precio_usado: 1
                        },
                        productoEdit: {
                            index: -1,
                            descripcion: '',
                            cantidad: '',
                            precio: '',
                            stock: '',
                            productoid: '',
                            codigo_pp: '',
                            nom_prod: ''
                        },
                        productos: [],
                        precioProductos: [],
                        transporte: {
                            tipo_documento: 'RUC',
                            ruc: '',
                            tipo_trans: '1',
                            razon_social: '',
                            veiculo: '',
                            chofer_dni: '',
                            chofer_datos: '',
                            chofer_id: '',
                            num_doc: '',
                            num_docu: ''
                        }
                    },
                    methods: {
                        editarProducto(index) {
                            const producto = JSON.parse(JSON.stringify(this.productos[index]));

                            this.productoEdit = {
                                index: index,
                                descripcion: producto.descripcion,
                                detalle: producto.detalle || '', // Agregamos el detalle
                                cantidad: producto.cantidad,
                                precio: producto.precio,
                                stock: producto.stock,
                                productoid: producto.productoid,
                                codigo_pp: producto.codigo_pp || producto.codigo,
                                nom_prod: producto.nombre || producto.nom_prod, // Asegurar que usamos el nombre correcto
                                nombre: producto.nombre || producto.nom_prod // Agregar también el nombre
                            };

                            console.log('Producto a editar:', this.productoEdit);

                            new bootstrap.Modal(document.getElementById('modalEditarProducto')).show();
                        },
                        actualizarProducto() {
                            if (!this.productoEdit.cantidad || this.productoEdit.cantidad <= 0) {
                                alertAdvertencia("Por favor, ingrese una cantidad válida");
                                return;
                            }

                            const index = this.productoEdit.index;
                            if (index > -1) {
                                // Crear un nuevo objeto con los datos actualizados
                                const productoActualizado = {
                                    ...this.productos[index],
                                    cantidad: parseFloat(this.productoEdit.cantidad),
                                    precio: this.productoEdit.precio,
                                    descripcion: this.productoEdit.descripcion,
                                    detalle: this.productoEdit.detalle,
                                    nom_prod: this.productoEdit.nom_prod,
                                    nombre: this.productoEdit.nom_prod, // Actualizar también el nombre
                                    codigo_pp: this.productoEdit.codigo_pp
                                };

                                // Imprimir para debug
                                console.log('Producto antes de actualizar:', this.productos[index]);
                                console.log('Producto actualizado:', productoActualizado);
                                // Actualizar el array de productos
                                this.$set(this.productos, index, productoActualizado);
                                // Forzar la actualización de la vista
                                this.$nextTick(() => {
                                    this.$forceUpdate();
                                    console.log('Productos después de actualizar:', this.productos);
                                });

                                bootstrap.Modal.getInstance(document.getElementById('modalEditarProducto')).hide();
                                alertExito("Producto actualizado correctamente");
                            } else {
                                alertAdvertencia("No se pudo encontrar el producto para actualizar");
                            }
                        },

                        onlyNumber($event) {
                            let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                            if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) {
                                $event.preventDefault();
                            }
                        },
                        autocompletarUbigeo(ubigeo) {
                            if (ubigeo && ubigeo.length === 6) {
                                const departamento = ubigeo.substring(0, 2);
                                const provincia = ubigeo.substring(2, 4);

                                // Primero seleccionamos el departamento
                                $("#select_departamento").val(departamento);

                                // Función para manejar la selección de provincia
                                const seleccionarProvincia = () => {
                                    return new Promise((resolve) => {
                                        $.ajax({
                                            data: { "departamento": departamento },
                                            url: _URL + '/ajs/consulta/lista/provincias',
                                            type: 'post',
                                            success: (response) => {
                                                const json_response = JSON.parse(response);
                                                const select_provincia = $("#select_provincia");
                                                select_provincia.empty();

                                                $(json_response).each(function (i, v) {
                                                    select_provincia.append(`<option value="${v.provincia}">${v.nombre}</option>`);
                                                });

                                                select_provincia.val(provincia);
                                                resolve();
                                            }
                                        });
                                    });
                                };

                                // Función para manejar la selección del distrito
                                const seleccionarDistrito = () => {
                                    return new Promise((resolve) => {
                                        $.ajax({
                                            data: {
                                                "departamento": departamento,
                                                "provincia": provincia
                                            },
                                            url: _URL + '/ajs/consulta/lista/distrito',
                                            type: 'post',
                                            success: (response) => {
                                                const json_response = JSON.parse(response);
                                                const select_distrito = $("#select_distrito");
                                                select_distrito.empty();

                                                $(json_response).each(function (i, v) {
                                                    select_distrito.append(`<option value="${v.ubigeo}">${v.nombre}</option>`);
                                                });

                                                select_distrito.val(ubigeo);
                                                resolve();
                                            }
                                        });
                                    });
                                };

                                // Ejecutar en secuencia
                                seleccionarProvincia()
                                    .then(() => seleccionarDistrito())
                                    .catch(error => console.error('Error al autocompletar ubigeo:', error));
                            }
                        },
                        buscarDocumentSS() {
                            // Verifica que el número de documento no esté vacío
                            if (!this.guia.doc_cli) {
                                alert("Ingrese un número de documento");
                                return;
                            }

                            const docLength = this.guia.doc_cli.length;
                            $("#loader-menor").show();

                            _ajax("/ajs/prealerta/doc/cliente", "POST", {
                                doc: this.guia.doc_cli
                            }, (resp) => {
                                $("#loader-menor").hide();

                                if (docLength === 11 && this.guia.doc_cli.startsWith('20')) {
                                    if (resp.razonSocial) {
                                        this.guia.nom_cli = resp.razonSocial;
                                        this.guia.dir_cli = resp.direccion || '';

                                        // Llamar a autocompletarUbigeo con el ubigeo de la respuesta
                                        if (resp.ubigeo) {
                                            this.autocompletarUbigeo(resp.ubigeo);
                                        }
                                    } else {
                                        alert("RUC no encontrado");
                                    }
                                } else if (docLength === 8) {
                                    if (resp.success) {
                                        this.guia.nom_cli = resp.nombres + ' ' + (resp.apellidoPaterno || '') + ' ' + (resp.apellidoMaterno || '');
                                    } else {
                                        alert("Documento no encontrado");
                                    }
                                } else {
                                    alert("Documento debe ser 8 dígitos (DNI) o 11 dígitos (RUC)");
                                }
                            });
                        },


                        buscarDocumentoTransporte() {
                            if (!this.transporte.num_docu) {
                                alert("Ingrese un número de documento");
                                return;
                            }

                            const docLength = this.transporte.num_docu.length;
                            $("#loader-menor").show();

                            _ajax("/ajs/prealerta/doc/cliente", "POST", {
                                doc: this.transporte.num_docu
                            }, (resp) => {
                                $("#loader-menor").hide();

                                if (docLength === 8) {
                                    if (resp.success) {
                                        this.transporte.chofer_datos = resp.nombres + ' ' + (resp.apellidoPaterno || '') + ' ' + (resp.apellidoMaterno || '');
                                    } else {
                                        alert("Documento no encontrado");
                                        this.transporte.chofer_datos = '';
                                    }
                                } else if (docLength === 11) {
                                    if (resp.razonSocial) {
                                        this.transporte.chofer_datos = resp.razonSocial;
                                    } else {
                                        alert("RUC no encontrado");
                                        this.transporte.chofer_datos = '';
                                    }
                                } else {
                                    alert("Documento debe ser 8 dígitos (DNI) o 11 dígitos (RUC)");
                                    this.transporte.chofer_datos = '';
                                }
                            });
                        },
                        registerGuia() {
                            if (!this.guia.nom_cli) {
                                alertAdvertencia("Por favor, ingrese un destinatario");
                                return;
                            }

                            if (!this.productos || this.productos.length === 0) {
                                alertAdvertencia("Por favor, agregue al menos un producto");
                                return;
                            }

                            if (!$("#select_motivo").val()) {
                                alertAdvertencia("Por favor, seleccione un motivo");
                                return;
                            }

                            const productosValidados = this.productos.map(prod => ({
                                ...prod,
                                idproducto: prod.idproducto || prod.productoid
                            }));

                            const data = {
                                ...this.guia,
                                ...this.transporte,
                                productos: JSON.stringify(productosValidados),
                                ubigeo: $("#select_distrito").val(),
                                motivo: $("#select_motivo").val(),
                                fecha_emision: $("#input_fecha").val()
                            };

                            $("#loader-menor").show();
                            _ajax("/ajs/guia/remision/add", "POST", data,
                                (resp) => {
                                    $("#loader-menor").hide();
                                    if (resp.res) {
                                        alertExito("Guía de Remisión Registrada")
                                            .then(() => {
                                                $("#backbuttonvp").click();
                                            });
                                    } else {
                                        alertAdvertencia(resp.msg || "No se pudo completar el registro de la GUIA");
                                    }
                                }
                            );
                        },
                        eliminarProducto(index) {
                            this.productos.splice(index, 1);
                        },
                        subTotalPro(cnt, precio) {
                            return (parseFloat(cnt) * parseFloat(precio)).toFixed(2);
                        },

                        addProduct() {
                            if (!this.producto.descripcion || !this.producto.cantidad) {
                                alertAdvertencia("Por favor, complete todos los campos requeridos.");
                                return;
                            }

                            if (this.producto.descripcion.length > 0 && this.producto.cantidad > 0) {
                                const prod = {
                                    descripcion: this.producto.descripcion,
                                    nombre: this.producto.nombre,
                                    cantidad: this.producto.cantidad,
                                    precio: this.producto.precio || '0',
                                    stock: this.producto.stock || '0',
                                    codigo: this.producto.codigo || '',
                                    productoid: this.producto.productoid || ''
                                };

                                this.productos.push(prod);
                                $('#modalBuscarProductos').modal('hide');

                                // Reset the form
                                this.producto = {
                                    editable: false,
                                    productoid: "",
                                    descripcion: "",
                                    nombre: "",
                                    nom_prod: "",
                                    cantidad: "",
                                    stock: "",
                                    precio: "1",
                                    codigo: "",
                                    codigo_pp: "",
                                    costo: "",
                                    codsunat: "",
                                    almacen: '<?php echo $_SESSION["sucursal"] ?>',
                                    precio2: '',
                                    precio_unidad: '',
                                    precioVenta: '',
                                    precio_usado: 1
                                };
                                this.precioProductos = [];

                                alertExito("Producto agregado correctamente");
                            } else {
                                alertAdvertencia("Por favor, complete todos los campos requeridos.");
                            }
                        },
                        getDocumentoGuia() {
                            _ajax("/ajs/consulta/sn", "POST", {
                                doc: '11'
                            },
                                (resp) => {
                                    this.guia.numero_g = resp.numero;
                                    this.guia.serie_g = resp.serie;
                                }
                            );
                        },
                        comprobarVenta() {
                            if (!this.guia.serie || !this.guia.numero) {
                                alertAdvertencia("Ingrese serie y número");
                                return;
                            }

                            var vue = this;
                            var data = {
                                idtido: this.guia.tipo_doc,
                                serie: this.guia.serie,
                                numero: this.guia.numero
                            };

                            $("#loader-menor").show();

                            _ajax("/ajs/consulta/guia/documentofb", "POST", data,
                                function (resp) {
                                    $("#loader-menor").hide();
                                    console.log('Response:', resp);

                                    if (resp.res) {
                                        alertExito("Documento encontrado");
                                        // Asegurarse de que cada producto tenga todos los campos necesarios
                                        vue.productos = resp.productos.map(prod => ({
                                            ...prod,
                                            nombre: prod.nombre || prod.nom_prod, // Asegurar que tengamos el nombre
                                            detalle: prod.detalle || prod.descripcion,
                                            codigo_pp: prod.codigo_pp || prod.codigo,
                                            nom_prod: prod.nom_prod || prod.nombre
                                        }));
                                        vue.guia.venta = resp.idventa;
                                        vue.guia.doc_cli = resp.doc_cliente;
                                        vue.guia.nom_cli = resp.nom_cliente;
                                        vue.guia.dir_cli = resp.dir_cliente;
                                        vue.guia.total = resp.total;
                                    } else {
                                        alertAdvertencia(resp.msg || "No se encontró el documento");
                                        vue.productos = [];
                                        vue.guia.venta = '';
                                        vue.guia.doc_cli = '';
                                        vue.guia.nom_cli = '';
                                        vue.guia.dir_cli = '';
                                        vue.guia.total = '';
                                    }
                                }
                            );
                        },
                        loadCotizacionData(cotiId) {
                            $("#loader-menor").show();
                            _ajax("/ajs/guia/remision/coti/" + cotiId, "POST", { cod: cotiId },
                                (resp) => {
                                    console.log('Productos:', resp);
                                    this.productos = resp;
                                    $("#loader-menor").hide();
                                }
                            );

                            _ajax("/ajs/guia/remision/coti/cliente/" + cotiId, "POST", { cod: cotiId },
                                (resp) => {
                                    console.log('Cliente:', resp);
                                    this.guia.nom_cli = resp.datos;
                                    this.guia.dir_cli = resp.direccion;
                                    this.guia.doc_cli = resp.documento;

                                    if (resp.ubigeo) {
                                        // Primero seleccionamos el departamento
                                        $("#select_departamento").val(resp.departamento.padStart(2, '0'));

                                        // Luego cargamos las provincias
                                        $.ajax({
                                            data: { "departamento": resp.departamento.padStart(2, '0') },
                                            url: _URL + '/ajs/consulta/lista/provincias',
                                            type: 'post',
                                            success: (response) => {
                                                const provincias = JSON.parse(response);
                                                const select_provincia = $("#select_provincia");
                                                select_provincia.empty();

                                                provincias.forEach(v => {
                                                    select_provincia.append(`<option value="${v.provincia}">${v.nombre}</option>`);
                                                });

                                                // Seleccionamos la provincia
                                                const provinciaCode = resp.ubigeo.substring(2, 4);
                                                select_provincia.val(provinciaCode);

                                                // Finalmente cargamos los distritos
                                                $.ajax({
                                                    data: {
                                                        "departamento": resp.departamento.padStart(2, '0'),
                                                        "provincia": provinciaCode
                                                    },
                                                    url: _URL + '/ajs/consulta/lista/distrito',
                                                    type: 'post',
                                                    success: (response) => {
                                                        const distritos = JSON.parse(response);
                                                        const select_distrito = $("#select_distrito");
                                                        select_distrito.empty();

                                                        distritos.forEach(v => {
                                                            select_distrito.append(`<option value="${v.ubigeo}">${v.nombre}</option>`);
                                                        });

                                                        // Seleccionamos el distrito usando el ubigeo completo
                                                        select_distrito.val(resp.ubigeo);
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }
                            );
                        },
                        duplicarGuia() {
                            // Obtener el ID de la guía de la URL
                            const urlParams = new URLSearchParams(window.location.search);
                            const idGuia = urlParams.get('id');

                            if (!idGuia) {
                                alertAdvertencia("ID de guía no proporcionado");
                                return;
                            }

                            // Validaciones
                            if (!this.guia.nom_cli) {
                                alertAdvertencia("Por favor, ingrese un destinatario");
                                return;
                            }

                            if (!this.productos || this.productos.length === 0) {
                                alertAdvertencia("Por favor, agregue al menos un producto");
                                return;
                            }

                            if (!this.guia.motivo) {
                                alertAdvertencia("Por favor, seleccione un motivo");
                                return;
                            }

                            // Preparar datos para enviar
                            const data = {
                                id_guia_remision: idGuia, // Agregar el ID de la guía
                                ...this.guia,
                                ...this.transporte,
                                productos: JSON.stringify(this.productos),
                                ubigeo: $("#select_distrito").val(),
                                motivo: this.guia.motivo
                            };

                            $("#loader-menor").show();
                            _ajax("/ajs/guia/remision/duplicar", "POST", data,
                                (resp) => {
                                    $("#loader-menor").hide();
                                    if (resp.res) {
                                        alertExito("Guía de Remisión Duplicada")
                                            .then(() => {
                                                window.location.href = '/guias/remision';
                                            });
                                    } else {
                                        alertAdvertencia(resp.error || "No se pudo completar la duplicación de la GUIA");
                                    }
                                }
                            );
                        },
                        cargarMotivos() {
                            return new Promise((resolve) => {
                                $.ajax({
                                    url: _URL + '/ajs/get/motivos-guia',
                                    type: 'GET',
                                    success: (response) => {
                                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                                        if (data.status) {
                                            this.actualizarSelectMotivos(data.data);
                                        }
                                        resolve();
                                    },
                                    error: () => resolve()
                                });
                            });
                        },

                        actualizarSelectMotivos(motivos) {
                            const select = $('#select_motivo');
                            select.empty();
                            select.append('<option value="">Seleccione un motivo</option>');
                            motivos.forEach(motivo => {
                                select.append(`<option value="${motivo.id}">${motivo.nombre}</option>`);
                            });
                        },

                        cargarDatosGuia(idGuia) {
                            $("#loader-menor").show();
                            _ajax("/ajs/guia/remision/obtener", "POST", { id_guia: idGuia },
                                (resp) => {
                                    $("#loader-menor").hide();
                                    if (resp.res) {
                                        console.log('Datos recibidos:', resp);

                                        // Cargar datos en el formulario
                                        this.guia = { ...this.guia, ...resp.guia };
                                        this.transporte = { ...this.transporte, ...resp.transporte };
                                        this.productos = resp.productos;

                                        // Cargar ubigeo
                                        if (resp.guia.ubigeo) {
                                            this.autocompletarUbigeo(resp.guia.ubigeo);
                                        }

                                        // Cargar motivos y establecer el valor
                                        this.cargarMotivos().then(() => {
                                            this.$nextTick(() => {
                                                if (this.guia.motivo) {
                                                    $('#select_motivo').val(this.guia.motivo);
                                                }
                                            });
                                        });

                                        // OPTIMIZACIÓN: Cargar datos del conductor sin delays
                                        this.$nextTick(() => {
                                            this.cargarDatosConductor(resp.transporte);
                                        });

                                    } else {
                                        alertAdvertencia("Error al cargar los datos de la guía: " + (resp.error || "Error desconocido"));
                                    }
                                }
                            );
                        },

                        // NUEVO MÉTODO: Cargar datos del conductor de forma optimizada
                        cargarDatosConductor(transporte) {
                            if (!transporte.chofer_id && !transporte.veiculo && !transporte.chofer_dni) {
                                return; // No hay datos de conductor para cargar
                            }

                            // Función para intentar cargar los datos
                            const intentarCargarDatos = (intentos = 0) => {
                                const maxIntentos = 10; // Máximo 10 intentos (1 segundo total)

                                // Verificar si los elementos existen
                                const selectChofer = $("#select_chofer");
                                const selectVehiculo = $("#select_vehiculo");
                                const selectLicencia = $("#select_licencia");

                                if (selectChofer.length && selectVehiculo.length && selectLicencia.length) {
                                    // Los elementos existen, cargar los datos
                                    if (transporte.chofer_id) {
                                        selectChofer.val(transporte.chofer_id).trigger('change');
                                    }

                                    if (transporte.veiculo) {
                                        selectVehiculo.val(transporte.veiculo);
                                    }

                                    if (transporte.chofer_dni) {
                                        selectLicencia.val(transporte.chofer_dni);
                                    }

                                    // Actualizar Vue
                                    this.transporte.chofer_id = transporte.chofer_id;
                                    this.transporte.chofer_datos = transporte.chofer_datos;
                                    this.transporte.veiculo = transporte.veiculo;
                                    this.transporte.chofer_dni = transporte.chofer_dni;
                                    this.$forceUpdate();

                                    console.log('Datos del conductor cargados exitosamente');
                                } else if (intentos < maxIntentos) {
                                    // Los elementos no existen aún, intentar de nuevo en 100ms
                                    setTimeout(() => intentarCargarDatos(intentos + 1), 100);
                                } else {
                                    console.warn('No se pudieron cargar los datos del conductor después de', maxIntentos, 'intentos');
                                }
                            };

                            // Iniciar la carga
                            intentarCargarDatos();
                        }




                    },
                    mounted() {
                        // Obtener ID de la guía de la URL
                        const urlParams = new URLSearchParams(window.location.search);
                        const idGuia = urlParams.get('id');

                        // Solo cargar motivos inicialmente
                        this.cargarMotivos().then(() => {
                            if (idGuia) {
                                this.cargarDatosGuia(idGuia);
                            }
                        });

                        this.getDocumentoGuia();
                        obtenerProvincias();
                    }


                });

                // Asignar la instancia de Vue a window para acceso global
                window.app = appguia;

            });
        </script>
        <script src="<?= URL::to('/public/js/guia-remision/motivos.js') ?>?v=<?= time() ?>"></script>
        <script src="<?= URL::to('/public/js/guia-remision/chofer-configuraciones.js') ?>?v=<?= time() ?>"></script>


</body>

</html>