<?php

require_once 'app/models/Cobranza.php';

class CobranzaController extends Controller
{
    private $cobranza;

    public function __construct()
    {
        $this->cobranza = new Cobranza();
    }
    
    public function render()
    {
        try {
            $getAll = $this->cobranza->getAllCobranzas();
            echo json_encode($getAll);
        } catch (Exception $e) {
            error_log("Error en render: " . $e->getMessage());
            echo json_encode([]);
        }
    }
    
    public function getAllByIdVenta()
    {
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                echo json_encode(['error' => 'ID de venta requerido']);
                return;
            }
            
            $getAll = $this->cobranza->getAllByIdVenta($_POST['id']);
            echo json_encode($getAll);
        } catch (Exception $e) {
            error_log("Error en getAllByIdVenta: " . $e->getMessage());
            echo json_encode(['error' => 'Error interno del servidor']);
        }
    }
    
    public function pagarCuota()
    {
        try {
            if (!isset($_POST['id']) || empty($_POST['id'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de cuota requerido'
                ]);
                return;
            }
            
            $resultado = $this->cobranza->pagarCuota($_POST['id']);
            echo json_encode($resultado);
        } catch (Exception $e) {
            error_log("Error en pagarCuota: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor'
            ]);
        }
    }
}