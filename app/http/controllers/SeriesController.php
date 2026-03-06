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
            $serie_id = $_POST['id'];

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
        error_log("verificarGarantias() llamado con ID: " . ($_POST['id'] ?? 'NO DEFINIDO'));

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
            error_log("Error en verificarGarantias(): " . $e->getMessage());
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
}
