<?php


Route::post("/ajs/admin/cliente/add", "AdminDataController@agregarCliente")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/admin/cliente/edt", "AdminDataController@actualizarCliente")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/admin/cliente/info", "AdminDataController@infoCliemt")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/admin/cliente/estado/edt", "AdminDataController@guardarEstado")->Middleware([ValidarTokenMiddleware::class]);

/* ============ INICO AJAX2 rutas para consultasController ================== */
Route::post('/ajs/cargar/productos/precios', "ConsultasController@cargarPreciosProd")->Middleware([ValidarTokenMiddleware::class]);
Route::get("/ajs/asearch/provedor/data", "ConsultasController@buscarDataProveedor")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/doc/venta/info", "ConsultasController@functionbuscarDocumentoVentasSN")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/nota/electronica/add", "ConsultasController@guardarNotaElectronica")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/send/sunat/notaelectronica", "ConsultasController@enviarDocumentoSunatNE")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sucursales/empresa", "ConsultasController@listasucursaleEmpresa")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sucursales/empresa/add", "ConsultasController@agregarSusucursal")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sucursales/empresa/info", "ConsultasController@getInfoSucursal")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sucursales/empresa/info/detalle", "ConsultasController@getInfoSucursalDetalle")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sucursales/empresa/edt", "ConsultasController@actualizarSucursal")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/metodo/pago", "ConsultasController@getMetodoPago")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/stock/almacen", "ConsultasController@consultaStockAlmacen")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/send/comprobante/email", "ConsultasController@enviarcomprobanteEmail");
Route::post("/ajs/informacion/venta/fb", "ConsultasController@informacionVentaFb");
Route::post("/ajs/verificador/token", "ConsultasController@verificadorToken");

/* ====================== FIN DE LAS RUTAS DE CONSULTAS CONTROLLER ========================================= */



Route::get("/data/cotizaciones/lista/ss", "ConsultaDelcontroller@getDataCotizacionSS")->Middleware([ValidarTokenMiddleware::class]);
Route::get("/data/taller/cotizaciones/lista/ss", "ConsultaDelcontroller@getDataTallerCotizacionSS")->middleware([ValidarTokenMiddleware::class]);

/* ====================== INICO DE LAS RUTAS DE PRODUCTOS CONTROLLER ========================================= */


Route::post("/ajs/data/producto/guardar/precios", "ProductosController@guardarPrecios")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/obtener/precios", "ProductosController@obtenerPrecios")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/add", "ProductosController@agregar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/delete", "ProductosController@delete")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/confirmar/traslado", "ProductosController@confirmarTraslado")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/add/lista", "ProductosController@agregarPorLista")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/producto/lista", "ProductosController@listaProducto")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/edt", "ProductosController@actualizar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/edt/precios", "ProductosController@actualizarPrecios")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/info/code", "ProductosController@informacionPorCodigo")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/info", "ProductosController@informacion")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/restock", "ProductosController@restock")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/producto/add/exel", "ProductosController@importarExel")->Middleware([ValidarTokenMiddleware::class]);

//  Rutas adicionales para las nuevas funcionalidades
Route::post("/ajs/save/condiciones", "ProductosController@saveCondicion");

//* Nuevas rutas para condiciones específicas por cotización
Route::get("/ajs/get/condiciones/default", "ProductosController@getCondicion");
Route::get("/ajs/get/condiciones/cotizacion/:id", "ProductosController@getCondicionCotizacion");
Route::post("/ajs/save/condiciones/cotizacion", "ProductosController@saveCondicionCotizacion");
Route::post("/ajs/save/condiciones/temp", "ProductosController@saveCondicionTemp");
Route::post("/ajs/save/condiciones/default", "ProductosController@saveCondicionDefault");
Route::get("/ajas/get/diagnosticos", "ProductosController@getDiagnostico");
Route::get("/ajas/get/diagnosticos", "ProductosController@saveDiagnostico");






Route::post("/ajs/cotizaciones", "CotizacionesController@listar");
Route::post("/ajs/cotizaciones/add", "CotizacionesController@agregar");
Route::post("/ajs/cotizaciones/edt", "CotizacionesController@actualizar");
Route::post("/ajs/cotizaciones/info", "CotizacionesController@getInformacion");
Route::post("/ajs/cotizaciones/del", "CotizacionesController@eliminarCotizacion");
Route::post("/ajs/cotizaciones/getvendedores", "CotizacionesController@getVendedores");
Route::post("/ajs/cotizaciones/crear-asunto", "CotizacionesController@crearAsunto");
Route::get("/ajs/cotizaciones/asuntos", "CotizacionesController@getAsuntos");



Route::post('/ajs/cuentas/cobrar/render', "CobranzaController@render");
Route::post('/ajas/getAllCuotas/byIdVenta', "CobranzaController@getAllByIdVenta");
Route::post('/ajs/pagar/cuota/cobranza', "CobranzaController@pagarCuota");
Route::post('/ajs/pagar/cuota/ventas', "PagosController@pagarCuotaVentas");

Route::post('/ajs/caja/apertura', "CajaController@aperturarCaja");
Route::post('/ajs/caja/apertura/listar', "CajaController@listar");
Route::post('/ajs/caja/chica/add', "CajaController@agregarMovimiento");
Route::post('/ajs/caja/chica/cerrar', "CajaController@cerrarCajaChica");


Route::post('/ajs/prodcutos/compras/render', "ComprasController@getAll");
Route::post('/ajas/compra/detalle', "ComprasController@getDetalle");
Route::post('/ajas/compra/buscar/producto', "ComprasController@buscarProducto");
Route::post('/ajas/compras/add', "ComprasController@guardarCompras");
Route::post('/ajas/compra/pagos', "ComprasController@getPagos");

// // Para cargar repuestos por almacén
// Route::get('/ajs/cargar/repuestos/:almacen', "RepuestosController@cargarPorAlmacen");


//observaciones para compras
Route::get("/ajs/get/observaciones/default", "ComprasController@getObservacion");
Route::get("/ajs/get/observaciones/compra/:id", "ComprasController@getObservacionCompra");
Route::post("/ajs/save/observaciones/compra", "ComprasController@saveObservacionCompra");
Route::post("/ajs/save/observaciones/temp", "ComprasController@saveObservacionTemp");
Route::post("/ajs/save/observaciones/default", "ComprasController@saveObservacionDefault");

Route::post('/ajas/cuentas/ventas/render', "PagosController@render");
Route::post('/ajas/getAllCuotas/byIdCompra', "PagosController@getAllByIdCompra");
Route::post('/ajs/pagar/cuota/pago', "PagosController@pagarCuota");


Route::get("/ajs/dashboard/cliente-detalle", "DashboardController@getClienteDetalle");
Route::get("/ajs/dashboard/cliente-estadisticas", "DashboardController@getClienteEstadisticas");
Route::get("/ajs/dashboard/producto-detalle", "DashboardController@getProductoDetalle");
Route::get("/ajs/dashboard/producto-estadisticas", "DashboardController@getProductoEstadisticas");
Route::get("/ajs/dashboard/estadisticas-stock", "DashboardController@getEstadisticasStock");
// Rutas para metas de empresa (NUEVO ENFOQUE)
Route::get('/ajs/dashboard/vendedores-metas', 'DashboardController@vendedoresMetasAction');
Route::post('/ajs/dashboard/guardar-meta-total', 'DashboardController@guardarMetaTotalAction');
Route::get('/ajs/dashboard/datos', 'DashboardController@getDatos');
Route::get("/ajs/dashboard/productos-por-estado", "DashboardController@getProductosPorEstado");
Route::get("/ajs/dashboard/datos-productos", "DashboardController@getDatosProductos");
Route::get("/ajs/dashboard/datos-ingresos-egresos", "DashboardController@getDatosIngresosEgresos");


/* ============================ INICIO Ventas controller rutas ajaxs======================================*/

Route::post("/ajas/ventas/porempresa", "VentasController@listaVentasPorEmpresa");
Route::post("/ajas/ventas/porempresa/regenxml", "VentasController@regenerarXML");
Route::post("/ajas/ventas/porempresa/sendsunat", "VentasController@enviarDocumentoSunatPorEmpresa");
Route::post("/ajas/ventas/porempresa/sendsunatresumen", "VentasController@envioResumenDiarioPorEmpresa");
Route::post("/ajas/ventas/porempresa/sendsunatcomubaja", "VentasController@envioComunicacionBajaPorEmpresa");
Route::post("/ajs/send/sunat/venta", "VentasController@enviarDocumentoSunat")->Middleware([ValidarTokenMiddleware::class]);
/* ============================ FIN Ventas controller rutas ======================================*/




Route::post("/ajs/getroles", "ConsultasController@getRoles");
Route::post("/ajs/add/users", "ConsultasController@saveUser");

// catehorias 
Route::get("/ajs/get/categorias", "CategoriasController@getCategoria");
Route::post("/ajs/save/categorias", "CategoriasController@saveCategoria");
Route::post("/ajs/getOne/categorias", "CategoriasController@getOneCategoria");
Route::post("/ajs/update/categorias", "CategoriasController@updateCategoria");
Route::post("/ajs/delete/categorias", "CategoriasController@deleteCategoria");
//categorias para repuestos
Route::get("/ajs/get/categorias/rep", "CategoriasController@getCategoriaRepuesto");
Route::post("/ajs/save/categorias/rep", "CategoriasController@saveCategoriaRepuesto");
Route::post("/ajs/getOne/categorias/rep", "CategoriasController@getOneCategoriaRepuesto");
Route::post("/ajs/update/categorias/rep", "CategoriasController@updateCategoriaRepuesto");
Route::post("/ajs/delete/categorias/rep", "CategoriasController@deleteCategoriaRepuesto");
// subcategorias para repuestos
Route::get("/ajs/get/subcategorias/rep", "CategoriasController@getSubcategoriaRepuesto");
Route::post("/ajs/get/subcategorias/rep/by-categoria", "CategoriasController@getSubcategoriasByCategoria");
Route::post("/ajs/save/subcategorias/rep", "CategoriasController@saveSubcategoriaRepuesto");
Route::post("/ajs/getOne/subcategorias/rep", "CategoriasController@getOneSubcategoriaRepuesto");
Route::post("/ajs/update/subcategorias/rep", "CategoriasController@updateSubcategoriaRepuesto");
Route::post("/ajs/delete/subcategorias/rep", "CategoriasController@deleteSubcategoriaRepuesto");


// unidades
Route::get("/ajs/get/unidades", "UnidadesController@getUnidad");
Route::post("/ajs/save/unidades", "UnidadesController@saveUnidad");
Route::post("/ajs/getOne/unidades", "UnidadesController@getOneUnidad");
Route::post("/ajs/update/unidades", "UnidadesController@updateUnidad");
Route::post("/ajs/delete/unidades", "UnidadesController@deleteUnidad");
// unidades para repuestos
Route::get("/ajs/get/unidades/rep", "UnidadesController@getUnidadRepuesto");
Route::post("/ajs/save/unidades/rep", "UnidadesController@saveUnidadRepuesto");
Route::post("/ajs/getOne/unidades/rep", "UnidadesController@getOneUnidadRepuesto");
Route::post("/ajs/update/unidades/rep", "UnidadesController@updateUnidadRepuesto");
Route::post("/ajs/delete/unidades/rep", "UnidadesController@deleteUnidadRepuesto");

// tecnicos
Route::get("/ajs/get/tecnicos", "TecnicosController@getTecnico");
Route::post("/ajs/save/tecnicos", "TecnicosController@saveTecnico");
Route::post("/ajs/getOne/tecnicos", "TecnicosController@getOneTecnico");
Route::post("/ajs/update/tecnicos", "TecnicosController@updateTecnico");
Route::post("/ajs/delete/tecnicos", "TecnicosController@deleteTecnico");

// equipos
Route::get("/ajs/get/equipos", "EquiposController@getEquipo");
Route::post("/ajs/save/equipos", "EquiposController@saveEquipo");
Route::post("/ajs/getOne/equipos", "EquiposController@getOneEquipo");
Route::post("/ajs/update/equipos", "EquiposController@updateEquipo");
Route::post("/ajs/delete/equipos", "EquiposController@deleteEquipo");

// Marcas
Route::get("/ajs/get/marcas", "MarcasController@getMarca");
Route::post("/ajs/save/marcas", "MarcasController@saveMarca");
Route::post("/ajs/getOne/marcas", "MarcasController@getOneMarca");
Route::post("/ajs/update/marcas", "MarcasController@updateMarca");
Route::post("/ajs/delete/marcas", "MarcasController@deleteMarca");

// modelos
Route::get("/ajs/get/modelos", "ModelosController@getModelo");
Route::post("/ajs/save/modelos", "ModelosController@saveModelo");
Route::post("/ajs/getOne/modelos", "ModelosController@getOneModelo");
Route::post("/ajs/update/modelos", "ModelosController@updateModelo");
Route::post("/ajs/delete/modelos", "ModelosController@deleteModelo");

// series rutas
Route::get("/ajs/get/numeroseries", "SeriesController@getSeries");
Route::post("/ajs/getOne/numeroseries", "SeriesController@getOneSerie");
Route::post("/ajs/save/numeroseries", "SeriesController@saveSerie");
Route::post("/ajs/update/numeroseries", "SeriesController@updateSerie");
Route::post("/ajs/delete/numeroseries", "SeriesController@deleteSerie");
Route::post("/ajs/getSerieByNumero", "SeriesController@getSerieByNumero");
Route::post("/ajs/verificar/numeroserie", "SeriesController@verificarNumeroSerie");
Route::get("/ajs/getOne/numeroseries/:id", "SeriesController@getOneSerieById");
Route::get("/ajs/get/ultimonumeroserie", "SeriesController@getUltimoNumeroSerie");
// maquinas gestion activos

Route::get("/ajs/get/maquinas", "RegistroMaquinaController@getMaquinas");
Route::post("/ajs/save/maquinas", "RegistroMaquinaController@saveMaquina");
Route::post("/ajs/getOne/maquinas", "RegistroMaquinaController@getOneMaquina");
Route::post("/ajs/update/maquinas", "RegistroMaquinaController@updateMaquina");
Route::post("/ajs/delete/maquinas", "RegistroMaquinaController@deleteMaquina");



//motivo
Route::get("/ajs/get/motivos", "MotivoController@getMotivo");
Route::post("/ajs/save/motivos", "MotivoController@saveMotivo");
Route::post("/ajs/getOne/motivos", "MotivoController@getOneMotivo");
Route::post("/ajs/update/motivos", "MotivoController@updateMotivo");
Route::post("/ajs/delete/motivos", "MotivoController@deleteMotivo");

//Rutas para Motivo Guia
Route::get("/ajs/get/motivos-guia", "MotivoGuiaController@getMotivo");
Route::post("/ajs/save/motivos-guia", "MotivoGuiaController@saveMotivo");
Route::post("/ajs/getOne/motivos-guia", "MotivoGuiaController@getOneMotivo");
Route::post("/ajs/update/motivos-guia", "MotivoGuiaController@updateMotivo");
Route::post("/ajs/delete/motivos-guia", "MotivoGuiaController@deleteMotivo");
Route::post("/ajs/set/default-motivo-guia", "MotivoGuiaController@setDefaultMotivo");

// Rutas para Chofer Guia
Route::get("/ajs/get/chofer", "ChoferController@getAll");
Route::post("/ajs/save/chofer", "ChoferController@save");
Route::post("/ajs/update/chofer", "ChoferController@update");
Route::post("/ajs/delete/chofer", "ChoferController@delete");

// Rutas para Vehiculo Guia
Route::get("/ajs/get/vehiculo", "VehiculoController@getAll");
Route::post("/ajs/save/vehiculo", "VehiculoController@save");
Route::post("/ajs/update/vehiculo", "VehiculoController@update");
Route::post("/ajs/delete/vehiculo", "VehiculoController@delete");

// Rutas para Licencia de Conducir Guia
Route::get("/ajs/get/licencia", "LicenciaController@getAll");
Route::post("/ajs/save/licencia", "LicenciaController@save");
Route::post("/ajs/update/licencia", "LicenciaController@update");
Route::post("/ajs/delete/licencia", "LicenciaController@delete");

// Rutas para las acciones AJAX
Route::get('/ajs/certificado/obtener', 'CertificadoController@obtenerCertificado');
Route::post('/ajs/certificado/guardar', 'CertificadoController@guardarCertificado');
Route::post('/ajs/certificado/vista-previa', 'CertificadoController@vistaPrevia');
Route::get('/certificado/preview', 'CertificadoController@mostrarVistaPrevia');
Route::post('/ajs/certificado/subir-imagen', 'CertificadoController@subirImagen');



Route::post("/ajs/data/repuesto/add", "RepuestosController@agregar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/repuesto/delete", "RepuestosController@delete")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/confirmar/traslado/repuesto", "RepuestosController@confirmarTraslado")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/repuesto/add/lista", "RepuestosController@agregarPorLista")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/repuesto/lista", "RepuestosController@listaRepuesto")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/repuesto/edt", "RepuestosController@actualizar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/repuesto/edt/precios", "RepuestosController@actualizarPrecios")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/repuesto/info/code", "RepuestosController@informacionPorCodigo")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/repuesto/info", "RepuestosController@informacion")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/repuesto/restock", "RepuestosController@restock")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/repuesto/add/exel", "RepuestosController@importarExel")->Middleware([ValidarTokenMiddleware::class]);
// Rutas para manejar los precios de repuestos
Route::post("/ajs/data/repuesto/guardar/precios", "RepuestosController@guardarPrecios")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/data/repuesto/obtener/precios", "RepuestosController@obtenerPrecios")->Middleware([ValidarTokenMiddleware::class]);

// Rutas para obtener datos por defecto
Route::get("/ajs/get/terminos/repuestos", "RepuestosController@getTerminosRepuestos");
Route::get("/ajas/get/diagnostico/repuestos", "RepuestosController@getDiagnosticoRepuestos");

// Rutas para condiciones de taller
Route::get("/ajs/get/taller/condiciones/cotizacion/:id", "TallerCotizacionesController@getCondicionCotizacionTaller");
Route::post("/ajs/save/taller/condiciones/cotizacion", "TallerCotizacionesController@saveCondicionCotizacionTaller");
Route::post("/ajs/save/taller/condiciones/temp", "TallerCotizacionesController@saveCondicionTempTaller");

// Rutas para diagnósticos de taller
Route::get("/ajas/get/taller/diagnosticos/cotizacion/:id", "TallerCotizacionesController@getDiagnosticoCotizacionTaller");
Route::post("/ajas/save/taller/diagnosticos/cotizacion", "TallerCotizacionesController@saveDiagnosticoCotizacionTaller");
Route::post("/ajas/save/taller/diagnosticos/temp", "TallerCotizacionesController@saveDiagnosticoTempTaller");



// taller cotizaciones 
Route::post("/ajs/taller/cotizaciones", "TallerCotizacionesController@listar");
Route::post("/ajs/taller/prealerta/info", "TallerCotizacionesController@obtenerInfoPreAlerta");
Route::post("/ajs/taller/cotizaciones/add", "TallerCotizacionesController@agregar");
Route::post("/ajs/taller/cotizaciones/edt", "TallerCotizacionesController@editar");
Route::post("/ajs/taller/cotizaciones/del", "TallerCotizacionesController@eliminarCotizacion");
Route::post("/ajs/taller/cotizacion/detalle", "TallerCotizacionesController@obtenerDetalleCotizacion");
Route::post("/ajs/taller/cotizacion/repuestos", "TallerCotizacionesController@obtenerRepuestosCotizacion");

Route::post("/ajs/taller/cotizaciones/eliminar-foto", "TallerCotizacionesController@eliminarFoto");

Route::post("/ajs/taller/observaciones/get", "TallerCotizacionesController@getObservaciones");
Route::post("/ajs/taller/observaciones/save", "TallerCotizacionesController@saveObservaciones");
Route::post("/ajs/taller/observaciones/temp", "TallerCotizacionesController@saveObservacionesTempTaller");



Route::post("/ajs/prealerta/actualizar-estado-cotizacion", "PreAlertaController@actualizarEstadoCotizacion");
Route::post("/ajs/prealerta/detalles", "PreAlertaController@detalles");



// guia remision 
Route::post("/ajs/guia/remision/info", "GuiaRemisionController@obtenerInfoGuia");
Route::post("/ajs/guia/remision/duplicar", "GuiaRemisionController@duplicarGuiaRemision")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/send/sunat/guiaremision", "GuiaRemisionController@enviarDocumentoSunat")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/guia/remision/obtener", "GuiaRemisionController@obtenerGuiaDuplicada")->Middleware([ValidarTokenMiddleware::class]);

// Rutas para rubros
Route::post("/ajs/rubros/render", "RubrosController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/rubros/add", "RubrosController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/rubros/update", "RubrosController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/rubros/delete", "RubrosController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/rubros/getOne", "RubrosController@getOne")->Middleware([ValidarTokenMiddleware::class]);

Route::get("/ajs/cargar/producto_precios/:id", "CotizacionesController@cargarProductoPrecios");
Route::get("/ajs/cargar/repuesto_precios/:id", "CotizacionesController@cargarRepuestoPrecios");
Route::post("/ajs/taller/verificar-cotizacion", "TallerCotizacionesController@verificarCotizacion");


// Rutas para operaciones AJAX
// Route::post("/ajs/gestion/listar", "GestionArchivosController@listarArchivos")->Middleware([ValidarTokenMiddleware::class]);
// Route::post("/ajs/gestion/guardar", "GestionArchivosController@guardarArchivo")->Middleware([ValidarTokenMiddleware::class]);
// Route::post("/ajs/gestion/actualizar", "GestionArchivosController@actualizarArchivo")->Middleware([ValidarTokenMiddleware::class]);
// Route::post("/ajs/gestion/eliminar", "GestionArchivosController@eliminarArchivo")->Middleware([ValidarTokenMiddleware::class]);
// Route::post("/ajs/gestion/obtener", "GestionArchivosController@obtenerArchivo")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/gestion/productos", "GestionArchivosController@listarProductos")->Middleware([ValidarTokenMiddleware::class]);
// Route::post("/ajs/gestion/adjunto/eliminar", "GestionArchivosController@eliminarAdjunto")->Middleware([ValidarTokenMiddleware::class]);
// Route::post("/ajs/gestion/adjunto/principal", "GestionArchivosController@establecerAdjuntoPrincipal")->Middleware([ValidarTokenMiddleware::class]);

// Rutas para Configuración de Conductores
Route::get("/ajs/get/conductor/configuraciones", "ConductorConfiguracionController@getAll");
Route::post("/ajs/get/conductor/configuraciones/chofer", "ConductorConfiguracionController@getConfiguracionesPorChofer");
Route::post("/ajs/save/conductor/configuracion", "ConductorConfiguracionController@save");
Route::post("/ajs/update/conductor/configuracion", "ConductorConfiguracionController@update"); // NUEVA
Route::post("/ajs/delete/conductor/configuracion", "ConductorConfiguracionController@delete"); // NUEVA
