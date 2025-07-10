<?php

require_once 'utils/lib/mpdf/vendor/autoload.php';
require_once "utils/lib/code/vendor/autoload.php";
use Picqer\Barcode\BarcodeGeneratorPNG;

require_once "app/clases/serverside.php";


class ConsultaDelcontroller extends Controller
{
    private $conexion;
    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();

        /*   $c_producto->setIdEmpresa($_SESSION['id_empresa']); */
    }

    public function getDataCotizacionSS(){
        $table_data = new TableData();
        
        $user_id = ($_SESSION['rol'] == 1) ? "" : "where usuario = '{$_SESSION['usuario_fac']}' and sucursal = '{$_SESSION['sucursal']}'";
        $table_data->get("view_cotizaciones", "cotizacion_id", [
            "numero",
            "fecha",
            "documento",
            "total", //aqui sera el subtotal
            "total", //aqui ira el IGV
            "total",
            "vendedor",
            "estado",
            "cotizacion_id",
            "cotizacion_id",
            "cotizacion_id",
        ], $user_id);
    }
   public function generarBarCode2() {
    // Verificar que el código existe antes de continuar
    if (!isset($_GET['code']) || empty($_GET['code'])) {
        die("ERROR: You should provide a barcode string.");
    }

    // Buscar el producto por código en lugar de por ID
    $codigo = trim($_GET['code']);
    $sql = "SELECT * FROM productos WHERE codigo = ?";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();

    // Verificar si se encontró el producto
    if (!$producto) {
        // Si no se encuentra el producto, usar directamente el código proporcionado
        $barcodeData = $codigo;
        $descripcion = "Producto no encontrado";
        $precio_unidad = 0;
        $precio4 = 0;
    } else {
        $barcodeData = trim($producto['codigo']);
        $descripcion = $producto['nombre'] ?? '';
        $precio_unidad = $producto['precio_unidad'] ?? 0;
        $precio4 = $producto['precio4'] ?? 0;
    }

    $barcodeGenerator = new BarcodeGeneratorPNG();
    $fontSixe1 = '16px'; // Aumentado de 13px a 16px
    $fontSixe2 = '18px'; // Aumentado de 15px a 18px

    // Genera el código de barras como una imagen PNG
    $barcodeImage = $barcodeGenerator->getBarcode($barcodeData, $barcodeGenerator::TYPE_CODE_128_B);
    
    if (isset($_GET['scal']) && $_GET['scal'] == 2) {
        $fontSixe1 = '14px'; // Aumentado de 10px a 14px
        $fontSixe2 = '16px'; // Aumentado de 14px a 16px
    }

    // Agrega la imagen del código de barras al contenido del PDF
    $html = '<div style="font-family: Arial, Helvetica, sans-serif; width: 100%; text-align: center">
        <span style="font-size: '.$fontSixe1.'; font-weight: bold;">'.$descripcion.'</span>
        <br>
    </div>'; 
    
    // Añadir la imagen del código de barras - centrado correctamente
    $html .= '<img style="display: block; margin: 0 auto;" src="data:image/png;base64,' . base64_encode($barcodeImage) . '">';
    
    // Código del producto con fuente más grande y mejor centrado
    $html .= '<div style="font-weight: bold; font-family: Arial, Helvetica, sans-serif; width: 100%; text-align: center; font-size: '.$fontSixe2.';">
        <span style="font-size: 20px;">'.$barcodeData.'</span>
    </div>';

    $this->mpdf = new \Mpdf\Mpdf([
        'margin_bottom' => 1,
        'margin_top' => 5,
        'margin_left' => 3,
        'margin_right' => 3,
        'mode' => 'utf-8',
    ]);

    $this->mpdf->AddPageByArray([
        "orientation" => "P",
        "newformat" => [75, 50], //
    ]);
    
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
}
  public function generarBarCode() {
    // Verificar que el código existe antes de continuar
    if (!isset($_GET['code']) || empty($_GET['code'])) {
        die("ERROR: You should provide a barcode string.");
    }

    // Buscar el producto por código en lugar de por ID
    $codigo = trim($_GET['code']);
    $sql = "SELECT * FROM productos WHERE codigo = ?";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bind_param("s", $codigo);
    $stmt->execute();
    $result = $stmt->get_result();
    $producto = $result->fetch_assoc();

    // Verificar si se encontró el producto
    if (!$producto) {
        // Si no se encuentra el producto, usar directamente el código proporcionado
        $barcodeData = $codigo;
        $descripcion = "Producto no encontrado";
        $precio_unidad = 0;
        $precio4 = 0;
    } else {
        $barcodeData = trim($producto['codigo']);
        $descripcion = $producto['nombre'] ?? '';
        $precio_unidad = $producto['precio_unidad'] ?? 0;
        $precio4 = $producto['precio4'] ?? 0;
    }

    $barcodeGenerator = new BarcodeGeneratorPNG();
    $fontSixe1 = '12px'; // Aumentado de 10px a 12px
    $fontSixe2 = '14px'; // Aumentado de 12px a 14px

    // Genera el código de barras como una imagen PNG
    $barcodeImage = $barcodeGenerator->getBarcode($barcodeData, $barcodeGenerator::TYPE_CODE_128_B);
    
    if (isset($_GET['scal']) && $_GET['scal'] == 2) {
        $fontSixe1 = '9px'; // Aumentado de 7px a 9px
        $fontSixe2 = '13px'; // Aumentado de 11px a 13px
    }

    // Agrega la imagen del código de barras al contenido del PDF
    $html = '<div style="font-family: Arial, Helvetica, sans-serif; width: 100%; text-align: center">
        <span style="font-size: '.$fontSixe1.'; font-weight: bold;">'.$descripcion.'</span>
    </div>';
    
    // Añadir la imagen del código de barras - centrado correctamente
    $html .= '<img style="display: block; margin: 0 auto;" src="data:image/png;base64,' . base64_encode($barcodeImage) . '">';
    
    // Código del producto con fuente más grande y mejor centrado
    $html .= '<div style="font-weight: bold; font-family: Arial, Helvetica, sans-serif; width: 100%; text-align: center; font-size: '.$fontSixe2.';">
        <span style="font-size: 16px;">'.$barcodeData.'</span>
    </div>';

    $this->mpdf = new \Mpdf\Mpdf([
        'margin_bottom' => 1,
        'margin_top' => 1,
        'margin_left' => 3,
        'margin_right' => 3,
        'mode' => 'utf-8',
    ]);

    $this->mpdf->AddPageByArray([
        "orientation" => "P",
        "newformat" => [50, 30], //
    ]);
    
    $this->mpdf->WriteHTML($html, \Mpdf\HTMLParserMode::HTML_BODY);
    $this->mpdf->Output();
}

public function getDataTallerCotizacionSS() {
    $table_data = new TableData();
    
    $user_id = "";
    $table_data->get("view_taller_cotizaciones", "cotizacion_id", [
        "numero",
        "fecha", 
        "documento",
        "vendedor",
        "tipo_origen",  // ✅ AGREGAR ESTA LÍNEA
        "cotizacion_id",
        "cotizacion_id"
    ], $user_id);
}

}
