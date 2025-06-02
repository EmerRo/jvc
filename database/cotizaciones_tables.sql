-- Tabla de cotizaciones
CREATE TABLE IF NOT EXISTS `cotizaciones` (
  `id_cotizacion` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_doc` varchar(20) NOT NULL,
  `cliente_nombre` varchar(200) NOT NULL,
  `cliente_direccion` text,
  `fecha` date NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `id_preAlerta` int(11),
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cotizacion`),
  KEY `id_preAlerta` (`id_preAlerta`),
  CONSTRAINT `cotizaciones_ibfk_1` FOREIGN KEY (`id_preAlerta`) REFERENCES `pre_alerta` (`id_preAlerta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de equipos en cotización
CREATE TABLE IF NOT EXISTS `cotizacion_equipos` (
  `id_equipo` int(11) NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int(11) NOT NULL,
  `equipo` varchar(100) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `numero_serie` varchar(100) NOT NULL,
  PRIMARY KEY (`id_equipo`),
  KEY `id_cotizacion` (`id_cotizacion`),
  CONSTRAINT `cotizacion_equipos_ibfk_1` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id_cotizacion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de repuestos por equipo
CREATE TABLE IF NOT EXISTS `cotizacion_repuestos` (
  `id_repuesto` int(11) NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int(11) NOT NULL,
  `id_equipo` int(11) NOT NULL,
  `descripcion` varchar(200) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id_repuesto`),
  KEY `id_cotizacion` (`id_cotizacion`),
  KEY `id_equipo` (`id_equipo`),
  CONSTRAINT `cotizacion_repuestos_ibfk_1` FOREIGN KEY (`id_cotizacion`) REFERENCES `cotizaciones` (`id_cotizacion`),
  CONSTRAINT `cotizacion_repuestos_ibfk_2` FOREIGN KEY (`id_equipo`) REFERENCES `cotizacion_equipos` (`id_equipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
