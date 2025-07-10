<?php
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
Route::get("/ajs/prealerta/buscar/series/cliente", "ConsultasController@buscarSeriesPorClientePreAlerta")->Middleware([ValidarTokenMiddleware::class]);

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

//' CRUD AJAX PARA GARANTIA
Route::post("/ajs/garantia/add", "GarantiaController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/garantia/editar", "GarantiaController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/garantia/render", "GarantiaController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/garantia/getOne", "GarantiaController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/garantia/borrar", "GarantiaController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::get("/ajs/garantia/cargar/datos/serie", "GarantiaController@cargarDatosNumeroSerie")->Middleware([ValidarTokenMiddleware::class]);

Route::post("/ajs/usuarios/render","UsuariosController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/usuarios/getOne","UsuariosController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/usuarios/editar","UsuariosController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/usuarios/borrar","UsuariosController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/roles/getModulosYSubmodulos", "UsuariosController@getModulosYSubmodulos")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/roles/getRolPermisos", "UsuariosController@getRolPermisos")->Middleware([ValidarTokenMiddleware::class]);

Route::post("/ajs/verificar-permiso", "UsuariosController@verificarPermiso")->Middleware([ValidarTokenMiddleware::class]);

// Rutas para Roles
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




// Rutas para fotos de cotizaciones de taller




// Rutas para operaciones AJAX
Route::post("/ajs/fichas-tecnicas/listar", "FichasTecnicasController@listarFichas")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/fichas-tecnicas/guardar", "FichasTecnicasController@guardarFicha")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/fichas-tecnicas/obtener", "FichasTecnicasController@obtenerFicha")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/fichas-tecnicas/eliminar", "FichasTecnicasController@eliminarFicha")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/fichas-tecnicas/compartir-whatsapp", "FichasTecnicasController@compartirWhatsApp")->Middleware([ValidarTokenMiddleware::class]);


// Rutas para informes
Route::get( "/ajs/informe/render",  "InformeController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/getOne",  "InformeController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/insertar",  "InformeController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/editar",  "InformeController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/borrar",  "InformeController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/informe/generarPDF",  "InformeController@generarPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/informe/obtener-template",  "InformeController@obtenerTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/guardar-template",  "InformeController@guardarTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/informe/vista-previa",  "InformeController@vistaPreviaPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/informe/getTipos",  "InformeController@getTipos")->Middleware([ValidarTokenMiddleware::class]);

Route::get("/ajs/informe/obtener-tipos-informe", "InformeController@obtenerTiposInforme")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/informe/insertar-tipo-informe", "InformeController@insertarTipoInforme")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/informe/editar-tipo-informe", "InformeController@editarTipoInforme")->Middleware([ValidarTokenMiddleware::class]);
Route::post("/ajs/informe/eliminar-tipo-informe", "InformeController@eliminarTipoInforme")->Middleware([ValidarTokenMiddleware::class]);

// Rutas para cartas
Route::get( "/ajs/carta/render",  "CartaController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/carta/getOne",  "CartaController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/carta/insertar",  "CartaController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/carta/editar",  "CartaController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/carta/borrar",  "CartaController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/carta/generarPDF",  "CartaController@generarPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/carta/obtener-template",  "CartaController@obtenerTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/carta/guardar-template",  "CartaController@guardarTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/carta/vista-previa",  "CartaController@vistaPreviaPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/carta/getTipos",  "CartaController@getTipos")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/carta/obtener-membretes",  "CartaController@obtenerMembretes")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/carta/guardar-membretes",  "CartaController@guardarMembretes")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/carta/obtener-tipos-cartas",  "CartaController@obtenerTiposCartas")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/carta/insertar-tipo-carta",  "CartaController@insertarTipoCarta")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/carta/editar-tipo-carta",  "CartaController@editarTipoCarta")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/carta/eliminar-tipo-carta",  "CartaController@eliminarTipoCarta")->Middleware([ValidarTokenMiddleware::class]);

// Rutas para constancias
Route::get( "/ajs/constancia/render",  "ConstanciaController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/constancia/getOne",  "ConstanciaController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/constancia/insertar",  "ConstanciaController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/constancia/editar",  "ConstanciaController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/constancia/borrar",  "ConstanciaController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/constancia/generarPDF",  "ConstanciaController@generarPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/constancia/obtener-template",  "ConstanciaController@obtenerTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/constancia/guardar-template",  "ConstanciaController@guardarTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/constancia/vista-previa",  "ConstanciaController@vistaPreviaPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/constancia/getTipos",  "ConstanciaController@getTipos")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/constancia/obtener-tipos-constancias",  "ConstanciaController@obtenerTipoConstancias")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/constancia/insertar-tipo-constancia",  "ConstanciaController@insertarTipoConstancia")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/constancia/editar-tipo-constancia",  "ConstanciaController@editarTipoConstancia")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/constancia/eliminar-tipo-constancia",  "ConstanciaController@eliminarTipoConstancia")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/constancia/obtener-membretes",  "ConstanciaController@obtenerMembretes")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/constancia/guardar-membretes",  "ConstanciaController@guardarMembretes")->Middleware([ValidarTokenMiddleware::class]);

// Rutas para archivos internos
Route::get( "/ajs/archivo-interno/render",  "ArchivoInternoController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/archivo-interno/getOne",  "ArchivoInternoController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/archivo-interno/insertar",  "ArchivoInternoController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/archivo-interno/editar",  "ArchivoInternoController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/archivo-interno/borrar",  "ArchivoInternoController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/archivo-interno/generarPDF",  "ArchivoInternoController@generarPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/archivo-interno/obtener-template",  "ArchivoInternoController@obtenerTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/archivo-interno/guardar-template",  "ArchivoInternoController@guardarTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/archivo-interno/vista-previa",  "ArchivoInternoController@vistaPreviaPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/archivo-interno/getTipos",  "ArchivoInternoController@getTipos")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/archivo-interno/obtener-tipos-archivos",  "ArchivoInternoController@obtenerTiposArchivos")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/archivo-interno/insertar-tipo-archivo",  "ArchivoInternoController@insertarTipoArchivos")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/archivo-interno/editar-tipo-archivo",  "ArchivoInternoController@editarTipoArchivo")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/archivo-interno/eliminar-tipo-archivo",  "ArchivoInternoController@eliminarTipoArchivo")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/archivo-interno/obtener-membretes",  "ArchivoInternoController@obtenerMembretes")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/archivo-interno/guardar-membretes",  "ArchivoInternoController@guardarMembretes")->Middleware([ValidarTokenMiddleware::class]);


// Rutas para otros archivos
Route::get( "/ajs/otro-archivo/render",  "OtroArchivoController@render")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/getOne",  "OtroArchivoController@getOne")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/insertar",  "OtroArchivoController@insertar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/editar",  "OtroArchivoController@editar")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/borrar",  "OtroArchivoController@borrar")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/otro-archivo/generarPDF",  "OtroArchivoController@generarPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/otro-archivo/obtener-template",  "OtroArchivoController@obtenerTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/guardar-template",  "OtroArchivoController@guardarTemplate")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/vista-previa",  "OtroArchivoController@vistaPreviaPDF")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/otro-archivo/getTipos",  "OtroArchivoController@getTipos")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/otro-archivo/getMotivos",  "OtroArchivoController@getMotivos")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/compartir-whatsapp",  "OtroArchivoController@compartirWhatsApp")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/otro-archivo/obtener-tipos-archivos",  "OtroArchivoController@obtenerTiposOtrosArchivos")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/insertar-tipo-archivo",  "OtroArchivoController@insertarTipoOtrosArchivos")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/editar-tipo-archivo",  "OtroArchivoController@editarTipoOtrosArchivos")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/eliminar-tipo-archivo",  "OtroArchivoController@eliminarTipoOtrosArchivos")->Middleware([ValidarTokenMiddleware::class]);
Route::get( "/ajs/otro-archivo/obtener-membretes",  "OtroArchivoController@obtenerMembretes")->Middleware([ValidarTokenMiddleware::class]);
Route::post( "/ajs/otro-archivo/guardar-membretes",  "OtroArchivoController@guardarMembretes")->Middleware([ValidarTokenMiddleware::class]);





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
