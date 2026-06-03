<?php
require_once "app/models/NumeroSerie.php";
require_once "app/models/DetalleSerie.php";


class SeriesController extends Controller
{
    private $conectar;
    private $numeroSerieModel;
    private $detalleSerieModel;

    public function __construct()
    {
        $this->conectar = (new Conexion())->getConexion();
        $this->numeroSerieModel = new NumeroSerie();
        $this->detalleSerieModel = new DetalleSerie();
    }

    /**
     * Obtener todas las series con sus detalles
     */
    public function getSeries()
    {
        try {
            $series = $this->numeroSerieModel->getAll();
            echo json_encode($series);
        } catch (Exception $e) {
            error_log("Error en getSeries: " . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Obtener una serie por ID (POST)
     */
    public function getOneSerie()
    {
        if (!isset($_POST["id"])) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }

        $id = $_POST["id"];
        $this->getSerieById($id);
    }

    /**
     * Obtener una serie por ID (GET - para rutas con parámetro)
     */
    public function getOneSerieById($id)
    {
        if (!$id || !is_numeric($id)) {
            echo json_encode(['success' => false, 'error' => 'ID no válido']);
            return;
        }

        $this->getSerieById($id);
    }

    /**
     * Método privado para obtener serie por ID con sus equipos
     */
    private function getSerieById($id)
    {
        try {
            // Obtener datos de la serie principal
            $serie = $this->numeroSerieModel->getById($id);

            if (!$serie) {
                echo json_encode(['success' => false, 'error' => 'Serie no encontrada']);
                return;
            }

            // Obtener equipos de la serie
            $equipos = $this->detalleSerieModel->getByNumeroSerieId($id);

            // Formatear respuesta
            $serie['tiene_cliente'] = !empty($serie['cliente_ruc_dni']) || !empty($serie['cliente_documento']);
            $serie['equipos'] = $equipos;

            echo json_encode(['success' => true, 'data' => [$serie]]);
        } catch (Exception $e) {
            error_log("Error en getSerieById: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Buscar serie por número de serie específico
     */
    public function getSerieByNumero()
    {
        if (!isset($_POST["numero_serie"])) {
            echo json_encode(['res' => false, 'msg' => 'Número de serie no proporcionado']);
            return;
        }

        try {
            $numeroSerie = $_POST["numero_serie"];
            $detalle = $this->detalleSerieModel->getByNumeroSerie($numeroSerie);

            if ($detalle) {
                echo json_encode(['res' => true, 'data' => $detalle]);
            } else {
                echo json_encode(['res' => false, 'msg' => 'Serie no encontrada']);
            }
        } catch (Exception $e) {
            error_log("Error en getSerieByNumero: " . $e->getMessage());
            echo json_encode(['res' => false, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Guardar nueva serie con sus equipos
     */
    public function saveSerie()
    {
        error_log("=== INICIO saveSerie ===");
        error_log("POST data: " . print_r($_POST, true));

        if (!isset($_POST['fecha_creacion']) || !isset($_POST['equipos'])) {
            echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos']);
            return;
        }

        $this->conectar->begin_transaction();

        try {
            // Decodificar equipos
            $equipos = is_string($_POST['equipos']) ? json_decode($_POST['equipos'], true) : $_POST['equipos'];

            if (!is_array($equipos) || empty($equipos)) {
                throw new Exception("El formato de equipos es inválido o está vacío");
            }

            // Validar números de serie duplicados
            $this->validarNumerosSerie($equipos);

            // Preparar datos de la serie principal
            $cliente_ruc_dni = !empty($_POST['cliente_ruc_dni']) ? $_POST['cliente_ruc_dni'] : null;
            $cliente_documento = !empty($_POST['cliente_documento']) ? $_POST['cliente_documento'] : null;
            $tipo_maquina = !empty($_POST['tipo_maquina']) ? $_POST['tipo_maquina'] : 'fabricada';

            // Verificar si es registro interno de la empresa
            $es_registro_interno = ($cliente_documento === '20538381978' && 
                                   $cliente_ruc_dni === 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.');

            if ($es_registro_interno) {
                $cliente_ruc_dni = null;
                $cliente_documento = null;
                $tiene_cliente = 0;
            } else {
                $tiene_cliente = !empty($cliente_ruc_dni) || !empty($cliente_documento) ? 1 : 0;
            }

            // Insertar serie principal
            $this->numeroSerieModel->setClienteRucDni($cliente_ruc_dni);
            $this->numeroSerieModel->setClienteDocumento($cliente_documento);
            $this->numeroSerieModel->setFechaCreacion($_POST['fecha_creacion']);
            $this->numeroSerieModel->setCantidadEquipos(count($equipos));
            $this->numeroSerieModel->setTipoMaquina($tipo_maquina);
            $this->numeroSerieModel->setTieneCliente($tiene_cliente);

            if (!$this->numeroSerieModel->insertar()) {
                throw new Exception("Error al insertar la serie principal");
            }

            $serie_id = $this->numeroSerieModel->getId();
            error_log("Serie insertada con ID: " . $serie_id . " - Tipo: " . $tipo_maquina);

            // Insertar equipos
            foreach ($equipos as $equipo) {
                if (!$this->validarDatosEquipo($equipo)) {
                    throw new Exception("Datos de equipo incompletos");
                }

                $this->detalleSerieModel->setNumeroSerieId($serie_id);
                $this->detalleSerieModel->setModeloId($equipo['modelo']);
                $this->detalleSerieModel->setMarcaId($equipo['marca']);
                $this->detalleSerieModel->setEquipoId($equipo['equipo']);
                // NUEVO: vínculo opcional al producto del almacén
                $idProducto = isset($equipo['id_producto']) && !empty($equipo['id_producto'])
                    ? (int)$equipo['id_producto']
                    : null;
                $this->detalleSerieModel->setIdProducto($idProducto);
                $this->detalleSerieModel->setNumeroSerie($equipo['numero_serie']);
                $this->detalleSerieModel->setEstado('disponible');
                $this->detalleSerieModel->setEstadoPrealerta('disponible');

                if (!$this->detalleSerieModel->insertar()) {
                    throw new Exception("Error al insertar equipo");
                }
            }

            $this->conectar->commit();
            error_log("=== FIN saveSerie - ÉXITO ===");
            echo json_encode(['success' => true, 'id' => $serie_id]);

        } catch (Exception $e) {
            error_log("Error en saveSerie: " . $e->getMessage());
            $this->conectar->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Actualizar serie existente con sus equipos
     */
    public function updateSerie()
    {
        error_log("=== INICIO updateSerie ===");
        error_log("POST data: " . print_r($_POST, true));

        if (!isset($_POST['id']) || !isset($_POST['fecha_creacion']) || !isset($_POST['equipos'])) {
            echo json_encode(['success' => false, 'error' => 'Faltan datos requeridos']);
            return;
        }

        $this->conectar->begin_transaction();

        try {
            $serie_id = $_POST['id'];

            // NUEVO: Solo se puede editar un lote en estado 'borrador'
            $estadoActual = $this->numeroSerieModel->getEstadoLoteById($serie_id);
            if ($estadoActual && $estadoActual !== 'borrador') {
                throw new Exception(
                    "No se puede editar este lote porque su estado es '{$estadoActual}'. " .
                    "Solo los lotes en estado 'borrador' pueden modificarse."
                );
            }

            // Decodificar equipos
            $equipos = is_string($_POST['equipos']) ? json_decode($_POST['equipos'], true) : $_POST['equipos'];

            if (!is_array($equipos) || empty($equipos)) {
                throw new Exception("El formato de equipos es inválido o está vacío");
            }

            // Validar números de serie (excluyendo los de esta serie)
            $this->validarNumerosSerie($equipos, $serie_id);

            // Preparar datos de la serie principal
            $cliente_ruc_dni = !empty($_POST['cliente_ruc_dni']) ? $_POST['cliente_ruc_dni'] : null;
            $cliente_documento = !empty($_POST['cliente_documento']) ? $_POST['cliente_documento'] : null;
            $tipo_maquina = !empty($_POST['tipo_maquina']) ? $_POST['tipo_maquina'] : 'fabricada';

            // Verificar si es registro interno
            $es_registro_interno = ($cliente_documento === '20538381978' && 
                                   $cliente_ruc_dni === 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.');

            if ($es_registro_interno) {
                $cliente_ruc_dni = null;
                $cliente_documento = null;
                $tiene_cliente = 0;
            } else {
                $tiene_cliente = !empty($cliente_ruc_dni) || !empty($cliente_documento) ? 1 : 0;
            }

            // Actualizar serie principal
            $this->numeroSerieModel->setId($serie_id);
            $this->numeroSerieModel->setClienteRucDni($cliente_ruc_dni);
            $this->numeroSerieModel->setClienteDocumento($cliente_documento);
            $this->numeroSerieModel->setFechaCreacion($_POST['fecha_creacion']);
            $this->numeroSerieModel->setCantidadEquipos(count($equipos));
            $this->numeroSerieModel->setTipoMaquina($tipo_maquina);
            $this->numeroSerieModel->setTieneCliente($tiene_cliente);

            if (!$this->numeroSerieModel->actualizar()) {
                throw new Exception("Error al actualizar la serie principal");
            }

            // Eliminar equipos existentes
            $this->detalleSerieModel->eliminarPorNumeroSerieId($serie_id);

            // Insertar nuevos equipos
            foreach ($equipos as $equipo) {
                if (!$this->validarDatosEquipo($equipo)) {
                    throw new Exception("Datos de equipo incompletos");
                }

                $this->detalleSerieModel->setNumeroSerieId($serie_id);
                $this->detalleSerieModel->setModeloId($equipo['modelo']);
                $this->detalleSerieModel->setMarcaId($equipo['marca']);
                $this->detalleSerieModel->setEquipoId($equipo['equipo']);
                // NUEVO: vínculo opcional al producto del almacén
                $idProducto = isset($equipo['id_producto']) && !empty($equipo['id_producto'])
                    ? (int)$equipo['id_producto']
                    : null;
                $this->detalleSerieModel->setIdProducto($idProducto);
                $this->detalleSerieModel->setNumeroSerie($equipo['numero_serie']);
                $this->detalleSerieModel->setEstado($equipo['estado'] ?? 'disponible');
                $this->detalleSerieModel->setEstadoPrealerta($equipo['estado_prealerta'] ?? 'disponible');

                if (!$this->detalleSerieModel->insertar()) {
                    throw new Exception("Error al insertar equipo actualizado");
                }
            }

            $this->conectar->commit();
            error_log("=== FIN updateSerie - ÉXITO ===");
            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            error_log("Error en updateSerie: " . $e->getMessage());
            $this->conectar->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Eliminar serie (CASCADE eliminará los detalles automáticamente)
     */
    public function deleteSerie()
    {
        if (!isset($_POST['id'])) {
            echo json_encode(['success' => false, 'error' => 'Falta el ID para eliminar']);
            return;
        }

        $this->conectar->begin_transaction();

        try {
            $serie_id = (int)$_POST['id'];

            // NUEVO: Si el lote está completado, revertir el stock antes de eliminar
            $serie = $this->numeroSerieModel->getById($serie_id);
            if (!$serie) {
                throw new Exception("La serie no existe");
            }

            $estadoLote = $serie['estado_lote'] ?? 'borrador';
            $esInterno  = empty($serie['cliente_ruc_dni']) || (int)($serie['tiene_cliente'] ?? 1) === 0;

            if ($estadoLote === 'completado' && $esInterno) {
                error_log("deleteSerie: lote {$serie_id} estaba COMPLETADO interno, revirtiendo stock...");
                $this->revertirStockLote($serie_id, $esInterno);
            }

            // Eliminar garantías relacionadas primero
            $sql_garantias = "DELETE g FROM garantia g
                             INNER JOIN detalle_serie ds ON g.detalle_serie_id = ds.id
                             WHERE ds.numero_serie_id = ?";
            $stmt = $this->conectar->prepare($sql_garantias);
            $stmt->bind_param("i", $serie_id);
            $stmt->execute();

            // Eliminar serie (CASCADE eliminará los detalles)
            $this->numeroSerieModel->setId($serie_id);
            if (!$this->numeroSerieModel->eliminar()) {
                throw new Exception("Error al eliminar la serie");
            }

            $this->conectar->commit();
            echo json_encode(['success' => true]);

        } catch (Exception $e) {
            error_log("Error en deleteSerie: " . $e->getMessage());
            $this->conectar->rollback();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Verificar si un número de serie ya existe
     */
    public function verificarNumeroSerie()
    {
        if (!isset($_POST['numero_serie'])) {
            echo json_encode(['success' => false, 'error' => 'Número de serie no proporcionado']);
            return;
        }

        try {
            $numero_serie = $_POST['numero_serie'];
            $excluir_id = isset($_POST['excluir_id']) ? $_POST['excluir_id'] : null;

            $existe = $this->detalleSerieModel->existeNumeroSerie($numero_serie, $excluir_id);

            echo json_encode([
                'success' => true,
                'existe' => $existe
            ]);
        } catch (Exception $e) {
            error_log("Error en verificarNumeroSerie: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Obtener el último número de serie registrado
     */
    public function getUltimoNumeroSerie()
    {
        try {
            $ultimo = $this->detalleSerieModel->getUltimoNumeroSerie();

            if ($ultimo) {
                echo json_encode(['success' => true, 'numero_serie' => $ultimo]);
            } else {
                echo json_encode(['success' => false, 'mensaje' => 'No hay números de serie registrados']);
            }
        } catch (Exception $e) {
            error_log("Error en getUltimoNumeroSerie: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Generar número de serie único de 5 dígitos
     */
    public function generarNumeroSerie()
    {
        try {
            $numeroGenerado = $this->detalleSerieModel->generarNumeroSerieUnico();

            if ($numeroGenerado) {
                echo json_encode(['success' => true, 'numero_serie' => $numeroGenerado]);
            } else {
                echo json_encode(['success' => false, 'error' => 'No se pudo generar un número único después de 100 intentos']);
            }
        } catch (Exception $e) {
            error_log("Error en generarNumeroSerie: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Obtener el próximo número de serie (NS-001, NS-002, etc.)
     */
    public function getProximoNumero()
    {
        try {
            $proximo = $this->numeroSerieModel->getProximoNumero();
            echo json_encode(['proximo_numero' => $proximo]);
        } catch (Exception $e) {
            error_log("Error en getProximoNumero: " . $e->getMessage());
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    /**
     * Verificar si una serie tiene garantías relacionadas
     */
    public function verificarGarantias()
    {
        // error_log("verificarGarantias() llamado con ID: " . ($_POST['id'] ?? 'NO DEFINIDO'));

        if (!isset($_POST['id'])) {
            echo json_encode(['success' => false, 'error' => 'ID no proporcionado']);
            return;
        }

        try {
            $serie_id = $_POST['id'];
            $this->numeroSerieModel->setId($serie_id);

            $tiene_garantias = $this->numeroSerieModel->tieneGarantias();

            if ($tiene_garantias) {
                $garantias = $this->numeroSerieModel->getGarantiasInfo();

                $ids_garantias = [];
                $numeros_garantia = [];

                foreach ($garantias as $garantia) {
                    $ids_garantias[] = $garantia['id'] ?? 'N/A';
                    // Buscar columna que contenga el número de garantía
                    foreach ($garantia as $key => $value) {
                        if (stripos($key, 'numero') !== false || stripos($key, 'codigo') !== false) {
                            $numeros_garantia[] = $value ?? 'N/A';
                            break;
                        }
                    }
                }

                $garantias_info = [
                    'total' => count($garantias),
                    'ids' => $ids_garantias,
                    'numeros' => $numeros_garantia
                ];

                echo json_encode([
                    'success' => true,
                    'tiene_garantias' => true,
                    'garantias_info' => $garantias_info
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'tiene_garantias' => false
                ]);
            }

            error_log("verificarGarantias() completado exitosamente");

        } catch (Exception $e) {
            // error_log("Error en verificarGarantias(): " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // ==================== MÉTODOS PRIVADOS DE VALIDACIÓN ====================

    /**
     * Validar que los números de serie no estén duplicados
     */
    private function validarNumerosSerie($equipos, $excluir_serie_id = null)
    {
        $numeros_serie = array_column($equipos, 'numero_serie');

        // Verificar duplicados en el array enviado
        $duplicados_internos = array_diff_assoc($numeros_serie, array_unique($numeros_serie));
        if (!empty($duplicados_internos)) {
            throw new Exception("Hay números de serie duplicados en el formulario: " . implode(', ', array_unique($duplicados_internos)));
        }

        // Verificar duplicados en la base de datos
        foreach ($numeros_serie as $numero) {
            // Obtener números de serie existentes de esta serie para excluirlos
            $numeros_existentes = [];
            if ($excluir_serie_id) {
                $detalles_existentes = $this->detalleSerieModel->getByNumeroSerieId($excluir_serie_id);
                $numeros_existentes = array_column($detalles_existentes, 'numero_serie');
            }

            // Solo verificar si el número no existía antes en esta serie
            if (!in_array($numero, $numeros_existentes)) {
                if ($this->detalleSerieModel->existeNumeroSerie($numero)) {
                    throw new Exception("El número de serie '{$numero}' ya existe en la base de datos");
                }
            }
        }
    }

    /**
     * Validar que un equipo tenga todos los datos requeridos
     */
    private function validarDatosEquipo($equipo)
    {
        return isset($equipo['modelo']) &&
               isset($equipo['marca']) &&
               isset($equipo['equipo']) &&
               isset($equipo['numero_serie']) &&
               !empty($equipo['numero_serie']);
    }

    // ================================================================
    // NUEVO: Completar lote NS y manejo de stock/kardex
    // ================================================================

    /**
     * Devolver un resumen del lote para mostrar antes de completar.
     * Retorna: { lote, tipo (interno/externo), equipos[], productos_a_afectar[] }
     */
    public function resumenLote()
    {
        try {
            $serie_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            if (!$serie_id) {
                echo json_encode(['success' => false, 'error' => 'ID de lote no proporcionado']);
                return;
            }

            $serie = $this->numeroSerieModel->getById($serie_id);
            if (!$serie) {
                echo json_encode(['success' => false, 'error' => 'Lote no encontrado']);
                return;
            }

            $equipos = $this->detalleSerieModel->getByNumeroSerieId($serie_id);

            // Agrupar por id_producto para mostrar cuánto va a aumentar/registrar
            $resumenProductos = [];
            $sinVincular = 0;
            foreach ($equipos as $eq) {
                if (!empty($eq['id_producto'])) {
                    $key = $eq['id_producto'];
                    if (!isset($resumenProductos[$key])) {
                        $resumenProductos[$key] = [
                            'id_producto'    => $eq['id_producto'],
                            'codigo'         => $eq['producto_codigo'] ?? '',
                            'nombre'         => $eq['producto_nombre'] ?? '',
                            'stock_actual'   => $eq['producto_stock'] ?? 0,
                            'cantidad_lote'  => 0,
                            'series'         => []
                        ];
                    }
                    $resumenProductos[$key]['cantidad_lote'] += 1;
                    $resumenProductos[$key]['series'][]      = $eq['numero_serie'];
                } else {
                    $sinVincular++;
                }
            }

            $esInterno = empty($serie['cliente_ruc_dni']) || (int)($serie['tiene_cliente'] ?? 1) === 0;

            echo json_encode([
                'success' => true,
                'data' => [
                    'lote'                 => $serie,
                    'es_interno'           => $esInterno,
                    'tipo'                 => $esInterno ? 'INTERNO' : 'EXTERNO',
                    'productos_a_afectar'  => array_values($resumenProductos),
                    'equipos_sin_vincular' => $sinVincular,
                    'total_equipos'        => count($equipos),
                    'estado_lote'          => $serie['estado_lote'] ?? 'borrador',
                ]
            ]);
        } catch (Exception $e) {
            error_log("Error en resumenLote: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Marcar el lote como COMPLETADO.
     *  - Si es INTERNO: aumenta el stock de cada producto vinculado y registra
     *    INGRESO en historial_stock con tipo_origen='LOTE_NS_INTERNO'.
     *  - Si es EXTERNO: NO toca el stock, solo registra INGRESO en historial_stock
     *    con tipo_origen='LOTE_NS_EXTERNO' (queda como movimiento informativo).
     */
    public function completarLote()
    {
        if (!isset($_POST['id'])) {
            echo json_encode(['success' => false, 'error' => 'Falta el ID del lote']);
            return;
        }

        $this->conectar->begin_transaction();

        try {
            $serie_id = (int)$_POST['id'];

            $serie = $this->numeroSerieModel->getById($serie_id);
            if (!$serie) {
                throw new Exception("Lote no encontrado");
            }

            $estadoActual = $serie['estado_lote'] ?? 'borrador';
            if ($estadoActual !== 'borrador') {
                throw new Exception("Solo se pueden completar lotes en estado 'borrador' (estado actual: {$estadoActual})");
            }

            $esInterno = empty($serie['cliente_ruc_dni']) || (int)($serie['tiene_cliente'] ?? 1) === 0;

            // Aplicar el efecto en stock/kardex según el tipo
            $this->aplicarStockLote($serie_id, $esInterno);

            // Marcar lote como completado
            $usuario = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre']
                       : (isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Sistema');

            $this->numeroSerieModel->setId($serie_id);
            if (!$this->numeroSerieModel->marcarComoCompletado($usuario)) {
                throw new Exception("No se pudo marcar el lote como completado");
            }

            $this->conectar->commit();
            echo json_encode([
                'success' => true,
                'mensaje' => $esInterno
                    ? 'Lote completado. Stock aumentado en almacén.'
                    : 'Lote completado. Movimiento registrado en kardex (sin afectar stock).'
            ]);
        } catch (Exception $e) {
            $this->conectar->rollback();
            error_log("Error en completarLote: " . $e->getMessage());
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Aplicar el impacto en stock al completar un lote.
     * Recorre detalle_serie del lote y:
     *  - Si interno: UPDATE productos.cantidad += 1 por cada serie vinculada
     *  - Siempre: INSERT en historial_stock para auditoría
     */
    private function aplicarStockLote($serie_id, $esInterno)
    {
        $equipos = $this->detalleSerieModel->getByNumeroSerieId($serie_id);

        $tipoOrigen = $esInterno ? 'LOTE_NS_INTERNO' : 'LOTE_NS_EXTERNO';
        $obs        = $esInterno
            ? 'Ingreso por lote NS interno (producción para stock)'
            : 'Ingreso por lote NS externo (producción para cliente)';

        // Agrupar por id_producto para hacer UPDATE atómico por producto
        $cantidadPorProducto = [];
        foreach ($equipos as $eq) {
            if (empty($eq['id_producto'])) {
                continue; // equipo no vinculado a producto, se registra solo la serie
            }
            $idP = (int)$eq['id_producto'];
            if (!isset($cantidadPorProducto[$idP])) {
                $cantidadPorProducto[$idP] = 0;
            }
            $cantidadPorProducto[$idP] += 1;
        }

        foreach ($cantidadPorProducto as $idProducto => $cantidad) {
            if ($esInterno) {
                $sql = "UPDATE productos SET cantidad = cantidad + ? WHERE id_producto = ?";
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("ii", $cantidad, $idProducto);
                if (!$stmt->execute()) {
                    throw new Exception("Error al aumentar stock del producto {$idProducto}: " . $stmt->error);
                }
            }

            $this->registrarMovimientoKardex($idProducto, 'INGRESO', $cantidad, $obs, $serie_id, $tipoOrigen);
        }
    }

    /**
     * Revertir el efecto en stock cuando se elimina/anula un lote completado interno.
     */
    private function revertirStockLote($serie_id, $esInterno)
    {
        $equipos = $this->detalleSerieModel->getByNumeroSerieId($serie_id);

        $cantidadPorProducto = [];
        foreach ($equipos as $eq) {
            if (empty($eq['id_producto'])) continue;
            $idP = (int)$eq['id_producto'];
            $cantidadPorProducto[$idP] = ($cantidadPorProducto[$idP] ?? 0) + 1;
        }

        foreach ($cantidadPorProducto as $idProducto => $cantidad) {
            if ($esInterno) {
                // Restar del stock (sin permitir negativos)
                $sql = "UPDATE productos
                        SET cantidad = GREATEST(0, cantidad - ?)
                        WHERE id_producto = ?";
                $stmt = $this->conectar->prepare($sql);
                $stmt->bind_param("ii", $cantidad, $idProducto);
                if (!$stmt->execute()) {
                    throw new Exception("Error al revertir stock del producto {$idProducto}: " . $stmt->error);
                }
            }

            $this->registrarMovimientoKardex(
                $idProducto,
                'EGRESO',
                $cantidad,
                'Reversión por eliminación de lote NS',
                $serie_id,
                $esInterno ? 'LOTE_NS_INTERNO_REVERSION' : 'LOTE_NS_EXTERNO_REVERSION'
            );
        }
    }

    /**
     * Insertar un movimiento en historial_stock (kardex).
     */
    private function registrarMovimientoKardex($idProducto, $tipoMov, $cantidad, $observaciones, $idLote, $tipoOrigen)
    {
        try {
            $usuario = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre']
                       : (isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Sistema');

            // Obtener costo del producto si existe (para mantener consistencia con otros movimientos)
            $costo = 0;
            $sqlCosto = "SELECT costo FROM productos WHERE id_producto = ?";
            $stmtC = $this->conectar->prepare($sqlCosto);
            $stmtC->bind_param("i", $idProducto);
            $stmtC->execute();
            $resC = $stmtC->get_result();
            if ($rowC = $resC->fetch_assoc()) {
                $costo = (float)($rowC['costo'] ?? 0);
            }

            $sql = "INSERT INTO historial_stock
                    (id_producto, tipo_movimiento, cantidad, costo_compra, fecha_movimiento, usuario, observaciones, id_orden_trabajo, tipo_origen)
                    VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, ?)";
            $stmt = $this->conectar->prepare($sql);
            // Tipos: i (id_producto) s (tipo_movimiento) i (cantidad)
            //        d (costo) s (usuario) s (observaciones) i (id_lote) s (tipo_origen)
            $stmt->bind_param(
                "isidssis",
                $idProducto,
                $tipoMov,
                $cantidad,
                $costo,
                $usuario,
                $observaciones,
                $idLote,
                $tipoOrigen
            );

            if (!$stmt->execute()) {
                throw new Exception("Error al registrar movimiento de kardex: " . $stmt->error);
            }
            return true;
        } catch (Exception $e) {
            error_log("Error en registrarMovimientoKardex: " . $e->getMessage());
            throw $e; // re-lanzar para que la transacción haga rollback
        }
    }
}
