<?php

require_once 'utils/lib/exel/vendor/autoload.php';
require_once 'app/models/Repuesto.php';
require_once 'app/models/Almacen.php';

class RepuestosController extends Controller
{
    private $conexion;
    private $c_repuesto;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function listaRepuestoServerSide()
    {
        require_once 'app/clases/serverside.php';
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Type: application/json');

        $almacen = isset($_GET['almacenId']) ? $_GET['almacenId'] : '1';
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

        $table_data = new TableData();
        $view = "view_repuestos_$almacen";

        // Construir cláusula WHERE para filtrado
        $where = '';
        switch ($filter) {
            case 'JVC':
                $where = "codigo LIKE 'JVC%'";
                break;
            case 'IMPLE':
                $where = "codigo LIKE 'IMPLE%'";
                break;
            case 'REP':
                $where = "codigo LIKE 'REP%'";
                break;
        }

        $result = $table_data->getAlmacen(
            $view,
            'id_repuesto',
            [
                'codigo',
                'nombre',
                'unidad',
                'precio_unidad',
                'cantidad',
                'id_repuesto',
                'id_repuesto',
                'moneda'
            ],
            false,
            '',
            $where
        );

        if (!$result) {
            error_log("Error en listaRepuestoServerSide: No se pudieron obtener datos de $view");
            echo json_encode([
                'sEcho' => intval($_GET['sEcho']),
                'iTotalRecords' => 0,
                'iTotalDisplayRecords' => 0,
                'aaData' => [],
                'error' => 'No se pudieron obtener los datos'
            ]);
            exit;
        }

        echo json_encode($result);
        exit;
    }

    public function listaRepuesto()
    {
        $c_repuesto = new Repuesto();
        $c_repuesto->setIdEmpresa($_SESSION['id_empresa']);
        $a_repuestos = $c_repuesto->verFilas($_POST['almacenId']);
        $lista = [];
        foreach ($a_repuestos as $rowT) {
            $lista[] = $rowT;
        }
        return json_encode($lista);
    }

    public function agregarPorLista()
    {
        $respuesta = ['res' => false, 'error' => ''];

        try {
            // CAPTURAR EL ALMACÉN SELECCIONADO DESDE EL MODAL (no del Excel)
            $almacenDestino = isset($_POST['almacen']) ? intval($_POST['almacen']) : 1;

            $lista = json_decode($_POST['lista'], true);

            foreach ($lista as $item) {
                $afect = isset($item['afecto']) ? ($item['afecto'] ? '1' : '0') : '0';
                $descripcion = isset($item['descripcicon']) ? $item['descripcicon'] : '';
                $codigoRep = isset($item['codigoRep']) ? $item['codigoRep'] : '';

                // Add default value for codsunat like in ProductosController
                $codsunat = isset($item['codsunat']) ? $item['codsunat'] : '0';

                // Default values for numeric fields
                $nombre = isset($item['repuesto']) ? $item['repuesto'] : '';
                $precio = isset($item['precio_unidad']) ? floatval($item['precio_unidad']) : 0;
                $precio2 = isset($item['precio2']) ? floatval($item['precio2']) : 0;
                // USAR EL ALMACÉN SELECCIONADO EN EL MODAL, NO EL DEL EXCEL
                $almacen = $almacenDestino;
                $precioUnidad = isset($item['precio_unidad']) ? floatval($item['precio_unidad']) : 0;
                $costo = isset($item['costo']) ? floatval($item['costo']) : 0;
                $cantidad = isset($item['cantidad']) ? intval($item['cantidad']) : 0;
                $moneda = isset($item['moneda']) ? $item['moneda'] : 'PEN';

                // Verificar si el repuesto existe - Filtrar por codigo, empresa, sucursal y almacen
                $sqlRepuesto = 'SELECT * FROM repuestos WHERE codigo = ? AND id_empresa = ? AND sucursal = ? AND almacen = ?';
                $stmt = $this->conexion->prepare($sqlRepuesto);
                $stmt->bind_param('siii', $codigoRep, $_SESSION['id_empresa'], $_SESSION['sucursal'], $almacen);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $repuesto = $resultado->fetch_assoc();
                $stmt->close();

                if ($repuesto) {
                    $updateRepuesto = 'UPDATE repuestos SET
                        nombre = ?,
                        detalle = ?,
                        precio = ?,
                        precio2 = ?,
                        almacen = ?,
                        precio_unidad = ?,
                        costo = ?,
                        cantidad = ?,
                        moneda = ?
                        WHERE codigo = ? AND id_empresa = ? AND sucursal = ? AND almacen = ?';

                    $stmt = $this->conexion->prepare($updateRepuesto);
                    if (!$stmt) {
                        throw new Exception('Error preparando actualización: ' . $this->conexion->error);
                    }

                    $stmt->bind_param(
                        'ssddsdddssiii',
                        $nombre,
                        $descripcion,
                        $precio,
                        $precio2,
                        $almacen,
                        $precioUnidad,
                        $costo,
                        $cantidad,
                        $moneda,
                        $codigoRep,
                        $_SESSION['id_empresa'],
                        $_SESSION['sucursal'],
                        $almacen
                    );
                } else {
                    $sql = 'INSERT INTO repuestos (
                        nombre, detalle, precio, precio2, almacen,
                        precio_unidad, costo, cantidad, iscbp,
                        id_empresa, sucursal, codigo, ultima_salida,
                        codsunat, moneda
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';

                    $stmt = $this->conexion->prepare($sql);
                    if (!$stmt) {
                        throw new Exception('Error preparando inserción: ' . $this->conexion->error);
                    }

                    $ultimaSalida = '1000-01-01';
                    $stmt->bind_param(
                        'ssddsdddsisssss',
                        $nombre,
                        $descripcion,
                        $precio,
                        $precio2,
                        $almacen,
                        $precioUnidad,
                        $costo,
                        $cantidad,
                        $afect,
                        $_SESSION['id_empresa'],
                        $_SESSION['sucursal'],
                        $codigoRep,
                        $ultimaSalida,
                        $codsunat,
                        $moneda
                    );
                }

                if (!$stmt->execute()) {
                    throw new Exception('Error en la operación: ' . $stmt->error);
                }
                $stmt->close();
            }

            $respuesta['res'] = true;
        } catch (Exception $e) {
            $respuesta['error'] = $e->getMessage();
            error_log('Error en agregarPorLista: ' . $e->getMessage());
        }

        return json_encode($respuesta);
    }

    public function importarExel()
    {
        $respuesta = ['res' => false];
        $filename = $_FILES['file']['name'];

        $path_parts = pathinfo($filename, PATHINFO_EXTENSION);
        $newName = Tools::getToken(80);
        $loc_ruta = 'files/temp';
        if (!file_exists($loc_ruta)) {
            mkdir($loc_ruta, 0777, true);
        }
        $location = $loc_ruta . '/' . $newName . '.' . $path_parts;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
            $nombre_logo = $newName . '.' . $path_parts;

            $respuesta['res'] = true;
            $type = $path_parts;

            if ($type == 'xlsx') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } elseif ($type == 'xls') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } elseif ($type == 'csv') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load('files/temp/' . $nombre_logo);

            $schdeules = $spreadsheet->getActiveSheet()->toArray();
            $respuesta['data'] = $schdeules;

            unlink($location);
        }

        return json_encode($respuesta);
    }

    public function restock()
    {
        $respuesta = ['res' => false];
        $sql = "update repuestos set cantidad=cantidad+{$_POST['cantidad']} where id_repuesto='{$_POST['cod']}'";
        if ($this->conexion->query($sql)) {
            $respuesta['res'] = true;
        }
        return json_encode($respuesta);
    }

    public function informacionPorCodigo()
    {
        $respuesta = ['res' => false];
        $sql = "SELECT * FROM repuestos where trim(codigo)='{$_POST['code']}' AND almacen = '{$_POST['almacen']}' and sucursal='{$_SESSION['sucursal']}'";

        if ($row = $this->conexion->query($sql)->fetch_assoc()) {
            $respuesta['res'] = true;
            $respuesta['data'] = $row;
        }
        return json_encode($respuesta);
    }

    public function informacion()
    {
        $respuesta = ['res' => false];
        $sql = "SELECT * FROM repuestos where id_repuesto='{$_POST['cod']}'";
        if ($row = $this->conexion->query($sql)->fetch_assoc()) {
            $respuesta['res'] = true;
            $respuesta['data'] = $row;
        }
        return json_encode($respuesta);
    }

    public function agregar()
    {
        $respuesta = ['res' => false];
        $codigoRep = $_POST['codigo'];
        $usar_multiprecio = isset($_POST['usar_multiprecio']) ? $_POST['usar_multiprecio'] : '0';
        $precios = isset($_POST['precios']) ? json_decode($_POST['precios'], true) : [];

        // obtener la subcategoria del POST
        $subcategoria = isset($_POST['subcategoria']) ? $_POST['subcategoria'] : 'NULL';
        try {
            $this->conexion->begin_transaction();

            // Manejo de la imagen (código existente)
            $nombreImagen = null;
            $rutaDestino = '';

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $imagen = $_FILES['imagen'];
                $nombreImagen = $imagen['name'];
                $rutaDestino = 'public/img/repuestos/' . $nombreImagen;

                if (!move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
                    throw new Exception('Error al subir la imagen');
                }
            }

            // ACTUALIZADO: Manejo del código de barras
            $codigoBar = null;
            if (isset($_POST['usar_barra']) && $_POST['usar_barra'] == 1) {
                $codigoBar = $_POST['codigo'];
            }

            // Insertar repuesto
            $sql = "INSERT INTO repuestos SET 
                nombre = '{$_POST['nombre']}',
                precio = '{$_POST['precio']}', 
                costo = '{$_POST['costo']}', 
                almacen = '{$_POST['almacen']}', 
                cantidad = '{$_POST['cantidad']}', 
                iscbp = '{$_POST['afecto']}', 
                sucursal = '{$_SESSION['sucursal']}', 
                id_empresa = '{$_SESSION['id_empresa']}', 
                ultima_salida = '1000-01-01', 
                codsunat = '{$_POST['codSunat']}', 
                precio_mayor = {$_POST['precio1']}, 
                precio_menor = {$_POST['precio2']}, 
                precio2 = {$_POST['precio2']}, 
                precio3 = {$_POST['precio3']}, 
                precio4 = {$_POST['precio4']}, 
                precio_unidad = {$_POST['precio']}, 
                razon_social = '{$_POST['razon']}', 
                ruc = '{$_POST['ruc']}', 
                detalle= '{$_POST['detalle']}',
                categoria= '{$_POST['categoria']}',
                subcategoria= '{$_POST['subcategoria']}',
                unidad= '{$_POST['unidad']}',
                moneda= '{$_POST['moneda']}',
                usar_multiprecio = '{$usar_multiprecio}',
                usar_barra = '" . (isset($_POST['usar_barra']) ? $_POST['usar_barra'] : '0') . "',
                cod_barra = " . ($codigoBar ? "'{$codigoBar}'" : 'NULL') . ',
                codigo = ?';

            if ($nombreImagen) {
                $sql .= ", imagen = '{$nombreImagen}'";
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('s', $codigoRep);

            if (!$stmt->execute()) {
                throw new Exception('Error al insertar repuesto: ' . $stmt->error);
            }

            $id_repuesto = $this->conexion->insert_id;

            // Si usa multiprecio, guardar los precios
            if ($usar_multiprecio === '1' && !empty($precios)) {
                $sql = 'INSERT INTO repuesto_precios (id_repuesto, nombre, precio) VALUES (?, ?, ?)';
                $stmt = $this->conexion->prepare($sql);

                foreach ($precios as $precio) {
                    $nombre = $precio['nombre'];
                    $valor = $precio['precio'];
                    $stmt->bind_param('iss', $id_repuesto, $nombre, $valor);
                    if (!$stmt->execute()) {
                        throw new Exception('Error al insertar precio: ' . $stmt->error);
                    }
                }
            }

            $this->conexion->commit();
            $respuesta['res'] = true;
        } catch (Exception $e) {
            $this->conexion->rollback();
            $respuesta['error'] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function actualizar()
    {
        $respuesta = ['res' => false];
        $codigoRep = $_POST['codigo'];
        $usar_multiprecio = isset($_POST['usar_multiprecio']) ? $_POST['usar_multiprecio'] : '0';

        try {
            $this->conexion->begin_transaction();

            // ACTUALIZADO: Manejo del código de barras
            $codigoBar = null;
            if ($_POST['usar_barra'] == 1) {
                // Usar el código del repuesto como código de barras
                $codigoBar = $_POST['codigo'];
            }

            $sql = "select * from repuestos where id_repuesto='{$_POST['cod']}'";
            $result = $this->conexion->query($sql);
            if ($row = $result->fetch_assoc()) {
                $almacenTemp = $row['almacen'] == '1' ? 2 : 1;
                $sql = 'update repuestos set 
                     cod_barra=' . ($codigoBar ? "'{$codigoBar}'" : 'NULL') . ",
                     usar_barra='{$_POST['usar_barra']}',
                     usar_multiprecio='{$usar_multiprecio}',
                     precio='{$_POST['precio']}',
                     costo='{$_POST['costo']}',
                     categoria='{$_POST['categoria']}',
                     subcategoria='{$_POST['subcategoria']}',
                     unidad='{$_POST['unidad']}',
                     iscbp='{$_POST['afecto']}',
                     codsunat='{$_POST['codSunat']}',
                     almacen = '{$_POST['almacen']}', 
                     precio_mayor={$_POST['precioMayor']},precio_menor={$_POST['precioMenor']},
                     precio2={$_POST['precioMenor']},precio3={$_POST['precio3']},precio4={$_POST['precio4']},precio_unidad={$_POST['precio']},
                     razon_social='{$_POST['razon']}',ruc='{$_POST['ruc']}',detalle='{$_POST['detalle']}',
                     moneda='{$_POST['moneda']}',
                     codigo=?
                     where descripcion=? and almacen='$almacenTemp'";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param('ss', $codigoRep, $row['descripcion']);

                if (!$stmt->execute()) {
                    throw new Exception('Error al actualizar repuesto relacionado: ' . $stmt->error);
                }
            }

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $imagen = $_FILES['imagen'];
                $nombreImagen = $imagen['name'];
                $rutaDestino = 'public/img/repuestos/' . $nombreImagen;

                if (!move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
                    throw new Exception('Error al subir la imagen');
                }
            }

            $sql = 'update repuestos set 
                 cod_barra=' . ($codigoBar ? "'{$codigoBar}'" : 'NULL') . ",
                 nombre = '{$_POST['nombre']}',
                 usar_barra='{$_POST['usar_barra']}',
                 usar_multiprecio='{$usar_multiprecio}',
                 almacen = '{$_POST['almacen']}', 
                 categoria='{$_POST['categoria']}',
                 subcategoria='{$_POST['subcategoria']}',
                 unidad='{$_POST['unidad']}',
                 precio='{$_POST['precio']}',
                 costo='{$_POST['costo']}',
                 iscbp='{$_POST['afecto']}',
                 cantidad='{$_POST['cantidad']}',
                 codsunat='{$_POST['codSunat']}',precio_mayor={$_POST['precioMayor']},precio_menor={$_POST['precioMenor']},
                 precio2={$_POST['precioMenor']},precio3={$_POST['precio3']},precio4={$_POST['precio4']},precio_unidad={$_POST['precio']},
                 detalle='{$_POST['detalle']}',
                 razon_social='{$_POST['razon']}',ruc='{$_POST['ruc']}',
                 moneda='{$_POST['moneda']}',
                 codigo=?
                 where id_repuesto='{$_POST['cod']}'";

            if (isset($nombreImagen)) {
                $sql = str_replace('codigo=?', "imagen = '{$nombreImagen}', codigo=?", $sql);
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('s', $codigoRep);

            if (!$stmt->execute()) {
                throw new Exception('Error al actualizar repuesto: ' . $stmt->error);
            }

            $this->conexion->commit();
            $respuesta['res'] = true;
            $respuesta['cod_barra'] = $codigoBar;  // devolver el nuevo código de barras
        } catch (Exception $e) {
            $this->conexion->rollback();
            $respuesta['error'] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function actualizarPrecios()
    {
        $respuesta = ['res' => false];
        $sql = "update repuestos set precio='{$_POST['precio']}',precio_unidad='{$_POST['precio_unidad']}', precio2='{$_POST['precio2']}', precio3='{$_POST['precio3']}', precio4='{$_POST['precio4']}' where id_repuesto='{$_POST['cod_rep']}'";
        if ($this->conexion->query($sql)) {
            $respuesta['res'] = true;
            $sql = "select * from repuestos where id_repuesto='{$_POST['cod_rep']}'";
            $result = $this->conexion->query($sql);
            if ($row = $result->fetch_assoc()) {
                $almacenTemp = $row['almacen'] == '1' ? 2 : 1;
                $sql = "update repuestos set 
                     precio='{$_POST['precio']}',precio_unidad='{$_POST['precio_unidad']}', 
                     precio2='{$_POST['precio2']}', precio3='{$_POST['precio3']}', 
                     precio4='{$_POST['precio4']}'
                  where descripcion=? and almacen='$almacenTemp'";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param('s', $row['descripcion']);

                if (!$stmt->execute()) {
                    // Handle error if needed
                }
            }
        }
        return json_encode($respuesta);
    }

    public function confirmarTraslado()
    {
        $respuesta['res'] = false;
        $sql = "SELECT id_repuesto,almacen_ingreso,almacen_egreso,cantidad FROM ingreso_egreso WHERE intercambio_id ='{$_POST['cod']}'";
        $result = $this->conexion->query($sql)->fetch_assoc();

        $almacen = $result['almacen_ingreso'];
        $id_repuesto = $result['id_repuesto'];
        $cantidad = $result['cantidad'];

        $sql = "SELECT * FROM repuestos WHERE id_repuesto = '{$result['id_repuesto']}'";
        $result = $this->conexion->query($sql)->fetch_assoc();

        $sql = "SELECT * FROM repuestos WHERE descripcion = '{$result['descripcion']}' AND almacen = '$almacen'";
        $result2 = $this->conexion->query($sql)->fetch_assoc();

        if (is_null($result2)) {
            $sql = "INSERT INTO repuestos 
            (cod_barra, descripcion, precio,categoria,unidad, costo,cantidad,iscbp,id_empresa,sucursal,ultima_salida,codsunat,usar_barra,precio_mayor,precio_menor,razon_social,ruc,estado,almacen,precio2,precio3)
            SELECT cod_barra, descripcion, precio,categoria,unidad, costo,$cantidad,iscbp,id_empresa,sucursal,ultima_salida,codsunat,usar_barra,precio_mayor,precio_menor,razon_social,ruc,estado, $almacen,precio2,precio3
            FROM repuestos
            WHERE id_repuesto = $id_repuesto";
            if ($this->conexion->query($sql)) {
                $sql = "UPDATE repuestos set cantidad = cantidad - $cantidad WHERE id_repuesto = $id_repuesto";
                if ($this->conexion->query($sql)) {
                    $respuesta['res'] = true;
                }
            }
        } else {
            $idExistente = $result2['id_repuesto'];
            $sql2 = "UPDATE repuestos set cantidad = cantidad - $cantidad WHERE id_repuesto = $id_repuesto";
            if ($this->conexion->query($sql2)) {
                $sql = "UPDATE repuestos set cantidad = cantidad + $cantidad WHERE id_repuesto = $idExistente";
                if ($this->conexion->query($sql)) {
                    $respuesta['res'] = true;
                }
            }
        }
        if ($respuesta['res']) {
            $sql = "UPDATE ingreso_egreso set estado = 1 WHERE intercambio_id = '{$_POST['cod']}'";
            if ($this->conexion->query($sql)) {
                $respuesta['res'] = true;
            }
        }
        echo json_encode($respuesta);
    }

    public function delete()
    {
        $respuesta['res'] = true;
        $respuesta['data'] = $_POST;
        $sql = '';
        foreach ($respuesta['data']['arrayId'] as $ids) {
            $sql = "UPDATE repuestos set estado=0 where id_repuesto = '{$ids['id']}'";
            if ($this->conexion->query($sql)) {
                $respuesta['res'] = true;
            }
        }
        return json_encode($respuesta);
    }

    // Método para guardar precios múltiples
    public function guardarPrecios()
    {
        $respuesta = ['res' => false];
        $id_repuesto = $_POST['id_repuesto'];
        $precios = $_POST['precios'];

        try {
            // Iniciar transacción
            $this->conexion->begin_transaction();

            // Eliminar precios existentes
            $sql = 'DELETE FROM repuesto_precios WHERE id_repuesto = ?';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('i', $id_repuesto);
            $stmt->execute();

            // Insertar nuevos precios
            $sql = 'INSERT INTO repuesto_precios (id_repuesto, nombre, precio) VALUES (?, ?, ?)';
            $stmt = $this->conexion->prepare($sql);

            foreach ($precios as $precio) {
                $nombre = $precio['nombre'];
                $valor = $precio['precio'];
                $stmt->bind_param('iss', $id_repuesto, $nombre, $valor);
                $stmt->execute();
            }

            // Actualizar el campo usar_multiprecio en la tabla repuestos
            $sql = "UPDATE repuestos SET usar_multiprecio = '1' WHERE id_repuesto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('i', $id_repuesto);
            $stmt->execute();

            // Confirmar transacción
            $this->conexion->commit();
            $respuesta['res'] = true;
        } catch (Exception $e) {
            // Revertir en caso de error
            $this->conexion->rollback();
            $respuesta['error'] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    // Método para obtener precios múltiples
    public function obtenerPrecios()
    {
        $respuesta = ['res' => false, 'precios' => []];
        $id_repuesto = $_POST['id_repuesto'];

        try {
            $sql = 'SELECT * FROM repuesto_precios WHERE id_repuesto = ?';
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('i', $id_repuesto);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $respuesta['precios'][] = [
                        'id' => $row['id'],
                        'nombre' => $row['nombre'],
                        'precio' => $row['precio']
                    ];
                }
                $respuesta['res'] = true;
            }
        } catch (Exception $e) {
            $respuesta['error'] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function aumentarStock()
    {
        $respuesta = ['res' => false];

        try {
            $repuesto_id = $_POST['repuesto_id'];
            $cantidad = intval($_POST['cantidad']);
            $fecha_actual = date('Y-m-d H:i:s');

            // Actualizar stock del repuesto
            $sql = 'UPDATE repuestos SET 
                    cantidad = cantidad + ?
                    WHERE id_repuesto = ?';

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('ii', $cantidad, $repuesto_id);

            if ($stmt->execute()) {
                // Registrar el movimiento en historial_stock_repuestos
                $sql_historial = "INSERT INTO historial_stock_repuestos 
                                 (id_repuesto, tipo_movimiento, cantidad, fecha_movimiento, usuario) 
                                 VALUES (?, 'INGRESO', ?, ?, ?)";

                $stmt_hist = $this->conexion->prepare($sql_historial);
                $usuario = $_SESSION['usuario'] ?? 'Sistema';
                $stmt_hist->bind_param('iiss', $repuesto_id, $cantidad, $fecha_actual, $usuario);
                $stmt_hist->execute();

                $respuesta['res'] = true;
            }
        } catch (Exception $e) {
            $respuesta['error'] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function disminuirStock()
    {
        $respuesta = ['res' => false];

        try {
            $repuesto_id = $_POST['repuesto_id'];
            $cantidad = intval($_POST['cantidad']);
            $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : null;
            $fecha_actual = date('Y-m-d H:i:s');

            // Verificar que hay stock suficiente
            $sql_check = 'SELECT cantidad FROM repuestos WHERE id_repuesto = ?';
            $stmt_check = $this->conexion->prepare($sql_check);
            $stmt_check->bind_param('i', $repuesto_id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $repuesto = $result->fetch_assoc();

            if ($repuesto['cantidad'] < $cantidad) {
                $respuesta['error'] = 'Stock insuficiente';
                return json_encode($respuesta);
            }

            // Actualizar stock del repuesto (restar)
            $sql = 'UPDATE repuestos SET 
                cantidad = cantidad - ?
                WHERE id_repuesto = ?';

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('ii', $cantidad, $repuesto_id);

            if ($stmt->execute()) {
                // Registrar el movimiento en historial como EGRESO
                $sql_historial = "INSERT INTO historial_stock_repuestos 
                             (id_repuesto, tipo_movimiento, cantidad, fecha_movimiento, usuario, observaciones) 
                             VALUES (?, 'EGRESO', ?, ?, ?, ?)";

                $stmt_hist = $this->conexion->prepare($sql_historial);
                $usuario = $_SESSION['usuario'] ?? 'Administrador';
                $stmt_hist->bind_param('iisss', $repuesto_id, $cantidad, $fecha_actual, $usuario, $observaciones);
                $stmt_hist->execute();

                $respuesta['res'] = true;
            }
        } catch (Exception $e) {
            $respuesta['error'] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function trasladoAlmacenes()
    {
        $respuesta = ['res' => false];

        try {
            $almacen_origen = $_POST['almacen_origen'];
            $almacen_destino = $_POST['almacen_destino'];
            $repuestos = $_POST['repuestos'];
            $nota = isset($_POST['nota']) ? $_POST['nota'] : '';
            $fecha_actual = date('Y-m-d H:i:s');

            // Validar que origen y destino sean diferentes
            if ($almacen_origen == $almacen_destino) {
                $respuesta['error'] = 'El almacén de origen y destino no pueden ser el mismo';
                return json_encode($respuesta);
            }

            // Iniciar transacción
            $this->conexion->begin_transaction();

            foreach ($repuestos as $repuesto) {
                $repuesto_id = $repuesto['id_repuesto'];
                $cantidad = intval($repuesto['cantidad']);

                // Verificar stock en almacén origen
                $sql_check = 'SELECT cantidad FROM repuestos WHERE id_repuesto = ? AND almacen = ?';
                $stmt_check = $this->conexion->prepare($sql_check);
                $stmt_check->bind_param('ii', $repuesto_id, $almacen_origen);
                $stmt_check->execute();
                $result = $stmt_check->get_result();
                $rep_origen = $result->fetch_assoc();

                if (!$rep_origen || $rep_origen['cantidad'] < $cantidad) {
                    $this->conexion->rollback();
                    $respuesta['error'] = 'Stock insuficiente en almacén origen para el repuesto ID: ' . $repuesto_id;
                    return json_encode($respuesta);
                }

                // Disminuir stock en almacén origen
                $sql_origen = 'UPDATE repuestos SET cantidad = cantidad - ? WHERE id_repuesto = ? AND almacen = ?';
                $stmt_origen = $this->conexion->prepare($sql_origen);
                $stmt_origen->bind_param('iii', $cantidad, $repuesto_id, $almacen_origen);
                $stmt_origen->execute();

                // Registrar EGRESO en historial
                $sql_hist_egreso = "INSERT INTO historial_stock_repuestos 
                             (id_repuesto, tipo_movimiento, cantidad, fecha_movimiento, usuario, observaciones) 
                             VALUES (?, 'EGRESO', ?, ?, ?, ?)";
                $stmt_hist_egreso = $this->conexion->prepare($sql_hist_egreso);
                $usuario = $_SESSION['usuario'] ?? 'Administrador';
                $obs_egreso = "Traslado de Almacén $almacen_origen a Almacén $almacen_destino. " . $nota;
                $stmt_hist_egreso->bind_param('iisss', $repuesto_id, $cantidad, $fecha_actual, $usuario, $obs_egreso);
                $stmt_hist_egreso->execute();

                // Verificar si el repuesto existe en almacén destino
                $sql_check_destino = 'SELECT id_repuesto, cantidad FROM repuestos WHERE id_repuesto = ? AND almacen = ?';
                $stmt_check_destino = $this->conexion->prepare($sql_check_destino);
                $stmt_check_destino->bind_param('ii', $repuesto_id, $almacen_destino);
                $stmt_check_destino->execute();
                $result_destino = $stmt_check_destino->get_result();
                $rep_destino = $result_destino->fetch_assoc();

                if ($rep_destino) {
                    // Aumentar stock en almacén destino
                    $sql_destino = 'UPDATE repuestos SET cantidad = cantidad + ? WHERE id_repuesto = ? AND almacen = ?';
                    $stmt_destino = $this->conexion->prepare($sql_destino);
                    $stmt_destino->bind_param('iii', $cantidad, $repuesto_id, $almacen_destino);
                    $stmt_destino->execute();
                } else {
                    // Crear registro en almacén destino (copiar del origen)
                    $sql_copiar = 'INSERT INTO repuestos (cod_barra, nombre, precio, costo, cantidad, iscbp, id_empresa, sucursal, ultima_salida, codsunat, usar_barra, usar_multiprecio, precio_mayor, precio_menor, razon_social, ruc, estado, almacen, precio2, precio3, precio4, precio_unidad, codigo, imagen, detalle, categoria, descripcion, unidad, moneda, subcategoria)
                                   SELECT cod_barra, nombre, precio, costo, ?, iscbp, id_empresa, sucursal, ultima_salida, codsunat, usar_barra, usar_multiprecio, precio_mayor, precio_menor, razon_social, ruc, estado, ?, precio2, precio3, precio4, precio_unidad, codigo, imagen, detalle, categoria, descripcion, unidad, moneda, subcategoria
                                   FROM repuestos WHERE id_repuesto = ? AND almacen = ? LIMIT 1';
                    $stmt_copiar = $this->conexion->prepare($sql_copiar);
                    $stmt_copiar->bind_param('iiii', $cantidad, $almacen_destino, $repuesto_id, $almacen_origen);
                    $stmt_copiar->execute();
                }

                // Registrar INGRESO en historial
                $sql_hist_ingreso = "INSERT INTO historial_stock_repuestos 
                             (id_repuesto, tipo_movimiento, cantidad, fecha_movimiento, usuario, observaciones) 
                             VALUES (?, 'INGRESO', ?, ?, ?, ?)";
                $stmt_hist_ingreso = $this->conexion->prepare($sql_hist_ingreso);
                $obs_ingreso = "Traslado desde Almacén $almacen_origen a Almacén $almacen_destino. " . $nota;
                $stmt_hist_ingreso->bind_param('iisss', $repuesto_id, $cantidad, $fecha_actual, $usuario, $obs_ingreso);
                $stmt_hist_ingreso->execute();
            }

            // Confirmar transacción
            $this->conexion->commit();
            $respuesta['res'] = true;
        } catch (Exception $e) {
            $this->conexion->rollback();
            $respuesta['error'] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function obtenerHistorialStock()
    {
        $respuesta = ["res" => false, "data" => []];

        try {
            $repuesto_id = isset($_POST['repuesto_id']) ? $_POST['repuesto_id'] : null;

            if ($repuesto_id) {
                // Historial de un repuesto específico
                $sql = "SELECT h.*, r.nombre as repuesto_nombre, r.codigo
                    FROM historial_stock_repuestos h 
                    INNER JOIN repuestos r ON h.id_repuesto = r.id_repuesto 
                    WHERE h.id_repuesto = ? 
                    ORDER BY h.fecha_movimiento DESC";

                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param('i', $repuesto_id);
            } else {
                // Historial general (todos los movimientos de la empresa)
                $id_empresa = $_SESSION['id_empresa'];
                $sql = "SELECT h.*, r.nombre as repuesto_nombre, r.codigo
                    FROM historial_stock_repuestos h 
                    INNER JOIN repuestos r ON h.id_repuesto = r.id_repuesto 
                    WHERE r.id_empresa = ?
                    ORDER BY h.fecha_movimiento DESC";

                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param('i', $id_empresa);
            }

            $stmt->execute();
            $resultado = $stmt->get_result();

            while ($row = $resultado->fetch_assoc()) {
                $respuesta["data"][] = [
                    "id" => $row['id'],
                    "repuesto_nombre" => $row['repuesto_nombre'],
                    "codigo" => $row['codigo'],
                    "tipo_movimiento" => $row['tipo_movimiento'],
                    "cantidad" => $row['cantidad'],
                    "costo_compra" => isset($row['costo_compra']) ? $row['costo_compra'] : null,
                    "fecha_movimiento" => $row['fecha_movimiento'],
                    "usuario" => $row['usuario'],
                    "observaciones" => $row['observaciones']
                ];
            }

            $respuesta["res"] = true;

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function repuestosGrid()
    {
        $respuesta = ["res" => false, "data" => [], "total" => 0];

        try {
            $almacenId = isset($_POST['almacenId']) ? $_POST['almacenId'] : 1;
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 12;
            $search = isset($_POST['search']) ? $this->conexion->real_escape_string($_POST['search']) : '';
            $offset = ($page - 1) * $limit;

            $whereConditions = [];
            $whereConditions[] = "r.id_empresa = '{$_SESSION['id_empresa']}'";
            $whereConditions[] = "r.sucursal = '{$_SESSION['sucursal']}'";
            $whereConditions[] = "r.estado = '1'";
            $whereConditions[] = "r.almacen = '$almacenId'";

            if (!empty($search)) {
                $whereConditions[] = "(r.nombre LIKE '%$search%' OR r.codigo LIKE '%$search%')";
            }

            $whereClause = "WHERE " . implode(" AND ", $whereConditions);

            $sqlCount = "SELECT COUNT(*) as total FROM repuestos r $whereClause";
            $resultCount = $this->conexion->query($sqlCount);
            $totalRow = $resultCount->fetch_assoc();
            $respuesta["total"] = intval($totalRow['total']);

            $sql = "SELECT r.*, u.nombre as unidad_nombre
                FROM repuestos r
                LEFT JOIN unidades u ON r.unidad = u.id
                $whereClause
                ORDER BY r.codigo ASC
                LIMIT $limit OFFSET $offset";

            $resultado = $this->conexion->query($sql);

            if ($resultado && $resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $respuesta["data"][] = $row;
                }
                $respuesta["res"] = true;
            } else if ($resultado) {
                $respuesta["res"] = true;
            }
        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function listarAlmacenes()
    {
        $repuesto = new Repuesto();
        $repuesto->setIdEmpresa($_SESSION['id_empresa']);
        
        $almacenesCreados = $repuesto->crearVistasFaltantes();
        if ($almacenesCreados > 0) {
            error_log("Se crearon $almacenesCreados vistas para repuestos");
        }
        
        $almacenes = $repuesto->listarAlmacenes();
        
        foreach ($almacenes as &$alm) {
            $alm['id_almacen'] = (int)$alm['id_almacen'];
            $alm['principal'] = (int)$alm['principal'];
            $alm['id_empresa'] = (int)$alm['id_empresa'];
        }
        unset($alm);
        
        echo json_encode(['estado' => true, 'almacenes' => $almacenes]);
    }

    public function agregarAlmacen()
    {
        $nombre = $_POST['nombre'] ?? '';
        if (empty(trim($nombre))) {
            echo json_encode(['estado' => false, 'mensaje' => 'El nombre es requerido']);
            return;
        }

        $almacenModel = new Almacen();
        $almacenModel->setIdEmpresa($_SESSION['id_empresa']);
        $id = $almacenModel->agregar(trim($nombre));

        if ($id) {
            $repuesto = new Repuesto();
            $repuesto->setIdEmpresa($_SESSION['id_empresa']);
            $repuesto->crearVista($id);

            echo json_encode([
                'estado' => true, 
                'mensaje' => 'Almacén agregado', 
                'id' => $id
            ]);
        } else {
            echo json_encode(['estado' => false, 'mensaje' => 'Error al agregar almacén']);
        }
    }

    public function editarAlmacen()
    {
        $id = $_POST['id'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $principal = isset($_POST['principal']) ? 1 : 0;

        if (empty($id) || empty(trim($nombre))) {
            echo json_encode(['estado' => false, 'mensaje' => 'El nombre es requerido']);
            return;
        }

        $almacenModel = new Almacen();
        $almacenModel->setIdEmpresa($_SESSION['id_empresa']);

        if ($principal) {
            $almacenModel->marcarPrincipal($id);
        } else {
            $almacenModel->actualizar($id, trim($nombre));
        }

        echo json_encode(['estado' => true, 'mensaje' => 'Almacén actualizado']);
    }

    public function eliminarAlmacen()
    {
        $id = $_POST['id'] ?? '';

        if (empty($id)) {
            echo json_encode(['estado' => false, 'mensaje' => 'ID requerido']);
            return;
        }

        $almacenModel = new Almacen();
        $almacenModel->setIdEmpresa($_SESSION['id_empresa']);
        $resultado = $almacenModel->eliminar($id);

        echo json_encode($resultado);
    }
}
