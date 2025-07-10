<?php

Route::get('/login',"ViewController@login");
Route::get('/logout',"UsuarioController@logout");
Route::get('/ge/bar/code',"ConsultaDelcontroller@generarBarCode");
Route::get('/ge/bar/code2',"ConsultaDelcontroller@generarBarCode2");

Route::get('/venta/comprobante/pdf/ma4/:venta',"ReportesVentaController@comprobanteVentaMa4");
Route::get('/venta/comprobante/pdf/ma4/:venta/:nombre',"ReportesVentaController@comprobanteVentaMa4");
Route::get('/venta/comprobante/pdf/:venta',"ReportesVentaController@comprobanteVenta");
Route::get('/venta/comprobante/pdf/:venta/:nombre',"ReportesVentaController@comprobanteVenta");
Route::get('/venta/comprobante/pdfd/:venta/:nombre',"ReportesVentaController@comprobanteVentaBinario");
Route::get('/guia/remision/pdf/:guia','ReportesVentaController@guiaRemision');
Route::get('/nota/electronica/pdf/:nota','ReportesVentaController@comprobanteNotaE');
Route::get('/nota/electronica/pdf/:nota/:nombre','ReportesVentaController@comprobanteNotaE');
Route::get('/guia/remision/pdf/:guia/:nombre','ReportesVentaController@guiaRemision');


//pdf para voucher de venta

/* Route::get('/venta/comprobante/pdf/:voucher',"ReportesVentaController@comprobanteVenta"); */
Route::get("/r/cotizaciones/reporte/:coti","ReportesVentaController@comprobanteCotizacion");
Route::get("/r/cotizaciones/reporte-media-a4/:coti", "ReportesVentaController@comprobanteCotizacionMediaA4");
Route::get("/r/cotizaciones/reporte-voucher-8cm/:coti", "ReportesVentaController@comprobanteCotizacionVoucher8cm");
Route::get("/r/cotizaciones/reporte-voucher-5-6cm/:coti", "ReportesVentaController@comprobanteCotizacionVoucher5_6cm");

Route::get("/reporte/ventas/pdf/:periodo","GeneradoresController@reportePeriodoVenta");
Route::get("/reporte/ventas/producto/lista/pdf/","ReportesVentaController@reporteVentaPorProducto");
Route::get("/reporte/ventasganancias/pdf/:id","GeneradoresController@reportePeriodoVentaGanancias");

Route::get('/venta/pdf/voucher/8cm/:voucher',"ReportesVentaController@imprimirvoucher8cm");
Route::get('/venta/pdf/voucher/8cm/:voucher/:nom',"ReportesVentaController@imprimirvoucher8cm");
Route::get('/venta/pdf/voucher/5.6cm/:voucher',"ReportesVentaController@imprimirvoucher5_6cm");
Route::get('/venta/pdf/voucher/5.6cm/:voucher/:nom',"ReportesVentaController@imprimirvoucher5_6cm");


Route::get("/escanear/codigobarra/:empresa/:sucursal","ViewController@escanearBarra");


Route::baseStatic("ViewController@index",[ValidarTokenMiddleware::class]);

Route::postBase("/","FragmentController@home");
Route::postBase("/administrarempresas","FragmentController@adminEmpresas");
Route::postBase("/administrarempresas/ventas/:empresa","FragmentController@adminEmpresasVentas");
Route::postBase("/pagos","FragmentController@pagos");

Route::postBase("/caja/flujo","FragmentController@cajaFlujo");
Route::postBase("/cajaRegistros","FragmentController@cajaRegistros");

Route::postBase("/compras","FragmentController@compras");
Route::postBase("/compras/add","FragmentController@comprasAdd");

Route::postBase("/cobranzas","FragmentController@cobranzas");


Route::postBase("/cotizaciones","FragmentController@cotizaciones");
Route::postBase("/cotizaciones/add","FragmentController@cotizacionesAdd");
Route::postBase("/cotizaciones/edt/:coti","FragmentController@cotizacionesEdt");

Route::postBase("/nota/electronica","FragmentController@notaElectronica");
Route::postBase("/nota/electronica/lista","FragmentController@notaElectronicaLista");

Route::postBase("/almacen/productos","FragmentController@almacenProductos");
Route::postBase("/almacen/productos/add","FragmentController@productoAdd");
Route::postBase("/test","FragmentController@test");

Route::postBase("/almacen/intercambio/productos","FragmentController@almacenIntercambioProductos");
/* Route::postBase("/almacen/intercambio/productos/add","FragmentController@productoAdd"); */

Route::postBase("/calendario","FragmentController@calendarioCliente");
Route::postBase("/clientes","FragmentController@clientesLista");
Route::postBase("/ventas","FragmentController@ventas");
Route::postBase("/guias/remision","FragmentController@guiaRemision");
Route::postBase("/ventas/productos","FragmentController@ventasProductos");
Route::postBase("/ventas/servicios","FragmentController@ventasServicios");
Route::postBase("/guia/remision/registrar","FragmentController@guiaRemisionAdd");
Route::postBase("/guia/remision/manual/registrar","FragmentController@guiaRemisionAddManual");

/* Route::postBase("/guia/remision/registrar/coti","FragmentController@guiaRemisionAddByCoti"); */
Route::postBase("/cuentas/cobrar","FragmentController@cuentasPorCobrar");


Route::postBase("/editar-venta-producto/:idVenta","FragmentController@editarVentaProducto");
Route::postBase("/editar-venta-servicio/:idVenta","FragmentController@editarVentaServicio");

// taller cotizaciones
Route::postBase("/taller/cotizaciones", "FragmentController@tallerCotizaciones");


Route::get("/reporte/excel/:fecha","GenerarReporte@generarExcel");
Route::get("/reporte/producto/excel","GenerarReporte@generarExcelProducto");
Route::get("/reporte/registros/excel/", "GenerarRegistros@generarExcelSeries");
Route::get("/reporte/rvta/excel/:fecha","GenerarReporte@generarExcelRVTA");
/* Route::get("/reporte/excel/test2","GenerarReporte@testExcel"); */
Route::get("/reporte/ingresos/egresos/:id","GenerarReporte@ingresosEgresos");
Route::get("/reporte/producto/guia","GenerarReporte@generarExcelProductoImporte");
Route::get("/reporte/caja/excel/:id","GenerarReporte@generarExcelCaja");
Route::postBase("/reporte/cotizaciones/vendedores", "GenerarReporte@reporteVentaPorVendedor");




Route::get("/reporte/cliente/:id","ReportesVentaController@reporteCliente");

Route::get("/reporte/compras/pdf/:id","ReportesVentaController@reporteCompra");


Route::get("/reporte/productos/pdf/:id","ReportesVentaController@reporteProductos");


Route::get("/reporte/compras","ReportesVentaController@reporteCompraAll");
Route::postBase("/usuarios","FragmentController@usuariosLista");
// categorias
Route::postBase("/categorias","FragmentController@categoria");

// unidades
Route::postBase("/unidades","FragmentController@unidad");

Route::postBase("/numeroseries", "FragmentController@numeroseries");// Numero series

Route::postBase("/garantia", "FragmentController@garantia");// garantia
Route::postBase("/garantia/add", "FragmentController@garantiaAdd");
Route::postBase("/garantia/manual", "FragmentController@garantiaManual");

// ordenes de trabajo

Route::postBase("/preAlerta","FragmentController@preAlerta"); // preAlerta
Route::postBase("/tecnicos", "FragmentController@tecnicos");// tecnicos
Route::postBase("/marcas", "FragmentController@marcas");// marcas
Route::postBase("/modelos", "FragmentController@modelos"); //modelos
Route::postBase("/equipos", "FragmentController@equipos");// equipos
Route::postBase("/orden/repuestos", "FragmentController@repuestos"); //repuestos

Route::postBase("/gestion/activos","FragmentController@gestionActivos");

// orden de servicio
Route::postBase("/servicio/prealerta","FragmentController@preAlertaServicio"); // preAlerta
Route::postBase("/precotizaciones/add", "FragmentController@precotizacionesAdd"); //precotizaciones add
Route::postBase("/precotizaciones", "FragmentController@precotizaciones"); //precotizaciones
Route::postBase("/servicio/tecnico", "FragmentController@tecnicoServicio");// tecnico servicio
Route::postBase("/servicio/marca", "FragmentController@marcaServicio");// marca servicio
Route::postBase("/servicio/modelo", "FragmentController@modeloServicio"); //modelo servicio
Route::postBase("/servicio/equipo", "FragmentController@equipoServicio");// equipo servicio
Route::postBase("/servicio/repuesto", "FragmentController@repuestoServicio"); //repuestos
Route::postBase("/taller","FragmentController@taller");

//gestion activos

Route::postBase("/registro/activos","FragmentController@registroActivos");
Route::postBase("/maquina","FragmentController@maquinaActivos");
Route::postBase("/motivo","FragmentController@motivo");

// reporte taller

Route::get("/r/taller/reporte/:id","ReporteTallerController@generateReport");
Route::get("/r/garantia/certificado/:id","CertificadoGarantia@garantiaCertificado");

// detalle prealerta pdf
Route::get("/r/detalle/prealerta/:id_prealerta","DetallePrealertaController@generarPDF");

Route::get("/r/dashboard/reporte", "ReporteDashboardController@generateReport");
Route::get("/r/dashboard/reporte-excel", "ReporteExcelDashboardController@generateExcelReport");

Route::postBase("/agregar/imagenes","FragmentController@fotosTaller");
Route::postBase("/taller/coti/view","FragmentController@vistacotiTaller");
Route::postBase("/edt/coti/taller","FragmentController@edtTaller");

// guia duplicada 

Route::postBase("/guia/remision/duplicada" , "FragmentController@guiaRemisionDuplicada");

Route::postBase("/acceso-denegado", "FragmentController@accesoDenegado");

Route::postBase("/categorias/repuestos", "FragmentController@categoriaRepuesto");
Route::postBase("/unidades/repuestos", "FragmentController@unidadRepuesto");
Route::get("/gestion/activos/descargar-pdf/:id", "GestionActivosController@descargarPDF");

Route::postBase("garantia/editar-certificado", "FragmentController@certificadoGarantia");


Route::get("/r/taller/inventario/:id", "InventarioTallerController@generateInventarioReport");
Route::get("/r/taller/inventario/excel/:id", "InventarioTallerController@exportInventarioExcel");

// Rutas para reportes individuales (por ID)
Route::get("/r/taller/inventario/:id", "ReporteInventarioController@generarReporteInventarioPDF");
Route::get("/r/taller/inventario/excel/:id", "ReporteInventarioController@generarReporteInventarioExcel");

// Rutas para reportes por período
Route::get("/reportes/inventario/pdf", "ReporteInventarioController@generarReporteInventarioPDF");
Route::get("/reportes/inventario/excel", "ReporteInventarioController@generarReporteInventarioExcel");

// Ruta para verificar si hay datos disponibles
Route::get("/reportes/verificar-datos", "ReporteInventarioController@verificarDatosDisponibles");

Route::postBase("/documentos", "FragmentController@documentos");
Route::postBase("/documentos/informe", "FragmentController@documentosInformes");
Route::postBase("/documentos/cartas", "FragmentController@documentosCartas");
Route::postBase("/documentos/constancias", "FragmentController@documentosConstancias");
Route::postBase("/documentos/archivos/internos", "FragmentController@documentosArchivosInternos");
Route::postBase("/documentos/otros", "FragmentController@documentosOtros");


