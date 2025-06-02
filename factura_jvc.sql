-- Tabla para las plantillas de informes
CREATE TABLE IF NOT EXISTS informe_template (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL DEFAULT 'INFORME',
    contenido LONGTEXT,
    header_image LONGTEXT,
    footer_image LONGTEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla para los informes generados
CREATE TABLE IF NOT EXISTS informes (
    id_informe INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(100) NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    contenido LONGTEXT,
    header_image LONGTEXT,
    footer_image LONGTEXT,
    cliente_id INT,
    usuario_id INT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (tipo),
    INDEX (cliente_id)
);



-- Tabla para los tipos/motivos de cartas
CREATE TABLE IF NOT EXISTS carta_tipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla para las plantillas de cartas
CREATE TABLE IF NOT EXISTS carta_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(100) NOT NULL,
    contenido LONGTEXT,
    header_image VARCHAR(255),
    footer_image VARCHAR(255),
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla para las cartas generadas (con claves foráneas corregidas)
CREATE TABLE IF NOT EXISTS cartas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT,
    id_usuario INT NOT NULL,
    tipo VARCHAR(100),
    titulo VARCHAR(150) NOT NULL,
    contenido LONGTEXT,
    header_image VARCHAR(255),
    footer_image VARCHAR(255),
    estado ENUM('borrador', 'finalizado') DEFAULT 'borrador',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(usuario_id) ON DELETE CASCADE
);

-- Insertar algunos tipos de carta por defecto
INSERT INTO carta_tipos (nombre, descripcion) VALUES 
('Carta de Compromiso', 'Cartas que establecen compromisos entre partes'),
('Carta de Presentación', 'Cartas para presentar personas o empresas'),
('Carta de Recomendación', 'Cartas que recomiendan a personas o servicios'),
('Carta de Solicitud', 'Cartas para solicitar servicios o información');

-- Tabla para constancias
CREATE TABLE IF NOT EXISTS constancias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    tipo VARCHAR(100) NOT NULL COMMENT 'Tipo: MANTENIMIENTO, ANTIGÜEDAD, GARANTÍA, etc.',
    cliente_id INT NULL,
    usuario_id INT NULL,
    contenido LONGTEXT NOT NULL,
    header_image LONGTEXT NULL COMMENT 'Imagen de cabecera en base64',
    footer_image LONGTEXT NULL COMMENT 'Imagen de pie en base64',
    estado VARCHAR(50) DEFAULT 'borrador',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para plantillas de constancias
CREATE TABLE IF NOT EXISTS constancias_plantillas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido LONGTEXT NOT NULL,
    header_image LONGTEXT NULL COMMENT 'Imagen de cabecera en base64',
    footer_image LONGTEXT NULL COMMENT 'Imagen de pie en base64',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar plantilla por defecto
INSERT INTO constancias_plantillas (titulo, contenido) 
VALUES ('Plantilla de Constancia Predeterminada', '<h2 style="text-align: center;">CONSTANCIA</h2><p><br></p><p>Por medio de la presente, se hace constar que:</p><p><br></p><p style="text-align: center;"><strong>[NOMBRE DEL CLIENTE]</strong></p><p><br></p><p>Ha recibido el servicio de [TIPO DE SERVICIO] para el equipo [EQUIPO] con número de serie [NÚMERO DE SERIE], el día [FECHA].</p><p><br></p><p>Se extiende la presente constancia para los fines que el interesado considere conveniente.</p><p><br></p><p>Atentamente,</p><p><br></p><p>[NOMBRE DE LA EMPRESA]</p>');

-- Tabla para archivos internos
CREATE TABLE IF NOT EXISTS archivos_internos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    tipo VARCHAR(100) NOT NULL COMMENT 'Tipo: MEMO, INFORME, ACTA, REPORTE, etc.',
    cliente_id INT NULL,
    usuario_id INT NULL,
    contenido LONGTEXT NULL COMMENT 'Contenido HTML del documento',
    archivo_pdf LONGTEXT NULL COMMENT 'Archivo PDF en base64 (cuando se sube un PDF)',
    header_image LONGTEXT NULL COMMENT 'Imagen de cabecera en base64',
    footer_image LONGTEXT NULL COMMENT 'Imagen de pie en base64',
    es_pdf_subido TINYINT(1) DEFAULT 0 COMMENT '1 si es un PDF subido, 0 si es un documento creado',
    estado VARCHAR(50) DEFAULT 'borrador',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES clientes(id_cliente) ON DELETE SET NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(usuario_id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla para plantillas de archivos internos
CREATE TABLE IF NOT EXISTS archivos_internos_plantillas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    contenido LONGTEXT NOT NULL,
    header_image LONGTEXT NULL COMMENT 'Imagen de cabecera en base64',
    footer_image LONGTEXT NULL COMMENT 'Imagen de pie en base64',
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar plantilla por defecto
INSERT INTO archivos_internos_plantillas (titulo, contenido) 
VALUES ('Plantilla de Documento Interno Predeterminada', '<h2 style="text-align: center;">DOCUMENTO INTERNO</h2><p><br></p><p>Fecha: [FECHA]</p><p>Asunto: [ASUNTO]</p><p><br></p><p>Contenido del documento...</p><p><br></p><p>Atentamente,</p><p><br></p><p>[NOMBRE]</p><p>[CARGO]</p>');

-- este es 
ALTER TABLE detalle_serie ADD COLUMN estado ENUM('disponible', 'en_garantia') NOT NULL DEFAULT 'disponible';


-- Paso 1: Eliminar la restricción de clave foránea existente
ALTER TABLE `archivos_internos`
DROP FOREIGN KEY `archivos_internos_ibfk_1`;

-- Paso 2: Eliminar el índice existente (si existe)
ALTER TABLE `archivos_internos`
DROP INDEX `cliente_id`;

-- Paso 3: Renombrar la columna
ALTER TABLE `archivos_internos`
CHANGE COLUMN `cliente_id` `id_cliente` int(11) DEFAULT NULL;

-- Paso 4: Recrear el índice con el nuevo nombre
ALTER TABLE `archivos_internos`
ADD INDEX `id_cliente` (`id_cliente`);

-- Paso 5: Recrear la restricción de clave foránea
ALTER TABLE `archivos_internos`
ADD CONSTRAINT `archivos_internos_ibfk_1` 
FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL;

-- Insertar el módulo DOCUMENTOS
INSERT INTO `modulos` (`nombre`, `descripcion`, `icono`, `ruta`) VALUES 
('DOCUMENTOS', 'Gestión de documentos del sistema', 'ri-file-text-line', '/jvc/documentos');

-- Insertar los submódulos usando el ID del módulo recién creado
INSERT INTO `submodulos` (`modulo_id`, `nombre`, `descripcion`, `ruta`) VALUES
((SELECT modulo_id FROM modulos WHERE nombre = 'DOCUMENTOS'), 'Ficha técnica', 'Gestión de fichas técnicas', '/jvc/documentos'),
((SELECT modulo_id FROM modulos WHERE nombre = 'DOCUMENTOS'), 'Informe', 'Gestión de informes', '/jvc/documentos/informe'),
((SELECT modulo_id FROM modulos WHERE nombre = 'DOCUMENTOS'), 'Cartas', 'Gestión de cartas', '/jvc/documentos/cartas'),
((SELECT modulo_id FROM modulos WHERE nombre = 'DOCUMENTOS'), 'Constancias', 'Gestión de constancias', '/jvc/documentos/constancias'),
((SELECT modulo_id FROM modulos WHERE nombre = 'DOCUMENTOS'), 'Archivos internos', 'Gestión de archivos internos', '/jvc/documentos/archivos/internos'),
((SELECT modulo_id FROM modulos WHERE nombre = 'DOCUMENTOS'), 'Otros', 'Gestión de otros documentos', '/jvc/documentos/otros');

CREATE TABLE IF NOT EXISTS `observacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `detalle` text COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

CREATE TABLE IF NOT EXISTS `observaciones_compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_compra` int(11) NOT NULL,
  `observaciones` text COLLATE utf8_spanish_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_compra` (`id_compra`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


INSERT INTO `observacion` (`detalle`) VALUES 
('• Los productos deben entregarse en perfecto estado y con su embalaje original.
• El proveedor debe cumplir con los plazos de entrega establecidos.
• Cualquier producto defectuoso será devuelto y deberá ser reemplazado sin costo adicional.
• La factura debe incluir el número de orden de compra como referencia.
• El pago se realizará según los términos acordados una vez verificada la mercancía.
• El proveedor debe proporcionar garantía para todos los productos suministrados.
• Los precios acordados no pueden ser modificados sin previo aviso por escrito.
• La empresa se reserva el derecho de cancelar la orden si no se cumplen las condiciones.');


ALTER TABLE guia_remision ADD COLUMN id_cotizacion INT NULL AFTER id_venta;

CREATE TABLE `metas_ventas` (
  `id_meta` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) NOT NULL,
  `id_vendedor` int(11) NOT NULL,
  `meta_mensual` decimal(10,2) NOT NULL,
  `mes` int(2) NOT NULL,
  `anio` int(4) NOT NULL,
  `fecha_creacion` timestamp DEFAULT CURRENT_TIMESTAMP,
  `estado` char(1) DEFAULT '1',
  PRIMARY KEY (`id_meta`),
  KEY `idx_empresa_vendedor_periodo` (`id_empresa`, `id_vendedor`, `mes`, `anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;


-- NUEVA TABLA para metas de empresa (no individuales)
CREATE TABLE IF NOT EXISTS `metas_empresa` (
  `id_meta_empresa` int(11) NOT NULL AUTO_INCREMENT,
  `id_empresa` int(11) NOT NULL,
  `meta_total` decimal(10,2) NOT NULL,
  `mes` int(2) NOT NULL,
  `anio` int(4) NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `estado` char(1) DEFAULT '1',
  PRIMARY KEY (`id_meta_empresa`),
  KEY `idx_empresa_mes_anio` (`id_empresa`, `mes`, `anio`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- Ejemplo de meta total para mayo 2025
INSERT INTO `metas_empresa` (`id_empresa`, `meta_total`, `mes`, `anio`, `fecha_creacion`, `estado`) 
VALUES (12, 50000.00, 5, 2025, NOW(), '1');

ALTER TABLE `detalle_serie` 
ADD COLUMN `estado_prealerta` ENUM('disponible', 'en_trabajo', 'culminado') 
NOT NULL DEFAULT 'disponible' 
AFTER `estado`;


ALTER TABLE numero_series ADD COLUMN cliente_documento VARCHAR(11) AFTER cliente_ruc_dni;