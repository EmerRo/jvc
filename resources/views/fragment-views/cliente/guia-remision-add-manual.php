<!-- resources\views\fragment-views\cliente\guia-remision-add-manual.php -->
<?php
require_once "app/models/Ubigeo.php";
$c_ubigeo = new Ubigeo();
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="<?= URL::to('/public/css/guia-remision/styles.css') ?>?v=<?= time() ?>">
<link rel="stylesheet" href="<?= URL::to('/public/css/styles-globals.css') ?>?v=<?= time() ?>">



<!-- guia de remision manual -->
<div class="page-title-box">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h6 class="page-title">Guía Remisión Manual</h6>
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Facturación</a></li>
                <li class="breadcrumb-item"><a href="/ventas" class="button-link">Guía Remisión</a></li>
                <li class="breadcrumb-item active" aria-current="page" style="color: #CA3438">Registrar Guia Remisión
                    Manual</li>
            </ol>
        </div>
        <div class="col-md-4">
            <div class="float-end d-none d-md-block">
                <button id="backbuttonvp" href="/guias/remision" type="button"
                    class="btn border-rojo text-rojo bg-white button-link">
                    <i class="fa fa-arrow-left"></i> Regresar
                </button>
            </div>
        </div>
    </div>
</div>

<div class="row" id="container-vue">


    <div>

        <input type="hidden" id="fecha-now-app" value="<?php echo date("Y-m-d"); ?>">
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-rojo text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-invoice me-2"></i>Información de Documento
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">

                            <form role="form" class="form-horizontal">
                                <h5 class="border-bottom pb-2">Datos de la Guía</h5>
                                <div class="form-group row mb-3">
                                    <label class="col-md-4 control-label text-end">Doc.</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control text-center" value="GUIA DE REMISION"
                                            readonly name="input_doc_envio">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Ser | Num</label>
                                    <div class="col-lg-4 pe-1">
                                        <input v-model="guia.serie_g" type="text" name="input_serie_guia"
                                            id="input_serie_guia" class="form-control text-center">
                                    </div>
                                    <div class="col-lg-4 ps-1">
                                        <input v-model="guia.numero_g" type="text" name="input_numero_guia"
                                            id="input_numero_guia" class="form-control text-center">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Fecha</label>
                                    <div class="col-lg-8">
                                        <input type="date" name="input_fecha" id="input_fecha"
                                            class="form-control text-center" value="<?php echo date("Y-m-d"); ?>">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-md-4 control-label text-end">Motivo.</label>
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <select class="form-select" name="select_motivo" id="select_motivo">
                                                <option value="">Seleccione un motivo</option>
                                            </select>
                                            <button type="button" class="btn bg-rojo text-white" data-bs-toggle="modal"
                                                data-bs-target="#motivoModal">
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Peso total</label>
                                    <div class="col-lg-8">
                                        <input v-model="guia.peso" type="text" id="input_peso_total"
                                            class="form-control text-center" value="0">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Nro Bultos</label>
                                    <div class="col-lg-8">
                                        <input v-model="guia.num_bultos" type="text" id="input_nro_bultos"
                                            class="form-control text-center" value="0">
                                    </div>
                                </div>
                                <h5 class="border-bottom pb-2">Datos de la Fac - Bol</h5>
                                <div class="form-group row mb-3">
                                    <label class="col-md-4 control-label text-end">Doc.</label>
                                    <div class="col-md-8">
                                        <select v-model="guia.tipo_doc" class="form-select"
                                            name="select_documento_venta" id="select_documento_venta">
                                            <option value="1">BOLETA DE VENTA</option>
                                            <option value="2">FACTURA</option>
                                            <option value="3">ORDEN DE COMPRA</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- ✅ CORREGIDO: Campo de ref_orden_compra -->
                                   <div class="form-group row mb-3" v-show="mostrarDocReferencia">
                                        <label class="col-md-4 col-form-label text-end" for="ref_orden_compra">
                                            {{ guia.tipo_doc === '3' ? 'Nro. Orden Compra' : 'Doc. de Referencia' }}
                                        </label>
                                        <div class="col-md-8">
                                            <input type="text" id="ref_orden_compra" class="form-control"
                                                v-model="guia.ref_orden_compra"
                                                :placeholder="guia.tipo_doc === '3' ? 'Ingrese número de orden de compra' : 'Ingrese documento de referencia'"
                                                :required="mostrarDocReferencia">
                                        </div>
                                    </div>
                                <div class="form-group row mb-3">
                                    <div class="col-lg-12 text-center">
                                        <button type="button" class="btn bg-rojo text-white" @click="comprobarVenta">
                                            <i class="fa fa-search"></i> Comprobar Documento Venta
                                        </button>
                                        <input type="hidden" name="input_id_venta_referencia"
                                            id="input_id_venta_referencia">
                                    </div>
                                </div>
                                <div class="form-group row mb-3">
                                    <label class="col-lg-4 control-label text-end">Ser | Num</label>
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
                                    <label class="col-lg-4 control-label text-end">Total</label>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control text-end" name="input_total_venta"
                                            id="input_total_venta" v-model="guia.total" disabled>
                                    </div>
                                </div>
                                <hr>

                                <hr>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
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
                                            placeholder="Nombre del Motivo" required>
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


            <div class="col-md-8">
                <div class="card">
                    <!-- Cambio de título a Detalle de Guía de Remisión -->
                    <div class="card-header bg-rojo text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-file-alt me-2"></i>Detalle de Guía de Remisión
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="col-md-12">
                            <form class="form-horizontal needs-validation" novalidate>
                                <!-- Agregar Destinatario -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-user-plus me-2"></i>Agregar Destinatario
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
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-user me-2"></i>Destinatario
                                    </label>
                                    <div class="col-lg-9">
                                        <input v-model="guia.nom_cli" type="text" class="form-control"
                                            id="input_datos_destinatario">
                                    </div>
                                </div>

                                <!-- Punto Partida -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-map-marker-alt me-2"></i>Punto Partida
                                    </label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" v-model="guia.dir_part"
                                            id="input_dir_partida" placeholder="Dirección de partida"
                                            value="AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE">
                                    </div>
                                </div>
                                <!-- Punto Llegada -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-flag-checkered me-2"></i>Punto Llegada
                                    </label>
                                    <div class="col-lg-9">
                                        <input type="text" class="form-control" v-model="guia.dir_cli"
                                            id="input_dir_llegada" placeholder="Dirección de llegada">
                                    </div>
                                </div>

                                <!-- Ubigeo -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-map me-2"></i>Ubigeo
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
                                                    id="select_provincia" onchange="obtenerDistritos()">
                                                </select>
                                            </div>
                                            <div class="col-lg-4">
                                                <select class="form-select" name="select_distrito" id="select_distrito">
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
                                        <!-- Transportista movido aquí -->
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">
                                                <i class="fas fa-truck me-2"></i>Transportista
                                            </label>
                                            <div class="col-lg-4">
                                                <select v-model="transporte.tipo_trans" class="form-select"
                                                    name="select_tipo_transporte" id="select_tipo_transporte">
                                                    <option value="1">Propio</option>
                                                    <option value="2">Externo</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Tipo y Número de Documento (REORGANIZADO) -->
                                        <div v-show="transporte.tipo_trans === '2'" class="row mb-4">
                                            <label class="col-lg-3 col-form-label">
                                                <i class="fas fa-file-alt me-2"></i>Documentos
                                            </label>
                                            <div class="col-lg-9">
                                                <div class="row g-2">
                                                    <div class="col-md-4">
                                                        <label class="form-label small">Tipo DOC</label>
                                                        <select v-model="transporte.tipo_documento" class="form-select">
                                                            <option value="DNI">DNI</option>
                                                            <option value="RUC">RUC</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <label class="form-label small">N° DOC</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control"
                                                                placeholder="N° Documento" v-model="transporte.num_docu"
                                                                maxlength="11">
                                                            <button
                                                                class="btn border-rojo text-rojo bg-white d-flex align-items-center"
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
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">
                                                <i class="fas fa-user-tie me-2"></i>Chofer
                                            </label>
                                            <div class="col-lg-9">
                                                <div class="input-group">
                                                    <!-- REMOVIDO v-model para evitar conflicto con jQuery -->
                                                    <select class="form-select" id="select_chofer">
                                                        <option value="">Seleccione un chofer</option>
                                                    </select>
                                                    <button class="btn bg-rojo " type="button" data-bs-toggle="modal"
                                                        data-bs-target="#choferModal">
                                                        <i class="fas fa-plus text-white"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Datos del Vehículo -->
                                        <div class="row mb-4">
                                            <label class="col-lg-3 col-form-label">
                                                <i class="fas fa-truck me-2"></i>Vehículo
                                            </label>
                                            <div class="col-lg-9">
                                                <div class="row g-2">
                                                    <div class="col-lg-6">
                                                        <div class="input-group">
                                                            <select class="form-select" v-model="transporte.veiculo"
                                                                id="select_vehiculo">
                                                                <option value="">Seleccione un vehículo</option>
                                                            </select>
                                                            <!-- <button class="btn bg-rojo" type="button"
                                                                data-bs-toggle="modal" data-bs-target="#vehiculoModal">
                                                                <i class="fas fa-plus text-white"></i>
                                                            </button> -->
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-6">
                                                        <div class="input-group">
                                                            <select class="form-select" v-model="transporte.chofer_dni"
                                                                id="select_licencia">
                                                                <option value="">Seleccione una licencia</option>
                                                            </select>
                                                            <!-- <button class="btn bg-rojo" type="button"
                                                                data-bs-toggle="modal" data-bs-target="#licenciaModal">
                                                                <i class="fas fa-plus text-white"></i>
                                                            </button> -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>




                                <!-- Observaciones -->
                                <div class="mb-4 row">
                                    <label class="col-lg-3 col-form-label">
                                        <i class="fas fa-comment-alt me-2"></i>Observaciones
                                    </label>
                                    <div class="col-lg-9">
                                        <textarea v-model="guia.observacion" class="form-control"
                                            id="input_datos_observaciones" rows="3"
                                            placeholder="Ingrese sus observaciones aquí"></textarea>
                                    </div>
                                </div>


                            </form>
                        </div>
                    </div>

                    <!-- Productos Section -->
                    <div class="card-header bg-rojo text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-box me-2"></i>Productos</h5>
                            <button type="button" class="btn bg-white text-rojo border-rojo" data-bs-toggle="modal"
                                data-bs-target="#modalBuscarProductos">
                                <i class="fas fa-search me-2 text-rojo"></i>Buscar Productos
                            </button>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer bg-light">
                        <button @click="registerGuia" type="button" class="btn bg-rojo text-white btn-lg">
                            <i class="fas fa-save me-2"></i>Generar Guía
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Chofer Mejorado -->
        <div class="modal fade" id="choferModal" tabindex="-1" aria-labelledby="choferModalLabel" aria-hidden="true">
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
                    <div class="modal-header bg-rojo text-white">
                        <h5 class="modal-title" id="modalBuscarProductosLabel">Buscar Productos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>
                    <div class="modal-body position-relative"> <!-- Añadido position-relative -->
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
                                    <input required v-model="producto.descripcion" type="text" placeholder="Descripción"
                                        class="form-control" readonly="true">
                                </div>
                            </div>

                            <div class="form-group row mb-3">
                                <div class="col-lg-12 d-flex align-items-end gap-3">
                                    <div class="flex-grow-1">
                                        <label for="example-text-input" class="col-form-label">Stock Actual</label>
                                        <input id="example-text-input" disabled v-model="producto.stock"
                                            class="form-control text-center" type="text" placeholder="0">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label for="cantidad-input" class="col-form-label">Cantidad</label>
                                        <input id="cantidad-input" @keypress="onlyNumber" required
                                            v-model="producto.cantidad" class="form-control text-center" type="text"
                                            placeholder="0">
                                    </div>
                                    <div class="flex-grow-1">
                                        <label for="precio-input" class="col-form-label">Precio</label>
                                        <div class="input-group">
                                            <input id="precio-input" type="text" class="form-control dropdown-toggle"
                                                :class="{'dropdown-toggle': producto.descripcion.length > 0}"
                                                :data-bs-toggle="producto.descripcion.length > 0 ? 'dropdown' : ''"
                                                aria-expanded="false" v-model="producto.precio_mostrado"
                                                style="background-color: #f8f9fa; cursor: pointer;"
                                                @click="mostrarMensajeProducto" readonly>

                                            <!-- Dropdown para precios base -->
                                            <ul class="dropdown-menu" style="width: 300px; max-width: none;">
                                                <li class="dropdown-header bg-light py-2 px-3">Precios Base</li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        @click.prevent="seleccionarPrecioConTipo('PV', producto.precioVenta)">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span><i class="fa fa-tag me-2"></i>Precio Venta:</span>
                                                            <span class="badge bg-success text-white rounded-pill">S/ {{
                                                                producto.precioVenta }}</span>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        @click.prevent="seleccionarPrecioConTipo('C', producto.costo)">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span><i class="fa fa-box me-2"></i>Costo:</span>
                                                            <span class="badge bg-success text-white rounded-pill">S/ {{
                                                                producto.costo }}</span>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        @click.prevent="seleccionarPrecioConTipo('PM', producto.precio_mayor)">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span><i class="fa fa-warehouse me-2"></i>Precio
                                                                Mayorista:</span>
                                                            <span class="badge bg-success text-white rounded-pill">S/ {{
                                                                producto.precio_mayor }}</span>
                                                        </div>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        @click.prevent="seleccionarPrecioConTipo('PMn', producto.precio_menor)">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span><i class="fa fa-store me-2"></i>Precio
                                                                Minorista:</span>
                                                            <span class="badge bg-success text-white rounded-pill">S/ {{
                                                                producto.precio_menor }}</span>
                                                        </div>
                                                    </a>
                                                </li>
                                            </ul>

                                            <button class="btn bg-rojo text-white dropdown-toggle" type="button"
                                                :data-bs-toggle="producto.descripcion.length > 0 ? 'dropdown' : ''"
                                                aria-expanded="false" @click="mostrarMensajeProducto">
                                                <i class="fa fa-chevron-down"></i>
                                            </button>

                                            <!-- Dropdown para precios adicionales -->
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                style="width: 300px; max-width: none;">
                                                <li class="dropdown-header bg-light py-2 px-3">Precios Adicionales</li>
                                                <template v-if="precioProductos && precioProductos.length > 0">
                                                    <li v-for="(value, key) in precioProductos" :key="key">
                                                        <a class="dropdown-item" href="#"
                                                            @click.prevent="seleccionarPrecioConTipo(value.nombre, value.precio)">
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <span><i class="fa fa-tag me-2"></i>{{ value.nombre
                                                                    }}:</span>
                                                                <span
                                                                    class="badge bg-success text-white rounded-pill">S/
                                                                    {{ value.precio }}</span>
                                                            </div>
                                                        </a>
                                                    </li>
                                                </template>
                                                <li v-if="!precioProductos || precioProductos.length === 0">
                                                    <div class="dropdown-item text-center text-muted py-2">
                                                        <i class="fa fa-info-circle me-1"></i> No hay precios
                                                        adicionales
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn bg-white text-rojo border-rojo"
                            data-bs-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn bg-rojo text-white" @click="addProduct">
                            <i class="fa fa-check me-2"></i> Agregar Producto
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal de Edición -->
        <div class="modal fade" id="modalEditarProducto" tabindex="-1">
            <div class="modal-dialog">
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
                        <!-- Detalle del producto -->
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" id="detalle" v-model="productoEdit.detalle"
                                rows="3"></textarea>
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
                            <div class="input-group">
                                <input type="text" class="form-control dropdown-toggle" data-bs-toggle="dropdown"
                                    aria-expanded="false" v-model="productoEdit.precio_mostrado"
                                    style="background-color: #f8f9fa; cursor: pointer;" readonly>

                                <!-- Dropdown para precios base -->
                                <ul class="dropdown-menu w-100 shadow">
                                    <li class="dropdown-header bg-light py-2 px-3">Precios Base</li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            @click.prevent="seleccionarPrecioEditConTipo('PV', productoEdit.precioVenta)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><i class="fa fa-tag me-2"></i>Precio Venta:</span>
                                                <span class="badge bg-success text-white rounded-pill">S/ {{
                                                    productoEdit.precioVenta }}</span>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            @click.prevent="seleccionarPrecioEditConTipo('C', productoEdit.costo)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><i class="fa fa-box me-2"></i>Costo:</span>
                                                <span class="badge bg-success text-white rounded-pill">S/ {{
                                                    productoEdit.costo }}</span>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            @click.prevent="seleccionarPrecioEditConTipo('PM', productoEdit.precio_mayor)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><i class="fa fa-warehouse me-2"></i>Precio Mayorista:</span>
                                                <span class="badge bg-success text-white rounded-pill">S/ {{
                                                    productoEdit.precio_mayor }}</span>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            @click.prevent="seleccionarPrecioEditConTipo('PMn', productoEdit.precio_menor)">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span><i class="fa fa-store me-2"></i>Precio Minorista:</span>
                                                <span class="badge bg-success text-white rounded-pill">S/ {{
                                                    productoEdit.precio_menor }}</span>
                                            </div>
                                        </a>
                                    </li>
                                </ul>

                                <button class="btn bg-rojo text-white dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fa fa-chevron-down"></i>
                                </button>

                                <!-- Dropdown para precios adicionales -->
                                <ul class="dropdown-menu dropdown-menu-end shadow">
                                    <li class="dropdown-header bg-light py-2 px-3">Precios Adicionales</li>
                                    <template v-if="preciosAdicionales && preciosAdicionales.length > 0">
                                        <li v-for="(value, key) in preciosAdicionales" :key="key">
                                            <a class="dropdown-item" href="#"
                                                @click.prevent="seleccionarPrecioEditConTipo(value.nombre, value.precio)">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span><i class="fa fa-tag me-2"></i>{{ value.nombre }}:</span>
                                                    <span class="badge bg-success text-white rounded-pill">S/ {{
                                                        value.precio }}</span>
                                                </div>
                                            </a>
                                        </li>
                                    </template>
                                    <li v-if="!preciosAdicionales || preciosAdicionales.length === 0">
                                        <div class="dropdown-item text-center text-muted py-2">
                                            <i class="fa fa-info-circle me-1"></i> No hay precios adicionales
                                        </div>
                                    </li>
                                </ul>
                            </div>
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

        <div class="col-md-12 mt-4">
            <div class="card">
                <div class="card-header bg-rojo text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-box me-2"></i>Detalle Venta
                    </h5>
                </div>
                <div class="card-body table-responsive">
                    <table class="table text-center table-sm">
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
                                <td class="text-start">{{item.descripcion}}</td>
                                <td>{{item.cantidad}}</td>
                                <!-- <td>{{item.precio}}</td> -->
                                <!-- <td>{{subTotalPro(item.cantidad,item.precio)}}</td> -->
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <!-- Botón Editar -->
                                        <button @click="editarProducto(index)" class="btn btn-warning btn-sm text-white"
                                            title="Editar">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                        <!-- Botón Eliminar (el que ya tenías) -->
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
        var appguia = new Vue({
            el: "#container-vue",
            data: {
                guia: {
                    fecha_emision: $("#fecha-now-app").val(),
                    tipo_doc: '3',
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
                    ref_orden_compra: '', // ✅ CORREGIDO: Campo correcto
                    peso: '1',
                    num_bultos: '1',

                },
                mostrarDocReferencia: true,
                producto: {
                    editable: false,
                    productoid: "",
                    descripcion: "",
                    detalle: "",
                    nom_prod: "",
                    cantidad: "",
                    stock: "",
                    precio: "",
                    codigo: "",
                    codigo_pp: "",
                    codsunat: "",
                    precio: '1',
                    almacen: '<?php echo $_SESSION["sucursal"] ?>',
                    precio2: '',
                    // precio3: '',
                    // precio4: '',
                    precio_unidad: '',
                    precio_usado: 1,
                    precio_mostrado: '',
                    tipo_precio: 'PV',
                    precioVenta: '',
                    costo: '',
                    precio_mayor: '',
                    precio_menor: '',
                    unidad_id: ''
                },
                productoEdit: {
                    index: -1,
                    descripcion: '',
                    cantidad: '',
                    detalle: '',
                    precio: '',
                    stock: '',
                    productoid: '',
                    precio_mostrado: '',
                    precioVenta: '',
                    costo: '',
                    precio_mayor: '',
                    precio_menor: '',
                    nom_prod: '',      // ← Asegurar que esté aquí
                    codigo_pp: '',     // ← Asegurar que esté aquí
                    // unidad_id
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
                    chofer_id: '', // AGREGADO: Para almacenar el ID del chofer
                    num_docu: ''

                },
                mensajeProductoVisible: false,
                preciosAdicionales: [],

                // productos: []
            },

            watch: {
                'guia.tipo_doc': function (newVal) {
                    // Mostrar Doc. de Referencia solo cuando se selecciona Orden de Compra (valor '3')
                    this.mostrarDocReferencia = newVal === '3';
                }
            },
            computed: {
                // Esta propiedad computed se ejecuta automáticamente cuando cambia guia.tipo_doc
                deberíaMostrarDocReferencia() {
                    return this.guia.tipo_doc === '3';
                }
            },

            mounted() {
                this.getDocumentoGuia();
                obtenerProvincias();
                
                // ✅ CRÍTICO: Establecer el estado inicial del campo Doc. de Referencia
                this.mostrarDocReferencia = this.guia.tipo_doc === '3';
                
                // Recuperar productos guardados si existen
                const productosGuardados = localStorage.getItem('productosGuiaRemision');
                if (productosGuardados) {
                    this.productos = JSON.parse(productosGuardados);
                }
            },

            methods: {
                onlyNumber($event) {
                    //console.log($event.keyCode); //keyCodes value
                    let keyCode = ($event.keyCode ? $event.keyCode : $event.which);
                    if ((keyCode < 48 || keyCode > 57) && keyCode !== 46) { // 46 is dot
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


                registerGuia() {
                    // Validaciones básicas
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

                    // Asegurarnos que cada producto tenga el idproducto
                    const productosValidados = this.productos.map(prod => ({
                        ...prod,
                        idproducto: prod.idproducto || prod.productoid // Aseguramos que exista idproducto
                    }));

                    // ✅ CRÍTICO: Preparar los datos para enviar incluyendo tipo_doc
                    const data = {
                        ...this.guia,
                        ...this.transporte,
                        productos: JSON.stringify(productosValidados),
                        ubigeo: $("#select_distrito").val(),
                        motivo: $("#select_motivo").val(),
                        fecha_emision: $("#input_fecha").val(),
                        tipo_doc: this.guia.tipo_doc // ✅ AGREGAR ESTA LÍNEA CRÍTICA
                    };

                    console.log('Datos enviados:', data); // Para debug

                    $("#loader-menor").show();
                    _ajax("/ajs/guia/remision/add3", "POST", data,
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
                    return (parseFloat(cnt + "") * parseFloat(precio + "")).toFixed(2)
                },
                comprobarRucTransporte() {
                    var vue = this
                    $("#loader-menor").show();
                    _ajax("/ajs/consulta/doc/cliente", "POST", {
                        doc: this.transporte.ruc
                    },
                        function (resp) {
                            $("#loader-menor").hide()
                            console.log(resp);
                            if (resp.res) {
                                vue.transporte.razon_social = (resp.data.razon_social ? resp.data.razon_social : '')
                                _ajax("/ajs/consulta/add/dtatranspor", "POST", {
                                    ruc: resp.data.ruc,
                                    razon: (resp.data.razon_social ? resp.data.razon_social : ''),
                                    direccion: resp.data.direccion
                                })
                            } else {
                                alertAdvertencia("RUC no enocntrado")
                            }
                        }
                    )

                },
                buscarDocumentoTransporte() {
                    // Verifica que el número de documento no esté vacío
                    if (!this.transporte.num_docu) {
                        alert("Ingrese un número de documento");
                        return;
                    }

                    const docLength = this.transporte.num_docu.length; // Usar num_docu
                    $("#loader-menor").show(); // Muestra un loader (cargando)

                    // Realiza la llamada AJAX
                    _ajax("/ajs/prealerta/doc/cliente", "POST", {
                        doc: this.transporte.num_docu // Usar el número de documento ingresado
                    }, (resp) => {
                        $("#loader-menor").hide(); // Oculta el loader

                        // Maneja la respuesta
                        if (docLength === 8) { // Si es un DNI
                            if (resp.success) {
                                // Actualiza el nombre del chofer
                                this.transporte.chofer_datos = resp.nombres + ' ' + (resp.apellidoPaterno || '') + ' ' + (resp.apellidoMaterno || '');
                            } else {
                                alert("Documento no encontrado");
                                this.transporte.chofer_datos = ''; // Limpiar campo si no se encuentra
                            }
                        } else if (docLength === 11) { // Si es un RUC
                            if (resp.razonSocial) {
                                // Actualiza el nombre del chofer (asumiendo que el RUC tiene un nombre asociado)
                                this.transporte.chofer_datos = resp.razonSocial;
                            } else {
                                alert("RUC no encontrado");
                                this.transporte.chofer_datos = ''; // Limpiar campo si no se encuentra
                            }
                        } else {
                            alert("Documento debe ser 8 dígitos (DNI) o 11 dígitos (RUC)");
                            this.transporte.chofer_datos = ''; // Limpiar campo si no es válido
                        }
                    });
                },
                getDocumentoGuia() {
                    var vue = this;
                    _ajax("/ajs/consulta/sn", "POST", {
                        doc: '11'
                    },
                        function (resp) {
                            vue.guia.numero_g = resp.numero
                            vue.guia.serie_g = resp.serie
                        }
                    )
                },
                comprobarVenta() {
                    var vue = this;
                    var data = {
                        idtido: this.guia.tipo_doc,
                        serie: this.guia.serie,
                        numero: this.guia.numero,
                    }
                    $("#loader-menor").show();
                    _ajax("/ajs/consulta/guia/documentofb", "POST",
                        data,
                        function (resp) {
                            console.log(resp);
                            if (resp.res) {
                                alertExito("Documento encontrado")
                                vue.productos = resp.productos
                                vue.guia.venta = resp.idventa
                                vue.guia.doc_cli = resp.doc_cliente
                                vue.guia.nom_cli = resp.nom_cliente
                                vue.guia.dir_cli = resp.dir_cliente
                                vue.guia.total = resp.total
                            } else {
                                alertAdvertencia(resp.msg)
                            }
                        }
                    )
                },
                addProduct() {
                    if (!this.producto) {
                        alertAdvertencia("Error: El producto no está inicializado");
                        return;
                    }

                    // Validación de campos requeridos
                    if (!this.producto.descripcion || !this.producto.cantidad) {
                        alertAdvertencia("Por favor, complete todos los campos requeridos.");
                        return;
                    }

                    // Validación de valores válidos
                    if (this.producto.descripcion.length > 0 && this.producto.cantidad > 0) {
                        const prod = {
                            // descripcion: this.producto.descripcion,
                            descripcion: this.producto.nom_prod || this.producto.detalle, // ← Solo el nombre/detalle
                            detalle: this.producto.detalle,
                            cantidad: this.producto.cantidad,
                            precio: this.producto.precio || '0',
                            stock: this.producto.stock || '0',
                            codigo: this.producto.codigo || '',
                            productoid: this.producto.productoid || '',
                            precioVenta: this.producto.precioVenta || this.producto.precio || '0',
                            costo: this.producto.costo || '0',
                            precio_mayor: this.producto.precio_mayor || '0',
                            precio_menor: this.producto.precio_menor || '0',
                            tipo: this.producto.tipo || 'producto', // También agregar el tipo
                            codigo_pp: this.producto.codigo_pp || '',
                            precio_mostrado: this.producto.precio_mostrado || this.producto.precio || '0',
                            unidad_id: this.producto.unidad_id || ''
                        };
                        console.log(prod.descripcion);
                        console.log(prod.detalle);
                        console.log(prod);

                        // Agregar el producto al array
                        this.productos.push(prod);

                        // Cerrar el modal y limpiar el formulario
                        $('#modalBuscarProductos').modal('hide');

                        // Limpiar los campos después de agregar
                        this.producto = {
                            editable: false,
                            productoid: "",
                            descripcion: "",
                            detalle: "",
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
                            precio_usado: 1,
                            precio_mostrado: '',
                            tipo_precio: 'PV',
                            precio_mayor: '',
                            precio_menor: '',
                            tipo: 'producto',
                            unidad_id: ''
                        };
                        this.precioProductos = [];

                        // Mostrar mensaje de éxito
                        alertExito("Producto agregado correctamente");
                    } else {
                        alertAdvertencia("Por favor, complete todos los campos requeridos.");
                    }
                },
                editarProducto(index) {
                    const producto = this.productos[index];

                    // Separar código y nombre si están juntos
                    let codigo = '';
                    let nombre = '';
                    if (producto.descripcion && producto.descripcion.includes(' | ')) {
                        const [codigoNombre, ...resto] = producto.descripcion.split(' | ');
                        codigo = codigoNombre.trim();
                        nombre = resto.join(' | ').trim();
                    } else {
                        codigo = producto.codigo_pp || '';
                        nombre = producto.nom_prod || producto.descripcion || '';
                    }

                    // Copiar TODOS los campos necesarios
                    this.productoEdit = {
                        index: index,
                        guia_detalle_id: producto.guia_detalle_id,
                        descripcion: producto.descripcion,
                        detalle: producto.detalle || '', // Asegurar que no sea undefined
                        cantidad: producto.cantidad,
                        precio: producto.precio,
                        stock: producto.stock,
                        productoid: producto.productoid,
                        codigo_pp: codigo,
                        nom_prod: nombre,
                        precio_mostrado: producto.precio_mostrado || producto.precio,
                        precioVenta: producto.precioVenta || producto.precio,
                        costo: producto.costo || "0.00",
                        precio_mayor: producto.precio_mayor || "0.00",
                        precio_menor: producto.precio_menor || "0.00",
                        unidad_id: producto.unidad_id || ''
                    };

                    // Cargar precios adicionales si existe productoid
                    if (producto.productoid) {
                        cargarPreciosAdicionalesEdit(producto.productoid, producto.tipo || 'producto');
                    }

                    // Abrir el modal
                    const modal = new bootstrap.Modal(document.getElementById('modalEditarProducto'));
                    modal.show();
                },


                actualizarProducto() {
                    // Validar cantidad
                    if (!this.productoEdit.cantidad || this.productoEdit.cantidad <= 0) {
                        alertAdvertencia("Por favor, ingrese una cantidad válida");
                        return;
                    }

                    const index = this.productoEdit.index;
                    if (index > -1 && index < this.productos.length) {
                        // RECONSTRUIR la descripción completa cuando se edita el nombre
                        let nuevaDescripcion = this.productoEdit.descripcion;

                        // Si se editó el nombre, reconstruir la descripción
                        if (this.productoEdit.nom_prod && this.productoEdit.codigo_pp) {
                            nuevaDescripcion = `${this.productoEdit.codigo_pp} | ${this.productoEdit.nom_prod}`;
                        }

                        // Actualizar TODOS los campos editables
                        const productoActualizado = {
                            ...this.productos[index], // Mantener campos originales
                            // Campos que se pueden editar en el modal
                            // descripcion: nuevaDescripcion, 
                            descripcion: this.productoEdit.nom_prod,
                            detalle: this.productoEdit.detalle,
                            cantidad: parseFloat(this.productoEdit.cantidad),
                            precio: this.productoEdit.precio,
                            precio_mostrado: this.productoEdit.precio_mostrado,
                            unidad_id: this.productoEdit.unidad_id || '',
                            // Actualizar también los precios si cambiaron
                            precioVenta: this.productoEdit.precioVenta,
                            costo: this.productoEdit.costo,
                            precio_mayor: this.productoEdit.precio_mayor,
                            precio_menor: this.productoEdit.precio_menor,
                            // Guardar también el nombre por separado
                            nom_prod: this.productoEdit.nom_prod,
                            codigo_pp: this.productoEdit.codigo_pp
                        };

                        // Usar Vue.set para reactividad
                        this.$set(this.productos, index, productoActualizado);

                        // Cerrar modal correctamente
                        const modal = document.getElementById('modalEditarProducto');
                        const modalInstance = bootstrap.Modal.getInstance(modal);
                        if (modalInstance) {
                            modalInstance.hide();
                        } else {
                            // Fallback para cerrar modal
                            $(modal).modal('hide');
                        }

                        alertExito("Producto actualizado correctamente");
                    } else {
                        alertAdvertencia("Error: No se pudo encontrar el producto para actualizar");
                    }
                },

                mostrarMensajeProducto() {
                    if (this.producto.descripcion.length === 0) {
                        this.mensajeProductoVisible = true;
                    }
                },

                seleccionarPrecioConTipo(tipo, precio) {
                    this.producto.precio_mostrado = precio;
                    this.producto.precio = precio;
                    this.producto.tipo_precio = tipo;
                    this.mensajeProductoVisible = false;
                },

                seleccionarPrecioEditConTipo(tipo, precio) {
                    this.productoEdit.precio_mostrado = precio;
                    this.productoEdit.precio = precio;
                    this.productoEdit.tipo_precio = tipo;
                }
            }
        });
        window.app = appguia;
        // 1. Primero, optimicemos la inicialización del autocomplete
        function initializeAutocomplete() {
            // Actualizar la función de autocompletado en el document.ready
            $("#input_buscar_productos").autocomplete({
                source: function (request, response) {
                    // Realizamos dos búsquedas en paralelo: productos y repuestos
                    $.when(
                        $.ajax({
                            url: _URL + `/ajs/cargar/productos/${appguia.producto.almacen}`,
                            dataType: "json",
                            data: { term: request.term }
                        }),
                        $.ajax({
                            url: _URL + `/ajs/cargar/repuestos/${appguia.producto.almacen}`,
                            dataType: "json",
                            data: { term: request.term }
                        })
                    ).done(function (productosResp, repuestosResp) {
                        // Combinamos los resultados
                        let productos = productosResp[0];
                        let repuestos = repuestosResp[0];

                        // Agregamos un identificador para diferenciar productos y repuestos
                        productos = productos.map(p => ({ ...p, tipo: 'producto' }));
                        repuestos = repuestos.map(r => ({ ...r, tipo: 'repuesto' }));

                        // Filtramos los resultados según el término de búsqueda
                        const searchLower = request.term.toLowerCase();
                        const filtered = [...productos, ...repuestos].filter(item =>
                            item.codigo_pp.toLowerCase().indexOf(searchLower) !== -1 ||
                            item.nombre.toLowerCase().indexOf(searchLower) !== -1
                        );

                        response(filtered);

                        // Ajustar el ancho del dropdown después de que se muestren los resultados
                        setTimeout(function () {
                            ajustarAnchoDropdown();
                        }, 0);
                    });
                },
                minLength: 1,
                appendTo: "#modalBuscarProductos",
                position: {
                    my: "left top",
                    at: "left bottom",
                    of: "#input_buscar_productos",
                    collision: "none"
                },
                select: function (event, ui) {
                    event.preventDefault();

                    const producto = appguia.producto;

                    // Asignamos el tipo (producto o repuesto)
                    producto.tipo = ui.item.tipo;

                    // Asignamos el ID según sea producto o repuesto
                    if (ui.item.tipo === 'repuesto') {
                        producto.productoid = 'R-' + ui.item.codigo; // Prefijo R para repuestos
                    } else {
                        producto.productoid = ui.item.codigo;
                    }

                    producto.detalle = ui.item.detalle;
                    producto.codigo_pp = ui.item.codigo_pp;
                    producto.descripcion = `${ui.item.codigo_pp} | ${ui.item.nombre}`;
                    producto.nom_prod = ui.item.nombre;
                    producto.cantidad = '';
                    producto.stock = ui.item.cnt;
                    producto.precio = formatearPrecio(ui.item.precio);
                    producto.precio2 = formatearPrecio(ui.item.precio2);
                    producto.precio_unidad = formatearPrecio(ui.item.precio_unidad);
                    producto.codigo = ui.item.codigo;
                    producto.codigo_prod = ui.item.codigo_pp;
                    producto.costo = ui.item.costo;
                    producto.precioVenta = formatearPrecio(ui.item.precio_unidad);


                    appguia.precioProductos = [
                        { precio: producto.precio },
                        { precio: producto.precio2 },
                        { precio: producto.precio_unidad }
                    ];
                    app.producto.precioVenta = formatearPrecio(ui.item.precio);
                    app.producto.costo = formatearPrecio(ui.item.costo);
                    app.producto.precio_mayor = formatearPrecio(ui.item.precio_mayor);
                    app.producto.precio_menor = formatearPrecio(ui.item.precio_menor);
                    app.producto.precio_mostrado = app.producto.precioVenta;
                    appguia.$set(producto, 'unidad_id', ui.item.unidad_id);

                    // Cargar precios adicionales
                    cargarPreciosAdicionales(ui.item.codigo, ui.item.tipo);

                    $('#input_buscar_productos').val("");
                    $("#cantidad-input").focus();
                },
                open: function (event, ui) {
                    ajustarAnchoDropdown();
                }
            }).data("ui-autocomplete")._renderItem = function (ul, item) {
                // Determinamos si es producto o repuesto
                const tipo = item.tipo === 'repuesto' ? 'repuesto' : 'producto';
                const tipoTexto = item.tipo === 'repuesto' ? 'repuesto' : 'producto';

                // ORDEN CORRECTO: primero la etiqueta, luego el código y después la descripción
                return $("<li>")
                    .append(`<div class='autocomplete-item'>
                <span class='producto-tipo ${tipo}'>${tipoTexto}</span>
                <span class='producto-codigo'>${item.codigo_pp}</span>
                <span class='producto-nombre'>${item.nombre}</span>
            </div>`)
                    .appendTo(ul);
            };

            // Función para ajustar el ancho del dropdown
            function ajustarAnchoDropdown() {
                var $input = $("#input_buscar_productos");
                var $dropdown = $(".ui-autocomplete:visible");

                if ($dropdown.length && $input.length) {
                    var inputWidth = $input.outerWidth();
                    var inputOffset = $input.offset();

                    if (inputOffset) {
                        // Aseguramos que el dropdown tenga EXACTAMENTE el mismo ancho que el input
                        $dropdown.css({
                            "width": inputWidth + "px",
                            "left": inputOffset.left + "px",
                            "max-width": inputWidth + "px"
                        });
                    }
                }
            }

            // Ajustar cuando cambia el tamaño de la ventana
            $(window).resize(function () {
                ajustarAnchoDropdown();
            });

            // También añadir estos estilos directamente para asegurar que el dropdown tenga el ancho correcto
            $("<style>")
                .prop("type", "text/css")
                .html(`
            .ui-autocomplete {
                width: auto !important;
                box-sizing: border-box !important;
                border-left: 1px solid #dee2e6 !important;
                border-right: 1px solid #dee2e6 !important;
                border-bottom: 1px solid #dee2e6 !important;
                border-top: none !important;
            }
            .producto-nombre {
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
                padding-left: 0 !important;
            }
            .autocomplete-item {
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
            }
        `)
                .appendTo("head");
        }
        function cargarPreciosAdicionales(idProducto, tipo = 'producto') {
            const url = tipo === 'repuesto'
                ? _URL + '/ajs/cargar/repuesto_precios/' + idProducto
                : _URL + '/ajs/cargar/producto_precios/' + idProducto;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data && data.length > 0) {
                        appguia.precioProductos = data.map(item => ({
                            nombre: item.nombre,
                            precio: parseFloat(item.precio).toFixed(2)
                        }));
                    } else {
                        appguia.precioProductos = [];
                    }
                },
                error: function () {
                    appguia.precioProductos = [];
                }
            });
        }
        function cargarPreciosAdicionalesEdit(idProducto, tipo = 'producto') {
            const url = tipo === 'repuesto'
                ? _URL + '/ajs/cargar/repuesto_precios/' + idProducto
                : _URL + '/ajs/cargar/producto_precios/' + idProducto;

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data && data.length > 0) {
                        appguia.preciosAdicionales = data.map(item => ({
                            nombre: item.nombre,
                            precio: parseFloat(item.precio).toFixed(2)
                        }));
                    } else {
                        appguia.preciosAdicionales = [];
                    }
                },
                error: function () {
                    appguia.preciosAdicionales = [];
                }
            });
        }



        function formatearPrecio(precio) {
            return parseFloat(precio || 0).toFixed(2);
        }

        // Función auxiliar para formatear precios
        function formatearPrecio(precio) {
            return parseFloat(precio || 0).toFixed(2);
        }

        // 2. Función auxiliar para formatear precios
        function formatearPrecio(precio) {
            return parseFloat(precio || 0).toFixed(2);
        }

        // 3. Optimizar la gestión del modal
        $('#modalBuscarProductos').on('shown.bs.modal', function () {
            initializeAutocomplete();
        });

        // 4. Mejorar el reseteo del formulario
        $('#modalBuscarProductos').on('hidden.bs.modal', function () {
            appguia.producto = {
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
                precio_usado: 1,
                unidad_id: ''
            };
            appguia.precioProductos = [];
        });






        $("#example-text-input").on('keypress', function (e) {
            if (e.which == 13) {
                $("#submit-a-product").click();
                $("#input_buscar_productos").focus();
            }
        });

        $("#input_documento_clientetr").autocomplete({

            source: _URL + `/ajs/consulta/buscar/dtatranspor`,
            minLength: 1,
            select: function (event, ui) {
                event.preventDefault();
                /*    console.log(item);
                   console.log(ui); */
                console.log(ui.item);
                appguia.transporte.ruc = ui.item.ruc
                appguia.transporte.razon_social = ui.item.razon
            }
        });
        appguia.getDocumentoGuia();
        obtenerProvincias()
    })
</script>



<!-- <script src="<?= URL::to('/public/js/guia-remision/conductor.js') ?>?v=<?= time() ?>"> </script> -->
<script src="<?= URL::to('/public/js/guia-remision/motivos.js') ?>?v=<?= time() ?>"> </script>
<!-- <script src="<?= URL::to('/public/js/guia-remision/choferes.js') ?>?v=<?= time() ?>"> </script> -->
<script src="<?= URL::to('/public/js/guia-remision/chofer-configuraciones.js') ?>?v=<?= time() ?>"> </script>