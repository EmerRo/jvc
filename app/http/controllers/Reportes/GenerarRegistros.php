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
        // Consulta actualizada para la estructura normalizada
        $sql = "SELECT ns.id, ns.numero, ns.cliente_ruc_dni, ns.fecha_creacion,
                   ds.id as detalle_id,
                   ds.numero_serie,
                   m.nombre as modelo_nombre,
                   ma.nombre as marca_nombre,
                   e.nombre as equipo_nombre
            FROM numero_series ns
            LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
            LEFT JOIN modelos m ON ds.modelo_id = m.id
            LEFT JOIN marcas ma ON ds.marca_id = ma.id
            LEFT JOIN equipos e ON ds.equipo_id = e.id
            ORDER BY ns.numero DESC, ds.id ASC";

        $result = $this->conexion->query($sql);

        $tbody = '';
        $contador = 1;

        foreach ($result as $fila) {
            // Determinar el cliente
            $cliente = $fila['cliente_ruc_dni'] ?: 'Registro Interno (JVC)';
            
            // Si no hay detalle de serie, mostrar solo el registro principal
            if (!$fila['detalle_id']) {
                $tbody .= '
            <tr>
                <td style="text-align: center;">' . $contador++ . '</td>
                <td style="text-align: center;">NS-' . str_pad($fila['numero'], 2, '0', STR_PAD_LEFT) . '</td>
                <td style="text-align: center;">' . $cliente . '</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">-</td>
                <td style="text-align: center;">' . $fila['fecha_creacion'] . '</td>
            </tr>';
                continue;
            }

            // Mostrar cada equipo en una fila
            $tbody .= '
            <tr>
                <td style="text-align: center;">' . $contador++ . '</td>
                <td style="text-align: center;">NS-' . str_pad($fila['numero'], 2, '0', STR_PAD_LEFT) . '</td>
                <td style="text-align: center;">' . $cliente . '</td>
                <td style="text-align: center;">' . ($fila['marca_nombre'] ?: '-') . '</td>
                <td style="text-align: center;">' . ($fila['modelo_nombre'] ?: '-') . '</td>
                <td style="text-align: center;">' . ($fila['equipo_nombre'] ?: '-') . '</td>
                <td style="text-align: center;">' . ($fila['numero_serie'] ?: '-') . '</td>
                <td style="text-align: center;">' . $fila['fecha_creacion'] . '</td>
            </tr>';
        }

        // Crear la tabla HTML
        $tabla = "
    <table>
        <tr>
            <th style='background-color: #90BFEB; width: 7px; text-align: center;'>N°</th>
            <th style='background-color: #90BFEB; width: 15px; text-align: center;'>Registro</th>
            <th style='background-color: #90BFEB; width: 35px; text-align: center;'>Cliente</th>
            <th style='background-color: #90BFEB; width: 25px; text-align: center;'>Marca</th>
            <th style='background-color: #90BFEB; width: 25px; text-align: center;'>Modelo</th>
            <th style='background-color: #90BFEB; width: 25px; text-align: center;'>Equipo</th>
            <th style='background-color: #90BFEB; width: 20px; text-align: center;'>Número de Serie</th>
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
}