CREATE TABLE orden_trabajo_repuestos (
    id_repuesto_orden INT AUTO_INCREMENT PRIMARY KEY,
    id_orden_trabajo INT NOT NULL,
    id_detalle_maquina INT NOT NULL, -- Para identificar a qué máquina pertenece
    id_repuesto INT NOT NULL,
    nombre_repuesto VARCHAR(255) NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    precio_total DECIMAL(10,2) NOT NULL,
    fecha_agregado TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_orden_trabajo) REFERENCES orden_trabajo_pre(id_orden_trabajo) ON DELETE CASCADE,
    FOREIGN KEY (id_repuesto) REFERENCES repuestos(id_repuesto)
);
-- Modificaciones necesarias en la base de datos
-- 1. Permitir valores NULL en los campos de cliente
ALTER TABLE numero_series 
MODIFY COLUMN cliente_ruc_dni VARCHAR(255) NULL,
MODIFY COLUMN cliente_documento VARCHAR(20) NULL;

-- 2. Agregar columna para identificar registros sin cliente (opcional pero recomendado)
ALTER TABLE numero_series 
ADD COLUMN tiene_cliente TINYINT(1) DEFAULT 1 COMMENT '1=con cliente, 0=sin cliente';

-- 3. Crear índice para mejorar performance en la búsqueda del último número de serie
CREATE INDEX idx_numero_serie_json ON detalle_serie((CAST(JSON_EXTRACT(numero_serie, '$[0]') AS UNSIGNED)));


ALTER TABLE orden_trabajo_pre MODIFY COLUMN cliente_ruc VARCHAR(20) NULL;


-- Primero elimina la foreign key existente
ALTER TABLE `orden_trabajo_repuestos` 
DROP FOREIGN KEY `orden_trabajo_repuestos_ibfk_2`;

-- Modifica la tabla para agregar campos que identifiquen el tipo
ALTER TABLE `orden_trabajo_repuestos` 
ADD COLUMN `tipo_item` ENUM('producto', 'repuesto') NOT NULL DEFAULT 'repuesto' AFTER `id_detalle_maquina`,
ADD COLUMN `codigo_item` VARCHAR(50) NULL AFTER `nombre_repuesto`;

-- Cambia el nombre del campo para ser más genérico
ALTER TABLE `orden_trabajo_repuestos` 
CHANGE `id_repuesto` `id_item` INT(11) NOT NULL,
CHANGE `nombre_repuesto` `nombre_item` VARCHAR(255) NOT NULL;
