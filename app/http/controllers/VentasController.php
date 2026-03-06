<?php

require_once "app/models/Venta.php";
require_once "app/models/Cliente.php";
require_once "app/models/DocumentoEmpresa.php";
require_once "app/models/ProductoVenta.php";
require_once "app/models/VentaServicio.php";
require_once "app/models/Varios.php";
require_once "app/models/VentaSunat.php";
require_once "app/models/VentaAnulada.php";
require_once "app/models/GuiaRemision.php";
require_once "app/clases/SendURL.php";
require_once "app/clases/SunatApi.php";


class VentasController extends Controller
{
    private $venta;
    private $sunatApi;
    private $conexion;
    private $guia;
    public function __construct()
    {
        $this->venta = new Venta();
        $this->sunatApi = new SunatApi();
        $this->guia = new GuiaRemision();
        $this->conexion = (new Conexion())->getConexion();
    }


    public function ingresosEgresosRender()
    {
        $sql = "SELECT
            ie.*,
            p.nombre,
            p.codigo,
            u.usuario,
            u.nombres,
            DATE_FORMAT(ie.fecha_creacion, '%d/%m/%Y %H:%i') as fecha_creacion_formatted,
            DATE_FORMAT(ie.fecha_actualizacion, '%d/%m/%Y %H:%i') as fecha_actualizacion_formatted,
            CASE 
                WHEN ie.almacen_egreso = '1' THEN 'Almacén 1'
                WHEN ie.almacen_egreso = '2' THEN 'Almacén 2'
                WHEN ie.almacen_egreso = '3' THEN 'Almacén 3'
                ELSE 'N/A'
            END as almacen_egreso_nombre,
            CASE 
                WHEN ie.almacen_ingreso = '1' THEN 'Almacén 1'
                WHEN ie.almacen_ingreso = '2' THEN 'Almacén 2'
                WHEN ie.almacen_ingreso = '3' THEN 'Almacén 3'
                ELSE 'N/A'
            END as almacen_ingreso_nombre
        FROM
            ingreso_egreso ie
            JOIN productos p ON ie.id_producto = p.id_producto
            INNER JOIN usuarios u on u.usuario_id = ie.id_usuario
        ORDER BY
            ie.fecha_creacion DESC";

        $result = $this->conexion->query($sql);

        // Verificar si la consulta fue exitosa
        if (!$result) {
            // Log del error para debugging
            error_log("Error en consulta SQL: " . $this->conexion->error);
            return []; // Retornar array vacío en caso de error
        }

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }

        return $data;
    }

    // Mantén los otros métodos como los tienes:
    public function ingresoAlmacen()
    {
        $respuesta['res'] = false;
        $observaciones = isset($_POST['observaciones']) ? $this->conexion->real_escape_string($_POST['observaciones']) : '';

        $sql = "INSERT INTO ingreso_egreso 
            SET id_producto = '{$_POST['productoid']}', 
                tipo = '{$_POST['tipo']}',
                cantidad = '{$_POST['cantidad']}', 
                id_usuario = '{$_SESSION['usuario_fac']}', 
                almacen_ingreso = '{$_POST['almacen']}',
                observaciones = '$observaciones',
                fecha_creacion = NOW(),
                fecha_actualizacion = NOW()";

        if ($this->conexion->query($sql)) {
            // Actualizar el stock del producto
            $sql = "UPDATE productos 
               SET cantidad = cantidad + '{$_POST['cantidad']}' 
               WHERE id_producto = '{$_POST['productoid']}'";
            $this->conexion->query($sql);
            $respuesta['res'] = true;
        }

        echo json_encode($respuesta);
    }

    public function egresoAlmacen()
    {
        $respuesta['res'] = false;
        $observaciones = isset($_POST['observaciones']) ? $this->conexion->real_escape_string($_POST['observaciones']) : '';

        // Verificar stock disponible antes de realizar el egreso
        $sql = "SELECT cantidad FROM productos WHERE id_producto = '{$_POST['productoid']}' AND almacen = '{$_POST['almacen']}'";
        $result = $this->conexion->query($sql);

        if ($result && $result->num_rows > 0) {
            $stock_actual = $result->fetch_assoc()['cantidad'];

            if ($stock_actual >= $_POST['cantidad']) {
                // Insertar el registro de egreso
                $sql = "INSERT INTO ingreso_egreso 
                    SET id_producto = '{$_POST['productoid']}', 
                        tipo = '{$_POST['tipo']}',
                        cantidad = '{$_POST['cantidad']}', 
                        id_usuario = '{$_SESSION['usuario_fac']}', 
                        almacen_ingreso = '{$_POST['alAlmacen']}', 
                        almacen_egreso = '{$_POST['almacen']}', 
                        estado = 0,
                        observaciones = '$observaciones',
                        fecha_creacion = NOW(),
                        fecha_actualizacion = NOW()";

                if ($this->conexion->query($sql)) {
                    // Actualizar el stock inmediatamente en el almacén de origen
                    $sql = "UPDATE productos 
                       SET cantidad = cantidad - {$_POST['cantidad']} 
                       WHERE id_producto = '{$_POST['productoid']}' 
                       AND almacen = '{$_POST['almacen']}'";
                    $this->conexion->query($sql);

                    $respuesta['res'] = true;
                }
            } else {
                $respuesta['res'] = false;
                $respuesta['msg'] = "Stock insuficiente";
            }
        } else {
            $respuesta['res'] = false;
            $respuesta['msg'] = "Producto no encontrado";
        }

        echo json_encode($respuesta);
    }

    public function confirmarTraslado()
    {
        if (isset($_POST['cod'])) {
            $id = $_POST['cod'];

            // Obtener información del traslado
            $sql = "SELECT * FROM ingreso_egreso WHERE intercambio_id = '$id'";
            $result = $this->conexion->query($sql);

            if ($result && $result->num_rows > 0) {
                $traslado = $result->fetch_assoc();

                // Actualizar stock en almacén de destino (sumar)
                $sql = "UPDATE productos 
                   SET cantidad = cantidad + '{$traslado['cantidad']}' 
                   WHERE id_producto = '{$traslado['id_producto']}' 
                   AND almacen = '{$traslado['almacen_ingreso']}'";
                $this->conexion->query($sql);

                // Marcar el traslado como confirmado y actualizar fecha
                $sql = "UPDATE ingreso_egreso 
                   SET estado = 1, 
                       fecha_actualizacion = NOW()
                   WHERE intercambio_id = '$id'";
                $this->conexion->query($sql);

                echo json_encode(['res' => true]);
                return;
            }
        }

        echo json_encode(['res' => false]);
    }

    public function envioComunicacionBajaPorEmpresa()
    {
        $listaBoletas = [];
        foreach (json_decode($_POST['boletas'], true) as $bol) {
            $listaBoletas[] = "v.id_venta='$bol'";
        }

        $sql = "select v.id_venta, v.enviado_sunat,vs.nombre_xml from ventas v
        join ventas_sunat vs on v.id_venta = vs.id_venta
        where " . implode(" OR ", $listaBoletas);

        $listaPorEnviar = $this->venta->exeSQL($sql);

        foreach ($listaPorEnviar as $vpr) {
            if ($vpr['enviado_sunat'] == '0') {
                if ($this->sunatApi->envioIndividualDocumentoVPorEmpresa($vpr['nombre_xml'], $_POST['empresa'])) {
                    $sql = "update ventas set enviado_sunat='1' where id_venta='{$vpr['id_venta']}'";
                    $this->venta->exeSQL($sql);
                }
                sleep(2);
            }
        }
        $respuesta = [];
        $respuesta['msg_resumen'] = $this->sunatApi->comunicacionBajaPorEmpresa(
            $listaBoletas,
            $_POST['empresa'],
            $_POST['fecharesumen'],
            $_POST["fechagen"],
            $_POST['correlativo1']
        );

        return json_encode($respuesta);
    }

    public function envioResumenDiarioPorEmpresa()
    {
        $listaBoletas = [];
        foreach (json_decode($_POST['boletas'], true) as $bol) {
            $listaBoletas[] = "v.id_venta='$bol'";
        }
        return json_encode([
            $this->sunatApi->resumenDiarioPorEmpresa(
                $listaBoletas,
                $_POST['empresa'],
                $_POST['fechagen'],
                $_POST['fecharesumen'],
                $_POST['correlativo1']
            ),
            $this->sunatApi->resumenDiarioBajaPorEmpresa(
                $listaBoletas,
                $_POST['empresa'],
                $_POST['fechagen'],
                $_POST['fecharesumen'],
                $_POST['correlativo2']
            )
        ]);
    }

    public function enviarDocumentoSunatPorEmpresa()
    {
        $sql = "select vs.*,v.id_empresa from ventas_sunat vs
        join ventas v on v.id_venta = vs.id_venta
        where vs.id_venta = '{$_POST["cod"]}'";
        $resultado = ["res" => false];
        if ($row = $this->venta->exeSQL($sql)->fetch_assoc()) {
            if ($this->sunatApi->envioIndividualDocumentoVPorEmpresa($row["nombre_xml"], $row['id_empresa'])) {
                $sql = "update ventas set  enviado_sunat='1'
                where id_venta = '{$_POST["cod"]}'";
                $this->venta->exeSQL($sql);
                $resultado['res'] = true;
            } else {
                $resultado['msg'] = $this->sunatApi->getMensaje();
            }
        }
        return json_encode($resultado);
    }

    public function regenerarXML()
    {
        $venta = $_POST["venta"];

        $sql = "SELECT * from ventas where id_venta='$venta'";
        $ventaData = $this->venta->exeSQL($sql)->fetch_assoc();
        $empresa = $this->venta->exeSQL("select * from empresas where id_empresa='{$ventaData['id_empresa']}'")->fetch_assoc();
        $cliente = $this->venta->exeSQL("select * from clientes where id_cliente='{$ventaData['id_cliente']}'")->fetch_assoc();


        $dataSend = [];
        $dataSend["certGlobal"] = false;

        $direccionselk = $cliente["direccion"];



        if (strlen(trim($direccionselk)) == "") {
            $direccionselk = '-';
        }
        if (trim($cliente["datos"]) == "") {
            $cliente["datos"] = '-';
        }

        $dataSend['cliente'] = json_encode([
            'doc_num' => $cliente["documento"],
            'nom_RS' => $cliente["datos"],
            'direccion' => $direccionselk
        ]);
        $dataSend['productos'] = [];
        $dataSend['apli_igv'] = $ventaData['apli_igv'] == 1;
        $dataSend['total'] = $ventaData["total"];
        $dataSend['serie'] = $ventaData["serie"];
        $dataSend['numero'] = $ventaData["numero"];
        $dataSend['fechaE'] = $ventaData["fecha_emision"];
        $dataSend['fechaV'] = $ventaData["fecha_vencimiento"];
        $dataSend['tipo_pago'] = $ventaData["id_tipo_pago"];
        $dataSend['igv_venta'] = $ventaData["igv"];
        $dataSend['dias_pagos'] = [];
        $dataSend['moneda'] = "PEN";

        $sql = "select * from dias_ventas where id_venta='$venta'";
        $cuotasVentas = $this->venta->exeSQL($sql);

        foreach ($cuotasVentas as $cuotas) {
            $dataSend['dias_pagos'][] = [
                "monto" => $cuotas['monto'],
                "fecha" => $cuotas['fecha']
            ];
        }

        $sql = "select pv.*,p.descripcion from productos_ventas pv
        join productos p on p.id_producto = pv.id_producto
        where pv.id_venta='$venta'";
        $listaProductos = $this->venta->exeSQL($sql);
        foreach ($listaProductos as $prod) {
            $dataSend['productos'][] = [
                "precio" => number_format($prod['precio'], 2, ".", ""),
                "cantidad" => number_format($prod['cantidad'], 0),
                "cod_pro" => $prod['id_producto'],
                "cod_sunat" => "",
                "descripcion" => $prod['descripcion']
            ];
        }

        $sql = "select * from ventas_servicios where  id_venta='$venta'";
        $listaProductos = $this->venta->exeSQL($sql);
        foreach ($listaProductos as $prod) {
            $dataSend['productos'][] = [
                "precio" => number_format($prod['monto'], 2, ".", ""),
                "cantidad" => number_format($prod['cantidad'], 0),
                "cod_pro" => $prod['id_item'],
                "cod_sunat" => $prod['codsunat'],
                "descripcion" => $prod['descripcion']
            ];
        }

        $dataSend["endpoints"] = $empresa['modo'];

        $dataSend['empresa'] = json_encode([
            'ruc' => $empresa['ruc'],
            'razon_social' => $empresa['razon_social'],
            'direccion' => $empresa['direccion'],
            'ubigeo' => $empresa['ubigeo'],
            'distrito' => $empresa['distrito'],
            'provincia' => $empresa['provincia'],
            'departamento' => $empresa['departamento'],
            'clave_sol' => $empresa['clave_sol'],
            'usuario_sol' => $empresa['user_sol']
        ]);
        $respuesta = ["res" => false];

        if ($ventaData['id_tido'] == 1 || $ventaData['id_tido'] == 2) {
            $dataSend['dias_pagos'] = json_encode($dataSend['dias_pagos']);

            $dataSend['productos'] = json_encode($dataSend['productos']);
            file_put_contents("Dataaaaaaaaaaaaaaaaaaaa.json", json_encode($dataSend));
            if ($ventaData['id_tido'] == 1) {
                $dataResp = $this->sunatApi->genBoletaXML($dataSend);
            } else {
                $dataResp = $this->sunatApi->genFacturaXML($dataSend);
            }
            if ($dataResp["res"]) {
                $respuesta["res"] = true;
                $sql = "select * from ventas_sunat where id_venta = '$venta'";
                if ($rrroooo = $this->venta->exeSQL($sql)->fetch_assoc()) {
                    $sql = "update ventas_sunat set hash='{$dataResp['data']['hash']}',
                      nombre_xml='{$dataResp['data']['nombre_archivo']}',
                      qr_data='{$dataResp['data']['qr']}' where id_venta = '$venta' ";
                    $this->venta->exeSQL($sql);
                } else {
                    $sql = "insert into ventas_sunat set hash='{$dataResp['data']['hash']}',
                      nombre_xml='{$dataResp['data']['nombre_archivo']}',
                      qr_data='{$dataResp['data']['qr']}',  id_venta = '$venta' ";
                    $this->venta->exeSQL($sql);
                }
            }
        }

        return json_encode($respuesta);
    }

    public function listaVentasPorEmpresa()
    {
        return json_encode($this->venta->verFilasPorEmpresas($_POST["empresa"], $_POST["sucursal"]));
    }


    public function enviarDocumentoSunat()
    {
        $sql = "select * from ventas_sunat where id_venta = '{$_POST["cod"]}'";
        $resultado = ["res" => false];
        if ($row = $this->venta->exeSQL($sql)->fetch_assoc()) {
            if ($this->sunatApi->envioIndividualDocumentoV($row["nombre_xml"])) {
                $sql = "update ventas set  enviado_sunat='1' where id_venta = '{$_POST["cod"]}'";
                $this->venta->exeSQL($sql);
                $resultado['res'] = true;
            } else {
                $resultado['msg'] = $this->sunatApi->getMensaje();
            }
        }
        return json_encode($resultado);
    }

    public function anularVenta()
    {
        $this->venta->setIdVenta($_POST['iventa']);
        $c_anulada = new VentaAnulada();
        $c_producto = new ProductoVenta();

        /*$c_producto->setIdVenta($this->venta->getIdVenta());
        $c_producto->eliminar();*/

        // Obtener datos de la venta antes de anular
        $sql = "SELECT id_cliente, total FROM ventas WHERE id_venta = '{$this->venta->getIdVenta()}'";
        $ventaData = $this->conexion->query($sql)->fetch_assoc();

        $c_anulada->setIdVenta($this->venta->getIdVenta());
        $c_anulada->setFecha(date("Y-m-d"));
        $c_anulada->setMotivo("-");
        $resultado = ["res" => false];
        if ($this->venta->anular()) {
            $resultado['res'] = true;
            $c_anulada->insertar();

            // ✅ Actualizar total_venta del cliente (restar el monto anulado)
            if ($ventaData) {
                $sql = "UPDATE clientes SET 
                        total_venta = GREATEST(0, COALESCE(total_venta, 0) - {$ventaData['total']})
                        WHERE id_cliente = '{$ventaData['id_cliente']}'";
                $this->conexion->query($sql);
            }
        }
        return json_encode($resultado);
    }

    public function listarVentas()
    {
        try {
            require_once "app/clases/serverside.php";
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache, must-revalidate');

            // Obtener el filtro de tipo si existe
            $tipoFiltro = isset($_GET['tipo_filtro']) ? $_GET['tipo_filtro'] : '';

            // Construir la consulta base
            $baseQuery = "SELECT 
            v.id_venta as cod_v,
            CONCAT(ds.abreviatura, ' | ', v.serie, ' - ', v.numero) as sn_v,
            CONCAT(c.documento, ' | ', c.datos) as datos_cl,
            CONCAT(IF(v.moneda = 1, 'S/ ', '$ '), ROUND(IF(v.apli_igv = '1', v.total / (v.igv + 1), v.total), 2)) as subtotal,
            CONCAT(IF(v.moneda = 1, 'S/ ', '$ '), ROUND(IF(v.apli_igv = '1', v.total / (v.igv + 1) * v.igv, 0), 2)) as igv_v,
            CONCAT(v.enviado_sunat, '-', v.id_tido, '-', v.id_venta) as doc_ventae,
            CONCAT(v.id_venta, '--', vs.nombre_xml) as id_venta,
            v.fecha_emision,
            ds.abreviatura,
            v.apli_igv,
            v.igv,
            v.id_tido,
            v.serie,
            v.numero,
            c.documento,
            c.datos,
            CONCAT(IF(v.moneda = 1, 'S/ ', '$ '), v.total) as total,
            v.estado,
            v.enviado_sunat,
            vs.nombre_xml
        FROM ventas v
        LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
        LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
        LEFT JOIN ventas_sunat vs ON v.id_venta = vs.id_venta
        WHERE v.id_empresa = '12'";

            // Agregar filtro según el tipo seleccionado
            if ($tipoFiltro === 'productos') {
                $baseQuery .= " AND EXISTS (SELECT 1 FROM productos_ventas pv WHERE pv.id_venta = v.id_venta)
                           AND NOT EXISTS (SELECT 1 FROM ventas_servicios vserv WHERE vserv.id_venta = v.id_venta)";
            } elseif ($tipoFiltro === 'servicios') {
                $baseQuery .= " AND EXISTS (SELECT 1 FROM ventas_servicios vserv WHERE vserv.id_venta = v.id_venta)
                           AND NOT EXISTS (SELECT 1 FROM productos_ventas pv WHERE pv.id_venta = v.id_venta)";
            } elseif ($tipoFiltro === 'mixto') {
                $baseQuery .= " AND EXISTS (SELECT 1 FROM productos_ventas pv WHERE pv.id_venta = v.id_venta)
                           AND EXISTS (SELECT 1 FROM ventas_servicios vserv WHERE vserv.id_venta = v.id_venta)";
            }

            // Filtro de sucursal si no es admin
            if ($_SESSION['rol'] != 1) {
                $baseQuery .= " AND v.sucursal = {$_SESSION["sucursal"]}";
            }

            $baseQuery .= " ORDER BY v.fecha_emision ASC, v.numero ASC";

            // Crear una vista temporal para ServerSide
            $tempViewName = "temp_filtered_ventas";
            $this->conexion->query("DROP VIEW IF EXISTS $tempViewName");
            $this->conexion->query("CREATE VIEW $tempViewName AS $baseQuery");

            $table_data = new TableData();
            $table_data->get(
                $tempViewName,
                "id_venta",
                [
                    "sn_v",
                    "fecha_emision",
                    "datos_cl",
                    "subtotal",
                    "igv_v",
                    "total",
                    "doc_ventae",
                    "estado",
                    "id_venta",
                ],
                false,
                "",
                false
            );

        } catch (Exception $e) {
            error_log("Error en listarVentas: " . $e->getMessage());
            echo json_encode([
                "sEcho" => isset($_GET['sEcho']) ? intval($_GET['sEcho']) : 1,
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => [],
                "error" => "Error al procesar la solicitud"
            ]);
            exit;
        }
    }

    public function detalleVenta()
    {
        //echo $_POST['iventa'];
        $this->venta->setIdVenta($_POST['iventa']);
        return $this->venta->verDetalle();
    }
    public function tipoVenta()
    {
        //echo $_POST['iventa'];
        $idVenta = $_POST['iventa'];
        $sqlProducto = "SELECT * FROM productos_ventas WHERE id_venta = $idVenta";
        $sqlServicio = "SELECT * FROM ventas_servicios WHERE id_venta = $idVenta";
        $returnFetch = $this->venta->exeSQL($sqlProducto)->fetch_assoc();
        $respuesta['tipo'] = '';
        $respuesta['res'] = false;
        if (empty($returnFetch)) {
            $returnFetchServicios = $this->venta->exeSQL($sqlServicio)->fetch_assoc();
            $respuesta['tipo'] = 'servicio';
            $respuesta['data'] = $returnFetchServicios;
            $respuesta['res'] = true;
            return json_encode($respuesta);
        } else {
            $respuesta['tipo'] = 'productos';
            $respuesta['data'] = $returnFetch;
            $respuesta['res'] = true;
            return json_encode($respuesta);
        }
    }


    public function detalleVenta2()
    {
        //echo $_POST['iventa'];
        $this->venta->setIdVenta($_POST['iventa']);
        return $this->venta->verDetalle2();
    }

    public function editVentaServicio()
    {
        $resultado = ["res" => false];



        $dataSend = [];
        $dataSend["certGlobal"] = false;


        $c_cliente = new Cliente();
        $c_venta = new Venta();
        $c_tido = new DocumentoEmpresa();
        $c_detalle = new ProductoVenta();
        $c_servicio = new VentaServicio();
        // $c_curl = new SendCurlVenta();
        $c_sunat = new VentaSunat();
        $c_varios = new Varios();

        $id_empresa = $_SESSION['id_empresa'];

        $sql = "SELECT * from empresas where id_empresa = " . $id_empresa;

        $respEmpre = $c_venta->exeSQL($sql)->fetch_assoc();

        $igv_empr_sel = $respEmpre['igv'];


        $c_cliente->setIdEmpresa($id_empresa);
        $c_cliente->setDocumento(filter_input(INPUT_POST, 'num_doc'));
        $c_cliente->setDatos(filter_input(INPUT_POST, 'nom_cli'));
        $c_cliente->setDireccion(filter_input(INPUT_POST, 'dir_cli'));
        $c_cliente->setDireccion2(filter_input(INPUT_POST, 'dir2_cli'));

        if ($c_cliente->getDocumento() == "") {
            $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar("SD" . $c_varios->generarCodigo(5), $nombre, $_POST['id_cliente']);
            /*             $c_cliente->setDocumento("SD" . $c_varios->generarCodigo(5));
            $c_cliente->insertar(); */
        } else {
            $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']);
            /*  $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']); */
            /*  if (!$c_cliente->verificarDocumento()) {
                $c_cliente->insertar();
            } else {
                $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
                $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
                $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']);
            } */
        }
        /*  $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
        $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
        $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']); */


        $resultado["email"] = $c_cliente->getEmail() ? $c_cliente->getEmail() : '';
        $resultado["cel"] = $c_cliente->getTelefono() ? $c_cliente->getTelefono() : '';

        $direccionselk = '';
        if ($_POST['dir_pos'] == 1) {
            $direccionselk = $_POST['dir_cli'];
        } elseif ($_POST['dir_pos'] == 2) {
            $direccionselk = $_POST['dir2_cli'];
        }

        if (trim($c_cliente->getDocumento()) == "") {
            $c_cliente->setDocumento('');
        }
        if (strlen(trim($direccionselk)) == "") {
            $direccionselk = '-';
        }
        if (trim($c_cliente->getDatos()) == "") {
            $c_cliente->setDatos('-');
        }

        $dataSend['cliente'] = json_encode([
            'doc_num' => $c_cliente->getDocumento(),
            'nom_RS' => $c_cliente->getDatos(),
            'direccion' => $direccionselk
        ]);
        $c_venta->setDireccion($direccionselk);
        /*   $dataSend['productos'] = []; */

        $c_venta->setApliIgv($_POST['apli_igv']);
        $c_venta->setIdEmpresa($id_empresa);
        $c_venta->setFecha($_POST['fecha']);
        $c_venta->setFechaVenc($_POST['tipo_pago'] == '1' ? $_POST['fecha'] : $_POST['fechaVen']);
        $c_venta->setDiasPagos($_POST['dias_pago']);
        $c_venta->setIdTipoPago($_POST['tipo_pago']);
        $c_venta->setObserva($_POST['observ']);

        $c_venta->setIdCliente($_POST['id_cliente']);
        $c_venta->setIgv($igv_empr_sel);
        $c_venta->setTotal(filter_input(INPUT_POST, 'total'));
        /*     $c_venta->setIdVenta(); */
        $tipoventa = filter_input(INPUT_POST, 'tipoventa');
        /* 

        $dataSend['apli_igv'] = $_POST['apli_igv'] == 1;
        $dataSend['total'] = $c_venta->getTotal();
        $dataSend['serie'] = $c_tido->getSerie();
        $dataSend['numero'] = $c_tido->getNumero();
        $dataSend['fechaE'] = $c_venta->getFecha();
        $dataSend['fechaV'] = $c_venta->getFechaVenc();
        $dataSend['tipo_pago'] = $c_venta->getIdTipoPago();
        $dataSend['igv_venta'] = $igv_empr_sel;
        $dataSend['dias_pagos'] = [];
        $dataSend['moneda'] = "PEN"; */

        $listaPagos = json_decode($_POST['dias_lista'], true);

        if ($c_venta->editar($_POST['idVenta'])) {

            $resultado["res"] = true;
            $array_detalle = json_decode($_POST['listaPro'], true);
            foreach ($listaPagos as $diaP) {
                $sql = "insert into dias_ventas set id_venta='{$c_venta->getIdVenta()}',
                    monto='{$diaP['monto']}',fecha='{$diaP['fecha']}',estado='0'";
                $c_venta->exeSQL($sql);
                /*  $dataSend['dias_pagos'][] = [
                    "monto" => $diaP['monto'],
                    "fecha" => $diaP['fecha']
                ]; */
            }
            /*    $dataSend['dias_pagos'] = json_encode($dataSend['dias_pagos']); */

            $nroitem = 1;


            /*  $c_servicio->setIdventa(); */
            $c_servicio->eliminar($_POST['idVenta']);

            foreach ($array_detalle as $fila) {
                $c_servicio->setDescripcion($fila['descripcion']);
                $c_servicio->setCantidad($fila['cantidad']);
                $c_servicio->setMonto($fila['precioVenta']);
                $c_servicio->setCodsunat(isset($fila['codsunat']) ? $fila['codsunat'] : '');
                $c_servicio->setIditem($nroitem);
                /*  $c_servicio->setIdventa($_POST['idVenta']); */
                $c_servicio->editar($_POST['idVenta']);
                $nroitem++;
                /*     $dataSend['productos'][] = [
                    "precio" => $fila['precio'],
                    "cantidad" => $fila['cantidad'],
                    "cod_pro" => $nroitem,
                    "cod_sunat" => isset($fila['codsunat']) ? $fila['codsunat'] : '',
                    "descripcion" => $fila['descripcion']
                ]; */
            }

            //definir url segun el tipo de documento sunat
            if ($c_venta->getIdTido() == 1) {
                $archivo = "boleta";
            }
            if ($c_venta->getIdTido() == 2) {
                $archivo = "factura";
            }

            /*   if ($c_venta->getIdTido() == 1 || $c_venta->getIdTido() == 2) { */

            /* 
                $dataSend["endpoints"] = $respEmpre['modo'];

                $dataSend['empresa'] = json_encode([
                    'ruc' => $respEmpre['ruc'],
                    'razon_social' => $respEmpre['razon_social'],
                    'direccion' => $respEmpre['direccion'],
                    'ubigeo' => $respEmpre['ubigeo'],
                    'distrito' => $respEmpre['distrito'],
                    'provincia' => $respEmpre['provincia'],
                    'departamento' => $respEmpre['departamento'],
                    'clave_sol' => $respEmpre['clave_sol'],
                    'usuario_sol' => $respEmpre['user_sol']
                ]);



                $dataSend['productos'] = json_encode($dataSend['productos']); */
            /* 
                if ($c_venta->getIdTido() == 1) {
                    $dataResp = $this->sunatApi->genBoletaXML($dataSend);
                } else {
                    $dataResp = $this->sunatApi->genFacturaXML($dataSend);
                }



                if ($dataResp["res"]) {
                    $c_sunat->setIdVenta($c_venta->getIdVenta());
                    $c_sunat->setHash($dataResp['data']['hash']);
                    $c_sunat->setNombreXml($dataResp['data']['nombre_archivo']);
                    $c_sunat->setQrData($dataResp['data']['qr']);
                    $c_sunat->insertar();
                } else {
                } */
            /* } */ /* else {
$c_sunat->setIdVenta($c_venta->getIdVenta());
$c_sunat->setHash("-");
$c_sunat->setNombreXml("-");
$c_sunat->setQrData('-');
$c_sunat->insertar();

$resultado["valor"] = $c_venta->getIdVenta();
} */
            /*    $resultado["nomFact"] = $c_sunat->getNombreXml() . ".pdf";
            $resultado["urlFact"] = URL::to('/venta/comprobante/pdf/' . $c_sunat->getIdVenta() . '/' . $c_sunat->getNombreXml());
            $resultado["urlFactd"] = URL::to('/venta/comprobante/pdfd/' . $c_sunat->getIdVenta() . '/' . $c_sunat->getNombreXml());
        } */
        }
        /*  $_REQUEST */
        $resultado["nomFact"] = '2020' . ".pdf";
        $resultado["urlFact"] = URL::to('/venta/comprobante/pdf/' . $_POST['idVenta'] . '/' . '2020');
        $resultado["urlFactd"] = URL::to('/venta/comprobante/pdfd/' . $_POST['idVenta'] . '/2020');

        return json_encode($resultado);
    }
    public function editVentaProducto()
    {


        $resultado = ["res" => false];



        $dataSend = [];
        $dataSend["certGlobal"] = false;


        $c_cliente = new Cliente();
        $c_venta = new Venta();
        $c_tido = new DocumentoEmpresa();
        $c_detalle = new ProductoVenta();
        /*  $c_servicio = new VentaServicio(); */
        // $c_curl = new SendCurlVenta();
        $c_sunat = new VentaSunat();
        $c_varios = new Varios();

        $id_empresa = $_SESSION['id_empresa'];

        $sql = "SELECT * from empresas where id_empresa = " . $id_empresa;

        $respEmpre = $c_venta->exeSQL($sql)->fetch_assoc();

        $igv_empr_sel = $respEmpre['igv'];


        $c_cliente->setIdEmpresa($id_empresa);
        $c_cliente->setDocumento(filter_input(INPUT_POST, 'num_doc'));
        $c_cliente->setDatos(filter_input(INPUT_POST, 'nom_cli'));
        $c_cliente->setDireccion(filter_input(INPUT_POST, 'dir_cli'));
        $c_cliente->setDireccion2(filter_input(INPUT_POST, 'dir2_cli'));


        if ($c_cliente->getDocumento() == "") {
            $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar("SD" . $c_varios->generarCodigo(5), $nombre, $_POST['id_cliente']);
            /*             $c_cliente->setDocumento("SD" . $c_varios->generarCodigo(5));
            $c_cliente->insertar(); */
        } else {
            $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']);
            /*  $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
            $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
            $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']); */
            /*  if (!$c_cliente->verificarDocumento()) {
                $c_cliente->insertar();
            } else {
                $numDoc = $_POST['num_doc'] == '' ? '' : $_POST['num_doc'];
                $nombre = $_POST['nom_cli'] == '' ? '' : $_POST['nom_cli'];
                $c_cliente->modificar($numDoc, $nombre, $_POST['id_cliente']);
            } */
        }

        $resultado["email"] = $c_cliente->getEmail() ? $c_cliente->getEmail() : '';
        $resultado["cel"] = $c_cliente->getTelefono() ? $c_cliente->getTelefono() : '';

        $direccionselk = '';
        if ($_POST['dir_pos'] == 1) {
            $direccionselk = $_POST['dir_cli'];
        } elseif ($_POST['dir_pos'] == 2) {
            $direccionselk = $_POST['dir2_cli'];
        }

        if (trim($c_cliente->getDocumento()) == "") {
            $c_cliente->setDocumento('');
        }
        if (strlen(trim($direccionselk)) == "") {
            $direccionselk = '-';
        }
        if (trim($c_cliente->getDatos()) == "") {
            $c_cliente->setDatos('-');
        }

        /*  $dataSend['cliente'] = json_encode([
            'doc_num' => $c_cliente->getDocumento(),
            'nom_RS' => $c_cliente->getDatos(),
            'direccion' => $direccionselk
        ]); */
        $c_venta->setDireccion($direccionselk);
        $c_tido->setIdEmpresa($id_empresa);
        $c_tido->setIdTido(filter_input(INPUT_POST, 'tipo_doc'));
        $c_tido->obtenerDatos();
        $c_venta->setApliIgv($_POST['apli_igv']);
        $c_venta->setIdEmpresa($id_empresa);
        $c_venta->setFecha($_POST['fecha']);
        $c_venta->setFechaVenc($_POST['tipo_pago'] == '1' ? $_POST['fecha'] : $_POST['fechaVen']);
        $c_venta->setDiasPagos($_POST['dias_pago']);
        $c_venta->setIdTipoPago($_POST['tipo_pago']);
        $c_venta->setObserva($_POST['observ']);
        $c_venta->setIdTido($c_tido->getIdTido());
        $c_venta->setSerie($c_tido->getSerie());
        $c_venta->setNumero($c_tido->getNumero());
        $c_venta->setIdCliente($_POST['id_cliente']);
        $c_venta->setIgv($igv_empr_sel);
        $c_venta->setTotal(filter_input(INPUT_POST, 'total'));


        /*      $dataSend['apli_igv'] = $_POST['apli_igv'] == 1;
        $dataSend['total'] = $c_venta->getTotal();
        $dataSend['serie'] = $c_tido->getSerie();
        $dataSend['numero'] = $c_tido->getNumero();
        $dataSend['fechaE'] = $c_venta->getFecha();
        $dataSend['fechaV'] = $c_venta->getFechaVenc();
        $dataSend['tipo_pago'] = $c_venta->getIdTipoPago();
        $dataSend['igv_venta'] = $igv_empr_sel;
        $dataSend['dias_pagos'] = [];
        $dataSend['moneda'] = "PEN"; */

        $listaPagos = json_decode($_POST['dias_lista'], true);

        if ($c_venta->editar($_POST['idVenta'])) {

            $resultado["res"] = true;
            $array_detalle = json_decode($_POST['listaPro'], true);
            foreach ($listaPagos as $diaP) {
                $sql = "insert into dias_ventas set id_venta='{$c_venta->getIdVenta()}',
                    monto='{$diaP['monto']}',fecha='{$diaP['fecha']}',estado='0'";
                $c_venta->exeSQL($sql);
                /*  $dataSend['dias_pagos'][] = [
                    "monto" => $diaP['monto'],
                    "fecha" => $diaP['fecha']
                ]; */
            }
            /*  $dataSend['dias_pago'] = json_encode($dataSend['dias_pagos']); */


            /* $c_detalle->setIdVenta($c_venta->getIdVenta()); */
            $c_detalle->eliminar($_POST['idVenta']);

            /*  $c_servicio->eliminar($_POST['idVenta']);   */

            foreach ($array_detalle as $fila) {
                $c_detalle->setIdProducto(isset($fila['productoid']) ? $fila['productoid'] : 0);
                $c_detalle->setCantidad(isset($fila['cantidad']) ? $fila['cantidad'] : 0);
                $c_detalle->setCosto(isset($fila['costo']) ? $fila['costo'] : 0);
                $c_detalle->setPrecio(isset($fila['precio']) ? $fila['precio'] : 0);
                $c_detalle->setIdVenta($_POST['idVenta']);
                $c_detalle->setPrecioUsado(isset($fila['precio_usado']) ? $fila['precio_usado'] : 1);
                $c_detalle->insertar();
                /*   $dataSend['productos'][] = [
                    "precio" => $fila['precio'],
                    "cantidad" => $fila['cantidad'],
                    "cod_pro" => $fila['productoid'],
                    "cod_sunat" => "",
                    "descripcion" => $fila['descripcion']
                ]; */
            }


            //definir url segun el tipo de documento sunat
            /*   if ($c_venta->getIdTido() == 1) {
                $archivo = "boleta";
            }
            if ($c_venta->getIdTido() == 2) {
                $archivo = "factura";
            }

            if ($c_venta->getIdTido() == 1 || $c_venta->getIdTido() == 2) {


                $dataSend["endpoints"] = $respEmpre['modo'];

                $dataSend['empresa'] = json_encode([
                    'ruc' => $respEmpre['ruc'],
                    'razon_social' => $respEmpre['razon_social'],
                    'direccion' => $respEmpre['direccion'],
                    'ubigeo' => $respEmpre['ubigeo'],
                    'distrito' => $respEmpre['distrito'],
                    'provincia' => $respEmpre['provincia'],
                    'departamento' => $respEmpre['departamento'],
                    'clave_sol' => $respEmpre['clave_sol'],
                    'usuario_sol' => $respEmpre['user_sol']
                ]);



                $dataSend['productos'] = json_encode($dataSend['productos']);

                if ($c_venta->getIdTido() == 1) {
                    $dataResp = $this->sunatApi->genBoletaXML($dataSend);
                } else {
                    $dataResp = $this->sunatApi->genFacturaXML($dataSend);
                }



                if ($dataResp["res"]) {
                    $c_sunat->setIdVenta($c_venta->getIdVenta());
                    $c_sunat->setHash($dataResp['data']['hash']);
                    $c_sunat->setNombreXml($dataResp['data']['nombre_archivo']);
                    $c_sunat->setQrData($dataResp['data']['qr']);
                    $c_sunat->insertar();
                } else {
                }
            } else {
                $c_sunat->setIdVenta($c_venta->getIdVenta());
                $c_sunat->setHash("-");
                $c_sunat->setNombreXml("-");
                $c_sunat->setQrData('-');
                $c_sunat->insertar();

                $resultado["valor"] = $c_venta->getIdVenta();
            } */
            $resultado["nomFact"] = '2020' . ".pdf";
            $resultado["urlFact"] = URL::to('/venta/comprobante/pdf/' . $_POST['idVenta'] . '/' . '2020');
            $resultado["urlFactd"] = URL::to('/venta/comprobante/pdfd/' . $_POST['idVenta'] . '/2020');
        }

        return json_encode($resultado);
    }
    public function guardarVentas()
    {
        try {
            // Logging inicial para depuración
            error_log("Iniciando guardarVentas con datos: " . json_encode($_POST));

            // Validación de moneda y tipo de cambio
            if (!isset($_POST['moneda']) || !in_array($_POST['moneda'], ['1', '2'])) {
                $_POST['moneda'] = '1'; // Establecer Soles como valor predeterminado
                error_log("Moneda no válida o no especificada, estableciendo a Soles (1)");
            }

            // Validar tipo de cambio según la moneda
            if ($_POST['moneda'] == '1') {
                // Si es Soles, establecer tc a 1
                $_POST['tc'] = '1';
                error_log("Moneda es Soles, estableciendo tc a 1");
            } else if ($_POST['moneda'] == '2') {
                // Si es Dólares, asegurar un valor válido para tc
                if (empty($_POST['tc']) || !is_numeric($_POST['tc']) || floatval($_POST['tc']) <= 0) {
                    $_POST['tc'] = '3.70';
                    error_log("Tipo de cambio no válido para dólares, estableciendo valor predeterminado: 3.70");
                }
            }
            error_log("Después de validación - Moneda: " . $_POST['moneda'] . ", TC: " . $_POST['tc']);

            $resultado = ["res" => false];
            $dataSend = [];
            $dataSend["certGlobal"] = false;

            $c_cliente = new Cliente();
            $c_venta = new Venta();
            $c_tido = new DocumentoEmpresa();
            $c_detalle = new ProductoVenta();
            $c_servicio = new VentaServicio();
            // $c_curl = new SendCurlVenta();
            $c_sunat = new VentaSunat();
            $c_varios = new Varios();
            $c_guia = new GuiaRemision();

            $id_empresa = $_SESSION['id_empresa'];

            $sql = "SELECT * from empresas where id_empresa = " . $id_empresa;
            $respEmpre = $c_venta->exeSQL($sql)->fetch_assoc();
            $igv_empr_sel = $respEmpre['igv'];

            $c_cliente->setIdEmpresa($id_empresa);
            $c_cliente->setDocumento(filter_input(INPUT_POST, 'num_doc'));
            $c_cliente->setDatos(filter_input(INPUT_POST, 'nom_cli'));
            $c_cliente->setDireccion(filter_input(INPUT_POST, 'dir_cli'));
            $c_cliente->setDireccion2(filter_input(INPUT_POST, 'dir2_cli'));

            if ($c_cliente->getDocumento() == "") {
                $c_cliente->setDocumento("SD" . $c_varios->generarCodigo(5));
                $c_cliente->insertar();
            } else {
                if (!$c_cliente->verificarDocumento()) {
                    $c_cliente->insertar();
                }
            }

            $resultado["email"] = $c_cliente->getEmail() ? $c_cliente->getEmail() : '';
            $resultado["cel"] = $c_cliente->getTelefono() ? $c_cliente->getTelefono() : '';

            $direccionselk = '';
            if (isset($_POST['dir_pos']) && $_POST['dir_pos'] == 1) {
                $direccionselk = $_POST['dir_cli'];
            } elseif (isset($_POST['dir_pos']) && $_POST['dir_pos'] == 2) {
                $direccionselk = $_POST['dir2_cli'];
            }

            if (trim($c_cliente->getDocumento()) == "") {
                $c_cliente->setDocumento('');
            }
            if (strlen(trim($direccionselk)) == "") {
                $direccionselk = '-';
            }
            if (trim($c_cliente->getDatos()) == "") {
                $c_cliente->setDatos('-');
            }

            $dataSend['cliente'] = json_encode([
                'doc_num' => $c_cliente->getDocumento(),
                'nom_RS' => $c_cliente->getDatos(),
                'direccion' => $direccionselk
            ]);

            $idCoti = isset($_POST['idCoti']) && $_POST['idCoti'] ? $_POST['idCoti'] : null;
            $c_venta->setDireccion($direccionselk);
            $dataSend['productos'] = [];
            $c_tido->setIdEmpresa($id_empresa);
            $c_tido->setIdTido(filter_input(INPUT_POST, 'tipo_doc'));
            $c_tido->obtenerDatos();
            $c_venta->setApliIgv($_POST['apli_igv']);
            $c_venta->setIdEmpresa($id_empresa);
            $c_venta->setFecha($_POST['fecha']);
            $c_venta->setFechaVenc($_POST['tipo_pago'] == '1' ? $_POST['fecha'] : $_POST['fechaVen']);
            $c_venta->setDiasPagos($_POST['dias_pago']);
            $c_venta->setIdTipoPago($_POST['tipo_pago']);
            $metodo = intval($_POST['metodo']);
            $c_venta->setMetodo($metodo);
            $c_venta->setObserva($_POST['observ']);
            $c_venta->setIdTido($c_tido->getIdTido());
            $c_venta->setSerie($c_tido->getSerie());
            $c_venta->setNumero($c_tido->getNumero());
            $c_venta->setIdCliente($c_cliente->getIdCliente());
            $c_venta->setIgv($igv_empr_sel);
            $c_venta->setTotal(filter_input(INPUT_POST, 'total'));
            $c_venta->setIdCoti($idCoti);
            $tipoventa = filter_input(INPUT_POST, 'tipoventa');

            $dataSend['apli_igv'] = $_POST['apli_igv'] == 1;
            $dataSend['total'] = number_format($c_venta->getTotal(), 2, '.', '');
            $dataSend['serie'] = $c_tido->getSerie();
            $dataSend['numero'] = $c_tido->getNumero();
            $dataSend['fechaE'] = $c_venta->getFecha();
            $dataSend['fechaV'] = $c_venta->getFechaVenc();
            $dataSend['tipo_pago'] = $c_venta->getIdTipoPago();
            $dataSend['igv_venta'] = $igv_empr_sel;
            $dataSend['dias_pagos'] = [];

            // Asegurar que la moneda sea consistente
            $dataSend['moneda'] = $_POST['moneda'] == '2' ? "USD" : "PEN";
            $dataSend['tc'] = $_POST['tc'];

            $datosGuiaRemosion = isset($_POST['datosGuiaRemosion']) ? json_decode($_POST['datosGuiaRemosion'], true) : [];
            $datosTransporteGuiaRemosion = isset($_POST['datosTransporteGuiaRemosion']) ? json_decode($_POST['datosTransporteGuiaRemosion'], true) : [];
            $listaPagos = isset($_POST['dias_lista']) ? json_decode($_POST['dias_lista'], true) : [];

            if ($c_venta->insertar()) {
                if (isset($_POST['pagos'])) {
                    $pagos = $_POST["pagos"];
                    foreach ($pagos as $i => $pago) {
                        $npago = $i + 1;
                        if (isset($pago["metodoPago"]) && $pago["metodoPago"] !== "" && isset($pago['montoPago']) && $pago['montoPago'] !== "") {
                            $sql = "insert into ventas_pagos set id_venta='{$c_venta->getIdVenta()}',
                            metodo_pago='{$pago['metodoPago']}',monto='{$pago['montoPago']}',npago='{$npago}'";
                            $c_venta->exeSQL($sql);
                        }
                    }
                }

                if (isset($_POST['idCoti']) && $_POST['idCoti']) {
                    $tipoCoti = isset($_POST['tipoCotizacion']) ? $_POST['tipoCotizacion'] : 'normal';

                    if ($tipoCoti === 'taller') {
                        $sql = "UPDATE taller_cotizaciones set estado = '1' WHERE id_cotizacion = '{$_POST['idCoti']}'";
                    } else {
                        $sql = "UPDATE cotizaciones set estado = '1' WHERE cotizacion_id = '{$_POST['idCoti']}'";
                    }
                    $this->conexion->query($sql);
                }

                // ✅ NUEVO: Actualizar guía de remisión si viene desde una guía
                if (isset($_POST['idGuia']) && $_POST['idGuia']) {
                    $sql = "UPDATE guia_remision SET id_venta = '{$c_venta->getIdVenta()}' WHERE id_guia_remision = '{$_POST['idGuia']}'";
                    $this->conexion->query($sql);
                }

                // ✅ Actualizar ultima_venta y total_venta del cliente con fecha y hora actual
                $sql = "UPDATE clientes SET 
                        ultima_venta = NOW(),
                        total_venta = COALESCE(total_venta, 0) + {$c_venta->getTotal()}
                        WHERE id_cliente = '{$c_cliente->getIdCliente()}'";
                $this->conexion->query($sql);

                $resultado["res"] = true;
                $array_detalle = isset($_POST['listaPro']) ? json_decode($_POST['listaPro'], true) : [];
                if (empty($array_detalle) && isset($_POST['listaPro'])) {
                    // Intentar decodificar eliminando backslashes si viniera escapado
                    $array_detalle = json_decode(stripslashes($_POST['listaPro']), true) ?: [];
                }
                error_log("guardarVentas listaPro count=" . (is_array($array_detalle) ? count($array_detalle) : 0));

                foreach ($listaPagos as $diaP) {
                    $sql = "insert into dias_ventas set id_venta='{$c_venta->getIdVenta()}',
                        monto='{$diaP['monto']}',fecha='{$diaP['fecha']}',estado='0'";
                    $c_venta->exeSQL($sql);
                    $dataSend['dias_pagos'][] = [
                        "monto" => $diaP['monto'],
                        "fecha" => $diaP['fecha']
                    ];
                }
                $dataSend['dias_pagos'] = json_encode($dataSend['dias_pagos']);

                $dataSaveLog = "Venta: {$c_venta->getIdVenta()}, fecha: " . date("Y-m-d") . "\n\n";

                if ($tipoventa == 1 || !empty($array_detalle)) {
                    $c_detalle->setIdVenta($c_venta->getIdVenta());

                    // Si viene de cotización de taller o guía con equipos, crear ventas_equipos y mapear
                    $mapEquipo = [];
                    $tieneEquipos = false;
                    
                    if (isset($_POST['tipoCotizacion']) && $_POST['tipoCotizacion'] === 'taller') {
                        $tieneEquipos = true;
                        $equiposVenta = isset($_POST['equiposVenta']) ? json_decode($_POST['equiposVenta'], true) : [];
                        foreach ($equiposVenta as $eq) {
                            $marca = $this->conexion->real_escape_string($eq['marca'] ?? '');
                            $equipo = $this->conexion->real_escape_string($eq['equipo'] ?? '');
                            $modelo = $this->conexion->real_escape_string($eq['modelo'] ?? '');
                            $serie = $this->conexion->real_escape_string($eq['numero_serie'] ?? '');
                            $idCotiEq = isset($eq['id_cotizacion_equipo']) ? intval($eq['id_cotizacion_equipo']) : 'NULL';

                            $sqlInsEq = "INSERT INTO ventas_equipos (id_venta, id_cotizacion_equipo, marca, equipo, modelo, numero_serie)
                                         VALUES ('{$c_venta->getIdVenta()}', " . ($idCotiEq === 'NULL' ? 'NULL' : "'$idCotiEq'") . ", '$marca', '$equipo', '$modelo', '$serie')";
                            if ($this->conexion->query($sqlInsEq)) {
                                $idVe = $this->conexion->insert_id;
                                if ($idCotiEq !== 'NULL') {
                                    $mapEquipo[$idCotiEq] = $idVe;
                                }
                            }
                        }
                    }
                    
                    // Si viene desde guía y tiene equipos, procesarlos y crear mapeo
                    if (isset($_POST['idGuia']) && $_POST['idGuia'] && isset($_POST['equiposVenta'])) {
                        $tieneEquipos = true;
                        $equiposVenta = json_decode($_POST['equiposVenta'], true);
                        
                        // Consultar la guía original para obtener relación producto-equipo
                        $guiaId = intval($_POST['idGuia']);
                        $sqlGuiaInfo = "SELECT gd.id_producto, gd.id_guia_equipo, ge.numero_serie 
                                       FROM guia_detalles gd 
                                       LEFT JOIN guia_equipos ge ON gd.id_guia_equipo = ge.id_guia_equipo 
                                       WHERE gd.id_guia = $guiaId";
                        $resultGuiaInfo = $this->conexion->query($sqlGuiaInfo);
                        
                        // Crear mapeo de producto -> serie de equipo
                        $productoEquipoMap = [];
                        if ($resultGuiaInfo) {
                            while ($row = $resultGuiaInfo->fetch_assoc()) {
                                if ($row['numero_serie']) {
                                    $productoEquipoMap[$row['id_producto']] = $row['numero_serie'];
                                    error_log("Mapeo producto {$row['id_producto']} -> equipo serie {$row['numero_serie']}");
                                }
                            }
                        } else {
                            error_log("Error en consulta guía: " . $this->conexion->error);
                            error_log("SQL ejecutado: $sqlGuiaInfo");
                        }
                        
                        foreach ($equiposVenta as $eq) {
                            // Los equipos de guía vienen con descripción, extraer datos
                            $descripcion = $eq['descripcion'] ?? '';
                            if (preg_match('/EQUIPO: (.+?) - Modelo: (.+?) - Serie: (.+?)$/', $descripcion, $matches)) {
                                $marcaEquipo = trim($matches[1]);
                                $modelo = trim($matches[2]);
                                $serie = trim($matches[3]);
                                
                                // Separar marca y equipo (formato: "MARCA EQUIPO")
                                $partes = explode(' ', $marcaEquipo, 2);
                                $marca = $partes[0] ?? '';
                                $equipoNombre = $partes[1] ?? $marcaEquipo;
                                
                                $marca = $this->conexion->real_escape_string($marca);
                                $equipoNombre = $this->conexion->real_escape_string($equipoNombre);
                                $modelo = $this->conexion->real_escape_string($modelo);
                                $serie = $this->conexion->real_escape_string($serie);
                                
                                $sqlInsEq = "INSERT INTO ventas_equipos (id_venta, id_cotizacion_equipo, marca, equipo, modelo, numero_serie)
                                             VALUES ('{$c_venta->getIdVenta()}', NULL, '$marca', '$equipoNombre', '$modelo', '$serie')";
                                if ($this->conexion->query($sqlInsEq)) {
                                    $idVentaEquipo = $this->conexion->insert_id;
                                    // Mapear por serie para usar en productos
                                    $mapEquipo[$serie] = $idVentaEquipo;
                                    error_log("Equipo desde guía guardado: $marca $equipoNombre - $modelo - $serie (ID: $idVentaEquipo)");
                                }
                            }
                        }
                    }

                    $insertados = 0;
                    foreach ($array_detalle as $fila) {
                        $c_detalle->setIdProducto(isset($fila['productoid']) ? $fila['productoid'] : 0);
                        $c_detalle->setCantidad(isset($fila['cantidad']) ? $fila['cantidad'] : 0);
                        $c_detalle->setCosto(isset($fila['costo']) ? $fila['costo'] : 0);

                        // Asegurar que tc sea un número válido para cálculos
                        $tc = floatval($_POST['tc']);
                        if ($tc <= 0) { $tc = 1; }

                        $precioVenta = isset($fila['precioVenta']) ? $fila['precioVenta'] : 0;
                        $c_detalle->setPrecio($_POST['moneda'] == '1' ? $precioVenta : $precioVenta / $tc);
                        // precio_usado en productos_ventas es un flag (char(1)), no un monto
                        $precioUsadoFlag = isset($_POST['usar_precio']) && $_POST['usar_precio'] !== '' ? $_POST['usar_precio'] : '5';
                        $c_detalle->setPrecioUsado($precioUsadoFlag);

                        // Asignar equipo de la venta si aplica
                        $idCotiEqFila = isset($fila['id_cotizacion_equipo']) ? intval($fila['id_cotizacion_equipo']) : null;
                        $idProducto = isset($fila['productoid']) ? intval($fila['productoid']) : 0;
                        
                        if ($idCotiEqFila && isset($mapEquipo[$idCotiEqFila])) {
                            // Caso: Viene de cotización de taller
                            $c_detalle->setIdVentaEquipo($mapEquipo[$idCotiEqFila]);
                            $c_detalle->setIdCotizacionEquipo($idCotiEqFila);
                        } else if (isset($productoEquipoMap[$idProducto])) {
                            // Caso: Viene de guía, usar mapeo por producto
                            $serieEquipo = $productoEquipoMap[$idProducto];
                            if (isset($mapEquipo[$serieEquipo])) {
                                $c_detalle->setIdVentaEquipo($mapEquipo[$serieEquipo]);
                                $c_detalle->setIdCotizacionEquipo(null);
                                error_log("Producto $idProducto asociado a equipo con serie $serieEquipo (ID: {$mapEquipo[$serieEquipo]})");
                            } else {
                                $c_detalle->setIdVentaEquipo(null);
                            }
                        } else {
                            $c_detalle->setIdVentaEquipo(null);
                            if ($idCotiEqFila) { $c_detalle->setIdCotizacionEquipo($idCotiEqFila); }
                        }

                        if ($c_detalle->insertar()) {
                            $dataSaveLog .= "Prod: " . $c_detalle->getSql() . " - true";
                        } else {
                            $dataSaveLog .= "Prod: " . $c_detalle->getSql() . " - false \n";
                            $dataSaveLog .= $c_detalle->getSqlError() . "\n\n\n";
                            error_log("Error insert productos_ventas: " . $c_detalle->getSqlError());
                        }
                        if ($c_detalle->getSqlError() == null || $c_detalle->getSqlError() === '') { $insertados++; }

                        $dataSend['productos'][] = [
                            "precio" => $_POST['moneda'] == '1' ? $precioVenta : number_format($precioVenta / $tc, 2, '.', ''),
                            "cantidad" => isset($fila['cantidad']) ? $fila['cantidad'] : 0,
                            "cod_pro" => isset($fila['productoid']) ? $fila['productoid'] : 0,
                            "cod_sunat" => "",
                            "descripcion" => isset($fila['descripcion']) ? $fila['descripcion'] : ''
                        ];
                    }
                    error_log("guardarVentas productos insertados=" . $insertados);
                }

                // Guardar log de la venta
                $logDir = "files/log/ventas/";
                if (!is_dir($logDir)) {
                    mkdir($logDir, 0777, true);
                }
                file_put_contents($logDir . "Venta_" . $c_venta->getIdVenta() . "_" . $dataSend['serie'] . '-' . $dataSend['numero'] . '.txt', $dataSaveLog);

                if ($tipoventa == 2) {
                    $nroitem = 1;
                    $c_servicio->setIdventa($c_venta->getIdVenta());
                    foreach ($array_detalle as $fila) {
                        $c_servicio->setDescripcion($fila['descripcion']);
                        $c_servicio->setCantidad($fila['cantidad']);
                        $c_servicio->setMonto($fila['precioVenta']);
                        $c_servicio->setCodsunat(isset($fila['codsunat']) ? $fila['codsunat'] : '');
                        $c_servicio->setIditem($nroitem);
                        $c_servicio->insertar();
                        $nroitem++;
                        $dataSend['productos'][] = [
                            "precio" => $fila['precioVenta'],
                            "cantidad" => $fila['cantidad'],
                            "cod_pro" => $nroitem,
                            "cod_sunat" => isset($fila['codsunat']) ? $fila['codsunat'] : '',
                            "descripcion" => $fila['descripcion']
                        ];
                    }
                }

                // Definir url según el tipo de documento sunat
                if ($c_venta->getIdTido() == 1) {
                    $archivo = "boleta";
                }
                if ($c_venta->getIdTido() == 2) {
                    $archivo = "factura";
                }

                $nom_xmlFac = '-';

                if ($c_venta->getIdTido() == 1 || $c_venta->getIdTido() == 2) {
                    $dataSend["endpoints"] = $respEmpre['modo'];

                    if ($_SESSION['sucursal'] != '1') {
                        $datoSucursal = $this->conexion->query("SELECT * FROM sucursales WHERE cod_sucursal ='{$_SESSION['sucursal']}' AND empresa_id=" . $_SESSION['id_empresa'])->fetch_assoc();
                        $dataSend['empresa'] = json_encode([
                            'ruc' => $respEmpre['ruc'],
                            'razon_social' => $respEmpre['razon_social'],
                            'direccion' => $datoSucursal['direccion'],
                            'ubigeo' => $datoSucursal['ubigeo'],
                            'distrito' => $datoSucursal['distrito'],
                            'provincia' => $datoSucursal['provincia'],
                            'departamento' => $datoSucursal['departamento'],
                            'clave_sol' => $respEmpre['clave_sol'],
                            'usuario_sol' => $respEmpre['user_sol']
                        ]);
                    } else {
                        $dataSend['empresa'] = json_encode([
                            'ruc' => $respEmpre['ruc'],
                            'razon_social' => $respEmpre['razon_social'],
                            'direccion' => $respEmpre['direccion'],
                            'ubigeo' => $respEmpre['ubigeo'],
                            'distrito' => $respEmpre['distrito'],
                            'provincia' => $respEmpre['provincia'],
                            'departamento' => $respEmpre['departamento'],
                            'clave_sol' => $respEmpre['clave_sol'],
                            'usuario_sol' => $respEmpre['user_sol']
                        ]);
                    }

                    $dataSend['productos'] = json_encode($dataSend['productos']);

                    if ($c_venta->getIdTido() == 1) {
                        $dataResp = $this->sunatApi->genBoletaXML($dataSend);
                    } else {
                        $dataResp = $this->sunatApi->genFacturaXML($dataSend);
                    }

                    if (isset($dataResp["res"]) && $dataResp["res"]) {
                        $c_sunat->setIdVenta($c_venta->getIdVenta());
                        $c_sunat->setHash($dataResp['data']['hash']);
                        $c_sunat->setNombreXml($dataResp['data']['nombre_archivo']);
                        $c_sunat->setQrData($dataResp['data']['qr']);
                        $c_sunat->insertar();

                        $nom_xmlFac = $dataResp['data']['nombre_archivo'];
                    }
                } else {
                    $c_sunat->setIdVenta($c_venta->getIdVenta());
                    $c_sunat->setHash("-");
                    $c_sunat->setNombreXml("-");
                    $c_sunat->setQrData('-');
                    $c_sunat->insertar();

                    $resultado["valor"] = $c_venta->getIdVenta();
                }

                $resultado["nomxml"] = $nom_xmlFac;
                $resultado["venta"] = $c_venta->getIdVenta();
                $resultado["nomFact"] = $c_sunat->getNombreXml() . ".pdf";
                $resultado["urlFact"] = URL::to('/venta/comprobante/pdf/' . $c_sunat->getIdVenta() . '/' . $c_sunat->getNombreXml());
                $resultado["urlFactd"] = URL::to('/venta/comprobante/pdfd/' . $c_sunat->getIdVenta() . '/' . $c_sunat->getNombreXml());
            } else {
                // Si hubo un error en la inserción, incluir información de depuración
                $resultado["error_info"] = [
                    "sql" => $c_venta->getSql(),
                    "sql_error" => $c_venta->getSqlError()
                ];
                error_log("Error al insertar venta: " . $c_venta->getSqlError());
            }

            return json_encode($resultado);

        } catch (Exception $e) {
            error_log("Excepción en guardarVentas: " . $e->getMessage());
            return json_encode([
                "res" => false,
                "error" => true,
                "mensaje" => $e->getMessage(),
                "debug_info" => [
                    "file" => $e->getFile(),
                    "line" => $e->getLine(),
                    "trace" => $e->getTraceAsString()
                ]
            ]);
        }
    }
   public function obtenerInfoCotizacionTaller()
{
    try {
        if (!isset($_POST['coti'])) {
            throw new Exception("ID de cotización no proporcionado");
        }

        $id_cotizacion = intval($_POST['coti']);
        error_log("Obteniendo info cotización taller ID: " . $id_cotizacion);

        // Obtener datos principales de la cotización
        $sql = "SELECT 
            tc.*,
            c.documento as num_doc,
            c.datos as nom_cli,
            c.direccion as dir_cli,
            c.direccion2 as dir2_cli
            FROM taller_cotizaciones tc
            INNER JOIN clientes c ON tc.id_cliente = c.id_cliente
            WHERE tc.id_cotizacion = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id_cotizacion);
        $stmt->execute();
        $result = $stmt->get_result();
        $cotizacion = $result->fetch_assoc();

        if (!$cotizacion) {
            throw new Exception("Cotización no encontrada");
        }

        // CORREGIDO: Obtener productos/repuestos con la consulta correcta
        $sqlItems = "SELECT 
            trc.*,
            CASE 
                WHEN trc.tipo_item = 'producto' THEN p.codigo
                WHEN trc.tipo_item = 'repuesto' THEN r.codigo
                ELSE 'Sin código'
            END as codigo_prod,
            CASE 
                WHEN trc.tipo_item = 'producto' THEN p.nombre
                WHEN trc.tipo_item = 'repuesto' THEN r.nombre
                ELSE 'Sin nombre'
            END as descripcion,
            CASE 
                WHEN trc.tipo_item = 'producto' THEN p.precio
                WHEN trc.tipo_item = 'repuesto' THEN r.precio
                ELSE 0
            END as precio_base,
            CASE 
                WHEN trc.tipo_item = 'producto' THEN p.precio2
                WHEN trc.tipo_item = 'repuesto' THEN r.precio2
                ELSE 0
            END as precio2,
            CASE 
                WHEN trc.tipo_item = 'producto' THEN p.precio_unidad
                WHEN trc.tipo_item = 'repuesto' THEN r.precio_unidad
                ELSE 0
            END as precio_unidad,
            tce.marca as equipo_marca,
            tce.equipo as equipo_nombre,
            tce.modelo as equipo_modelo,
            tce.numero_serie as equipo_serie
            FROM taller_repuestos_cotis trc
            LEFT JOIN repuestos r ON trc.id_repuesto = r.id_repuesto AND trc.tipo_item = 'repuesto'
            LEFT JOIN productos p ON trc.id_producto = p.id_producto AND trc.tipo_item = 'producto'
            LEFT JOIN taller_cotizaciones_equipos tce ON trc.id_cotizacion_equipo = tce.id_cotizacion_equipo
            WHERE trc.id_coti = ?
            ORDER BY trc.id_cotizacion_equipo, trc.id_repuesto_coti";

        $stmtItems = $this->conexion->prepare($sqlItems);
        $stmtItems->bind_param("i", $id_cotizacion);
        $stmtItems->execute();
        $resultItems = $stmtItems->get_result();
        
        // Organizar productos por equipo
        $productosPorEquipo = [];
        while ($item = $resultItems->fetch_assoc()) {
            $equipoId = $item['id_cotizacion_equipo'];
            
            if (!isset($productosPorEquipo[$equipoId])) {
                $productosPorEquipo[$equipoId] = [];
            }
            
            $productosPorEquipo[$equipoId][] = [
                'productoid' => $item['tipo_item'] === 'producto' ? $item['id_producto'] : $item['id_repuesto'],
                'codigo' => $item['codigo_prod'],
                'descripcion' => $item['descripcion'],
                'cantidad' => $item['cantidad'],
                'precioVenta' => $item['precio'],
                'costo' => $item['costo'],
                'precio' => $item['precio_base'],
                'precio2' => $item['precio2'],
                'precio_unidad' => $item['precio_unidad'],
                'edicion' => false,
                'tipo_item' => $item['tipo_item'],
                'id_cotizacion_equipo' => $equipoId,
                'equipo_info' => [
                    'marca' => $item['equipo_marca'],
                    'equipo' => $item['equipo_nombre'],
                    'modelo' => $item['equipo_modelo'],
                    'numero_serie' => $item['equipo_serie']
                ]
            ];
        }

        // Obtener equipos en orden
        $sqlEquipos = "SELECT * FROM taller_cotizaciones_equipos WHERE id_cotizacion = ? ORDER BY id_cotizacion_equipo";
        $stmtEquipos = $this->conexion->prepare($sqlEquipos);
        $stmtEquipos->bind_param("i", $id_cotizacion);
        $stmtEquipos->execute();
        $resultEquipos = $stmtEquipos->get_result();

        $equipos = [];
        $todosLosProductos = []; // Para mantener compatibilidad con el frontend actual
        
        while ($equipo = $resultEquipos->fetch_assoc()) {
            $equipoData = [
                'id_cotizacion_equipo' => $equipo['id_cotizacion_equipo'],
                'marca' => $equipo['marca'],
                'equipo' => $equipo['equipo'],
                'modelo' => $equipo['modelo'],
                'numero_serie' => $equipo['numero_serie'],
                'productos' => $productosPorEquipo[$equipo['id_cotizacion_equipo']] ?? []
            ];
            
            $equipos[] = $equipoData;
            
            // Agregar productos de este equipo al array general (para compatibilidad)
            if (isset($productosPorEquipo[$equipo['id_cotizacion_equipo']])) {
                $todosLosProductos = array_merge($todosLosProductos, $productosPorEquipo[$equipo['id_cotizacion_equipo']]);
            }
        }

        // Obtener cuotas si existen
        $sqlCuotas = "SELECT * FROM cuotas_cotizacion WHERE id_coti = ? ORDER BY fecha";
        $stmtCuotas = $this->conexion->prepare($sqlCuotas);
        $stmtCuotas->bind_param("i", $id_cotizacion);
        $stmtCuotas->execute();
        $resultCuotas = $stmtCuotas->get_result();
        
        $cuotas = [];
        while ($cuota = $resultCuotas->fetch_assoc()) {
            $cuotas[] = [
                'fecha' => $cuota['fecha'],
                'monto' => $cuota['monto']
            ];
        }

        $response = [
            'res' => true,
            'productos' => $todosLosProductos, // Mantener para compatibilidad
            'equipos' => $equipos, // Equipos con sus productos organizados
            'productos_por_equipo' => $productosPorEquipo, // Productos organizados por equipo
            'cliente_doc' => $cotizacion['num_doc'],
            'cliente_nom' => $cotizacion['nom_cli'],
            'cliente_dir1' => $cotizacion['dir_cli'],
            'cliente_dir2' => $cotizacion['dir2_cli'],
            'id_tido' => $cotizacion['id_tido'],
            'moneda' => $cotizacion['moneda'],
            'cm_tc' => $cotizacion['cm_tc'],
            'id_tipo_pago' => $cotizacion['id_tipo_pago'],
            'dias_pagos' => $cotizacion['dias_pagos'],
            'direccion' => $cotizacion['direccion'],
            'usar_precio' => $cotizacion['usar_precio'],
            'cuotas' => $cuotas
        ];

        error_log("Respuesta cotización taller: " . json_encode($response));
        echo json_encode($response);

    } catch (Exception $e) {
        error_log("Error en obtenerInfoCotizacionTaller: " . $e->getMessage());
        echo json_encode([
            'res' => false,
            'error' => $e->getMessage()
        ]);
    }
}

}
