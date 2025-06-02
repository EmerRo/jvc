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

    // Getters y Setters para los nuevos campos
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
        " . ($this->id_venta ? "'$this->id_venta'" : "NULL") . ",
        " . ($this->id_cotizacion ? "'$this->id_cotizacion'" : "NULL") . ",
        " . ($this->destinatario_nombre ? "'$this->destinatario_nombre'" : "NULL") . ",
        " . ($this->destinatario_documento ? "'$this->destinatario_documento'" : "NULL") . ",
        '$this->fecha',
        '$this->dir_partida',
        '$this->motivo_traslado',
        '$this->dir_llegada',
        '$this->ubigeo',
        '$this->tipo_transporte',
        '$this->ruc_transporte',
        '$this->raz_transporte',
        '$this->vehiculo',
        '$this->chofer',
        '$this->chofer_datos',
        '$this->observaciones',
        '$this->doc_referencia',
        '0',
        '',
        '',
        '$this->serie',
        '$this->numero',
        '$this->peso',
        '$this->nro_bultos',
        '1',
        '$this->id_empresa',
        '{$_SESSION['sucursal']}'
    )";
    
    $result = $this->conectar->query($sql);
    if ($result) {
        $this->id_guia = $this->conectar->insert_id;
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
        COALESCE(v.serie, '') as serie_venta,
        e.ruc as ruc_empresa,
        COALESCE(v.numero, '') as numero_venta,
        CASE
            WHEN gr.id_venta IS NOT NULL THEN COALESCE(ds.abreviatura, 'MANUAL')
            WHEN gr.id_cotizacion IS NOT NULL THEN CONCAT('COTI-', LPAD(cot.numero, 3, '0'))
            ELSE 'MANUAL'
        END as doc_venta,
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