-- =====================================================
-- MIGRACIÓN: Crear tabla historial_stock_repuestos
-- Proyecto: JVC - Sistema de Facturación Electrónica
-- Fecha: 2026-05-19
-- Propósito: Registrar movimientos de stock de repuestos
-- Status: Esta tabla NO existe en la BD pero es referenciada
--         por RepuestosController.php (líneas 662, 713, 816, 839)
-- =====================================================

-- =====================================================
-- PASO 1: Verificar que la tabla NO existe
-- =====================================================

-- Debe dar error 1146: Table doesn't exist
DESCRIBE historial_stock_repuestos;

-- =====================================================
-- PASO 2: Crear la tabla
-- =====================================================

CREATE TABLE IF NOT EXISTS historial_stock_repuestos (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    id_repuesto INT NOT NULL,
    tipo_movimiento ENUM('INGRESO', 'EGRESO') NOT NULL,
    cantidad INT NOT NULL,
    costo_compra DECIMAL(10,2) NULL,
    fecha_movimiento DATETIME NOT NULL,
    usuario VARCHAR(100) NULL,
    observaciones TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices para consultas frecuentes
    INDEX idx_repuesto (id_repuesto),
    INDEX idx_fecha (fecha_movimiento),
    INDEX idx_tipo (tipo_movimiento),
    INDEX idx_repuesto_fecha (id_repuesto, fecha_movimiento DESC)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PASO 3: Verificar estructura
-- =====================================================

DESCRIBE historial_stock_repuestos;

-- =====================================================
-- CONSULTAS DE VERIFICACIÓN POST-CREACIÓN
-- =====================================================

-- Ver todos los movimientos (vacío al inicio)
SELECT * FROM historial_stock_repuestos LIMIT 10;

-- Ver estructura de historial_stock (productos) para comparar
DESCRIBE historial_stock;

-- =====================================================
-- NOTAS:
-- - Estructura idéntica a historial_stock para productos
-- - Incluye costo_compra para calcular costo promedio
-- - Índices optimizados para consultas por repuesto y fecha
-- =====================================================