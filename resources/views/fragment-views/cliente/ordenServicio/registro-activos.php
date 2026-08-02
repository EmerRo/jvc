<div id="client" class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Agregar Registro</h1>
        <a id="enlaceGestion" href="#" class="btn btn-warning">Ir a Gestión de Activos</a>
    </div>

    <!-- Form -->
    <div class="card shadow-sm">
        <div class="card-body">
            <form id="frmClientesAgregar">
                <input type="hidden" name="origen" value="Ord Servicio">

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="input_datos_cliente" class="form-label">DNI o RUC <span style="color:red">(*)</span></label>
                            <div class="input-group">
                                <input id="input_datos_cliente" v-model="maquinaSerie.num_doc" type="text"
                                    placeholder="Ingrese Documento" class="form-control" maxlength="11">
                                <button @click="buscarDocumentSS" class="btn btn-primary" type="button">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                            <p id="input_datos_cliente-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="input_buscar_Dataseries" class="form-label">
                                Buscar Serie<span class="text-danger"> (*)</span>
                            </label>
                            <input id="input_buscar_Dataseries" v-model="maquinaSerie.buscar_serie" type="text"
                                placeholder="Ingrese Serie" class="form-control">
                            <p id="input_buscar_Dataseries-error" class="text-danger mt-1 mb-0"></p>
                        </div>
                        <div class="mb-3">
                            <label for="marca" class="form-label">Marca</label>
                            <input type="text" v-model="maquinaSerie.marc" class="form-control" id="marca" name="marca"
                                readonly>
                            <p id="marca-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="modelo" class="form-label">Modelo</label>
                            <input id="modelo" v-model="maquinaSerie.model" name="modelo" class="form-control"
                                readonly>
                            <p id="modelo-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="equipo" class="form-label">Equipo</label>
                            <input type="text" v-model="maquinaSerie.equipo" class="form-control" id="equipo"
                                name="equipo" readonly>
                            <p id="equipo-error" class="text-danger mt-1 mb-0"></p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="cliente_razon_social" class="form-label">Cliente </label>
                            <input v-model="maquinaSerie.cliente_Rsocial" type="text" placeholder="Nombre del cliente"
                                class="form-control" autocomplete="off" id="cliente_razon_social"
                                name="cliente_razon_social">
                            <p id="cliente_razon_social-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="numero_serie" class="form-label">Número De Serie</label>
                            <input type="text" v-model="maquinaSerie.num_serie" class="form-control" id="numero_serie"
                                name="numero_serie">
                            <p id="numero_serie-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo</label>
                            <select class="form-select" id="motivo" name="motivo"></select>
                            <p id="motivo-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="fecha_salida" class="form-label">Fecha De Salida</label>
                            <input type="date" class="form-control" id="fecha_salida" name="fecha_salida">
                            <p id="fecha_salida-error" class="text-danger mt-1 mb-0"></p>
                        </div>
                        <div class="mb-3">
                            <label for="fecha_ingreso" class="form-label">Fecha De Ingreso</label>
                            <input type="date" class="form-control" id="fecha_ingreso" name="fecha_ingreso">
                            <p id="fecha_ingreso-error" class="text-danger mt-1 mb-0"></p>
                        </div>

                        <div class="mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control" id="observaciones" name="observaciones" rows="3"></textarea>
                            <p id="observaciones-error" class="text-danger mt-1 mb-0"></p>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" id="submitRegistro" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('enlaceGestion').href = `${_URL}/gestion/activos`;
</script>
<script src="<?= URL::to('public/js/modulo-registro-activos.js') ?>?v=<?= time() ?>"></script>
