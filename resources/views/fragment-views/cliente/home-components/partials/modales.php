<!-- Modal Período Personalizado -->
<div class="modal fade" id="periodoPersonalizadoModal" tabindex="-1"
    aria-labelledby="periodoPersonalizadoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="periodoPersonalizadoModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Seleccionar Período Personalizado
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPeriodo">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaInicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fechaInicio"
                                    v-model="filtroFechas.inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fechaFin"
                                    v-model="filtroFechas.fin" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" @click="aplicarPeriodoPersonalizado">
                    <i class="fas fa-filter me-1"></i>Aplicar Filtro
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Período Personalizado Stock -->
<div class="modal fade" id="periodoPersonalizadoStockModal" tabindex="-1"
    aria-labelledby="periodoPersonalizadoStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="periodoPersonalizadoStockModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Seleccionar Período para Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPeriodoStock">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaInicioStock" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fechaInicioStock"
                                    v-model="filtroFechasStock.inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaFinStock" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fechaFinStock"
                                    v-model="filtroFechasStock.fin" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" @click="aplicarPeriodoPersonalizadoStock">
                    <i class="fas fa-filter me-1"></i>Aplicar Filtro
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Período Personalizado Ingresos -->
<div class="modal fade" id="periodoPersonalizadoIngresosModal" tabindex="-1"
    aria-labelledby="periodoPersonalizadoIngresosModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="periodoPersonalizadoIngresosModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Seleccionar Período para Ingresos/Egresos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPeriodoIngresos">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaInicioIngresos" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fechaInicioIngresos"
                                    v-model="filtroFechasIngresos.inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaFinIngresos" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fechaFinIngresos"
                                    v-model="filtroFechasIngresos.fin" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" @click="aplicarPeriodoPersonalizadoIngresos">
                    <i class="fas fa-filter me-1"></i>Aplicar Filtro
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Período Personalizado Clientes -->
<div class="modal fade" id="periodoPersonalizadoClientesModal" tabindex="-1"
    aria-labelledby="periodoPersonalizadoClientesModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="periodoPersonalizadoClientesModalLabel">
                    <i class="fas fa-calendar-alt me-2"></i>Seleccionar Período para Clientes
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formPeriodoClientes">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaInicioClientes" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fechaInicioClientes"
                                    v-model="filtroFechasClientes.inicio" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fechaFinClientes" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fechaFinClientes"
                                    v-model="filtroFechasClientes.fin" required>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" @click="aplicarPeriodoPersonalizadoClientes">
                    <i class="fas fa-filter me-1"></i>Aplicar Filtro
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Descargar Reporte -->
<div class="modal fade" id="descargarReporteModal" tabindex="-1"
    aria-labelledby="descargarReporteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="descargarReporteModalLabel">
                    <i class="fas fa-file-download me-2"></i>Descargar Reporte
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formReporte">
                    <div class="mb-3">
                        <label for="tipoReporte" class="form-label">Tipo de Reporte</label>
                        <select class="form-select" id="tipoReporte" v-model="reporteSeleccionado"
                            required>
                            <option value="">Seleccione un tipo de reporte</option>
                            <option value="ventas">Reporte de Ventas</option>
                            <option value="productos">Reporte de Productos</option>
                            <option value="stock">Reporte de Stock</option>
                            <option value="clientes">Reporte de Clientes</option>
                            <option value="metas">Reporte de Metas de Ventas</option>
                            <option value="completo">Reporte Completo</option>
                        </select>
                    </div>

                    <!-- Selector de período -->
                    <div class="mb-3">
                        <label for="tipoPeriodo" class="form-label">Período del Reporte</label>
                        <select class="form-select" id="tipoPeriodo" v-model="tipoPeriodoReporte"
                            @change="cambiarTipoPeriodo" required>
                            <option value="rango">Rango de Fechas</option>
                            <option value="anual">Por Año</option>
                        </select>
                    </div>

                    <!-- Selector de fechas (mostrar solo si es rango) -->
                    <div v-show="tipoPeriodoReporte === 'rango'" class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reporteFechaInicio" class="form-label">Fecha
                                    Inicio</label>
                                <input type="date" class="form-control" id="reporteFechaInicio"
                                    v-model="filtroFechas.inicio"
                                    :required="tipoPeriodoReporte === 'rango'">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reporteFechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="reporteFechaFin"
                                    v-model="filtroFechas.fin"
                                    :required="tipoPeriodoReporte === 'rango'">
                            </div>
                        </div>
                    </div>

                    <!-- Selector de año (mostrar solo si es anual) -->
                    <div v-show="tipoPeriodoReporte === 'anual'" class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reporteAnio" class="form-label">Año</label>
                                <select class="form-select" id="reporteAnio"
                                    v-model="anioSeleccionado"
                                    :required="tipoPeriodoReporte === 'anual'">
                                    <option value="">Seleccione un año</option>
                                    <option v-for="anio in aniosDisponibles" :key="anio"
                                        :value="anio">{{ anio }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="reporteMes" class="form-label">Mes (Opcional)</label>
                                <select class="form-select" id="reporteMes"
                                    v-model="mesSeleccionado">
                                    <option value="">Todo el año</option>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Información adicional para reportes anuales -->
                    <div v-show="tipoPeriodoReporte === 'anual'" class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Reporte Anual:</strong>
                        <span v-if="mesSeleccionado">
                            Se generará el reporte para {{ obtenerNombreMes(mesSeleccionado) }} de
                            {{ anioSeleccionado }}
                        </span>
                        <span v-else>
                            Se generará el reporte para todo el año {{ anioSeleccionado }}
                        </span>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn bg-success text-white"
                    @click="descargarReporteExcel">
                    <i class="fas fa-file-excel me-1"></i>Descargar Excel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Establecer Meta -->
<div class="modal fade" id="metaModal" tabindex="-1" aria-labelledby="metaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="metaModalLabel">
                    <i class="fas fa-target me-2"></i>Establecer Meta Total de Ventas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formMeta">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="mesSelect" class="form-label">Mes</label>
                                <select class="form-select" id="mesSelect" required>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="anioInput" class="form-label">Año</label>
                                <input type="number" class="form-control" id="anioInput"
                                    value="<?= date('Y') ?>" min="2020" max="2030" required>
                            </div>
                        </div>
                    </div>

                    <!-- META TOTAL -->
                    <div class="mb-4">
                        <label for="metaTotalInput" class="form-label">
                            <i class="fas fa-bullseye text-rojo me-2"></i>
                            <strong>Meta Total de Ventas (S/)</strong>
                        </label>
                        <input type="number" class="form-control form-control-lg" id="metaTotalInput"
                            step="0.01" min="0" placeholder="Ej: 50000.00" required>
                        <div class="form-text">Esta meta se distribuirá automáticamente entre todos los
                            vendedores activos</div>
                    </div>

                    <!-- DISTRIBUCIÓN AUTOMÁTICA -->
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Distribución Automática:</strong> El sistema asignará metas individuales a
                        cada vendedor
                        basándose en su rendimiento histórico y la meta total establecida.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" @click="guardarMetaTotal">
                    <i class="fas fa-save me-1"></i>Establecer Meta Total
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cliente Detalle -->
<div class="modal fade" id="clienteDetalleModal" tabindex="-1" aria-labelledby="clienteDetalleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="clienteDetalleModalLabel">
                    <i class="fas fa-user-circle me-2"></i>Detalle del Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="clienteDetalleContent">
                    <div class="text-center">
                        <div class="spinner-border text-rojo" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando información del cliente...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cliente Estadísticas -->
<div class="modal fade" id="clienteEstadisticasModal" tabindex="-1"
    aria-labelledby="clienteEstadisticasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="clienteEstadisticasModalLabel">
                    <i class="fas fa-chart-line me-2"></i>Estadísticas del Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="clienteEstadisticasContent">
                    <div class="text-center">
                        <div class="spinner-border text-rojo" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando estadísticas del cliente...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Producto Detalle -->
<div class="modal fade" id="productoDetalleModal" tabindex="-1" aria-labelledby="productoDetalleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="productoDetalleModalLabel">
                    <i class="fas fa-box me-2"></i>Detalle del Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="productoDetalleContent">
                    <div class="text-center">
                        <div class="spinner-border text-rojo" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando información del producto...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Producto Estadísticas -->
<div class="modal fade" id="productoEstadisticasModal" tabindex="-1"
    aria-labelledby="productoEstadisticasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="productoEstadisticasModalLabel">
                    <i class="fas fa-chart-bar me-2"></i>Estadísticas del Producto
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="productoEstadisticasContent">
                    <div class="text-center">
                        <div class="spinner-border text-rojo" role="status">
                            <span class="visually-hidden">Cargando...</span>
                        </div>
                        <p class="mt-2 text-muted">Cargando estadísticas del producto...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Productos por Estado de Stock -->
<div class="modal fade" id="productosEstadoModal" tabindex="-1" aria-labelledby="productosEstadoModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="productosEstadoModalLabel">
                    <i class="fas fa-boxes me-2"></i>Productos por Estado de Stock
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Información del estado seleccionado -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert" id="alertaEstadoSeleccionado" role="alert">
                            <div class="d-flex align-items-center">
                                <i id="iconoEstadoSeleccionado" class="fas fa-box me-3 fs-4"></i>
                                <div>
                                    <h6 class="mb-1" id="tituloEstadoSeleccionado">Estado: </h6>
                                    <small id="descripcionEstadoSeleccionado">Productos en este
                                        estado</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barra de búsqueda -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" id="busquedaProductosEstado"
                                placeholder="Buscar por nombre, código o categoría...">
                        </div>
                    </div>
                </div>

                <!-- Tabla de productos -->
                <div style="max-height: 60vh; overflow-y: auto;">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tablaProductosEstado">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Categoría</th>
                                    <th>Stock Actual</th>
                                    <th>Precio</th>
                                    <th>Estado</th>
                                    <th>Última Actualización</th>
                                </tr>
                            </thead>
                            <tbody id="cuerpoTablaProductosEstado">
                                <!-- Los productos se cargarán aquí dinámicamente -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Loading state -->
                <div id="loadingProductosEstado" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-rojo" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando productos...</p>
                </div>

                <!-- Estado vacío -->
                <div id="estadoVacioProductos" class="text-center py-5" style="display: none;">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron productos</h5>
                    <p class="text-muted">No hay productos en este estado o que coincidan con la búsqueda
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <small class="text-muted" id="contadorProductosEstado">0 productos encontrados</small>
                </div>
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Gestión de Vendedores -->
<div class="modal fade" id="vendedoresModal" tabindex="-1" aria-labelledby="vendedoresModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-rojo">
            <div class="modal-header bg-rojo text-white">
                <h5 class="modal-title" id="vendedoresModalLabel">
                    <i class="fas fa-users me-2"></i>Gestión de Vendedores y Metas Individuales
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Filtros y controles superiores -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" v-model="busquedaVendedores" 
                                placeholder="Buscar por nombre, usuario o rol...">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" v-model="filtroRolVendedor" @change="filtrarVendedoresPorRol">
                            <option value="">Todos los roles</option>
                            <option value="3">Solo Vendedores</option>
                            <option value="1">Solo Administradores</option>
                        </select>
                    </div>
                </div>

                <!-- Información del período actual -->
                <div class="alert alert-info">
                    <div class="row">
                        <div class="col-md-6">
                            <i class="fas fa-calendar me-2"></i>
                            <strong>Período:</strong> {{ obtenerNombreMes(new Date().getMonth() + 1) }} {{ new Date().getFullYear() }}
                        </div>
                        <div class="col-md-6" v-if="resumenVendedores.meta_total_empresa">
                            <i class="fas fa-target me-2"></i>
                            <strong>Meta Total Empresa:</strong> S/ {{ parseFloat(resumenVendedores.meta_total_empresa).toLocaleString('es-PE', {minimumFractionDigits: 2}) }}
                        </div>
                    </div>
                </div>

                <!-- Tabla de vendedores -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">Avatar</th>
                                <th>Vendedor</th>
                                <th>Usuario/Rol</th>
                                <th width="150">Ventas Actuales</th>
                                <th width="150">Meta Individual</th>
                                <th width="100">Progreso</th>
                                <th width="120">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="vendedor in vendedoresFiltrados" :key="vendedor.usuario_id">
                                <td>
                                    <div class="avatar-sm">
                                        <div class="avatar-title bg-primary text-white rounded-circle">
                                            {{ (vendedor.nombres || vendedor.usuario || 'V').charAt(0).toUpperCase() }}
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ vendedor.nombres || vendedor.usuario }}</h6>
                                        <small class="text-muted">{{ vendedor.apellidos || '' }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <span class="fw-medium">{{ vendedor.usuario }}</span><br>
                                        <small class="text-muted">{{ vendedor.tipo_usuario || vendedor.nombre_rol }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-success">
                                        S/ {{ parseFloat(vendedor.ventas_actuales || vendedor.ventas_mes || 0).toLocaleString('es-PE', {minimumFractionDigits: 2}) }}
                                    </span>
                                </td>
                                <td>
                                    <div v-if="vendedor.editandoMeta">
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                class="form-control" 
                                                v-model.number="vendedor.nueva_meta_individual"
                                                step="0.01" 
                                                min="0"
                                                @keyup.enter="guardarMetaIndividual(vendedor)"
                                                @keyup.esc="cancelarEditarMeta(vendedor)">
                                            <button class="btn btn-outline-success btn-sm" 
                                                @click="guardarMetaIndividual(vendedor)"
                                                :disabled="!vendedor.nueva_meta_individual || vendedor.nueva_meta_individual <= 0">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary btn-sm" 
                                                @click="cancelarEditarMeta(vendedor)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div v-else>
                                        <span v-if="vendedor.meta_individual" class="fw-bold text-primary">
                                            S/ {{ parseFloat(vendedor.meta_individual).toLocaleString('es-PE', {minimumFractionDigits: 2}) }}
                                        </span>
                                        <small v-else class="text-muted">Sin meta asignada</small>
                                    </div>
                                </td>
                                <td>
                                    <div v-if="vendedor.meta_individual && vendedor.meta_individual > 0">
                                        <div class="progress mb-1" style="height: 8px;">
                                            <div class="progress-bar" 
                                                :class="obtenerClaseProgreso(vendedor)" 
                                                :style="`width: ${Math.min((parseFloat(vendedor.ventas_actuales || vendedor.ventas_mes || 0) / parseFloat(vendedor.meta_individual)) * 100, 100)}%`">
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            {{ ((parseFloat(vendedor.ventas_actuales || vendedor.ventas_mes || 0) / parseFloat(vendedor.meta_individual)) * 100).toFixed(1) }}%
                                        </small>
                                    </div>
                                    <small v-else class="text-muted">-</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button v-if="!vendedor.editandoMeta" 
                                            class="btn btn-outline-primary" 
                                            @click="editarMetaIndividual(vendedor)"
                                            title="Editar meta">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info" 
                                            @click="verDetalleVendedor(vendedor)"
                                            title="Ver detalle">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Estado vacío -->
                <div v-if="!vendedoresFiltrados.length" class="text-center py-5">
                    <i class="fas fa-users-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No se encontraron vendedores</h5>
                    <p class="text-muted">No hay vendedores que coincidan con los filtros aplicados</p>
                </div>

                <!-- Loading state -->
                <div v-if="cargandoVendedores" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <p class="mt-2 text-muted">Cargando vendedores...</p>
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <small class="text-muted">{{ vendedoresFiltrados.length }} vendedores encontrados</small>
                </div>
                <button type="button" class="btn btn-success" @click="aplicarMetasAutomaticas">
                    <i class="fas fa-magic me-1"></i>Distribuir Metas Automáticamente
                </button>
                <button type="button" class="btn border-rojo" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cerrar
                </button>
            </div>
        </div>
    </div>
</div>