<?php

class GuiaRemision
{
    private $id_guia;
    private $id_venta;
    private $fecha;
    private $dir_partida;
    private $motivo_traslado;
    private $serie;
    private $numero;
    private $dir_llegada;
    private $ubigeo;
    private $tipo_transporte;
    private $ruc_transporte;
    private $raz_transporte;
    private $vehiculo;
    private $chofer;
    private $chofer_datos;
    private $observaciones;
    private $doc_referencia;
    private $ref_orden_compra; // ✅ NUEVO CAMPO
    private $enviado_sunat;
    private $hash;
    private $nombre_xml;
    private $peso;
    private $nro_bultos;
    private $estado;
    private $id_empresa;
    private $destinatario_nombre;
    private $destinatario_documento;
    private $id_cotizacion;
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    // ✅ NUEVOS GETTERS Y SETTERS para ref_orden_compra
    public function getRefOrdenCompra()
    {
        return $this->ref_orden_compra;
    }

    public function setRefOrdenCompra($ref_orden_compra)
    {
        $this->ref_orden_compra = $ref_orden_compra;
    }

    // Getters y Setters para los campos existentes
    public function getDirPartida()
    {
        return $this->dir_partida;
    }

    public function setDirPartida($dir_partida)
    {
        $this->dir_partida = $dir_partida;
    }

    public function getMotivoTraslado()
    {
        return $this->motivo_traslado;
    }

    public function setMotivoTraslado($motivo_traslado)
    {
        $this->motivo_traslado = $motivo_traslado;
    }

    public function getChoferDatos()
    {
        return $this->chofer_datos;
    }

    public function setChoferDatos($chofer_datos)
    {
        $this->chofer_datos = $chofer_datos;
    }

    public function getObservaciones()
    {
        return $this->observaciones;
    }

    public function setObservaciones($observaciones)
    {
        $this->observaciones = $observaciones;
    }

    public function getDocReferencia()
    {
        return $this->doc_referencia;
    }

    public function setDocReferencia($doc_referencia)
    {
        $this->doc_referencia = $doc_referencia;
    }

    // Getters y setters existentes
    public function getIdGuia()
    {
        return $this->id_guia;
    }

    public function setIdGuia($id_guia)
    {
        $this->id_guia = $id_guia;
    }

    public function getIdVenta()
    {
        return $this->id_venta;
    }

    public function setIdVenta($id_venta)
    {
        $this->id_venta = $id_venta;
    }

    public function getFecha()
    {
        return $this->fecha;
    }

    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    public function getSerie()
    {
        return $this->serie;
    }

    public function setSerie($serie)
    {
        $this->serie = $serie;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    public function getDirLlegada()
    {
        return $this->dir_llegada;
    }

    public function setDirLlegada($dir_llegada)
    {
        $this->dir_llegada = $dir_llegada;
    }

    public function getUbigeo()
    {
        return $this->ubigeo;
    }

    public function setUbigeo($ubigeo)
    {
        $this->ubigeo = $ubigeo;
    }

    public function getTipoTransporte()
    {
        return $this->tipo_transporte;
    }

    public function setTipoTransporte($tipo_transporte)
    {
        $this->tipo_transporte = $tipo_transporte;
    }

    public function getRucTransporte()
    {
        return $this->ruc_transporte;
    }

    public function setRucTransporte($ruc_transporte)
    {
        $this->ruc_transporte = $ruc_transporte;
    }

    public function getRazTransporte()
    {
        return $this->raz_transporte;
    }

    public function setRazTransporte($raz_transporte)
    {
        $this->raz_transporte = $raz_transporte;
    }

    public function getVehiculo()
    {
        return $this->vehiculo;
    }

    public function setVehiculo($vehiculo)
    {
        $this->vehiculo = $vehiculo;
    }

    public function getChofer()
    {
        return $this->chofer;
    }

    public function setChofer($chofer)
    {
        $this->chofer = $chofer;
    }
    public function getDestinatarioNombre()
    {
        return $this->destinatario_nombre;
    }

    public function setDestinatarioNombre($destinatario_nombre)
    {
        $this->destinatario_nombre = $destinatario_nombre;
    }

    public function getDestinatarioDocumento()
    {
        return $this->destinatario_documento;
    }

    public function setDestinatarioDocumento($destinatario_documento)
    {
        $this->destinatario_documento = $destinatario_documento;
    }

    public function getEnviadoSunat()
    {
        return $this->enviado_sunat;
    }

    public function setEnviadoSunat($enviado_sunat)
    {
        $this->enviado_sunat = $enviado_sunat;
    }

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    public function getNombreXml()
    {
        return $this->nombre_xml;
    }

    public function setNombreXml($nombre_xml)
    {
        $this->nombre_xml = $nombre_xml;
    }

    public function getPeso()
    {
        return $this->peso;
    }

    public function setPeso($peso)
    {
        $this->peso = $peso;
    }

    public function getNroBultos()
    {
        return $this->nro_bultos;
    }

    public function setNroBultos($nro_bultos)
    {
        $this->nro_bultos = $nro_bultos;
    }

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    public function getIdEmpresa()
    {
        return $this->id_empresa;
    }

    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }
    public function getIdCotizacion()
{
    return $this->id_cotizacion;
}

public function setIdCotizacion($id_cotizacion)
{
    $this->id_cotizacion = $id_cotizacion;
}

    public function obtenerId()
    {
        $sql = "select ifnull(max(id_guia_remision) + 1, 1) as codigo 
            from guia_remision";
        $this->id_guia = $this->conectar->get_valor_query($sql, 'codigo');
    }

    public function obtenerDatos()
    {
        $sql = "SELECT * 
        FROM guia_remision 
        WHERE id_guia_remision = '$this->id_guia'";
        $result = $this->conectar->query($sql);
        $fila = $result->fetch_assoc();
    
        if ($fila) {
            $this->fecha = $fila['fecha_emision'];
            $this->id_venta = $fila['id_venta'];
            $this->destinatario_nombre = $fila['destinatario_nombre'];
            $this->destinatario_documento = $fila['destinatario_documento'];
            $this->dir_partida = $fila['dir_partida'];
            $this->motivo_traslado = $fila['motivo_traslado'];
            $this->dir_llegada = $fila['dir_llegada'];
            $this->ubigeo = $fila['ubigeo'];
            $this->tipo_transporte = $fila['tipo_transporte'];
            $this->ruc_transporte = $fila['ruc_transporte'];
            $this->raz_transporte = $fila['razon_transporte']; // Asegúrate que este es el nombre correcto en la BD
            $this->vehiculo = $fila['vehiculo'];
            $this->chofer = $fila['chofer_brevete'];
            $this->chofer_datos = $fila['chofer_datos'];
            $this->observaciones = $fila['observaciones'];
            $this->doc_referencia = $fila['doc_referencia'];
            $this->ref_orden_compra = $fila['ref_orden_compra']; // ✅ NUEVO CAMPO
            $this->enviado_sunat = $fila['enviado_sunat'];
            $this->hash = $fila['hash'];
            $this->nombre_xml = $fila['nombre_xml'];
            $this->serie = $fila['serie'];
            $this->numero = $fila['numero'];
            $this->peso = $fila['peso'];
            $this->nro_bultos = $fila['nro_bultos'];
            $this->estado = $fila['estado'];
            $this->id_empresa = $fila['id_empresa'];
            return true; // Retorna true si encontró la guía
        }
        return false; // Retorna false si no encontró la guía
    }
    
    public function exeSQL($sql)
    {
        return $this->conectar->query($sql);
    }

 public function insertar()
{
    // Escapar todas las propiedades de cadena para prevenir SQL injection y errores de sintaxis
    $id_venta_escaped = $this->id_venta ? "'" . $this->conectar->real_escape_string($this->id_venta) . "'" : "NULL";
    $id_cotizacion_escaped = $this->id_cotizacion ? "'" . $this->conectar->real_escape_string($this->id_cotizacion) . "'" : "NULL";
    $destinatario_nombre_escaped = $this->destinatario_nombre ? "'" . $this->conectar->real_escape_string($this->destinatario_nombre) . "'" : "NULL";
    $destinatario_documento_escaped = $this->destinatario_documento ? "'" . $this->conectar->real_escape_string($this->destinatario_documento) . "'" : "NULL";
    $fecha_escaped = $this->conectar->real_escape_string($this->fecha);
    $dir_partida_escaped = $this->conectar->real_escape_string($this->dir_partida);
    $motivo_traslado_escaped = $this->conectar->real_escape_string($this->motivo_traslado);
    $dir_llegada_escaped = $this->conectar->real_escape_string($this->dir_llegada);
    $ubigeo_escaped = $this->conectar->real_escape_string($this->ubigeo);
    $tipo_transporte_escaped = $this->conectar->real_escape_string($this->tipo_transporte);
    $ruc_transporte_escaped = $this->conectar->real_escape_string($this->ruc_transporte);
    $raz_transporte_escaped = $this->conectar->real_escape_string($this->raz_transporte);
    $vehiculo_escaped = $this->conectar->real_escape_string($this->vehiculo);
    $chofer_escaped = $this->conectar->real_escape_string($this->chofer);
    $chofer_datos_escaped = $this->conectar->real_escape_string($this->chofer_datos);
    $observaciones_escaped = $this->conectar->real_escape_string($this->observaciones);
    $doc_referencia_escaped = $this->conectar->real_escape_string($this->doc_referencia);
    $ref_orden_compra_escaped = $this->conectar->real_escape_string($this->ref_orden_compra); // ✅ NUEVO CAMPO
    $serie_escaped = $this->conectar->real_escape_string($this->serie);
    $numero_escaped = $this->conectar->real_escape_string($this->numero);
    $peso_escaped = $this->conectar->real_escape_string($this->peso);
    $nro_bultos_escaped = $this->conectar->real_escape_string($this->nro_bultos);
    $id_empresa_escaped = $this->conectar->real_escape_string($this->id_empresa);
    $sucursal_escaped = $this->conectar->real_escape_string($_SESSION['sucursal']);

    $sql = "INSERT INTO guia_remision (
        id_venta,
        id_cotizacion,
        destinatario_nombre,
        destinatario_documento,
        fecha_emision,
        dir_partida,
        motivo_traslado,
        dir_llegada,
        ubigeo,
        tipo_transporte,
        ruc_transporte,
        razon_transporte,
        vehiculo,
        chofer_brevete,
        chofer_datos,
        observaciones,
        doc_referencia,
        ref_orden_compra,
        enviado_sunat,
        hash,
        nombre_xml,
        serie,
        numero,
        peso,
        nro_bultos,
        estado,
        id_empresa,
        sucursal
    ) VALUES (
        $id_venta_escaped,
        $id_cotizacion_escaped,
        $destinatario_nombre_escaped,
        $destinatario_documento_escaped,
        '$fecha_escaped',
        '$dir_partida_escaped',
        '$motivo_traslado_escaped',
        '$dir_llegada_escaped',
        '$ubigeo_escaped',
        '$tipo_transporte_escaped',
        '$ruc_transporte_escaped',
        '$raz_transporte_escaped',
        '$vehiculo_escaped',
        '$chofer_escaped',
        '$chofer_datos_escaped',
        '$observaciones_escaped',
        '$doc_referencia_escaped',
        '$ref_orden_compra_escaped',
        '0',
        '',
        '',
        '$serie_escaped',
        '$numero_escaped',
        '$peso_escaped',
        '$nro_bultos_escaped',
        '1',
        '$id_empresa_escaped',
        '$sucursal_escaped'
    )";

    $result = $this->conectar->query($sql);
    if ($result) {
        $this->id_guia = $this->conectar->insert_id;
    } else {
    }
    return $result;
}

    public function actualizarHash()
    {
        $sql = "update guia_remision 
        set hash = '$this->hash', 
            nombre_xml = '$this->nombre_xml', 
            enviado_sunat = 1 
        where id_guia_remision = '$this->id_guia'";
        return $this->conectar->query($sql);
    }

    public function anular()
    {
        $sql = "update guia_remision 
        set estado = '2'   
        where id_guia_remision = '$this->id_guia'";
        return $this->conectar->query($sql);
    }

public function verFilas()
{
    $sql = "SELECT 
        gr.fecha_emision, 
        gr.id_guia_remision,
        gr.dir_partida,
        gr.motivo_traslado, 
        gr.dir_llegada, 
        gr.enviado_sunat, 
        gr.serie, 
        gr.numero,
        gr.estado,
        CASE 
            WHEN gr.id_venta IS NOT NULL THEN c_venta.datos
            WHEN gr.id_cotizacion IS NOT NULL THEN c_coti.datos
            ELSE gr.destinatario_nombre
        END as datos,
        -- Obtener el documento del cliente para determinar el tipo
        CASE 
            WHEN gr.id_venta IS NOT NULL THEN c_venta.documento
            WHEN gr.id_cotizacion IS NOT NULL THEN c_coti.documento
            ELSE gr.destinatario_documento
        END as documento_cliente,
        -- ✅ MEJORADO: Solo mostrar datos de factura cuando realmente hay una venta
        CASE 
            WHEN gr.id_venta IS NOT NULL THEN v.serie
            ELSE ''
        END as serie_venta,
        e.ruc as ruc_empresa,
        CASE 
            WHEN gr.id_venta IS NOT NULL THEN v.numero
            ELSE ''
        END as numero_venta,
        -- ✅ MEJORADO: Mostrar solo facturas reales, N/A para el resto
        CASE
            WHEN gr.id_venta IS NOT NULL THEN COALESCE(ds.abreviatura, 'DOC')
            ELSE 'N/A'
        END as doc_venta,
        -- ✅ NUEVO: Campo para identificar el tipo de guía
        CASE
            WHEN gr.id_venta IS NOT NULL THEN 'facturas'
            WHEN gr.id_cotizacion IS NOT NULL THEN 'cotizaciones'
            ELSE 'manuales'
        END as tipo_guia,
        COALESCE(gs.nombre_xml, '') as nom_guia_xml
    FROM guia_remision gr
    LEFT JOIN ventas v ON gr.id_venta = v.id_venta 
    LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido            
    LEFT JOIN clientes c_venta ON v.id_cliente = c_venta.id_cliente 
    LEFT JOIN cotizaciones cot ON gr.id_cotizacion = cot.cotizacion_id
    LEFT JOIN clientes c_coti ON cot.id_cliente = c_coti.id_cliente
    JOIN empresas e ON e.id_empresa = gr.id_empresa
    LEFT JOIN guia_sunat gs ON gr.id_guia_remision = gs.id_guia
    WHERE gr.id_empresa = '$this->id_empresa' 
    AND gr.sucursal = '{$_SESSION['sucursal']}'
    ORDER BY gr.id_guia_remision DESC";

    return $this->conectar->query($sql);
}

}