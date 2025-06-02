<!-- resources\views\fragment-views\cliente\guia-remision-add.php -->
<?php
require_once "app/models/Ubigeo.php";
$c_ubigeo = new Ubigeo();
?>
<!DOCTYPE html>
<html>

<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="<?= URL::to('/public/css/guia-remision/styles.css') ?>?v=<?= time() ?>">


</head>

<body>

    <div class="page-title-box">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="page-title">Guía Remisión</h6>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                    <li class="breadcrumb-item"><a href="/ventas" class="button-link">Guía Remisión</a></li>
                    <li class="breadcrumb-item active " aria-current="page" style="color: #CA3438;">Registrar Guia
                        Remisión</li>
                </ol>
            </div>
            <div class="col-md-4">
                <div class="float-end d-none d-md-block">
                    <button id="backbuttonvp" href="/guias/remision" type="button"
                        class="btn bg-white border-rojo text-rojo button-link">
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
                    <div class="card h-100">
                        <div class="card-header bg-rojo text-white">
                            <h5 class="mb-0">
                                <i class="fas fa-file-invoice me-2"></i>Información de Documento
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12">
                                <form role="form" class="form-horizontal">


                                    <h5 class="border-bottom pb-2 ">Datos de la Guía</h5>
                                    <div class="form-group row mb-3">
                                        <label class="col-md-4 col-form-label text-end">Doc.</label>
                                        <div class="col-md-8">
                                            <input type="text" class="form-control text-center" value="GUIA DE REMISION"
                                                readonly name="input_doc_envio">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-lg-4 col-form-label text-end">Ser | Num</label>
                                        <div class="col-lg-4 pe-1">
                                            <input v-model="guia.serie_g" type="text" name="input_serie_guia"
                                                id="input_serie_guia" class="form-control text-center" readonly>
                                        </div>
                                        <div class="col-lg-4 ps-1">
                                            <input v-model="guia.numero_g" type="text" name="input_numero_guia"
                                                id="input_numero_guia" class="form-control text-center" readonly>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-lg-4 col-form-label text-end">Fecha</label>
                                        <div class="col-lg-8">
                                            <input type="date" name="input_fecha" id="input_fecha"
                                                class="form-control text-center" value="<?php echo date("Y-m-d"); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-md-4 col-form-label text-end">Motivo</label>
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <select class="form-select" name="select_motivo" id="select_motivo">
                                                    <option value="">Seleccione un motivo</option>
                                                </select>
                                                <button type="button" class="btn bg-rojo text-white"
                                                    data-bs-toggle="modal" data-bs-target="#motivoModal">
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-lg-4 col-form-label text-end">Peso total</label>
                                        <div class="col-lg-8">
                                            <input v-model="guia.peso" type="text" id="input_peso_total"
                                                class="form-control text-center" value="0">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-lg-4 col-form-label text-end">Nro Bultos</label>
                                        <div class="col-lg-8">
                                            <input v-model="guia.num_bultos" type="text" id="input_nro_bultos"
                                                class="form-control text-center" value="0">
                                        </div>
                                    </div>
                                    <h5 class="border-bottom pb-2">Datos de la Fac - Bol</h5>
                                    <div class="form-group row mb-3">
                                        <label class="col-md-4 col-form-label text-end">Doc.</label>
                                        <div class="col-md-8">
                                            <select v-model="guia.tipo_doc" class="form-select"
                                                name="select_documento_venta" id="select_documento_venta">
                                                <option value="1">BOLETA DE VENTA</option>
                                                <option value="2">FACTURA</option>
                                                <option value="3">ORDEN DE COMPRA</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Campo de Doc. de Referencia -->
                                    <div class="form-group row mb-3" v-show="mostrarDocReferencia">
                                        <label class="col-md-4 col-form-label text-end" for="doc_referencia">
                                            Doc. de Referencia
                                        </label>
                                        <div class="col-md-8">
                                            <input type="text" id="doc_referencia" class="form-control"
                                                v-model="guia.doc_referencia"
                                                placeholder="Ingrese documento de referencia"
                                                :required="mostrarDocReferencia">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <div class="col-lg-12 text-center">
                                            <button type="button" class="btn bg-white text-rojo border-rojo w-100"
                                                @click="comprobarVenta">
                                                <i class="fa fa-search me-2"></i> Comprobar Documento Venta
                                            </button>
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-lg-4 col-form-label text-end">Ser | Num</label>
                                        <div class="col-lg-4 pe-1">
                                            <input v-model="guia.serie" type="text" name="input_serie_venta"
                                                id="input_serie_venta" class="form-control text-center">
                                        </div>
                                        <div class="col-lg-4 ps-1">
                                            <input v-model="guia.numero" type="text" name="input_numero_venta"
                                                id="input_numero_venta" class="form-control text-center">
                                        </div>
                                    </div>
                                    <div class="form-group row mb-3">
                                        <label class="col-lg-4 col-form-label text-end">Total</label>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control text-end" name="input_total_venta"
                                                id="input_total_venta" v-model="guia.total" disabled>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="form-group row mb-3">
                                        <div class="col-12 text-center">
                                            <button type="button" class="btn bg-rojo text-white btn-lg w-100"
                                                @click="registerGuia">
                                                <i class="fas fa-file-export me-2"></i>
                                                Generar Guía
                                            </button>
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
                                    <div class="mb-3 row">
                                        <label class="col-lg-3 col-form-label">
                                            <i class="fas fa-user-plus me-2 text-rojo"></i>Agregar Destinatario
                                        </label>
                                        <div class="col-lg-9">
                                            <div class="input-group">
                                                <input type="text" class="form-control" v-model="guia.doc_cli"
                                                    placeholder="Ingrese Documento" maxlength="11">
                                                <button class="btn bg-rojo text-white" type="button"
                                                    @click="buscarDocumentSS">
                                                    <i class="fas fa-search"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Destinatario -->
                                    <div class="mb-3 row">
                                        <label class="col-lg-3 col-form-label">
                                            <i class="fas fa-user me-2 text-rojo"></i>Destinatario
                                        </label>
                                        <div class="col-lg-9">
                                            <input v-model="guia.nom_cli" type="text" class="form-control">
                                        </div>
                                    </div>

                                    <!-- Punto Partida -->
                                    <div class="mb-3 row">
                                        <label class="col-lg-3 col-form-label">
                                            <i class="fas fa-map-marker-alt me-2 text-rojo"></i>Punto Partida
                                        </label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" v-model="guia.dir_part"
                                                value="AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE">
                                        </div>
                                    </div>

                                    <!-- Punto Llegada -->
                                    <div class="mb-3 row">
                                        <label class="col-lg-3 col-form-label">
                                            <i class="fas fa-flag-checkered me-2 text-rojo"></i>Punto Llegada
                                        </label>
                                        <div class="col-lg-9">
                                            <input type="text" class="form-control" v-model="guia.dir_cli">
                                        </div>
                                    </div>

                                    <!-- Ubigeo -->
                                    <div class="mb-4 row">
                                        <label class="col-lg-3 col-form-label">
                                            <i class="fas fa-map me-2 text-rojo"></i>Ubigeo
                                        </label>
                                        <div class="col-lg-9">
                                            <div class="row g-2">
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
                                        <div
                                            class="section-header bg-rojo text-white p-3 rounded d-flex align-items-center">
                                            <i class="fas fa-id-card me-2"></i>
                                            <h5 class="mb-0">Datos del Conductor</h5>
                                        </div>

                                        <div class="mt-4">
                                            <!-- Transportista -->
                                            <div class="row mb-3">
                                                <label class="col-lg-3 col-form-label">
                                                    <i class="fas fa-truck me-2 text-rojo"></i>Transportista
                                                </label>
                                                <div class="col-lg-4">
                                                    <select v-model="transporte.tipo_trans" class="form-select"
                                                        name="select_tipo_transporte" id="select_tipo_transporte">
                                                        <option value="1">Propio</option>
                                                        <option value="2">Externo</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- Tipo y Número de Documento - REORGANIZADO -->
                                            <div v-show="transporte.tipo_trans === '2'" class="row mb-3">
                                                <label class="col-lg-3 col-form-label">
                                                    <i class="fas fa-file-alt me-2 text-rojo"></i>Documentos
                                                </label>
                                                <div class="col-lg-9">
                                                    <div class="row g-2">
                                                        <div class="col-md-4">
                                                            <label class="form-label small">Tipo DOC</label>
                                                            <select v-model="transporte.tipo_documento"
                                                                class="form-select">
                                                                <option value="DNI">DNI</option>
                                                                <option value="RUC">RUC</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-8">
                                                            <label class="form-label small">N° DOC</label>
                                                            <div class="input-group">
                                                                <input type="text" class="form-control"
                                                                    v-model="transporte.num_docu" maxlength="11">
                                                                <button
                                                                    class="btn bg-rojo text-white d-flex align-items-center"
                                                                    type="button" @click="buscarDocumentoTransporte">
                                                                    <i class="fas fa-check me-1"></i>
                                                                    Verificar
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Datos del Chofer -->
                                            <div class="row mb-3">
                                                <label class="col-lg-3 col-form-label">
                                                    <i class="fas fa-user-tie me-2 text-rojo"></i>Chofer
                                                </label>
                                                <div class="col-lg-9">
                                                    <div class="input-group">
                                                        <select class="form-select" v-model="transporte.chofer_datos"
                                                            id="select_chofer">
                                                            <option value="">Seleccione un chofer</option>
                                                        </select>
                                                        <button class="btn bg-rojo text-white" type="button"
                                                            data-bs-toggle="modal" data-bs-target="#choferModal">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Datos del Vehículo -->
                                            <div class="row mb-3">
                                                <label class="col-lg-3 col-form-label">
                                                    <i class="fas fa-truck me-2 text-rojo"></i>Vehículo
                                                </label>
                                                <div class="col-lg-9">
                                                    <div class="row g-2">
                                                        <div class="col-lg-6">
                                                            <div class="input-group">
                                                                <select class="form-select" v-model="transporte.veiculo"
                                                                    id="select_vehiculo">
                                                                    <option value="">Seleccione un vehículo</option>
                                                                </select>
                                                                <button class="btn bg-rojo text-white" type="button"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#vehiculoModal">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="input-group">
                                                                <select class="form-select"
                                                                    v-model="transporte.chofer_dni"
                                                                    id="select_licencia">
                                                                    <option value="">Seleccione una licencia</option>
                                                                </select>
                                                                <button class="btn bg-rojo text-white" type="button"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#licenciaModal">
                                                                    <i class="fas fa-plus"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Observaciones -->
                                    <div class="mb-3 row">
                                        <label class="col-lg-3 col-form-label">
                                            <i class="fas fa-comment-alt me-2 text-rojo"></i>Observaciones
                                        </label>
                                        <div class="col-lg-9">
                                            <textarea v-model="guia.observacion" class="form-control"
                                                rows="3"></textarea>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Productos -->
            <div class="col-md-12 mt-4">
                <div class="card">
                    <div class="card-header bg-rojo text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-box me-2"></i>Detalle Venta
                            </h5>
                            <!-- <button type="button" class="btn btn-light" data-bs-toggle="modal"
                                data-bs-target="#modalBuscarProductos">
                                <i class="fas fa-plus me-2"></i>Agregar Producto
                            </button> -->
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover text-center table-sm">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <!-- <th>P. Unit.</th> -->
                                        <!-- <th>Parcial</th> -->
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item,index) in productos">
                                        <td>{{index+1}}</td>
                                        <td class="text-start">{{item.nombre}}</td>
                                        <td>{{item.cantidad}}</td>
                                        <!-- <td>{{parseFloat(item.precio).toFixed(2)}}</td> -->
                                        <!-- <td>{{subTotalPro(item.cantidad,item.precio)}}</td> -->
                                        <td>
                                            <div class="d-flex gap-2 justify-content-center">
                                                <!-- Botón Editar -->
                                                <button @click="editarProducto(index)" class="btn btn-warning btn-sm"
                                                    title="Editar">
                                                    <i class="fa fa-edit text-white"></i>
                                                </button>
                                                <!-- Botón Eliminar (el que ya existe) -->
                                                <button @click="eliminarProducto(index)" class="btn btn-danger btn-sm"
                                                    title="Eliminar">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr v-if="!productos || productos.length === 0">
                                        <td colspan="4" class="text-center py-3 text-muted">
                                            No hay productos agregados
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Modal Motivo -->
            <div class="modal fade" id="motivoModal" tabindex="-1" aria-labelledby="motivoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="motivoModalLabel">Gestionar Motivos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="motivoForm">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nombreMotivo"
                                            placeholder="Nombre del Motivo">
                                        <button type="submit" class="btn bg-rojo text-white">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <h6 class="fw-bold">Motivos Existentes</h6>
                            <ul id="listaMotivos" class="list-group mt-3">
                                <!-- Los motivos se cargarán aquí dinámicamente -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Chofer -->
            <div class="modal fade" id="choferModal" tabindex="-1" aria-labelledby="choferModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="choferModalLabel">Gestionar Choferes</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="choferForm">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="nombreChofer"
                                            placeholder="Nombre del Chofer">
                                        <input type="hidden" id="choferId">
                                        <button type="submit" class="btn bg-rojo text-white">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <h6 class="fw-bold">Choferes Existentes</h6>
                            <ul id="listaChoferes" class="list-group mt-3">
                                <!-- Los choferes se cargarán aquí dinámicamente -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Vehículo -->
            <div class="modal fade" id="vehiculoModal" tabindex="-1" aria-labelledby="vehiculoModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="vehiculoModalLabel">Gestionar Vehículos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="vehiculoForm">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="placaVehiculo"
                                            placeholder="Placa del Vehículo">
                                        <input type="hidden" id="vehiculoId">
                                        <button type="submit" class="btn bg-rojo text-white">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <h6 class="fw-bold">Vehículos Existentes</h6>
                            <ul id="listaVehiculos" class="list-group mt-3">
                                <!-- Los vehículos se cargarán aquí dinámicamente -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal Licencia -->
            <div class="modal fade" id="licenciaModal" tabindex="-1" aria-labelledby="licenciaModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="licenciaModalLabel">Gestionar Licencias</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="licenciaForm">
                                <div class="mb-3">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="numeroLicencia"
                                            placeholder="Número de Licencia">
                                        <input type="hidden" id="licenciaId">
                                        <button type="submit" class="btn bg-rojo text-white">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <hr>
                            <h6 class="fw-bold">Licencias Existentes</h6>
                            <ul id="listaLicencias" class="list-group mt-3">
                                <!-- Las licencias se cargarán aquí dinámicamente -->
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal para buscar productos -->
            <div class="modal fade" id="modalBuscarProductos" tabindex="-1" aria-labelledby="modalBuscarProductosLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
                            <h5 class="modal-title" id="modalBuscarProductosLabel">Buscar Productos</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form v-on:submit.prevent="addProduct" class="form-horizontal">
                                <div class="form-group row mb-3">
                                    <label class="col-lg-2 col-form-label">Buscar</label>
                                    <div class="col-lg-10">
                                        <div class="input-group">
                                            <input type="text" placeholder="Buscar por código o nombre del producto"
                                                class="form-control" id="input_buscar_productos" autocomplete="off">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row mb-3">
                                    <label class="col-lg-2 col-form-label">Descripción</label>
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
                                            <select class="form-select" v-model="producto.precio">
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
                                    <button type="button" class="btn bg-rojo text-white" @click="addProduct">
                                        <i class="fa fa-check me-2"></i> Agregar Producto
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Edición -->
            <div class="modal fade" id="modalEditarProducto" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-rojo text-white">
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
                                <select class="form-select" v-model="productoEdit.precio">
                                    <option v-for="(value, key) in precioProductos" :value="value.precio" :key="key">
                                        {{ value.precio }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" class="btn bg-rojo text-white" @click="actualizarProducto">
                                <i class="fas fa-save me-2"></i> Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
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
                    mostrarDocReferencia: false,
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
                        chofer_id: '',
                        chofer_datos: '',
                        num_doc: '',
                        num_docu: ''
                    }
                },
                watch: {
                    'guia.tipo_doc': function (newVal) {
                        // Mostrar Doc. de Referencia solo cuando se selecciona Orden de Compra (valor '3')
                        this.mostrarDocReferencia = newVal === '3';
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
                        const cotizacion = document.getElementById('cotizacion').value;
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
                            fecha_emision: $("#input_fecha").val(),
                            chofer_datos: this.transporte.chofer_datos,
                            cotizacion: cotizacion,
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
                        //cargar datos deñ cliente
                        _ajax("/ajs/guia/remision/coti/cliente/" + cotiId, "POST", { cod: cotiId },
                            (resp) => {
                                console.log('Cliente:', resp);

                                if (resp.error) {

                                    consola.log(resp.error);
                                    return;
                                }
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
                    }

                },
                mounted() {
                    this.getDocumentoGuia();
                    obtenerProvincias();
                    const cotiId = document.getElementById('cotizacion').value;
                    if (cotiId) {
                        this.loadCotizacionData(cotiId);
                    }
                }
            });

            // Asignar la instancia de Vue a window para acceso global
            window.app = appguia;

        });
    </script>
    <script src="<?= URL::to('/public/js/guia-remision/motivos.js') ?>"></script>
    <script src="<?= URL::to('/public/js/guia-remision/conductor.js') ?>"></script>
    <script src="<?= URL::to('/public/js/guia-remision/choferes.js') ?>"></script>

</body>

</html>