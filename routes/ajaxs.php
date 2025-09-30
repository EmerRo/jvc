<?php
require_once "utils/DocumentRouteGenerator.php";
Route::post('/ajs/generar/txt/ventareporte',"GeneradoresController@generarTextLibroVentas")->Middleware([ValidarTokenMiddleware::class]);

/* ============================ INICIO Ventas controller rutas ======================================*/
Route::get('/ajs/ventas',"VentasController@listarVentas")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/ventas/add',"VentasController@guardarVentas")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/ingreso/almacen/add',"VentasController@ingresoAlmacen")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/egreso/almacen/add',"VentasController@egresoAlmacen")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/confirmar/traslado',"VentasController@confirmarTraslado")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/ingresos/egresos/render","VentasController@ingresosEgresosRender")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/ventas/servicios/edit',"VentasController@editVentaServicio")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/ventas/productos/edit',"VentasController@editVentaProducto")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/venta/consultas/tipo/venta","VentasController@tipoVenta")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/venta/detalle","VentasController@detalleVenta")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/venta/anular","VentasController@anularVenta")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajas/ventas/porempresa", "VentasController@listaVentasPorEmpresa");
Route::post("/ajas/ventas/porempresa/regenxml", "VentasController@regenerarXML");
Route::post("/ajas/ventas/porempresa/sendsunat", "VentasController@enviarDocumentoSunatPorEmpresa");
Route::post("/ajas/ventas/porempresa/sendsunatresumen", "VentasController@envioResumenDiarioPorEmpresa");
Route::post("/ajas/ventas/porempresa/sendsunatcomubaja", "VentasController@envioComunicacionBajaPorEmpresa");
Route::post("/ajs/send/sunat/venta", "VentasController@enviarDocumentoSunat")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/taller/cotizaciones/info","VentasController@obtenerInfoCotizacionTaller")->Middleware([ValidarTokenMiddleware::class]);
/* ============================ FIN Ventas controller rutas ======================================*/

Route::post('/login',"UsuarioController@login")->Middleware([ValidarTokenMiddleware::class]);
Route::get("/check-session", "UsuarioController@checkSession");


/* ============================  ConsultasController rutas ======================================*/

Route::get('/ajs/cargar/productos/:id',"ConsultasController@buscarProducto")->Middleware([ValidarTokenMiddleware::class]);
Route::get('/ajs/cargar/repuestos/:id',"ConsultasController@buscarRepuesto")->Middleware([ValidarTokenMiddleware::class]);
Route::get('/ajs/cargar/productos',"ConsultasController@buscarProductoCoti")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/cargar/venta/servicios',"ConsultasController@cargarVentaServicios")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/cargar/venta/productos',"ConsultasController@cargarVentaProductos")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/cargar/venta/info',"ConsultasController@cargarVentaDetalles")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/sn","ConsultasController@buscarSNdoc")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/ruc","ConsultasController@consultaRuc")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/lista/provincias","ConsultasController@listarProvincias")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/lista/distrito","ConsultasController@listarDistri")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/consulta/guia/documentofb","ConsultasController@consultvfb")->Middleware([ValidarTokenMiddleware::class]);

Route::post('/ajs/consulta/doc/cliente',"ConsultasController@buscarDocInfo")->Middleware([ValidarTokenMiddleware::class]);

Route::get('/ajs/consulta/buscar/dtatranspor',"ConsultasController@buscarTransporteGui")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/consulta/add/dtatranspor',"ConsultasController@agregarTransportista")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/consulta/prod/coti',"ConsultasController@buscarProdId")->Middleware([ValidarTokenMiddleware::class]);

Route::get('/ajs/buscar/cliente/datos',"ConsultasController@buscarDataCliente")->Middleware([ValidarTokenMiddleware::class]);
Route::get('/ajs/prealerta/buscar/serie/datos',"ConsultasController@buscarDataSeriePreAlerta")->Middleware([ValidarTokenMiddleware::class]);
Route::get("/ajs/prealerta/buscar/cliente/serie", "ConsultasController@buscarClienteSeriePreAlerta")->Middleware([ValidarTokenMiddleware::class]);

Route::get("/ajs/prealerta/buscar/ns", "ConsultasController@buscarPreAlertaPorNS")->Middleware([ValidarTokenMiddleware::class]);

Route::get('/ajs/prealerta/buscar/numero/datos',"ConsultasController@buscarDataNumeroPreAlerta")->Middleware([ValidarTokenMiddleware::class]);

Route::post('/ajs/consulta/buscar/producto',"ConsultasController@buscarProducto")->Middleware([ValidarTokenMiddleware::class]);

Route::get("/ajs/buscar/cliente/serie", "ConsultasController@buscarClienteSerie");
Route::get("/ajs/buscar/series/cliente", "ConsultasController@buscarSeriesPorCliente");
Route::get('/ajs/buscar/serie/datos',"ConsultasController@buscarDataSerie");


/* ============================ INICIO GUIA controller rutas ======================================*/

Route::post('/ajs/guia/remision/add',"GuiaRemisionController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/guia/remision/add3',"GuiaRemisionController@insertarManual")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/guia/remision/add2',"GuiaRemisionController@insertar2")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/guia/remision/actualizar-producto', "GuiaRemisionController@actualizarProductoGuia")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/guia/remision/info", "GuiaRemisionController@obtenerInfoGuia");
Route::post("/ajs/guia/remision/duplicar", "GuiaRemisionController@duplicarGuiaRemision")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/send/sunat/guiaremision", "GuiaRemisionController@enviarDocumentoSunat")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/guia/remision/obtener", "GuiaRemisionController@obtenerGuiaDuplicada")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/guia/remision/coti/:id", "GuiaRemisionController@consultarGuiaXCoti")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/guia/remision/coti/cliente/:id", "GuiaRemisionController@consultarGuiaXCotiCliente")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/guia/remision/coti/taller/:id", "GuiaRemisionController@consultarGuiaXCotiTaller")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/guia/remision/coti/taller/cliente/:id", "GuiaRemisionController@consultarGuiaXCotiTallerCliente")->Middleware([ValidarTokenMiddleware::class]);

/* ============================ FIN GUIA controller rutas ======================================*/


// Rutas para el CRUD de GestionActivos
Route::post("/ajs/gestion/activos/add", "GestionActivosController@insertar")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/gestion/activos/update", "GestionActivosController@actualizarActivo")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/gestion/activos/render", "GestionActivosController@listarActivos")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/gestion/activos/get/:id", "GestionActivosController@obtenerActivoPorId")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/gestion/activos/delete", "GestionActivosController@eliminarActivo")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/gestion/activos/confirmar", "GestionActivosController@confirmarActivo")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/gestion/activos/obtener", "GestionActivosController@obtenerActivo")->middleware([ValidarTokenMiddleware::class]);


Route::post("/ajs/clientes/add","ClientesController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/add/por/lista","ClientesController@insertarXLista")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/render","ClientesController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/getOne","ClientesController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/editar","ClientesController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/borrar","ClientesController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/clientes/add/exel","ClientesController@importarExcel")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/cuentas/cobrar","ClientesController@cuentasCobrar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/cuentas/cobrar/estado","ClientesController@cuentasCobrarEstado")->Middleware([ValidarTokenMiddleware::class]);

// ================  GarantiaController ===================================

Route::post("/ajs/garantia/add", "GarantiaController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/garantia/editar", "GarantiaController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/garantia/render", "GarantiaController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/garantia/getOne", "GarantiaController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/garantia/borrar", "GarantiaController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::get("/ajs/garantia/cargar/datos/serie", "GarantiaController@cargarDatosNumeroSerie")->Middleware([ValidarTokenMiddleware::class]); 

// ================  UsuariosController ===================================
Route::post("/ajs/usuarios/render","UsuariosController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/usuarios/getOne","UsuariosController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/usuarios/editar","UsuariosController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/usuarios/borrar","UsuariosController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/add/users","UsuariosController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/roles/getModulosYSubmodulos", "UsuariosController@getModulosYSubmodulos")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/roles/getRolPermisos", "UsuariosController@getRolPermisos")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/verificar-permiso", "UsuariosController@verificarPermiso")->Middleware([ValidarTokenMiddleware::class]);

// ================  NotificacionController ===================================
Route::post("/ajs/notificaciones/no-leidas", "NotificacionController@obtenerNoLeidas")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/notificaciones/contar", "NotificacionController@contarNoLeidas")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/notificaciones/marcar-leida", "NotificacionController@marcarComoLeida")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/notificaciones/marcar-todas-leidas", "NotificacionController@marcarTodasComoLeidas")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/notificaciones/obtener-todas", "NotificacionController@obtenerTodas")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/notificaciones/crear", "NotificacionController@crear")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/notificaciones/limpiar-antiguas", "NotificacionController@limpiarAntiguas")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/notificaciones/usuarios-online", "NotificacionController@obtenerUsuariosOnline")->Middleware([ValidarTokenMiddleware::class]);
Route::get("/stream/notificaciones", "NotificacionController@streamNotificaciones")->Middleware([ValidarTokenMiddleware::class]);
// ================  RolesController ===================================
Route::post("/ajs/roles/render", "RolesController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/roles/getOne", "RolesController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/roles/crear", "RolesController@crear")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/roles/editar", "RolesController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/roles/borrar", "RolesController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/roles/getModulos", "RolesController@getModulos")->Middleware([ValidarTokenMiddleware::class]);


Route::get('/ajs/buscar/maquina/datos',"RegistroMaquinaController@buscarDataMaquina")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/imagenes/guardar", "ImagenesController@guardarImagenes")->Middleware([ValidarTokenMiddleware::class]);



Route::get("/ajs/server/sider/productos","ProductosController@listaProductoServerSide");
Route::get("/ajs/server/sider/repuestos","RepuestosController@listaRepuestoServerSide");
Route::post('/ajs/data/producto/aumentar/stock', "ProductosController@aumentarStock")->Middleware([ValidarTokenMiddleware::class]);
Route::post('/ajs/data/repuesto/aumentar/stock', "RepuestosController@aumentarStock")->Middleware([ValidarTokenMiddleware::class]);

Route::post("/ajs/data/productos/grid", "ProductosController@productosGrid")->Middleware([ValidarTokenMiddleware::class]);



// Rutas para fotos de cotizaciones de taller




// Rutas para operaciones AJAX
Route::post("/ajs/fichas-tecnicas/listar", "FichasTecnicasController@listarFichas")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/fichas-tecnicas/guardar", "FichasTecnicasController@guardarFicha")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/fichas-tecnicas/obtener", "FichasTecnicasController@obtenerFicha")->Middleware([ValidarTokenMiddleware::class]);
Route::get("/ajs/fichas-tecnicas/info-completa/:id_ficha", "FichasTecnicasController@obtenerInfoCompleta")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/fichas-tecnicas/eliminar", "FichasTecnicasController@eliminarFicha")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/fichas-tecnicas/compartir-whatsapp", "FichasTecnicasController@compartirWhatsApp")->Middleware([ValidarTokenMiddleware::class]);


// Rutas para informes
Route::get( "/ajs/informe/render",  "InformeController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/getOne",  "InformeController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/insertar",  "InformeController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/editar",  "InformeController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/borrar",  "InformeController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/informe/generarPDF",  "InformeController@generarPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/informe/generarPDFBase64",  "InformeController@generarPDFBase64")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/informe/obtener-template",  "InformeController@obtenerTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/guardar-template",  "InformeController@guardarTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/vista-previa",  "InformeController@vistaPreviaPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/informe/getTipos",  "InformeController@getTipos")->Middleware([ValidarTokenMiddleware::class]);

Route::get("/ajs/informe/obtener-tipos-informe", "InformeController@obtenerTiposInforme")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/informe/insertar-tipo-informe", "InformeController@insertarTipoInforme")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/informe/editar-tipo-informe", "InformeController@editarTipoInforme")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/informe/eliminar-tipo-informe", "InformeController@eliminarTipoInforme")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/informe/compartir-whatsapp", "InformeController@compartirWhatsApp")->Middleware([ValidarTokenMiddleware::class]);

// Registrar rutas para cartas
DocumentRouteGenerator::registerRoutes('carta', 'CartaController');

// Registrar rutas para constancias  
DocumentRouteGenerator::registerRoutes('constancia', 'ConstanciaController', );

DocumentRouteGenerator::registerRoutes('archivoInterno', 'ArchivoInternoController', );
DocumentRouteGenerator::registerRoutes('otroArchivo', 'OtroArchivoController', );





//CRUD AJAX PARA PREALERTA
Route::post('/ajs/prealerta/doc/cliente',"PreAlertaController@buscarDocInfo")->Middleware([ValidarTokenMiddleware::class]);

//CRUD AJAX PARA ORDEN DE TRABAJO
Route::post("/ajs/orden-trabajo/add", "OrdenTrabajoController@insertar")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-trabajo/update", "OrdenTrabajoController@editar")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-trabajo/render", "OrdenTrabajoController@render")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-trabajo/get/:id", "OrdenTrabajoController@getOne")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-trabajo/delete", "OrdenTrabajoController@borrar")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-trabajo/culminar", "OrdenTrabajoController@culminarTrabajo")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-trabajo/detalles", "OrdenTrabajoController@detalles")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-trabajo/repuestos/obtener", "OrdenTrabajoController@obtenerRepuestos")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-trabajo/repuestos/agregar", "OrdenTrabajoController@agregarRepuesto")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-trabajo/repuestos/eliminar", "OrdenTrabajoController@eliminarRepuesto")->middleware([ValidarTokenMiddleware::class]);


//CRUD AJAX PARA ORDEN DE SERVICIO
Route::post("/ajs/orden-servicio/add", "OrdenServicioController@insertar")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-servicio/update", "OrdenServicioController@editar")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-servicio/render", "OrdenServicioController@render")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-servicio/get/:id", "OrdenServicioController@getOne")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-servicio/delete", "OrdenServicioController@borrar")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-servicio/culminar", "OrdenServicioController@culminarTrabajo")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/orden-servicio/detalles", "OrdenServicioController@detalles")->middleware([ValidarTokenMiddleware::class]);

// Rutas para el controlador unificado de taller
Route::post("/ajs/taller/render-unificado", "TallerController@renderUnificado")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/taller/detalles-unificado", "TallerController@detallesUnificado")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/taller/culminar-unificado", "TallerController@culminarTrabajoUnificado")->middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/taller/delete-unificado", "TallerController@borrarUnificado")->middleware([ValidarTokenMiddleware::class]);
