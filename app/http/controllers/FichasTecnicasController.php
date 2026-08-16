<?php

require_once 'app/helpers/ImageStorage.php';
require_once "app/models/GestionArchivo.php";
require_once "app/models/GestionAdjunto.php";
require_once "app/models/Producto.php";

class FichasTecnicasController extends Controller
{
    private $conexion;
    private $archivosNuevosActualizacion = [];
    private $archivosAntiguosPendientes = [];

    public function __construct()
    {
        // NUEVO: Suprimir notices para evitar que interfieran con JSON
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        
        $this->conexion = (new Conexion())->getConexion();
    }



    public function listarFichas()
    {
        // NUEVO: Limpiar cualquier salida previa y establecer headers
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        // Inicializar respuesta con res=true por defecto
        $respuesta = ["res" => true, "fichas" => []];

        try {
            $termino = isset($_POST['termino']) ? $_POST['termino'] : null;
            $id_producto = isset($_POST['id_producto']) ? $_POST['id_producto'] : null;

            $id_empresa = isset($_SESSION['id_empresa']) ? $_SESSION['id_empresa'] : 12;
            $sucursal = isset($_SESSION['sucursal']) ? $_SESSION['sucursal'] : 1;

            // NUEVO: Consulta optimizada con JOIN en lugar de subconsultas
            $sql = "SELECT a.id_archivo, a.numero, a.titulo, a.id_producto, a.fecha_creacion, a.fecha_actualizacion,
                           p.nombre as nombre_producto,
                           ga.url_pdf, ga.url_editable, ga.url_imagen, ga.url_youtube,
                           ga.url_imagen_2, ga.url_imagen_3
                    FROM gestion_archivos a 
                    LEFT JOIN productos p ON a.id_producto = p.id_producto
                    LEFT JOIN gestion_adjuntos ga ON a.id_archivo = ga.id_archivo
                    WHERE a.tipo = 'ficha_tecnica'";

            $params = [];
            $types = "";

            if ($termino) {
                $sql .= " AND (a.titulo LIKE ? OR p.nombre LIKE ?)";
                $terminoLike = "%$termino%";
                $params[] = $terminoLike;
                $params[] = $terminoLike;
                $types .= "ss";
            }

            if ($id_producto) {
                $sql .= " AND a.id_producto = ?";
                $params[] = $id_producto;
                $types .= "i";
            }

            $sql .= " ORDER BY a.fecha_creacion DESC";

            $stmt = $this->conexion->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar la consulta: " . $this->conexion->error);
            }
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $resultado = $stmt->get_result();

            $fichas = [];
            $fichasProcesadas = [];
            
            while ($fila = $resultado->fetch_assoc()) {
                $id_archivo = $fila['id_archivo'];
                
                // NUEVO: Procesar solo una vez por archivo
                if (isset($fichasProcesadas[$id_archivo])) {
                    continue;
                }
                
                $fichasProcesadas[$id_archivo] = true;
                
                // NUEVO: Organizar adjuntos directamente desde la consulta
                $pdf = null;
                $editable = null;
                $imagenes = [];
                $youtube = null;

                if ($fila['url_pdf']) {
                    $pdf = ['url' => $fila['url_pdf']];
                }
                
                if ($fila['url_editable']) {
                    $editable = ['url' => $fila['url_editable']];
                }
                
                if ($fila['url_imagen']) {
                    $imagenes[] = ['url' => ImageStorage::url('fichas-tecnicas', $fila['url_imagen'])];
                }
                
                if ($fila['url_imagen_2']) {
                    $imagenes[] = ['url' => ImageStorage::url('fichas-tecnicas', $fila['url_imagen_2'])];
                }
                
                if ($fila['url_imagen_3']) {
                    $imagenes[] = ['url' => ImageStorage::url('fichas-tecnicas', $fila['url_imagen_3'])];
                }
                
                if ($fila['url_youtube']) {
                    $youtube = ['url' => $fila['url_youtube']];
                }

                $fichas[] = [
                    "id_archivo" => $id_archivo,
                    "numero" => $fila['numero'],
                    "titulo" => $fila['titulo'],
                    "id_producto" => $fila['id_producto'],
                    "nombre_producto" => $fila['nombre_producto'],
                    "fecha_creacion" => $fila['fecha_creacion'],
                    "fecha_actualizacion" => $fila['fecha_actualizacion'],
                    "adjuntos" => [
                        "pdf" => $pdf,
                        "editable" => $editable,
                        "imagenes" => $imagenes,
                        "youtube" => $youtube
                    ]
                ];
            }

            $respuesta["fichas"] = $fichas;
            $stmt->close();

        } catch (Exception $e) {
            $respuesta["res"] = false;
            $respuesta["error"] = $e->getMessage();
        }

        echo json_encode($respuesta);
        exit;
    }

    public function guardarFicha()
    {
        // Limpiar cualquier salida previa y establecer headers
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        $respuesta = ["res" => false];

        try {
            $this->validarLimiteSolicitud();
            $this->validarArchivoGestion($_FILES['pdf'] ?? null, 'pdf', true);
            $this->validarArchivoGestion($_FILES['editable'] ?? null, 'editable', false);
            $this->conexion->begin_transaction();

            $eliminarPdf = $this->obtenerBanderaEliminacion('eliminar_pdf');
            $eliminarEditable = $this->obtenerBanderaEliminacion('eliminar_editable');
            $hayPdf = isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK;

            if ($eliminarPdf || $eliminarEditable) {
                throw new Exception("No se pueden eliminar adjuntos al crear una ficha técnica");
            }

            if (!$hayPdf) {
                throw new Exception("El archivo PDF es obligatorio");
            }

            // Datos de la ficha técnica
            $titulo = $_POST['titulo'];
            $id_producto = isset($_POST['id_producto']) && !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;

            // Crear el archivo
            $archivo = new GestionArchivo();
            $archivo->setTitulo($titulo);
            $archivo->setTipo('ficha_tecnica');
            $archivo->setIdProducto($id_producto);
            $archivo->setVersion('1.0');
            
            // Establecer empresa y sucursal usando los setters
            $id_empresa = isset($_SESSION['id_empresa']) ? $_SESSION['id_empresa'] : 12;
            $sucursal = isset($_SESSION['sucursal']) ? $_SESSION['sucursal'] : '1';
            
            $archivo->setIdEmpresa($id_empresa);
            $archivo->setSucursal($sucursal);
            $archivo->setNumero($archivo->generarSiguienteNumero());

            if (!$archivo->insertar()) {
                // Obtener información detallada del error
                $error = $this->conexion->error;
                $errno = $this->conexion->errno;
                throw new Exception("Error al crear la ficha técnica. MySQL Error $errno: $error");
            }

            $id_archivo = $archivo->getIdArchivo();
            
            // Verificar que se obtuvo el ID
            if (!$id_archivo) {
                throw new Exception("Error: No se pudo obtener el ID del archivo creado");
            }

            // Procesar adjuntos usando los nuevos campos
            $adjuntos = $this->procesarAdjuntos($id_archivo);

            $this->conexion->commit();

            $respuesta = [
                "res" => true,
                "id_archivo" => $id_archivo,
                "numero" => $archivo->getNumero(),
                "mensaje" => "Ficha técnica guardada correctamente",
                "adjuntos" => $adjuntos
            ];

        } catch (Exception $e) {
            $this->conexion->rollback();
            $respuesta["error"] = $e->getMessage();
        }

        echo json_encode($respuesta);
        exit;
    }

    public function actualizarFicha()
    {
        // Limpiar cualquier salida previa y establecer headers
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        $respuesta = ["res" => false];

        $this->archivosNuevosActualizacion = [];
        $this->archivosAntiguosPendientes = [];

        try {
            $this->validarLimiteSolicitud();
            $this->validarArchivoGestion($_FILES['pdf'] ?? null, 'pdf', false);
            $this->validarArchivoGestion($_FILES['editable'] ?? null, 'editable', false);
            $this->conexion->begin_transaction();

            // Obtener el ID de la ficha a actualizar
            $id_ficha = isset($_POST['id_ficha']) ? $_POST['id_ficha'] : null;
            
            if (!$id_ficha) {
                throw new Exception("ID de ficha no proporcionado");
            }

            // Verificar que la ficha existe
            $sqlVerificar = "SELECT id_archivo FROM gestion_archivos WHERE id_archivo = ? AND tipo = 'ficha_tecnica'";
            $stmtVerificar = $this->conexion->prepare($sqlVerificar);
            $stmtVerificar->bind_param("i", $id_ficha);
            $stmtVerificar->execute();
            $resultado = $stmtVerificar->get_result();
            
            if ($resultado->num_rows === 0) {
                throw new Exception("Ficha técnica no encontrada");
            }
            $stmtVerificar->close();

            // Datos de la ficha técnica
            $titulo = $_POST['titulo'];
            $id_producto = isset($_POST['id_producto']) && !empty($_POST['id_producto']) ? $_POST['id_producto'] : null;

            // Actualizar el registro principal
            $sqlActualizar = "UPDATE gestion_archivos 
                             SET titulo = ?, 
                                 id_producto = ?,
                                 fecha_actualizacion = NOW()
                             WHERE id_archivo = ?";
            
            $stmtActualizar = $this->conexion->prepare($sqlActualizar);
            if (!$stmtActualizar) {
                throw new Exception("Error al preparar actualización: " . $this->conexion->error);
            }
            
            $stmtActualizar->bind_param("sii", $titulo, $id_producto, $id_ficha);
            
            if (!$stmtActualizar->execute()) {
                throw new Exception("Error al actualizar la ficha: " . $stmtActualizar->error);
            }
            $stmtActualizar->close();

            // Procesar adjuntos si se enviaron nuevos archivos
            $adjuntosActualizados = $this->actualizarAdjuntos($id_ficha);

            if (!$this->conexion->commit()) {
                throw new Exception("Error al confirmar la actualización de la ficha técnica");
            }

            $this->limpiarArchivosAntiguosActualizacion();

            $respuesta = [
                "res" => true,
                "id_archivo" => $id_ficha,
                "mensaje" => "Ficha técnica actualizada correctamente",
                "adjuntos_actualizados" => $adjuntosActualizados
            ];

        } catch (Exception $e) {
            $this->conexion->rollback();
            $this->limpiarArchivosNuevosActualizacion();
            $respuesta["error"] = $e->getMessage();
        }

        echo json_encode($respuesta);
        exit;
    }

    public function obtenerFicha()
    {
        // NUEVO: Limpiar cualquier salida previa y establecer headers
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        $respuesta = ["res" => false];

        try {
            $id_archivo = $_POST['id_archivo'];

            // NUEVO: Consulta optimizada con JOIN en lugar de consultas separadas
            $sql = "SELECT a.id_archivo, a.titulo, a.id_producto, a.fecha_creacion, a.fecha_actualizacion,
                           p.nombre as nombre_producto,
                           ga.url_pdf, ga.url_editable, ga.url_imagen, ga.url_youtube,
                           ga.url_imagen_2, ga.url_imagen_3
                    FROM gestion_archivos a 
                    LEFT JOIN productos p ON a.id_producto = p.id_producto
                    LEFT JOIN gestion_adjuntos ga ON a.id_archivo = ga.id_archivo
                    WHERE a.id_archivo = ? AND a.tipo = 'ficha_tecnica'";

            $stmt = $this->conexion->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->conexion->error);
            }
            
            $stmt->bind_param("i", $id_archivo);
            $stmt->execute();
            $fila = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            if (!$fila) {
                throw new Exception("Ficha técnica no encontrada");
            }

            // NUEVO: Organizar adjuntos directamente desde la consulta
            $pdf = null;
            $editable = null;
            $imagenes = [];
            $youtube = null;

            if ($fila['url_pdf']) {
                $pdf = ['url' => $fila['url_pdf']];
            }
            
            if ($fila['url_editable']) {
                $editable = ['url' => $fila['url_editable']];
            }
            
            if ($fila['url_imagen']) {
                $imagenes[] = ['url' => ImageStorage::url('fichas-tecnicas', $fila['url_imagen'])];
            }
            
            if ($fila['url_imagen_2']) {
                $imagenes[] = ['url' => ImageStorage::url('fichas-tecnicas', $fila['url_imagen_2'])];
            }
            
            if ($fila['url_imagen_3']) {
                $imagenes[] = ['url' => ImageStorage::url('fichas-tecnicas', $fila['url_imagen_3'])];
            }
            
            if ($fila['url_youtube']) {
                $youtube = ['url' => $fila['url_youtube']];
            }

            $respuesta = [
                "res" => true,
                "ficha" => [
                    "id_archivo" => $fila['id_archivo'],
                    "titulo" => $fila['titulo'],
                    "id_producto" => $fila['id_producto'],
                    "nombre_producto" => $fila['nombre_producto'],
                    "fecha_creacion" => $fila['fecha_creacion'],
                    "fecha_actualizacion" => $fila['fecha_actualizacion'],
                    "adjuntos" => [
                        "pdf" => $pdf,
                        "editable" => $editable,
                        "imagenes" => $imagenes,
                        "youtube" => $youtube
                    ]
                ]
            ];

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        echo json_encode($respuesta);
        exit;
    }

    public function eliminarFicha()
    {
        // Limpiar cualquier salida previa y establecer headers
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        $respuesta = ["res" => false];

        try {
            $id_archivo = $_POST['id_archivo'];

            // 1. Obtener las rutas de archivos ANTES de eliminar de la BD
            $rutasArchivos = $this->obtenerRutasArchivos($id_archivo);

            // 2. Eliminar archivos físicos usando las rutas obtenidas
            $archivosEliminados = $this->eliminarArchivosFisicosConRutas($rutasArchivos);

            // 3. Eliminar registros de gestion_adjuntos
            $sqlEliminarAdjuntos = "DELETE FROM gestion_adjuntos WHERE id_archivo = ?";
            $stmtAdjuntos = $this->conexion->prepare($sqlEliminarAdjuntos);
            
            if (!$stmtAdjuntos) {
                throw new Exception("Error al preparar eliminación de adjuntos: " . $this->conexion->error);
            }
            
            $stmtAdjuntos->bind_param("i", $id_archivo);
            $stmtAdjuntos->execute();
            $adjuntosEliminados = $stmtAdjuntos->affected_rows;
            $stmtAdjuntos->close();

            // 4. Eliminar el registro principal de gestion_archivos
            $sqlEliminarArchivo = "DELETE FROM gestion_archivos WHERE id_archivo = ?";
            $stmtArchivo = $this->conexion->prepare($sqlEliminarArchivo);
            
            if (!$stmtArchivo) {
                throw new Exception("Error al preparar eliminación de archivo: " . $this->conexion->error);
            }
            
            $stmtArchivo->bind_param("i", $id_archivo);
            $resultado = $stmtArchivo->execute();
            $stmtArchivo->close();

            if ($resultado) {
                $respuesta["res"] = true;
                $respuesta["mensaje"] = "Ficha técnica eliminada completamente";
                $respuesta["adjuntos_eliminados"] = $adjuntosEliminados;
                $respuesta["archivos_eliminados"] = $archivosEliminados;
            } else {
                throw new Exception("Error al eliminar la ficha técnica de la base de datos");
            }

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        echo json_encode($respuesta);
        exit;
    }

    public function eliminarFichasMasivo()
    {
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');

        if (!isset($_POST['ids']) || !is_array($_POST['ids'])) {
            echo json_encode(['res' => false, 'msg' => 'IDs no proporcionados']);
            return;
        }
        $ids = array_filter(array_map('intval', $_POST['ids']), fn($id) => $id > 0);
        if (empty($ids)) {
            echo json_encode(['res' => false, 'msg' => 'No se proporcionaron IDs válidos']);
            return;
        }

        $eliminados = 0;
        foreach ($ids as $id_archivo) {
            try {
                $rutas = $this->obtenerRutasArchivos($id_archivo);
                $this->eliminarArchivosFisicosConRutas($rutas);

                $stmt = $this->conexion->prepare("DELETE FROM gestion_adjuntos WHERE id_archivo = ?");
                $stmt->bind_param("i", $id_archivo);
                $stmt->execute();
                $stmt->close();

                $stmt2 = $this->conexion->prepare("DELETE FROM gestion_archivos WHERE id_archivo = ?");
                $stmt2->bind_param("i", $id_archivo);
                if ($stmt2->execute()) $eliminados++;
                $stmt2->close();
            } catch (Exception $e) {
                // continuar con los demás si uno falla
            }
        }

        $count = $eliminados;
        echo json_encode([
            'res' => $eliminados > 0,
            'msg' => "$count ficha técnica" . ($count !== 1 ? "s" : "") . " eliminada" . ($count !== 1 ? "s" : "") . " correctamente"
        ]);
        exit;
    }

    public function obtenerInfoCompleta($id_ficha)
    {
        $respuesta = ["res" => false];

        try {
            // Validar que el ID sea numérico
            if (!is_numeric($id_ficha)) {
                throw new Exception("ID de ficha técnica inválido");
            }

            // NUEVO: Obtener datos completos de la ficha técnica SIN filtro de estado
            $sql = "SELECT 
                        a.*,
                        p.nombre as nombre_producto,
                        p.codigo as codigo_producto,
                        p.detalle as descripcion_producto,
                        p.precio as precio_producto,
                        p.costo as costo_producto,
                        p.cantidad as stock_producto,
                        p.estado as estado_producto
                    FROM gestion_archivos a 
                    LEFT JOIN productos p ON a.id_producto = p.id_producto
                    WHERE a.id_archivo = ? 
                    AND a.tipo = 'ficha_tecnica'";

            $stmt = $this->conexion->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta: " . $this->conexion->error);
            }
            
            $stmt->bind_param("i", $id_ficha);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows === 0) {
                throw new Exception("Ficha técnica no encontrada");
            }

            $ficha = $resultado->fetch_assoc();

            // Obtener todos los adjuntos de la ficha
            $adjuntoObj = new GestionAdjunto();
            $adjuntoObj->setIdArchivo($id_ficha);
            $adjuntos = $adjuntoObj->listarPorArchivo();

            $listaAdjuntos = [];
            $adjuntosPorTipo = [
                'pdf' => [],
                'editable' => [],
                'imagen' => [],
                'youtube' => []
            ];

            if ($adjuntos && $adjuntos->num_rows > 0) {
                while ($row = $adjuntos->fetch_assoc()) {
                    $listaAdjuntos[] = $row;
                    
                    // Agrupar por tipo
                    $tipo = $row['tipo_adjunto'];
                    if (isset($adjuntosPorTipo[$tipo])) {
                        $adjuntosPorTipo[$tipo][] = $row;
                    }
                }
            }

            // Obtener información de la empresa
            $id_empresa = $ficha['id_empresa'];
            $sqlEmpresa = "SELECT 
                            e.razon_social,
                            e.ruc,
                            e.direccion,
                            e.telefono,
                            e.email,
                            e.ubigeo,
                            e.distrito,
                            e.provincia,
                            e.departamento
                          FROM empresas e 
                          WHERE e.id_empresa = ?";
            
            $stmtEmpresa = $this->conexion->prepare($sqlEmpresa);
            $stmtEmpresa->bind_param("i", $id_empresa);
            $stmtEmpresa->execute();
            $empresa = $stmtEmpresa->get_result()->fetch_assoc();

            // Construir respuesta completa
            $respuesta = [
                "res" => true,
                "ficha_tecnica" => [
                    "id_archivo" => $ficha['id_archivo'],
                    "titulo" => $ficha['titulo'],
                    "version" => $ficha['version'],
                    "fecha_creacion" => $ficha['fecha_creacion'],
                    "id_empresa" => $ficha['id_empresa'],
                    "sucursal" => $ficha['sucursal']
                ],
                "producto" => $ficha['id_producto'] ? [
                    "id_producto" => $ficha['id_producto'],
                    "nombre" => $ficha['nombre_producto'],
                    "codigo" => $ficha['codigo_producto'],
                    "descripcion" => $ficha['descripcion_producto'],
                    "precio" => $ficha['precio_producto'],
                    "costo" => $ficha['costo_producto'],
                    "stock" => $ficha['stock_producto'],
                    "estado" => $ficha['estado_producto']
                ] : null,
                "empresa" => $empresa,
                "adjuntos" => [
                    "todos" => $listaAdjuntos,
                    "por_tipo" => $adjuntosPorTipo,
                    "resumen" => [
                        "total_adjuntos" => count($listaAdjuntos),
                        "pdfs" => count($adjuntosPorTipo['pdf']),
                        "editables" => count($adjuntosPorTipo['editable']),
                        "imagenes" => count($adjuntosPorTipo['imagen']),
                        "videos_youtube" => count($adjuntosPorTipo['youtube'])
                    ]
                ],
                "urls_completas" => [
                    "base_url" => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]",
                    "adjuntos" => array_map(function($adjunto) {
                        return [
                            "nombre" => $adjunto['nombre_adjunto'],
                            "tipo" => $adjunto['tipo_adjunto'],
                            "url_completa" => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]/" . $adjunto['ruta_adjunto'],
                            "ruta_relativa" => $adjunto['ruta_adjunto'],
                            "es_principal" => $adjunto['es_principal'] === '1'
                        ];
                    }, $listaAdjuntos)
                ]
            ];

        } catch (Exception $e) {
            $respuesta["res"] = false;
            $respuesta["error"] = $e->getMessage();
        }

        // Establecer headers para JSON
        header('Content-Type: application/json');
        return json_encode($respuesta, JSON_PRETTY_PRINT);
    }

    public function compartirWhatsApp()
    {
        // NUEVO: Limpiar cualquier salida previa y establecer headers
        if (ob_get_level()) ob_end_clean();
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, must-revalidate');
        
        $respuesta = ["res" => false];

        try {
            $id_archivo = $_POST['id_archivo'];
            $telefono = $_POST['telefono'];

            // Validar el número de teléfono
            if (!preg_match('/^[0-9]{9}$/', $telefono)) {
                throw new Exception("Número de teléfono inválido");
            }

            // Obtener la ficha técnica
            $archivo = new GestionArchivo();
            $archivo->setIdArchivo($id_archivo);
            $ficha = $archivo->obtenerPorId();

            if (!$ficha) {
                throw new Exception("Ficha técnica no encontrada");
            }

            // Obtener adjuntos usando los nuevos campos
            $sqlAdjuntos = "SELECT url_pdf, url_editable, url_imagen, url_imagen_2, url_imagen_3, url_youtube 
                           FROM gestion_adjuntos 
                           WHERE id_archivo = ?";
            
            $stmt = $this->conexion->prepare($sqlAdjuntos);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta de adjuntos: " . $this->conexion->error);
            }
            
            $stmt->bind_param("i", $id_archivo);
            $stmt->execute();
            $adjunto = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            // Obtener selecciones del usuario (por defecto todo marcado)
            $incluirPDF = isset($_POST['incluir_pdf']) ? $_POST['incluir_pdf'] === 'true' : true;
            $incluirEditable = isset($_POST['incluir_editable']) ? $_POST['incluir_editable'] === 'true' : true;
            $incluirImagenes = isset($_POST['incluir_imagenes']) ? $_POST['incluir_imagenes'] === 'true' : true;
            $incluirYouTube = isset($_POST['incluir_youtube']) ? $_POST['incluir_youtube'] === 'true' : true;

            // Construir mensaje de WhatsApp
            $mensaje = "🔧 *FICHA TÉCNICA*\n\n";
            $mensaje .= "📋 *" . $ficha['titulo'] . "*\n\n";

            if ($incluirPDF && $adjunto['url_pdf']) {
                $mensaje .= "📄 *PDF:* " . $_SERVER['HTTP_HOST'] . "/" . $adjunto['url_pdf'] . "\n\n";
            }

            if ($incluirEditable && $adjunto['url_editable']) {
                $mensaje .= "📝 *Archivo Editable:* " . $_SERVER['HTTP_HOST'] . "/" . $adjunto['url_editable'] . "\n\n";
            }

            if ($incluirImagenes) {
                if ($adjunto['url_imagen']) {
                    $mensaje .= "🖼️ *Imagen 1:* " . ImageStorage::url('fichas-tecnicas', $adjunto['url_imagen']) . "\n\n";
                }
                if ($adjunto['url_imagen_2']) {
                    $mensaje .= "🖼️ *Imagen 2:* " . ImageStorage::url('fichas-tecnicas', $adjunto['url_imagen_2']) . "\n\n";
                }
                if ($adjunto['url_imagen_3']) {
                    $mensaje .= "🖼️ *Imagen 3:* " . ImageStorage::url('fichas-tecnicas', $adjunto['url_imagen_3']) . "\n\n";
                }
            }

            if ($incluirYouTube && $adjunto['url_youtube']) {
                $mensaje .= "🎥 *Video:* " . $adjunto['url_youtube'] . "\n\n";
            }

            $mensaje .= "📱 *Compartido desde JVC*\n";
            $mensaje .= "🌐 " . $_SERVER['HTTP_HOST'];

            // Generar URL de WhatsApp
            $mensajeCodificado = urlencode($mensaje);
            $urlWhatsApp = "https://wa.me/51$telefono?text=$mensajeCodificado";

            $respuesta = [
                "res" => true,
                "whatsapp_url" => $urlWhatsApp,
                "mensaje" => $mensaje
            ];

        } catch (Exception $e) {
            $respuesta["error"] = $e->getMessage();
        }

        echo json_encode($respuesta);
        exit;
    }

    private function procesarAdjuntos($id_archivo)
    {
        // Variables para almacenar las rutas de cada tipo
        $url_pdf = null;
        $url_editable = null;
        $url_imagen = null;
        $url_imagen_2 = null; // Para la segunda imagen
        $url_imagen_3 = null; // Para la tercera imagen
        $url_youtube = null;

        // Procesar PDF
        if (isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['pdf'];
            $datosArchivo = $this->validarArchivoGestion($archivo, 'pdf', true);

            // Generar nombre único
            $extension = $datosArchivo['extension'];
            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
            $rutaDestino = 'files/gestion_archivos/pdf/' . $nombreUnico;

            // Crear directorio si no existe
            $directorio = dirname($rutaDestino);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                $url_pdf = $rutaDestino;
            } else {
                throw new Exception("Error al subir el PDF");
            }
        }

        // Procesar archivo editable
        if (isset($_FILES['editable']) && $_FILES['editable']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['editable'];
            $datosArchivo = $this->validarArchivoGestion($archivo, 'editable', true);

            // Generar nombre único
            $extension = $datosArchivo['extension'];
            $nombreUnico = uniqid() . '_' . time() . '.' . $extension;
            $rutaDestino = 'files/gestion_archivos/editable/' . $nombreUnico;

            // Crear directorio si no existe
            $directorio = dirname($rutaDestino);
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                $url_editable = $rutaDestino;
            } else {
                throw new Exception("Error al subir el archivo editable");
            }
        }

        // Procesar imágenes - Guardar las 3 imágenes en campos separados
        if (isset($_FILES['imagenes']) && 
            is_array($_FILES['imagenes']['name']) && 
            !empty($_FILES['imagenes']['name'][0])) {
            
            $total = count($_FILES['imagenes']['name']);

            // Validar máximo 3 imágenes
            if ($total > 3) {
                throw new Exception("Solo se pueden subir máximo 3 imágenes");
            }

            // Procesar cada imagen y guardar en campos separados
            for ($i = 0; $i < min($total, 3); $i++) {
                if (isset($_FILES['imagenes']['name'][$i]) && 
                    isset($_FILES['imagenes']['tmp_name'][$i]) &&
                    isset($_FILES['imagenes']['type'][$i]) &&
                    isset($_FILES['imagenes']['size'][$i]) &&
                    $_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {

                    $archivo = [
                        'name' => $_FILES['imagenes']['name'][$i],
                        'type' => $_FILES['imagenes']['type'][$i],
                        'tmp_name' => $_FILES['imagenes']['tmp_name'][$i],
                        'size' => $_FILES['imagenes']['size'][$i]
                    ];

                    // Validar tipo de imagen
                    $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                    if (in_array($archivo['type'], $tiposPermitidos)) {
                        // Validar tamaño (máximo 10MB antes de compresión)
                        if ($archivo['size'] <= 10 * 1024 * 1024) {
                            // Comprimir imagen
                            $imagenComprimida = $this->comprimirImagen($archivo);
                            
                            if ($imagenComprimida) {
                                // Guardar cada imagen en su campo correspondiente
                                switch ($i) {
                                    case 0:
                                        $url_imagen = $imagenComprimida['ruta'];
                                        break;
                                    case 1:
                                        $url_imagen_2 = $imagenComprimida['ruta'];
                                        break;
                                    case 2:
                                        $url_imagen_3 = $imagenComprimida['ruta'];
                                        break;
                                }
                            }
                        }
                    }
                }
            }
        }

        // Procesar URL de YouTube
        if (isset($_POST['youtube']) && !empty($_POST['youtube'])) {
            $youtube = trim($_POST['youtube']);
            
            // Validar que sea una URL válida de YouTube
            if (filter_var($youtube, FILTER_VALIDATE_URL) && 
                (strpos($youtube, 'youtube.com') !== false || strpos($youtube, 'youtu.be') !== false)) {
                
                $url_youtube = $youtube;
            }
        }

        // Crear UNA SOLA FILA con todos los campos
        if ($url_pdf || $url_editable || $url_imagen || $url_imagen_2 || $url_imagen_3 || $url_youtube) {
            $sql = "INSERT INTO gestion_adjuntos 
                    (id_archivo, url_pdf, url_editable, url_imagen, url_imagen_2, url_imagen_3, url_youtube) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conexion->prepare($sql);
            if (!$stmt) {
                throw new Exception("Error al preparar consulta de adjunto: " . $this->conexion->error);
            }
            
            $stmt->bind_param("issssss", 
                $id_archivo,
                $url_pdf,
                $url_editable,
                $url_imagen,
                $url_imagen_2,
                $url_imagen_3,
                $url_youtube
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Error al guardar el adjunto: " . $stmt->error);
            }
            
            $stmt->close();
        }

        // Retornar array con las rutas para compatibilidad
        return [
            'pdf' => $url_pdf,
            'editable' => $url_editable,
            'imagen' => $url_imagen,
            'imagen_2' => $url_imagen_2,
            'imagen_3' => $url_imagen_3,
            'youtube' => $url_youtube
        ];
    }

    private function actualizarAdjuntos($id_archivo)
    {
        $adjuntosActualizados = [];
        $eliminarPdf = $this->obtenerBanderaEliminacion('eliminar_pdf');
        $eliminarEditable = $this->obtenerBanderaEliminacion('eliminar_editable');
        $hayPdfNuevo = isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK;
        $hayEditableNuevo = isset($_FILES['editable']) && $_FILES['editable']['error'] === UPLOAD_ERR_OK;

        if ($eliminarPdf && $hayPdfNuevo) {
            throw new Exception("No se puede eliminar y reemplazar el PDF en la misma solicitud");
        }

        if ($eliminarEditable && $hayEditableNuevo) {
            throw new Exception("No se puede eliminar y reemplazar el archivo editable en la misma solicitud");
        }

        // Verificar si ya existe un registro de adjuntos
        $sqlVerificar = "SELECT id_adjunto FROM gestion_adjuntos WHERE id_archivo = ?";
        $stmtVerificar = $this->conexion->prepare($sqlVerificar);
        $stmtVerificar->bind_param("i", $id_archivo);
        $stmtVerificar->execute();
        $resultado = $stmtVerificar->get_result();
        $existeAdjunto = $resultado->num_rows > 0;
        $stmtVerificar->close();

        // Obtener adjuntos actuales
        $sqlActuales = "SELECT url_pdf, url_editable, url_imagen, url_imagen_2, url_imagen_3, url_youtube 
                       FROM gestion_adjuntos WHERE id_archivo = ?";
        $stmtActuales = $this->conexion->prepare($sqlActuales);
        $stmtActuales->bind_param("i", $id_archivo);
        $stmtActuales->execute();
        $adjuntosActuales = $stmtActuales->get_result()->fetch_assoc();
        $stmtActuales->close();

        // Inicializar con valores actuales
        $url_pdf = $adjuntosActuales['url_pdf'] ?? null;
        $url_editable = $adjuntosActuales['url_editable'] ?? null;
        $url_imagen = $adjuntosActuales['url_imagen'] ?? null;
        $url_imagen_2 = $adjuntosActuales['url_imagen_2'] ?? null;
        $url_imagen_3 = $adjuntosActuales['url_imagen_3'] ?? null;
        $url_youtube = $adjuntosActuales['url_youtube'] ?? null;

        // Mover primero los reemplazos; los archivos anteriores se conservan hasta confirmar la BD.
        $nuevoPdf = $this->moverArchivoGestionActualizacion('pdf', 'pdf');
        if ($nuevoPdf !== null) {
            $this->programarLimpiezaArchivoAnterior($url_pdf, 'pdf');
            $url_pdf = $nuevoPdf;
            $adjuntosActualizados[] = 'pdf';
        } else if ($eliminarPdf) {
            $this->programarLimpiezaArchivoAnterior($url_pdf, 'pdf');
            $url_pdf = null;
            $adjuntosActualizados[] = 'pdf_eliminado';
        }

        $nuevoEditable = $this->moverArchivoGestionActualizacion('editable', 'editable');
        if ($nuevoEditable !== null) {
            $this->programarLimpiezaArchivoAnterior($url_editable, 'editable');
            $url_editable = $nuevoEditable;
            $adjuntosActualizados[] = 'editable';
        } else if ($eliminarEditable) {
            $this->programarLimpiezaArchivoAnterior($url_editable, 'editable');
            $url_editable = null;
            $adjuntosActualizados[] = 'editable_eliminado';
        }

        // Procesar imágenes si se enviaron nuevas
        if (isset($_FILES['imagenes']) && 
            is_array($_FILES['imagenes']['name']) && 
            !empty($_FILES['imagenes']['name'][0])) {
            
            $total = count($_FILES['imagenes']['name']);

            if ($total <= 3) {
                // Eliminar imágenes anteriores (soporta rutas antiguas y nuevas basadas en filename)
                $this->eliminarImagenSiExiste($url_imagen);
                $this->eliminarImagenSiExiste($url_imagen_2);
                $this->eliminarImagenSiExiste($url_imagen_3);

                // Resetear URLs de imágenes
                $url_imagen = null;
                $url_imagen_2 = null;
                $url_imagen_3 = null;

                for ($i = 0; $i < min($total, 3); $i++) {
                    if (isset($_FILES['imagenes']['name'][$i]) && 
                        $_FILES['imagenes']['error'][$i] === UPLOAD_ERR_OK) {

                        $archivo = [
                            'name' => $_FILES['imagenes']['name'][$i],
                            'type' => $_FILES['imagenes']['type'][$i],
                            'tmp_name' => $_FILES['imagenes']['tmp_name'][$i],
                            'size' => $_FILES['imagenes']['size'][$i]
                        ];

                        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                        if (in_array($archivo['type'], $tiposPermitidos) && $archivo['size'] <= 10 * 1024 * 1024) {
                            $imagenComprimida = $this->comprimirImagen($archivo);
                            
                            if ($imagenComprimida) {
                                switch ($i) {
                                    case 0:
                                        $url_imagen = $imagenComprimida['ruta'];
                                        break;
                                    case 1:
                                        $url_imagen_2 = $imagenComprimida['ruta'];
                                        break;
                                    case 2:
                                        $url_imagen_3 = $imagenComprimida['ruta'];
                                        break;
                                }
                                $adjuntosActualizados[] = 'imagen_' . ($i + 1);
                            }
                        }
                    }
                }
            }
        }

        // Procesar URL de YouTube si se envió
        if (isset($_POST['youtube'])) {
            $youtube = trim($_POST['youtube']);
            
            if (!empty($youtube) && filter_var($youtube, FILTER_VALIDATE_URL) && 
                (strpos($youtube, 'youtube.com') !== false || strpos($youtube, 'youtu.be') !== false)) {
                $url_youtube = $youtube;
                $adjuntosActualizados[] = 'youtube';
            } else if (empty($youtube)) {
                // Si se envió vacío, eliminar el enlace
                $url_youtube = null;
                $adjuntosActualizados[] = 'youtube_eliminado';
            }
        }

        // Actualizar o insertar adjuntos
        if ($existeAdjunto) {
            $sqlActualizar = "UPDATE gestion_adjuntos 
                             SET url_pdf = ?, 
                                 url_editable = ?, 
                                 url_imagen = ?, 
                                 url_imagen_2 = ?, 
                                 url_imagen_3 = ?, 
                                 url_youtube = ?
                             WHERE id_archivo = ?";
            
            $stmt = $this->conexion->prepare($sqlActualizar);
            $stmt->bind_param("ssssssi", 
                $url_pdf,
                $url_editable,
                $url_imagen,
                $url_imagen_2,
                $url_imagen_3,
                $url_youtube,
                $id_archivo
            );
        } else {
            $sqlInsertar = "INSERT INTO gestion_adjuntos 
                           (id_archivo, url_pdf, url_editable, url_imagen, url_imagen_2, url_imagen_3, url_youtube) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conexion->prepare($sqlInsertar);
            $stmt->bind_param("issssss", 
                $id_archivo,
                $url_pdf,
                $url_editable,
                $url_imagen,
                $url_imagen_2,
                $url_imagen_3,
                $url_youtube
            );
        }

        if (!$stmt->execute()) {
            throw new Exception("Error al actualizar adjuntos: " . $stmt->error);
        }
        
        $stmt->close();

        return $adjuntosActualizados;
    }

    private function obtenerBanderaEliminacion($nombre)
    {
        $valor = isset($_POST[$nombre]) ? $_POST[$nombre] : '0';

        if ($valor !== '0' && $valor !== '1') {
            throw new Exception("Valor inválido para $nombre");
        }

        return $valor === '1';
    }

    private function moverArchivoGestionActualizacion($campo, $tipo)
    {
        if (!isset($_FILES[$campo]) || $_FILES[$campo]['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $archivo = $_FILES[$campo];
        $datosArchivo = $this->validarArchivoGestion($archivo, $tipo, true);
        $extension = $datosArchivo['extension'];

        $baseRelativa = "files/gestion_archivos/$tipo/";
        $baseAbsoluta = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $baseRelativa);
        if (!is_dir($baseAbsoluta) && !mkdir($baseAbsoluta, 0755, true)) {
            throw new Exception("No se pudo crear el directorio para el archivo $campo");
        }

        $nombreUnico = uniqid('', true) . '_' . time() . '.' . $extension;
        $rutaRelativa = $baseRelativa . $nombreUnico;
        $rutaAbsoluta = $baseAbsoluta . $nombreUnico;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaAbsoluta)) {
            throw new Exception($tipo === 'pdf' ? "Error al subir el PDF" : "Error al subir el archivo editable");
        }

        $this->archivosNuevosActualizacion[] = ['ruta' => $rutaRelativa, 'tipo' => $tipo];
        return $rutaRelativa;
    }

    private function validarArchivoGestion($archivo, $tipo, $obligatorio)
    {
        $nombreCampo = $tipo === 'pdf' ? 'PDF' : 'archivo editable';

        if (!is_array($archivo) || !isset($archivo['error']) || $archivo['error'] === UPLOAD_ERR_NO_FILE) {
            if ($obligatorio) {
                throw new Exception("El archivo PDF es obligatorio");
            }
            return null;
        }

        $this->validarErrorSubida($archivo['error'], $nombreCampo);

        if (!isset($archivo['name'], $archivo['tmp_name']) || !is_file($archivo['tmp_name'])) {
            throw new Exception("No se pudo validar el $nombreCampo recibido");
        }

        $politicas = $this->obtenerPoliticasArchivos();
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!isset($politicas[$extension]) || ($tipo === 'pdf') !== ($extension === 'pdf')) {
            throw new Exception($tipo === 'pdf'
                ? "Solo se permite un archivo PDF"
                : "Formato editable no permitido. Use CDR, PSD, AI, DOC, DOCX, XLS, XLSX, PPT o PPTX");
        }

        $tamano = filesize($archivo['tmp_name']);
        if ($tamano === false || $tamano <= 0) {
            throw new Exception("El $nombreCampo está vacío o no se pudo leer");
        }

        $politica = $politicas[$extension];
        if ($tamano > $politica['maximo']) {
            throw new Exception("El archivo .$extension no puede exceder {$politica['limite_mb']} MB");
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo === false) {
            throw new Exception("No se pudo inicializar la validación MIME del archivo");
        }

        $mime = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        $mime = is_string($mime) ? strtolower(trim($mime)) : '';

        if (!in_array($mime, $politica['mimes'], true)) {
            throw new Exception("El contenido del archivo .$extension no coincide con su formato");
        }

        return ['extension' => $extension, 'mime' => $mime, 'tamano' => $tamano];
    }

    private function obtenerPoliticasArchivos()
    {
        $mb = 1024 * 1024;

        return [
            'pdf' => ['maximo' => 25 * $mb, 'limite_mb' => 25, 'mimes' => ['application/pdf']],
            'psd' => ['maximo' => 200 * $mb, 'limite_mb' => 200, 'mimes' => [
                'image/vnd.adobe.photoshop', 'image/x-photoshop', 'application/octet-stream'
            ]],
            'cdr' => ['maximo' => 100 * $mb, 'limite_mb' => 100, 'mimes' => [
                'application/cdr', 'application/coreldraw', 'application/x-cdr', 'application/x-coreldraw',
                'application/vnd.corel-draw', 'image/cdr', 'image/x-cdr', 'image/vnd.corel-draw',
                'application/octet-stream'
            ]],
            'ai' => ['maximo' => 100 * $mb, 'limite_mb' => 100, 'mimes' => [
                'application/illustrator', 'application/vnd.adobe.illustrator', 'application/postscript',
                'application/pdf', 'application/octet-stream'
            ]],
            'doc' => ['maximo' => 50 * $mb, 'limite_mb' => 50, 'mimes' => ['application/msword']],
            'docx' => ['maximo' => 50 * $mb, 'limite_mb' => 50, 'mimes' => [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'
            ]],
            'xls' => ['maximo' => 50 * $mb, 'limite_mb' => 50, 'mimes' => ['application/vnd.ms-excel']],
            'xlsx' => ['maximo' => 50 * $mb, 'limite_mb' => 50, 'mimes' => [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'
            ]],
            'ppt' => ['maximo' => 50 * $mb, 'limite_mb' => 50, 'mimes' => ['application/vnd.ms-powerpoint']],
            'pptx' => ['maximo' => 50 * $mb, 'limite_mb' => 50, 'mimes' => [
                'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'
            ]]
        ];
    }

    private function validarErrorSubida($codigo, $nombreCampo)
    {
        if ($codigo === UPLOAD_ERR_OK) {
            return;
        }

        $mensajes = [
            UPLOAD_ERR_INI_SIZE => "El $nombreCampo supera el límite de carga configurado en PHP",
            UPLOAD_ERR_FORM_SIZE => "El $nombreCampo supera el límite permitido por el formulario",
            UPLOAD_ERR_PARTIAL => "La carga del $nombreCampo quedó incompleta; vuelva a intentarlo",
            UPLOAD_ERR_NO_TMP_DIR => "El servidor no tiene un directorio temporal disponible para la carga",
            UPLOAD_ERR_CANT_WRITE => "El servidor no pudo escribir el $nombreCampo en disco",
            UPLOAD_ERR_EXTENSION => "Una extensión de PHP detuvo la carga del $nombreCampo"
        ];

        throw new Exception($mensajes[$codigo] ?? "No se pudo recibir el $nombreCampo (código de carga $codigo)");
    }

    private function validarLimiteSolicitud()
    {
        if (!isset($_SERVER['CONTENT_LENGTH'])) {
            return;
        }

        $limitePost = $this->convertirLimiteIniABytes(ini_get('post_max_size'));
        if ($limitePost > 0 && (int) $_SERVER['CONTENT_LENGTH'] > $limitePost) {
            throw new Exception("La solicitud supera el límite total permitido por PHP (post_max_size)");
        }
    }

    private function convertirLimiteIniABytes($valor)
    {
        $valor = trim((string) $valor);
        if ($valor === '') {
            return 0;
        }

        $numero = (float) $valor;
        $unidad = strtolower(substr($valor, -1));
        if ($unidad === 'g') return (int) ($numero * 1024 * 1024 * 1024);
        if ($unidad === 'm') return (int) ($numero * 1024 * 1024);
        if ($unidad === 'k') return (int) ($numero * 1024);
        return (int) $numero;
    }

    private function programarLimpiezaArchivoAnterior($ruta, $tipo)
    {
        if (!empty($ruta)) {
            $this->archivosAntiguosPendientes[] = ['ruta' => $ruta, 'tipo' => $tipo];
        }
    }

    private function limpiarArchivosAntiguosActualizacion()
    {
        foreach ($this->archivosAntiguosPendientes as $archivo) {
            $this->eliminarArchivoGestionSeguro($archivo['ruta'], $archivo['tipo'], 'archivo anterior leído de la BD');
        }

        $this->archivosAntiguosPendientes = [];
        $this->archivosNuevosActualizacion = [];
    }

    private function limpiarArchivosNuevosActualizacion()
    {
        foreach ($this->archivosNuevosActualizacion as $archivo) {
            $this->eliminarArchivoGestionSeguro($archivo['ruta'], $archivo['tipo'], 'archivo nuevo generado por la actualización');
        }

        $this->archivosNuevosActualizacion = [];
        $this->archivosAntiguosPendientes = [];
    }

    private function eliminarArchivoGestionSeguro($ruta, $tipo, $contexto)
    {
        $basesPermitidas = [
            'pdf' => 'files/gestion_archivos/pdf/',
            'editable' => 'files/gestion_archivos/editable/'
        ];
        $extensionesPermitidas = [
            'pdf' => ['pdf'],
            'editable' => ['xlsx', 'xls', 'doc', 'docx', 'cdr', 'psd', 'ai', 'ppt', 'pptx']
        ];

        if (!isset($basesPermitidas[$tipo]) || !is_string($ruta)) {
            error_log("Ficha técnica: ruta inválida para limpiar $contexto");
            return false;
        }

        $rutaNormalizada = str_replace('\\', '/', ltrim($ruta, '/'));
        $baseRelativa = $basesPermitidas[$tipo];
        $nombreArchivo = basename($rutaNormalizada);
        $extension = strtolower(pathinfo($nombreArchivo, PATHINFO_EXTENSION));

        if ($rutaNormalizada !== $baseRelativa . $nombreArchivo ||
            !in_array($extension, $extensionesPermitidas[$tipo], true)) {
            error_log("Ficha técnica: se rechazó una ruta fuera de la base permitida para $contexto: $ruta");
            return false;
        }

        $raizProyecto = dirname(__DIR__, 3);
        $baseReal = realpath($raizProyecto . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $baseRelativa));
        $archivoReal = realpath($raizProyecto . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $rutaNormalizada));

        if ($baseReal === false || $archivoReal === false || !is_file($archivoReal) ||
            strcasecmp(dirname($archivoReal), $baseReal) !== 0) {
            error_log("Ficha técnica: no se pudo verificar el archivo para limpiar $contexto: $ruta");
            return false;
        }

        if (!@unlink($archivoReal)) {
            error_log("Ficha técnica: no se pudo eliminar $contexto: $ruta");
            return false;
        }

        return true;
    }

    // Función para comprimir imágenes
    private function comprimirImagen($archivo)
    {
        try {
            // Validar que el archivo sea válido
            if (!is_array($archivo) || !isset($archivo['tmp_name']) || !file_exists($archivo['tmp_name'])) {
                return false;
            }

            // Usar ImageStorage para la ruta de destino
            $directorio = ImageStorage::BASE_PATH . 'fichas-tecnicas/';
            if (!is_dir($directorio)) {
                mkdir($directorio, 0755, true);
            }

            // Generar nombre único para la imagen comprimida
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombreUnico = uniqid() . '_' . time() . '.jpg'; // Siempre guardar como JPG
            $rutaDestino = $directorio . $nombreUnico;

            // Cargar imagen según el tipo
            $imagen = null;
            switch (strtolower($extension)) {
                case 'jpg':
                case 'jpeg':
                    $imagen = imagecreatefromjpeg($archivo['tmp_name']);
                break;
                case 'png':
                    $imagen = imagecreatefrompng($archivo['tmp_name']);
                break;
                case 'gif':
                    $imagen = imagecreatefromgif($archivo['tmp_name']);
                break;
            default:
                    return false;
        }

            if (!$imagen) {
                return false;
        }

        // Obtener dimensiones originales
            $anchoOriginal = imagesx($imagen);
            $altoOriginal = imagesy($imagen);

            // Calcular nuevas dimensiones (máximo 800x600)
            $anchoMaximo = 800;
            $altoMaximo = 600;

            if ($anchoOriginal > $anchoMaximo || $altoOriginal > $altoMaximo) {
                $ratio = min($anchoMaximo / $anchoOriginal, $altoMaximo / $altoOriginal);
            $nuevoAncho = round($anchoOriginal * $ratio);
            $nuevoAlto = round($altoOriginal * $ratio);
            } else {
            $nuevoAncho = $anchoOriginal;
            $nuevoAlto = $altoOriginal;
        }

            // Crear nueva imagen con las dimensiones calculadas
        $imagenNueva = imagecreatetruecolor($nuevoAncho, $nuevoAlto);

        // Preservar transparencia para PNG
            if (strtolower($extension) === 'png') {
            imagealphablending($imagenNueva, false);
            imagesavealpha($imagenNueva, true);
        }

        // Redimensionar imagen
            imagecopyresampled($imagenNueva, $imagen, 0, 0, 0, 0, $nuevoAncho, $nuevoAlto, $anchoOriginal, $altoOriginal);

            // Guardar imagen comprimida como JPG
            $calidad = 85; // Calidad del 85%
            if (imagejpeg($imagenNueva, $rutaDestino, $calidad)) {
                $tamañoFinal = filesize($rutaDestino);

        // Limpiar memoria
                imagedestroy($imagen);
        imagedestroy($imagenNueva);

        return [
                    'ruta' => $nombreUnico, // Solo filename, consistente con ImageStorage::save()
                    'size' => $tamañoFinal
                ];
            }

            // Limpiar memoria en caso de error
            imagedestroy($imagen);
            imagedestroy($imagenNueva);
            return false;

        } catch (Exception $e) {
            return false;
        }
    }

    // FUNCIÓN ELIMINADA: guardarAdjunto ya no se usa con los nuevos campos específicos

    // NUEVO: Método para obtener rutas de archivos ANTES de eliminar de la BD
    private function obtenerRutasArchivos($id_archivo)
    {
        try {
            $sql = "SELECT url_pdf, url_editable, url_imagen, url_imagen_2, url_imagen_3 
                    FROM gestion_adjuntos 
                    WHERE id_archivo = ?";
            
            $stmt = $this->conexion->prepare($sql);
            if (!$stmt) {
                return [];
            }
            
            $stmt->bind_param("i", $id_archivo);
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            $rutas = [];
            
            if ($adjunto = $resultado->fetch_assoc()) {
                // Agregar solo las rutas que no sean NULL o vacías
                if ($adjunto['url_pdf'] && !empty($adjunto['url_pdf']) && $adjunto['url_pdf'] !== 'NULL') {
                    $rutas[] = $adjunto['url_pdf'];
                }
                if ($adjunto['url_editable'] && !empty($adjunto['url_editable']) && $adjunto['url_editable'] !== 'NULL') {
                    $rutas[] = $adjunto['url_editable'];
                }
                if ($adjunto['url_imagen'] && !empty($adjunto['url_imagen']) && $adjunto['url_imagen'] !== 'NULL') {
                    $rutas[] = $adjunto['url_imagen'];
                }
                if ($adjunto['url_imagen_2'] && !empty($adjunto['url_imagen_2']) && $adjunto['url_imagen_2'] !== 'NULL') {
                    $rutas[] = $adjunto['url_imagen_2'];
                }
                if ($adjunto['url_imagen_3'] && !empty($adjunto['url_imagen_3']) && $adjunto['url_imagen_3'] !== 'NULL') {
                    $rutas[] = $adjunto['url_imagen_3'];
                }
            }
            
            $stmt->close();
            return $rutas;
            
        } catch (Exception $e) {
            return [];
        }
    }

    // Elimina una imagen soportando tanto rutas completas antiguas como nuevos filenames
    private function eliminarImagenSiExiste($imagen)
    {
        if (empty($imagen)) return;

        if (strpos($imagen, '/') !== false) {
            // Formato antiguo: ruta completa (files/gestion_archivos/imagen/...)
            if (file_exists($imagen)) @unlink($imagen);
        } else {
            // Nuevo formato: solo filename (guardado via ImageStorage)
            ImageStorage::delete('fichas-tecnicas', $imagen);
        }
    }

    // Método para eliminar archivos físicos usando rutas pre-obtenidas
    private function eliminarArchivosFisicosConRutas($rutas)
    {
        try {
            $archivosEliminados = 0;
            
            foreach ($rutas as $ruta) {
                // Construir ruta completa si es relativa
                $rutaCompleta = $ruta;
                
                // Si la ruta no existe, probar con diferentes variaciones
                if (!file_exists($rutaCompleta)) {
                    // Probar con ruta absoluta del servidor
                    $rutaAbsoluta = $_SERVER['DOCUMENT_ROOT'] . '/' . $ruta;
                    
                    if (file_exists($rutaAbsoluta)) {
                        $rutaCompleta = $rutaAbsoluta;
                    } else {
                        // Probar con ruta desde el directorio actual
                        $rutaActual = getcwd() . '/' . $ruta;
                        
                        if (file_exists($rutaActual)) {
                            $rutaCompleta = $rutaActual;
                        } else {
                            // Probar con ruta de ImageStorage (nuevo formato: solo filename)
                            $rutaImg = ImageStorage::BASE_PATH . 'fichas-tecnicas/' . $ruta;
                            if (file_exists($rutaImg)) {
                                $rutaCompleta = $rutaImg;
                            } else {
                                continue;
                            }
                        }
                    }
                }
                
                if (file_exists($rutaCompleta)) {
                    if (@unlink($rutaCompleta)) {
                        $archivosEliminados++;
                    }
                }
            }
            
            return $archivosEliminados;
            
        } catch (Exception $e) {
            return 0;
        }
    }
}
