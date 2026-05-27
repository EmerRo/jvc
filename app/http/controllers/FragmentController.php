<?php

class FragmentController extends Controller
{
    public function home()
    {
        return $this->view("fragment-views/cliente/home");
    }
    public function cotizacionesEdt($coti)
    {
        return $this->view("fragment-views/cliente/cotizaciones-edt", ["coti" => $coti]);
    }
    public function adminEmpresasVentas($empresa)
    {
        return $this->view("fragment-views/cliente/admin-empresas-ventas", ["emprCod" => $empresa]);
    }
    public function adminEmpresas()
    {
        return $this->view("fragment-views/cliente/admin-empresas");
    }
    public function pagos()
    {
        return $this->view("fragment-views/cliente/pagos");
    }
    public function comprasAdd()
    {
        return $this->view("fragment-views/cliente/compra-add");
    }
    public function comprasEdit($id = null)
    {
        return $this->view("fragment-views/cliente/compra-edit", ['id' => $id]);
    }
    public function compras()
    {
        return $this->view("fragment-views/cliente/compras");
    }
    public function cajaFlujo()
    {
        return $this->view("fragment-views/cliente/flujo-caja");
    }
    public function cajaRegistros()
    {
        return $this->view("fragment-views/cliente/caja-registros");
    }
    public function cobranzas()
    {
        return $this->view("fragment-views/cliente/cobranzas");
    }
    public function cotizacionesAdd()
    {
        return $this->view("fragment-views/cliente/cotizaciones-add");
    }
    public function cotizaciones()
    {
        return $this->view("fragment-views/cliente/cotizaciones");
    }
    public function ventas()
    {
        return $this->view("fragment-views/cliente/ventas");
    }
    public function notaElectronicaLista()
    {
        return $this->view("fragment-views/cliente/nota-electronica-lista");
    }
    public function notaElectronica()
    {
        return $this->view("fragment-views/cliente/nota-electronica");
    }
public function ventasProductos()
{
    $request = $this->getRequest();
    
    $coti = $request->coti;
    $coti_taller = $request->{"coti-taller"};
    $guia = $request->guia;
    
    // DEBUG: Verificar qué parámetros están llegando
    // error_log("🔍 FragmentController ventasProductos() - Parámetros recibidos:");
    // error_log("  - coti: " . ($coti ?? 'NULL'));
    // error_log("  - coti_taller: " . ($coti_taller ?? 'NULL'));
    // error_log("  - guia: " . ($guia ?? 'NULL'));
    // error_log("  - _GET: " . json_encode($_GET));
    // error_log("  - _POST: " . json_encode($_POST));
    
    return $this->view("fragment-views/cliente/ventas-productos", [
        'coti' => $coti,
        'coti_taller' => $coti_taller,
        'guia' => $guia
    ]);
}


    public function ventasServicios()
    {
        return $this->view("fragment-views/cliente/ventas-servicios");
    }
    public function test()
    {
        return $this->view("fragment-views/cliente/test");
    }
    public function calendarioCliente()
    {
        return $this->view("fragment-views/cliente/calendario");
    }
    public function guiaRemision()
    {
        return $this->view("fragment-views/cliente/guia-remision");
    }
    public function guiaRemisionAdd()
    {
        return $this->view("fragment-views/cliente/guia-remision-add");
    }
    public function guiaRemisionAddManual()
    {
        return $this->view("fragment-views/cliente/guia-remision-add-manual");
    }
    public function almacenProductos()
    {
        return $this->view("fragment-views/cliente/almacen-productos");
    }

    public function almacenEquipos()
    {
        return $this->view("fragment-views/cliente/almacen-equipos");
    }

    public function almacenIntercambioProductos()
    {
        return $this->view("fragment-views/cliente/intercambio-productos");
    }
    public function clientesLista()
    {
        return $this->view("fragment-views/cliente/clientes");
    }
    public function productoAdd()
    {
        return $this->view("fragment-views/cliente/add-producto");
    }
    public function cuentasPorCobrar()
    {
        return $this->view("fragment-views/cuentascobrar");
    }
    public function reporteExcel()
    {
        return $this->view("fragment-views/cliente/reporte-excel");
    }
    public function tes()
    {
        return "hola";
    }
    public function editarVentaServicio($idVenta)
    {
        return $this->view("fragment-views/cliente/editar-venta-servicio", ["idVenta" => $idVenta]);
     
    }
    public function editarVentaProducto($idVenta)
    {
        return $this->view("fragment-views/cliente/editar-venta-producto", ["idVenta" => $idVenta]);
     
    }
    
    public function usuariosLista()
    {
        return $this->view("fragment-views/cliente/usuarios");
    }

    public function miEmpresa()
    {
        return $this->view("fragment-views/cliente/mi-empresa");
    }

	public function categoria()
    {
        return $this->view("fragment-views/cliente/categoria");
    }

    // unidad
	public function unidad()
    {
        return $this->view("fragment-views/cliente/unidad");
    }
    // orden de trabajo
	public function preAlerta()
    {
        return $this->view("fragment-views/cliente/ordenTrabajo/pre-alerta");
    }
    
    public function tecnicos()    // tecnico
    {
        return $this->view("fragment-views/cliente/ordenTrabajo/tecnico");
    }
  
    public function marcas()   // marca
    {
        return $this->view("fragment-views/cliente/ordenTrabajo/marca");
    }

    public function equipos()    // equipo
    {
        return $this->view("fragment-views/cliente/ordenTrabajo/equipo");
    }
    public function modelos()    // modelo
    {
        return $this->view("fragment-views/cliente/ordenTrabajo/modelo");
    }

    public function repuestos()
    {
        return $this->view("fragment-views/cliente/ordenTrabajo/repuestos"); //repuestos
    }

    // taller
    public function taller (){
        return $this->view("fragment-views/cliente/taller");
    }



    //  orden de servicio
	public function preAlertaServicio()
    {
        return $this->view("fragment-views/cliente/ordenServicio/pre-alerta-servicio");
    }
    public function precotizaciones()
    {
        return $this->view("fragment-views/cliente/odenServicio/pre-cotizaciones");
    }
	public function repuestoServicio()
    {
        return $this->view("fragment-views/cliente/ordenServicio/repuestos"); //repuestos
    }

    public function precotizacionesAdd()
    {
        return $this->view("fragment-views/cliente/ordenServicio/pre-cotizaciones-add");
    }
    public function registroActivos()
    {
        return $this->view("fragment-views/cliente/ordenServicio/registro-activos");
    }
    public function tecnicoServicio()  // tecnico
    {
        return $this->view("fragment-views/cliente/ordenServicio/tecnico");
    }
   
    public function marcaServicio()  // marca
    {
        return $this->view("fragment-views/cliente/ordenServicio/marca");
    }
    
    public function equipoServicio() // equipo
    {
        return $this->view("fragment-views/cliente/ordenServicio/equipo");
    }

    public function modeloServicio()  // modelo
    {
        return $this->view("fragment-views/cliente/ordenServicio/modelo");
    }
   
	public function numeroseries() // registro de series
    {
        return $this->view("fragment-views/cliente/numero-series");
    }

	public function garantia()    // registrar garantia
    {
        return $this->view("fragment-views/cliente/garantia");
    }

	public function tallerCotizaciones(){
        return $this->view("fragment-views/cliente/taller-cotizaciones");
    
    }
    // getion de activos 
    public function gestionActivos(){
        return $this->view("fragment-views/cliente/ordenServicio/gestion-activos");
    }
    public function garantiaAdd(){
        return $this->view("fragment-views/cliente/garantia-add");
    }
    public function garantiaManual(){
        return $this->view("fragment-views/cliente/garantia-manual");
    }
    public function maquinaActivos(){
        return $this->view("fragment-views/cliente/ordenServicio/maquina");
    }
    public function motivo(){
        return $this->view("fragment-views/cliente/ordenServicio/motivo");
    }

    public function fotosTaller() {
        return $this->view("fragment-views/cliente/agregar-fotos");
    }
    public function vistaCotiTaller() {
        return $this->view("fragment-views/cliente/cotizacion-vista-taller");
    }
    public function edtTaller() {
        return $this->view("fragment-views/cliente/editar-cotizacion-taller");
    }

    public function guiaRemisionDuplicada() {
        return $this->view("fragment-views/cliente/guia-remision-duplicada");
    }
    public function accesoDenegado() {
        return $this->view("fragment-views/cliente/acceso-denegado");
    }
    //categoria para repuestos 
    public function categoriaRepuesto() {
        return $this->view("fragment-views/cliente/categoria-repuesto");
    }
    
    public function unidadRepuesto() {
        return $this->view("fragment-views/cliente/unidad-repuesto");
    }
    
    public function certificadoGarantia() {
        return $this->view("fragment-views/cliente/editar-certificado");
    }
    // modulo documentos 
    public function documentos() {
        return $this->view("fragment-views/cliente/documentos/modulo-documentos");
    }
    public function documentosInformes() {
        return $this->view("fragment-views/cliente/documentos/informes");
    }
    public function documentosCartas() {
        return $this->view("fragment-views/cliente/documentos/cartas");
    }
    public function documentosConstancias() {
        return $this->view("fragment-views/cliente/documentos/constancias");
    }
    
    public function documentosArchivosInternos() {
        return $this->view("fragment-views/cliente/documentos/archivos-internos");
    }
    
    
    public function documentosOtros() {
        return $this->view("fragment-views/cliente/documentos/otros-archivos");
    }
    
}
