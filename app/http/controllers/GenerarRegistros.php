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
        // Consulta modificada para unir todas las tablas necesarias y obtener los nombres
        $sql = "SELECT ns.cliente_ruc_dni, 
                      mo.nombre AS modelo_nombre, 
                      ma.nombre AS marca_nombre, 
                      e.nombre AS equipo_nombre, 
                      ds.numero_serie, 
                      ns.fecha_creacion 
               FROM numero_series ns
               LEFT JOIN detalle_serie ds ON ns.id = ds.numero_serie_id
               LEFT JOIN modelos mo ON ds.modelo = mo.id
               LEFT JOIN marcas ma ON ds.marca = ma.id
               LEFT JOIN equipos e ON ds.equipo = e.id
               ORDER BY ns.id, ds.id";
                
        $result = $this->conexion->query($sql);
    
        $tbody = '';
        $contador = 1; // Iniciar el contador
    
        foreach ($result as $fila) {
            $tbody .= '
            <tr>
                <td style="text-align: center;">' . $contador++ . '</td>
                <td style="text-align: center;">' . $fila['cliente_ruc_dni'] . '</td>
                <td style="text-align: center;">' . $fila['marca_nombre'] . '</td>
                <td style="text-align: center;">' . $fila['modelo_nombre'] . '</td>
                <td style="text-align: center;">' . $fila['equipo_nombre'] . '</td>
                <td>' . $fila['numero_serie'] . '</td>
                <td>' . $fila['fecha_creacion'] . '</td>
            </tr>';
        }
    
        // El resto del código permanece igual
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
    
        $nombre_excel = "registros_de_series.xlsx";
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Html();
        $spreadsheet = $reader->loadFromString($tabla);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, "Xlsx");
    
        // Guardar el archivo en el servidor
        $writer->save($nombre_excel);
    
        // Redirigir para descargar el archivo
        header('Content-Disposition: attachment; filename="' . $nombre_excel . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        readfile($nombre_excel);
        exit;
    }
}