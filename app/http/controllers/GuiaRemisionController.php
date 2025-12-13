<?php
require_once "app/models/GuiaRemision.php";
require_once "app/models/GuiaDetalle.php";
require_once "app/models/DocumentoEmpresa.php";
require_once "app/models/GuiaSunat.php";
require_once "app/clases/SendURL.php";
require_once "app/clases/SunatApi.php";
require_once "app/clases/SunatApi2.php";
require_once 'app/models/TallerRepuesto.php';
require_once 'app/models/TallerEquipo.php';
require_once 'app/models/TallerCotizacion.php';
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

        // ✅ AGREGAR ESTA LÍNEA CRÍTICA - Establecer el ID de cotización
        $tipo_cotizacion = isset($_POST['tipo_cotizacion']) ? $_POST['tipo_cotizacion'] : '';
        if ($id_cotizacion) {
            if ($tipo_cotizacion === 'taller') {
                $c_guia->setIdCotizacionTaller($id_cotizacion);
            } else {
                $c_guia->setIdCotizacion($id_cotizacion);
            }
        }

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

        // ✅ CORREGIDO: Lógica para guías desde facturas/boletas/cotizaciones
        $tipo_doc = filter_input(INPUT_POST, 'tipo_doc');
        if ($tipo_doc === '3') { // Orden de Compra
            $c_guia->setRefOrdenCompra(filter_input(INPUT_POST, 'doc_referencia'));
            $c_guia->setDocReferencia(''); // Limpiar doc_referencia
        } else {
            $c_guia->setDocReferencia(filter_input(INPUT_POST, 'doc_referencia'));
            $c_guia->setRefOrdenCompra(''); // Limpiar ref_orden_compra
        }

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

                // Establecer descripción - PRIORIDAD CORREGIDA
                $descripcion = '';
                if (isset($prodG['nombre']) && !empty($prodG['nombre'])) {
                    $descripcion = $prodG['nombre'];                     // ✅ PRIORIDAD 1: Nombre del producto
                } elseif (isset($prodG['descripcion']) && !empty($prodG['descripcion'])) {
                    $descripcion = $prodG['descripcion'];               // ✅ PRIORIDAD 2: Para taller  
                } elseif (isset($prodG['detalle']) && !empty($prodG['detalle'])) {
                    $descripcion = $prodG['detalle'];                   // ✅ PRIORIDAD 3: Descripción técnica
                }

                if (empty($descripcion)) {
                    continue;
                }
                
                // ✅ LIMPIAR cualquier carácter especial restante antes de guardar en BD
                $descripcion = preg_replace('/[^\x20-\x7E\xA1-\xFF]/', '', $descripcion); // Solo caracteres ASCII y latin1
                $descripcion = trim($descripcion); // Quitar espacios extra
                
                $guiaDetalle->setDetalles($descripcion);

                // ✅ NUEVO: Establecer ID según tipo de item (producto o repuesto)
                $tipoItem = isset($prodG['tipo_item']) ? $prodG['tipo_item'] : 'producto';
                $guiaDetalle->setTipoItem($tipoItem);
                
                if ($tipoItem === 'repuesto') {
                    // Es repuesto - usar id_repuesto
                    $idRepuesto = 0;
                    if (isset($prodG['id_producto']) && !empty($prodG['id_producto'])) {
                        $idRepuesto = $prodG['id_producto']; // En taller viene como id_producto pero es id_repuesto
                    } elseif (isset($prodG['idproducto']) && !empty($prodG['idproducto'])) {
                        $idRepuesto = $prodG['idproducto'];
                    } elseif (isset($prodG['productoid']) && !empty($prodG['productoid'])) {
                        $idRepuesto = $prodG['productoid'];
                    }
                    $guiaDetalle->setIdRepuesto($idRepuesto);
                    $guiaDetalle->setIdProducto(null); // Limpiar id_producto
                } else {
                    // Es producto - usar id_producto
                    $idProducto = 0;
                    if (isset($prodG['id_producto']) && !empty($prodG['id_producto'])) {
                        $idProducto = $prodG['id_producto'];
                    } elseif (isset($prodG['idproducto']) && !empty($prodG['idproducto'])) {
                        $idProducto = $prodG['idproducto'];
                    } elseif (isset($prodG['productoid']) && !empty($prodG['productoid'])) {
                        $idProducto = $prodG['productoid'];
                    }
                    $guiaDetalle->setIdProducto($idProducto);
                    $guiaDetalle->setIdRepuesto(null); // Limpiar id_repuesto
                }

                // Establecer precio
                $precio = 0;
                if (isset($prodG['precio']) && !empty($prodG['precio'])) {
                    $precio = $prodG['precio'];
                }
                $guiaDetalle->setPrecio($precio);

                // Establecer unidad - CORREGIDO para obtener NOMBRE desde tabla unidades
                $nombreUnidad = "NIU"; // valor por defecto
                if ($tipoItem === 'repuesto') {
                    // Para repuestos: obtener nombre de unidad desde JOIN con tabla unidades
                    if (isset($idRepuesto) && $idRepuesto > 0) {
                        $sqlUnidad = "SELECT u.nombre FROM repuestos r 
                                     LEFT JOIN unidades u ON r.unidad = u.id 
                                     WHERE r.id_repuesto = " . intval($idRepuesto);
                        $resultUnidad = $this->conexion->query($sqlUnidad);
                        if ($resultUnidad && $resultUnidad->num_rows > 0) {
                            $rowUnidad = $resultUnidad->fetch_assoc();
                            $nombreUnidad = $rowUnidad['nombre'] ?: 'NIU';
                        }
                    }
                } else {
                    // Para productos: obtener nombre de unidad desde JOIN con tabla unidades
                    if (isset($idProducto) && $idProducto > 0) {
                        $sqlUnidad = "SELECT u.nombre FROM productos p 
                                     LEFT JOIN unidades u ON p.unidad = u.id 
                                     WHERE p.id_producto = " . intval($idProducto);
                        $resultUnidad = $this->conexion->query($sqlUnidad);
                        if ($resultUnidad && $resultUnidad->num_rows > 0) {
                            $rowUnidad = $resultUnidad->fetch_assoc();
                            $nombreUnidad = $rowUnidad['nombre'] ?: 'NIU';
                        }
                    }
                }
                $guiaDetalle->setUnidad($nombreUnidad);

                // Intentar insertar con manejo de errores
                try {
                    $insertResult = $guiaDetalle->insertar();

                    if ($insertResult) {
                        $productos_insertados++;
                    } else {
                    }
                } catch (Exception $e) {
                }

                // ✅ NUEVO: Agregar a dataSend para SUNAT usando el ID correcto según tipo
                $codPro = ($tipoItem === 'repuesto') ? 
                    (isset($idRepuesto) ? $idRepuesto : 0) : 
                    (isset($idProducto) ? $idProducto : 0);
                    
                $dataSend['productos'][] = [
                    'cantidad' => $prodG['cantidad'],
                    'cod_pro' => $codPro,
                    'cod_sunat' => "000",
                    'descripcion' => $descripcion
                ];
            }

            // NUEVO: Guardar equipos y actualizar guia_numero si viene de cotización de taller
            if ($tipo_cotizacion === 'taller' && $id_cotizacion) {
                // Primero guardar equipos y obtener el mapeo de IDs
                $equipos_map = $this->guardarEquiposDeTallerEnGuiaConMapeo($c_guia->getIdGuia(), $id_cotizacion);
                
                // Actualizar los productos para relacionarlos con sus equipos
                $this->actualizarProductosConEquipos($c_guia->getIdGuia(), $equipos_map, $listaProd);
                
                // Actualizar guia_numero en taller_cotizaciones
                $guia_numero = $c_guia->getSerie() . '-' . $c_guia->getNumero();
                $sql_update_taller = "UPDATE taller_cotizaciones SET guia_numero = '{$guia_numero}' WHERE id_cotizacion = '{$id_cotizacion}'";
                $this->conexion->query($sql_update_taller);
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
        }
        return json_encode($resultado);
    }

    public function obtenerInfoGuia()
    {
        try {
            $guiaId = $_POST['guia'];
            $conexion = (new Conexion())->getConexion();

            // Get guide and client information - MEJORADA para incluir cotización
            $query = "SELECT
            gr.id_guia_remision,
            gr.fecha_emision,
            gr.dir_llegada as cliente_direccion,
            COALESCE(c_venta.documento, c_coti.documento, c_taller.documento, gr.destinatario_documento) as cliente_doc,
            COALESCE(c_venta.datos, c_coti.datos, c_taller.datos, gr.destinatario_nombre) as cliente_nombre,
            gr.serie,
            gr.numero,
            gr.estado,
            gr.ref_orden_compra
            FROM guia_remision gr
            LEFT JOIN ventas v ON gr.id_venta = v.id_venta
            LEFT JOIN clientes c_venta ON v.id_cliente = c_venta.id_cliente
            LEFT JOIN cotizaciones cot ON gr.id_cotizacion = cot.cotizacion_id
            LEFT JOIN clientes c_coti ON cot.id_cliente = c_coti.id_cliente
            LEFT JOIN taller_cotizaciones tc ON gr.id_cotizacion_taller = tc.id_cotizacion
            LEFT JOIN clientes c_taller ON tc.id_cliente = c_taller.id_cliente
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

            // Get products from guia_detalles with equipment info
            $queryProductos = "SELECT 
                gd.id_producto,
                gd.detalles as descripcion,
                gd.cantidad,
                gd.precio as precioVenta,
                gd.unidad,
                gd.id_guia_equipo,
                -- Equipment info if exists
                ge.marca as equipo_marca,
                ge.equipo as equipo_nombre,
                ge.modelo as equipo_modelo,
                ge.numero_serie as equipo_serie
                FROM guia_detalles gd 
                LEFT JOIN guia_equipos ge ON gd.id_guia_equipo = ge.id_guia_equipo
                WHERE gd.id_guia = ?
                ORDER BY ge.id_guia_equipo ASC, gd.guia_detalle_id ASC";

            $stmt = $conexion->prepare($queryProductos);
            $stmt->bind_param("i", $guiaId);
            $stmt->execute();
            $productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

            // Transform products with equipment grouping
            $productosFormateados = [];
            $equiposYaAgregados = [];
            
            foreach ($productos as $prod) {
                // Check if this product belongs to an equipment
                if ($prod['equipo_marca'] && $prod['id_guia_equipo']) {
                    $equipoId = $prod['id_guia_equipo'];
                    
                    // Add equipment header if not added yet
                    if (!in_array($equipoId, $equiposYaAgregados)) {
                        $equipoInfo = "EQUIPO: {$prod['equipo_marca']} {$prod['equipo_nombre']} - Modelo: {$prod['equipo_modelo']} - Serie: {$prod['equipo_serie']}";
                        
                        $productosFormateados[] = [
                            'cantidad' => '',
                            'descripcion' => $equipoInfo,
                            'precioVenta' => '',
                            'precio' => '', // Para compatibilidad con la vista  
                            'edicion' => false,
                            'productoid' => '',
                            'esEquipo' => true
                        ];
                        
                        $equiposYaAgregados[] = $equipoId;
                    }
                    
                    // Add product under equipment (indented)
                    $descripcionProducto = "    " . ($prod['descripcion'] ?: 'Producto sin descripción');
                } else {
                    // Regular product (no equipment)
                    $descripcionProducto = $prod['descripcion'] ?: 'Producto sin descripción';
                }
                
                // Add the product
                $productosFormateados[] = [
                    'cantidad' => (int) $prod['cantidad'],
                    'descripcion' => $descripcionProducto,
                    'nombre' => $descripcionProducto, // Para compatibilidad con la vista
                    'precioVenta' => number_format((float) $prod['precioVenta'], 2, '.', ''),
                    'precio' => number_format((float) $prod['precioVenta'], 2, '.', ''),
                    'edicion' => false,
                    'productoid' => $prod['id_producto'],
                    'esEquipo' => false
                ];
            }

            // DEBUG: Log para verificar datos
            error_log("DEBUG - Productos encontrados: " . count($productos));
            if (!empty($productos)) {
                error_log("DEBUG - Primer producto: " . json_encode($productos[0]));
                error_log("DEBUG - Productos formateados: " . json_encode(array_slice($productosFormateados, 0, 2)));
            }

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
                'ref_orden_compra' => $guia['ref_orden_compra'],
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

        // Comentamos las líneas originales y agregamos verificaciones
        if (isset($datosGuiaRemosion['fecha_emision'])) {
            $c_guia->setFecha($datosGuiaRemosion['fecha_emision']);
        } else {
            $c_guia->setFecha(date('Y-m-d')); // Valor por defecto
        }

        if (isset($result['id_venta'])) {
            $c_guia->setIdVenta($result['id_venta']);
        } else {
        }

        if (isset($datosGuiaRemosion['dir_cli'])) {
            $c_guia->setDirLlegada($datosGuiaRemosion['dir_cli']);
        } else {
            $c_guia->setDirLlegada('-'); // Valor por defecto
        }

        if (isset($data['datosUbigeoGuiaRemosion'])) {
            $c_guia->setUbigeo($data['datosUbigeoGuiaRemosion']);
        } else {
            $c_guia->setUbigeo('150101'); // Valor por defecto (Lima)
        }

        if (isset($datosTransporteGuiaRemosion['tipo_trans'])) {
            $c_guia->setTipoTransporte($datosTransporteGuiaRemosion['tipo_trans']);
        } else {
            $c_guia->setTipoTransporte('01'); // Valor por defecto
        }

        if (isset($datosTransporteGuiaRemosion['ruc'])) {
            $c_guia->setRucTransporte($datosTransporteGuiaRemosion['ruc']);
        } else {
            $c_guia->setRucTransporte('-'); // Valor por defecto
        }

        if (isset($datosTransporteGuiaRemosion['razon_social'])) {
            $c_guia->setRazTransporte($datosTransporteGuiaRemosion['razon_social']);
        } else {
            $c_guia->setRazTransporte('-'); // Valor por defecto
        }

        if (isset($datosTransporteGuiaRemosion['veiculo'])) {
            $c_guia->setVehiculo($datosTransporteGuiaRemosion['veiculo']);
        } else {
            $c_guia->setVehiculo('-'); // Valor por defecto
        }

        if (isset($datosTransporteGuiaRemosion['chofer_dni'])) {
            $c_guia->setChofer($datosTransporteGuiaRemosion['chofer_dni']);
        } else {
            $c_guia->setChofer('-'); // Valor por defecto
        }

        if (isset($datosGuiaRemosion['peso'])) {
            $c_guia->setPeso($datosGuiaRemosion['peso']);
        } else {
            $c_guia->setPeso('1.000'); // Valor por defecto
        }

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

        $resultado = ["res" => false];
        if ($c_guia->insertar()) {
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
                        
                        // ✅ LIMPIAR caracteres UTF-8 problemáticos antes de guardar en BD
                        $descripcion = $prodG['descripcion'];
                        $descripcion = preg_replace('/[^\x20-\x7E\xA1-\xFF]/', '', $descripcion); // Solo caracteres ASCII y latin1
                        $descripcion = trim($descripcion); // Quitar espacios extra
                        
                        $guiaDetalle->setDetalles($descripcion);
                        $guiaDetalle->setIdProducto($prodG['productoid']);
                        $guiaDetalle->setPrecio(isset($prodG['precio']) ? $prodG['precio'] : 0);
                        // Establecer unidad - CORREGIDO para obtener NOMBRE desde tabla unidades
                        $nombreUnidad = "NIU"; // valor por defecto
                        if (isset($prodG['productoid']) && $prodG['productoid'] > 0) {
                            $sqlUnidad = "SELECT u.nombre FROM productos p 
                                         LEFT JOIN unidades u ON p.unidad = u.id 
                                         WHERE p.id_producto = " . intval($prodG['productoid']);
                            $resultUnidad = $this->conexion->query($sqlUnidad);
                            if ($resultUnidad && $resultUnidad->num_rows > 0) {
                                $rowUnidad = $resultUnidad->fetch_assoc();
                                $nombreUnidad = $rowUnidad['nombre'] ?: 'NIU';
                            }
                        }
                        $guiaDetalle->setUnidad($nombreUnidad);
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

        // ✅ CORREGIDO: Lógica específica para guías manuales
        $tipo_doc = filter_input(INPUT_POST, 'tipo_doc');
        if ($tipo_doc === '3') { // Orden de Compra
            $c_guia->setRefOrdenCompra(filter_input(INPUT_POST, 'ref_orden_compra'));
            $c_guia->setDocReferencia(''); // Limpiar doc_referencia
        } else {
            $c_guia->setDocReferencia(filter_input(INPUT_POST, 'doc_referencia'));
            $c_guia->setRefOrdenCompra(''); // Limpiar ref_orden_compra
        }

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
                    
                    // ✅ LIMPIAR cualquier carácter especial restante antes de guardar en BD
                    $descripcion = preg_replace('/[^\x20-\x7E\xA1-\xFF]/', '', $descripcion); // Solo caracteres ASCII y latin1
                    $descripcion = trim($descripcion); // Quitar espacios extra
                    
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

            // ✅ CORREGIDO: Para duplicar guías, mantener ambos campos como vienen
            $c_guia->setDocReferencia(filter_input(INPUT_POST, 'doc_referencia'));
            $c_guia->setRefOrdenCompra(filter_input(INPUT_POST, 'ref_orden_compra'));

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

            // ✅ NUEVO: Duplicar equipos de la guía original ANTES de procesar productos
            $id_guia_original = filter_input(INPUT_POST, 'id_guia_remision') ?: filter_input(INPUT_POST, 'id_guia_original');
            $equipos_map = $this->duplicarEquiposDeGuia($c_guia->getIdGuia(), $id_guia_original);

            // Procesar productos
            if (isset($_POST['productos']) && !empty($_POST['productos'])) {
                $listaProd = json_decode($_POST['productos'], true);

                if (is_array($listaProd) && count($listaProd) > 0) {
                    foreach ($listaProd as $prodG) {
                        $guiaDetalle = new GuiaDetalle();
                        $guiaDetalle->setIdGuia($c_guia->getIdGuia());
                        $guiaDetalle->setCantidad($prodG['cantidad']);
                        
                        // ✅ LIMPIAR caracteres UTF-8 problemáticos antes de guardar en BD
                        $descripcion = $prodG['descripcion'];
                        $descripcion = preg_replace('/[^\x20-\x7E\xA1-\xFF]/', '', $descripcion); // Solo caracteres ASCII y latin1
                        $descripcion = trim($descripcion); // Quitar espacios extra
                        
                        $guiaDetalle->setDetalles($descripcion);
                        $guiaDetalle->setIdProducto($prodG['productoid']);
                        $guiaDetalle->setPrecio($prodG['precio']);
                        // Establecer unidad - CORREGIDO para obtener NOMBRE desde tabla unidades
                        $nombreUnidad = "NIU"; // valor por defecto
                        if (isset($prodG['productoid']) && $prodG['productoid'] > 0) {
                            $sqlUnidad = "SELECT u.nombre FROM productos p 
                                         LEFT JOIN unidades u ON p.unidad = u.id 
                                         WHERE p.id_producto = " . intval($prodG['productoid']);
                            $resultUnidad = $this->conexion->query($sqlUnidad);
                            if ($resultUnidad && $resultUnidad->num_rows > 0) {
                                $rowUnidad = $resultUnidad->fetch_assoc();
                                $nombreUnidad = $rowUnidad['nombre'] ?: 'NIU';
                            }
                        }
                        $guiaDetalle->setUnidad($nombreUnidad);
                        
                        // ✅ NUEVO: Relacionar producto con equipo si existe
                        if (isset($prodG['id_guia_equipo']) && !empty($prodG['id_guia_equipo'])) {
                            $id_equipo_original = $prodG['id_guia_equipo'];
                            if (isset($equipos_map[$id_equipo_original])) {
                                $guiaDetalle->setIdGuiaEquipo($equipos_map[$id_equipo_original]);
                            }
                        }
                        
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

            // ✅ CONSULTA COMPLETA: Incluir todos los tipos de relación (ventas, cotizaciones, taller)
            $query = "SELECT 
            gr.*,
            -- Obtener datos del cliente desde cualquier tipo de relación
            COALESCE(
                c_venta.documento,      -- De ventas
                c_coti.documento,       -- De cotizaciones
                c_taller.documento,     -- De taller_cotizaciones  
                gr.destinatario_documento  -- Manual
            ) as doc_cli,
            COALESCE(
                c_venta.datos,          -- De ventas
                c_coti.datos,           -- De cotizaciones
                c_taller.datos,         -- De taller_cotizaciones
                gr.destinatario_nombre     -- Manual
            ) as nom_cli,
            COALESCE(ds.nombre, 'GUIA DE REMISION') as tipo_documento,
            -- Datos del chofer desde configuraciones
            gcc.chofer_id,
            gcc.chofer_nombre,
            gcc.chofer_dni,
            gcc.vehiculo_placa,
            gcc.vehiculo_marca,
            gcc.licencia_numero
            FROM guia_remision gr
            -- JOIN para ventas
            LEFT JOIN ventas v ON gr.id_venta = v.id_venta
            LEFT JOIN clientes c_venta ON v.id_cliente = c_venta.id_cliente
            -- JOIN para cotizaciones normales  
            LEFT JOIN cotizaciones coti ON gr.id_cotizacion = coti.cotizacion_id
            LEFT JOIN clientes c_coti ON coti.id_cliente = c_coti.id_cliente
            -- JOIN para cotizaciones de taller
            LEFT JOIN taller_cotizaciones tc ON gr.id_cotizacion_taller = tc.id_cotizacion
            LEFT JOIN clientes c_taller ON tc.id_cliente = c_taller.id_cliente
            -- Otros JOINs
            LEFT JOIN documentos_sunat ds ON COALESCE(v.id_tido, coti.id_tido, tc.id_tido) = ds.id_tido
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

            // ✅ CONSULTA CORREGIDA: Productos con equipos usando columnas reales
            $queryProductos = "
            SELECT 
                gd.*,
                p.nombre,
                p.codigo as codigo_pp,
                ge.id_guia_equipo,
                ge.equipo,
                ge.modelo,
                ge.numero_serie,
                ge.marca
            FROM guia_detalles gd 
            LEFT JOIN productos p ON gd.id_producto = p.id_producto
            LEFT JOIN guia_equipos ge ON gd.id_guia_equipo = ge.id_guia_equipo
            WHERE gd.id_guia = " . intval($idGuia) . "
            ORDER BY ge.id_guia_equipo, gd.guia_detalle_id";

            $result = $this->conexion->query($queryProductos);
            if (!$result) {
                throw new Exception("Error en la consulta de productos: " . $this->conexion->error);
            }

            $productos = [];
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }

            // ✅ FORMATEO MEJORADO: Agrupar por equipos y formatear precios
            $productosFormateados = [];
            $equiposAgregados = [];

            foreach ($productos as $prod) {
                // Si tiene equipo asociado y no lo hemos agregado
                if (!empty($prod['id_guia_equipo']) && !in_array($prod['id_guia_equipo'], $equiposAgregados)) {
                    // Agregar fila del equipo con formato mejorado
                    $nombreEquipo = trim(($prod['marca'] ?? '') . ' ' . ($prod['equipo'] ?? ''));
                    $detalleCompleto = "EQUIPO: " . $nombreEquipo . 
                                      " - Modelo: " . ($prod['modelo'] ?? '') . 
                                      " - Serie: " . ($prod['numero_serie'] ?? '');
                    
                    $productosFormateados[] = [
                        'esEquipo' => true,
                        'id_guia_equipo' => $prod['id_guia_equipo'],
                        'nombre' => $prod['equipo'] ?? '',
                        'descripcion' => $prod['modelo'] ?? '',
                        'serie' => $prod['numero_serie'] ?? '',
                        'marca' => $prod['marca'] ?? '',
                        'cantidad' => '',
                        'precio' => '',
                        'codigo_pp' => '',
                        'detalle' => $detalleCompleto,
                        'nombreCompleto' => $nombreEquipo
                    ];
                    $equiposAgregados[] = $prod['id_guia_equipo'];
                }

                // Agregar el producto - con indentación si tiene equipo asociado
                $descripcionProducto = !empty($prod['id_guia_equipo']) 
                    ? "  " . ($prod['detalles'] ?? '') 
                    : ($prod['detalles'] ?? '');
                
                $productosFormateados[] = [
                    'esEquipo' => false,
                    'productoid' => $prod['id_producto'],
                    'id_guia_equipo' => $prod['id_guia_equipo'],
                    'nombre' => $prod['nombre'] ?? $prod['detalles'],
                    'descripcion' => $prod['detalles'],
                    'cantidad' => $prod['cantidad'],
                    'precio' => number_format((float) $prod['precio'], 5, '.', ''),
                    'precioVenta' => number_format((float) $prod['precio'], 5, '.', ''),
                    'codigo_pp' => $prod['codigo_pp'] ?? '',
                    'detalle' => $descripcionProducto,
                    'detalleOriginal' => $prod['detalles']
                ];
            }

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
                    'ref_orden_compra' => $guia['ref_orden_compra'], // ✅ NUEVO CAMPO
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
    function consultarGuiaXCoti()
    {
        $sql = "SELECT * FROM productos_cotis WHERE id_coti = '{$_POST['cod']}'";
        $lista = [];
        foreach ($this->conexion->query($sql) as $row) {
            $sql = "SELECT * FROM productos WHERE id_producto = '{$row['id_producto']}'";

            foreach ($this->conexion->query($sql) as $row2) {
                // Convertir la cantidad a entero si no tiene decimales
                $cantidad = floatval($row['cantidad']);
                $cantidadFormateada = $cantidad == floor($cantidad) ? number_format($cantidad, 0) : $cantidad;

                $lista[] = [
                    'cantidad' => $cantidadFormateada,
                    'costo' => $row['costo'],
                    'id_producto' => $row['id_producto'],
                    'precio' => $row['precio'],
                    'nombre' => $row2['nombre'],
                    'codigo' => $row2['codigo'],
                    'detalle' => $row2['detalle']
                ];
            }
        }
        echo json_encode($lista);
    }

    function consultarGuiaXCotiCliente()
    {
        if (!isset($_POST['cod']) || empty($_POST['cod'])) {
            return json_encode([
                'error' => true,
                'mensaje' => 'No se proporcionó un código de cotización válido'
            ]);
        }

        // Consulta SQL modificada para manejar ubigeo nulo
        $sql = "SELECT 
                    c.datos, 
                    c.direccion, 
                    c.documento, 
                    COALESCE(SUBSTRING(c.ubigeo, 1, 2), '') as departamento,
                    COALESCE(SUBSTRING(c.ubigeo, 3, 2), '') as provincia,
                    COALESCE(SUBSTRING(c.ubigeo, 5, 2), '') as distrito,
                    COALESCE(c.ubigeo, '') as ubigeo 
                FROM cotizaciones co 
                JOIN clientes c ON co.id_cliente = c.id_cliente 
                WHERE co.cotizacion_id = ?";

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            return json_encode([
                'error' => true,
                'mensaje' => 'Error al preparar la consulta: ' . $this->conexion->error
            ]);
        }

        $stmt->bind_param('s', $_POST['cod']);
        if (!$stmt->execute()) {
            return json_encode([
                'error' => true,
                'mensaje' => 'Error al ejecutar la consulta: ' . $stmt->error
            ]);
        }

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            return json_encode($data);
        } else {
            return json_encode([
                'error' => true,
                'mensaje' => 'No se encontraron datos para esta cotización'
            ]);
        }
    }
    function consultarGuiaXCotiTaller($id)
    {
        try {
            $id_cotizacion = intval($id);

            // <CHANGE> Usar los modelos existentes en lugar de consultas SQL directas
            $tallerRepuesto = new TallerRepuesto();
            $tallerEquipo = new TallerEquipo();

            // Obtener productos/repuestos usando el modelo
            $productosData = $tallerRepuesto->obtenerPorCotizacion($id_cotizacion);

            $productos = [];
            foreach ($productosData as $row) {
                // Determinar el ID del producto según el tipo
                $productoid = ($row['tipo_item'] === 'producto') ? $row['id_producto'] : $row['id_repuesto'];

                // Convertir la cantidad a entero si no tiene decimales
                $cantidad = floatval($row['cantidad']);
                $cantidadFormateada = $cantidad == floor($cantidad) ? number_format($cantidad, 0) : $cantidad;

                $productos[] = [
                    'cantidad' => $cantidadFormateada,
                    'costo' => $row['costo'],
                    'id_producto' => $productoid,
                    'precio' => $row['precio'],
                    'nombre' => $row['descripcion'],
                    'codigo' => $row['codigo_prod'],
                    'detalle' => $row['descripcion'],
                    'tipo_item' => $row['tipo_item'],
                    'id_cotizacion_equipo' => $row['id_cotizacion_equipo']
                ];
            }

            // <CHANGE> Obtener equipos usando el modelo (tabla correcta: taller_cotizaciones_equipos)
            $equiposData = $tallerEquipo->obtenerPorCotizacion($id_cotizacion);

            $equipos = [];
            foreach ($equiposData as $row) {
                $equipos[] = [
                    'id_cotizacion_equipo' => $row['id_cotizacion_equipo'],
                    'marca' => $row['marca'],
                    'equipo' => $row['equipo'],
                    'modelo' => $row['modelo'],
                    'numero_serie' => $row['numero_serie']
                ];
            }

            // Respuesta completa similar a ventas normales
            $response = [
                'res' => true,
                'productos' => $productos,
                'equipos' => $equipos
            ];

            echo json_encode($response);

        } catch (Exception $e) {
            echo json_encode(['error' => 'Error al obtener datos: ' . $e->getMessage()]);
        }
    }
  function consultarGuiaXCotiTallerCliente($id)
{
    try {
        $id_cotizacion = intval($id);
        
        if (empty($id_cotizacion)) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'No se proporcionó un código de cotización válido'
            ]);
            return;
        }

        // Consulta actualizada para usar tabla clientes unificada
        $sql = "SELECT 
                    c.datos, 
                    c.direccion, 
                    c.documento,
                    c.direccion2 as atencion,
                    c.telefono,
                    c.email
                FROM taller_cotizaciones tc 
                JOIN clientes c ON tc.id_cliente = c.id_cliente 
                WHERE tc.id_cotizacion = ?";

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'Error al preparar la consulta: ' . $this->conexion->error
            ]);
            return;
        }

        $stmt->bind_param('i', $id_cotizacion);
        if (!$stmt->execute()) {
            echo json_encode([
                'error' => true,
                'mensaje' => 'Error al ejecutar la consulta: ' . $stmt->error
            ]);
            return;
        }

        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $data = $result->fetch_assoc();
            
            // Respuesta usando tabla clientes unificada
            $response = [
                'datos' => $data['datos'],
                'direccion' => $data['direccion'], 
                'documento' => $data['documento'],
                'atencion' => $data['atencion'] ?? '',
                'telefono' => $data['telefono'] ?? '',
                'email' => $data['email'] ?? '',
                // Para mantener compatibilidad con el frontend
                'ubigeo' => '',
                'departamento' => '',
                'provincia' => '',
                'distrito' => ''
            ];
            
            echo json_encode($response);
        } else {
            echo json_encode([
                'error' => true,
                'mensaje' => 'No se encontraron datos para esta cotización de taller'
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'error' => true, 
            'mensaje' => 'Error al obtener cliente: ' . $e->getMessage()
        ]);
    }
}

    /**
     * Guardar equipos de cotización de taller en la guía de remisión
     */
    private function guardarEquiposDeTallerEnGuia($id_guia, $id_cotizacion_taller)
    {
        try {
            // Obtener equipos de la cotización de taller
            $sql = "SELECT id_cotizacion_equipo, marca, equipo, modelo, numero_serie 
                    FROM taller_cotizaciones_equipos 
                    WHERE id_cotizacion = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion_taller);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Insertar cada equipo en la tabla guia_equipos
            while ($equipo = $result->fetch_assoc()) {
                $sqlInsert = "INSERT INTO guia_equipos (id_guia, id_cotizacion_equipo, marca, equipo, modelo, numero_serie) 
                              VALUES (?, ?, ?, ?, ?, ?)";
                
                $stmtInsert = $this->conexion->prepare($sqlInsert);
                $stmtInsert->bind_param("iissss", 
                    $id_guia, 
                    $equipo['id_cotizacion_equipo'],
                    $equipo['marca'], 
                    $equipo['equipo'], 
                    $equipo['modelo'], 
                    $equipo['numero_serie']
                );
                
                $stmtInsert->execute();
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error guardando equipos en guía: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Guardar equipos y retornar mapeo de IDs para relación con productos
     */
    private function guardarEquiposDeTallerEnGuiaConMapeo($id_guia, $id_cotizacion_taller)
    {
        try {
            $equipos_map = [];
            
            // Obtener equipos de la cotización de taller
            $sql = "SELECT id_cotizacion_equipo, marca, equipo, modelo, numero_serie 
                    FROM taller_cotizaciones_equipos 
                    WHERE id_cotizacion = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id_cotizacion_taller);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Insertar cada equipo y mapear IDs
            while ($equipo = $result->fetch_assoc()) {
                $sqlInsert = "INSERT INTO guia_equipos (id_guia, id_cotizacion_equipo, marca, equipo, modelo, numero_serie) 
                              VALUES (?, ?, ?, ?, ?, ?)";
                
                $stmtInsert = $this->conexion->prepare($sqlInsert);
                $stmtInsert->bind_param("iissss", 
                    $id_guia, 
                    $equipo['id_cotizacion_equipo'],
                    $equipo['marca'], 
                    $equipo['equipo'], 
                    $equipo['modelo'], 
                    $equipo['numero_serie']
                );
                
                $stmtInsert->execute();
                
                // Mapear: id_cotizacion_equipo => id_guia_equipo
                $equipos_map[$equipo['id_cotizacion_equipo']] = $this->conexion->insert_id;
            }
            
            return $equipos_map;
        } catch (Exception $e) {
            error_log("Error guardando equipos en guía con mapeo: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Actualizar productos para relacionarlos con sus equipos correspondientes
     */
    private function actualizarProductosConEquipos($id_guia, $equipos_map, $productos_data)
    {
        try {
            
            // Actualizar cada producto con su equipo correspondiente
            foreach ($productos_data as $index => $prodG) {
                // Validar que el producto tenga id_cotizacion_equipo
                if (!isset($prodG['id_cotizacion_equipo']) || empty($prodG['id_cotizacion_equipo'])) {
                    continue;
                }
                
                $id_cotizacion_equipo = $prodG['id_cotizacion_equipo'];
                
                // Buscar el id_guia_equipo correspondiente en el mapeo
                if (!isset($equipos_map[$id_cotizacion_equipo])) {
                    continue;
                }
                
                $id_guia_equipo = $equipos_map[$id_cotizacion_equipo];
                
                // Identificar el producto específico para actualizar
                // Usaremos id_producto o id_repuesto según el tipo
                $identificador_producto = null;
                $campo_identificador = null;
                
                // Verificar tipo_item con fallback seguro
                $tipo_item = $prodG['tipo_item'] ?? 'producto';
                error_log("Tipo item detectado: " . $tipo_item);
                
                if ($tipo_item === 'repuesto' && isset($prodG['id_producto'])) {
                    // Para repuestos, el ID viene en id_producto pero debe ir a id_repuesto
                    $identificador_producto = $prodG['id_producto'];
                    $campo_identificador = 'id_repuesto';
                } else {
                    // Para productos normales
                    $identificador_producto = $prodG['id_producto'] ?? $prodG['idproducto'] ?? $prodG['productoid'] ?? null;
                    $campo_identificador = 'id_producto';
                }
                
                if (!$identificador_producto) {
                    continue;
                }
                
                // Actualizar el detalle específico usando el identificador del producto
                $sqlUpdate = "UPDATE guia_detalles 
                            SET id_guia_equipo = ? 
                            WHERE id_guia = ? AND $campo_identificador = ? AND id_guia_equipo IS NULL";
                
                $stmtUpdate = $this->conexion->prepare($sqlUpdate);
                $stmtUpdate->bind_param("iii", $id_guia_equipo, $id_guia, $identificador_producto);
                
                if ($stmtUpdate->execute()) {
                    $affected_rows = $stmtUpdate->affected_rows;
                    
                    // Si no se afectó ninguna fila, intentar sin la condición IS NULL
                    if ($affected_rows === 0) {
                        $sqlUpdate2 = "UPDATE guia_detalles 
                                     SET id_guia_equipo = ? 
                                     WHERE id_guia = ? AND $campo_identificador = ?";
                        
                        $stmtUpdate2 = $this->conexion->prepare($sqlUpdate2);
                        $stmtUpdate2->bind_param("iii", $id_guia_equipo, $id_guia, $identificador_producto);
                        $stmtUpdate2->execute();
                    }
                }
            }
            
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * ✅ NUEVO: Duplicar equipos de una guía original a una nueva guía
     */
    private function duplicarEquiposDeGuia($id_nueva_guia, $id_guia_original)
    {
        try {
            $equipos_map = [];
            
            if (empty($id_guia_original)) {
                return $equipos_map; // No hay guía original, devolver array vacío
            }
            
            // Obtener equipos de la guía original
            $sql = "SELECT id_guia_equipo, id_cotizacion_equipo, marca, equipo, modelo, numero_serie 
                    FROM guia_equipos 
                    WHERE id_guia = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param("i", $id_guia_original);
            $stmt->execute();
            $result = $stmt->get_result();
            
            // Insertar cada equipo en la nueva guía y mapear IDs
            while ($equipo = $result->fetch_assoc()) {
                $sqlInsert = "INSERT INTO guia_equipos (id_guia, id_cotizacion_equipo, marca, equipo, modelo, numero_serie) 
                              VALUES (?, ?, ?, ?, ?, ?)";
                
                $stmtInsert = $this->conexion->prepare($sqlInsert);
                $stmtInsert->bind_param("iissss", 
                    $id_nueva_guia, 
                    $equipo['id_cotizacion_equipo'],
                    $equipo['marca'], 
                    $equipo['equipo'], 
                    $equipo['modelo'], 
                    $equipo['numero_serie']
                );
                
                $stmtInsert->execute();
                
                // Mapear: id_guia_equipo_original => id_guia_equipo_nuevo
                $equipos_map[$equipo['id_guia_equipo']] = $this->conexion->insert_id;
            }
            
            return $equipos_map;
        } catch (Exception $e) {
            error_log("Error duplicando equipos: " . $e->getMessage());
            return [];
        }
    }

}