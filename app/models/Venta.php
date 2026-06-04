<?php

class Venta
{
    private $id_venta;
    private $fecha;
    private $fechaVenc;
    private $id_tipo_pago;
    private $dias_pagos;
    private $id_tido;
    private $serie;
    private $numero;
    private $id_cliente;
    private $total;
    private $estado;
    private $enviado_sunat;
    private $id_empresa;
    private $direccion;
    private $sucursal;
    private $apli_igv;
    private $observa;
    private $igv;
    private $metodo;
private $idCoti;
    private $conectar;
    private $sql;
private $sql_error;

    /**
     * Venta constructor.
     */
    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
        if (!class_exists('DB')) {
            require_once 'app/clases/DB.php';
        }
    }

    /**
     * @return mixed
     */
    public function getIgv()
    {
        return $this->igv;
    }

    /**
     * @param mixed $igv
     */
    public function setIgv($igv): void
    {
        $this->igv = $igv;
    }

    /**
     * @return mixed
     */
    public function getObserva()
    {
        return $this->observa;
    }

    /**
     * @param mixed $observa
     */
    public function setObserva($observa): void
    {
        $this->observa = $observa;
    }
    public function getMetodo()
    {
        return $this->metodo;
    }

    /**
     * @param mixed $metodo
     */
    public function setMetodo($metodo): void
    {
        $this->metodo = $metodo;
    }

    /**
     * @return mixed
     */
    public function getApliIgv()
    {
        return $this->apli_igv;
    }

    /**
     * @param mixed $apli_igv
     */
    public function setApliIgv($apli_igv): void
    {
        $this->apli_igv = $apli_igv;
    }

    /**
     * @return mixed
     */
    public function getSucursal()
    {
        return $this->sucursal;
    }

    /**
     * @param mixed $sucursal
     */
    public function setSucursal($sucursal): void
    {
        $this->sucursal = $sucursal;
    }

    /**
     * @return mixed
     */
    public function getFechaVenc()
    {
        return $this->fechaVenc;
    }

    /**
     * @param mixed $fechaVenc
     */
    public function setFechaVenc($fechaVenc): void
    {
        $this->fechaVenc = $fechaVenc;
    }

    /**
     * @return mixed
     */
    public function getIdTipoPago()
    {
        return $this->id_tipo_pago;
    }

    /**
     * @param mixed $id_tipo_pago
     */
    public function setIdTipoPago($id_tipo_pago): void
    {
        $this->id_tipo_pago = $id_tipo_pago;
    }

    /**
     * @return mixed
     */
    public function getDiasPagos()
    {
        return $this->dias_pagos;
    }

    /**
     * @param mixed $dias_pagos
     */
    public function setDiasPagos($dias_pagos): void
    {
        $this->dias_pagos = $dias_pagos;
    }

    /**
     * @return mixed
     */
    public function getDireccion()
    {
        return $this->direccion;
    }

    /**
     * @param mixed $direccion
     */
    public function setDireccion($direccion): void
    {
        $this->direccion = $direccion;
    }

    /**
     * @return mixed
     */
    public function getIdVenta()
    {
        return $this->id_venta;
    }

    /**
     * @param mixed $id_venta
     */
    public function setIdVenta($id_venta)
    {
        $this->id_venta = $id_venta;
    }

    /**
     * @return mixed
     */
    public function getFecha()
    {
        return $this->fecha;
    }

    /**
     * @param mixed $fecha
     */
    public function setFecha($fecha)
    {
        $this->fecha = $fecha;
    }

    /**
     * @return mixed
     */
    public function getIdTido()
    {
        return $this->id_tido;
    }

    /**
     * @param mixed $id_tido
     */
    public function setIdTido($id_tido)
    {
        $this->id_tido = $id_tido;
    }

    /**
     * @return mixed
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * @param mixed $serie
     */
    public function setSerie($serie)
    {
        $this->serie = $serie;
    }

    /**
     * @return mixed
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * @param mixed $numero
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;
    }

    /**
     * @return mixed
     */
    public function getIdCliente()
    {
        return $this->id_cliente;
    }

    /**
     * @param mixed $id_cliente
     */
    public function setIdCliente($id_cliente)
    {
        $this->id_cliente = $id_cliente;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     */
    public function setTotal($total)
    {
        $this->total = $total;
    }

    /**
     * @return mixed
     */
    public function getEstado()
    {
        return $this->estado;
    }

    /**
     * @param mixed $estado
     */
    public function setEstado($estado)
    {
        $this->estado = $estado;
    }

    /**
     * @return mixed
     */
    public function getEnviadoSunat()
    {
        return $this->enviado_sunat;
    }

    /**
     * @param mixed $enviado_sunat
     */
    public function setEnviadoSunat($enviado_sunat)
    {
        $this->enviado_sunat = $enviado_sunat;
    }

    /**
     * @return mixed
     */
    public function getIdEmpresa()
    {
        return $this->id_empresa;
    }

    /**
     * @param mixed $id_empresa
     */
    public function setIdEmpresa($id_empresa)
    {
        $this->id_empresa = $id_empresa;
    }
    
    /**
     * @return mixed
     */
    public function getIdCoti()
    {
        return $this->idCoti;
    }

    /**
     * @param mixed $id_empresa
     */
    public function setIdCoti($idCoti)
    {
        $this->idCoti = $idCoti;
    }
    /**
 * @return mixed
 */
public function getSql()
{
    return $this->sql;
}

/**
 * @param mixed $sql
 */
public function setSql($sql): void
{
    $this->sql = $sql;
}

/**
 * @return mixed
 */
public function getSqlError()
{
    return $this->sql_error;
}

/**
 * @param mixed $sql_error
 */
public function setSqlError($sql_error): void
{
    $this->sql_error = $sql_error;
}
public function getDocReferencia()
{
    return $this->doc_referencia;
}

// Setter
public function setDocReferencia($doc_referencia): void
{
    $this->doc_referencia = $doc_referencia;
}
    
    public function exeSQL($sql)
    {
        return $this->conectar->query($sql);
    }
    public function insertar()
    {
        // Asegurar que tc tenga un valor válido cuando moneda es 1 (Soles)
        $tc = isset($_POST['tc']) && !empty($_POST['tc']) ? $_POST['tc'] : '0';
        if (isset($_POST['moneda']) && $_POST['moneda'] == '1') {
            $tc = '1';
        }

        $sucursal      = DB::int($_SESSION['sucursal'] ?? 0);
        $usuarioFac    = DB::int($_SESSION['usuario_fac'] ?? 0);
        $moneda        = DB::int($_POST['moneda'] ?? 1);
        $tcF           = DB::float($tc, 1.0);
        $pagado        = DB::float($_POST['pagacon'] ?? 0);
        $docReferencia = DB::str($_POST['doc_referencia'] ?? '');
        $apliIgv       = DB::int($this->apli_igv);
        $idTido        = DB::int($this->id_tido);
        $idTipoPago    = DB::int($this->id_tipo_pago);
        $fechaEm       = DB::str($this->fecha);
        $fechaVe       = DB::str($this->fechaVenc);
        $diasPagos     = DB::str($this->dias_pagos);
        $direccion     = DB::str($this->direccion);
        $serie         = DB::str($this->serie);
        $numero        = DB::str($this->numero);
        $idCliente     = DB::int($this->id_cliente);
        $totalV        = DB::float($this->total);
        $igvV          = DB::float($this->igv);
        $idEmpresa     = DB::int($this->id_empresa);
        $observa       = DB::str($this->observa);
        $metodo        = DB::int($this->metodo);

        // id_coti puede ser NULL ⇒ si no se setea, lo dejamos como 0 (placeholder)
        // pero el campo `id_coti` en la tabla puede tolerar 0. Si necesitás NULL real,
        // ajustá la columna a permitir NULL y cambiá el bind a 's' con 'NULL'.
        $idCoti = (isset($this->idCoti) && $this->idCoti !== '' && $this->idCoti !== null)
            ? DB::int($this->idCoti)
            : 0;

        // Bloque opcional de segundo pago (no rompe la firma original)
        $isSegundo = isset($_POST['segundoPago']) && $_POST['segundoPago'];
        $metodo2   = $isSegundo ? DB::int($_POST['metodo2'] ?? 0)   : 0;
        $pagado2   = $isSegundo ? DB::float($_POST['pagacon2'] ?? 0) : 0.0;
        $isSegFlag = $isSegundo ? 1 : 0;

        $sql = "INSERT INTO ventas
                SET doc_referencia    = ?,
                    moneda            = ?,
                    cm_tc             = ?,
                    pagado            = ?,
                    apli_igv          = ?,
                    id_tido           = ?,
                    id_tipo_pago      = ?,
                    fecha_emision     = ?,
                    fecha_vencimiento = ?,
                    dias_pagos        = ?,
                    direccion         = ?,
                    serie             = ?,
                    numero            = ?,
                    id_cliente        = ?,
                    total             = ?,
                    estado            = '1',
                    enviado_sunat     = '0',
                    igv               = ?,
                    id_empresa        = ?,
                    sucursal          = ?,
                    observacion       = ?,
                    medoto_pago_id    = ?,
                    id_coti           = ?,
                    id_vendedor       = ?,
                    is_segun_pago     = ?,
                    medoto_pago2_id   = ?,
                    pagado2           = ?";

        // Tipos: s i d d i i i s s s s s s i d d i i s i i i i i d (25 placeholders = 25 chars)
        $types  = 'siddiiissssssidd' . 'iisiiiiid';
        $params = [
            $docReferencia, // s
            $moneda,        // i
            $tcF,           // d
            $pagado,        // d
            $apliIgv,       // i
            $idTido,        // i
            $idTipoPago,    // i
            $fechaEm,       // s
            $fechaVe,       // s
            $diasPagos,     // s
            $direccion,     // s
            $serie,         // s
            $numero,        // s
            $idCliente,     // i
            $totalV,        // d
            $igvV,          // d
            $idEmpresa,     // i
            $sucursal,      // i
            $observa,       // s
            $metodo,        // i
            $idCoti,        // i
            $usuarioFac,    // i
            $isSegFlag,     // i
            $metodo2,       // i
            $pagado2,       // d
        ];

        $insertId = DB::insert($this->conectar, $sql, $types, $params);
        if ($insertId > 0) {
            $this->id_venta = $insertId;
            return true;
        }
        $this->sql_error = $this->conectar->error;
        $this->sql       = $sql;
        return false;
    }
    public function editar($id_venta)
    {
        $idVentaInt = DB::int($id_venta);
        if ($idVentaInt <= 0) {
            return false;
        }

        $metodo     = DB::int($_POST['metodo'] ?? 0);
        $moneda     = DB::int($_POST['moneda'] ?? 1);
        $tc         = DB::float($_POST['tc'] ?? 1, 1.0);
        $apliIgv    = DB::int($this->apli_igv);
        $idTido     = DB::int($this->id_tido);
        $idTipoPago = DB::int($this->id_tipo_pago);
        $fechaEm    = DB::str($this->fecha);
        $fechaVe    = DB::str($this->fechaVenc);
        $diasPagos  = DB::str($this->dias_pagos);
        $direccion  = DB::str($this->direccion);
        $idCliente  = DB::int($this->id_cliente);
        $totalV     = DB::float($this->total);
        $igvV       = DB::float($this->igv);
        $idEmpresa  = DB::int($this->id_empresa);
        $observa    = DB::str($this->observa);

        $sql = "UPDATE ventas
                SET medoto_pago_id    = ?,
                    moneda            = ?,
                    cm_tc             = ?,
                    apli_igv          = ?,
                    id_tido           = ?,
                    id_tipo_pago      = ?,
                    fecha_emision     = ?,
                    fecha_vencimiento = ?,
                    dias_pagos        = ?,
                    direccion         = ?,
                    id_cliente        = ?,
                    total             = ?,
                    igv               = ?,
                    id_empresa        = ?,
                    observacion       = ?
                WHERE id_venta = ?";

        $types  = 'iidiiissssidd' . 'isi';
        $params = [
            $metodo, $moneda, $tc, $apliIgv, $idTido, $idTipoPago,
            $fechaEm, $fechaVe, $diasPagos, $direccion,
            $idCliente, $totalV, $igvV,
            $idEmpresa, $observa, $idVentaInt,
        ];

        return DB::execute($this->conectar, $sql, $types, $params);
    }

    public function anular()
    {
        $id = DB::int($this->id_venta);
        if ($id <= 0) return false;
        return DB::execute(
            $this->conectar,
            "UPDATE ventas SET estado = '2' WHERE id_venta = ?",
            'i',
            [$id]
        );
    }

    public function obtenerId()
    {
        $sql = "select ifnull(max(id_venta) + 1, 1) as codigo 
            from ventas";
        $this->id_venta = $this->conectar->get_valor_query($sql, 'codigo');
    }

    public function verDetalle()
    {
        $idVenta = DB::int($this->id_venta);
        $respuesta = ['res' => false];
        if ($idVenta <= 0) {
            return json_encode($respuesta);
        }

        $row = DB::selectOne(
            $this->conectar,
            "SELECT ventas.*, c.documento, c.datos
             FROM ventas
             JOIN clientes c ON c.id_cliente = ventas.id_cliente
             WHERE id_venta = ?",
            'i',
            [$idVenta]
        );

        if ($row) {
            $totalVenta = 0;
            $row['detalles'] = [];

            $detallesProd = DB::select(
                $this->conectar,
                "SELECT productos_ventas.*, p.nombre, p.codigo
                 FROM productos_ventas
                 JOIN productos p ON p.id_producto = productos_ventas.id_producto
                 WHERE id_venta = ?",
                'i',
                [$idVenta]
            );
            foreach ($detallesProd as $depro) {
                $totalVenta += $depro['cantidad'] * $depro['precio'];
                $row['detalles'][] = $depro;
            }

            $detallesServ = DB::select(
                $this->conectar,
                "SELECT *, '' as codigo FROM ventas_servicios WHERE id_venta = ?",
                'i',
                [$idVenta]
            );
            foreach ($detallesServ as $depro) {
                $depro['precio'] = $depro['monto'];
                $totalVenta += $depro['cantidad'] * $depro['monto'];
                $row['detalles'][] = $depro;
            }

            // Si es una venta proveniente de cotización de taller, armar estructura por equipos
            $equiposVenta = [];
            $equipos = DB::select(
                $this->conectar,
                "SELECT * FROM ventas_equipos WHERE id_venta = ? ORDER BY id_venta_equipo",
                'i',
                [$idVenta]
            );
            if (!empty($equipos)) {
                foreach ($equipos as $eq) {
                    $idVe = DB::int($eq['id_venta_equipo']);
                    $items = DB::select(
                        $this->conectar,
                        "SELECT pv.*, p.nombre, p.codigo
                         FROM productos_ventas pv
                         JOIN productos p ON p.id_producto = pv.id_producto
                         WHERE pv.id_venta = ? AND pv.id_venta_equipo = ?",
                        'ii',
                        [$idVenta, $idVe]
                    );
                    $subtotalEquipo = 0;
                    foreach ($items as $it) {
                        $subtotalEquipo += $it['cantidad'] * $it['precio'];
                    }
                    // Fallback: si no hay items vinculados por id_venta_equipo (ventas anteriores),
                    // intentar recuperar por id_cotizacion_equipo
                    if (empty($items) && !empty($eq['id_cotizacion_equipo'])) {
                        $idCotiEq = DB::int($eq['id_cotizacion_equipo']);
                        $items = DB::select(
                            $this->conectar,
                            "SELECT pv.*, p.nombre, p.codigo
                             FROM productos_ventas pv
                             JOIN productos p ON p.id_producto = pv.id_producto
                             WHERE pv.id_venta = ? AND pv.id_cotizacion_equipo = ?",
                            'ii',
                            [$idVenta, $idCotiEq]
                        );
                        foreach ($items as $it2) {
                            $subtotalEquipo += $it2['cantidad'] * $it2['precio'];
                        }
                    }
                    $eq['items'] = $items;
                    $eq['subtotal'] = number_format($subtotalEquipo, 2, '.', '');
                    $equiposVenta[] = $eq;
                }
                $row['equipos'] = $equiposVenta;
            } else {
                $row['equipos'] = [];
            }
            $row['montoTotal'] = number_format($totalVenta, 2, '.', '');
            $respuesta['res']  = true;
            $respuesta['data'] = $row;
        }
        return json_encode($respuesta);
    }
    public function verDetalle2()
    {
        $idVenta = DB::int($this->id_venta);
        $respuesta = ['res' => false];
        if ($idVenta <= 0) {
            return $respuesta;
        }

        $row = DB::selectOne(
            $this->conectar,
            "SELECT ventas.*, c.documento, c.datos
             FROM ventas
             JOIN clientes c ON c.id_cliente = ventas.id_cliente
             WHERE id_venta = ?",
            'i',
            [$idVenta]
        );

        if ($row) {
            $totalVenta = 0;
            $row['detalles'] = [];

            $detallesProd = DB::select(
                $this->conectar,
                "SELECT productos_ventas.*, p.descripcion
                 FROM productos_ventas
                 JOIN productos p ON p.id_producto = productos_ventas.id_producto
                 WHERE id_venta = ?",
                'i',
                [$idVenta]
            );
            foreach ($detallesProd as $depro) {
                $totalVenta += $depro['cantidad'] * $depro['precio'];
                $row['detalles'][] = $depro;
            }

            $detallesServ = DB::select(
                $this->conectar,
                "SELECT * FROM ventas_servicios WHERE id_venta = ?",
                'i',
                [$idVenta]
            );
            foreach ($detallesServ as $depro) {
                $depro['precio'] = $depro['monto'];
                $totalVenta += $depro['cantidad'] * $depro['monto'];
                $row['detalles'][] = $depro;
            }
            $row['montoTotal'] = number_format($totalVenta, 2, '.', '');
            $respuesta['res']  = true;
            $respuesta['data'] = $row;
        }
        return $respuesta;
    }

    public function obtenerDatos()
    {
        $idVenta = DB::int($this->id_venta);
        if ($idVenta <= 0) return;

        $fila = DB::selectOne(
            $this->conectar,
            "SELECT * FROM ventas WHERE id_venta = ?",
            'i',
            [$idVenta]
        );
        if (!$fila) return;

        $this->fecha         = $fila['fecha_emision'];
        $this->id_tido       = $fila['id_tido'];
        $this->serie         = $fila['serie'];
        $this->numero        = $fila['numero'];
        $this->id_cliente    = $fila['id_cliente'];
        $this->total         = $fila['total'];
        $this->estado        = $fila['estado'];
        $this->enviado_sunat = $fila['enviado_sunat'];
        $this->id_empresa    = $fila['id_empresa'];
        $this->sucursal      = $fila['sucursal'];
    }

    public function actualizar_envio()
    {
        $id = DB::int($this->id_venta);
        if ($id <= 0) return false;
        return DB::execute(
            $this->conectar,
            "UPDATE ventas SET enviado_sunat = 1 WHERE id_venta = ?",
            'i',
            [$id]
        );
    }

    public function validarVenta()
    {
        $idTido    = DB::int($this->id_tido);
        $serie     = DB::str($this->serie);
        $numero    = DB::str($this->numero);
        $idEmpresa = DB::int($_SESSION['id_empresa'] ?? 0);

        $row = DB::selectOne(
            $this->conectar,
            "SELECT id_venta AS codigo
             FROM ventas
             WHERE id_tido = ? AND serie = ? AND numero = ? AND id_empresa = ?",
            'issi',
            [$idTido, $serie, $numero, $idEmpresa]
        );
        $this->id_venta = $row ? $row['codigo'] : null;
    }

    public function verFilasPeriodoGanancia($periodo)
    {
        $temoAr     = explode('-', $periodo);
        $idEmpresa  = DB::int($this->id_empresa);
        $sucursal   = DB::int($_SESSION['sucursal'] ?? 0);
        $anio       = DB::int($temoAr[0] ?? 0);
        $mes        = DB::int($temoAr[1] ?? 0);
        $diaTok     = $temoAr[2] ?? '0';

        $base = "SELECT v.id_venta, v.fecha_emision, ds.abreviatura,
                v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado,
                v.enviado_sunat, vs.nombre_xml, metodo_pago.nombre AS metodo
             FROM ventas AS v
                 LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                 LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                 LEFT JOIN ventas_sunat vs ON v.id_venta = vs.id_venta
                 LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago = v.medoto_pago_id";

        if ((int)$diaTok > 0) {
            // Día específico (filtra también por sucursal)
            $dia  = DB::int($diaTok);
            $rest = DB::select(
                $this->conectar,
                "$base
                 WHERE v.id_empresa = ? AND v.sucursal = ?
                   AND YEAR(v.fecha_emision) = ? AND MONTH(v.fecha_emision) = ? AND DAY(v.fecha_emision) = ?
                 ORDER BY v.fecha_emision ASC, v.numero ASC",
                'iiiii',
                [$idEmpresa, $sucursal, $anio, $mes, $dia]
            );
        } elseif ($diaTok === 'nn') {
            // Mes completo, sin filtro de sucursal
            $rest = DB::select(
                $this->conectar,
                "$base
                 WHERE v.id_empresa = ?
                   AND YEAR(v.fecha_emision) = ? AND MONTH(v.fecha_emision) = ?
                 ORDER BY v.fecha_emision ASC, v.numero ASC",
                'iii',
                [$idEmpresa, $anio, $mes]
            );
        } else {
            // Período YYYYMM
            $periodoF = sprintf('%04d%02d', $anio, $mes);
            $rest = DB::select(
                $this->conectar,
                "$base
                 WHERE v.id_empresa = ?
                   AND CONCAT(YEAR(v.fecha_emision), LPAD(MONTH(v.fecha_emision), 2, 0)) = ?
                 ORDER BY v.fecha_emision ASC, v.numero ASC",
                'is',
                [$idEmpresa, $periodoF]
            );
        }

        $lista = [];
        foreach ($rest as $row) {
            $costo = DB::selectValue(
                $this->conectar,
                "SELECT SUM(pv.cantidad * pv.costo) AS costo FROM productos_ventas pv WHERE pv.id_venta = ?",
                'i',
                [DB::int($row['id_venta'])]
            );
            $row['costo']    = $costo !== null ? $costo : 0;
            $row['cod_v']    = $row['id_venta'];
            $row['id_venta'] = $row['id_venta'] . '--' . $row['nombre_xml'];
            $lista[]         = $row;
        }
        return $lista;
    }
    public function verFilasPeriodo($periodo)
    {
        $temoAr    = explode('-', $periodo);
        $idEmpresa = DB::int($this->id_empresa);
        $sucursal  = DB::int($_SESSION['sucursal'] ?? 0);
        $anio      = DB::int($temoAr[0] ?? 0);
        $mes       = DB::int($temoAr[1] ?? 0);
        $diaTok    = $temoAr[2] ?? '0';
        $metodo    = DB::int($temoAr[3] ?? 0);

        $base = "SELECT v.id_venta, v.fecha_emision, ds.abreviatura, mdp2.nombre AS metodo2,
                v.pagado, v.pagado2,
                v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado,
                v.enviado_sunat, vs.nombre_xml, metodo_pago.nombre AS metodo
             FROM ventas AS v
                 LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                 LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                 LEFT JOIN ventas_sunat vs ON v.id_venta = vs.id_venta
                 LEFT JOIN metodo_pago ON metodo_pago.id_metodo_pago = v.medoto_pago_id
                 LEFT JOIN metodo_pago mdp2 ON mdp2.id_metodo_pago = v.medoto_pago2_id";

        if ((int)$diaTok > 0 && $metodo != 0) {
            $dia = DB::int($diaTok);
            $rest = DB::select(
                $this->conectar,
                "$base
                 WHERE v.estado <> 2 AND v.id_empresa = ? AND v.sucursal = ?
                   AND YEAR(v.fecha_emision) = ? AND MONTH(v.fecha_emision) = ? AND DAY(v.fecha_emision) = ?
                   AND metodo_pago.id_metodo_pago = ?
                 ORDER BY v.fecha_emision ASC, v.numero ASC",
                'iiiiii',
                [$idEmpresa, $sucursal, $anio, $mes, $dia, $metodo]
            );
        } elseif ($diaTok === 'nn' && $metodo != 0) {
            $rest = DB::select(
                $this->conectar,
                "$base
                 WHERE v.estado <> 2 AND v.id_empresa = ?
                   AND YEAR(v.fecha_emision) = ? AND MONTH(v.fecha_emision) = ?
                   AND metodo_pago.id_metodo_pago = ?
                 ORDER BY v.fecha_emision ASC, v.numero ASC",
                'iiii',
                [$idEmpresa, $anio, $mes, $metodo]
            );
        } elseif ($diaTok === 'nn' && $metodo == 0) {
            $rest = DB::select(
                $this->conectar,
                "$base
                 WHERE v.estado <> 2 AND v.id_empresa = ? AND v.sucursal = ?
                   AND YEAR(v.fecha_emision) = ? AND MONTH(v.fecha_emision) = ?
                 ORDER BY v.fecha_emision ASC, v.numero ASC",
                'iiii',
                [$idEmpresa, $sucursal, $anio, $mes]
            );
        } elseif ((int)$diaTok > 0 && $metodo == 0) {
            $dia = DB::int($diaTok);
            $rest = DB::select(
                $this->conectar,
                "$base
                 WHERE v.estado <> 2 AND v.id_empresa = ? AND v.sucursal = ?
                   AND YEAR(v.fecha_emision) = ? AND MONTH(v.fecha_emision) = ? AND DAY(v.fecha_emision) = ?
                 ORDER BY v.fecha_emision ASC, v.numero ASC",
                'iiiii',
                [$idEmpresa, $sucursal, $anio, $mes, $dia]
            );
        } else {
            $periodoF = sprintf('%04d%02d', $anio, $mes);
            $rest = DB::select(
                $this->conectar,
                "$base
                 WHERE v.estado <> 2 AND v.id_empresa = ?
                   AND CONCAT(YEAR(v.fecha_emision), LPAD(MONTH(v.fecha_emision), 2, 0)) = ?
                 ORDER BY v.fecha_emision ASC, v.numero ASC",
                'is',
                [$idEmpresa, $periodoF]
            );
        }

        $lista = [];
        foreach ($rest as $row) {
            $row['cod_v']    = $row['id_venta'];
            $row['id_venta'] = $row['id_venta'] . '--' . $row['nombre_xml'];
            $lista[]         = $row;
        }
        return $lista;
    }
    public function verFilasPorEmpresas($empresa, $sucuarsal)
    {
        $idEmpresa = DB::int($empresa);
        $sucursal  = DB::int($sucuarsal);

        $rest = DB::select(
            $this->conectar,
            "SELECT v.igv, v.id_venta, v.fecha_emision, ds.abreviatura,
                    v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total,
                    v.estado, v.enviado_sunat, vs.nombre_xml
             FROM ventas AS v
                LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
                LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
                LEFT JOIN ventas_sunat vs ON v.id_venta = vs.id_venta
             WHERE v.id_empresa = ? AND v.sucursal = ?
             ORDER BY v.fecha_emision ASC, v.numero ASC",
            'ii',
            [$idEmpresa, $sucursal]
        );

        $lista = [];
        foreach ($rest as $row) {
            $row['cod_v']    = $row['id_venta'];
            $row['id_venta'] = $row['id_venta'] . '--' . $row['nombre_xml'];
            $lista[]         = $row;
        }
        return $lista;
    }
    public function verFilas($periodo)
    {
        $sql = "select v.id_venta, v.fecha_emision, ds.abreviatura,v.apli_igv,v.igv,
       v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml
        from ventas as v
            LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
            LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
        where v.id_empresa = '12' and v.sucursal='1' and year(v.fecha_emision)='2023'
        order by v.fecha_emision asc, v.numero asc";

        /*$sql = "select v.id_venta, v.fecha, ds.abreviatura,
       v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml
        from ventas as v
            LEFT JOIN documentos_sunat ds on v.id_tido = ds.id_tido
            LEFT JOIN clientes c on v.id_cliente = c.id_cliente
            LEFT JOIN ventas_sunat vs on v.id_venta = vs.id_venta
        where v.id_empresa = '$this->id_empresa' and concat(year(fecha), LPAD(month(fecha), 2, 0)) = '$periodo'
        order by v.fecha asc, v.numero asc";*/

        /*$sql = "select v.id_venta, v.fecha, ds.abreviatura, v.id_tido, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.enviado_sunat, vs.nombre_xml
        from ventas as v
            inner join documentos_sunat ds on v.id_tido = ds.id_tido
            inner join clientes c on v.id_cliente = c.id_cliente
            inner join ventas_sunat vs on v.id_venta = vs.id_venta
        where v.id_empresa = '$this->id_empresa'
        order by v.fecha asc, v.numero asc";*/


        //echo $sql;
        $rest = $this->conectar->query($sql);
        $lista = [];
        foreach ($rest as $row) {
            $row['cod_v'] = $row['id_venta'];
            $row['id_venta'] = $row['id_venta'] . '--' . $row['nombre_xml'];
            $lista[] = $row;
        }
        return $lista;
    }

    public function verDocumentosResumen()
    {
        $sql = "select v.id_venta, v.fecha, ds.cod_sunat, ds.abreviatura, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.id_tido, v.enviado_sunat, v.estado
        from ventas as v 
            inner join documentos_sunat ds on v.id_tido = ds.id_tido
            inner join clientes c on v.id_cliente = c.id_cliente 
        where v.id_empresa = '$this->id_empresa' and v.fecha = '$this->fecha' and v.id_tido in (1,3)";
        return $this->conectar->get_Cursor($sql);
    }

    public function verFacturasResumen()
    {
        $sql = "select v.id_venta, v.fecha, ds.cod_sunat, ds.abreviatura, v.serie, v.numero, c.documento, c.datos, v.total, v.estado, v.id_tido, v.enviado_sunat, v.estado
        from ventas as v 
            inner join documentos_sunat ds on v.id_tido = ds.id_tido
            inner join clientes c on v.id_cliente = c.id_cliente 
        where v.id_empresa = '$this->id_empresa' and v.fecha = '$this->fecha' and v.id_tido = 2 ";
        return $this->conectar->get_Cursor($sql);
    }

    public function verPeriodos()
    {
        $sql = "select DISTINCT(concat(year(fecha), LPAD(month(fecha), 2, 0))) as periodo 
        from ventas 
        where id_empresa = '$this->id_empresa'
        order by concat(year(fecha), LPAD(month(fecha), 2, 0)) desc";
        return $this->conectar->get_Cursor($sql);
    }
  
}