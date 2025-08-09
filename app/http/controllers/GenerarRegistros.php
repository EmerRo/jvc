<?php

require_once 'utils/lib/vendor/autoload.php';
// require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once 'utils/lib/exel/vendor/autoload.php';

class GenerarRegistros extends Controller
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
    }

    public function generarExcelSeries()
    {
        // Primero obtenemos todos los registros con los datos JSON
        $sql = "SELECT ns.id, ns.cliente_ruc_dni, ns.fecha_creacion,
                   ds.modelo, ds.marca, ds.equipo, ds.numero_serie
            FROM numero_series ns
            LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
            ORDER BY ns.id DESC";

        $result = $this->conexion->query($sql);

        $tbody = '';
        $contador = 1;

        foreach ($result as $fila) {
            // Decodificar los arrays JSON
            $modelos = json_decode($fila['modelo'], true) ?: [];
            $marcas = json_decode($fila['marca'], true) ?: [];
            $equipos = json_decode($fila['equipo'], true) ?: [];
            $numeros_serie = json_decode($fila['numero_serie'], true) ?: [];

            // Determinar la cantidad máxima de elementos
            $max_count = max(count($modelos), count($marcas), count($equipos), count($numeros_serie));

            // Si no hay datos en los arrays, mostrar una fila vacía
            if ($max_count === 0) {
                $tbody .= '
            <tr>
                <td style="text-align: center;">' . $contador++ . '</td>
                <td style="text-align: center;">' . ($fila['cliente_ruc_dni'] ?: 'Sin Cliente') . '</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">' . $fila['fecha_creacion'] . '</td>
            </tr>';
                continue;
            }

            // Crear una fila por cada equipo
            for ($i = 0; $i < $max_count; $i++) {
                // Obtener IDs para hacer consultas individuales
                $modelo_id = isset($modelos[$i]) ? $modelos[$i] : null;
                $marca_id = isset($marcas[$i]) ? $marcas[$i] : null;
                $equipo_id = isset($equipos[$i]) ? $equipos[$i] : null;
                $numero_serie = isset($numeros_serie[$i]) ? $numeros_serie[$i] : '';

                // Obtener nombres reales de las tablas
                $modelo_nombre = $this->obtenerNombre('modelos', $modelo_id);
                $marca_nombre = $this->obtenerNombre('marcas', $marca_id);
                $equipo_nombre = $this->obtenerNombre('equipos', $equipo_id);

                $tbody .= '
            <tr>
                <td style="text-align: center;">' . $contador++ . '</td>
                <td style="text-align: center;">' . ($fila['cliente_ruc_dni'] ?: 'Sin Cliente') . '</td>
                <td style="text-align: center;">' . $marca_nombre . '</td>
                <td style="text-align: center;">' . $modelo_nombre . '</td>
                <td style="text-align: center;">' . $equipo_nombre . '</td>
                <td style="text-align: center;">' . $numero_serie . '</td>
                <td style="text-align: center;">' . $fila['fecha_creacion'] . '</td>
            </tr>';
            }
        }

        // Crear la tabla HTML
        $tabla = "
    <table>
        <tr>
            <th style='background-color: #90BFEB; width: 7px; text-align: center;'>ID</th>
            <th style='background-color: #90BFEB; width: 35px; text-align: center;'>Cliente RUC/DNI</th>
            <th style='background-color: #90BFEB; width: 35px; text-align: center;'>Marca</th>
            <th style='background-color: #90BFEB; width: 35px; text-align: center;'>Modelo</th>
            <th style='background-color: #90BFEB; width: 35px; text-align: center;'>Equipo</th>
            <th style='background-color: #90BFEB; width: 35px; text-align: center;'>Número de Serie</th>
            <th style='background-color: #90BFEB; width: 17px; text-align: center;'>Fecha de Creación</th>
        </tr>
        <tbody>
            " . $tbody . "
        </tbody>
    </table>";

        $nombre_excel = "registros_de_series_" . date('Y-m-d_H-i-s') . ".xlsx";
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");

        // Guardar el archivo en el servidor
        $writer->save($nombre_excel);

        // Redirigir para descargar el archivo
        header('Content-Disposition: attachment; filename="' . $nombre_excel . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        readfile($nombre_excel);

        // Limpiar el archivo temporal
        unlink($nombre_excel);
        exit;
    }

    /**
     * Método auxiliar para obtener el nombre de una entidad por su ID
     */
    private function obtenerNombre($tabla, $id)
    {
        if (empty($id) || !is_numeric($id)) {
            return '-';
        }

        $stmt = $this->conexion->prepare("SELECT nombre FROM {$tabla} WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($row = $resultado->fetch_assoc()) {
            return $row['nombre'];
        }

        return '-';
    }
}