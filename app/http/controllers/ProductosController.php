<?php

require_once "utils/lib/exel/vendor/autoload.php";
require_once "app/models/Producto.php";
require_once "app/models/Almacen.php";
require_once "app/helpers/ImageStorage.php";


class ProductosController extends Controller
{
    private $conexion;
    private $c_producto;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();

        /*   $c_producto->setIdEmpresa($_SESSION['id_empresa']); */
    }

    public function listarAlmacenes()
    {
        $almacen = new Almacen();
        $almacen->setIdEmpresa($_SESSION['id_empresa']);
        
        // Verificar y crear vistas faltantes para todos los almacenes
        $almacenesCreados = $almacen->crearVistasFaltantes();
        if ($almacenesCreados > 0) {
            error_log("Se crearon $almacenesCreados vistas para almacenes");
        }
        
        $almacenes = $almacen->listar();
        
        // Convertir id_almacen a entero para consistencia con Vue
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

        $almacen = new Almacen();
        $almacen->setIdEmpresa($_SESSION['id_empresa']);
        $id = $almacen->agregar(trim($nombre));

        if ($id) {
            $esPrincipal = ($id == 1) ? true : false; // Verificar si se marcó como principal
            echo json_encode([
                'estado' => true, 
                'mensaje' => 'Almacén agregado', 
                'id' => $id,
                'es_principal' => $esPrincipal
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

        $almacen = new Almacen();
        $almacen->setIdEmpresa($_SESSION['id_empresa']);

        // Si se marca como principal, actualizar todos los demás
        $almacen->actualizar($id, trim($nombre));
        if ($principal) {
            $almacen->marcarPrincipal($id);
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

        // No permitir eliminar si es el único almacén
        $sql = "SELECT COUNT(*) as cnt FROM almacenes WHERE id_empresa = '{$_SESSION['id_empresa']}' AND estado = '1'";
        $result = (new Conexion())->getConexion()->query($sql);
        $row = $result->fetch_assoc();
        if ($row['cnt'] <= 1) {
            echo json_encode(['estado' => false, 'mensaje' => 'No se puede eliminar. Debe haber al menos un almacén.']);
            return;
        }

        $almacen = new Almacen();
        $almacen->setIdEmpresa($_SESSION['id_empresa']);
        $resultado = $almacen->eliminar($id);

        if ($resultado['success']) {
            echo json_encode(['estado' => true, 'mensaje' => $resultado['message']]);
        } else {
            echo json_encode(['estado' => false, 'mensaje' => $resultado['error']]);
        }
    }

    public function obtenerAlmacen()
    {
        $id = $_GET['id'] ?? '';

        if (empty($id)) {
            echo json_encode(['estado' => false, 'mensaje' => 'ID requerido']);
            return;
        }

        $almacen = new Almacen();
        $almacen->setIdEmpresa($_SESSION['id_empresa']);
        $data = $almacen->obtener($id);

        if ($data) {
            $data['tiene_productos'] = $almacen->tieneProductos($id);
            echo json_encode(['estado' => true, 'almacen' => $data]);
        } else {
            echo json_encode(['estado' => false, 'mensaje' => 'Almacén no encontrado']);
        }
    }

    public function listaProductoServerSide()
    {
        require_once "app/clases/serverside.php";
        header('Pragma: no-cache');
        header('Cache-Control: no-store, no-cache, must-revalidate');
        header('Content-Type: application/json');

        // Obtener parámetros
        $almacen = isset($_GET['almacenId']) ? $_GET['almacenId'] : '1';
        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';

        // error_log("Buscando productos con almacen: $almacen y filtro: $filter");

        $table_data = new TableData();
        $view = "view_productos_$almacen";
        // error_log("Usando vista: $view");

        // Construir cláusula WHERE para filtrado

        $where = "";
        switch ($filter) {
            case 'JVC':
                $where = "codigo LIKE 'JVC%'";
                break;
            case 'IMPLE':
                $where = "codigo LIKE 'IMPLE%'";
                break;
            case 'CEP':
                $where = "codigo LIKE 'CEP%'";
                break;
            case 'PAD':
                $where = "codigo LIKE 'PAD%'";
                break;
            case 'PORT':
                $where = "codigo LIKE 'PORT%'";
                break;
            case 'ACC':
                $where = "codigo LIKE 'ACC%'";
                break;
        }
        // Agregar ORDER BY personalizado para mostrar JVC primero
        $orderBy = "ORDER BY CASE WHEN codigo LIKE 'JVC%' THEN 0 ELSE 1 END, codigo ASC";
        $result = $table_data->getAlmacen(
            $view,
            "id_producto",
            [
                "codigo",
                "nombre",
                "unidad",
                "precio_unidad",
                "cantidad",
                "id_producto",
                "id_producto",
                "moneda"
            ],
            false,
            $orderBy,
            $where
        );

        if (!$result) {
            // Log del error para debugging
            // error_log("Error en listaProductoServerSide: No se pudieron obtener datos de $view");
            echo json_encode([
                "sEcho" => intval($_GET['sEcho']),
                "iTotalRecords" => 0,
                "iTotalDisplayRecords" => 0,
                "aaData" => [],
                "error" => "No se pudieron obtener los datos"
            ]);
            exit;
        }

        echo json_encode($result);
        exit;
    }
    public function listaProducto()
    {
        $c_producto = new Producto();

        // Si no encuentra id_empresa en la sesión, usar el ID 12 como predeterminado
        $id_empresa = isset($_SESSION['id_empresa']) ? $_SESSION['id_empresa'] : 12;
        $c_producto->setIdEmpresa($id_empresa);

        // Verificar si almacenId existe en POST, si no, usar 1 como predeterminado
        $almacenId = isset($_POST['almacenId']) ? $_POST['almacenId'] : 1;

        // Log para depuración
        error_log("Buscando productos para empresa: $id_empresa, almacén: $almacenId");

        $a_productos = $c_producto->verFilas($almacenId);

        // Verificar si $a_productos es un objeto mysqli_result
        $lista = [];
        if ($a_productos && is_object($a_productos) && method_exists($a_productos, 'fetch_assoc')) {
            while ($row = $a_productos->fetch_assoc()) {
                $lista[] = $row;
            }
            error_log("Se encontraron " . count($lista) . " productos");
        } else {
            error_log("El resultado no es un objeto mysqli_result válido");
        }

        return json_encode($lista);
    }
    public function agregarPorLista()
    {
        $respuesta = ["res" => false, "error" => ""];

        try {
            // Iniciar transacción
            $this->conexion->begin_transaction();

            // Establecer un timeout más largo para la sesión actual

            // CAPTURAR EL ALMACÉN SELECCIONADO DESDE EL MODAL (no del Excel)
            $almacenDestino = isset($_POST['almacen']) ? intval($_POST['almacen']) : 1;

            $lista = json_decode($_POST['lista'], true);

            foreach ($lista as $item) {
                // Procesar unidad si existe en el Excel
                $unidadId = null;
                if (!empty($item['unidad'])) {
                    // Buscar unidad existente usando FOR UPDATE para bloqueo explícito
                    $sqlUnidad = "SELECT id FROM unidades WHERE LOWER(nombre) = LOWER(?) FOR UPDATE";
                    $stmt = $this->conexion->prepare($sqlUnidad);
                    $stmt->bind_param('s', $item['unidad']);
                    $stmt->execute();
                    $resultUnidad = $stmt->get_result();

                    if ($row = $resultUnidad->fetch_assoc()) {
                        $unidadId = $row['id'];
                    } else {
                        // Crear nueva unidad
                        $sqlNewUnidad = "INSERT INTO unidades (nombre) VALUES (?)";
                        $stmt = $this->conexion->prepare($sqlNewUnidad);
                        $stmt->bind_param('s', $item['unidad']);
                        $stmt->execute();
                        $unidadId = $this->conexion->insert_id;
                    }
                    $stmt->close();
                }

                // Procesar categoría si existe en el Excel
                $categoriaId = null;
                if (!empty($item['categoria'])) {
                    // Buscar categoría existente usando FOR UPDATE para bloqueo explícito
                    $sqlCategoria = "SELECT id FROM categorias WHERE LOWER(nombre) = LOWER(?) FOR UPDATE";
                    $stmt = $this->conexion->prepare($sqlCategoria);
                    $stmt->bind_param('s', $item['categoria']);
                    $stmt->execute();
                    $resultCategoria = $stmt->get_result();

                    if ($row = $resultCategoria->fetch_assoc()) {
                        $categoriaId = $row['id'];
                    } else {
                        // Crear nueva categoría
                        $sqlNewCategoria = "INSERT INTO categorias (nombre) VALUES (?)";
                        $stmt = $this->conexion->prepare($sqlNewCategoria);
                        $stmt->bind_param('s', $item['categoria']);
                        $stmt->execute();
                        $categoriaId = $this->conexion->insert_id;
                    }
                    $stmt->close();
                }

                // Valores por defecto
                $afect = isset($item['afecto']) ? ($item['afecto'] ? '1' : '0') : '0';
                $descripcion = isset($item['descripcicon']) ? $item['descripcicon'] : '';
                $codigoProd = isset($item['codigoProd']) ? $item['codigoProd'] : '';
                $codsunat = isset($item['codsunat']) ? $item['codsunat'] : '0';
                $nombre = isset($item['producto']) ? $item['producto'] : '';
                $precioUnidad = isset($item['precio_unidad']) ? floatval($item['precio_unidad']) : 0;
                $precioMayor = isset($item['precio_mayor']) ? floatval($item['precio_mayor']) : 0;
                $precioMenor = isset($item['precio_menor']) ? floatval($item['precio_menor']) : 0;
                // USAR EL ALMACÉN SELECCIONADO EN EL MODAL, NO EL DEL EXCEL
                $almacen = $almacenDestino;
                $costo = isset($item['costo']) ? floatval($item['costo']) : 0;
                $cantidad = isset($item['cantidad']) ? intval($item['cantidad']) : 0;
                $moneda = isset($item['moneda']) ? $item['moneda'] : 'PEN';

                // Verificar si el producto existe usando FOR UPDATE para bloqueo explícito
                // Filtrar por codigo, empresa, sucursal y almacen para evitar duplicados
                $sqlProducto = "SELECT * FROM productos WHERE codigo = ? AND id_empresa = ? AND sucursal = ? AND almacen = ? FOR UPDATE";
                $stmt = $this->conexion->prepare($sqlProducto);
                $stmt->bind_param('siii', $codigoProd, $_SESSION['id_empresa'], $_SESSION['sucursal'], $almacen);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $producto = $resultado->fetch_assoc();
                $stmt->close();

                if ($producto) {
                    // Actualizar producto existente
                    // CORRECCIÓN: Solo actualizar unidad y categoría si NO están vacías en el Excel
                    $updateProducto = "UPDATE productos SET
                        nombre = ?,
                        detalle = ?,
                        precio = ?,
                        precio_unidad = ?,
                        precio_mayor = ?,
                        precio_menor = ?,
                        precio2 = ?,
                        almacen = ?,
                        costo = ?,
                        cantidad = ?,
                        estado = '1',
                        moneda = ?";

                    // Agregar unidad solo si viene del Excel (no vacía)
                    if ($unidadId !== null) {
                        $updateProducto .= ", unidad = ?";
                    }

                    // Agregar categoría solo si viene del Excel (no vacía)
                    if ($categoriaId !== null) {
                        $updateProducto .= ", categoria = ?";
                    }

                    $updateProducto .= " WHERE codigo = ? AND id_empresa = ? AND sucursal = ? AND almacen = ?";

                    $stmt = $this->conexion->prepare($updateProducto);
                    if (!$stmt) {
                        throw new Exception("Error preparando actualización: " . $this->conexion->error);
                    }

                    // Construir parámetros dinámicamente
                    $params = [
                        $nombre,
                        $descripcion,
                        $precioUnidad,
                        $precioUnidad,
                        $precioMayor,
                        $precioMenor,
                        $precioMenor,
                        $almacen,
                        $costo,
                        $cantidad,
                        $moneda
                    ];

                    // Agregar unidad a parámetros si existe
                    if ($unidadId !== null) {
                        $params[] = $unidadId;
                    }

                    // Agregar categoría a parámetros si existe
                    if ($categoriaId !== null) {
                        $params[] = $categoriaId;
                    }

                    // Agregar código, empresa, sucursal y almacen al final (WHERE)
                    $params[] = $codigoProd;
                    $params[] = $_SESSION['id_empresa'];
                    $params[] = $_SESSION['sucursal'];
                    $params[] = $almacen;

                    // Crear tipos dinámicamente
                    $types = 'ssdddddidis'; // nombre, detalle, precio, precio_unidad, precio_mayor, precio_menor, precio2, almacen, costo, cantidad, moneda
                    if ($unidadId !== null) $types .= 'i';
                    if ($categoriaId !== null) $types .= 'i';
                    $types .= 's'; // codigo
                    $types .= 'iii'; // id_empresa, sucursal, almacen

                    $stmt->bind_param($types, ...$params);
                } else {
                    // Insertar nuevo producto
                    $sql = "INSERT INTO productos (
                        nombre, detalle, precio, precio_unidad, precio_mayor,
                        precio_menor, precio2, almacen, costo, cantidad, iscbp,
                        id_empresa, sucursal, codigo, ultima_salida,
                        codsunat, estado, unidad, categoria, moneda
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1', ?, ?, ?)";

                    $stmt = $this->conexion->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Error preparando inserción: " . $this->conexion->error);
                    }

                    $ultimaSalida = '1000-01-01';
                    $stmt->bind_param(
                        'ssdddddidisiisssiis',
                        $nombre,
                        $descripcion,
                        $precioUnidad,
                        $precioUnidad,
                        $precioMayor,
                        $precioMenor,
                        $precioMenor,
                        $almacen,
                        $costo,
                        $cantidad,
                        $afect,
                        $_SESSION['id_empresa'],
                        $_SESSION['sucursal'],
                        $codigoProd,
                        $ultimaSalida,
                        $codsunat,
                        $unidadId,
                        $categoriaId,
                        $moneda
                    );
                }

                if (!$stmt->execute()) {
                    throw new Exception("Error en la operación: " . $stmt->error);
                }
                $stmt->close();
            }

            // Confirmar la transacción
            $this->conexion->commit();
            $respuesta["res"] = true;

        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $this->conexion->rollback();
            $respuesta["error"] = "Error en la operación: " . $e->getMessage();
            error_log("Error en agregarPorLista: " . $e->getMessage());
        }

        return json_encode($respuesta);
    }


    public function importarExel()
    {
        $respuesta = ["res" => false];
        $filename = $_FILES['file']['name'];

        $path_parts = pathinfo($filename, PATHINFO_EXTENSION);
        $newName = Tools::getToken(80);
        /* Location */
        $loc_ruta = "files/temp";
        if (!file_exists($loc_ruta)) {
            mkdir($loc_ruta, 0777, true);
        }
        $location = $loc_ruta . "/" . $newName . '.' . $path_parts;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $location)) {
            $nombre_logo = $newName . "." . $path_parts;

            $respuesta["res"] = true;
            $type = $path_parts;

            if ($type == "xlsx") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
            } elseif ($type == "xls") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
            } elseif ($type == "csv") {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
            }

            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load("files/temp/" . $nombre_logo);

            $schdeules = $spreadsheet->getActiveSheet()->toArray();
            // array_shift($schdeules);
            $respuesta["data"] = $schdeules;

            unlink($location);
            //return $schdeules;
        }

        return json_encode($respuesta);
    }

    public function restock()
    {
        $respuesta = ["res" => false];
        $sql = "update productos set cantidad=cantidad+{$_POST['cantidad']} where id_producto='{$_POST['cod']}'";
        //echo $sql;
        if ($this->conexion->query($sql)) {
            $respuesta["res"] = true;
        }
        return json_encode($respuesta);
    }
    public function informacionPorCodigo()
    {
        $respuesta = ["res" => false];
        $sql = "SELECT * FROM productos where trim(codigo)='{$_POST['code']}' AND almacen = '{$_POST['almacen']}' and sucursal='{$_SESSION['sucursal']}'";

        if ($row = $this->conexion->query($sql)->fetch_assoc()) {
            $respuesta["res"] = true;
            $respuesta["data"] = $row;
        }
        return json_encode($respuesta);
    }
    public function informacion()
    {
        $respuesta = ["res" => false];
        $sql = "SELECT * FROM productos where id_producto='{$_POST['cod']}'";
        if ($row = $this->conexion->query($sql)->fetch_assoc()) {
            $respuesta["res"] = true;
            $respuesta["data"] = $row;
        }
        return json_encode($respuesta);
    }
    public function agregar()
    {
        $respuesta = ["res" => false];
        $codigoProd = $_POST['codigo'];
        $usar_multiprecio = isset($_POST['usar_multiprecio']) ? $_POST['usar_multiprecio'] : '0';
        $precios = isset($_POST['precios']) ? json_decode($_POST['precios'], true) : [];

        try {
            $this->conexion->begin_transaction();

            $nombreImagen = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $nombreImagen = ImageStorage::save($_FILES['imagen'], 'productos');
            }
            $codigoBarras = null;
            if (isset($_POST['usar_barra']) && $_POST['usar_barra'] == 1) {
                $codigoBarras = $_POST['codigo'];
            }

            // Manejar categoría y unidad nulas o vacías
            $categoria = (!empty($_POST['categoria']) && $_POST['categoria'] !== 'null') ? $_POST['categoria'] : null;
            $unidad = (!empty($_POST['unidad']) && $_POST['unidad'] !== 'null') ? $_POST['unidad'] : null;

            // Consulta SQL con manejo de imagen opcional y categorías nulas
            $sql = "INSERT INTO productos SET
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
                categoria= " . ($categoria ? "'$categoria'" : "NULL") . ",
                unidad= " . ($unidad ? "'$unidad'" : "NULL") . ",
                moneda= '{$_POST['moneda']}',
                usar_multiprecio = '{$usar_multiprecio}',
                 usar_barra = '" . (isset($_POST['usar_barra']) ? $_POST['usar_barra'] : '0') . "',
            cod_barra = " . ($codigoBarras ? "'{$codigoBarras}'" : "NULL") . ",
                codigo = ?";

            if ($nombreImagen) {
                $sql .= ", imagen = '{$nombreImagen}'";
            }

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('s', $codigoProd);

            if (!$stmt->execute()) {
                throw new Exception("Error al insertar producto: " . $stmt->error);
            }

            $id_producto = $this->conexion->insert_id;

            // Si usa multiprecio, guardar los precios
            if ($usar_multiprecio === '1' && !empty($precios)) {
                $sql = "INSERT INTO producto_precios (id_producto, nombre, precio) VALUES (?, ?, ?)";
                $stmt = $this->conexion->prepare($sql);

                foreach ($precios as $precio) {
                    $nombre = $precio['nombre'];
                    $valor = $precio['precio'];
                    $stmt->bind_param('iss', $id_producto, $nombre, $valor);
                    if (!$stmt->execute()) {
                        throw new Exception("Error al insertar precio: " . $stmt->error);
                    }
                }
            }

            $this->conexion->commit();
            $respuesta["res"] = true;

        } catch (Exception $e) {
            $this->conexion->rollback();
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }
    public function actualizar()
    {
        $respuesta = ["res" => false];

        try {
            $this->conexion->begin_transaction();

            // DEBUG: Log para verificar el contenido del campo detalle
            error_log("DETALLE RECIBIDO: '" . $_POST['detalle'] . "'");
            error_log("LONGITUD DETALLE: " . strlen($_POST['detalle']));

            // ✅ Obtener la cantidad anterior para comparar
            $sqlGetCantidad = "SELECT cantidad FROM productos WHERE id_producto = ?";
            $stmtGetCant = $this->conexion->prepare($sqlGetCantidad);
            $stmtGetCant->bind_param('i', $_POST['id_producto']);
            $stmtGetCant->execute();
            $resultCant = $stmtGetCant->get_result();
            $cantidadAnterior = 0;
            if ($rowCant = $resultCant->fetch_assoc()) {
                $cantidadAnterior = $rowCant['cantidad'];
            }
            $stmtGetCant->close();

            // Obtener la imagen actual antes de actualizarla
            $imagenAnterior = null;
            $sqlGetImagen = "SELECT imagen FROM productos WHERE id_producto = ?";
            $stmtGet = $this->conexion->prepare($sqlGetImagen);
            $stmtGet->bind_param('i', $_POST['id_producto']);
            $stmtGet->execute();
            $result = $stmtGet->get_result();

            if ($row = $result->fetch_assoc()) {
                $imagenAnterior = $row['imagen'];
            }

            $nombreImagen = null;
            $eliminarImagen = isset($_POST['eliminar_imagen']) && $_POST['eliminar_imagen'] === '1';

            if ($eliminarImagen) {
                $nombreImagen = 'NULL';
                ImageStorage::delete('productos', $imagenAnterior);
            } elseif (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $nombreImagen = ImageStorage::save($_FILES['imagen'], 'productos', $imagenAnterior);
            }

            $codigoBarras = null;

            if ($_POST['usar_barra'] == 1) {
                // Usar el código del producto como código de barras
                $codigoBarras = $_POST['codigo'];
            }


          // Construir la consulta SQL correctamente
$sqlImagenPart = "";
if ($eliminarImagen) {
    $sqlImagenPart = ", imagen = NULL";
} elseif ($nombreImagen && $nombreImagen !== 'NULL') {
    $sqlImagenPart = ", imagen = ?";
}

$sql = "UPDATE productos SET
    nombre = ?,
    codigo = ?,
    detalle = ?,
    categoria = ?,
    unidad = ?,
    precio = ?,
    costo = ?,
    almacen = ?,
    codsunat = ?,
    iscbp = ?,
    usar_barra = ?,
    cod_barra = ?,
    precio_mayor = ?,
    precio_menor = ?,
    precio2 = ?,
    precio3 = ?,
    precio4 = ?,
    precio_unidad = ?,
    cantidad = ?,
    razon_social = ?,
    ruc = ?,
    moneda = ?" . $sqlImagenPart . " WHERE id_producto = ?";


$stmt = $this->conexion->prepare($sql);

// Manejar categoría nula o vacía
$categoria = (!empty($_POST['categoria']) && $_POST['categoria'] !== 'null') ? $_POST['categoria'] : null;
$unidad = (!empty($_POST['unidad']) && $_POST['unidad'] !== 'null') ? $_POST['unidad'] : null;

// Crear array de parámetros - CORREGIDO: Preservar espacios en detalle y manejar categoría nula
$params = [
                $_POST['nombre'],
                $_POST['codigo'],
                $_POST['detalle'], // Este campo ahora preservará los espacios correctamente
                $categoria, // Manejar categoría nula correctamente
                $unidad, // Manejar unidad nula correctamente
                $_POST['precio'],
                $_POST['costo'],
                $_POST['almacen'],
                $_POST['codSunat'],
                $_POST['afecto'],
                $_POST['usar_barra'],
                $codigoBarras,
                $_POST['precioMayor'],
                $_POST['precioMenor'],
                $_POST['precioMenor'], // precio2
                $_POST['precio3'],
                $_POST['precio4'],
                $_POST['precio'],
                $_POST['cantidad'],
                $_POST['razon'],
                $_POST['ruc'],
                $_POST['moneda']
            ];
// Agregar imagen solo si no es NULL y no está vacía
if ($nombreImagen && $nombreImagen !== 'NULL') {
    $params[] = $nombreImagen;
}
$params[] = $_POST['cod'];

// Crear string de tipos basado en el número real de parámetros
$paramCount = count($params);

// CORREGIDO: Contar exactamente los tipos de datos
// s = string, d = double/decimal, i = integer
$types = 'sssiiiddisisddddddisss'; // 22 parámetros base

// Agregar tipo para imagen si existe
if ($nombreImagen && $nombreImagen !== 'NULL') {
    $types .= 's'; // imagen
}
$types .= 'i'; // id_producto (WHERE clause)

// Debug para verificar parámetros
error_log("Número de parámetros: " . $paramCount);
error_log("Tipos generados: " . $types);
error_log("Detalle enviado: " . $_POST['detalle']);

$stmt->bind_param($types, ...$params);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar producto: " . $stmt->error);
            }

            // ✅ Registrar cambio de stock en historial si hubo diferencia
            $cantidadNueva = intval($_POST['cantidad']);
            $diferencia = $cantidadNueva - $cantidadAnterior;
            
            if ($diferencia != 0) {
                $usuario = $_SESSION['usuario_id'] ?? 'Sistema';
                $tipoMovimiento = $diferencia > 0 ? 'INGRESO' : 'EGRESO';
                $cantidadMovimiento = abs($diferencia);
                $observacion = "Edición de producto (Stock anterior: {$cantidadAnterior}, Stock nuevo: {$cantidadNueva})";
                
                $sqlHistorial = "INSERT INTO historial_stock (id_producto, tipo_movimiento, cantidad, fecha_movimiento, usuario, observaciones) 
                                 VALUES (?, ?, ?, NOW(), ?, ?)";
                $stmtHist = $this->conexion->prepare($sqlHistorial);
                $stmtHist->bind_param('isiss', $_POST['id_producto'], $tipoMovimiento, $cantidadMovimiento, $usuario, $observacion);
                $stmtHist->execute();
                $stmtHist->close();
            }

            $this->conexion->commit();
            $respuesta["res"] = true;
            $respuesta["cod_barra"] = $codigoBarras; // devolver el nuevo código de barras

        } catch (Exception $e) {
            $this->conexion->rollback();
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }
    public function actualizarPrecios()
    {
        $respuesta = ["res" => false];
        $sql = "update productos set precio='{$_POST['precio']}',precio_unidad='{$_POST['precio_unidad']}', precio2='{$_POST['precio2']}', precio3='{$_POST['precio3']}', precio4='{$_POST['precio4']}' where id_producto='{$_POST['cod_prod']}'";
        if ($this->conexion->query($sql)) {
            $respuesta["res"] = true;
            $sql = "select * from productos where id_producto='{$_POST['cod_prod']}'";
            $result = $this->conexion->query($sql);
            if ($row = $result->fetch_assoc()) {
                $almacenTemp = $row["almacen"] == "1" ? 2 : 1;
                $sql = "update productos set 
                     precio='{$_POST['precio']}',precio_unidad='{$_POST['precio_unidad']}', 
                     precio2='{$_POST['precio2']}', precio3='{$_POST['precio3']}', 
                     precio4='{$_POST['precio4']}'
                  where descripcion=? and almacen='$almacenTemp'";
                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param('s', $row['descripcion']);
                /*   $stmt->bind_param('s', $codigoProd); */

                if (!$stmt->execute()) {
                }
            }
        }
        return json_encode($respuesta);
    }
    public function confirmarTraslado()
    {
        $respuesta['res'] = false;
        $sql = "SELECT id_producto,almacen_ingreso,almacen_egreso,cantidad FROM ingreso_egreso WHERE intercambio_id ='{$_POST['cod']}'";
        $result = $this->conexion->query($sql)->fetch_assoc();

        $almacen = $result['almacen_ingreso'];
        $id_producto = $result['id_producto'];
        $cantidad = $result['cantidad'];

        $sql = "SELECT * FROM productos WHERE id_producto = '{$result['id_producto']}'";
        $result = $this->conexion->query($sql)->fetch_assoc();


        $sql = "SELECT * FROM productos WHERE descripcion = '{$result['descripcion']}' AND almacen = '$almacen'";
        $result2 = $this->conexion->query($sql)->fetch_assoc();


        if (is_null($result2)) {
            $sql = "INSERT INTO productos 
            (cod_barra, descripcion, precio,categoria,unidad, costo,cantidad,iscbp,id_empresa,sucursal,ultima_salida,codsunat,usar_barra,precio_mayor,precio_menor,razon_social,ruc,estado,almacen,precio2,precio3)
            SELECT cod_barra, descripcion, precio,categoria,unidad, costo,$cantidad,iscbp,id_empresa,sucursal,ultima_salida,codsunat,usar_barra,precio_mayor,precio_menor,razon_social,ruc,estado, $almacen,precio2,precio3
            FROM productos
            WHERE id_producto = $id_producto";
            if ($this->conexion->query($sql)) {
                $sql = "UPDATE productos set cantidad = cantidad - $cantidad   WHERE id_producto = $id_producto";
                if ($this->conexion->query($sql)) {
                    $respuesta['res'] = true;
                }
            }
        } else {
            $idExistente = $result2['id_producto'];
            $sql2 = "UPDATE  productos set cantidad =  cantidad - $cantidad  WHERE id_producto = $id_producto";
            if ($this->conexion->query($sql2)) {
                $sql = "UPDATE  productos set cantidad = cantidad + $cantidad   WHERE id_producto = $idExistente";
                if ($this->conexion->query($sql)) {
                    $respuesta['res'] = true;
                }
            }
        }
        if ($respuesta['res']) {
            $sql = "UPDATE  ingreso_egreso set estado = 1   WHERE intercambio_id = '{$_POST['cod']}'";
            if ($this->conexion->query($sql)) {
                $respuesta['res'] = true;
            }
        }
        echo json_encode($respuesta);
    }
    public function delete()
    {
        $respuesta["res"] = true;
        $respuesta["data"] = $_POST;

        foreach ($respuesta["data"]['arrayId'] as $ids) {
            // Obtener la imagen del producto antes de eliminarlo
            $sqlGetImagen = "SELECT imagen FROM productos WHERE id_producto = '{$ids['id']}'";
            $resultado = $this->conexion->query($sqlGetImagen);

            if ($resultado && $row = $resultado->fetch_assoc()) {
                $imagenAnterior = $row['imagen'];
                if ($imagenAnterior) {
                    ImageStorage::delete('productos', $imagenAnterior);
                }
            }

            // Cambiar estado a 0 (eliminado lógico)
            $sql = "UPDATE productos set estado=0 where id_producto = '{$ids['id']}'";
            if ($this->conexion->query($sql)) {
                $respuesta["res"] = true;
            }
        }
        return json_encode($respuesta);
    }

    public function getCondicion()
    {
        $respuesta = [];
        $sql = "SELECT * FROM condicion";
        $resultado = $this->conexion->query($sql);
        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }
        return json_encode($respuesta);
    }

    // Método para guardar las condiciones predeterminadas
    public function saveCondicion()
    {
        $sql = "UPDATE condicion SET nombre='{$_POST["nombre"]}'";
        $this->conexion->query($sql);
    }

    public function getCondicionCotizacion($id_cotizacion = null)
    {
        // Verificar si el ID viene como parte de la URL (parámetro de ruta)
        if (!$id_cotizacion && isset($this->params['id'])) {
            $id_cotizacion = $_GET['id'];
        }
        // Verificar si el ID viene como parámetro GET
        else if (!$id_cotizacion && isset($_GET['id'])) {
            $id_cotizacion = $_GET['id'];
        }

        $respuesta = [];
        $sql = "SELECT * FROM condiciones_cotizacion WHERE id_cotizacion = '$id_cotizacion'";
        $resultado = $this->conexion->query($sql);

        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $respuesta[] = $row;
            }
        }

        return json_encode($respuesta);
    }
    // Nuevo método para guardar condiciones específicas de una cotización
    public function saveCondicionCotizacion()
    {
        $id_cotizacion = $_POST['cotizacion_id'];
        $condiciones = $_POST['condiciones'];

        // Verificar si ya existe una condición para esta cotización
        $sql = "SELECT * FROM condiciones_cotizacion WHERE id_cotizacion = '$id_cotizacion'";
        $resultado = $this->conexion->query($sql);

        if ($resultado->num_rows > 0) {
            // Actualizar condiciones existentes
            $sql = "UPDATE condiciones_cotizacion SET condiciones='$condiciones' WHERE id_cotizacion='$id_cotizacion'";
        } else {
            // Insertar nuevas condiciones
            $sql = "INSERT INTO condiciones_cotizacion (id_cotizacion, condiciones) VALUES ('$id_cotizacion', '$condiciones')";
        }

        $this->conexion->query($sql);
        return json_encode(['success' => true]);
    }

    // Nuevo método para guardar condiciones temporales en sesión
    public function saveCondicionTemp()
    {
        $_SESSION['temp_condiciones'] = $_POST['condiciones'];
        return json_encode(['success' => true]);
    }
    // diagnostico
    public function getDiagnostico()
    {
        $resouesta = [];
        $sql = "SELECT * FROM diagnostico";
        $resultado = $this->conexion->query($sql);
        if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
                $resouesta[] = $row;
            }
        }
        return json_encode($resouesta);
    }

    public function saveDiagnostico()
    {
        $sql = "UPDATE diagnostico SET detalle='{$_POST["detalle"]}'";
        $this->conexion->query($sql);
    }
    // Método para guardar las condiciones predeterminadas
    public function saveCondicionDefault()
    {
        if (!isset($_POST['nombre'])) {
            return json_encode(['success' => false, 'message' => 'No se proporcionaron condiciones']);
        }

        $condiciones = $this->conexion->real_escape_string($_POST['nombre']);

        // Verificar si ya existe un registro en la tabla condicion
        $sql = "SELECT * FROM condicion LIMIT 1";
        $resultado = $this->conexion->query($sql);

        if ($resultado->num_rows > 0) {
            // Actualizar el registro existente
            $sql = "UPDATE condicion SET nombre='$condiciones'";
        } else {
            // Insertar un nuevo registro
            $sql = "INSERT INTO condicion (nombre) VALUES ('$condiciones')";
        }

        if ($this->conexion->query($sql)) {
            return json_encode(['success' => true]);
        } else {
            return json_encode(['success' => false, 'message' => 'Error al guardar: ' . $this->conexion->error]);
        }
    }



    public function guardarPrecios()
    {
        $respuesta = ["res" => false];
        $id_producto = $_POST['id_producto'];
        $precios = isset($_POST['precios']) ? $_POST['precios'] : [];

        try {
            // Iniciar transacción
            $this->conexion->begin_transaction();

            // Eliminar precios existentes
            $sql = "DELETE FROM producto_precios WHERE id_producto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('i', $id_producto);
            $stmt->execute();

            // Insertar nuevos precios
            $sql = "INSERT INTO producto_precios (id_producto, nombre, precio) VALUES (?, ?, ?)";
            $stmt = $this->conexion->prepare($sql);
            if (!empty($precios) && is_array($precios)) {
                foreach ($precios as $precio) {
                    $nombre = $precio['nombre'];
                    $valor = $precio['precio'];
                    $stmt->bind_param('iss', $id_producto, $nombre, $valor);
                    $stmt->execute();
                }
            }

            // Actualizar el campo usar_multiprecio en la tabla productos
            $sql = "UPDATE productos SET usar_multiprecio = '1' WHERE id_producto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('i', $id_producto);
            $stmt->execute();

            // Confirmar transacción
            $this->conexion->commit();
            $respuesta["res"] = true;

        } catch (Exception $e) {
            // Revertir en caso de error
            $this->conexion->rollback();
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function obtenerPrecios()
    {
        $respuesta = ["res" => false, "precios" => []];
        $id_producto = $_POST['id_producto'];

        try {
            $sql = "SELECT * FROM producto_precios WHERE id_producto = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('i', $id_producto);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $respuesta["precios"][] = [
                        "id" => $row['id'],
                        "nombre" => $row['nombre'],
                        "precio" => $row['precio']
                    ];
                }
                $respuesta["res"] = true;
            }
        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }
    public function aumentarStock()
    {
        $respuesta = ["res" => false];

        try {
            $producto_id = $_POST['producto_id'];
            $cantidad = intval($_POST['cantidad']);
            $costo_compra = isset($_POST['costo_compra']) ? floatval($_POST['costo_compra']) : null;
            $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : null;
            $fecha_actual = date('Y-m-d H:i:s');

            // Actualizar stock del producto
            $sql = "UPDATE productos SET 
                cantidad = cantidad + ?, 
                fecha_ultimo_ingreso = ?
                WHERE id_producto = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('isi', $cantidad, $fecha_actual, $producto_id);

            if ($stmt->execute()) {
                // Registrar el movimiento en historial con costo y observaciones
                $sql_historial = "INSERT INTO historial_stock 
                             (id_producto, tipo_movimiento, cantidad, costo_compra, fecha_movimiento, usuario, observaciones) 
                             VALUES (?, 'INGRESO', ?, ?, ?, ?, ?)";

                $stmt_hist = $this->conexion->prepare($sql_historial);
                $usuario = $_SESSION['usuario_id'] ?? 'Sistema';
                $stmt_hist->bind_param('iidsss', $producto_id, $cantidad, $costo_compra, $fecha_actual, $usuario, $observaciones);
                $stmt_hist->execute();

                $respuesta["res"] = true;
            }

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function disminuirStock()
    {
        $respuesta = ["res" => false];

        try {
            $producto_id = $_POST['producto_id'];
            $cantidad = intval($_POST['cantidad']);
            $observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : null;
            $fecha_actual = date('Y-m-d H:i:s');

            // Verificar que hay stock suficiente
            $sql_check = "SELECT cantidad FROM productos WHERE id_producto = ?";
            $stmt_check = $this->conexion->prepare($sql_check);
            $stmt_check->bind_param('i', $producto_id);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $producto = $result->fetch_assoc();

            if ($producto['cantidad'] < $cantidad) {
                $respuesta["error"] = "Stock insuficiente";
                return json_encode($respuesta);
            }

            // Actualizar stock del producto (restar)
            $sql = "UPDATE productos SET 
                cantidad = cantidad - ?
                WHERE id_producto = ?";

            $stmt = $this->conexion->prepare($sql);
            $stmt->bind_param('ii', $cantidad, $producto_id);

            if ($stmt->execute()) {
                // Registrar el movimiento en historial como EGRESO
                $sql_historial = "INSERT INTO historial_stock 
                             (id_producto, tipo_movimiento, cantidad, fecha_movimiento, usuario, observaciones) 
                             VALUES (?, 'EGRESO', ?, ?, ?, ?)";

                $stmt_hist = $this->conexion->prepare($sql_historial);
                $usuario = $_SESSION['usuario_id'] ?? 'Sistema';
                $stmt_hist->bind_param('iisss', $producto_id, $cantidad, $fecha_actual, $usuario, $observaciones);
                $stmt_hist->execute();

                $respuesta["res"] = true;
            }

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function trasladoAlmacenes()
    {
        $respuesta = ["res" => false];

        try {
            $almacen_origen = $_POST['almacen_origen'];
            $almacen_destino = $_POST['almacen_destino'];
            $productos = $_POST['productos'];
            $nota = isset($_POST['nota']) ? $_POST['nota'] : '';
            $fecha_actual = date('Y-m-d H:i:s');

            // Validar que origen y destino sean diferentes
            if ($almacen_origen == $almacen_destino) {
                $respuesta["error"] = "El almacén de origen y destino no pueden ser el mismo";
                return json_encode($respuesta);
            }

            // Iniciar transacción
            $this->conexion->begin_transaction();

            foreach ($productos as $producto) {
                $producto_id = $producto['id_producto'];
                $cantidad = intval($producto['cantidad']);

                // Obtener datos del producto origen (incluyendo código)
                $sql_get_origen = "SELECT id_producto, codigo, cantidad FROM productos WHERE id_producto = ? AND almacen = ?";
                $stmt_get_origen = $this->conexion->prepare($sql_get_origen);
                $stmt_get_origen->bind_param('ii', $producto_id, $almacen_origen);
                $stmt_get_origen->execute();
                $result_origen = $stmt_get_origen->get_result();
                $prod_origen = $result_origen->fetch_assoc();

                if (!$prod_origen || $prod_origen['cantidad'] < $cantidad) {
                    $this->conexion->rollback();
                    $respuesta["error"] = "Stock insuficiente en almacén origen para el producto ID: " . $producto_id;
                    return json_encode($respuesta);
                }

                $codigo_producto = $prod_origen['codigo'];

                // Disminuir stock en almacén origen
                $sql_origen = "UPDATE productos SET cantidad = cantidad - ? WHERE id_producto = ? AND almacen = ?";
                $stmt_origen = $this->conexion->prepare($sql_origen);
                $stmt_origen->bind_param('iii', $cantidad, $producto_id, $almacen_origen);
                $stmt_origen->execute();

                // Registrar EGRESO en historial
                $sql_hist_egreso = "INSERT INTO historial_stock 
                             (id_producto, tipo_movimiento, cantidad, fecha_movimiento, usuario, observaciones) 
                             VALUES (?, 'EGRESO', ?, ?, ?, ?)";
                $stmt_hist_egreso = $this->conexion->prepare($sql_hist_egreso);
                $usuario = $_SESSION['usuario_id'] ?? 'Sistema';
                $obs_egreso = "Traslado de Almacén $almacen_origen a Almacén $almacen_destino. " . $nota;
                $stmt_hist_egreso->bind_param('iisss', $producto_id, $cantidad, $fecha_actual, $usuario, $obs_egreso);
                $stmt_hist_egreso->execute();

                // Buscar si el producto existe en almacén destino (por CODIGO, no por id_producto)
                $sql_check_destino = "SELECT id_producto, cantidad FROM productos WHERE codigo = ? AND almacen = ? AND id_empresa = ?";
                $stmt_check_destino = $this->conexion->prepare($sql_check_destino);
                $id_empresa = $_SESSION['id_empresa'];
                $stmt_check_destino->bind_param('sii', $codigo_producto, $almacen_destino, $id_empresa);
                $stmt_check_destino->execute();
                $result_destino = $stmt_check_destino->get_result();
                $prod_destino = $result_destino->fetch_assoc();

                if ($prod_destino) {
                    // Aumentar stock en almacén destino (usando el id_producto del destino)
                    $id_producto_destino = $prod_destino['id_producto'];
                    $sql_destino = "UPDATE productos SET cantidad = cantidad + ?, fecha_ultimo_ingreso = ? WHERE id_producto = ? AND almacen = ?";
                    $stmt_destino = $this->conexion->prepare($sql_destino);
                    $stmt_destino->bind_param('isii', $cantidad, $fecha_actual, $id_producto_destino, $almacen_destino);
                    $stmt_destino->execute();

                    // Registrar INGRESO en historial con el id_producto del destino
                    $sql_hist_ingreso = "INSERT INTO historial_stock 
                                 (id_producto, tipo_movimiento, cantidad, fecha_movimiento, usuario, observaciones) 
                                 VALUES (?, 'INGRESO', ?, ?, ?, ?)";
                    $stmt_hist_ingreso = $this->conexion->prepare($sql_hist_ingreso);
                    $obs_ingreso = "Traslado desde Almacén $almacen_origen a Almacén $almacen_destino. " . $nota;
                    $stmt_hist_ingreso->bind_param('iisss', $id_producto_destino, $cantidad, $fecha_actual, $usuario, $obs_ingreso);
                    $stmt_hist_ingreso->execute();
                } else {
                    // Crear registro en almacén destino (copiar del origen)
                    $sql_copiar = "INSERT INTO productos (cod_barra, nombre, precio, costo, cantidad, iscbp, id_empresa, sucursal, ultima_salida, codsunat, usar_barra, usar_multiprecio, precio_mayor, precio_menor, razon_social, ruc, estado, almacen, precio2, precio3, precio4, precio_unidad, codigo, imagen, detalle, categoria, descripcion, unidad, moneda, fecha_ultimo_ingreso)
                                   SELECT cod_barra, nombre, precio, costo, ?, iscbp, id_empresa, sucursal, ultima_salida, codsunat, usar_barra, usar_multiprecio, precio_mayor, precio_menor, razon_social, ruc, estado, ?, precio2, precio3, precio4, precio_unidad, codigo, imagen, detalle, categoria, descripcion, unidad, moneda, ?
                                   FROM productos WHERE id_producto = ? AND almacen = ? LIMIT 1";
                    $stmt_copiar = $this->conexion->prepare($sql_copiar);
                    $stmt_copiar->bind_param('iisii', $cantidad, $almacen_destino, $fecha_actual, $producto_id, $almacen_origen);
                    $stmt_copiar->execute();

                    // Obtener el id del producto recién insertado
                    $nuevo_id_producto = $this->conexion->insert_id;

                    // Registrar INGRESO en historial con el nuevo id_producto
                    $sql_hist_ingreso = "INSERT INTO historial_stock 
                                 (id_producto, tipo_movimiento, cantidad, fecha_movimiento, usuario, observaciones) 
                                 VALUES (?, 'INGRESO', ?, ?, ?, ?)";
                    $stmt_hist_ingreso = $this->conexion->prepare($sql_hist_ingreso);
                    $obs_ingreso = "Traslado desde Almacén $almacen_origen a Almacén $almacen_destino. " . $nota;
                    $stmt_hist_ingreso->bind_param('iisss', $nuevo_id_producto, $cantidad, $fecha_actual, $usuario, $obs_ingreso);
                    $stmt_hist_ingreso->execute();
                }
            }

            // Confirmar transacción
            $this->conexion->commit();
            $respuesta["res"] = true;

        } catch (Exception $e) {
            $this->conexion->rollback();
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }

    public function obtenerHistorialStock()
    {
        $respuesta = ["res" => false, "data" => []];

        try {
            $producto_id = isset($_POST['producto_id']) ? $_POST['producto_id'] : null;

            if ($producto_id) {
                // Historial de un producto específico
                $sql = "SELECT h.*, p.nombre as producto_nombre, p.codigo,
                        CASE 
                            WHEN h.usuario REGEXP '^[0-9]+$' THEN 
                                CONCAT(COALESCE(u.nombres,''), ' ', COALESCE(u.apellidos,''))
                            ELSE h.usuario 
                        END AS usuario_nombre
                    FROM historial_stock h 
                    INNER JOIN productos p ON h.id_producto = p.id_producto 
                    LEFT JOIN usuarios u ON u.usuario_id = h.usuario
                    WHERE h.id_producto = ? 
                    ORDER BY h.fecha_movimiento DESC";

                $stmt = $this->conexion->prepare($sql);
                $stmt->bind_param('i', $producto_id);
            } else {
                // Historial general (últimos 100 movimientos)
                $sql = "SELECT h.*, p.nombre as producto_nombre, p.codigo,
                        CASE 
                            WHEN h.usuario REGEXP '^[0-9]+$' THEN 
                                CONCAT(COALESCE(u.nombres,''), ' ', COALESCE(u.apellidos,''))
                            ELSE h.usuario 
                        END AS usuario_nombre
                    FROM historial_stock h 
                    INNER JOIN productos p ON h.id_producto = p.id_producto 
                    LEFT JOIN usuarios u ON u.usuario_id = h.usuario
                    ORDER BY h.fecha_movimiento DESC 
                    LIMIT 100";

                $stmt = $this->conexion->prepare($sql);
            }

            $stmt->execute();
            $resultado = $stmt->get_result();

            while ($row = $resultado->fetch_assoc()) {
                $respuesta["data"][] = [
                    "id" => $row['id'],
                    "producto_nombre" => $row['producto_nombre'],
                    "codigo" => $row['codigo'],
                    "tipo_movimiento" => $row['tipo_movimiento'],
                    "cantidad" => $row['cantidad'],
                    "costo_compra" => $row['costo_compra'],
                    "fecha_movimiento" => $row['fecha_movimiento'],
                    "usuario" => !empty($row['usuario_nombre']) ? trim($row['usuario_nombre']) : $row['usuario'],
                    "observaciones" => $row['observaciones']
                ];
            }

            $respuesta["res"] = true;

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }
    public function productosGrid()
    {
        $respuesta = ["res" => false, "data" => [], "total" => 0];

        try {
            $almacenId = isset($_POST['almacenId']) ? $_POST['almacenId'] : 1;
            $page = isset($_POST['page']) ? intval($_POST['page']) : 1;
            $limit = isset($_POST['limit']) ? intval($_POST['limit']) : 12;
            $search = isset($_POST['search']) ? $_POST['search'] : '';
            $filter = isset($_POST['filter']) ? $_POST['filter'] : '';

            $offset = ($page - 1) * $limit;

            // Construir WHERE clause
            $whereConditions = [];
            $whereConditions[] = "id_empresa = '{$_SESSION['id_empresa']}'";
            $whereConditions[] = "sucursal = '{$_SESSION['sucursal']}'";
            $whereConditions[] = "estado = '1'";
            $whereConditions[] = "almacen = '$almacenId'";

            if (!empty($search)) {
                $whereConditions[] = "(nombre LIKE '%$search%' OR codigo LIKE '%$search%')";
            }

            if (!empty($filter)) {
                $whereConditions[] = "codigo LIKE '$filter%'";
            }

            $whereClause = "WHERE " . implode(" AND ", $whereConditions);

            // Contar total de productos
            $sqlCount = "SELECT COUNT(*) as total FROM productos $whereClause";
            $resultCount = $this->conexion->query($sqlCount);
            $totalRow = $resultCount->fetch_assoc();
            $respuesta["total"] = intval($totalRow['total']);

            // Obtener productos con paginación
            $sql = "SELECT p.*, u.nombre as unidad_nombre 
                FROM productos p 
                LEFT JOIN unidades u ON p.unidad = u.id 
                $whereClause 
                ORDER BY CASE WHEN p.codigo LIKE 'JVC%' THEN 0 ELSE 1 END, p.codigo ASC 
                LIMIT $limit OFFSET $offset";

            $resultado = $this->conexion->query($sql);

            if ($resultado && $resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $respuesta["data"][] = $row;
                }
                $respuesta["res"] = true;
            } else if ($resultado) {
                $respuesta["res"] = true; // Sin productos, pero consulta exitosa
            }

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        return json_encode($respuesta);
    }



}

