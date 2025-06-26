<?php
require_once "app/models/GuiaRemision.php";
require_once "app/models/GuiaDetalle.php";
require_once "app/models/DocumentoEmpresa.php";
require_once "app/models/GuiaSunat.php";
require_once "app/clases/SendURL.php";
require_once "app/clases/SunatApi.php";
require_once "app/clases/SunatApi2.php";

class GuiaRemisionController extends Controller
{
    private $sunatApi;
    private $sunatApi2;
    private $conexion;
    public function __construct()
    {
        $this->sunatApi2 = new SunatApi2();
        $this->sunatApi = new SunatApi();
        $this->conexion = (new Conexion())->getConexion();
    }

    public function enviarDocumentoSunat()
    {
        $conexion = (new Conexion())->getConexion();
        $sql = "select * from guia_sunat where id_guia = '{$_POST['cod']}'";
        $dataGuia = $conexion->query($sql)->fetch_assoc();
        $resultado = ["res" => false];
        if ($this->sunatApi2->envioIndividualGuiaRemi($dataGuia['nombre_xml'])) {
            $sql = "update guia_remision set  enviado_sunat='1' where id_guia_remision= '{$_POST["cod"]}'";
            $conexion->query($sql);
            $resultado['res'] = true;
        } else {
            //echo "Error1";
            $resultado['msg'] = $this->sunatApi2->getMensaje();
        }
        return json_encode($resultado);
    }

  public function insertar()
{
    $c_guia = new GuiaRemision();
    $c_documentos = new DocumentoEmpresa();
    $guiaSunat = new GuiaSunat();
    $sendURL = new SendURL();

    $dataSend = [];
    $dataSend["certGlobal"] = false;

    // Capturar ID de cotización si existe
    $id_cotizacion = isset($_POST['cotizacion']) && !empty($_POST['cotizacion']) ?
        filter_input(INPUT_POST, 'cotizacion') : null;

  
    // Configurar datos de la guía
    $c_guia->setFecha(filter_input(INPUT_POST, 'fecha_emision'));
    $c_guia->setIdVenta(filter_input(INPUT_POST, 'venta'));
    
   
    
    $c_guia->setDirLlegada(filter_input(INPUT_POST, 'dir_cli'));
    $c_guia->setUbigeo(filter_input(INPUT_POST, 'ubigeo'));
    $c_guia->setTipoTransporte(filter_input(INPUT_POST, 'tipo_trans'));
    $c_guia->setRucTransporte(filter_input(INPUT_POST, 'ruc'));
    $c_guia->setRazTransporte(filter_input(INPUT_POST, 'razon_social'));
    $c_guia->setVehiculo(filter_input(INPUT_POST, 'veiculo'));
    $c_guia->setChofer(filter_input(INPUT_POST, 'chofer_dni'));
    
    
    $c_guia->setMotivoTraslado(filter_input(INPUT_POST, 'motivo'));
    $c_guia->setChoferDatos(filter_input(INPUT_POST, 'chofer_datos'));
    $c_guia->setObservaciones(filter_input(INPUT_POST, 'observacion'));
    $c_guia->setDocReferencia(filter_input(INPUT_POST, 'doc_referencia'));
    $c_guia->setDirPartida(filter_input(INPUT_POST, 'dir_part'));
    $c_guia->setPeso(filter_input(INPUT_POST, 'peso'));
    $c_guia->setNroBultos(filter_input(INPUT_POST, 'num_bultos'));
    $c_guia->setIdEmpresa($_SESSION['id_empresa']);

    // Configurar documentos
    $c_documentos->setIdTido(11);
    $c_documentos->setIdEmpresa($c_guia->getIdEmpresa());
    $c_documentos->obtenerDatos();

    $c_guia->setSerie($c_documentos->getSerie());
    $c_guia->setNumero($c_documentos->getNumero());

    // Preparar datos para SUNAT
    $dataSend['peso'] = $c_guia->getPeso();
    $dataSend['ubigeo'] = $c_guia->getUbigeo();
    $dataSend['direccion'] = $c_guia->getDirLlegada();
    $dataSend['serie'] = $c_guia->getSerie();
    $dataSend['numero'] = $c_guia->getNumero();
    $dataSend['fecha'] = $c_guia->getFecha();

    $resultado = ["res" => false];
    
   
    // Insertar guía principal
    if ($c_guia->insertar()) {
        $resultado["res"] = true;
        $resultado["guia"] = $c_guia->getIdGuia();
        
       // Actualizar estado de cotización si existe
    if ($id_cotizacion) {
        $sql = "UPDATE cotizaciones SET estado = '1' WHERE cotizacion_id = '{$id_cotizacion}'";
        $this->conexion->query($sql);
    }
        
        // Verificar si hay productos
        if (!isset($_POST['productos']) || empty($_POST['productos'])) {
            return json_encode($resultado);
        }
        
        // Decodificar productos
        $listaProd = json_decode($_POST['productos'], true);
        
        // Verificar decodificación JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            return json_encode($resultado);
        }
        
        // Verificar que hay productos
        if (!is_array($listaProd) || count($listaProd) == 0) {
            return json_encode($resultado);
        }
        
        
        
        $dataSend['productos'] = [];
        $productos_insertados = 0;
        
        // Procesar cada producto - CREAR NUEVA INSTANCIA PARA CADA UNO
        foreach ($listaProd as $index => $prodG) {
            
            
            // IMPORTANTE: Crear nueva instancia para cada producto
            $guiaDetalle = new GuiaDetalle();
            $guiaDetalle->setIdGuia($c_guia->getIdGuia());
            
            // Validar y establecer cantidad
            if (!isset($prodG['cantidad']) || empty($prodG['cantidad'])) {
                continue;
            }
            $guiaDetalle->setCantidad($prodG['cantidad']);
            
            // Establecer descripción
            $descripcion = '';
            if (isset($prodG['descripcion']) && !empty($prodG['descripcion'])) {
                $descripcion = $prodG['descripcion'];
            } elseif (isset($prodG['detalle']) && !empty($prodG['detalle'])) {
                $descripcion = $prodG['detalle'];
            } elseif (isset($prodG['nombre']) && !empty($prodG['nombre'])) {
                $descripcion = $prodG['nombre'];
            }
            
            if (empty($descripcion)) {
                continue;
            }
            $guiaDetalle->setDetalles($descripcion);
            
            // Establecer ID de producto
            $idProducto = 0;
            if (isset($prodG['idproducto']) && !empty($prodG['idproducto'])) {
                $idProducto = $prodG['idproducto'];
            } elseif (isset($prodG['productoid']) && !empty($prodG['productoid'])) {
                $idProducto = $prodG['productoid'];
            }
            $guiaDetalle->setIdProducto($idProducto);
            
            // Establecer precio
            $precio = 0;
            if (isset($prodG['precio']) && !empty($prodG['precio'])) {
                $precio = $prodG['precio'];
            }
            $guiaDetalle->setPrecio($precio);
            
            // Establecer unidad
            $guiaDetalle->setUnidad("NIU");
            
            // Intentar insertar con manejo de errores
            try {
               
                
                $insertResult = $guiaDetalle->insertar();
                
                if ($insertResult) {
                    $productos_insertados++;
                } else {
                }
            } catch (Exception $e) {
            }
            
            // Agregar a dataSend para SUNAT
            $dataSend['productos'][] = [
                'cantidad' => $prodG['cantidad'],
                'cod_pro' => $idProducto,
                'cod_sunat' => "000",
                'descripcion' => $descripcion
            ];
        }
        
        
        
        // Continuar con el resto del proceso
        $dataSend['productos'] = json_encode($dataSend['productos']);

        $sql = "SELECT * from empresas where id_empresa = " . $_SESSION['id_empresa'];
        $respEmpre = $c_guia->exeSQL($sql)->fetch_assoc();

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

        $dataSend['venta'] = json_encode([
            'serie' => filter_input(INPUT_POST, 'serie'),
            'numero' => filter_input(INPUT_POST, 'numero')
        ]);
        
        $dataSend['cliente'] = json_encode([
            'doc_num' => filter_input(INPUT_POST, 'doc_cli'),
            'nom_RS' => filter_input(INPUT_POST, 'nom_cli')
        ]);
        
        $dataSend['transporte'] = json_encode([
            'ruc' => filter_input(INPUT_POST, 'ruc'),
            'razon_social' => filter_input(INPUT_POST, 'razon_social'),
            'placa' => filter_input(INPUT_POST, 'veiculo'),
            'doc_chofer' => filter_input(INPUT_POST, 'chofer_dni')
        ]);

        $dataResp = $this->sunatApi->genGuiaRemision($dataSend);

        if (isset($dataResp["res"]) && $dataResp["res"]) {
            $guiaSunat = new GuiaSunat();
            $guiaSunat->setIdGuia($c_guia->getIdGuia());
            $guiaSunat->setHash($dataResp["data"]['hash']);
            $guiaSunat->setNombreXml($dataResp["data"]['nombre_archivo']);
            $guiaSunat->setQrData($dataResp["data"]['qr']);
            $guiaSunat->insertar();
        }
    } else {
    }
    return json_encode($resultado);
}
    public function obtenerInfoGuia()
    {
        try {
            $guiaId = $_POST['guia'];
            $conexion = (new Conexion())->getConexion();

            

            // Get guide and client information
            $query = "SELECT 
            gr.id_guia_remision,
            gr.fecha_emision,
            gr.dir_llegada as cliente_direccion,
            COALESCE(c.documento, gr.destinatario_documento) as cliente_doc,
            COALESCE(c.datos, gr.destinatario_nombre) as cliente_nombre,
            gr.serie,
            gr.numero,
            gr.estado
            FROM guia_remision gr
            LEFT JOIN ventas v ON gr.id_venta = v.id_venta
            LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
            WHERE gr.id_guia_remision = ?";

            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $guiaId);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $guia = $resultado->fetch_assoc();

            if (!$guia) {
                echo json_encode([
                    'res' => false,
                    'error' => 'Guía no encontrada',
                    'debug' => 'Query executed: ' . $query . ' with ID: ' . $guiaId
                ]);
                return;
            }

            // Get products from guia_detalles
            $queryProductos = "SELECT 
                gd.id_producto,
                gd.detalles as descripcion,
                gd.cantidad,
                gd.precio as precioVenta,
                gd.unidad
                FROM guia_detalles gd 
                WHERE gd.id_guia = ?";

            $stmt = $conexion->prepare($queryProductos);
            $stmt->bind_param("i", $guiaId);
            $stmt->execute();
            $productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            

            // Transform products to match invoice format
            $productosFormateados = array_map(function ($prod) {
                return [
                    'cantidad' => (int) $prod['cantidad'],
                    'descripcion' => $prod['descripcion'] ?: 'Producto sin descripción',
                    'precioVenta' => number_format((float) $prod['precioVenta'], 2, '.', ''),
                    'edicion' => false,
                    'productoid' => $prod['id_producto']
                ];
            }, $productos);

            $response = [
                'res' => true,
                'guia' => [
                    'id_guia_remision' => $guia['id_guia_remision'],
                    'fecha_emision' => $guia['fecha_emision'],
                    'serie' => $guia['serie'],
                    'numero' => $guia['numero'],
                    'estado' => $guia['estado']
                ],
                'cliente_doc' => $guia['cliente_doc'],
                'cliente_nombre' => $guia['cliente_nombre'],
                'cliente_direccion' => $guia['cliente_direccion'],
                'productos' => $productosFormateados
            ];

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode([
                'res' => false,
                'error' => 'Error al procesar la guía: ' . $e->getMessage()
            ]);
        }
    }
    public function insertar2()
    {
        $c_guia = new GuiaRemision();
        $c_documentos = new DocumentoEmpresa();
        $guiaDetalle = new GuiaDetalle();
        $guiaSunat = new GuiaSunat();
        $sendURL = new SendURL();

        $dataSend = [];
        $dataSend["certGlobal"] = false;

        // Habilitar registro de errores para depuración
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);

        //$sendGuia = new SendCurlGuia();
        /* return json_encode($_POST['idVenta']);
        return; */
        $data = $_POST['data'];

        // Verificar y decodificar datosGuiaRemosion
        if (isset($data['datosGuiaRemosion']) && !empty($data['datosGuiaRemosion'])) {
            $datosGuiaRemosion = json_decode($data['datosGuiaRemosion'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $datosGuiaRemosion = [];
            }
        } else {
            $datosGuiaRemosion = [];
        }

        // Verificar y decodificar datosTransporteGuiaRemosion
        if (isset($data['datosTransporteGuiaRemosion']) && !empty($data['datosTransporteGuiaRemosion'])) {
            $datosTransporteGuiaRemosion = json_decode($data['datosTransporteGuiaRemosion'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $datosTransporteGuiaRemosion = [];
            }
        } else {
            $datosTransporteGuiaRemosion = [];
        }

        // Consultar datos de la venta
        $sql = "SELECT * FROM ventas WHERE id_venta = '{$_POST['data']['idVenta']}'";
        $result = $this->conexion->query($sql)->fetch_assoc();

        /*  return json_encode($result['id_venta']);
        return; */
        /*      return json_encode($data);
        return; */
        /*   $c_guia->setFecha(filter_input(INPUT_POST, 'fecha_emision')); 
        $c_guia->setIdVenta(filter_input(INPUT_POST, 'venta'));
           $c_guia->setDirLlegada(filter_input(INPUT_POST, 'dir_cli'));
             $c_guia->setUbigeo(filter_input(INPUT_POST, 'ubigeo'));
              $c_guia->setTipoTransporte(filter_input(INPUT_POST, 'tipo_trans'));
               $c_guia->setRucTransporte(filter_input(INPUT_POST, 'ruc'));
      */
        // Comentamos las líneas originales y agregamos verificaciones
        // $c_guia->setFecha($datosGuiaRemosion['fecha_emision']);
        if (isset($datosGuiaRemosion['fecha_emision'])) {
            $c_guia->setFecha($datosGuiaRemosion['fecha_emision']);
        } else {
            $c_guia->setFecha(date('Y-m-d')); // Valor por defecto
        }

        // $c_guia->setIdVenta($result['id_venta']);
        if (isset($result['id_venta'])) {
            $c_guia->setIdVenta($result['id_venta']);
        } else {
        }

        // $c_guia->setDirLlegada($datosGuiaRemosion['dir_cli']);
        if (isset($datosGuiaRemosion['dir_cli'])) {
            $c_guia->setDirLlegada($datosGuiaRemosion['dir_cli']);
        } else {
            $c_guia->setDirLlegada('-'); // Valor por defecto
        }

        // $c_guia->setUbigeo($data['datosUbigeoGuiaRemosion']);
        if (isset($data['datosUbigeoGuiaRemosion'])) {
            $c_guia->setUbigeo($data['datosUbigeoGuiaRemosion']);
        } else {
            $c_guia->setUbigeo('150101'); // Valor por defecto (Lima)
        }

        // $c_guia->setTipoTransporte($datosTransporteGuiaRemosion['tipo_trans']);
        if (isset($datosTransporteGuiaRemosion['tipo_trans'])) {
            $c_guia->setTipoTransporte($datosTransporteGuiaRemosion['tipo_trans']);
        } else {
            $c_guia->setTipoTransporte('01'); // Valor por defecto
        }

        // $c_guia->setRucTransporte($datosTransporteGuiaRemosion['ruc']);
        if (isset($datosTransporteGuiaRemosion['ruc'])) {
            $c_guia->setRucTransporte($datosTransporteGuiaRemosion['ruc']);
        } else {
            $c_guia->setRucTransporte('-'); // Valor por defecto
        }

        // $c_guia->setRazTransporte($datosTransporteGuiaRemosion['razon_social']);
        if (isset($datosTransporteGuiaRemosion['razon_social'])) {
            $c_guia->setRazTransporte($datosTransporteGuiaRemosion['razon_social']);
        } else {
            $c_guia->setRazTransporte('-'); // Valor por defecto
        }

        // $c_guia->setVehiculo($datosTransporteGuiaRemosion['veiculo']);
        if (isset($datosTransporteGuiaRemosion['veiculo'])) {
            $c_guia->setVehiculo($datosTransporteGuiaRemosion['veiculo']);
        } else {
            $c_guia->setVehiculo('-'); // Valor por defecto
        }

        // $c_guia->setChofer($datosTransporteGuiaRemosion['chofer_dni']);
        if (isset($datosTransporteGuiaRemosion['chofer_dni'])) {
            $c_guia->setChofer($datosTransporteGuiaRemosion['chofer_dni']);
        } else {
            $c_guia->setChofer('-'); // Valor por defecto
        }

        // $c_guia->setPeso($datosGuiaRemosion['peso']);
        if (isset($datosGuiaRemosion['peso'])) {
            $c_guia->setPeso($datosGuiaRemosion['peso']);
        } else {
            $c_guia->setPeso('1.000'); // Valor por defecto
        }

        // $c_guia->setNroBultos($datosGuiaRemosion['num_bultos']);
        if (isset($datosGuiaRemosion['num_bultos'])) {
            $c_guia->setNroBultos($datosGuiaRemosion['num_bultos']);
        } else {
            $c_guia->setNroBultos('1'); // Valor por defecto
        }

        $c_guia->setIdEmpresa($_SESSION['id_empresa']);

        $c_documentos->setIdTido(11);
        $c_documentos->setIdEmpresa($c_guia->getIdEmpresa());
        $c_documentos->obtenerDatos();

        $c_guia->setSerie($c_documentos->getSerie());
        $c_guia->setNumero($c_documentos->getNumero());

        $dataSend['peso'] = $c_guia->getPeso();
        $dataSend['ubigeo'] = $c_guia->getUbigeo();
        $dataSend['direccion'] = $c_guia->getDirLlegada();
        $dataSend['serie'] = $c_guia->getSerie();
        $dataSend['numero'] = $c_guia->getNumero();
        $dataSend['fecha'] = $c_guia->getFecha();

        // $c_guia->obtenerId();
        $resultado = ["res" => false];
        if ($c_guia->insertar()) {
            //echo "xsssss";

            $resultado["res"] = true;
            $resultado["guia"] = $c_guia->getIdGuia();

            // Verificar y decodificar listaPro
            if (isset($data['listaPro']) && !empty($data['listaPro'])) {
                $listaProd = json_decode($data['listaPro'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $listaProd = [];
                }
            } else {
                $listaProd = [];
            }

            $guiaDetalle->setIdGuia($c_guia->getIdGuia());

            $dataSend['productos'] = [];
            if (is_array($listaProd) && count($listaProd) > 0) {
                foreach ($listaProd as $prodG) {
                    // Verificar que los índices necesarios existan
                    if (isset($prodG['cantidad']) && isset($prodG['descripcion']) && isset($prodG['productoid'])) {
                        $guiaDetalle->setCantidad($prodG['cantidad']);
                        $guiaDetalle->setDetalles($prodG['descripcion']);
                        $guiaDetalle->setIdProducto($prodG['productoid']);
                        $guiaDetalle->setPrecio(isset($prodG['precio']) ? $prodG['precio'] : 0);
                        $guiaDetalle->setUnidad("NIU");
                        $guiaDetalle->insertar();

                        $dataSend['productos'][] = [
                            'cantidad' => $prodG['cantidad'],
                            'cod_pro' => $prodG['productoid'],
                            'cod_sunat' => "000",
                            'descripcion' => $prodG['descripcion']
                        ];
                    } else {
                    }
                }
            }

            $dataSend['productos'] = json_encode($dataSend['productos']);

            $sql = "SELECT * from empresas where id_empresa = " . $_SESSION['id_empresa'];
            $respEmpre = $c_guia->exeSQL($sql)->fetch_assoc();

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

            // Verificar que result tenga los índices necesarios
            $dataSend['venta'] = json_encode([
                'serie' => isset($result['serie']) ? $result['serie'] : '',
                'numero' => isset($result['numero']) ? $result['numero'] : ''
            ]);

            // Verificar que data tenga los índices necesarios
            $dataSend['cliente'] = json_encode([
                'doc_num' => isset($data['num_doc']) ? $data['num_doc'] : '',
                'nom_RS' => isset($data['nom_cli']) ? $data['nom_cli'] : ''
            ]);

            // Verificar que datosTransporteGuiaRemosion tenga los índices necesarios
            $dataSend['transporte'] = json_encode([
                'ruc' => isset($datosTransporteGuiaRemosion['ruc']) ? $datosTransporteGuiaRemosion['ruc'] : '',
                'razon_social' => isset($datosTransporteGuiaRemosion['razon_social']) ? $datosTransporteGuiaRemosion['razon_social'] : '',
                'placa' => isset($datosTransporteGuiaRemosion['veiculo']) ? $datosTransporteGuiaRemosion['veiculo'] : '',
                'doc_chofer' => isset($datosTransporteGuiaRemosion['chofer_dni']) ? $datosTransporteGuiaRemosion['chofer_dni'] : ''
            ]);

            $dataResp = $this->sunatApi->genGuiaRemision($dataSend);

            /*$respCURL =SendURL::SendGuiaRemision($dataSend);
            $respCURL = json_decode($respCURL,true);
            $dataResp= $respCURL["data"];
    
            $rutaFileXML="file/xml/".$respEmpre['ruc'];
            if (!file_exists($rutaFileXML)){
                mkdir($rutaFileXML, 0777, true);
            }
    
            $myfile = fopen($rutaFileXML.'/'.$dataResp['nombre_archivo'].".xml", "w");
            fwrite($myfile,$dataResp['consten_XML']);
            fclose($myfile);*/

            if (isset($dataResp["res"]) && $dataResp["res"]) {
                $guiaSunat->setIdGuia($c_guia->getIdGuia());
                $guiaSunat->setHash($dataResp["data"]['hash']);
                $guiaSunat->setNombreXml($dataResp["data"]['nombre_archivo']);
                $guiaSunat->setQrData($dataResp["data"]['qr']);
                $guiaSunat->insertar();
            }
        }
        return json_encode($resultado);
    }
    public function insertarManual()
    {
       
        $c_guia = new GuiaRemision();
        $c_documentos = new DocumentoEmpresa();
        $guiaDetalle = new GuiaDetalle();
        $guiaSunat = new GuiaSunat();
        $sendURL = new SendURL();

        $dataSend = [];
        $dataSend["certGlobal"] = false;

        // Configurar datos básicos
        $c_guia->setFecha(filter_input(INPUT_POST, 'fecha_emision'));
        $c_guia->setIdVenta(filter_input(INPUT_POST, 'venta'));

        // Nuevos campos
        $c_guia->setDestinatarioNombre(filter_input(INPUT_POST, 'nom_cli'));
        $c_guia->setDestinatarioDocumento(filter_input(INPUT_POST, 'doc_cli'));
        $c_guia->setDirPartida(filter_input(INPUT_POST, 'dir_part'));
        $c_guia->setMotivoTraslado(filter_input(INPUT_POST, 'motivo'));
        $c_guia->setDirLlegada(filter_input(INPUT_POST, 'dir_cli'));
        $c_guia->setUbigeo(filter_input(INPUT_POST, 'ubigeo'));
        $c_guia->setTipoTransporte(filter_input(INPUT_POST, 'tipo_trans'));
        $c_guia->setRucTransporte(filter_input(INPUT_POST, 'ruc'));
        $c_guia->setRazTransporte(filter_input(INPUT_POST, 'razon_social'));
        $c_guia->setVehiculo(filter_input(INPUT_POST, 'veiculo'));
        $c_guia->setChofer(filter_input(INPUT_POST, 'chofer_dni'));
        $c_guia->setChoferDatos(filter_input(INPUT_POST, 'chofer_datos'));
        $c_guia->setObservaciones(filter_input(INPUT_POST, 'observacion'));
        $c_guia->setDocReferencia(filter_input(INPUT_POST, 'doc_referencia'));
        $c_guia->setPeso(filter_input(INPUT_POST, 'peso'));
        $c_guia->setNroBultos(filter_input(INPUT_POST, 'num_bultos'));
        $c_guia->setIdEmpresa($_SESSION['id_empresa']);

        // Obtener serie y número
        $c_documentos->setIdTido(11);
        $c_documentos->setIdEmpresa($c_guia->getIdEmpresa());
        $c_documentos->obtenerDatos();

        $c_guia->setSerie($c_documentos->getSerie());
        $c_guia->setNumero($c_documentos->getNumero());

        // Preparar datos para envío
        $dataSend['peso'] = $c_guia->getPeso();
        $dataSend['ubigeo'] = $c_guia->getUbigeo();
        $dataSend['direccion'] = $c_guia->getDirLlegada();
        $dataSend['dir_partida'] = $c_guia->getDirPartida();
        $dataSend['serie'] = $c_guia->getSerie();
        $dataSend['numero'] = $c_guia->getNumero();
        $dataSend['fecha'] = $c_guia->getFecha();
        $dataSend['motivo'] = $c_guia->getMotivoTraslado();
        $dataSend['observaciones'] = $c_guia->getObservaciones();
        $dataSend['doc_referencia'] = $c_guia->getDocReferencia();

        $resultado = ["res" => false];

      

        if ($c_guia->insertar()) {
            $resultado["res"] = true;
            $resultado["guia"] = $c_guia->getIdGuia();

            // Procesar productos
            $listaProd = json_decode($_POST['productos'], true);
            $guiaDetalle->setIdGuia($c_guia->getIdGuia());

            $dataSend['productos'] = [];
            if (is_array($listaProd) && count($listaProd) > 0) {
                foreach ($listaProd as $index => $prodG) { // Añadimos $index para depuración
                
                    $guiaDetalle->setCantidad($prodG['cantidad']);
                    // $guiaDetalle->setDetalles($prodG['descripcion']);
                    // Extraer solo el nombre del producto, sin el código
$descripcion = $prodG['descripcion'];
if (strpos($descripcion, ' | ') !== false) {
    $partes = explode(' | ', $descripcion, 2);
    $descripcion = $partes[1]; // Solo la parte después del código
}
$guiaDetalle->setDetalles($descripcion);
                    $guiaDetalle->setIdProducto($prodG['idproducto']);
                    $guiaDetalle->setPrecio($prodG['precio']);
                   $nombreUnidad = "NIU"; //valor por defecto
                    // 1. Obtener el ID de la unidad del producto desde los datos recibidos del frontend
                    $unidadId = isset($prodG['unidad_id']) ? $prodG['unidad_id'] : null;

                    if ($unidadId) {
                        // 2. Consultar la tabla 'unidades' para obtener el nombre de la unidad
                        //    Usamos $this->conexion que ya está disponible en el controlador
                        $sqlUnidad = "SELECT nombre FROM unidades WHERE id = " . intval($unidadId);
                        $resultUnidad = $this->conexion->query($sqlUnidad);
                        if ($resultUnidad && $resultUnidad->num_rows > 0) {
                            $rowUnidad = $resultUnidad->fetch_assoc();
                            $nombreUnidad = $rowUnidad['nombre'];
                           
                        } 
                    } 
                    $guiaDetalle->setUnidad($nombreUnidad);
                    try {
                        if ($guiaDetalle->insertar()) {
                        }
                    } catch (Exception $e) {
                    }

                    $dataSend['productos'][] = [
                        'cantidad' => $prodG['cantidad'],
                        'cod_pro' => $prodG['idproducto'],
                        'cod_sunat' => "000",
                        'descripcion' => $prodG['descripcion']
                    ];
                }
            } 

            $dataSend['productos'] = json_encode($dataSend['productos']);

            // Obtener datos de la empresa
            $sql = "SELECT * from empresas where id_empresa = " . $_SESSION['id_empresa'];
            $respEmpre = $c_guia->exeSQL($sql)->fetch_assoc();

            $dataSend["endpoints"] = $respEmpre['modo'];

            // Preparar datos de empresa
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

            // Preparar datos de venta
            $dataSend['venta'] = json_encode([
                'serie' => filter_input(INPUT_POST, 'serie'),
                'numero' => filter_input(INPUT_POST, 'numero')
            ]);

            // Preparar datos de cliente
            $dataSend['cliente'] = json_encode([
                'doc_num' => filter_input(INPUT_POST, 'doc_cli'),
                'nom_RS' => filter_input(INPUT_POST, 'nom_cli')
            ]);

            // Preparar datos de transporte
            $dataSend['transporte'] = json_encode([
                'ruc' => filter_input(INPUT_POST, 'ruc'),
                'razon_social' => filter_input(INPUT_POST, 'razon_social'),
                'placa' => filter_input(INPUT_POST, 'veiculo'),
                'doc_chofer' => filter_input(INPUT_POST, 'chofer_dni'),
                'nombre_chofer' => filter_input(INPUT_POST, 'chofer_datos')
            ]);

            // Generar guía en SUNAT
            $dataResp = $this->sunatApi->genGuiaRemision($dataSend);

            if ($dataResp["res"]) {
                $guiaSunat->setIdGuia($c_guia->getIdGuia());
                $guiaSunat->setHash($dataResp["data"]['hash']);
                $guiaSunat->setNombreXml($dataResp["data"]['nombre_archivo']);
                $guiaSunat->setQrData($dataResp["data"]['qr']);
                $guiaSunat->insertar();
            }
        } 
       
        return json_encode($resultado);
    }

  public function duplicarGuiaRemision()
{
    try {
        // Usar el mismo método que insertarManual pero con datos de la guía original
        $c_guia = new GuiaRemision();
        $c_documentos = new DocumentoEmpresa();
        $guiaSunat = new GuiaSunat();

        $dataSend = [];
        $dataSend["certGlobal"] = false;

        // Configurar datos básicos desde POST
        $c_guia->setFecha(filter_input(INPUT_POST, 'fecha_emision'));
        $c_guia->setIdVenta(filter_input(INPUT_POST, 'venta'));

        // Campos principales
        $c_guia->setDestinatarioNombre(filter_input(INPUT_POST, 'nom_cli'));
        $c_guia->setDestinatarioDocumento(filter_input(INPUT_POST, 'doc_cli'));
        $c_guia->setDirPartida(filter_input(INPUT_POST, 'dir_part'));
        $c_guia->setMotivoTraslado(filter_input(INPUT_POST, 'motivo'));
        $c_guia->setDirLlegada(filter_input(INPUT_POST, 'dir_cli'));
        $c_guia->setUbigeo(filter_input(INPUT_POST, 'ubigeo'));
        $c_guia->setTipoTransporte(filter_input(INPUT_POST, 'tipo_trans'));
        $c_guia->setRucTransporte(filter_input(INPUT_POST, 'ruc'));
        $c_guia->setRazTransporte(filter_input(INPUT_POST, 'razon_social'));
        $c_guia->setVehiculo(filter_input(INPUT_POST, 'veiculo'));
        $c_guia->setChofer(filter_input(INPUT_POST, 'chofer_dni'));
        $c_guia->setChoferDatos(filter_input(INPUT_POST, 'chofer_datos'));
        $c_guia->setObservaciones(filter_input(INPUT_POST, 'observacion'));
        
        // ✅ IMPORTANTE: Guardar Doc. de Referencia
        $c_guia->setDocReferencia(filter_input(INPUT_POST, 'doc_referencia'));
        
        $c_guia->setPeso(filter_input(INPUT_POST, 'peso'));
        $c_guia->setNroBultos(filter_input(INPUT_POST, 'num_bultos'));
        $c_guia->setIdEmpresa($_SESSION['id_empresa']);

        // Obtener nueva serie y número
        $c_documentos->setIdTido(11);
        $c_documentos->setIdEmpresa($c_guia->getIdEmpresa());
        if (!$c_documentos->obtenerDatos()) {
            throw new Exception("Error al obtener serie y número");
        }

        $c_guia->setSerie($c_documentos->getSerie());
        $c_guia->setNumero($c_documentos->getNumero());

        // Insertar nueva guía
        if (!$c_guia->insertar()) {
            throw new Exception("Error al insertar la nueva guía");
        }

        // Procesar productos
        if (isset($_POST['productos']) && !empty($_POST['productos'])) {
            $listaProd = json_decode($_POST['productos'], true);
            
            if (is_array($listaProd) && count($listaProd) > 0) {
                foreach ($listaProd as $prodG) {
                    $guiaDetalle = new GuiaDetalle();
                    $guiaDetalle->setIdGuia($c_guia->getIdGuia());
                    $guiaDetalle->setCantidad($prodG['cantidad']);
                    $guiaDetalle->setDetalles($prodG['descripcion']);
                    $guiaDetalle->setIdProducto($prodG['productoid']);
                    $guiaDetalle->setPrecio($prodG['precio']);
                    $guiaDetalle->setUnidad("NIU");
                    $guiaDetalle->insertar();
                }
            }
        }

        return json_encode([
            'res' => true,
            'mensaje' => 'Guía de remisión duplicada con éxito',
            'nueva_guia_id' => $c_guia->getIdGuia()
        ]);

    } catch (Exception $e) {
        return json_encode([
            'res' => false,
            'error' => $e->getMessage()
        ]);
    }
}

   public function obtenerGuiaDuplicada()
{
    try {
        if (!isset($_POST['id_guia'])) {
            throw new Exception("ID de guía no proporcionado");
        }

        $idGuia = $_POST['id_guia'];

        // Consulta principal MEJORADA para incluir datos del chofer
        $query = "SELECT 
            gr.*,
            COALESCE(c.documento, gr.destinatario_documento) as doc_cli,
            COALESCE(c.datos, gr.destinatario_nombre) as nom_cli,
            COALESCE(ds.nombre, 'GUIA DE REMISION') as tipo_documento,
            -- Datos del chofer desde configuraciones
            gcc.chofer_id,
            gcc.chofer_nombre,
            gcc.chofer_dni,
            gcc.vehiculo_placa,
            gcc.vehiculo_marca,
            gcc.licencia_numero
            FROM guia_remision gr
            LEFT JOIN ventas v ON gr.id_venta = v.id_venta
            LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
            LEFT JOIN documentos_sunat ds ON v.id_tido = ds.id_tido
            -- NUEVA UNIÓN: Buscar configuración del chofer por vehículo y licencia
            LEFT JOIN guia_conductor_configuraciones gcc ON (
                gcc.vehiculo_placa = gr.vehiculo 
                AND gcc.licencia_numero = gr.chofer_brevete
                AND gcc.chofer_nombre = gr.chofer_datos
            )
            WHERE gr.id_guia_remision = " . intval($idGuia);

        $result = $this->conexion->query($query);
        if (!$result) {
            throw new Exception("Error en la consulta: " . $this->conexion->error);
        }

        $guia = $result->fetch_assoc();
        if (!$guia) {
            throw new Exception("Guía no encontrada");
        }

        // Consulta de productos (sin cambios)
        $queryProductos = "SELECT 
            gd.*,
            p.nombre,
            p.codigo as codigo_pp
            FROM guia_detalles gd 
            LEFT JOIN productos p ON gd.id_producto = p.id_producto
            WHERE gd.id_guia = " . intval($idGuia);

        $result = $this->conexion->query($queryProductos);
        if (!$result) {
            throw new Exception("Error en la consulta de productos: " . $this->conexion->error);
        }

        $productos = [];
        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }

        // Formatear productos
        $productosFormateados = array_map(function ($prod) {
            return [
                'productoid' => $prod['id_producto'],
                'nombre' => $prod['nombre'] ?? $prod['detalles'],
                'descripcion' => $prod['detalles'],
                'cantidad' => $prod['cantidad'],
                'precio' => $prod['precio'],
                'codigo_pp' => $prod['codigo_pp'] ?? '',
                'detalle' => $prod['detalles']
            ];
        }, $productos);

        // Información del transporte MEJORADA
        $transporte = [
            'tipo_trans' => $guia['tipo_transporte'] ?? '1',
            'ruc' => $guia['ruc_transporte'] ?? '',
            'razon_social' => $guia['razon_transporte'] ?? '',
            'veiculo' => $guia['vehiculo'] ?? '',
            'chofer_dni' => $guia['chofer_brevete'] ?? '',
            'chofer_datos' => $guia['chofer_datos'] ?? '',
            // NUEVOS CAMPOS desde configuraciones
            'chofer_id' => $guia['chofer_id'] ?? '',
            'chofer_nombre' => $guia['chofer_nombre'] ?? $guia['chofer_datos'],
            'vehiculo_marca' => $guia['vehiculo_marca'] ?? '',
            'licencia_numero' => $guia['licencia_numero'] ?? $guia['chofer_brevete']
        ];

        return json_encode([
            'res' => true,
            'guia' => [
                'fecha_emision' => $guia['fecha_emision'] ?? date('Y-m-d'),
                'serie_g' => $guia['serie'],
                'numero_g' => $guia['numero'],
                'doc_cli' => $guia['doc_cli'],
                'nom_cli' => $guia['nom_cli'],
                'dir_cli' => $guia['dir_llegada'],
                'dir_part' => $guia['dir_partida'],
                'observacion' => $guia['observaciones'],
                'doc_referencia' => $guia['doc_referencia'],
                'peso' => $guia['peso'],
                'num_bultos' => $guia['nro_bultos'],
                'motivo' => $guia['motivo_traslado'],
                'tipo_documento' => $guia['tipo_documento'],
                'ubigeo' => $guia['ubigeo'] // AGREGADO: para autocompletar ubigeo
            ],
            'transporte' => $transporte,
            'productos' => $productosFormateados
        ]);

    } catch (Exception $e) {
        return json_encode([
            'res' => false,
            'error' => $e->getMessage()
        ]);
    }
}

}
