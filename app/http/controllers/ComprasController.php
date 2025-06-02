<?php

require_once "app/models/Compra.php";
require_once "app/models/DocumentoEmpresa.php";

class ComprasController extends Controller
{
    private $conectar;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
    }

    public function guardarCompras()
    {
        /* $this->sunatApi = new SunatApi(); */
        $insert = false;

        $c_compra = new Compra();
        $c_tido = new DocumentoEmpresa();


        $idProveedor = '';
        if ($_POST['num_doc'] !== '') {
            $empresaExistente = $c_tido->consultarProveedor($_POST['num_doc']);
            if (!empty($empresaExistente)) {
                $idProveedor = $empresaExistente[0]['proveedor_id'];
            } else {
                $insert = $c_tido->insertarProveedor($_POST['num_doc'], $_POST['nom_cli']);
                $idProveedor = $insert;
            }
        }

        $id_tido = $_POST['tipo_doc'] !== '' ? $_POST['tipo_doc'] : 2;
        $tipo_pago = $_POST['tipo_pago'] !== '' ? $_POST['tipo_pago'] : '';
        $fecha = isset($_POST['fecha']) ? $_POST['fecha'] : '';
        $fechaVen = isset($_POST['fechaVen']) ? $_POST['fechaVen'] : '';
        $dir_cli = $_POST['dir_cli'] !== '' ? $_POST['dir_cli'] : '-';
        $serie = $_POST['serie'] !== '' ? $_POST['serie'] : '';
        $numero = $_POST['numero'] !== '' ? $_POST['numero'] : '';
        $total = $_POST['total'] !== 0 ? intval($_POST['total']) : 0;
        $moneda = $_POST['moneda'] !== '' ? $_POST['moneda'] : '';
        $tipoventa = $_POST['tipoventa'] !== '' ? $_POST['tipoventa'] : '';
$id_usuario = isset($_SESSION['usuario_fac']) ? $_SESSION['usuario_fac'] : 'NULL';


        if ($id_tido !== '' && $tipo_pago !== '' && $fecha !== '' && $fechaVen !== '' && $dir_cli !== '' && $serie !== '' && $numero !== '' && $total > 0 && $moneda !== '' && $idProveedor !== '') {
            $array_detalle = json_decode($_POST['listaPro'], true);
            $listaPagos = json_decode($_POST['dias_lista'], true);
           $insertarCompra = $c_compra->insertarCompra($id_tido, $tipo_pago, $idProveedor, $fecha, $fechaVen, $dir_cli, $serie, $numero, $total, $_SESSION['id_empresa'], $moneda, $id_usuario);

            if (is_int($insertarCompra)) {
                // Si hay observaciones temporales, guardarlas para esta compra
                if (isset($_SESSION['temp_observaciones'])) {
                    $observaciones = $this->conectar->real_escape_string($_SESSION['temp_observaciones']);
                    $sql = "INSERT INTO observaciones_compra (id_compra, observaciones) VALUES ('$insertarCompra', '$observaciones')";
                    $this->conectar->query($sql);

                    // Limpiar las observaciones temporales
                    unset($_SESSION['temp_observaciones']);
                }
                ///echo "ssssssss";
                $updateStock = false;
                foreach ($array_detalle as $row) {
                    $updateStock = $c_compra->updateStock($row['cantidad'], $row['productoid']);
                }
                if ($updateStock) {
                    if ($tipo_pago == 1) {
                        $insertCompra = false;
                        foreach ($array_detalle as $fila) {
                            $insertCompra = $c_compra->insertProductosCompras($fila['productoid'], $insertarCompra, $fila['cantidad'], $fila['precio']);
                        }
                        if ($insertCompra) {
                            echo json_encode(array('resp' => true, 'msj' => 'Registro exitoso 135'));
                        } else {
                            echo json_encode(array('resp' => false, 'msj' => 'Ocurrio un Error 137'));
                        }
                    } elseif ($tipo_pago == 2) {
                        // Primero guardar los productos
                        $insertCompra = false;
                        foreach ($array_detalle as $fila) {
                            $insertCompra = $c_compra->insertProductosCompras($fila['productoid'], $insertarCompra, $fila['cantidad'], $fila['precio']);
                        }

                        // Luego guardar los días de pago
                        $insertDiasCompra = false;
                        foreach ($listaPagos as $fila) {
                            $insertDiasCompra = $c_compra->insertDiasCompras($insertarCompra, $fila['monto'], $fila['fecha']);
                        }

                        if ($insertCompra && $insertDiasCompra) {
                            echo json_encode(array('resp' => true, 'msj' => 'Registro exitoso'));
                        } else {
                            echo json_encode(array('resp' => false, 'msj' => 'Ocurrio un Error'));
                        }
                    }
                } else {
                    echo json_encode(array('resp' => false, 'msj' => 'Ocurrio un Error'));
                }
            } else {
                echo json_encode(array('resp' => false, 'msj' => 'Ocurrio un Error'));
            }
        } else {
            echo json_encode(array('resp' => false, 'msj' => 'Llene todos los campos'));
        }



        /*   */
        /*   echo json_encode(array($_POST['num_doc'])); */
        /*    $id_empresa = $_SESSION['id_empresa'];
        $c_cliente->setIdEmpresa($id_empresa);
        $c_cliente->setDocumento(filter_input(INPUT_POST, 'num_doc'));
        $c_cliente->setDatos(filter_input(INPUT_POST, 'nom_cli'));
        $c_cliente->setDireccion(filter_input(INPUT_POST, 'dir_cli'));
        $c_cliente->setDireccion2(filter_input(INPUT_POST, 'dir2_cli'));

        return json_encode(array($c_cliente->setDocumento(filter_input(INPUT_POST, 'num_doc'))));
        if ($c_cliente->getDocumento() == "") {
            $c_cliente->setDocumento("SD" . $c_varios->generarCodigo(5));
            $c_cliente->insertar();
        } else {
            if (!$c_cliente->verificarDocumento()) {
                $c_cliente->insertar();
            }
        }

        $direccionselk = $_POST['dir_cli'] !== null ? $_POST['dir_cli'] : '-'; */
    }

  public function getAll()
{
    $where = ($_SESSION['rol'] == 1) ? "" : "and c.sucursal = {$_SESSION["sucursal"]} ";
    $sql = "SELECT c.id_compra, c.fecha_emision, c.fecha_vencimiento, c.serie, c.numero, 
        p.razon_social, u.nombres, u.apellidos, u.usuario_id 
        FROM compras AS c 
        LEFT JOIN proveedores AS p ON c.id_proveedor = p.proveedor_id 
        LEFT JOIN usuarios AS u ON c.id_usuario = u.usuario_id 
        WHERE c.id_empresa = '{$_SESSION['id_empresa']}' $where";
    
    $result = $this->conectar->query($sql);
    $data = $result->fetch_all(MYSQLI_ASSOC);
    
    // Cambio importante: devolver los datos como JSON
    echo json_encode($data);
    exit; // Asegurarse de que no se ejecute más código después
}

    public function getDetalle()
    {
        $sql = "SELECT pc.id_producto_venta,p.nombre,p.codigo, p.descripcion,pc.cantidad,pc.precio FROM productos_compras AS pc LEFT JOIN productos AS p ON
        pc.id_producto=p.id_producto LEFT JOIN compras AS c ON
        pc.id_compra=c.id_compra WHERE c.id_compra = '{$_POST['id']}'";
        return json_encode($this->conectar->query($sql)->fetch_all(MYSQLI_ASSOC));

    }
    public function getPagos()
    {
        $id_compra = $_POST['id'];

        // Obtener el tipo de pago
        $sql = "SELECT id_tipo_pago FROM compras WHERE id_compra = '$id_compra'";
        $tipo_pago = $this->conectar->query($sql)->fetch_assoc()['id_tipo_pago'];

        $result = ['tipo_pago' => $tipo_pago];

        if ($tipo_pago == 2) {
            // Es a crédito, obtener los pagos
            $sql = "SELECT fecha, monto, estado FROM dias_compras WHERE id_compra = '$id_compra'";
            $pagos = $this->conectar->query($sql)->fetch_all(MYSQLI_ASSOC);
            $result['pagos'] = $pagos;
        }

        return json_encode($result);
    }

    public function buscarProducto()
    {
        $dataProducto = $_POST['producto'];
        if ($dataProducto !== '') {
            $sql = "SELECT * from productos 
        where codigo LIKE '%$dataProducto%' AND almacen = 1";

            $getAll = $this->conectar->query($sql)->fetch_all(MYSQLI_ASSOC);
            if (!empty($getAll)) {
                $res = array("res" => true, "data" => $getAll);
                echo json_encode($res);
            } else {
                $res = array("res" => false, "msj" => 'No se encontró ningun producto');
                echo json_encode($res);
            }
        }
    }
    // Método para obtener observaciones predeterminadas
    public function getObservacion()
    {
        $respuesta = [];
        $sql = "SELECT * FROM observacion";
        $resultado = $this->conectar->query($sql);
        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }
        return json_encode($respuesta);
    }

    // Método para guardar las observaciones predeterminadas
    public function saveObservacion()
    {
        $sql = "UPDATE observacion SET detalle='{$_POST["detalle"]}'";
        $this->conectar->query($sql);
    }

    public function getObservacionCompra($id_compra = null)
    {
        // Verificar si el ID viene como parte de la URL (parámetro de ruta)
        if (!$id_compra && isset($this->params['id'])) {
            $id_compra = $_GET['id'];
        }
        // Verificar si el ID viene como parámetro GET
        else if (!$id_compra && isset($_GET['id'])) {
            $id_compra = $_GET['id'];
        }

        $respuesta = [];
        $sql = "SELECT * FROM observaciones_compra WHERE id_compra = '$id_compra'";
        $resultado = $this->conectar->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }

    // Método para guardar observaciones específicas de una compra
    public function saveObservacionCompra()
    {
        $id_compra = $_POST['compra_id'];
        $observaciones = $_POST['observaciones'];

        // Verificar si ya existe una observación para esta compra
        $sql = "SELECT * FROM observaciones_compra WHERE id_compra = '$id_compra'";
        $resultado = $this->conectar->query($sql);

        if ($resultado->num_rows > 0) {
            // Actualizar observaciones existentes
            $sql = "UPDATE observaciones_compra SET observaciones='$observaciones' WHERE id_compra='$id_compra'";
        } else {
            // Insertar nuevas observaciones
            $sql = "INSERT INTO observaciones_compra (id_compra, observaciones) VALUES ('$id_compra', '$observaciones')";
        }

        $this->conectar->query($sql);
        return json_encode(['success' => true]);
    }

    // Método para guardar observaciones temporales en sesión
    public function saveObservacionTemp()
    {
        $_SESSION['temp_observaciones'] = $_POST['observaciones'];
        return json_encode(['success' => true]);
    }

    // Método para guardar las observaciones predeterminadas
    public function saveObservacionDefault()
    {
        if (!isset($_POST['detalle'])) {
            return json_encode(['success' => false, 'message' => 'No se proporcionaron observaciones']);
        }

        $observaciones = $this->conectar->real_escape_string($_POST['detalle']);

        // Verificar si ya existe un registro en la tabla observacion
        $sql = "SELECT * FROM observacion LIMIT 1";
        $resultado = $this->conectar->query($sql);

        if ($resultado->num_rows > 0) {
            // Actualizar el registro existente
            $sql = "UPDATE observacion SET detalle='$observaciones'";
        } else {
            // Insertar un nuevo registro
            $sql = "INSERT INTO observacion (detalle) VALUES ('$observaciones')";
        }

        if ($this->conectar->query($sql)) {
            return json_encode(['success' => true]);
        } else {
            return json_encode(['success' => false, 'message' => 'Error al guardar: ' . $this->conectar->error]);
        }
    }



}