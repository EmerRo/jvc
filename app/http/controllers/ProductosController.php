<?php

require_once "utils/lib/exel/vendor/autoload.php";
require_once "app/models/Producto.php";


class ProductosController extends Controller
{
    private $conexion;
    private $c_producto;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();

        /*   $c_producto->setIdEmpresa($_SESSION['id_empresa']); */
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

        error_log("Buscando productos con almacen: $almacen y filtro: $filter");

        $table_data = new TableData();
        $view = "view_productos_$almacen";
        error_log("Usando vista: $view");

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
                "id_producto"
            ],
            false,
            $orderBy,
            $where
        );

        if (!$result) {
            // Log del error para debugging
            error_log("Error en listaProductoServerSide: No se pudieron obtener datos de $view");
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
                $precio = isset($item['precio_unidad']) ? floatval($item['precio_unidad']) : 0;
                $precio2 = isset($item['precio2']) ? floatval($item['precio2']) : 0;
                $almacen = isset($item['almacen']) ? intval($item['almacen']) : 1;
                $precioUnidad = isset($item['precio_unidad']) ? floatval($item['precio_unidad']) : 0;
                $costo = isset($item['costo']) ? floatval($item['costo']) : 0;
                $cantidad = isset($item['cantidad']) ? intval($item['cantidad']) : 0;

                // Verificar si el producto existe usando FOR UPDATE para bloqueo explícito
                $sqlProducto = "SELECT * FROM productos WHERE codigo = ? FOR UPDATE";
                $stmt = $this->conexion->prepare($sqlProducto);
                $stmt->bind_param('s', $codigoProd);
                $stmt->execute();
                $resultado = $stmt->get_result();
                $producto = $resultado->fetch_assoc();
                $stmt->close();

                if ($producto) {
                    // Actualizar producto existente
                    $updateProducto = "UPDATE productos SET 
                        nombre = ?,
                        detalle = ?,
                        precio = ?,
                        precio2 = ?,
                        almacen = ?,
                        precio_unidad = ?,
                        costo = ?,
                        cantidad = ?,
                        estado = '1',
                        unidad = ?,
                        categoria = ?
                        WHERE codigo = ?";

                    $stmt = $this->conexion->prepare($updateProducto);
                    if (!$stmt) {
                        throw new Exception("Error preparando actualización: " . $this->conexion->error);
                    }

                    $stmt->bind_param(
                        'ssddsdddiii',
                        $nombre,
                        $descripcion,
                        $precio,
                        $precio2,
                        $almacen,
                        $precioUnidad,
                        $costo,
                        $cantidad,
                        $unidadId,
                        $categoriaId,
                        $codigoProd
                    );
                } else {
                    // Insertar nuevo producto
                    $sql = "INSERT INTO productos (
                        nombre, detalle, precio, precio2, almacen, 
                        precio_unidad, costo, cantidad, iscbp, 
                        id_empresa, sucursal, codigo, ultima_salida,
                        codsunat, estado, unidad, categoria
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '1', ?, ?)";

                    $stmt = $this->conexion->prepare($sql);
                    if (!$stmt) {
                        throw new Exception("Error preparando inserción: " . $this->conexion->error);
                    }

                    $ultimaSalida = '1000-01-01';
                    $stmt->bind_param(
                        'ssddsdddsissssii',
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
                        $codigoProd,
                        $ultimaSalida,
                        $codsunat,
                        $unidadId,
                        $categoriaId
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

            // Manejo de la imagen
            $nombreImagen = null;
            $rutaDestino = '';

            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $imagen = $_FILES['imagen'];
                $nombreImagen = $imagen['name'];
                $rutaDestino = 'public/img/productos/' . $nombreImagen;

                if (!move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
                    throw new Exception("Error al subir la imagen");
                }
            }
            $codigoBarras = null;
            if (isset($_POST['usar_barra']) && $_POST['usar_barra'] == 1) {
                $codigoBarras = $_POST['codigo'];
            }

            // Consulta SQL con manejo de imagen opcional
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
                categoria= '{$_POST['categoria']}',  
                unidad= '{$_POST['unidad']}',
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

            // Manejo de imagen
            $nombreImagen = null;
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                $imagen = $_FILES['imagen'];
                $nombreImagen = time() . '_' . $imagen['name']; // Nombre único
                $rutaDestino = 'public/img/productos/' . $nombreImagen;

                if (!move_uploaded_file($imagen['tmp_name'], $rutaDestino)) {
                    throw new Exception("Error al subir la imagen");
                }
            }
            $codigoBarras = null;
            $codigoBarras = null;
            if ($_POST['usar_barra'] == 1) {
                // Usar el código del producto como código de barras
                $codigoBarras = $_POST['codigo'];
            }


            // Actualizar producto principal
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
                cod_barra =?,
                precio_mayor = ?,
                precio_menor = ?,
                precio2 = ?,
                precio3 = ?,
                precio4 = ?,
                precio_unidad = ?,
                cantidad = ?,
                razon_social = ?,
                ruc = ?" .
                ($nombreImagen ? ", imagen = ?" : "") .
                " WHERE id_producto = ?";

            $stmt = $this->conexion->prepare($sql);

            // Crear array de parámetros
            $params = [
                $_POST['nombre'],
                $_POST['codigo'],
                $_POST['detalle'],
                $_POST['categoria'],
                $_POST['unidad'],
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
                $_POST['ruc']
            ];

            if ($nombreImagen) {
                $params[] = $nombreImagen;
            }
            $params[] = $_POST['cod'];

            // Crear string de tipos
            $types = str_repeat('s', count($params));

            $stmt->bind_param($types, ...$params);

            if (!$stmt->execute()) {
                throw new Exception("Error al actualizar producto: " . $stmt->error);
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
        $sql = '';
        foreach ($respuesta["data"]['arrayId'] as $ids) {
            /*   $sql .= $ids; */

            $sql = "UPDATE   productos set estado=0 where id_producto = '{$ids['id']}'";
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
        $precios = $_POST['precios'];

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

            foreach ($precios as $precio) {
                $nombre = $precio['nombre'];
                $valor = $precio['precio'];
                $stmt->bind_param('iss', $id_producto, $nombre, $valor);
                $stmt->execute();
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
        $fecha_actual = date('Y-m-d H:i:s');
        
        // Actualizar stock del producto
        $sql = "UPDATE productos SET 
                cantidad = cantidad + ?, 
                fecha_ultimo_ingreso = ?
                WHERE id_producto = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param('isi', $cantidad, $fecha_actual, $producto_id);
        
        if ($stmt->execute()) {
            // Registrar el movimiento en historial (opcional)
            $sql_historial = "INSERT INTO historial_stock 
                             (id_producto, tipo_movimiento, cantidad, fecha_movimiento, usuario) 
                             VALUES (?, 'INGRESO', ?, ?, ?)";
            
            $stmt_hist = $this->conexion->prepare($sql_historial);
            $usuario = $_SESSION['usuario'] ?? 'Sistema';
            $stmt_hist->bind_param('iiss', $producto_id, $cantidad, $fecha_actual, $usuario);
            $stmt_hist->execute();
            
            $respuesta["res"] = true;
        }
        
    } catch (Exception $e) {
        $respuesta["error"] = $e->getMessage();
    }
    
    return json_encode($respuesta);
}

}

