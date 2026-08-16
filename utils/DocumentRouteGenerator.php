<?php
// utils/DocumentRouteGenerator.php

class DocumentRouteGenerator
{
    /**
     * Genera rutas automáticamente para un tipo de documento
     */
    public static function generateRoutes($documentType, $controller)
    {
        $routes = [
            // Rutas básicas CRUD
            ['method' => 'GET', 'path' => "/ajs/{$documentType}/render", 'action' => 'render'],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/getOne", 'action' => 'getOne'],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/insertar", 'action' => 'insertar'],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/editar", 'action' => 'editar'],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/borrar", 'action' => 'borrar'],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/borrar-masivo", 'action' => 'borrarMasivo'],
            
            // Rutas para PDF
            ['method' => 'GET', 'path' => "/ajs/{$documentType}/generarPDF", 'action' => 'generarPDF'],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/vista-previa", 'action' => 'vistaPreviaPDF'],
            
            // Rutas para plantillas
            ['method' => 'GET', 'path' => "/ajs/{$documentType}/obtener-template", 'action' => 'obtenerTemplate'],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/guardar-template", 'action' => 'guardarTemplate'],
            
            // Rutas para tipos
            ['method' => 'GET', 'path' => "/ajs/{$documentType}/getTipos", 'action' => 'getTipos'],
            
            // Rutas para membretes
            ['method' => 'GET', 'path' => "/ajs/{$documentType}/obtener-membretes", 'action' => 'obtenerMembretes'],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/guardar-membretes", 'action' => 'guardarMembretes'],
            
            // Ruta para compartir por WhatsApp
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/compartir-whatsapp", 'action' => 'compartirWhatsApp'],
        ];
        
        // Rutas específicas para tipos de documentos
        $tipoRoutes = [
            ['method' => 'GET', 'path' => "/ajs/{$documentType}/obtener-tipos-{$documentType}s", 'action' => "obtenerTipo" . ucfirst($documentType) . "s"],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/insertar-tipo-{$documentType}", 'action' => "insertarTipo" . ucfirst($documentType)],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/editar-tipo-{$documentType}", 'action' => "editarTipo" . ucfirst($documentType)],
            ['method' => 'POST', 'path' => "/ajs/{$documentType}/eliminar-tipo-{$documentType}", 'action' => "eliminarTipo" . ucfirst($documentType)],
        ];
        
        return array_merge($routes, $tipoRoutes);
    }
    
    /**
     * Registra las rutas en el sistema de routing
     */
    public static function registerRoutes($documentType, $controller, $middleware = [])
    {
        $routes = self::generateRoutes($documentType, $controller);
        
        foreach ($routes as $route) {
            $routeDefinition = Route::{$route['method']}($route['path'], "{$controller}@{$route['action']}");
            
            if (!empty($middleware)) {
                $routeDefinition->Middleware($middleware);
            }
        }
    }
}