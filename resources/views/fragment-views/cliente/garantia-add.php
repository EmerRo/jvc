<div id="content-vue-modals" class="container-fluid">
    <div class="warranty-container bg-white py-3" style="max-width: 1000px; margin: 0 auto;">
        <div class="d-flex justify-content-between align-items-center px-4 mb-3">
            <h2 class="warranty-title text-rojo mb-0">
                <i class="fa fa-shield-alt me-2"></i>Registro de Garantía
            </h2>
            <div class="d-flex gap-2">
                <a href="/garantia" class="btn border-rojo text-rojo bg-white">
                    <i class="fa fa-arrow-left me-1"></i> Regresar
                </a>
                <button type="button" id="submitRegistro" class="btn bg-rojo text-white">
                    <i class="fa fa-save me-1"></i> Guardar Garantía
                </button>
            </div>
        </div>

        <div class="alert alert-success" id="alertSuccess" style="display: none;">
            <i class="fa fa-check-circle me-2"></i> Garantía registrada con éxito.
        </div>

        <form>
            <!-- Sección de Información del Cliente -->
            <div class="mb-0">
                <div class="bg-white text-rojo p-2">
                    <h5 class="mb-0"><i class="fa fa-user me-2"></i>Información del Cliente</h5>
                </div>
                <div class="p-3">
                    <div class="row">
                        <div class="col-md-6 ">
                            <label for="cliente" class="form-label">Nombre/Razón Social <span
                                    class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa fa-building"></i></span>
                                <input v-model="garantia.cliente_nombre" type="text" placeholder="Nombre del cliente"
                                    class="form-control" name="cliente" id="cliente" readonly>
                            </div>
                        </div>
                        <div class="col-md-6 ">
                            <label for="guia_remision" class="form-label">Guía De Remisión</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa fa-file-alt"></i></span>
                                <input v-model="garantia.guiaRemision" type="text" name="guia_remision"
                                    placeholder="Ingrese la guía de remisión" class="form-control" id="guia_remision">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Período de Garantía -->
            <div class="mb-0">
                <div class="bg-white text-rojo p-2">
                    <h5 class="mb-0"><i class="fa fa-calendar-alt me-2"></i>Período de Garantía</h5>
                </div>
                <div class="p-3">
                    <div class="row">
                        <div class="col-md-6 ">
                            <label for="fecha_inicio" class="form-label">Fecha De Inicio</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa fa-calendar"></i></span>
                                <input v-model="garantia.fechaInicio" type="date" name="fecha_inicio"
                                    class="form-control" id="fecha_inicio">
                            </div>
                            <small class="text-primary"><i class="fa fa-info-circle me-1"></i>Fecha de inicio de la
                                garantía (establecida automáticamente)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="fecha_caducidad" class="form-label">Fecha De Caducidad</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fa fa-calendar"></i></span>
                                <input v-model="garantia.fechaCaducidad" name="fecha_caducidad" type="date"
                                    class="form-control" id="fecha_caducidad">
                            </div>
                            <small class="text-danger"><i class="fa fa-exclamation-circle me-1"></i>La garantía vence un
                                año después de la fecha de inicio</small>
                        </div>
                    </div>

                    <div class="alert alert-primary bg-light-blue border-0 mt-2 mb-0" role="alert"
                        style="background-color: #e6f0ff;">
                        <div class="d-flex">
                            <div class="me-2">
                                <i class="fa fa-info-circle text-primary"></i>
                            </div>
                            <div>
                                <strong class="text-primary">Información de Garantía</strong>
                                <p class="mb-0">La garantía tiene una duración de 1 año a partir de la fecha de inicio.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sección de Equipos -->
            <div class="mb-3">
                <div class="bg-white text-rojo p-2">
                    <h5 class="mb-0"><i class="fa fa-tools me-2"></i>Equipos</h5>
                </div>
                <div class="p-3">
                    <!-- Contenedor con scroll para muchos equipos -->
                    <div style="max-height: 300px; overflow-y: auto;">
                        <div v-for="(equipo, index) in garantia.equipos" :key="index" class="card mb-3">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fa fa-laptop me-2"></i>Equipo {{ index + 1 }}
                                </h5>
                            </div>
                            <div class="card-body py-2">
                                <div class="row">
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Modelo</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fa fa-tag"></i></span>
                                            <input v-model="equipo.modelo_nombre" type="text" class="form-control"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Marca</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="fa fa-trademark"></i></span>
                                            <input v-model="equipo.marca_nombre" type="text" class="form-control"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Equipo</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fa fa-desktop"></i></span>
                                            <input v-model="equipo.equipo_nombre" type="text" class="form-control"
                                                readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="form-label">Número de Serie</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i class="fa fa-barcode"></i></span>
                                            <input v-model="equipo.numero_serie" type="text" class="form-control"
                                                readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="mt-4 mb-0">
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        const app = new Vue({
            el: "#content-vue-modals",
            data: {
                garantia: {
                    cliente_nombre: '',
                    guiaRemision: '',
                    fechaInicio: '',
                    fechaCaducidad: '',
                    equipos: []
                }
            },
            mounted() {
                this.cargarDatosNumeroSerie();
            },
            methods: {
                cargarDatosNumeroSerie() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const id = urlParams.get('id');
                    if (id) {
                        $.ajax({
                            url: _URL + "/ajs/garantia/cargar/datos/serie",
                            type: "GET",
                            data: { id: id },
                            success: (response) => {
                                const data = JSON.parse(response);
                                if (data.success) {
                                    this.garantia.cliente_nombre = data.data.cliente_ruc_dni;
                                    this.garantia.equipos = data.data.equipos;

                                    // Establecer fecha de inicio como la fecha actual
                                    const fechaActual = new Date();
                                    this.garantia.fechaInicio = this.formatDate(fechaActual);

                                    // Establecer fecha de caducidad como un año después
                                    const fechaCaducidad = new Date();
                                    fechaCaducidad.setFullYear(fechaCaducidad.getFullYear() + 1);
                                    this.garantia.fechaCaducidad = this.formatDate(fechaCaducidad);
                                } else {
                                    Swal.fire("Error", data.error, "error");
                                }
                            },
                            error: (jqXHR, textStatus, errorThrown) => {
                                Swal.fire("Error", "No se pudieron cargar los datos", "error");
                            }
                        });
                    }
                },
                // Función auxiliar para formatear fechas en formato YYYY-MM-DD
                formatDate(date) {
                    const year = date.getFullYear();
                    const month = String(date.getMonth() + 1).padStart(2, '0');
                    const day = String(date.getDate()).padStart(2, '0');
                    return `${year}-${month}-${day}`;
                }
            }
        });

        $("#submitRegistro").click(function () {
            // Validación más completa
            if (!app.garantia.cliente_nombre) {
                Swal.fire("¡Error!", "Falta el nombre del cliente.", "error");
                return;
            }

            if (!app.garantia.fechaInicio) {
                Swal.fire("¡Error!", "Falta la fecha de inicio.", "error");
                return;
            }

            if (!app.garantia.fechaCaducidad) {
                Swal.fire("¡Error!", "Falta la fecha de caducidad.", "error");
                return;
            }

            // Verificar que haya equipos
            if (!app.garantia.equipos || app.garantia.equipos.length === 0) {
                Swal.fire("¡Error!", "No hay equipos asociados a esta garantía.", "error");
                return;
            }

            const data = {
                numero_serie_id: new URLSearchParams(window.location.search).get('id'),
                guia_remision: app.garantia.guiaRemision || '',
                fecha_inicio: app.garantia.fechaInicio,
                fecha_caducidad: app.garantia.fechaCaducidad,
                cliente_nombre: app.garantia.cliente_nombre,
                equipos: JSON.stringify(app.garantia.equipos)
            };

            // Depuración
            console.log("Datos a enviar:", data);

            Swal.fire({
                title: "¿Está seguro?",
                text: "Va a registrar una nueva garantía para este equipo.",
                icon: "question",
                showCancelButton: true,
                confirmButtonText: "Sí, guardar",
                cancelButtonText: "Cancelar"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _URL + "/ajs/garantia/add",
                        type: "POST",
                        data: data,
                        success: function (response) {
                            console.log("Respuesta del servidor:", response);
                            try {
                                const result = JSON.parse(response);
                                if (result.res) {
                                    Swal.fire({
                                        title: "¡Éxito!",
                                        text: "Se registró la garantía con éxito.",
                                        icon: "success"
                                    }).then(() => {
                                        window.location.href = _URL + "/garantia";
                                    });
                                } else {
                                    Swal.fire({
                                        title: "¡Error!",
                                        text: result.msg || "Error desconocido",
                                        icon: "error"
                                    });
                                }
                            } catch (e) {
                                console.error("Error al parsear respuesta:", e);
                                Swal.fire({
                                    title: "¡Error!",
                                    text: "Error al procesar la respuesta del servidor",
                                    icon: "error"
                                });
                            }
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            console.error("Error en la solicitud:", jqXHR.responseText);
                            Swal.fire({
                                title: "¡Error!",
                                text: "No se pudo registrar. Intenta nuevamente. Detalles: " + errorThrown,
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });
    });
</script>