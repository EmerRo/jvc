/*
 Navicat Premium Dump SQL

 Source Server         : localhist
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : magusqao_jvc_factura

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 27/05/2026 15:21:46
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for almacenes
-- ----------------------------
DROP TABLE IF EXISTS `almacenes`;
CREATE TABLE `almacenes`  (
  `id_almacen` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `id_empresa` int NOT NULL,
  `estado` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT '1',
  `principal` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_registro` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_almacen`) USING BTREE,
  INDEX `idx_almacenes_empresa_estado`(`id_empresa` ASC, `estado` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of almacenes
-- ----------------------------
INSERT INTO `almacenes` VALUES (1, 'Almacén Principal', 12, '1', 1, '2026-04-17 15:42:47');
INSERT INTO `almacenes` VALUES (2, 'Almacén 2', 1, '1', 0, '2026-04-17 15:42:47');
INSERT INTO `almacenes` VALUES (3, 'Almacén 3', 1, '1', 0, '2026-04-17 15:42:47');
INSERT INTO `almacenes` VALUES (8, 'dfsvdfsv', 12, '1', 0, '2026-05-27 15:18:54');

-- ----------------------------
-- Table structure for archivos_internos
-- ----------------------------
DROP TABLE IF EXISTS `archivos_internos`;
CREATE TABLE `archivos_internos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo: MEMO, INFORME, ACTA, REPORTE, etc.',
  `cliente_id` int NULL DEFAULT NULL,
  `usuario_id` int NULL DEFAULT NULL,
  `contenido` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Contenido HTML del documento',
  `header_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de cabecera en base64',
  `footer_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de pie en base64',
  `estado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'borrador',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `cliente_id`(`cliente_id` ASC) USING BTREE,
  INDEX `usuario_id`(`usuario_id` ASC) USING BTREE,
  CONSTRAINT `archivos_internos_ibfk_1` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `archivos_internos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of archivos_internos
-- ----------------------------
INSERT INTO `archivos_internos` VALUES (1, 'sdcsdacdsa', 'INFORME', 34, 40, '<h2 style=\"text-align: center;\">DOCUMENTO INTERNO</h2><p><br></p><p>Fecha: [FECHA]</p><p>Asunto: [ASUNTO]</p><p><br></p><p>Contenido del documento...</p><p><br></p><p>Atentamente,</p><p><br></p><p>[NOMBRE]</p><p>[CARGO]</p>', NULL, NULL, 'borrador', '2026-05-27 13:33:47', '2026-05-27 13:33:47');

-- ----------------------------
-- Table structure for archivos_internos_plantillas
-- ----------------------------
DROP TABLE IF EXISTS `archivos_internos_plantillas`;
CREATE TABLE `archivos_internos_plantillas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenido` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de cabecera en base64',
  `footer_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de pie en base64',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of archivos_internos_plantillas
-- ----------------------------
INSERT INTO `archivos_internos_plantillas` VALUES (1, 'Plantilla de Documento Interno Predeterminada', '<h2 style=\"text-align: center;\">DOCUMENTO INTERNO</h2><p><br></p><p>Fecha: [FECHA]</p><p>Asunto: [ASUNTO]</p><p><br></p><p>Contenido del documento...</p><p><br></p><p>Atentamente,</p><p><br></p><p>[NOMBRE]</p><p>[CARGO]</p>', NULL, NULL, '2025-05-12 15:56:20', '2025-05-12 15:56:20');

-- ----------------------------
-- Table structure for asuntos_coti
-- ----------------------------
DROP TABLE IF EXISTS `asuntos_coti`;
CREATE TABLE `asuntos_coti`  (
  `id_asunto` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `id_empresa` int NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_asunto`) USING BTREE,
  INDEX `id_empresa`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 42 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of asuntos_coti
-- ----------------------------
INSERT INTO `asuntos_coti` VALUES (1, 'SR EMER . COTIZACION DE PRODUCTO', 12, '2025-03-01 12:06:17');
INSERT INTO `asuntos_coti` VALUES (2, 'SR EDUARDO LOPEZ', 12, '2025-03-04 10:30:56');
INSERT INTO `asuntos_coti` VALUES (3, 'EDUARDO - COTIZACION MAQUINAS', 12, '2025-03-04 10:55:45');
INSERT INTO `asuntos_coti` VALUES (4, 'SDOLA GOLA ', 12, '2025-03-04 11:23:30');
INSERT INTO `asuntos_coti` VALUES (5, 'sdv', 12, '2025-03-10 21:50:21');
INSERT INTO `asuntos_coti` VALUES (6, 'Sr EDUARDO CR', 12, '2025-04-08 11:12:29');
INSERT INTO `asuntos_coti` VALUES (7, 'sr eduardo - Atencion de maquinas', 12, '2025-04-08 12:01:03');
INSERT INTO `asuntos_coti` VALUES (8, 'Sra Olinda - Cotizacion de termonebulizadora', 12, '2025-05-09 13:47:09');
INSERT INTO `asuntos_coti` VALUES (9, 'SR EDUARDO', 12, '2025-06-11 14:21:08');
INSERT INTO `asuntos_coti` VALUES (10, 'WQWQW', 12, '2025-06-20 12:57:18');
INSERT INTO `asuntos_coti` VALUES (11, 'edawae', 12, '2025-06-20 13:05:33');
INSERT INTO `asuntos_coti` VALUES (12, 'Sr Juan Carlos', 12, '2025-08-07 13:43:51');
INSERT INTO `asuntos_coti` VALUES (13, 'EDI', 12, '2025-08-14 11:36:47');
INSERT INTO `asuntos_coti` VALUES (14, 'EDW', 12, '2025-08-14 11:37:37');
INSERT INTO `asuntos_coti` VALUES (15, 'Sr. Eduardo', 12, '2025-08-25 11:20:09');
INSERT INTO `asuntos_coti` VALUES (16, 'SR JUDY', 12, '2025-08-29 17:51:51');
INSERT INTO `asuntos_coti` VALUES (17, 'SE ED', 12, '2025-08-29 17:54:06');
INSERT INTO `asuntos_coti` VALUES (18, 'SR ED', 12, '2025-08-29 17:54:55');
INSERT INTO `asuntos_coti` VALUES (19, 'SREDD', 12, '2025-08-29 18:04:24');
INSERT INTO `asuntos_coti` VALUES (20, 'SR PELMER', 12, '2025-09-25 14:24:54');
INSERT INTO `asuntos_coti` VALUES (21, 'EDUARDO1', 12, '2025-10-06 19:47:41');
INSERT INTO `asuntos_coti` VALUES (22, 'EDUARDO2', 12, '2025-10-06 19:50:06');
INSERT INTO `asuntos_coti` VALUES (23, 'EDUARDO3', 12, '2025-10-06 19:52:28');
INSERT INTO `asuntos_coti` VALUES (24, 'EDUARDO4', 12, '2025-10-06 19:56:38');
INSERT INTO `asuntos_coti` VALUES (25, 'EDUARDO5', 12, '2025-10-06 20:09:35');
INSERT INTO `asuntos_coti` VALUES (26, 'EDUARDO6', 12, '2025-10-06 20:13:23');
INSERT INTO `asuntos_coti` VALUES (27, 'SR EDUARDO 1', 12, '2025-10-14 12:23:00');
INSERT INTO `asuntos_coti` VALUES (28, 'SR EDUARDO 2', 12, '2025-10-14 13:22:07');
INSERT INTO `asuntos_coti` VALUES (29, 'Sr Judy 2', 12, '2025-11-18 11:36:37');
INSERT INTO `asuntos_coti` VALUES (30, 'Sr Judy 1', 12, '2025-11-18 11:37:25');
INSERT INTO `asuntos_coti` VALUES (31, 'Sr Judy 4', 12, '2025-11-18 11:48:37');
INSERT INTO `asuntos_coti` VALUES (32, 'Sr Judy 5', 12, '2025-11-18 11:51:29');
INSERT INTO `asuntos_coti` VALUES (33, 'Sr Judy 6', 12, '2025-11-18 11:53:55');
INSERT INTO `asuntos_coti` VALUES (34, 'Judy', 12, '2025-11-18 12:09:20');
INSERT INTO `asuntos_coti` VALUES (35, 'Sr Judy 8', 12, '2025-11-18 12:11:52');
INSERT INTO `asuntos_coti` VALUES (36, 'Sr Judy 9', 12, '2025-11-18 12:16:08');
INSERT INTO `asuntos_coti` VALUES (37, 'Sr judy 10', 12, '2025-11-18 12:27:48');
INSERT INTO `asuntos_coti` VALUES (38, 'Sr 11', 12, '2025-11-18 12:35:54');
INSERT INTO `asuntos_coti` VALUES (39, 'Sr Judy 12', 12, '2025-11-18 12:37:31');
INSERT INTO `asuntos_coti` VALUES (40, 'SR EDUARDO1', 12, '2025-12-02 19:31:04');
INSERT INTO `asuntos_coti` VALUES (41, 'SR EDUARDO 7', 12, '2025-12-03 18:40:20');

-- ----------------------------
-- Table structure for caja_chica
-- ----------------------------
DROP TABLE IF EXISTS `caja_chica`;
CREATE TABLE `caja_chica`  (
  `caja_chica_id` int NOT NULL AUTO_INCREMENT,
  `id_caja_empresa` int NULL DEFAULT NULL,
  `hora` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `detalle` varchar(220) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tipo` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'f',
  `entrada` double(15, 2) NULL DEFAULT NULL,
  `salida` double(15, 2) NULL DEFAULT NULL,
  `metodo` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT '1 = EFECTIVO 2 =TARJETAS 3 =TRANSFERENCIAS',
  `documento` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`caja_chica_id`) USING BTREE,
  INDEX `id_caja_empresa`(`id_caja_empresa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 167 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of caja_chica
-- ----------------------------
INSERT INTO `caja_chica` VALUES (160, 68, '10:14 AM', 'Apertura de caja', 'a', 3000.00, 0.00, '1', NULL);
INSERT INTO `caja_chica` VALUES (161, 69, '03:43 PM', 'Apertura de caja', 'a', 2000.00, 0.00, '1', NULL);
INSERT INTO `caja_chica` VALUES (162, 70, '06:38 PM', 'Apertura de caja', 'a', 200.00, 0.00, '1', NULL);
INSERT INTO `caja_chica` VALUES (166, 72, '02:35 PM', 'Apertura de caja', 'a', 423432.00, 0.00, '1', NULL);

-- ----------------------------
-- Table structure for caja_empresa
-- ----------------------------
DROP TABLE IF EXISTS `caja_empresa`;
CREATE TABLE `caja_empresa`  (
  `caja_id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_empresa` int NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `detalle` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `fecha_cierre` datetime NULL DEFAULT NULL,
  `entrada` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `salida` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '1',
  PRIMARY KEY (`caja_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 73 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of caja_empresa
-- ----------------------------
INSERT INTO `caja_empresa` VALUES (68, 'CA-001', 12, 1, 'apertura 25', '2025-08-25', '2025-08-25 11:14:33', '3000', '0', '0');
INSERT INTO `caja_empresa` VALUES (69, 'CA-069', 12, 1, 'APERTURA CAJA HOY 17', '2025-10-17', NULL, '', '', '1');
INSERT INTO `caja_empresa` VALUES (70, 'CA-070', 12, 1, 'LUNES', '2025-12-02', NULL, '', '', '1');
INSERT INTO `caja_empresa` VALUES (71, 'CA-071', 12, 1, 'fbds', '2025-12-05', '2025-12-05 12:28:58', '0', '0', '0');
INSERT INTO `caja_empresa` VALUES (72, 'CA-072', 12, 1, 'sdcdsa', '2026-05-27', NULL, '', '', '1');

-- ----------------------------
-- Table structure for carta_templates
-- ----------------------------
DROP TABLE IF EXISTS `carta_templates`;
CREATE TABLE `carta_templates`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `contenido` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `header_image` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `footer_image` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `activo` tinyint(1) NULL DEFAULT 1,
  `fecha_creacion` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of carta_templates
-- ----------------------------
INSERT INTO `carta_templates` VALUES (1, 'CARTA', '<p>Las estrategias de este tipo funcionan por otro motivo, también: refuerzan la identidad que quieres construir. Si apareces en el gimnasio cinco días seguidos —incluso si es durante solamente dos minutos—, estás sumando puntos para tu nueva identidad. No estás preocupándote por ponerte en forma. Te estás enfocando en convertirte en el tipo de persona que no falta a sus entrenamientos. Estás tomando la acción mínima necesaria para confirmar el tipo de persona que quieres llegar a ser. Raramente pensamos en el cambio de esta manera porque todos están enfocados en la meta final. Pero una lagartija es mejor que no hacer nada de ejercicio. Un minuto de práctica de guitarra es mejor que nada de práctica. Un minuto de lectura es mejor que nunca abrir un libro. Es mejor hacer menos de lo que te propusiste que no hacer nada. En algún momento, una vez que has establecido el hábito y estás cumpliendo diariamente con hacerlo, puedes empezar a combinar la regla de los dos minutos con una técnica que llamamos modelado de hábitos, 7 de tal forma que puedas elevar tu hábito hasta la meta que te habías propuesto originalmente. Empieza por dominar los primeros dos minutos de la versión más sencilla de la conducta que quieres convertir en hábito. Luego, avanza hasta un paso intermedio y repite el proceso —enfócate solamente en los primeros dos minutos hasta que domines esa etapa antes de avanzar al siguiente nivel—. Finalmente, terminarás dominando el hábito que originalmente habías deseado desarrollar mientras sigues manteniendo tu atención donde debe estar: en los primeros dos minutos de la conducta</p>', 'files/cartas/1756990498_68b98c22eac38.jpg', 'files/cartas/1756990499_68b98c233fbe8.jpg', 0, '2025-05-15 18:21:03', '2025-12-03 17:59:37');
INSERT INTO `carta_templates` VALUES (2, 'CARTA', '<p><strong>Estimados señores:</strong></p><p> Presente.</p><p>Por medio de la presente, me permito dirigirme a ustedes para comunicar lo siguiente, en relación al asunto que motiva esta carta.</p><p><br></p>', 'files/cartas/1756990498_68b98c22eac38.jpg', 'files/cartas/1756990499_68b98c233fbe8.jpg', 1, '2025-12-03 17:59:37', '2025-12-03 17:59:37');

-- ----------------------------
-- Table structure for carta_tipos
-- ----------------------------
DROP TABLE IF EXISTS `carta_tipos`;
CREATE TABLE `carta_tipos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `descripcion` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `activo` tinyint(1) NULL DEFAULT 1,
  `fecha_creacion` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of carta_tipos
-- ----------------------------
INSERT INTO `carta_tipos` VALUES (1, 'Carta de Compromiso', 'Cartas que establecen compromisos entre partes', 1, '2025-05-12 15:56:20', '2025-05-12 15:56:20');
INSERT INTO `carta_tipos` VALUES (2, 'Carta de Presentación', 'Cartas para presentar personas o empresas', 1, '2025-05-12 15:56:20', '2025-05-12 15:56:20');
INSERT INTO `carta_tipos` VALUES (3, 'Carta de Recomendación', 'Cartas que recomiendan a personas o servicios', 1, '2025-05-12 15:56:20', '2025-05-12 15:56:20');
INSERT INTO `carta_tipos` VALUES (4, 'Carta de Solicitud', 'Cartas para solicitar servicios o información', 1, '2025-05-12 15:56:20', '2025-05-12 15:56:20');

-- ----------------------------
-- Table structure for cartas
-- ----------------------------
DROP TABLE IF EXISTS `cartas`;
CREATE TABLE `cartas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cliente` int NULL DEFAULT NULL,
  `id_usuario` int NOT NULL,
  `tipo` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `titulo` varchar(150) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `contenido` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `header_image` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `footer_image` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `estado` enum('borrador','finalizado') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'borrador',
  `fecha_creacion` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_cliente`(`id_cliente` ASC) USING BTREE,
  INDEX `id_usuario`(`id_usuario` ASC) USING BTREE,
  CONSTRAINT `cartas_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `cartas_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 16 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cartas
-- ----------------------------
INSERT INTO `cartas` VALUES (14, NULL, 40, 'FORMAL', 'dgfvbfgdgfdb', '<p>Las estrategias de este tipo funcionan por otro motivo, también: refuerzan la identidad que quieres construir. Si apareces en el gimnasio cinco días seguidos —incluso si es durante solamente dos minutos—, estás sumando puntos para tu nueva identidad. No estás preocupándote por ponerte en forma. Te estás enfocando en convertirte en el tipo de persona que no falta a sus entrenamientos. Estás tomando la acción mínima necesaria para confirmar el tipo de persona que quieres llegar a ser. Raramente pensamos en el cambio de esta manera porque todos están enfocados en la meta final. Pero una lagartija es mejor que no hacer nada de ejercicio. Un minuto de práctica de guitarra es mejor que nada de práctica. Un minuto de lectura es mejor que nunca abrir un libro. Es mejor hacer menos de lo que te propusiste que no hacer nada. En algún momento, una vez que has establecido el hábito y estás cumpliendo diariamente con hacerlo, puedes empezar a combinar la regla de los dos minutos con una técnica que llamamos modelado de hábitos, 7 de tal forma que puedas elevar tu hábito hasta la meta que te habías propuesto originalmente. Empieza por dominar los primeros dos minutos de la versión más sencilla de la conducta que quieres convertir en hábito. Luego, avanza hasta un paso intermedio y repite el proceso —enfócate solamente en los primeros dos minutos hasta que domines esa etapa antes de avanzar al siguiente nivel—. Finalmente, terminarás dominando el hábito que originalmente habías deseado desarrollar mientras sigues manteniendo tu atención donde debe estar: en los primeros dos minutos de la conducta</p>', NULL, NULL, 'borrador', '2025-09-02 12:42:53', '2025-09-02 12:42:53');
INSERT INTO `cartas` VALUES (15, NULL, 40, 'FORMAL', 'sopa res', '<p>Las estrategias de este tipo funcionan por otro motivo, también: refuerzan la identidad que quieres construir. Si apareces en el gimnasio cinco días seguidos —incluso si es durante solamente dos minutos—, estás sumando puntos para tu nueva identidad. No estás preocupándote por ponerte en forma. Te estás enfocando en convertirte en el tipo de persona que no falta a sus entrenamientos. Estás tomando la acción mínima necesaria para confirmar el tipo de persona que quieres llegar a ser. Raramente pensamos en el cambio de esta manera porque todos están enfocados en la meta final. Pero una lagartija es mejor que no hacer nada de ejercicio. Un minuto de práctica de guitarra es mejor que nada de práctica. Un minuto de lectura es mejor que nunca abrir un libro. Es mejor hacer menos de lo que te propusiste que no hacer nada. En algún momento, una vez que has establecido el hábito y estás cumpliendo diariamente con hacerlo, puedes empezar a combinar la regla de los dos minutos con una técnica que llamamos modelado de hábitos, 7 de tal forma que puedas elevar tu hábito hasta la meta que te habías propuesto originalmente. Empieza por dominar los primeros dos minutos de la versión más sencilla de la conducta que quieres convertir en hábito. Luego, avanza hasta un paso intermedio y repite el proceso —enfócate solamente en los primeros dos minutos hasta que domines esa etapa antes de avanzar al siguiente nivel—. Finalmente, terminarás dominando el hábito que originalmente habías deseado desarrollar mientras sigues manteniendo tu atención donde debe estar: en los primeros dos minutos de la conducta</p>', NULL, NULL, 'borrador', '2025-09-02 12:44:17', '2025-09-02 12:44:17');

-- ----------------------------
-- Table structure for categorias
-- ----------------------------
DROP TABLE IF EXISTS `categorias`;
CREATE TABLE `categorias`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `creado_el` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of categorias
-- ----------------------------
INSERT INTO `categorias` VALUES (5, 'MAQUINA CRIS-TAURO', '2024-08-21 12:12:38');
INSERT INTO `categorias` VALUES (6, 'MAQUINA MASTER GOLDS', '2024-08-21 12:12:52');
INSERT INTO `categorias` VALUES (8, 'MAQUINA SPEED POWER', '2024-09-12 11:53:52');
INSERT INTO `categorias` VALUES (20, 'MAQUINA MASTER FOG', '2025-08-14 10:32:15');
INSERT INTO `categorias` VALUES (21, 'MAQUINA MASTER GREEN', '2025-08-14 10:32:24');
INSERT INTO `categorias` VALUES (22, 'MAQUINA TVX', '2025-08-14 10:32:47');
INSERT INTO `categorias` VALUES (23, 'CEPILLOS', '2025-12-02 17:28:52');
INSERT INTO `categorias` VALUES (24, 'PORTA PAD', '2025-12-02 17:28:58');

-- ----------------------------
-- Table structure for categorias_repuestos
-- ----------------------------
DROP TABLE IF EXISTS `categorias_repuestos`;
CREATE TABLE `categorias_repuestos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `creado_el` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of categorias_repuestos
-- ----------------------------
INSERT INTO `categorias_repuestos` VALUES (21, 'CRIS-TAURO', '2025-04-10 17:00:58');
INSERT INTO `categorias_repuestos` VALUES (22, 'MASTER GOLDS', '2025-04-10 17:01:03');
INSERT INTO `categorias_repuestos` VALUES (23, 'SPEED POWER', '2025-04-10 17:01:10');
INSERT INTO `categorias_repuestos` VALUES (24, 'UNIVERSAL', '2025-04-10 17:02:27');
INSERT INTO `categorias_repuestos` VALUES (26, 'IMPLEMENTO MASTER GOLDS516', '2025-05-01 00:24:22');

-- ----------------------------
-- Table structure for certificado_templates
-- ----------------------------
DROP TABLE IF EXISTS `certificado_templates`;
CREATE TABLE `certificado_templates`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `contenido` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `imagenes_config` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_modificacion` datetime NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of certificado_templates
-- ----------------------------
INSERT INTO `certificado_templates` VALUES (1, 'CERTIFICADO DE GARANTÍA', '<p><strong>COMERCIAL & INDUSTRIAL J.V.C. S.A.C.</strong> Garantiza estas Máquinas de uso Industrial, por el término de 12 meses a partir de la fecha de compra, presentando este Certificado de Garantía y la Factura original dentro del plazo antes mencionado.</p>\n    <p>Esta garantía cubre todo defecto o falla de fabricación y/o ensamblaje que pudiera producirse en las máquinas.</p>\n    <p><strong>COMERCIAL & INDUSTRIAL J.V.C. S.A.C.</strong> asegura que estos Equipos cumple con las normas de seguridad vigentes.</p>\n    <p>Las condiciones de uso, instalación y mantenimiento necesarias de este equipo deberán hacerse siguiendo y respetando las especificaciones técnicas, instalación, indicación, y consejo que se formulan en el Manual de Instrucciones que forma parte de esta garantía.</p>\n    <p><strong>La presente Garantía dejará de tener validez cuando:</strong></p>\n    <ol>\n        <li>La etiqueta de identificación y/o número de serie hubiera sido dañado, alterado o quitado.</li>\n        <li>Hayan intervenido personas ajenas al Servicio Técnico de la Firma.</li>\n        <li>No se presente la factura de compra, o la misma tuviera enmiendas y/o faltare la fecha de compra.</li>\n        <li>Se verifique que los daños fueron causados por cualquier factor ajeno al uso normal del equipo.</li>\n        <li>Se verifique que los daños se hayan producido por el transporte después de la compra, golpes o accidentes de cualquier naturaleza.</li>\n        <li>Se verifique mala manipulación del equipo y/o mal uso del mismo.</li>\n        <li>El usuario no realice el mantenimiento preventivo y/o correctivo del equipo anualmente para una buena durabilidad del motor.</li>\n    </ol>\n    <p>En caso de falla del equipo, el consumidor deberá llamar a nuestro <strong>CENTRO DE SERVICIO TÉCNICO: 980088015.</strong> Cuando el examen realizado por nuestro Personal Técnico sobre el producto y la documentación pertinente, determine que rigen los términos de la garantía, el mismo será reparado sin cargo alguno.</p>', NULL, '2025-03-21 18:16:38', '2025-03-21 18:16:38', 0);

-- ----------------------------
-- Table structure for cliente_venta
-- ----------------------------
DROP TABLE IF EXISTS `cliente_venta`;
CREATE TABLE `cliente_venta`  (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id_cliente`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cliente_venta
-- ----------------------------

-- ----------------------------
-- Table structure for clientes
-- ----------------------------
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes`  (
  `id_cliente` int NOT NULL AUTO_INCREMENT,
  `documento` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `datos` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion2` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono2` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `ultima_venta` date NULL DEFAULT NULL,
  `total_venta` double(8, 2) NULL DEFAULT NULL,
  `id_rubro` int NULL DEFAULT NULL,
  `ubigeo` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `departamento` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `provincia` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `distrito` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_cliente`) USING BTREE,
  INDEX `fk_clientes_empresas_idx`(`id_empresa` ASC) USING BTREE,
  INDEX `fk_cliente_rubro`(`id_rubro` ASC) USING BTREE,
  CONSTRAINT `fk_cliente_rubro` FOREIGN KEY (`id_rubro`) REFERENCES `rubros` (`id_rubro`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 36 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of clientes
-- ----------------------------
INSERT INTO `clientes` VALUES (31, '20100070970', 'SUPERMERCADOS PERUANOS SOCIEDAD ANONIMA \'O \' S.P.S.A.', 'CAL. MORELLI NRO. 181 INT. P-2 LIMA LIMA SAN BORJA', NULL, NULL, NULL, NULL, 12, '2025-12-15', 0.00, NULL, '150130', 'LIMA', 'LIMA', 'SAN BORJA');
INSERT INTO `clientes` VALUES (32, '20601212472', 'LIM KIT CORPORACION E.I.R.L.', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', NULL, NULL, NULL, NULL, 12, '2025-12-17', 3894.00, NULL, '', '', '', '');
INSERT INTO `clientes` VALUES (33, '70830096', 'ANTHONY KENER GUSTAVO MIRANDA PUN', '', NULL, '', NULL, '', 12, '1000-01-01', 0.00, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `clientes` VALUES (34, '77425200', 'EMER RODRIGO YARLEQUE ZAPATA', '', NULL, NULL, NULL, NULL, 12, '2026-05-15', 20042.24, NULL, '', '', '', '');
INSERT INTO `clientes` VALUES (35, '20538381978', 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.', '', '', NULL, NULL, NULL, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- ----------------------------
-- Table structure for compra
-- ----------------------------
DROP TABLE IF EXISTS `compra`;
CREATE TABLE `compra`  (
  `id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_documento` enum('01','03','nv','in','sa','rc') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'nv',
  `serie` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `descripcion` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `forma_de_pago` enum('co','cr') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'co',
  `tipo_moneda` enum('s','d') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 's',
  `tipo_de_cambio` decimal(9, 4) NOT NULL DEFAULT 1.0000,
  `percepcion` decimal(9, 4) NOT NULL DEFAULT 0.0000,
  `numero_dias` int NULL DEFAULT NULL,
  `fecha_vencimiento` datetime(3) NULL DEFAULT NULL,
  `fecha` datetime(3) NOT NULL,
  `guia` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `estado_de_compra` enum('cr','ee','an','pr') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cr',
  `egreso_dinero_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `despliegue_de_pago_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `user_id` varchar(191) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `almacen_id` int NOT NULL,
  `created_at` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `updated_at` datetime(3) NOT NULL,
  `proveedor_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `Compra_serie_numero_proveedor_id_key`(`serie` ASC, `numero` ASC, `proveedor_id` ASC) USING BTREE,
  INDEX `Compra_fecha_idx`(`fecha` ASC) USING BTREE,
  INDEX `Compra_estado_de_compra_idx`(`estado_de_compra` ASC) USING BTREE,
  INDEX `Compra_proveedor_id_idx`(`proveedor_id` ASC) USING BTREE,
  INDEX `Compra_almacen_id_idx`(`almacen_id` ASC) USING BTREE,
  INDEX `Compra_user_id_idx`(`user_id` ASC) USING BTREE,
  INDEX `Compra_created_at_idx`(`created_at` ASC) USING BTREE,
  INDEX `Compra_despliegue_de_pago_id_fkey`(`despliegue_de_pago_id` ASC) USING BTREE,
  INDEX `Compra_egreso_dinero_id_fkey`(`egreso_dinero_id` ASC) USING BTREE,
  CONSTRAINT `Compra_almacen_id_fkey` FOREIGN KEY (`almacen_id`) REFERENCES `almacen` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Compra_despliegue_de_pago_id_fkey` FOREIGN KEY (`despliegue_de_pago_id`) REFERENCES `desplieguedepago` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `Compra_egreso_dinero_id_fkey` FOREIGN KEY (`egreso_dinero_id`) REFERENCES `egresodinero` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `Compra_proveedor_id_fkey` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `Compra_user_id_fkey` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of compra
-- ----------------------------

-- ----------------------------
-- Table structure for compras
-- ----------------------------
DROP TABLE IF EXISTS `compras`;
CREATE TABLE `compras`  (
  `id_compra` int NOT NULL AUTO_INCREMENT,
  `id_tido` int NULL DEFAULT NULL,
  `id_tipo_pago` int NULL DEFAULT NULL,
  `id_proveedor` int NULL DEFAULT NULL,
  `fecha_emision` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `fecha_vencimiento` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `dias_pagos` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `serie` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `serie_proveedor` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero_proveedor` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `total` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NULL DEFAULT NULL,
  `moneda` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  `estado` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1' COMMENT 'Estado de la compra: 1=Activa, 0=Anulada',
  `devolucion_observaciones` text CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL,
  PRIMARY KEY (`id_compra`) USING BTREE,
  INDEX `id_empresa`(`id_empresa` ASC) USING BTREE,
  INDEX `id_tipo_pago`(`id_tipo_pago` ASC) USING BTREE,
  INDEX `id_tido`(`id_tido` ASC) USING BTREE,
  INDEX `id_proveedor`(`id_proveedor` ASC) USING BTREE,
  INDEX `id_usuario`(`id_usuario` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of compras
-- ----------------------------
INSERT INTO `compras` VALUES (7, 12, 1, 71, '2025-12-16', '2025-12-17', NULL, '-', 'OC', '006', NULL, NULL, '1320', 12, '1', 1, 40, '0', NULL);
INSERT INTO `compras` VALUES (8, 12, 1, 601, '2026-01-08', '2026-01-09', NULL, '-', 'OC', '007', NULL, NULL, '3852', 12, '1', 1, 40, '0', NULL);
INSERT INTO `compras` VALUES (9, 2, 1, 53, '2026-05-27', '2026-05-28', NULL, '-', 'OC', '008', 'f001', '23424', '1024', 12, '1', 1, 40, '1', '\nsdcdsac\nsdcdsac\nsdcdsac\nsdcdsac\nsdcdsac\nsdcdsac\nsdcdsac\nsdcdsac');

-- ----------------------------
-- Table structure for condicion
-- ----------------------------
DROP TABLE IF EXISTS `condicion`;
CREATE TABLE `condicion`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of condicion
-- ----------------------------
INSERT INTO `condicion` VALUES (1, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');

-- ----------------------------
-- Table structure for condiciones_cotizacion
-- ----------------------------
DROP TABLE IF EXISTS `condiciones_cotizacion`;
CREATE TABLE `condiciones_cotizacion`  (
  `id_condicion_cotizacion` int NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int NOT NULL,
  `condiciones` text CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  PRIMARY KEY (`id_condicion_cotizacion`) USING BTREE,
  INDEX `id_cotizacion`(`id_cotizacion` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 160 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of condiciones_cotizacion
-- ----------------------------
INSERT INTO `condiciones_cotizacion` VALUES (1, 1710, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (2, 1712, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (3, 1713, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (4, 1715, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (5, 1716, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (6, 1717, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (7, 1718, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (8, 1719, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (9, 1720, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (10, 1721, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (11, 1722, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (12, 1723, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (13, 1724, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (14, 1725, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (15, 1726, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (16, 1727, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (17, 1728, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (18, 1729, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (19, 1730, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (20, 1731, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (21, 1732, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (22, 1733, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (23, 1734, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (24, 1735, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (25, 1738, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (26, 1739, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (27, 1740, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (28, 1741, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (29, 1742, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (30, 1743, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (31, 1744, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (32, 1745, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (33, 1746, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (34, 1747, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (35, 1748, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (36, 1749, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (37, 1750, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (38, 1751, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (39, 1752, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (40, 1753, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (41, 1754, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (42, 1755, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (43, 1756, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (44, 1757, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (45, 1766, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (46, 1767, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (47, 1768, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (48, 1769, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (49, 1770, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (50, 1771, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (51, 1772, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (52, 1773, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (53, 1774, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (54, 1775, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (55, 1776, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (56, 1777, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (57, 1778, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (58, 1779, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (59, 1780, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (60, 1781, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (61, 1782, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (62, 1783, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (64, 1784, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (65, 1785, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (66, 1786, '• P<span style=\"background-color: rgb(255, 255, 0);\">recios unitarios No Incluyen I.G</span>.V.\n • Forma de Pago: Contado y/o tramite de factura\n • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n • <span style=\"background-color: rgb(255, 255, 0);\">Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n • Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (67, 1787, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b><u> há</u></b>b</b>iles.1dsss</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (68, 1788, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com</span><b style=\"background-color: rgb(255, 255, 255);\">ercial &amp; Industrial J.V.</b><span style=\"background-color: rgb(255, 255, 255);\">C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de </span><span style=\"background-color: rgb(255, 255, 0);\"><b style=\"\"><u style=\"\"><i style=\"\">C</i><i style=\"\">otiza</i>ción: 07 días</u></b><font color=\"#000000\" style=\"\"><b style=\"\"><b><u> há</u></b>b</b>iles</font></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">.1dsss</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (69, 1789, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b><u> há</u></b>b</b>iles.</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (70, 1790, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • T</span><span style=\"background-color: rgb(255, 255, 0);\">iempo de Entrega: 04 días hábiles</span><span style=\"background-color: rgb(255, 255, 255);\"> luego de recibir OS.\n • Validez de <b><i style=\"\">C</i><i style=\"\">otiza</i>ción: 07 días</b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b> há</b>b</b>iles.</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (71, 1791, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b><u> há</u></b>b</b>iles.</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (72, 1792, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b><u> há</u></b>b</b>iles.</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (73, 1793, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b><u> há</u></b>b</b>iles.</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (74, 1794, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b><u> há</u></b>b</b>iles.</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (75, 1795, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b><u> há</u></b>b</b>iles.</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (76, 1796, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b><u> há</u></b>b</b>iles.</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (77, 1797, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios </span><span style=\"background-color: rgb(255, 231, 156);\">No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 231, 156);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b><font color=\"#000000\" style=\"\"><b style=\"\"><b><u> há</u></b>b</b>iles.</font></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (78, 1798, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b><u> há</u></b>b</b>iles.</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (79, 1799, '<p>• Preci<span style=\"background-color: rgb(255, 255, 255);\">os </span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\">un</font><font color=\"#000000\" style=\"\"><span style=\"background-color: rgb(255, 255, 255);\">itarios No Incluyen I.G.V.\n • Forma de Pago: Contado y/o tramite </span></font><span style=\"background-color: rgb(255, 255, 255);\">de factura\n • Emitir Orden de Servicio a nombre de Com<b>ercial &amp; Industrial J.V.</b>C. S.A.C.\n • Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n • Validez de <b><u><i>C</i><i>otiza</i>ción: 07 días</u></b></span><font color=\"#000000\" style=\"background-color: rgb(255, 255, 255);\"><b><b><u> há</u></b>b</b>iles.</font><font color=\"#000000\" style=\"background-color: rgb(255, 255, 0);\"><br></font></p>');
INSERT INTO `condiciones_cotizacion` VALUES (80, 1800, '<p>• </p><p>• P<span>recios unitarios No Incluyen I.G</span>.V.\n  </p><p>• Forma de Pago: Contado y/o tramite de factura\n  </p><p>• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  </p><p>• Tiempo de<span style=\"background-color: rgb(255, 255, 0);\"> Entrega: 04 días </span><span style=\"background-color: rgb(255, 255, 0);\">hábiles luego de recibir OS.\n  </span></p><p></p><p>• Validez de Cotización: 07 días hábiles</p>');
INSERT INTO `condiciones_cotizacion` VALUES (81, 1801, '  • P<span>recios unitarios No Incluyen I.G</span>.V.\n  • Forma de Pago: Contado y/o tramite de factura\n  • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  • <span>Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n  • Validez de Cotización: 07 días hábiles\n ');
INSERT INTO `condiciones_cotizacion` VALUES (82, 1802, '<p>• P<span>recios unitarios No Incluyen I.G</span>.V.\n  </p><p>• Forma de Pago: Contado y/o tramite de factura\n  </p><p>• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  </p><p>• Tiempo de Entrega<span style=\"background-color: rgb(255, 255, 0);\">: 04 días </span><span style=\"background-color: rgb(255, 255, 0);\">hábiles luego de recibir OS.\n  </span></p><p>• Validez de Cotización: 07 días hábiles</p>');
INSERT INTO `condiciones_cotizacion` VALUES (83, 1803, '<p>• P<span>recios unitarios No Incluyen I.G</span>.V.\n  </p><p>• Forma de Pago: Contado y/o tramite de factura\n  </p><p>• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  </p><p>• <span>Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n  </p><p>• Validez de Cotización: <span style=\"background-color: rgb(255, 255, 0);\">07 días hábiles</span></p><p>• Garantia de 12 meses por defecto de fabricación</p>');
INSERT INTO `condiciones_cotizacion` VALUES (84, 1804, '<p style=\"text-align: left;\"><b>Precios unitarios No Incluyen I.G.V.\n  </b></p><p style=\"text-align: left;\">Forma de Pago: Contado y/o tramite de factura\n  </p><p style=\"text-align: left;\">Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  </p><p style=\"text-align: left;\"><span>Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n  </p><p style=\"text-align: left;\">Validez de Cotización: 07 días hábiles</p>');
INSERT INTO `condiciones_cotizacion` VALUES (85, 1805, '  • P<span>recios unitarios No Incluyen I.G</span>.V.\n  • Forma de Pago: Contado y/o tramite de factura\n  • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  • <span>Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n  • Validez de Cotización: 07 días hábiles\n ');
INSERT INTO `condiciones_cotizacion` VALUES (86, 1806, '  • P<span>recios unitarios No Incluyen I.G</span>.V.\n  • Forma de Pago: Contado y/o tramite de factura\n  • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  • <span>Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n  • Validez de Cotización: 07 días hábiles\n ');
INSERT INTO `condiciones_cotizacion` VALUES (87, 1807, '  • P<span>recios unitarios No Incluyen I.G</span>.V.\n  • Forma de Pago: Contado y/o tramite de factura\n  • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  • <span>Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n  • Validez de Cotización: 07 días hábiles\n ');
INSERT INTO `condiciones_cotizacion` VALUES (88, 1808, '  • P<span>recios unitarios No Incluyen I.G</span>.V.\n  • Forma de Pago: Contado y/o tramite de factura\n  • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  • <span>Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n  • Validez de Cotización: 07 días hábiles\n ');
INSERT INTO `condiciones_cotizacion` VALUES (89, 1809, '  • P<span>recios unitarios No Incluyen I.G</span>.V.\n  • Forma de Pago: Contado y/o tramite de factura\n  • Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  • <span>Tiempo de Entrega: 04 días </span>hábiles luego de recibir OS.\n  • Validez de Cotización: 07 días hábiles\n ');
INSERT INTO `condiciones_cotizacion` VALUES (90, 1810, '  Precios unitarios No Incluyen I.G.V.\n  Forma de Pago: Contado y/o tramite de factura\n  Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n  Validez de Cotización: 07 días hábiles\n ');
INSERT INTO `condiciones_cotizacion` VALUES (91, 1811, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Conta<span style=\"background-color: rgb(250, 204, 204);\">do y/o tramite de factura</span>\n• Emitir Orden de Servici<span style=\"background-color: rgb(255, 255, 0);\">o a nombre de Comercial &amp; Industrial J.V.C. S.A.C.</span>\n• Tiempo de Entrega<strong>: 04 días hábiles luego de rec</strong>ibir OS.\n• Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (92, 1812, '  Precios unitarios No Incluyen I.G.V.\n  Forma de Pago: Contado y/o tramite de factura\n  Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n  Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n  Validez de Cotización: 07 días hábiles\n ');
INSERT INTO `condiciones_cotizacion` VALUES (93, 1813, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre<span style=\"background-color: rgb(255, 255, 204);\"> de Comercial &amp; Industrial J.V.</span>C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (94, 1814, '• Precios unitarios <span style=\"background-color: rgb(255, 255, 102);\">No Incluyen I.G.V.</span>\n• Forma de Pago: Contado <strong>y/o tramite de factura</strong>\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega:<strong> 04 días hábiles luego de recibir OS.</strong>\n• Validez de Cotización: 07 días hábiles\n');
INSERT INTO `condiciones_cotizacion` VALUES (95, 1815, '• <span style=\"background-color: rgb(255, 255, 0);\">Precios unitarios No Incluyen I.G.V.</span>\n• <span style=\"background-color: rgb(255, 153, 0);\">Forma de Pago: Contado y/o tramite de factura</span>\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• 12 Meses de garantia por defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (96, 1816, '• <strong style=\"background-color: rgb(255, 255, 0);\">Precios unitarios No Incluyen I.G.V.</strong>\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de <strong>Comercial &amp; Industrial J.V.C. S.A.C.</strong>\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• 12 Meses de garantia por defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (97, 1817, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (98, 1818, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (99, 1819, '• Precios unitarios No Incluyen I.G.V.\n• <span style=\"background-color: rgb(255, 255, 0);\">• Forma de Pago: Contado y/o tramite de factura</span>\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (100, 1820, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 10 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (101, 1821, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (102, 1822, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (103, 1823, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (104, 1824, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (105, 1825, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (106, 1826, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 24 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (107, 1827, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (108, 1828, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• <span style=\"background-color: rgb(255, 255, 0);\">Validez de Cotización: 07 días hábiles</span>\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (109, 1829, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• <span style=\"background-color: rgb(255, 153, 0);\">• Garantia: 12 meses defecto de fabrica</span>\n');
INSERT INTO `condiciones_cotizacion` VALUES (110, 1830, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• <span style=\"background-color: rgb(255, 153, 0);\">Garantia: 12 meses defecto de fabrica</span>\n');
INSERT INTO `condiciones_cotizacion` VALUES (111, 1831, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (112, 1832, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (113, 1833, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (114, 1834, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (115, 1835, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (116, 1836, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (117, 1837, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (118, 1838, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (119, 1839, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (120, 1840, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (121, 1841, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• <span style=\"background-color: rgb(255, 255, 0);\">• Validez de Cotización: 07 días hábiles</span>\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (122, 1842, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (123, 1843, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (124, 1844, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (125, 1845, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (126, 1846, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: <span style=\"background-color: rgb(255, 255, 0);\">07 días hábiles</span>\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (127, 1847, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (128, 1848, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (129, 1849, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (130, 1850, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (131, 1851, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (132, 1852, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (133, 1853, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (134, 1854, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: <span style=\"background-color: rgb(255, 255, 0);\">12 meses defecto de fabrica</span>\n');
INSERT INTO `condiciones_cotizacion` VALUES (135, 1855, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (136, 1856, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (137, 1857, '• <span style=\"background-color: rgb(255, 255, 0);\">Precios unitarios No Incluyen I.G.V.</span>\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (138, 1858, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• <span style=\"background-color: rgb(255, 255, 0);\">Garantia: 12 meses defecto de fabrica</span>\n');
INSERT INTO `condiciones_cotizacion` VALUES (139, 1859, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (140, 1860, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (141, 1861, '• Precios unitarios No Incluyen I.G.V.\n• <span style=\"background-color: rgb(255, 153, 0);\">• Forma de Pago: Contado y/o tramite de factura</span>\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• <span style=\"background-color: rgb(255, 255, 0);\">• Tiempo de Entrega: 100 días hábiles luego de recibir OS.</span>\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (142, 1862, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (143, 1863, '• <em>• Precios unitarios No Incluyen I.G.V.</em>\n• <u>• Forma de Pago: Contado y/o tramite de factura</u>\n• <span style=\"background-color: rgb(230, 0, 0);\">• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.</span>\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (144, 1864, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (145, 1865, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (146, 1866, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (147, 1867, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (148, 1868, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (149, 1869, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 <span style=\"background-color: rgb(255, 255, 0);\">meses defecto de fabrica</span>\n');
INSERT INTO `condiciones_cotizacion` VALUES (150, 1870, '• <span style=\"background-color: rgb(255, 255, 0);\">• Precios unitarios No Incluyen I.G.V.</span>\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábilessss\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (151, 1871, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (152, 1872, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (153, 1873, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (154, 1874, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (155, 1875, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (156, 1876, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (157, 1877, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garant<span style=\"color: rgb(255, 255, 0);\">ia: 12 meses defecto de fabrica</span>\n');
INSERT INTO `condiciones_cotizacion` VALUES (158, 1878, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• <span style=\"color: rgb(107, 36, 178);\">• Tiempo de Entrega: 04 días hábiles luego de recibir OS.</span>\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');
INSERT INTO `condiciones_cotizacion` VALUES (159, 1879, '• Precios unitarios No Incluyen I.G.V.\n• Forma de Pago: Contado y/o tramite de factura\n• Emitir Orden de Servicio a nombre de Comercial &amp; Industrial J.V.C. S.A.C.\n• Tiempo de Entrega: 04 días hábiles luego de recibir OS.\n• Validez de Cotización: 07 días hábiles\n• Garantia: 12 meses defecto de fabrica\n');

-- ----------------------------
-- Table structure for constancias
-- ----------------------------
DROP TABLE IF EXISTS `constancias`;
CREATE TABLE `constancias`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo: MANTENIMIENTO, ANTIGÜEDAD, GARANTÍA, etc.',
  `id_cliente` int NULL DEFAULT NULL,
  `usuario_id` int NULL DEFAULT NULL,
  `contenido` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de cabecera en base64',
  `footer_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de pie en base64',
  `estado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'borrador',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `cliente_id`(`id_cliente` ASC) USING BTREE,
  INDEX `usuario_id`(`usuario_id` ASC) USING BTREE,
  CONSTRAINT `constancias_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `constancias_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of constancias
-- ----------------------------
INSERT INTO `constancias` VALUES (1, 'constancia ehgresa', 'MANTENIMIENTO', NULL, 40, '<p>sdcsdacascsd fsdfsda fsdaf</p>', NULL, NULL, 'borrador', '2025-08-27 17:32:16', '2025-08-27 17:32:16');

-- ----------------------------
-- Table structure for constancias_plantillas
-- ----------------------------
DROP TABLE IF EXISTS `constancias_plantillas`;
CREATE TABLE `constancias_plantillas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenido` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de cabecera en base64',
  `footer_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de pie en base64',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of constancias_plantillas
-- ----------------------------
INSERT INTO `constancias_plantillas` VALUES (1, 'Plantilla de Constancia Predeterminada', '<h2 style=\"text-align: center;\">CONSTANCIA</h2><p><br></p><p>Por medio de la presente, se hace constar que:</p><p><br></p><p style=\"text-align: center;\"><strong>[NOMBRE DEL CLIENTE]</strong></p><p><br></p><p>Ha recibido el servicio de [TIPO DE SERVICIO] para el equipo [EQUIPO] con número de serie [NÚMERO DE SERIE], el día [FECHA].</p><p><br></p><p>Se extiende la presente constancia para los fines que el interesado considere conveniente.</p><p><br></p><p>Atentamente,</p><p><br></p><p>[NOMBRE DE LA EMPRESA]</p>', 'data:image/jpeg;base64,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', 'data:image/jpeg;base64,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', '2025-05-12 15:56:20', '2025-09-02 12:59:33');

-- ----------------------------
-- Table structure for cotizaciones
-- ----------------------------
DROP TABLE IF EXISTS `cotizaciones`;
CREATE TABLE `cotizaciones`  (
  `cotizacion_id` int NOT NULL AUTO_INCREMENT,
  `numero` int NULL DEFAULT NULL,
  `id_tido` int NOT NULL,
  `id_tipo_pago` int NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `dias_pagos` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_cliente` int NOT NULL,
  `total` double(10, 2) NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `usar_precio` int NULL DEFAULT NULL,
  `moneda` int NULL DEFAULT 1,
  `cm_tc` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_usuario` int NOT NULL,
  `descuento` decimal(10, 2) NULL DEFAULT 0.00,
  `aplicar_igv` tinyint(1) NOT NULL DEFAULT 1,
  `id_asunto` int NULL DEFAULT NULL,
  PRIMARY KEY (`cotizacion_id`) USING BTREE,
  INDEX `id_tido`(`id_tido` ASC) USING BTREE,
  INDEX `id_tipo_pago`(`id_tipo_pago` ASC) USING BTREE,
  INDEX `id_cliente`(`id_cliente` ASC) USING BTREE,
  INDEX `id_asunto`(`id_asunto` ASC) USING BTREE,
  CONSTRAINT `fk_cotizaciones_asuntos` FOREIGN KEY (`id_asunto`) REFERENCES `asuntos_coti` (`id_asunto`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1880 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cotizaciones
-- ----------------------------
INSERT INTO `cotizaciones` VALUES (1871, 1, 2, 1, '2025-12-15', '', '1', 31, 387.50, '1', 12, 1, 5, 1, '', 40, 0.00, 1, NULL);
INSERT INTO `cotizaciones` VALUES (1872, 2, 2, 1, '2025-12-16', '', '1', 32, 3894.00, '1', 12, 1, 5, 1, '', 40, 0.00, 1, NULL);
INSERT INTO `cotizaciones` VALUES (1873, 3, 2, 1, '2025-12-17', '', '1', 32, 3958.00, '0', 12, 1, 5, 1, '', 40, 0.00, 1, NULL);
INSERT INTO `cotizaciones` VALUES (1874, 4, 1, 2, '2026-01-20', '2026-02-19,2026-03-19,2026-04-19', '1', 34, 3658.00, '1', 12, 1, 5, 1, '', 40, 0.00, 1, NULL);
INSERT INTO `cotizaciones` VALUES (1875, 5, 1, 2, '2026-01-20', '2026-02-19,2026-03-19', '1', 34, 3658.00, '0', 12, 1, 5, 1, '', 40, 0.00, 1, NULL);
INSERT INTO `cotizaciones` VALUES (1876, 6, 1, 2, '2026-01-20', '2026-02-19,2026-03-19', '1', 34, 3658.00, '0', 12, 1, 5, 1, '', 40, 0.00, 1, NULL);
INSERT INTO `cotizaciones` VALUES (1877, 7, 1, 1, '2026-03-05', '', '1', 34, 3584.84, '0', 12, 1, 5, 1, '', 40, 2.00, 1, NULL);
INSERT INTO `cotizaciones` VALUES (1878, 8, 1, 1, '2026-04-08', '', '1', 34, 3658.00, '0', 12, 1, 5, 1, '', 40, 0.00, 1, NULL);
INSERT INTO `cotizaciones` VALUES (1879, 9, 1, 2, '2026-04-08', '2026-05-07', '1', 34, 3658.00, '0', 12, 1, 5, 1, '', 40, 0.00, 1, NULL);

-- ----------------------------
-- Table structure for cuotas_cotizacion
-- ----------------------------
DROP TABLE IF EXISTS `cuotas_cotizacion`;
CREATE TABLE `cuotas_cotizacion`  (
  `cuota_coti_id` int NOT NULL AUTO_INCREMENT,
  `id_coti` int NULL DEFAULT NULL,
  `monto` double(10, 3) NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0',
  `tipo` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'cuota',
  PRIMARY KEY (`cuota_coti_id`) USING BTREE,
  INDEX `id_coti`(`id_coti` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 166 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of cuotas_cotizacion
-- ----------------------------
INSERT INTO `cuotas_cotizacion` VALUES (149, 1871, 0.000, '2025-12-15', '0', 'inicial');
INSERT INTO `cuotas_cotizacion` VALUES (150, 1872, 0.000, '2025-12-16', '0', 'inicial');
INSERT INTO `cuotas_cotizacion` VALUES (151, 1873, 0.000, '2025-12-17', '0', 'inicial');
INSERT INTO `cuotas_cotizacion` VALUES (152, 1874, 0.000, '2026-01-20', '0', 'inicial');
INSERT INTO `cuotas_cotizacion` VALUES (153, 1874, 1219.330, '2026-02-19', '0', 'cuota');
INSERT INTO `cuotas_cotizacion` VALUES (154, 1874, 1219.330, '2026-03-19', '0', 'cuota');
INSERT INTO `cuotas_cotizacion` VALUES (155, 1874, 1219.340, '2026-04-19', '0', 'cuota');
INSERT INTO `cuotas_cotizacion` VALUES (156, 1875, 0.000, '2026-01-20', '0', 'inicial');
INSERT INTO `cuotas_cotizacion` VALUES (157, 1875, 1829.000, '2026-02-19', '0', 'cuota');
INSERT INTO `cuotas_cotizacion` VALUES (158, 1875, 1829.000, '2026-03-19', '0', 'cuota');
INSERT INTO `cuotas_cotizacion` VALUES (159, 1876, 0.000, '2026-01-20', '0', 'inicial');
INSERT INTO `cuotas_cotizacion` VALUES (160, 1876, 1829.000, '2026-02-19', '0', 'cuota');
INSERT INTO `cuotas_cotizacion` VALUES (161, 1876, 1829.000, '2026-03-19', '0', 'cuota');
INSERT INTO `cuotas_cotizacion` VALUES (162, 1877, 0.000, '2026-03-05', '0', 'inicial');
INSERT INTO `cuotas_cotizacion` VALUES (163, 1878, 0.000, '2026-04-08', '0', 'inicial');
INSERT INTO `cuotas_cotizacion` VALUES (164, 1879, 841.340, '2026-04-08', '0', 'inicial');
INSERT INTO `cuotas_cotizacion` VALUES (165, 1879, 3658.000, '2026-05-07', '0', 'cuota');

-- ----------------------------
-- Table structure for detalle_garantia
-- ----------------------------
DROP TABLE IF EXISTS `detalle_garantia`;
CREATE TABLE `detalle_garantia`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_garantia` int NOT NULL,
  `detalle_serie_id` int NOT NULL,
  `numero_serie` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `marca_id` int NULL DEFAULT NULL,
  `modelo_id` int NULL DEFAULT NULL,
  `equipo_id` int NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_garantia`(`id_garantia` ASC) USING BTREE,
  INDEX `idx_detalle_serie`(`detalle_serie_id` ASC) USING BTREE,
  CONSTRAINT `detalle_garantia_ibfk_1` FOREIGN KEY (`id_garantia`) REFERENCES `garantia` (`id_garantia`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `detalle_garantia_ibfk_2` FOREIGN KEY (`detalle_serie_id`) REFERENCES `detalle_serie` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of detalle_garantia
-- ----------------------------
INSERT INTO `detalle_garantia` VALUES (1, 48, 15, '12324', 7, 10, 5, '2026-01-20 14:21:32');
INSERT INTO `detalle_garantia` VALUES (2, 48, 16, '12325', 6, 10, 6, '2026-01-20 14:21:32');
INSERT INTO `detalle_garantia` VALUES (5, 50, 13, '12322', 1, 8, 4, '2026-01-20 14:43:46');
INSERT INTO `detalle_garantia` VALUES (6, 50, 14, '12323', 6, 10, 6, '2026-01-20 14:43:46');
INSERT INTO `detalle_garantia` VALUES (7, 51, 17, '12326', 1, 8, 5, '2026-01-20 16:31:30');
INSERT INTO `detalle_garantia` VALUES (8, 51, 18, '12327', 1, 8, 5, '2026-01-20 16:31:30');
INSERT INTO `detalle_garantia` VALUES (9, 51, 19, '12328', 1, 8, 5, '2026-01-20 16:31:30');
INSERT INTO `detalle_garantia` VALUES (10, 51, 20, '12329', 1, 8, 5, '2026-01-20 16:31:30');

-- ----------------------------
-- Table structure for detalle_serie
-- ----------------------------
DROP TABLE IF EXISTS `detalle_serie`;
CREATE TABLE `detalle_serie`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero_serie_id` int NOT NULL,
  `modelo_id` int NULL DEFAULT NULL,
  `marca_id` int NULL DEFAULT NULL,
  `equipo_id` int NULL DEFAULT NULL,
  `id_producto` int NULL DEFAULT NULL,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `estado` enum('disponible','en_garantia') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'disponible',
  `estado_prealerta` enum('disponible','en_trabajo','culminado') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'disponible',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `numero_serie_unique`(`numero_serie` ASC) USING BTREE,
  INDEX `numero_serie_id`(`numero_serie_id` ASC) USING BTREE,
  INDEX `modelo_id`(`modelo_id` ASC) USING BTREE,
  INDEX `marca_id`(`marca_id` ASC) USING BTREE,
  INDEX `equipo_id`(`equipo_id` ASC) USING BTREE,
  INDEX `idx_detalle_serie_producto`(`id_producto` ASC) USING BTREE,
  CONSTRAINT `detalle_serie_ibfk_1` FOREIGN KEY (`numero_serie_id`) REFERENCES `numero_series` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `detalle_serie_ibfk_2` FOREIGN KEY (`modelo_id`) REFERENCES `modelos` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `detalle_serie_ibfk_3` FOREIGN KEY (`marca_id`) REFERENCES `marcas` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `detalle_serie_ibfk_4` FOREIGN KEY (`equipo_id`) REFERENCES `equipos` (`id`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `detalle_serie_producto_fk` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of detalle_serie
-- ----------------------------
INSERT INTO `detalle_serie` VALUES (2, 18, 8, 1, 5, NULL, '12313', 'disponible', 'culminado');
INSERT INTO `detalle_serie` VALUES (3, 18, 8, 1, 5, NULL, '12314', 'disponible', 'culminado');
INSERT INTO `detalle_serie` VALUES (4, 18, 8, 1, 5, NULL, '12315', 'disponible', 'culminado');
INSERT INTO `detalle_serie` VALUES (5, 18, 8, 1, 5, NULL, '12316', 'disponible', 'culminado');
INSERT INTO `detalle_serie` VALUES (6, 18, 8, 1, 5, NULL, '12317', 'disponible', 'culminado');
INSERT INTO `detalle_serie` VALUES (7, 18, 8, 1, 5, NULL, '12318', 'disponible', 'culminado');
INSERT INTO `detalle_serie` VALUES (8, 18, 8, 1, 5, NULL, '12319', 'disponible', 'culminado');
INSERT INTO `detalle_serie` VALUES (9, 18, 8, 1, 5, NULL, '12320', 'disponible', 'culminado');
INSERT INTO `detalle_serie` VALUES (10, 18, 8, 1, 5, NULL, '12321', 'disponible', 'culminado');
INSERT INTO `detalle_serie` VALUES (13, 20, 8, 1, 4, NULL, '12322', 'en_garantia', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (14, 20, 10, 6, 6, NULL, '12323', 'en_garantia', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (15, 21, 10, 7, 5, NULL, '12324', 'en_garantia', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (16, 21, 10, 6, 6, NULL, '12325', 'en_garantia', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (17, 22, 8, 1, 5, NULL, '12326', 'en_garantia', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (18, 22, 8, 1, 5, NULL, '12327', 'en_garantia', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (19, 22, 8, 1, 5, NULL, '12328', 'en_garantia', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (20, 22, 8, 1, 5, NULL, '12329', 'en_garantia', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (21, 23, 10, 1, 4, NULL, '12330', 'disponible', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (22, 23, 10, 2, 3, NULL, '12331', 'disponible', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (23, 23, 11, 6, 6, NULL, '12332', 'disponible', 'en_trabajo');
INSERT INTO `detalle_serie` VALUES (24, 24, 8, 1, 5, NULL, '12333', 'disponible', 'disponible');
INSERT INTO `detalle_serie` VALUES (25, 25, 10, 6, 4, NULL, '12334', 'disponible', 'disponible');
INSERT INTO `detalle_serie` VALUES (26, 26, 8, 2, 4, 373, '12335', 'disponible', 'disponible');
INSERT INTO `detalle_serie` VALUES (27, 27, 8, 1, 4, 38, '12336', 'disponible', 'disponible');

-- ----------------------------
-- Table structure for diagnostico_repuestos
-- ----------------------------
DROP TABLE IF EXISTS `diagnostico_repuestos`;
CREATE TABLE `diagnostico_repuestos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of diagnostico_repuestos
-- ----------------------------
INSERT INTO `diagnostico_repuestos` VALUES (1, 'Equipo presenta fallas en el sistema electrico\r\nSe requiere cambio de componentes internos\r\nRevision completa del sistema de funcionamiento\r\nLimpieza general del equipo\r\nCalibracion de parametros de operacion', 1, '2025-07-07 11:10:45');

-- ----------------------------
-- Table structure for dias_compras
-- ----------------------------
DROP TABLE IF EXISTS `dias_compras`;
CREATE TABLE `dias_compras`  (
  `dias_compra_id` int NOT NULL AUTO_INCREMENT,
  `id_compra` int NULL DEFAULT NULL,
  `monto` double(10, 3) NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`dias_compra_id`) USING BTREE,
  INDEX `id_compra`(`id_compra` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dias_compras
-- ----------------------------
INSERT INTO `dias_compras` VALUES (3, 4, 150.000, '2025-09-24', '1');
INSERT INTO `dias_compras` VALUES (4, 4, 150.000, '2025-10-24', '0');
INSERT INTO `dias_compras` VALUES (5, 6, 112.500, '2026-01-02', '0');
INSERT INTO `dias_compras` VALUES (6, 6, 112.500, '2026-02-02', '0');

-- ----------------------------
-- Table structure for dias_ventas
-- ----------------------------
DROP TABLE IF EXISTS `dias_ventas`;
CREATE TABLE `dias_ventas`  (
  `dias_venta_id` int NOT NULL AUTO_INCREMENT,
  `id_venta` int NULL DEFAULT NULL,
  `monto` double(10, 3) NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0',
  PRIMARY KEY (`dias_venta_id`) USING BTREE,
  INDEX `id_venta`(`id_venta` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of dias_ventas
-- ----------------------------
INSERT INTO `dias_ventas` VALUES (1, 11, 0.000, '2025-11-18', '0');
INSERT INTO `dias_ventas` VALUES (2, 14, 200.000, '2026-01-02', '0');
INSERT INTO `dias_ventas` VALUES (3, 14, 200.000, '2026-02-02', '0');
INSERT INTO `dias_ventas` VALUES (4, 15, 245.150, '2026-01-02', '0');
INSERT INTO `dias_ventas` VALUES (5, 15, 245.150, '2026-02-02', '0');
INSERT INTO `dias_ventas` VALUES (6, 16, 300.980, '2026-01-02', '0');
INSERT INTO `dias_ventas` VALUES (7, 16, 300.970, '2026-02-02', '0');
INSERT INTO `dias_ventas` VALUES (8, 18, 100.000, '2025-12-02', '0');
INSERT INTO `dias_ventas` VALUES (9, 18, 366.250, '2026-01-01', '0');
INSERT INTO `dias_ventas` VALUES (10, 18, 366.240, '2026-02-01', '0');
INSERT INTO `dias_ventas` VALUES (11, 20, 0.000, '2025-12-15', '0');
INSERT INTO `dias_ventas` VALUES (12, 21, 0.000, '2025-12-15', '0');
INSERT INTO `dias_ventas` VALUES (13, 22, 0.000, '2025-12-16', '0');
INSERT INTO `dias_ventas` VALUES (14, 23, 0.000, '2026-01-20', '0');
INSERT INTO `dias_ventas` VALUES (15, 23, 1219.330, '2026-02-19', '0');
INSERT INTO `dias_ventas` VALUES (16, 23, 1219.330, '2026-03-19', '0');
INSERT INTO `dias_ventas` VALUES (17, 23, 1219.340, '2026-04-19', '0');

-- ----------------------------
-- Table structure for documentos_empresas
-- ----------------------------
DROP TABLE IF EXISTS `documentos_empresas`;
CREATE TABLE `documentos_empresas`  (
  `id_empresa` int NOT NULL,
  `id_tido` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `serie` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  INDEX `fk_empresas_has_documentos_sunat_documentos_sunat1_idx`(`id_tido` ASC) USING BTREE,
  INDEX `fk_empresas_has_documentos_sunat_empresas1_idx`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of documentos_empresas
-- ----------------------------
INSERT INTO `documentos_empresas` VALUES (12, 1, 1, 'B001', 616);
INSERT INTO `documentos_empresas` VALUES (12, 2, 1, 'F001', 2396);
INSERT INTO `documentos_empresas` VALUES (12, 3, 1, 'F001', 14);
INSERT INTO `documentos_empresas` VALUES (12, 4, 1, 'F001', 1);
INSERT INTO `documentos_empresas` VALUES (12, 6, 1, 'NV01', 2951);
INSERT INTO `documentos_empresas` VALUES (12, 11, 1, 'T001', 1135);
INSERT INTO `documentos_empresas` VALUES (12, 1, 2, 'B002', 595);
INSERT INTO `documentos_empresas` VALUES (12, 2, 2, 'F002', 2359);
INSERT INTO `documentos_empresas` VALUES (12, 3, 2, 'F002', 6);
INSERT INTO `documentos_empresas` VALUES (12, 4, 2, 'F002', 1);
INSERT INTO `documentos_empresas` VALUES (12, 6, 2, 'NV02', 2945);
INSERT INTO `documentos_empresas` VALUES (12, 11, 2, 'T002', 1025);
INSERT INTO `documentos_empresas` VALUES (12, 12, 1, 'OC', 8);

-- ----------------------------
-- Table structure for documentos_sunat
-- ----------------------------
DROP TABLE IF EXISTS `documentos_sunat`;
CREATE TABLE `documentos_sunat`  (
  `id_tido` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cod_sunat` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `abreviatura` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_tido`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of documentos_sunat
-- ----------------------------
INSERT INTO `documentos_sunat` VALUES (1, 'BOLETA DE VENTA', '03', 'BT');
INSERT INTO `documentos_sunat` VALUES (2, 'FACTURA', '01', 'FT');
INSERT INTO `documentos_sunat` VALUES (3, 'NOTA DE CREDITO', '07', 'NC');
INSERT INTO `documentos_sunat` VALUES (4, 'NOTA DE DEBITO', '08', 'ND');
INSERT INTO `documentos_sunat` VALUES (5, 'NOTA DE RECEPCION', '09', 'GR');
INSERT INTO `documentos_sunat` VALUES (6, 'NOTA DE VENTA', '00', 'NV');
INSERT INTO `documentos_sunat` VALUES (7, 'NOTA DE SEPARACION', '00', 'NS');
INSERT INTO `documentos_sunat` VALUES (8, 'NOTA DE TRASLADO', '00', 'NT');
INSERT INTO `documentos_sunat` VALUES (9, 'NOTA DE INVENTARIO', '00', 'NIV');
INSERT INTO `documentos_sunat` VALUES (10, 'NOTA DE INGRESO', '00', 'NIG');
INSERT INTO `documentos_sunat` VALUES (11, 'GUIA DE REMISION', '09', 'GR');
INSERT INTO `documentos_sunat` VALUES (12, 'NOTA DE COMPRA', '00', NULL);

-- ----------------------------
-- Table structure for empresas
-- ----------------------------
DROP TABLE IF EXISTS `empresas`;
CREATE TABLE `empresas`  (
  `id_empresa` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `razon_social` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `comercial` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `cod_sucursal` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `email` varchar(145) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `password` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `user_sol` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `clave_sol` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `client_id_sunat` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `client_secret_sunat` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `logo` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ubigeo` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `distrito` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `provincia` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `departamento` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `tipo_impresion` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `modo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `igv` double(10, 2) NULL DEFAULT 0.18,
  `propaganda` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono2` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `telefono3` varchar(30) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_empresa`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 35 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of empresas
-- ----------------------------
INSERT INTO `empresas` VALUES (12, '20538381978', 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.', 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.', NULL, 'JAVIER PRADO ESTE 8402, LIMA – LIMA – ATE', 'ventas@industriajvcsac.com', '01 7489599', '1', NULL, 'JVCADM', 'JVC123456', '4c4fd4c3-c380-4447-9223-0e60a8a09a14', 'WLX0zMBp8jE2Jhr5UHomjg==', '69d6c13398c7c.jpg', '040101', 'PUEBLO LIBRE', 'LIMA', 'LIMA', NULL, 'beta', 0.18, '', '', '');

-- ----------------------------
-- Table structure for equipos
-- ----------------------------
DROP TABLE IF EXISTS `equipos`;
CREATE TABLE `equipos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `marca_id` int NULL DEFAULT NULL,
  `modelo_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of equipos
-- ----------------------------
INSERT INTO `equipos` VALUES (3, 'ASPIRADORA', 1, 11);
INSERT INTO `equipos` VALUES (4, 'LUSTRADORA', 2, 8);
INSERT INTO `equipos` VALUES (5, 'LAVA BUTACAS', 2, 8);
INSERT INTO `equipos` VALUES (6, 'LAVADORA DE ALFOMBRAS ', 6, 10);
INSERT INTO `equipos` VALUES (9, 'FREGADORA', 7, NULL);

-- ----------------------------
-- Table structure for garantia
-- ----------------------------
DROP TABLE IF EXISTS `garantia`;
CREATE TABLE `garantia`  (
  `id_garantia` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `numero_serie_id` int NOT NULL,
  `detalle_serie_id` int NULL DEFAULT NULL,
  `id_cliente` int NULL DEFAULT NULL,
  `series_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `guia_remision` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fecha_inicio` date NULL DEFAULT NULL,
  `fecha_caducidad` date NULL DEFAULT NULL,
  PRIMARY KEY (`id_garantia`) USING BTREE,
  INDEX `numero_serie_id`(`numero_serie_id` ASC) USING BTREE,
  INDEX `fk_garantia_detalle_serie`(`detalle_serie_id` ASC) USING BTREE,
  INDEX `idx_series_ids`(`series_ids`(100) ASC) USING BTREE,
  INDEX `idx_id_cliente`(`id_cliente` ASC) USING BTREE,
  CONSTRAINT `fk_garantia_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_garantia_detalle_serie` FOREIGN KEY (`detalle_serie_id`) REFERENCES `detalle_serie` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 52 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of garantia
-- ----------------------------
INSERT INTO `garantia` VALUES (48, 'GR-01', 21, NULL, 34, NULL, '', '2026-01-20', '2027-01-20');
INSERT INTO `garantia` VALUES (50, 'GR-02', 20, NULL, 34, NULL, '', '2025-01-01', '2026-02-19');
INSERT INTO `garantia` VALUES (51, 'GR-03', 22, NULL, 34, NULL, '', '2026-01-20', '2027-01-20');

-- ----------------------------
-- Table structure for gestion_activos
-- ----------------------------
DROP TABLE IF EXISTS `gestion_activos`;
CREATE TABLE `gestion_activos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cliente_razon_social` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `marca` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `equipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `modelo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `numero_serie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_ingreso` date NULL DEFAULT NULL,
  `fecha_salida` date NULL DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'PENDIENTE' COMMENT 'Estados posibles: PENDIENTE, CONFIRMADO',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of gestion_activos
-- ----------------------------
INSERT INTO `gestion_activos` VALUES (14, 'GA-01', 'LIM KIT CORPORACION E.I.R.L.', 'PRESTAMOS', 'TAURO', 'LUSTRADORA', 'TD-12N', '123456', '2025-03-14', '2025-03-07', 'con pad y porta pad', 'CONFIRMADO');
INSERT INTO `gestion_activos` VALUES (15, 'GA-02', 'EMER RODRIGO YARLEQUE ZAPATA', 'REMPLAZO', 'MASTER GOLDS', 'ASPIRADORA', 'AMG-15L', '159123', '2025-03-18', '2025-03-15', 'hello wpord', 'CONFIRMADO');
INSERT INTO `gestion_activos` VALUES (16, 'GA-03', 'EMER RODRIGO YARLEQUE ZAPATA', 'ALQUILER', 'MASTER GOLDS', 'ASPIRADORA', 'AMG-15L', '159123', '2025-03-23', '2025-03-15', 'aspiradora detaller aqui', 'CONFIRMADO');
INSERT INTO `gestion_activos` VALUES (17, '', 'BRENDY YOSELY ZAPATA TORRES', 'ALQUILER', 'MASTER GOLDS', 'LUSTRADORA', 'AMG-15L', '1549416', '2025-06-07', '2025-07-07', '', 'PENDIENTE');
INSERT INTO `gestion_activos` VALUES (18, 'GA-04', 'LIM KIT CORPORACION E.I.R.L.', 'ALQUILER', 'MASTER GOLDS', 'LUSTRADORA', 'AMG-15L', '1549416', '2025-12-04', '2025-12-03', '1 DIA DE ALQUILER', 'PENDIENTE');

-- ----------------------------
-- Table structure for gestion_adjuntos
-- ----------------------------
DROP TABLE IF EXISTS `gestion_adjuntos`;
CREATE TABLE `gestion_adjuntos`  (
  `id_adjunto` int NOT NULL AUTO_INCREMENT,
  `id_archivo` int NOT NULL,
  `fecha_creacion` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `url_pdf` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'Ruta del archivo PDF',
  `url_editable` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'Ruta del archivo editable (Word, Excel, etc.)',
  `url_imagen` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'Ruta de la imagen',
  `url_youtube` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'URL del video de YouTube',
  `url_imagen_2` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'Ruta de la segunda imagen',
  `url_imagen_3` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'Ruta de la tercera imagen',
  PRIMARY KEY (`id_adjunto`) USING BTREE,
  INDEX `id_archivo`(`id_archivo` ASC) USING BTREE,
  INDEX `idx_url_pdf`(`url_pdf` ASC) USING BTREE,
  INDEX `idx_url_editable`(`url_editable` ASC) USING BTREE,
  INDEX `idx_url_imagen`(`url_imagen` ASC) USING BTREE,
  INDEX `idx_url_youtube`(`url_youtube` ASC) USING BTREE,
  INDEX `idx_url_imagen_2`(`url_imagen_2` ASC) USING BTREE,
  INDEX `idx_url_imagen_3`(`url_imagen_3` ASC) USING BTREE,
  CONSTRAINT `gestion_adjuntos_ibfk_1` FOREIGN KEY (`id_archivo`) REFERENCES `gestion_archivos` (`id_archivo`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 31 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of gestion_adjuntos
-- ----------------------------
INSERT INTO `gestion_adjuntos` VALUES (22, 9, '2025-08-25 11:54:45', 'files/gestion_archivos/pdf/68ac87456bb3f_1756137285.pdf', NULL, NULL, 'https://www.youtube.com/watch?v=SlYVhyhbwGo', NULL, NULL);
INSERT INTO `gestion_adjuntos` VALUES (23, 10, '2025-08-25 11:57:13', 'files/gestion_archivos/pdf/68ac87d98b4a2_1756137433.pdf', NULL, NULL, 'https://www.youtube.com/watch?v=SlYVhyhbwGo', NULL, NULL);
INSERT INTO `gestion_adjuntos` VALUES (24, 11, '2025-08-25 11:58:49', 'files/gestion_archivos/pdf/68ac88399d641_1756137529.pdf', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gestion_adjuntos` VALUES (25, 12, '2025-08-25 12:17:40', 'files/gestion_archivos/pdf/68ac8ca424ee1_1756138660.pdf', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gestion_adjuntos` VALUES (26, 13, '2025-08-25 12:21:04', 'files/gestion_archivos/pdf/68ac8d7056731_1756138864.pdf', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gestion_adjuntos` VALUES (27, 14, '2025-08-25 12:22:12', 'files/gestion_archivos/pdf/68ac8db4c6cf7_1756138932.pdf', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gestion_adjuntos` VALUES (28, 15, '2025-08-25 12:24:07', 'files/gestion_archivos/pdf/68ac8e270845a_1756139047.pdf', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gestion_adjuntos` VALUES (29, 16, '2025-08-25 12:24:39', 'files/gestion_archivos/pdf/68ac8e47b9274_1756139079.pdf', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `gestion_adjuntos` VALUES (30, 17, '2025-08-27 16:09:48', 'files/gestion_archivos/pdf/6a17316d266fc_1779904877.pdf', 'files/gestion_archivos/editable/68af660c8d8ec_1756325388.xlsx', 'files/gestion_archivos/imagen/68af660c8f659_1756325388.jpg', 'https://www.youtube.com/watch?v=cSbnijvxqGo&list=RDcSbnijvxqGo&start_radio=1', 'files/gestion_archivos/imagen/68af660c90796_1756325388.jpg', 'files/gestion_archivos/imagen/68af660c91280_1756325388.jpg');

-- ----------------------------
-- Table structure for gestion_archivos
-- ----------------------------
DROP TABLE IF EXISTS `gestion_archivos`;
CREATE TABLE `gestion_archivos`  (
  `id_archivo` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tipo` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'ficha_tecnica, manual, informe, carta, constancia, interno, otro',
  `id_producto` int NULL DEFAULT NULL COMMENT 'Relación con producto (opcional)',
  `fecha_creacion` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `version` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '1.0',
  `estado` tinyint(1) NULL DEFAULT 1,
  `id_empresa` int NOT NULL,
  `sucursal` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id_archivo`) USING BTREE,
  INDEX `id_producto`(`id_producto` ASC) USING BTREE,
  CONSTRAINT `gestion_archivos_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of gestion_archivos
-- ----------------------------
INSERT INTO `gestion_archivos` VALUES (9, 'FICHA TECNICA LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 12\" - MARCA: CRIS-TAURO', 'ficha_tecnica', 18, '2025-08-25 10:54:45', '2025-08-25 11:54:45', '1.0', 1, 12, '1');
INSERT INTO `gestion_archivos` VALUES (10, 'FICHA TECNICA LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 14\" - MARCA: CRIS-TAURO', 'ficha_tecnica', 20, '2025-08-25 10:57:13', '2025-08-25 11:57:13', '1.0', 1, 12, '1');
INSERT INTO `gestion_archivos` VALUES (11, 'FICHA TECNICA LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 16\" - MARCA: CRIS-TAURO', 'ficha_tecnica', 21, '2025-08-25 10:58:49', '2025-08-25 11:58:49', '1.0', 1, 12, '1');
INSERT INTO `gestion_archivos` VALUES (12, 'FICHA TECNICA LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 18\" - MARCA: CRIS-TAURO', 'ficha_tecnica', 28, '2025-08-25 11:17:40', '2025-08-25 12:17:40', '1.0', 1, 12, '1');
INSERT INTO `gestion_archivos` VALUES (13, 'FICHA TECNICA LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 20\" - MARCA: CRIS-TAURO', 'ficha_tecnica', 30, '2025-08-25 11:21:04', '2025-08-25 12:21:04', '1.0', 1, 12, '1');
INSERT INTO `gestion_archivos` VALUES (14, 'FICHA TECNICA LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 23\" - MARCA: CRIS-TAURO', 'ficha_tecnica', 31, '2025-08-25 11:22:12', '2025-08-25 12:22:12', '1.0', 1, 12, '1');
INSERT INTO `gestion_archivos` VALUES (15, 'FICHA TECNICA ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 6 GALONES', 'ficha_tecnica', 33, '2025-08-25 11:24:07', '2025-08-25 12:24:07', '1.0', 1, 12, '1');
INSERT INTO `gestion_archivos` VALUES (16, 'FICHA TECNICA ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 8 GALONES', 'ficha_tecnica', 34, '2025-08-25 11:24:39', '2025-08-25 12:24:39', '1.0', 1, 12, '1');
INSERT INTO `gestion_archivos` VALUES (17, 'informe tec', 'ficha_tecnica', 42, '2025-08-27 15:09:48', '2026-05-27 13:01:17', '1.0', 1, 12, '1');

-- ----------------------------
-- Table structure for gestion_metadatos
-- ----------------------------
DROP TABLE IF EXISTS `gestion_metadatos`;
CREATE TABLE `gestion_metadatos`  (
  `id_metadato` int NOT NULL AUTO_INCREMENT,
  `id_archivo` int NOT NULL,
  `clave` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `valor` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id_metadato`) USING BTREE,
  INDEX `id_archivo`(`id_archivo` ASC) USING BTREE,
  CONSTRAINT `gestion_metadatos_ibfk_1` FOREIGN KEY (`id_archivo`) REFERENCES `gestion_archivos` (`id_archivo`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of gestion_metadatos
-- ----------------------------

-- ----------------------------
-- Table structure for gestion_plantillas
-- ----------------------------
DROP TABLE IF EXISTS `gestion_plantillas`;
CREATE TABLE `gestion_plantillas`  (
  `id_plantilla` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `tipo` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL COMMENT 'informe, carta, constancia, interno, otro',
  `contenido` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `id_empresa` int NOT NULL,
  `estado` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id_plantilla`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of gestion_plantillas
-- ----------------------------

-- ----------------------------
-- Table structure for gestion_versiones
-- ----------------------------
DROP TABLE IF EXISTS `gestion_versiones`;
CREATE TABLE `gestion_versiones`  (
  `id_version` int NOT NULL AUTO_INCREMENT,
  `id_archivo` int NOT NULL,
  `version` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `fecha_creacion` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `contenido` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `id_usuario` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_version`) USING BTREE,
  INDEX `id_archivo`(`id_archivo` ASC) USING BTREE,
  CONSTRAINT `gestion_versiones_ibfk_1` FOREIGN KEY (`id_archivo`) REFERENCES `gestion_archivos` (`id_archivo`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of gestion_versiones
-- ----------------------------

-- ----------------------------
-- Table structure for guia_choferes
-- ----------------------------
DROP TABLE IF EXISTS `guia_choferes`;
CREATE TABLE `guia_choferes`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `dni` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `telefono` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_choferes
-- ----------------------------
INSERT INTO `guia_choferes` VALUES (14, 'EMER RODRIGO YARLEQUE ZAPATA', '77425200', NULL, '2025-06-23 08:56:19', '2025-06-23 08:56:19');
INSERT INTO `guia_choferes` VALUES (15, 'EMER YARLEQUE ZAPATA', '77425201', NULL, '2025-06-23 08:58:16', '2025-06-23 08:58:16');
INSERT INTO `guia_choferes` VALUES (16, 'Eduardo Crisostomo Rodriguez', '76877537', NULL, '2025-08-14 12:35:51', '2025-08-14 12:35:51');

-- ----------------------------
-- Table structure for guia_conductor_configuraciones
-- ----------------------------
DROP TABLE IF EXISTS `guia_conductor_configuraciones`;
CREATE TABLE `guia_conductor_configuraciones`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `chofer_id` int NOT NULL,
  `chofer_nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `chofer_dni` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `vehiculo_placa` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `vehiculo_marca` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `licencia_numero` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `fecha_registro` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_chofer_id`(`chofer_id` ASC) USING BTREE,
  INDEX `idx_chofer_dni`(`chofer_dni` ASC) USING BTREE,
  INDEX `idx_vehiculo_placa`(`vehiculo_placa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_conductor_configuraciones
-- ----------------------------
INSERT INTO `guia_conductor_configuraciones` VALUES (1, 14, 'EMER RODRIGO YARLEQUE ZAPATA', '77425200', 'donato', 'dfsvsdv', '124312', '2025-06-23 08:56:19');
INSERT INTO `guia_conductor_configuraciones` VALUES (2, 15, 'EMER YARLEQUE ZAPATA', '77425201', 'sdcsa', 'sdacsac', 'sacdsa', '2025-06-23 08:58:16');

-- ----------------------------
-- Table structure for guia_detalles
-- ----------------------------
DROP TABLE IF EXISTS `guia_detalles`;
CREATE TABLE `guia_detalles`  (
  `guia_detalle_id` int NOT NULL AUTO_INCREMENT,
  `id_guia` int NULL DEFAULT NULL,
  `id_producto` int NULL DEFAULT NULL,
  `id_repuesto` int NULL DEFAULT NULL,
  `tipo_item` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'producto',
  `detalles` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `unidad` varchar(10) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `cantidad` int NULL DEFAULT NULL,
  `precio` double(20, 5) NULL DEFAULT NULL,
  `id_guia_equipo` int NULL DEFAULT NULL,
  PRIMARY KEY (`guia_detalle_id`) USING BTREE,
  INDEX `id_guia`(`id_guia` ASC) USING BTREE,
  INDEX `fk_guia_detalles_equipo`(`id_guia_equipo` ASC) USING BTREE,
  CONSTRAINT `fk_guia_detalles_equipo` FOREIGN KEY (`id_guia_equipo`) REFERENCES `guia_equipos` (`id_guia_equipo`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 14 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_detalles
-- ----------------------------
INSERT INTO `guia_detalles` VALUES (7, 3, 18, NULL, 'producto', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 12\" - MARCA: CRIS-TAURO', 'NIU', 1, 3658.00000, NULL);
INSERT INTO `guia_detalles` VALUES (8, 4, 44, NULL, 'producto', 'ASPIRADORA PROFESIONAL DE POLVO DE 10 LITROS (LINEA HOTELERA) - MARCA: MASTER GOLDS', 'UNIDAD', 1, 767.00000, NULL);
INSERT INTO `guia_detalles` VALUES (9, 5, 44, NULL, 'producto', 'ASPIRADORA PROFESIONAL DE POLVO DE 10 LITROS (LINEA HOTELERA) - MARCA: MASTER GOLDS', 'UNIDAD', 1, 500.00000, NULL);
INSERT INTO `guia_detalles` VALUES (10, 6, 44, NULL, 'producto', 'ASPIRADORA PROFESIONAL DE POLVO DE 10 LITROS (LINEA HOTELERA) - MARCA: MASTER GOLDS', 'UNIDAD', 1, 500.00000, NULL);
INSERT INTO `guia_detalles` VALUES (11, 7, 44, NULL, 'producto', 'ASPIRADORA PROFESIONAL DE POLVO DE 10 LITROS (LINEA HOTELERA) - MARCA: MASTER GOLDS', 'UNIDAD', 1, 690.30000, NULL);
INSERT INTO `guia_detalles` VALUES (12, 8, 44, NULL, 'producto', 'ASPIRADORA PROFESIONAL DE POLVO DE 10 LITROS (LINEA HOTELERA) - MARCA: MASTER GOLDS', 'UNIDAD', 1, 690.30000, NULL);
INSERT INTO `guia_detalles` VALUES (13, 9, 45, NULL, 'producto', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 15 LT - MARCA: MASTER GOLDS // SERIE: 147258369', 'UNIDAD', 1, 944.00000, NULL);

-- ----------------------------
-- Table structure for guia_equipos
-- ----------------------------
DROP TABLE IF EXISTS `guia_equipos`;
CREATE TABLE `guia_equipos`  (
  `id_guia_equipo` int NOT NULL AUTO_INCREMENT,
  `id_guia` int NOT NULL,
  `id_cotizacion_equipo` int NULL DEFAULT NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `equipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `modelo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_guia_equipo`) USING BTREE,
  INDEX `fk_guia_equipos_guia`(`id_guia` ASC) USING BTREE,
  CONSTRAINT `fk_guia_equipos_guia` FOREIGN KEY (`id_guia`) REFERENCES `guia_remision` (`id_guia_remision`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_equipos
-- ----------------------------

-- ----------------------------
-- Table structure for guia_licencias
-- ----------------------------
DROP TABLE IF EXISTS `guia_licencias`;
CREATE TABLE `guia_licencias`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(15) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_licencias
-- ----------------------------
INSERT INTO `guia_licencias` VALUES (4, 'Q76877539', '2024-12-18 19:44:16', '2024-12-18 19:44:16');

-- ----------------------------
-- Table structure for guia_remision
-- ----------------------------
DROP TABLE IF EXISTS `guia_remision`;
CREATE TABLE `guia_remision`  (
  `id_guia_remision` int NOT NULL AUTO_INCREMENT,
  `id_venta` int NULL DEFAULT NULL,
  `id_cotizacion` int NULL DEFAULT NULL,
  `id_cotizacion_taller` int NULL DEFAULT NULL,
  `destinatario_nombre` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `destinatario_documento` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `fecha_emision` date NULL DEFAULT NULL,
  `dir_partida` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `motivo_traslado` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `dir_llegada` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ubigeo` varchar(6) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `tipo_transporte` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ruc_transporte` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `razon_transporte` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `vehiculo` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `chofer_brevete` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `chofer_datos` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL,
  `doc_referencia` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `enviado_sunat` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `hash` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(245) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `serie` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `peso` double(8, 2) NULL DEFAULT NULL,
  `nro_bultos` int NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  `ref_orden_compra` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_guia_remision`) USING BTREE,
  INDEX `fk_guia_remision_ventas1_idx`(`id_venta` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 10 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_remision
-- ----------------------------
INSERT INTO `guia_remision` VALUES (3, NULL, NULL, NULL, 'EMER RODRIGO YARLEQUE ZAPATA', '77425200', '2025-10-17', 'AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE', '1', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', '200501', '1', '', '', 'donato', '124312', 'EMER RODRIGO YARLEQUE ZAPATA', '', '', '0', '', '', 'T001', 1128, 1.00, 1, '1', 12, 1, 63, '');
INSERT INTO `guia_remision` VALUES (4, 10, 1856, NULL, NULL, NULL, '2025-12-02', 'AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE', '1', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', '150201', '1', '', '', 'donato', '124312', 'EMER RODRIGO YARLEQUE ZAPATA', 'ENTREGA HOY', '', '0', '', '', 'T001', 1129, 1.00, 1, '1', 12, 1, 63, 'NP-0001');
INSERT INTO `guia_remision` VALUES (5, 12, 1859, NULL, NULL, NULL, '2025-12-02', 'AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE', '1', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', '010202', '1', '', '', 'sdcsa', 'sacdsa', 'EMER YARLEQUE ZAPATA', 'PRUEBA 3', '', '0', '', '', 'T001', 1130, 2.00, 2, '1', 12, 1, 63, '');
INSERT INTO `guia_remision` VALUES (6, 14, 1860, NULL, NULL, NULL, '2025-12-02', 'AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE', '1', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', '010202', '1', '', '', 'donato', '124312', 'EMER RODRIGO YARLEQUE ZAPATA', 'PRUEBA 4', '', '0', '', '', 'T001', 1131, 1.00, 1, '1', 12, 1, 63, '');
INSERT INTO `guia_remision` VALUES (7, 15, 1861, NULL, NULL, NULL, '2025-12-02', 'AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE', '1', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', '010202', '1', '', '', 'sdcsa', 'sacdsa', 'EMER YARLEQUE ZAPATA', '', '', '0', '', '', 'T001', 1132, 25.00, 25, '1', 12, 1, 63, 'MP16');
INSERT INTO `guia_remision` VALUES (8, NULL, 1862, NULL, NULL, NULL, '2025-12-02', 'AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE', '1', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', '010202', '1', '', '', '', '', '', '', '', '0', '', '', 'T001', 1133, 1.00, 1, '1', 12, 1, 63, '');
INSERT INTO `guia_remision` VALUES (9, 17, 1870, NULL, NULL, NULL, '2025-12-03', 'AV. JAVIER PRADO ESTE 8402, LIMA – LIMA - ATE', '1', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', '150103', '1', '', '', 'donato', '124312', 'EMER RODRIGO YARLEQUE ZAPATA', 'ENTREGA URGENTE', '', '0', '', '', 'T001', 1134, 7.00, 7, '1', 12, 1, 63, '10');

-- ----------------------------
-- Table structure for guia_sunat
-- ----------------------------
DROP TABLE IF EXISTS `guia_sunat`;
CREATE TABLE `guia_sunat`  (
  `id_guia` int NOT NULL,
  `hash` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `qr_data` varchar(220) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_guia`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_sunat
-- ----------------------------
INSERT INTO `guia_sunat` VALUES (3, '', '20538381978-09-T001-1128', '20538381978|09|T001-1128|0.00|0.00|2025-10-17|1|77425200');
INSERT INTO `guia_sunat` VALUES (4, '', '20538381978-09-T001-1129', '20538381978|09|T001-1129|0.00|0.00|2025-12-02|6|20601212472');
INSERT INTO `guia_sunat` VALUES (5, '', '20538381978-09-T001-1130', '20538381978|09|T001-1130|0.00|0.00|2025-12-02|6|20601212472');
INSERT INTO `guia_sunat` VALUES (6, '', '20538381978-09-T001-1131', '20538381978|09|T001-1131|0.00|0.00|2025-12-02|6|20601212472');
INSERT INTO `guia_sunat` VALUES (7, '', '20538381978-09-T001-1132', '20538381978|09|T001-1132|0.00|0.00|2025-12-02|6|20601212472');
INSERT INTO `guia_sunat` VALUES (8, '', '20538381978-09-T001-1133', '20538381978|09|T001-1133|0.00|0.00|2025-12-02|6|20601212472');
INSERT INTO `guia_sunat` VALUES (9, '', '20538381978-09-T001-1134', '20538381978|09|T001-1134|0.00|0.00|2025-12-03|6|20601212472');

-- ----------------------------
-- Table structure for guia_vehiculos
-- ----------------------------
DROP TABLE IF EXISTS `guia_vehiculos`;
CREATE TABLE `guia_vehiculos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `placa` varchar(30) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `marca` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `modelo` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of guia_vehiculos
-- ----------------------------
INSERT INTO `guia_vehiculos` VALUES (4, 'Z7B-401', NULL, NULL, '2024-12-18 19:44:04', '2024-12-18 19:44:04');
INSERT INTO `guia_vehiculos` VALUES (5, 'CCM-025', NULL, NULL, '2025-03-27 14:33:32', '2025-03-27 14:33:32');
INSERT INTO `guia_vehiculos` VALUES (6, 'A8G-845', NULL, NULL, '2025-03-27 14:34:06', '2025-03-27 14:34:06');
INSERT INTO `guia_vehiculos` VALUES (7, 'BNX-790', NULL, NULL, '2025-03-27 14:34:15', '2025-03-27 14:34:15');
INSERT INTO `guia_vehiculos` VALUES (8, 'CNI-450', NULL, NULL, '2025-03-27 14:34:23', '2025-03-27 14:34:23');

-- ----------------------------
-- Table structure for historial_stock
-- ----------------------------
DROP TABLE IF EXISTS `historial_stock`;
CREATE TABLE `historial_stock`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `tipo_movimiento` enum('INGRESO','EGRESO') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `cantidad` int NOT NULL,
  `costo_compra` decimal(10, 2) NULL DEFAULT NULL,
  `fecha_movimiento` datetime NOT NULL,
  `usuario` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `observaciones` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `id_orden_trabajo` int NULL DEFAULT NULL,
  `tipo_origen` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'MANUAL',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_producto`(`id_producto` ASC) USING BTREE,
  CONSTRAINT `historial_stock_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of historial_stock
-- ----------------------------
INSERT INTO `historial_stock` VALUES (1, 18, 'INGRESO', 10, NULL, '2025-06-20 12:50:48', 'Sistema', NULL, NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (2, 18, 'INGRESO', 2, NULL, '2025-06-25 09:32:10', 'Sistema', NULL, NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (3, 18, 'INGRESO', 1, NULL, '2025-07-21 10:46:17', 'Sistema', NULL, NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (4, 18, 'INGRESO', 1, NULL, '2025-08-14 10:32:29', 'Administrador', NULL, NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (5, 42, 'INGRESO', 1, NULL, '2025-10-06 17:36:36', 'Administrador', NULL, NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (6, 20, 'EGRESO', 1, NULL, '2025-12-17 19:02:06', 'Sistema', 'Venta F001-2394', NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (7, 117, 'INGRESO', 14, NULL, '2026-01-20 10:36:58', 'Sistema', 'Edición de producto (Stock anterior: -2, Stock nuevo: 12)', NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (8, 117, 'EGRESO', 1, NULL, '2026-01-20 10:41:03', 'Sistema', 'Venta B001-610', NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (9, 341, 'INGRESO', 1, NULL, '2026-01-20 15:23:58', 'Sistema', 'Edición de producto (Stock anterior: 1, Stock nuevo: 2)', NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (10, 117, 'EGRESO', 1, NULL, '2026-01-20 15:24:15', 'Sistema', 'Edición de producto (Stock anterior: 12, Stock nuevo: 11)', NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (11, 341, 'INGRESO', 3, 0.00, '2026-01-20 15:32:45', 'Sistema', 'Producción interna - Orden de Trabajo', 23, 'ORDEN_TRABAJO_INTERNA');
INSERT INTO `historial_stock` VALUES (12, 371, 'INGRESO', 2, 0.00, '2026-01-20 15:32:45', 'Sistema', 'Producción interna - Orden de Trabajo', 23, 'ORDEN_TRABAJO_INTERNA');
INSERT INTO `historial_stock` VALUES (13, 36, 'INGRESO', 11, 0.00, '2026-01-20 15:32:46', 'Sistema', 'Producción interna - Orden de Trabajo', 23, 'ORDEN_TRABAJO_INTERNA');
INSERT INTO `historial_stock` VALUES (14, 341, 'INGRESO', 3, 0.00, '2026-01-20 16:38:41', 'Sistema', 'Producción interna - Orden de Trabajo', 24, 'ORDEN_TRABAJO_INTERNA');
INSERT INTO `historial_stock` VALUES (15, 371, 'INGRESO', 2, 0.00, '2026-01-20 16:38:41', 'Sistema', 'Producción interna - Orden de Trabajo', 24, 'ORDEN_TRABAJO_INTERNA');
INSERT INTO `historial_stock` VALUES (16, 36, 'INGRESO', 11, 0.00, '2026-01-20 16:38:41', 'Sistema', 'Producción interna - Orden de Trabajo', 24, 'ORDEN_TRABAJO_INTERNA');
INSERT INTO `historial_stock` VALUES (17, 117, 'EGRESO', 1, NULL, '2026-03-05 23:06:42', 'Sistema', 'Venta B001-611', NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (18, 117, 'EGRESO', 1, NULL, '2026-03-05 23:07:53', 'Sistema', 'Venta B001-612', NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (21, 373, 'INGRESO', 1, 0.00, '2026-04-08 15:42:28', 'Sistema', 'Ingreso por lote NS interno (producción para stock)', 26, 'LOTE_NS_INTERNO');
INSERT INTO `historial_stock` VALUES (22, 38, 'INGRESO', 1, 0.00, '2026-04-08 16:09:57', 'Sistema', 'Ingreso por lote NS interno (producción para stock)', 27, 'LOTE_NS_INTERNO');
INSERT INTO `historial_stock` VALUES (24, 31, 'EGRESO', 1, NULL, '2026-04-08 16:43:00', 'Sistema', 'Venta B001-613', NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (25, 117, 'EGRESO', 1, NULL, '2026-05-15 08:13:56', 'Sistema', 'Venta B001-614', NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (26, 211, 'EGRESO', 1, NULL, '2026-05-15 08:21:56', 'Sistema', 'Venta B001-615', NULL, 'MANUAL');
INSERT INTO `historial_stock` VALUES (27, 341, 'EGRESO', 2, NULL, '2026-05-27 15:20:04', '40', 'Devolución compra #9', NULL, 'DEVOLUCION');

-- ----------------------------
-- Table structure for historial_stock_repuestos
-- ----------------------------
DROP TABLE IF EXISTS `historial_stock_repuestos`;
CREATE TABLE `historial_stock_repuestos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_repuesto` int NOT NULL,
  `tipo_movimiento` enum('INGRESO','EGRESO') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cantidad` int NOT NULL,
  `costo_compra` decimal(10, 2) NULL DEFAULT NULL,
  `fecha_movimiento` datetime NOT NULL,
  `usuario` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_repuesto`(`id_repuesto` ASC) USING BTREE,
  INDEX `idx_fecha`(`fecha_movimiento` ASC) USING BTREE,
  INDEX `idx_tipo`(`tipo_movimiento` ASC) USING BTREE,
  INDEX `idx_repuesto_fecha`(`id_repuesto` ASC, `fecha_movimiento` DESC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of historial_stock_repuestos
-- ----------------------------

-- ----------------------------
-- Table structure for informe_template
-- ----------------------------
DROP TABLE IF EXISTS `informe_template`;
CREATE TABLE `informe_template`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'INFORME',
  `contenido` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `header_image` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `footer_image` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of informe_template
-- ----------------------------
INSERT INTO `informe_template` VALUES (1, 'INFORME', '<p><strong>Estimados señores:</strong></p><p>Presente.</p><p>Por medio del presente, me permito poner a su consideración el <strong>informe correspondiente</strong>, elaborado con la finalidad de comunicar de manera clara y objetiva la información relevante relacionada al tema evaluado.</p>', 'files/informes/membretes/1775681132_69d6be6ca97f3.jpg', 'files/informes/membretes/1775681132_69d6be6cb90a8.jpg', '2025-05-15 18:15:53', '2026-04-08 15:45:32');

-- ----------------------------
-- Table structure for informes
-- ----------------------------
DROP TABLE IF EXISTS `informes`;
CREATE TABLE `informes`  (
  `id_informe` int NOT NULL AUTO_INCREMENT,
  `tipo` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `titulo` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `contenido` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `imagen1` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `imagen2` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `cliente_id` int NULL DEFAULT NULL,
  `persona_entregar` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `usuario_id` int NULL DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_informe`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of informes
-- ----------------------------
INSERT INTO `informes` VALUES (1, 'INFORME DE MANTENIMIENTO PREVENTIVO', 'INFORME', '<p><strong>Estimados señores:</strong></p><p>Presente.</p><p>Por medio del presente, me permito poner a su consideración el <strong>informe correspondiente</strong>, elaborado con la finalidad de comunicar de manera clara y objetiva la información relevante relacionada al tema evaluado.</p>', 'files/informes/1775681259_69d6beeb01b5f.jpg', 'files/informes/1775681259_69d6beeb3f701.jpg', 34, '', 40, '2026-04-08 15:47:42', '2026-04-08 15:47:42');

-- ----------------------------
-- Table structure for ingreso_egreso
-- ----------------------------
DROP TABLE IF EXISTS `ingreso_egreso`;
CREATE TABLE `ingreso_egreso`  (
  `intercambio_id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NULL DEFAULT NULL,
  `tipo` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cantidad` int NULL DEFAULT NULL,
  `almacen_ingreso` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `almacen_egreso` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_usuario` int NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '2' COMMENT '2 = solo ingreso',
  `observaciones` text CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL,
  `fecha_creacion` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`intercambio_id`) USING BTREE,
  INDEX `id_usuario`(`id_usuario` ASC) USING BTREE,
  INDEX `id_producto`(`id_producto` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ingreso_egreso
-- ----------------------------
INSERT INTO `ingreso_egreso` VALUES (1, 33, 'i', 0, '1', NULL, 40, '2', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (2, 18, 'e', 0, '2', '1', 40, '0', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (3, 18, 'i', 10, '1', NULL, 40, '2', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (4, 33, 'i', 10, '1', NULL, 40, '2', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (5, 33, 'e', 0, '1', '1', 40, '1', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (6, 30, 'i', 11, '1', NULL, 40, '2', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (7, 18, 'e', 0, '2', '1', 40, '0', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (8, 18, 'e', 0, '3', '1', 40, '0', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (9, 18, 'i', 10, '1', NULL, 40, '2', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (10, 18, 'i', 7, '1', NULL, 40, '2', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (11, 18, 'i', 10, '2', NULL, 40, '2', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (12, 18, 'i', 10, '2', NULL, 40, '2', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (13, 18, 'e', 1, '2', '1', 40, '0', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (14, 18, 'i', 1, '1', NULL, 40, '2', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (15, 18, 'e', 1, '2', '1', 40, '1', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (16, 20, 'i', 1, '1', NULL, 40, '2', NULL, '2025-06-25 11:17:45', '2025-06-25 11:17:45');
INSERT INTO `ingreso_egreso` VALUES (17, 18, 'e', 1, '2', '1', 40, '0', 'HOLA', '2025-06-25 11:17:45', '2025-06-25 11:17:45');

-- ----------------------------
-- Table structure for maquina
-- ----------------------------
DROP TABLE IF EXISTS `maquina`;
CREATE TABLE `maquina`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `equipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `marca` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `modelo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `numero_serie` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'DISPONIBLE',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `numero_serie`(`numero_serie` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of maquina
-- ----------------------------
INSERT INTO `maquina` VALUES (5, 'MQ-01', 'LUSTRADORA', 'TAURO', 'TD-12N', '123456', 'NO DISPONIBLE');
INSERT INTO `maquina` VALUES (6, 'MQ-02', 'ASPIRADORA', 'MASTER GOLDS', 'AMG-15L', '159123', 'NO DISPONIBLE');
INSERT INTO `maquina` VALUES (7, 'MQ-03', 'LUSTRADORA', 'MASTER GOLDS', 'AMG-15L', '1549416', 'DISPONIBLE');

-- ----------------------------
-- Table structure for marcas
-- ----------------------------
DROP TABLE IF EXISTS `marcas`;
CREATE TABLE `marcas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of marcas
-- ----------------------------
INSERT INTO `marcas` VALUES (1, 'CRIS-TAURO');
INSERT INTO `marcas` VALUES (2, 'MASTER GOLDS');
INSERT INTO `marcas` VALUES (6, 'SPEED POWER');
INSERT INTO `marcas` VALUES (7, 'TENNAN');

-- ----------------------------
-- Table structure for mes
-- ----------------------------
DROP TABLE IF EXISTS `mes`;
CREATE TABLE `mes`  (
  `id` int NOT NULL,
  `nombre` varchar(12) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of mes
-- ----------------------------
INSERT INTO `mes` VALUES (1, 'Ene');
INSERT INTO `mes` VALUES (2, 'Feb');
INSERT INTO `mes` VALUES (3, 'Mar');
INSERT INTO `mes` VALUES (4, 'Abr');
INSERT INTO `mes` VALUES (5, 'May');
INSERT INTO `mes` VALUES (6, 'Jun');
INSERT INTO `mes` VALUES (7, 'Jul');
INSERT INTO `mes` VALUES (8, 'Ago');
INSERT INTO `mes` VALUES (9, 'Set');
INSERT INTO `mes` VALUES (10, 'Oct');
INSERT INTO `mes` VALUES (11, 'Nov');
INSERT INTO `mes` VALUES (12, 'Dic');

-- ----------------------------
-- Table structure for metas_empresa
-- ----------------------------
DROP TABLE IF EXISTS `metas_empresa`;
CREATE TABLE `metas_empresa`  (
  `id_meta_empresa` int NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `meta_total` decimal(10, 2) NOT NULL,
  `mes` int NOT NULL,
  `anio` int NOT NULL,
  `fecha_creacion` datetime NOT NULL,
  `fecha_actualizacion` datetime NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  PRIMARY KEY (`id_meta_empresa`) USING BTREE,
  INDEX `idx_empresa_mes_anio`(`id_empresa` ASC, `mes` ASC, `anio` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of metas_empresa
-- ----------------------------
INSERT INTO `metas_empresa` VALUES (1, 12, 40000.00, 5, 2025, '2025-05-26 17:31:02', '2025-05-26 17:31:13', '1');
INSERT INTO `metas_empresa` VALUES (2, 12, 80000.00, 6, 2025, '2025-06-01 18:10:55', '2025-06-04 13:46:40', '1');
INSERT INTO `metas_empresa` VALUES (3, 12, 23222.00, 5, 2026, '2026-05-27 13:59:23', '2026-05-27 14:05:34', '1');

-- ----------------------------
-- Table structure for metas_vendedores
-- ----------------------------
DROP TABLE IF EXISTS `metas_vendedores`;
CREATE TABLE `metas_vendedores`  (
  `id_meta_vendedor` int NOT NULL AUTO_INCREMENT,
  `id_empresa` int NOT NULL,
  `usuario_id` int NOT NULL,
  `meta_individual` decimal(10, 2) NOT NULL,
  `mes` int NOT NULL,
  `anio` int NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` datetime NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  PRIMARY KEY (`id_meta_vendedor`) USING BTREE,
  INDEX `idx_empresa_vendedor_periodo`(`id_empresa` ASC, `usuario_id` ASC, `mes` ASC, `anio` ASC) USING BTREE,
  INDEX `idx_vendedor`(`usuario_id` ASC) USING BTREE,
  CONSTRAINT `fk_metas_vendedores_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of metas_vendedores
-- ----------------------------

-- ----------------------------
-- Table structure for metodo_pago
-- ----------------------------
DROP TABLE IF EXISTS `metodo_pago`;
CREATE TABLE `metodo_pago`  (
  `id_metodo_pago` int NOT NULL,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  PRIMARY KEY (`id_metodo_pago`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of metodo_pago
-- ----------------------------
INSERT INTO `metodo_pago` VALUES (1, 'TRANSFERENCIA BANCO BCP', '1');
INSERT INTO `metodo_pago` VALUES (2, 'TRANSFERENCIA BANCO NACION', '1');
INSERT INTO `metodo_pago` VALUES (3, 'TRANSFERENCIA BANCO INTERBANK', '1');
INSERT INTO `metodo_pago` VALUES (4, 'TRANSFERENCIA BANCO BBVA', '1');
INSERT INTO `metodo_pago` VALUES (5, 'YAPE', '1');
INSERT INTO `metodo_pago` VALUES (6, 'PLIN', '1');
INSERT INTO `metodo_pago` VALUES (7, 'TARJETA DE CREDITO VISA', '0');
INSERT INTO `metodo_pago` VALUES (8, 'TARJETA DE CREDITO MASTERCARD', '0');
INSERT INTO `metodo_pago` VALUES (9, 'TARJETA DE CREDITO DINNERS CLUB', '0');
INSERT INTO `metodo_pago` VALUES (10, 'POS ', '1');
INSERT INTO `metodo_pago` VALUES (11, 'TRANSFERENCIA BANCO SCOTIABANK', '1');
INSERT INTO `metodo_pago` VALUES (12, 'EFECTIVO', '1');

-- ----------------------------
-- Table structure for modelos
-- ----------------------------
DROP TABLE IF EXISTS `modelos`;
CREATE TABLE `modelos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `marca_id` int NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of modelos
-- ----------------------------
INSERT INTO `modelos` VALUES (8, 'AG-06', 2);
INSERT INTO `modelos` VALUES (10, 'AG-08	', 6);
INSERT INTO `modelos` VALUES (11, 'ASJ12', 1);

-- ----------------------------
-- Table structure for motivo
-- ----------------------------
DROP TABLE IF EXISTS `motivo`;
CREATE TABLE `motivo`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of motivo
-- ----------------------------
INSERT INTO `motivo` VALUES (1, 'Prestamos');
INSERT INTO `motivo` VALUES (2, 'Remplazo');
INSERT INTO `motivo` VALUES (3, 'Alquiler');

-- ----------------------------
-- Table structure for motivo_documento
-- ----------------------------
DROP TABLE IF EXISTS `motivo_documento`;
CREATE TABLE `motivo_documento`  (
  `id_motivo` int NOT NULL,
  `codigo` varchar(10) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombre` varchar(145) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_tido` int NOT NULL,
  PRIMARY KEY (`id_motivo`) USING BTREE,
  INDEX `fk_motivo_documento_documentos_sunat1_idx`(`id_tido` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of motivo_documento
-- ----------------------------
INSERT INTO `motivo_documento` VALUES (1, '01', 'Anulación de la operacion', 3);
INSERT INTO `motivo_documento` VALUES (2, '02', 'Anulación por error en el RUC', 3);
INSERT INTO `motivo_documento` VALUES (3, '03', 'Corrección por error en la descripción', 3);
INSERT INTO `motivo_documento` VALUES (4, '10', 'Otros Conceptos', 3);
INSERT INTO `motivo_documento` VALUES (5, '01', 'Intereses por mora', 4);
INSERT INTO `motivo_documento` VALUES (6, '02', 'Aumento en el valor', 4);
INSERT INTO `motivo_documento` VALUES (7, '03', 'Penalidades/ otros conceptos', 4);

-- ----------------------------
-- Table structure for motivos_guia
-- ----------------------------
DROP TABLE IF EXISTS `motivos_guia`;
CREATE TABLE `motivos_guia`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `es_defecto` tinyint(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of motivos_guia
-- ----------------------------
INSERT INTO `motivos_guia` VALUES (1, 'VENTA', '2025-06-21 10:57:20', '2025-06-21 10:57:46', 1);
INSERT INTO `motivos_guia` VALUES (2, 'TRASLADO', '2025-06-21 10:57:20', '2025-06-21 10:57:20', 0);
INSERT INTO `motivos_guia` VALUES (3, 'RECOJO', '2025-06-21 10:57:20', '2025-06-21 10:57:46', 0);
INSERT INTO `motivos_guia` VALUES (4, 'DEVOLUCIÓN', '2025-06-21 10:57:20', '2025-06-21 10:57:38', 0);

-- ----------------------------
-- Table structure for notas_electronicas
-- ----------------------------
DROP TABLE IF EXISTS `notas_electronicas`;
CREATE TABLE `notas_electronicas`  (
  `nota_id` int NOT NULL,
  `id_venta` int NULL DEFAULT NULL,
  `id_empresa` int NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `tido` int NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `serie` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `motivo` int NULL DEFAULT NULL,
  `monto` double(15, 2) NULL DEFAULT NULL,
  `productos` longtext CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `estado_sunat` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '0',
  `estado` char(1) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT '1',
  PRIMARY KEY (`nota_id`) USING BTREE,
  INDEX `tido`(`tido` ASC) USING BTREE,
  INDEX `id_venta`(`id_venta` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of notas_electronicas
-- ----------------------------
INSERT INTO `notas_electronicas` VALUES (0, 5, 12, 1, 3, '2025-04-28', 'F001', 12, 3, 3658.00, '[{\"productoid\":\"\",\"descripcion\":\"LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 12\\\" - MARCA: CRIS-TAURO \",\"cantidad\":1,\"precio\":3658,\"codigo\":\"\",\"costo\":\"\"}]', '0', '1');
INSERT INTO `notas_electronicas` VALUES (1, 8, 12, 1, 3, '2024-08-03', 'F001', 6, 1, 192.00, '[{\"productoid\":\"\",\"descripcion\":\"kuatitos\",\"cantidad\":\"1\",\"precio\":\"192\",\"codigo\":\"\",\"costo\":\"\"}]', '0', '1');

-- ----------------------------
-- Table structure for notas_electronicas_sunat
-- ----------------------------
DROP TABLE IF EXISTS `notas_electronicas_sunat`;
CREATE TABLE `notas_electronicas_sunat`  (
  `id_notas_electronicas` int NOT NULL,
  `hash` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `qr_data` varchar(220) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_notas_electronicas`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of notas_electronicas_sunat
-- ----------------------------
INSERT INTO `notas_electronicas_sunat` VALUES (0, 'CWk0mb9Jh88O1xTUKF6lZrWUjbo=', '20603319274-07-F001-6', '20603319274|07|F001-6|29.29|29.29|2024-08-03|0|00000000');

-- ----------------------------
-- Table structure for numero_series
-- ----------------------------
DROP TABLE IF EXISTS `numero_series`;
CREATE TABLE `numero_series`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `numero` int NOT NULL DEFAULT 0,
  `cliente_ruc_dni` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `cliente_documento` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `fecha_creacion` date NULL DEFAULT NULL,
  `cantidad_equipos` int NOT NULL,
  `tipo_maquina` enum('fabricada','importada') CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'fabricada',
  `tiene_cliente` tinyint(1) NULL DEFAULT 1 COMMENT '1=con cliente, 0=sin cliente',
  `estado_lote` enum('borrador','completado','anulado') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'borrador',
  `fecha_completado` datetime NULL DEFAULT NULL,
  `convertido_de_externo` tinyint(1) NOT NULL DEFAULT 0,
  `usuario_completo` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_numero`(`numero` ASC) USING BTREE,
  INDEX `idx_numero_series_estado`(`estado_lote` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 28 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of numero_series
-- ----------------------------
INSERT INTO `numero_series` VALUES (18, 2, NULL, NULL, '2026-01-09', 9, 'fabricada', 0, 'borrador', NULL, 0, NULL);
INSERT INTO `numero_series` VALUES (20, 3, 'EMER RODRIGO YARLEQUE ZAPATA', '77425200', '2026-01-20', 2, 'fabricada', 1, 'borrador', NULL, 0, NULL);
INSERT INTO `numero_series` VALUES (21, 4, 'EMER RODRIGO YARLEQUE ZAPATA', '77425200', '2026-01-20', 2, 'fabricada', 1, 'borrador', NULL, 0, NULL);
INSERT INTO `numero_series` VALUES (22, 5, NULL, NULL, '2026-01-20', 4, 'fabricada', 0, 'borrador', NULL, 0, NULL);
INSERT INTO `numero_series` VALUES (23, 6, NULL, NULL, '2026-01-20', 3, 'fabricada', 0, 'borrador', NULL, 0, NULL);
INSERT INTO `numero_series` VALUES (24, 7, 'EMER RODRIGO YARLEQUE ZAPATA', '77425200', '2026-04-08', 1, 'fabricada', 1, 'borrador', NULL, 0, NULL);
INSERT INTO `numero_series` VALUES (25, 8, NULL, NULL, '2026-04-08', 1, 'fabricada', 0, 'borrador', NULL, 0, NULL);
INSERT INTO `numero_series` VALUES (26, 9, NULL, NULL, '2026-04-08', 1, 'fabricada', 0, 'completado', '2026-04-08 15:42:28', 0, 'Sistema');
INSERT INTO `numero_series` VALUES (27, 10, NULL, NULL, '2026-04-08', 1, 'fabricada', 0, 'completado', '2026-04-08 16:09:57', 0, 'Sistema');

-- ----------------------------
-- Table structure for observacion
-- ----------------------------
DROP TABLE IF EXISTS `observacion`;
CREATE TABLE `observacion`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `detalle` text CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of observacion
-- ----------------------------
INSERT INTO `observacion` VALUES (1, '• Los productos deben entregarse en perfecto estado y con su embalaje original.\n• El proveedor debe cumplir con los plazos de entrega establecidos.\n• Cualquier producto defectuoso será devuelto y deberá ser reemplazado sin costo adicional.\n• La factura debe incluir el número de orden de compra como referencia.\n• El pago se realizará según los términos acordados una vez verificada la mercancía.\n• El proveedor debe proporcionar garantía para todos los productos suministrados.\n• Los precios acordados no pueden ser modificados sin previo aviso por escrito.\n');

-- ----------------------------
-- Table structure for observaciones_compra
-- ----------------------------
DROP TABLE IF EXISTS `observaciones_compra`;
CREATE TABLE `observaciones_compra`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_compra` int NOT NULL,
  `observaciones` text CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_compra`(`id_compra` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of observaciones_compra
-- ----------------------------
INSERT INTO `observaciones_compra` VALUES (1, 4, '• Los productos deben entregarse en perfecto estado y con su embalaje original.\n• El proveedor debe cumplir con los plazos<span style=\"color: rgb(255, 194, 102);\"> de entrega establecidos</span><span style=\"color: rgb(255, 255, 102);\">.</span>\n• Cualquier producto defectuoso será devuelto y deberá ser reemplazado sin costo adicional.\n• La factura debe incluir el número de orden de compra como referencia.\n• El pago se realizará según los términos acordados una vez verificada la mercancía.\n• El proveedor debe proporcionar garantía para todos los productos suministrados.\n• Los precios acordados no pueden ser modificados sin previo aviso por escrito.\n');
INSERT INTO `observaciones_compra` VALUES (2, 5, '• <span style=\"background-color: rgb(255, 255, 0);\">• Los productos deben entregarse en perfecto estado y con su embalaje original.</span>\n• El proveedor debe cumplir con los plazos de entrega establecidos.\n• Cualquier producto defectuoso será devuelto y deberá ser reemplazado sin costo adicional.\n• La factura debe incluir el número de orden de compra como referencia.\n• El pago se realizará según los términos acordados una vez verificada la mercancía.\n• El proveedor debe proporcionar garantía para todos los productos suministrados.\n• Los precios acordados no pueden ser modificados sin previo aviso por escrito.\n');

-- ----------------------------
-- Table structure for orden_servicio_detalles
-- ----------------------------
DROP TABLE IF EXISTS `orden_servicio_detalles`;
CREATE TABLE `orden_servicio_detalles`  (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_orden_servicio` int NOT NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `equipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `modelo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_detalle`) USING BTREE,
  INDEX `fk_orden_servicio`(`id_orden_servicio` ASC) USING BTREE,
  CONSTRAINT `fk_orden_servicio_detalles` FOREIGN KEY (`id_orden_servicio`) REFERENCES `orden_servicio_pre` (`id_orden_servicio`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 15 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of orden_servicio_detalles
-- ----------------------------
INSERT INTO `orden_servicio_detalles` VALUES (13, 12, 'CRIS-TAURO', 'ASPIRADORA', 'AG-06', '1234');
INSERT INTO `orden_servicio_detalles` VALUES (14, 12, 'MASTER GOLDS', 'LUSTRADORA', 'ASJ12', '12345');

-- ----------------------------
-- Table structure for orden_servicio_pre
-- ----------------------------
DROP TABLE IF EXISTS `orden_servicio_pre`;
CREATE TABLE `orden_servicio_pre`  (
  `id_orden_servicio` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cliente_razon_social` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cliente_ruc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `atencion_encargado` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fecha_ingreso` date NULL DEFAULT NULL,
  `tiene_cotizacion` tinyint(1) NULL DEFAULT 0,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'PENDIENTE',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_orden_servicio`) USING BTREE,
  INDEX `idx_cliente_ruc`(`cliente_ruc` ASC) USING BTREE,
  INDEX `idx_fecha_ingreso`(`fecha_ingreso` ASC) USING BTREE,
  INDEX `idx_estado`(`estado` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of orden_servicio_pre
-- ----------------------------
INSERT INTO `orden_servicio_pre` VALUES (12, 'OS-01', 'LIM KIT CORPORACION E.I.R.L.', '20601212472', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', 'EDUARDO', '2025-12-03', 1, 'PENDIENTE', 'MOTOR NO ENCIENDE', '2025-12-03 16:16:04', '2025-12-03 16:28:52');

-- ----------------------------
-- Table structure for orden_trabajo_detalles
-- ----------------------------
DROP TABLE IF EXISTS `orden_trabajo_detalles`;
CREATE TABLE `orden_trabajo_detalles`  (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_orden_trabajo` int NOT NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `equipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `modelo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_detalle`) USING BTREE,
  INDEX `fk_orden_trabajo`(`id_orden_trabajo` ASC) USING BTREE,
  CONSTRAINT `fk_orden_trabajo_detalles` FOREIGN KEY (`id_orden_trabajo`) REFERENCES `orden_trabajo_pre` (`id_orden_trabajo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 81 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of orden_trabajo_detalles
-- ----------------------------
INSERT INTO `orden_trabajo_detalles` VALUES (59, 18, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12313');
INSERT INTO `orden_trabajo_detalles` VALUES (60, 18, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12314');
INSERT INTO `orden_trabajo_detalles` VALUES (61, 18, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12315');
INSERT INTO `orden_trabajo_detalles` VALUES (62, 18, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12316');
INSERT INTO `orden_trabajo_detalles` VALUES (63, 18, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12317');
INSERT INTO `orden_trabajo_detalles` VALUES (64, 18, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12318');
INSERT INTO `orden_trabajo_detalles` VALUES (65, 18, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12319');
INSERT INTO `orden_trabajo_detalles` VALUES (66, 18, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12320');
INSERT INTO `orden_trabajo_detalles` VALUES (67, 18, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12321');
INSERT INTO `orden_trabajo_detalles` VALUES (68, 19, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12326');
INSERT INTO `orden_trabajo_detalles` VALUES (69, 19, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12327');
INSERT INTO `orden_trabajo_detalles` VALUES (70, 19, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12328');
INSERT INTO `orden_trabajo_detalles` VALUES (71, 19, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12329');
INSERT INTO `orden_trabajo_detalles` VALUES (72, 20, 'CRIS-TAURO', 'LUSTRADORA', 'AG-08', '12330');
INSERT INTO `orden_trabajo_detalles` VALUES (73, 20, 'MASTER GOLDS', 'ASPIRADORA', 'AG-08', '12331');
INSERT INTO `orden_trabajo_detalles` VALUES (74, 20, 'SPEED POWER', 'LAVADORA DE ALFOMBRAS', 'ASJ12', '12332');
INSERT INTO `orden_trabajo_detalles` VALUES (75, 21, 'TENNAN', 'LAVA BUTACAS', 'AG-08', '12324');
INSERT INTO `orden_trabajo_detalles` VALUES (76, 21, 'SPEED POWER', 'LAVADORA DE ALFOMBRAS', 'AG-08', '12325');
INSERT INTO `orden_trabajo_detalles` VALUES (77, 22, 'CRIS-TAURO', 'LUSTRADORA', 'AG-06', '12322');
INSERT INTO `orden_trabajo_detalles` VALUES (78, 22, 'SPEED POWER', 'LAVADORA DE ALFOMBRAS', 'AG-08', '12323');

-- ----------------------------
-- Table structure for orden_trabajo_pre
-- ----------------------------
DROP TABLE IF EXISTS `orden_trabajo_pre`;
CREATE TABLE `orden_trabajo_pre`  (
  `id_orden_trabajo` int NOT NULL AUTO_INCREMENT,
  `numero` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cliente_razon_social` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `cliente_ruc` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `atencion_encargado` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fecha_ingreso` date NULL DEFAULT NULL,
  `fecha_salida` date NULL DEFAULT NULL,
  `tiene_cotizacion` tinyint(1) NULL DEFAULT 0,
  `estado` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'PENDIENTE',
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_orden_trabajo`) USING BTREE,
  INDEX `idx_cliente_ruc`(`cliente_ruc` ASC) USING BTREE,
  INDEX `idx_fecha_ingreso`(`fecha_ingreso` ASC) USING BTREE,
  INDEX `idx_estado`(`estado` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of orden_trabajo_pre
-- ----------------------------
INSERT INTO `orden_trabajo_pre` VALUES (18, 'OT-01', 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.', '20538381978', NULL, 'EDUARDO', '2026-01-09', '2026-01-16', 0, 'CULMINADO', '', '2026-01-09 13:48:09', '2026-01-09 14:21:56');
INSERT INTO `orden_trabajo_pre` VALUES (19, 'OT-02', 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.', '20538381978', NULL, 'EDUARDO', '2026-01-20', '2026-01-27', 1, 'PENDIENTE', '', '2026-01-20 14:54:29', '2026-01-20 15:07:26');
INSERT INTO `orden_trabajo_pre` VALUES (20, 'OT-03', 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.', '20538381978', NULL, 'EDUARDO', '2026-01-20', '2026-01-27', 1, 'PENDIENTE', '', '2026-01-20 15:25:38', '2026-01-20 15:32:46');
INSERT INTO `orden_trabajo_pre` VALUES (21, 'OT-04', 'EMER RODRIGO YARLEQUE ZAPATA', '77425200', NULL, 'EDUARDO', '2026-01-20', '2026-01-27', 0, 'PENDIENTE', '', '2026-01-20 16:31:09', '2026-01-20 16:31:09');
INSERT INTO `orden_trabajo_pre` VALUES (22, 'OT-05', 'EMER RODRIGO YARLEQUE ZAPATA', '77425200', NULL, 'EDUARDO', '2026-04-08', '2026-04-15', 0, 'PENDIENTE', '', '2026-04-08 14:32:57', '2026-04-08 14:32:57');

-- ----------------------------
-- Table structure for orden_trabajo_repuestos
-- ----------------------------
DROP TABLE IF EXISTS `orden_trabajo_repuestos`;
CREATE TABLE `orden_trabajo_repuestos`  (
  `id_repuesto_orden` int NOT NULL AUTO_INCREMENT,
  `id_orden_trabajo` int NOT NULL,
  `id_detalle_maquina` int NOT NULL,
  `tipo_item` enum('producto','repuesto') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `id_producto` int NULL DEFAULT NULL,
  `id_repuesto` int NULL DEFAULT NULL,
  `cantidad` int NOT NULL,
  `precio_unitario` decimal(10, 2) NOT NULL,
  `precio_total` decimal(10, 2) NOT NULL,
  `fecha_agregado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_repuesto_orden`) USING BTREE,
  INDEX `idx_orden_trabajo_repuestos_orden`(`id_orden_trabajo` ASC) USING BTREE,
  INDEX `idx_orden_trabajo_repuestos_detalle`(`id_detalle_maquina` ASC) USING BTREE,
  INDEX `idx_orden_trabajo_repuestos_producto`(`id_producto` ASC) USING BTREE,
  INDEX `idx_orden_trabajo_repuestos_repuesto`(`id_repuesto` ASC) USING BTREE,
  CONSTRAINT `orden_trabajo_repuestos_ibfk_1` FOREIGN KEY (`id_orden_trabajo`) REFERENCES `orden_trabajo_pre` (`id_orden_trabajo`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `orden_trabajo_repuestos_ibfk_2` FOREIGN KEY (`id_detalle_maquina`) REFERENCES `orden_trabajo_detalles` (`id_detalle`) ON DELETE CASCADE ON UPDATE RESTRICT,
  CONSTRAINT `orden_trabajo_repuestos_ibfk_3` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `orden_trabajo_repuestos_ibfk_4` FOREIGN KEY (`id_repuesto`) REFERENCES `repuestos` (`id_repuesto`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 21 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of orden_trabajo_repuestos
-- ----------------------------
INSERT INTO `orden_trabajo_repuestos` VALUES (5, 18, 59, 'producto', 117, NULL, 1, 3658.00, 3658.00, '2026-01-09 14:30:11');
INSERT INTO `orden_trabajo_repuestos` VALUES (6, 18, 60, 'producto', 341, NULL, 1, 3958.00, 3958.00, '2026-01-09 14:30:17');
INSERT INTO `orden_trabajo_repuestos` VALUES (7, 18, 61, 'producto', 341, NULL, 1, 3958.00, 3958.00, '2026-01-09 14:30:23');
INSERT INTO `orden_trabajo_repuestos` VALUES (8, 18, 62, 'producto', 21, NULL, 1, 4130.00, 4130.00, '2026-01-09 14:30:37');
INSERT INTO `orden_trabajo_repuestos` VALUES (9, 18, 63, 'producto', 34, NULL, 1, 2596.00, 2596.00, '2026-01-09 14:30:43');
INSERT INTO `orden_trabajo_repuestos` VALUES (10, 18, 64, 'producto', 41, NULL, 1, 6490.00, 6490.00, '2026-01-09 14:30:50');
INSERT INTO `orden_trabajo_repuestos` VALUES (11, 18, 65, 'producto', 52, NULL, 1, 2891.00, 2891.00, '2026-01-09 14:30:56');
INSERT INTO `orden_trabajo_repuestos` VALUES (12, 18, 66, 'producto', 195, NULL, 1, 7552.00, 7552.00, '2026-01-09 14:31:02');
INSERT INTO `orden_trabajo_repuestos` VALUES (13, 18, 67, 'producto', 48, NULL, 1, 1298.00, 1298.00, '2026-01-09 14:31:11');
INSERT INTO `orden_trabajo_repuestos` VALUES (14, 19, 68, 'producto', 117, NULL, 1, 3658.00, 3658.00, '2026-01-20 14:55:24');
INSERT INTO `orden_trabajo_repuestos` VALUES (15, 19, 69, 'producto', 341, NULL, 1, 3958.00, 3958.00, '2026-01-20 14:55:31');
INSERT INTO `orden_trabajo_repuestos` VALUES (16, 19, 70, 'producto', 20, NULL, 1, 3894.00, 3894.00, '2026-01-20 14:55:38');
INSERT INTO `orden_trabajo_repuestos` VALUES (17, 19, 71, 'producto', 21, NULL, 1, 4130.00, 4130.00, '2026-01-20 14:55:46');
INSERT INTO `orden_trabajo_repuestos` VALUES (18, 20, 72, 'producto', 341, NULL, 3, 3958.00, 11874.00, '2026-01-20 15:25:50');
INSERT INTO `orden_trabajo_repuestos` VALUES (19, 20, 73, 'producto', 371, NULL, 2, 4194.00, 8388.00, '2026-01-20 15:26:00');
INSERT INTO `orden_trabajo_repuestos` VALUES (20, 20, 74, 'producto', 36, NULL, 11, 3422.00, 37642.00, '2026-01-20 15:26:10');

-- ----------------------------
-- Table structure for otros_archivos
-- ----------------------------
DROP TABLE IF EXISTS `otros_archivos`;
CREATE TABLE `otros_archivos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo: CATALOGO, CERTIFICADO, MANUAL, etc.',
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT 'Motivo o descripción del archivo',
  `id_cliente` int NULL DEFAULT NULL,
  `usuario_id` int NULL DEFAULT NULL,
  `contenido` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Contenido HTML del documento (cuando es creado)',
  `header_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de cabecera en base64',
  `footer_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de pie en base64',
  `imagen1` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `imagen2` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `imagen3` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `estado` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT 'activo',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `cliente_id`(`id_cliente` ASC) USING BTREE,
  INDEX `usuario_id`(`usuario_id` ASC) USING BTREE,
  CONSTRAINT `otros_archivos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id_cliente`) ON DELETE SET NULL ON UPDATE RESTRICT,
  CONSTRAINT `otros_archivos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`usuario_id`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of otros_archivos
-- ----------------------------
INSERT INTO `otros_archivos` VALUES (12, 'sdfavdfsavfds', 'ACTA', NULL, 34, 40, '<h2 style=\"text-align: center;\">DOCUMENTO</h2><p><br></p><p>Fecha: [FECHA]</p><p>Asunto: [ASUNTO]</p><p><br></p><p>Contenido del documento...</p><p><br></p><p>Atentamente,</p><p><br></p><p>[NOMBRE]</p><p>[CARGO]</p>', NULL, NULL, 'files/otroArchivos/1779908179_6a173e5329a98.jpg', 'files/otroArchivos/1779908179_6a173e5348e76.jpg', 'files/otroArchivos/1779908179_6a173e539c52e.jpg', 'borrador', '2026-05-27 13:56:19', '2026-05-27 13:56:19');

-- ----------------------------
-- Table structure for otros_archivos_plantillas
-- ----------------------------
DROP TABLE IF EXISTS `otros_archivos_plantillas`;
CREATE TABLE `otros_archivos_plantillas`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contenido` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de cabecera en base64',
  `footer_image` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL COMMENT 'Imagen de pie en base64',
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of otros_archivos_plantillas
-- ----------------------------
INSERT INTO `otros_archivos_plantillas` VALUES (1, 'Plantilla de Documento Predeterminada', '<h2 style=\"text-align: center;\">DOCUMENTO</h2><p><br></p><p>Fecha: [FECHA]</p><p>Asunto: [ASUNTO]</p><p><br></p><p>Contenido del documento...</p><p><br></p><p>Atentamente,</p><p><br></p><p>[NOMBRE]</p><p>[CARGO]</p>', NULL, NULL, '2025-05-12 15:47:00', '2025-05-12 15:47:00');

-- ----------------------------
-- Table structure for pre_alerta
-- ----------------------------
DROP TABLE IF EXISTS `pre_alerta`;
CREATE TABLE `pre_alerta`  (
  `id_preAlerta` int NOT NULL AUTO_INCREMENT,
  `cliente_razon_social` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `cliente_ruc` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `direccion` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `atencion_encargado` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `fecha_ingreso` date NULL DEFAULT NULL,
  `origen` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `tiene_cotizacion` tinyint(1) NULL DEFAULT 0,
  `estado` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'PENDIENTE',
  `observaciones` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  PRIMARY KEY (`id_preAlerta`) USING BTREE,
  INDEX `idx_id_preAlerta`(`id_preAlerta` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of pre_alerta
-- ----------------------------

-- ----------------------------
-- Table structure for pre_alerta_detalles
-- ----------------------------
DROP TABLE IF EXISTS `pre_alerta_detalles`;
CREATE TABLE `pre_alerta_detalles`  (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_preAlerta` int NOT NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `equipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `modelo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_detalle`) USING BTREE,
  INDEX `id_preAlerta`(`id_preAlerta` ASC) USING BTREE,
  CONSTRAINT `pre_alerta_detalles_ibfk_1` FOREIGN KEY (`id_preAlerta`) REFERENCES `pre_alerta` (`id_preAlerta`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 78 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of pre_alerta_detalles
-- ----------------------------

-- ----------------------------
-- Table structure for producto_precios
-- ----------------------------
DROP TABLE IF EXISTS `producto_precios`;
CREATE TABLE `producto_precios`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `precio` double(10, 2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_producto_precios_productos`(`id_producto` ASC) USING BTREE,
  CONSTRAINT `fk_producto_precios_productos` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_producto`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 30 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of producto_precios
-- ----------------------------
INSERT INTO `producto_precios` VALUES (12, 45, 'EDUARDO', 800.00);
INSERT INTO `producto_precios` VALUES (14, 43, 'EDUARDO', 700.00);
INSERT INTO `producto_precios` VALUES (15, 46, 'EDUARDO', 750.00);
INSERT INTO `producto_precios` VALUES (16, 42, 'EDUARDO', 3000.00);
INSERT INTO `producto_precios` VALUES (23, 18, 'NG1', 2700.00);
INSERT INTO `producto_precios` VALUES (24, 18, 'EDU1', 2600.00);
INSERT INTO `producto_precios` VALUES (27, 44, 'EDUARDO', 500.00);
INSERT INTO `producto_precios` VALUES (28, 20, 'NG1', 2900.00);
INSERT INTO `producto_precios` VALUES (29, 20, 'EDU1', 2800.00);

-- ----------------------------
-- Table structure for productos
-- ----------------------------
DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos`  (
  `id_producto` int NOT NULL AUTO_INCREMENT,
  `cod_barra` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `precio` double(10, 2) NULL DEFAULT NULL,
  `costo` double(10, 2) NULL DEFAULT NULL,
  `cantidad` int NULL DEFAULT NULL,
  `iscbp` int NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `ultima_salida` date NOT NULL,
  `codsunat` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `usar_barra` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '0',
  `usar_multiprecio` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '0',
  `precio_mayor` double(10, 2) NULL DEFAULT NULL,
  `precio_menor` double(10, 2) NULL DEFAULT NULL,
  `razon_social` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ruc` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  `almacen` int NULL DEFAULT 1,
  `precio2` double(10, 2) NULL DEFAULT 0.00,
  `precio3` double(10, 2) NULL DEFAULT 0.00,
  `precio4` double(10, 2) NULL DEFAULT 0.00,
  `precio_unidad` double(10, 2) NULL DEFAULT NULL,
  `codigo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `detalle` text CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL,
  `categoria` int NULL DEFAULT NULL,
  `descripcion` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL,
  `unidad` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `moneda` enum('PEN','USD') CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT 'PEN',
  `fecha_registro` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_ultimo_ingreso` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id_producto`) USING BTREE,
  INDEX `fk_productos_empresas1_idx`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 376 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of productos
-- ----------------------------
INSERT INTO `productos` VALUES (15, '', 'prueba', 0.00, 1.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 12.00, 2.00, 'LABORATORIOS CLINICOS MULTIPLES S.A.C.', '20554454276', '0', 1, 0.00, 0.00, 0.00, 1.00, '123', NULL, 'Modelo: TD-12N Potencia de motor: 1.5 HP', NULL, 'Modelo: TD-12N Potencia de motor: 1.5 HP', NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (16, '', 'dsadsad', 12.00, 12.00, 0, 0, 12, 1, '1000-01-01', '12', '0', '0', 12.00, 12.00, 'LABORATORIOS CLINICOS MULTIPLES S.A.C.', '20554454276', '0', 1, 12.00, 21.00, 12.00, 12.00, 'asdas', NULL, 'sdadsad', 1, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (17, '', 'prueba', 0.00, 1.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 12.00, 2.00, 'LABORATORIOS CLINICOS MULTIPLES S.A.C.', '20554454276', '0', 1, 0.00, 0.00, 0.00, 1.00, '123', NULL, 'Modelo: TD-12N Potencia de motor: 1.5 HP', 2, 'Modelo: TD-12N Potencia de motor: 1.5 HP', NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (18, 'JVC-001', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 12\" - MARCA: CRIS-TAURO', 3658.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '1', 3292.20, 3109.30, '1', '1', '0', 1, 3109.30, 0.00, 0.00, 3658.00, 'JVC-001', '1754351623_FOTOS MAQUINAS JVC LIMPIAS (3).png', 'Modelo: TD-12N \nPotencia de motor: 1.5 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotaci├│n: 175 RPM. \nMotor: KDS del Grupo Imperial. \nEstructura en Acero Inoxidable Anticorrosivo.\nBase de Motor en Aluminio Fundido anticorrosivo.\nPlato en Acero Inoxidable (calidad 304) de 12\".\nCable Vulcanizado Homologado de 3x14: 15 metros.\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\".', 5, 'Modelo: TD-12N Potencia de motor: 1.5 HP Voltaje / Frecuencia: 220 V/60 Hz. Velocidad de Rotaci├│n: 175 RPM. Motor: KDS del Grupo Imperial Estructura en Acero Inoxidable Anticorrosivo Base de Motor en Aluminio Fundido anticorrosivo Plato en Acero Inoxidabl', '14', 'PEN', '2025-06-10 17:07:44', '2025-08-14 10:32:29');
INSERT INTO `productos` VALUES (19, NULL, 'prueba', 0.00, 1.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 1.00, 1.00, '', '', '0', 1, 0.00, 0.00, 0.00, 1.00, '123', NULL, 'Modelo: TD-12N Potencia de motor: 1.5 HP', 1, 'Modelo: TD-12N Potencia de motor: 1.5 HP', NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (20, 'JVC-002', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 14\" - MARCA: CRIS-TAURO', 3894.00, 0.00, 1, 0, 12, 1, '1000-01-01', '0', '1', '1', 3504.60, 3309.90, '1', '1', '1', 1, 3309.90, 0.00, 0.00, 3894.00, 'JVC-002', '1754351675_FOTOS MAQUINAS JVC LIMPIAS (35).jpg', 'Modelo: TD-14N \r\nPotencia de motor: 1.5 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotaci├│n: 175 RPM. \r\nMotor: KDS del Grupo Imperial \r\nEstructura en Acero Inoxidable Anticorrosivo. \r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 14\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros.\r\nIncluye: Cepillo de Lavar de 13\" y Cepillo de Lustrar de 13\".', 5, 'ADASD SAD ASD ASD', '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (21, 'JVC-003', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 16\" - MARCA: CRIS-TAURO', 4130.00, 321.00, 9, 0, 12, 1, '1000-01-01', '32423', '1', '0', 3717.00, 3510.50, '1', '1', '1', 1, 3510.50, 0.00, 0.00, 4130.00, 'JVC-003', '1754667793_FOTOS MAQUINAS JVC LIMPIAS (35).jpg', 'Modelo: TD-16N \nPotencia de motor: 1.5 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotaci├│n: 175 RPM. \nMotor: KDS del Grupo Imperial.\nEstructura en Acero Inoxidable Anticorrosivo.\nBase de Motor en Aluminio Fundido anticorrosivo.\nPlato en Acero Inoxidable (calidad 304) de 16\".\nCable Vulcanizado Homologado de 3x14: 15 metros.\nIncluye: Cepillo de Lavar de 15\" y Cepillo de Lustrar de 15\".', 5, 'VCGH YUVUYH', '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (28, 'JVC-004', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 18\" - MARCA: CRIS-TAURO', 4366.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 3929.40, 3711.10, '1', '1', '1', 1, 3711.10, 0.00, 0.00, 4366.00, 'JVC-004', '1754339695_FOTOS MAQUINAS JVC LIMPIAS (36).jpg', 'Modelo: TD-18N \nPotencia de motor: 1.5 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotaci├│n: 175 RPM. \nMotor: KDS del Grupo Imperial.\nEstructura en Acero Inoxidable Anticorrosivo.\nBase de Motor en Aluminio Fundido anticorrosivo.\nPlato en Acero Inoxidable (calidad 304) de 18\".\nCable Vulcanizado Homologado de 3x14: 15 metros.\nIncluye: Cepillo de Lavar de 17\" y Cepillo de Lustrar de 17\"', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (29, '', 'aspirado', 1400.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '', '', '0', 1, 0.00, 0.00, 0.00, 1400.00, 'jvc ', NULL, 'sadasd\r\nddasdas\r\nasdasdasd\r\nasdsd', 4, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (30, 'JVC-005', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 20\" - MARCA: CRIS-TAURO', 4602.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 4141.80, 3911.70, '1', '1', '1', 1, 3911.70, 0.00, 0.00, 4602.00, 'JVC-005', '1754665173_FOTOS MAQUINAS JVC LIMPIAS (37).jpg', 'Modelo: TD-20N \nPotencia de motor: 1.5 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotaci├│n: 175 RPM. \nMotor: KDS del Grupo Imperial.\nEstructura en Acero Inoxidable Anticorrosivo.\nBase de Motor en Aluminio Fundido anticorrosivo.\nPlato en Acero Inoxidable (calidad 304) de 20\".\nCable Vulcanizado Homologado de 3x14: 15 metros.\nIncluye: Cepillo de Lavar de 19\" y Cepillo de Lustrar de 19\"', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (31, 'JVC-006', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 23\" - MARCA: CRIS-TAURO', 5310.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 4779.00, 4513.50, '1', '1', '1', 1, 4513.50, 0.00, 0.00, 5310.00, 'JVC-006', '1754665673_FOTOS MAQUINAS JVC LIMPIAS (37).jpg', 'Modelo: TD-23N \nPotencia de motor: 2.0 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotaci├│n: 175 RPM. \nMotor: KDS del Grupo Imperial.\nEstructura en Acero Inoxidable Anticorrosivo.\nBase de Motor en Aluminio Fundido anticorrosivo.\nPlato en Acero Inoxidable (calidad 304) de 23\".\nCable Vulcanizado Homologado de 3x14: 15 metros.\nIncluye: Cepillo de Lavar de 22\" y Cepillo de Lustrar de 22\".', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (32, NULL, 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS (2HP)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 0.00, 'JVC-006-1', NULL, 'Modelo: TD-23N Potencia de motor: 2 HP Voltaje / Frecuencia: 220 V/60 Hz. Velocidad de Rotaci├│n: 175 RPM Motor: KDS del Grupo Imperial. Estructura en Acero Inoxidable Anticorrosivo Base de Motor en Aluminio Fundido anticorrosivo Plato en Acero Inoxidable (calidad 304) de 23\" Cable Vulcanizado Homologado de 3x14: 15 metros Incluye cepillo de lavar y lustrar', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (33, 'JVC-007', 'ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 6 GALONES - MARCA: CRIS-TAURO', 2183.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 1964.70, 1855.55, '1', '1', '1', 1, 1855.55, 0.00, 0.00, 2183.00, 'JVC-007', '1754667858_FOTOS-LIMPIAS-JVC-2024-09-10T155819.415.png', 'Modelo: AD-06G \nMotor: 1200W - 60Hz / 18000 RPM\nAspirado: Polvo y Agua\nTanque: Fibra de vidrio de 06 Galones.\nCable Vulcanizado Homologado de 3x14: 10 metros.\nIncluye: Kit completo de accesorios', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (34, 'JVC-008', 'ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 8 GALONES - MARCA: CRIS-TAURO', 2596.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2336.40, 2206.60, '1', '1', '1', 1, 2206.60, 0.00, 0.00, 2596.00, 'JVC-008', '1754667894_FOTOS-LIMPIAS-JVC-2024-09-10T162628.808 (1).png', 'Modelo: AD-08G \nMotor: 1200W - 60Hz / 18000 RPM\nAspirado: Polvo y Agua\nTanque: Fibra de vidrio de 08 Galones.\nCable Vulcanizado Homologado de 3x14: 10 metros.\nIncluye: Kit completo de accesorios', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (35, 'JVC-009', 'ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 10 GALONES - MARCA: CRIS-TAURO', 3009.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2708.10, 2557.65, '1', '1', '1', 1, 2557.65, 0.00, 0.00, 3009.00, 'JVC-009', '1749570088_FOTOS-LIMPIAS-JVC-2024-09-10T163234.726.png', 'Modelo: AD-10G \nMotor: 1200W - 60Hz / 18000 RPM\nAspirado: Polvo y Agua\nTanque: Fibra de vidrio de 10 Galones.\nCable Vulcanizado Homologado de 3x14: 10 metros.\nIncluye: Kit completo de accesorios', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (36, 'JVC-010', 'ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 12 GALONES - MARCA: CRIS-TAURO', 3422.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 3079.80, 2908.70, '1', '1', '1', 1, 2908.70, 0.00, 0.00, 3422.00, 'JVC-010', '1754668006_FOTOS-LIMPIAS-JVC-2024-09-10T164256.537 (1).png', 'Modelo: AD-12G \r\nMotor: 1200W - 60Hz / 18000 RPM\r\nAspirado: Polvo y Agua\r\nTanque: Fibra de vidrio de 12 Galones.\r\nCable Vulcanizado Homologado de 3x14: 10 metros.\r\nIncluye: Kit completo de accesorios', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (37, 'JVC-011', 'ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 15 GALONES - MARCA: CRIS-TAURO', 3835.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 3451.50, 3259.75, '1', '1', '1', 1, 3259.75, 0.00, 0.00, 3835.00, 'JVC-011', '1754665805_aspiradora 15g.png', 'Modelo: AD-15G \nMotor: 1200W - 60Hz / 18000 RPM\nAspirado: Polvo y Agua\nTanque: Fibra de vidrio de 15 Galones.\nCable Vulcanizado Homologado de 3x14: 10 metros.\nIncluye: Kit completo de accesorios', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (38, 'JVC-012', 'ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 20 GALONES - MARCA: CRIS-TAURO', 4248.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 3823.20, 3610.80, '1', '1', '1', 1, 3610.80, 0.00, 0.00, 4248.00, 'JVC-012', '1754665833_FOTOS-LIMPIAS-JVC-2024-09-16T092619.035 (1).png', 'Modelo: AD-20G \nMotor: 1200W - 60Hz / 18000 RPM\nAspirado: Polvo y Agua\nTanque: Fibra de vidrio de 20 Galones.\nCable Vulcanizado Homologado de 3x14: 10 metros.\nIncluye: Kit completo de accesorios', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (39, 'JVC-013', 'ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 25 GALONES (DOBLE MOTOR) - MARCA: CRIS-TAURO', 4661.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 4194.90, 3961.85, '1', '1', '1', 1, 3961.85, 0.00, 0.00, 4661.00, 'JVC-013', '1754665859_FOTOS-LIMPIAS-JVC-2024-09-19T151353.562 (1).png', 'Modelo: AD-25G \nMotor: 1200W - 60Hz / 18000 RPM\nAspirado: Polvo y Agua\nTanque: Fibra de vidrio de 25 Galones.\nCable Vulcanizado Homologado de 3x14: 10 metros.\nIncluye: Kit completo de accesorios', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (40, 'JVC-014', 'LAVADORA DE ALFOMBRAS INDUSTRIAL 16\" (SISTEMA CONVENSIONAL) - MARCA: CRIS- TAURO', 4543.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 4088.70, 3861.55, '1', '1', '1', 1, 3861.55, 0.00, 0.00, 4543.00, 'JVC-014', '1749572471_FOTOS MAQUINAS LIMPIAS (1).png', 'Modelo: LTC-16C \nMotor: 1.5HP - 60Hz / 1750 RPM\nSistema de lavado: Sistema de inyecci├│n por gravedad.\nCapacidad del Tanque: 12 Litros polietileno.\nIncluye: Cepillo ranurado de lavar alfombras de 15\".', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (41, 'JVC-015', 'LAVADORA DE ALFOMBRAS INDUSTRIAL 16\" (SISTEMA INYECCI├ôN A ESPUMA) - MARCA: CRIS-TAURO', 6490.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 5841.00, 5516.50, '1', '1', '1', 1, 5516.50, 0.00, 0.00, 6490.00, 'JVC-015', '1749572884_FOTOS-LIMPIAS-JVC-2024-09-16T093209.815.png', 'Modelo: LTC-16A \nMotor: 1.5HP - 60Hz / 1750 RPM \nMotor de Mezcla: 1200W - 60 Hz \nSistema de lavado: Sistema generador de espuma.\nTanque: 12 Litros de fibra de vidrio.\nIncluye: Cepillo ranurado de lavar alfombras de 15\".', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (42, 'JVC-016', 'ABRILLANTADOR INDUSTRIAL PARA PISOS DE 20\" - MARCA: MASTER GOLDS', 4484.00, 0.00, 1, 0, 12, 1, '1000-01-01', '', '1', '1', 4035.60, 3811.40, '1', '1', '1', 1, 3811.40, 0.00, 0.00, 4484.00, 'JVC-016', '1754665981_FOTOS MAQUINAS JVC LIMPIAS (10).jpg', 'Modelo: AMG-1500A \nMotor: 2.0 HP \nInducido Estructura: Polipropileno de alta densidad.\nPlato: Polipropileno de 20\" \nCable: Vulcanizado x 10 metros.\nIncluye: Porta Pad 20\" y Disco Pad 20\".', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', '2025-10-06 17:36:36');
INSERT INTO `productos` VALUES (43, 'JVC-017', 'ASPIRADORA PROFESIONAL DE POLVO DE 6 LITROS (TIPO MOCHILA) - MARCA: MASTER GOLDS', 979.40, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '1', 881.46, 832.49, '1', '1', '1', 1, 832.49, 0.00, 0.00, 979.40, 'JVC-017', '1759790355_FOTOS-LIMPIAS-JVC-2024-09-09T153944.086.png', 'Modelo: AMG-6L \nMotor: 1000W LAMB AMETEK \nCapacidad: 6 LT \nAspirado: Polvo \nCable: Vulcanizado x 10 metros \nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (44, 'JVC-018', 'ASPIRADORA PROFESIONAL DE POLVO DE 10 LITROS (LINEA HOTELERA) - MARCA: MASTER GOLDS', 767.00, 0.00, -7, 0, 12, 1, '1000-01-01', '', '1', '1', 690.30, 651.95, '1', '1', '1', 1, 651.95, 0.00, 0.00, 767.00, 'JVC-018', '1755116433_FOTOS MAQUINAS JVC LIMPIAS (39).jpg', 'Modelo: AMG-10L \nMotor: 1000W LAMB AMETEK \nCapacidad: 10 LT \nAspirado: Polvo \nCable: Vulcanizado x 7 metros \nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (45, 'JVC-019', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 15 LT - MARCA: MASTER GOLDS', 944.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '1', 849.60, 802.40, '1', '1', '1', 1, 802.40, 0.00, 0.00, 944.00, 'JVC-019', '1755116234_FOTOS MAQUINAS JVC LIMPIAS (38).jpg', 'Modelo: AMG-15L \nMotor: 1000W LAMB AMETEK \nCapacidad: 15 LT \nEstructura: Tanque de acero \nAspirado: Polvo y Agua \nCable: Vulcanizado x 7 metros \nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (46, 'JVC-020', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 25 LT - MARCA: MASTER GOLDS', 1062.00, 0.00, 1, 0, 12, 1, '1000-01-01', '', '1', '1', 955.80, 902.70, '1', '1', '1', 1, 902.70, 0.00, 0.00, 1062.00, 'JVC-020', '1759790504_FOTOS-LIMPIAS-JVC-5-scaled.png', 'Modelo: AMG-25L \nMotor: 1000W LAMB AMETEK \nCapacidad: 25 LT \nEstructura: Tanque de acero \nAspirado: Polvo y Agua \nCable: Vulcanizado x 7 metros \nIncluye: Kit de accesorios.', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (47, 'JVC-021', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 30 LT - MARCA: MASTER GOLDS', 1180.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 1062.00, 1003.00, '1', '1', '1', 1, 1003.00, 0.00, 0.00, 1180.00, 'JVC-021', NULL, 'Modelo: AMG-30L \nMotor: 1000W LAMB AMETEK \nCapacidad: 30 LT \nEstructura: Tanque de acero \nAspirado: Polvo y Agua \nCable: Vulcanizado x 7 metros.\nIncluye: Kit de accesorios.', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (48, 'JVC-022', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 38 LT - MARCA: MASTER GOLDS', 1298.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 1168.20, 1103.30, '1', '1', '1', 1, 1103.30, 0.00, 0.00, 1298.00, 'JVC-022', NULL, 'Modelo: AMG-38L \nMotor: 1000W LAMB AMETEK \nCapacidad: 38 LT \nEstructura: Tanque de acero\nAspirado: Polvo y agua\nCable: Vulcanizado x 7 metros.\nIncluye: Kit de accesorios.', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (49, 'JVC-023', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 10 GALONES - MARCA: MASTER GOLDS', 1593.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 1433.70, 1354.05, '1', '1', '1', 1, 1354.05, 0.00, 0.00, 1593.00, 'JVC-023', NULL, 'Modelo: AMG-10G \r\nMotor: 1200W ITALY \r\nCapacidad: 10 GALONES \r\nEstructura: Tanque de acero con manija de transporte \r\nAspirado: Polvo y Agua \r\nCable: Vulcanizado x 7 metros \r\nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (50, 'JVC-024', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 12 GALONES - MARCA: MASTER GOLDS', 2065.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 1858.50, 1755.25, '1', '1', '1', 1, 1755.25, 0.00, 0.00, 2065.00, 'JVC-024', NULL, 'Modelo: AMG-12G \r\nMotor: 3000W (2 de 1500W) LAMB AMETEK \r\nCapacidad: 12 GALONES \r\nEstructura: Tanque de acero con manija de transporte\r\nAspirado: Polvo y Agua \r\nCable: Vulcanizado x 7 metros \r\nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (51, 'JVC-025', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 60 LT - MARCA: MASTER GOLDS', 2714.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2442.60, 2306.90, '1', '1', '1', 1, 2306.90, 0.00, 0.00, 2714.00, 'JVC-025', NULL, 'Modelo: AMG-60L \r\nMotor: 2000W (2 de 1000W) LAMB AMETEK \r\nCapacidad: 60 LT \r\nEstructura: Tanque de acero con manija de transporte \r\nAspirado: Polvo y Agua \r\nCable: Vulcanizado x 7 metros \r\nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (52, 'JVC-026', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 70 LT - MARCA: MASTER GOLDS', 2891.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2601.90, 2457.35, '1', '1', '1', 1, 2457.35, 0.00, 0.00, 2891.00, 'JVC-026', NULL, 'Modelo: AMG-70L \r\nMotor: 2000W (2 de 1000W) LAMB AMETEK \r\nCapacidad: 70 LT \r\nEstructura: Tanque de acero con manija de transporte \r\nAspirado: Polvo y Agua \r\nCable: Vulcanizado x 7 metros \r\nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (53, 'JVC-027', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 80 LT - MARCA: MASTER GOLDS', 3363.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 3026.70, 2858.55, '1', '1', '1', 1, 2858.55, 0.00, 0.00, 3363.00, 'JVC-027', NULL, 'Modelo: AMG-80L \r\nMotor: 3000W (3 de 1000W) LAMB AMETEK \r\nCapacidad: 80 LT \r\nEstructura: Tanque de acero con manija de transporte \r\nAspirado: Polvo y Agua \r\nCable: Vulcanizado x 7 metros \r\nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (54, 'JVC-028', 'LIMPIADOR MULTIFUNCIONAL DE ALFOMBRAS Y SOFAS DE 20 LT- MARCA: MASTER GOLDS', 3540.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 3186.00, 3009.00, '1', '1', '1', 1, 3009.00, 0.00, 0.00, 3540.00, 'JVC-028', NULL, 'Modelo: LMS-20SF \r\nMotor de Aspirado: 1079W \r\nMotor de Lavado: 34W \r\nCapacidad: 20 LT \r\nEstructura: Tanque de acero \r\nCable: Vulcanizado x 7 metros \r\nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (55, 'JVC-029', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 25 LT - MARCA: SPEED POWER', 684.40, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 615.96, 581.74, '1', '1', '1', 1, 581.74, 0.00, 0.00, 684.40, 'JVC-029', NULL, 'Modelo: ASP-25L \nMotor: 1200W \nCapacidad: 25 LT \nEstructura: Tanque de acero \nAspirado: Polvo y Agua \nCable: Vulcanizado x 7 metros \nIncluye: Kit de accesorios', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (56, 'JVC-030', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 38 LT - MARCA: SPEED POWER', 802.40, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 722.16, 682.04, '1', '1', '1', 1, 682.04, 0.00, 0.00, 802.40, 'JVC-030', '', 'Modelo: ASP-38L \nMotor: 1500W \nCapacidad: 38 LT \nEstructura: Tanque de acero \nAspirado: Polvo y Agua \nCable: Vulcanizado x 7 metros \nIncluye: Kit de accesorios', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (57, 'JVC-031', 'ASPIRADORA PROFESIONAL DE POLVO Y AGUA DE 38 LT - MARCA: SPEED POWER', 1003.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 902.70, 852.55, '1', '1', '1', 1, 852.55, 0.00, 0.00, 1003.00, 'JVC-031', NULL, 'Modelo: ASP-30LA \nMotor: 1000W \nCapacidad: 38 LT \nEstructura: Tanque de acero \nAspirado: Polvo y Agua \nCable: Vulcanizado x 10 metros \nIncluye: Kit de accesorios', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (58, 'JVC-032', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 14\" - MARCA: SPEED POWER', 2950.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2655.00, 2507.50, '1', '1', '1', 1, 2507.50, 0.00, 0.00, 2950.00, 'JVC-032', NULL, 'Modelo: LPS-14 \nMotor: 1.5 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotaci├│n: 175 RPM. \nEstrucura: Acero inoxidable \nPlato: Acero inox de 14\" \nCable: Vulcanizado x 12 metros \nIncluye: Cepillo de lavar 13\" y lustrar de 13\"', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (59, 'JVC-033', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 18\" - MARCA: SPEED POWER', 3304.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2973.60, 2808.40, '1', '1', '1', 1, 2808.40, 0.00, 0.00, 3304.00, 'JVC-033', NULL, 'Modelo: LPS-18 \nMotor: 1.5 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotaci├│n: 175 RPM. \nEstrucura: Acero inoxidable \nPlato: Acero revestido de 18\" \nCable: Vulcanizado x 12 metros \nIncluye: Cepillo de lavar 16\" y lustrar de 16\"', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (60, 'JVC-034', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 20\" - MARCA: SPEED POWER', 3540.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 3186.00, 3009.00, '1', '1', '1', 1, 3009.00, 0.00, 0.00, 3540.00, 'JVC-034', NULL, 'Modelo: LPS-20 \nMotor: 1.5 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotaci├│n: 175 RPM. \nEstrucura: Acero inoxidable \nPlato: Acero revestido de 20\" \nCable: Vulcanizado x 12 metros \nIncluye: Cepillo de lavar y lustrar de 19\"', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (61, 'JVC-035', 'LAVADORA DE ALFOMBRAS DE 18\" (SISTEMA CONVESIONAL) - MARCA: SPEED POWER', 3894.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 3504.60, 3309.90, '1', '1', '1', 1, 3309.90, 0.00, 0.00, 3894.00, 'JVC-035', NULL, 'Modelo: LSP-16C \nMotor: 1.5HP / 1710 RPM \nSistema de lavado: Sistema de inyecci├│n por gravedad \nCapacidad del Tanque: 12 Litros polietileno \nEstructura: Acero Plato: 18\" \nCable: Vulcanizado de 12 metros \nIncluye: Cepillo ranurado de lavar alfombras 16\"', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (62, 'JVC-036', 'LAVADORA DE ALFOMBRAS DE 18\" (SISTEMA GENERADOR ESPUMA) - MARCA: SPEED POWER', 4484.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 4035.60, 3811.40, '1', '1', '1', 1, 3811.40, 0.00, 0.00, 4484.00, 'JVC-036', '1755119023_Sin t├¡tulo-1.png', 'Modelo: LSP-15A \nMotor: 1.5HP / 1710 RPM \nSistema de lavado: Sistema generador de espuma \nCapacidad del Tanque: 12 Litros \nEstructura: Acero Plato: 18\" \nCable: Vulcanizado de 12 metros \nIncluye: Cepillo ranurado de lavar alfombras 16\"', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (63, NULL, 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 14\" - MARCA: SPEED POWER', 2950.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 2950.00, 'JVC-037', NULL, 'Modelo: LPS-14 Motor: 1.5 HP Voltaje / Frecuencia: 220 V/60 Hz. Velocidad de Rotaci├│n: 175 RPM. Estrucura: Acero inoxidable Plato: Acero revestido Cable: Vulcanizado x 12 metros Incluye: Cepillo de lavar y lustrar de 13\"', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (64, NULL, 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 18\" - MARCA: SPEED POWER', 3304.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 3304.00, 'JVC-038', NULL, 'Modelo: LPS-18 Motor: 1.5 HP Voltaje / Frecuencia: 220 V/60 Hz. Velocidad de Rotaci├│n: 175 RPM. Estrucura: Acero inoxidable Plato: Acero revestido Cable: Vulcanizado x 12 metros Incluye: Cepillo de lavar y lustrar de 16\"', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (65, 'JVC-039', 'LAVADORA Y SECADORA DE MUEBLES Y COLCHONES (LAVA BUTACAS) - MARCA: MASTER GOLDS', 5664.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 5097.60, 4814.40, '1', '1', '1', 1, 4814.40, 0.00, 0.00, 5664.00, 'JVC-039', '1772116726_FOTOS-MAQUINAS-JVC-LIMPIAS-15-scaled.jpg', 'Modelo: MLC-730 \r\nMotor: 1000W \r\nMotor Cepillo: 32 V \r\nCapacidad tanque soluci├│n: 16L \r\nCapacidad tanque recuperaci├│n: 12L \r\nManguera: 1.5 metros \r\nCable: vulcanizado de 8 metros \r\nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (66, 'JVC-040', 'SECADORA INDUSTRIAL DE PISOS Y ALFOMBRAS DE 350W (BLOWER) - MARCA: SPEED POWER', 885.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 796.50, 752.25, '1', '1', '1', 1, 752.25, 0.00, 0.00, 885.00, 'JVC-040', NULL, 'Modelo: SSL-350 \nPotencia: 350W \nCaudales regulables: Baja - Media - Alta \nVelocidad: 3 Temporizador: 30 - 60 - 90 minutos \nCable: Vulcanizado de 8 metros', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (67, 'JVC-040-1', 'SECADORA INDUSTRIAL DE PISOS Y ALFOMBRAS DE 850W (RUEDA DE TRANSPORTE) - MARCA: GAOMEI', 1180.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 1062.00, 1003.00, '1', '1', '1', 1, 1003.00, 0.00, 0.00, 1180.00, 'JVC-040-1', NULL, 'Modelo: B-3 \nPotencia: 850W \nCaudales regulables: 2500 - 3400 - 4200 m3/h \nCable: Vulcanizado de 7 metros', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (68, 'JVC-041', 'SECADORA INDUSTRIAL DE ALFOMBRAS Y PASADIZOS DE 900W - MARCA: MASTER GOLDS', 1298.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 1168.20, 1103.30, '1', '1', '1', 1, 1103.30, 0.00, 0.00, 1298.00, 'JVC-041', NULL, 'Modelo: SMG-900 \nPotencia: 900W \nVelocidades: 3\nCaudales regulables: 2500 - 3400 - 4200 m3/h \nCable: Vulcanizado de 7 metros', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (69, 'JVC-042', 'SECADORA INDUSTRIAL DE ALFOMBRAS Y PASADIZOS DE 900W (RUEDA DE TRANSPORTE) - MARCA: MASTER GOLDS', 1475.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 1327.50, 1253.75, '1', '1', '1', 1, 1253.75, 0.00, 0.00, 1475.00, 'JVC-042', '1755120671_FOTOS MAQUINAS JVC LIMPIAS (40).jpg', 'Modelo: SMG-900B \nPotencia: 900W \nVelocidades: 3\nCaudales regulables: 2500 - 3400 - 4200 m3/h \nCon manija de transporte\nCable: vulcanizado de 7 metros ', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (70, 'JVC-043', 'VAPORIZADORA DE ALTA PRESI├ôN DE 2200W - MARCA: MASTER GOLDS', 3304.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2973.60, 2808.40, '1', '1', '1', 1, 2808.40, 0.00, 0.00, 3304.00, 'JVC-043', NULL, 'Modelo: VMG-1800 \nPotencia: 2200W \nNivel de vapor: 02 \nDep├│sito de agua: 2L \nMaterial: ABS, Acero inox \nCable: Vulcanizado de 5 metros \nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (71, 'JVC-044', 'VAPORIZADOR DE ALTA PRESI├ôN DE 2200W - MARCA: MASTER GOLDS', 2950.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2655.00, 2507.50, '1', '1', '1', 1, 2507.50, 0.00, 0.00, 2950.00, 'JVC-044', NULL, 'Modelo: VMG-1800 \nPotencia: 2200W \nNivel de vapor: 04 \nDep├│sito de agua: 5L Material: ABS, Acero inox \nCable: Vulcanizado de 5 metros \nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (72, 'JVC-045', 'BARREDORA INDUSTRIAL MEC├üNICA (HOMBRE ANDANTE) - MARCA: MASTER GOLDS', 2714.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2442.60, 2306.90, '1', '1', '1', 1, 2306.90, 0.00, 0.00, 2714.00, 'JVC-045', '1755120896_BARREDORA GRISS MG.jpg', 'Modelo: BMG-B40L \nEficiencia de trabajo: 3680 m2/h \nAncho de limpieza: 920 mm \nVolumen de basura: 40L', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (73, 'JVC-046', 'BARREDORA INDUSTRIAL (HOMBRE ANDANTE) CAPACIDAD 35 LT - MASTER GOLDS', 10620.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 9558.00, 9027.00, '1', '1', '1', 1, 9027.00, 0.00, 0.00, 10620.00, 'JVC-046', NULL, 'Modelo: MG-60', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (74, 'JVC-047', 'BARREDORA INDUSTRIAL 180 LT (HOMBRE ABORDO) - MARCA: MASTER GOLDS', 25960.00, 0.00, 1, 0, 12, 1, '1000-01-01', '', '1', '0', 23364.00, 22066.00, '1', '1', '1', 1, 22066.00, 0.00, 0.00, 25960.00, 'JVC-047', '1755121325_FOTOS MAQUINAS JVC LIMPIAS (41).jpg', 'Modelo: BEM-1800 \r\nPotencia de motor: 1200W \r\nBater├¡a: 8 x 48V \r\nProductividad de trabajo: 12500 m2/h \r\nTanque de recolecci├│n: 180L \r\nCantidad Cepillos: 5 unidades \r\nHoras de trabajo: 5 - 6 horas', 6, NULL, '14', 'USD', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (75, 'JVC-048', 'HIDROLAVADORA INDUSTRIAL (MONOF├üSICA) - MARCA: MASTER GOLDS', 4956.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 4460.40, 4212.60, '1', '1', '1', 1, 4212.60, 0.00, 0.00, 4956.00, 'JVC-048', '1755121916_FOTOS MAQUINAS JVC LIMPIAS (42).jpg', 'Modelo: HMG6-15CL \nPotencia KW: 3.1KW \nVoltaje/Hz: 220V/60Hz \nFlujo de agua: 560 L/H \nBarra de presi├│n: 150 bar', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (76, 'JVC-049', 'HIDROLAVADORA INDUSTRIAL (TRIF├üSICA) - MARCA: MASTER GOLDS', 6844.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 6159.60, 5817.40, '1', '1', '1', 1, 5817.40, 0.00, 0.00, 6844.00, 'JVC-049', '1755179376_FOTOS MAQUINAS JVC LIMPIAS (43).jpg', 'Modelo: HMG7-18CL \nPotencia KW: 4.7KW \nVoltaje/Hz: 380V/60Hz \nFlujo de agua: 700 L/H \nBarra de presi├│n: 180 bar', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (77, 'JVC-050', 'FREGADORA SEMI INDUSTRIAL EL├ëCTRICA (CEPILLO 14\') - MARCA: MASTER GOLDS', 5900.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 5310.00, 5015.00, '1', '1', '1', 1, 5015.00, 0.00, 0.00, 5900.00, 'JVC-050', '1755179633_FOTOS MAQUINAS JVC LIMPIAS (44).jpg', 'Modelo: FMG-K201 \nPotencia: 550W \nVoltaje/Hz: 220V/60Hz \nCapacidad de trabajo: 1100 m2/h \nCapacidad de tanque de soluci├│n: 11.8L \nCapacidad de tanque de recuperaci├│n: 13.4L \nCable: Vulcanizado 25 metros', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (78, 'JVC-051', 'FREGADORA INDUSTRIAL DE PISOS DE 19\" A BATERIA - MARCA: SPEED POWER', 15930.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 14337.00, 13540.50, '1', '1', '1', 1, 13540.50, 0.00, 0.00, 15930.00, 'JVC-051', '1755179978_FOTOS MAQUINAS JVC LIMPIAS (45).jpg', '', 8, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (79, 'JVC-052', 'FREGADORA INDUSTRIAL DE PISOS DE 18\" A BATERIA - MARCA: MASTER GOLDS', 17110.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 15399.00, 14543.50, '1', '1', '1', 1, 14543.50, 0.00, 0.00, 17110.00, 'JVC-052', '1755180124_FOTOS MAQUINAS JVC LIMPIAS (46).jpg', '', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (80, 'JVC-053', 'FREGADORA INDUSTRIAL DE PISOS DE 19\" A BATERIA - MARCA: MASTER GOLDS', 20650.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 18585.00, 17552.50, '1', '1', '1', 1, 17552.50, 0.00, 0.00, 20650.00, 'JVC-053', '1755180261_FOTOS MAQUINAS JVC LIMPIAS (47).jpg', '', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (81, NULL, 'HIDROLAVADORA INDUSTRIAL (TRIF├üSICA) - MARCA: MASTER GOLDS', 7670.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 7670.00, 'JVC-054', NULL, 'Modelo: HMG7-18CL Potencia KW: 4.7KW Voltaje/Hertz: 380V/60Hz Flujo de agua: 700 L/H Barra de presi├│n: 180bar', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (82, 'JVC-055', 'TERMONEBULIZADORA (CA├æON CORTO) - MARCA: MASTER FOG', 6490.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 5841.00, 5516.50, '1', '1', '1', 1, 5516.50, 0.00, 0.00, 6490.00, 'JVC-055', NULL, 'Modelo: TMG-T34 \r\nFuente de alimentaci├│n: 4 x 1.5V \r\nCaudal Max: 25L/H \r\nCapacidad de tanque de soluci├│n: 6 LT \r\nCapacidad de tanque de combusti├│n: 2 LT', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (83, 'JVC-056', 'TERMONEBULIZADORA (CA├æON LARGO) - MARCA: MASTER FOG', 5900.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 5310.00, 5015.00, '1', '1', '1', 1, 5015.00, 0.00, 0.00, 5900.00, 'JVC-056', NULL, 'Modelo: TMG-BW-20 \r\nFuente de alimentaci├│n: 4 x 1.5V \r\nCaudal Max: 45L/H \r\nCapacidad de tanque de soluci├│n: 6 LT \r\nCapacidad de tanque de combusti├│n: 2 LT', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (84, 'JVC-057', 'M├üQUINA ULV 2.5 LITROS (A BATER├ìA) - MARCA: MASTER FOG', 5310.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 4779.00, 4513.50, '1', '1', '1', 1, 4513.50, 0.00, 0.00, 5310.00, 'JVC-057', '1755182518_FOTOS MAQUINAS JVC LIMPIAS (53).jpg', 'Modelo: UMG-3600B \r\nMotor el├®ctrico: 450W \r\nBoquilla de nebulizaci├│n: Contrarotativas\r\nCapacidad del tanque: 2.5L', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (85, 'JVC-058', 'M├üQUINA ULV 2.5 LITROS (EL├ëCTRICO) - MARCA: MASTER FOG', 2714.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2442.60, 2306.90, '1', '1', '1', 1, 2306.90, 0.00, 0.00, 2714.00, 'JVC-058', '1755182628_FOTOS MAQUINAS JVC LIMPIAS (54).jpg', 'Modelo: UMG-3600E \r\nMotor el├®ctrico: 800W - 220V \r\nBoquilla de nebulizaci├│n: Contrarotativas \r\nCapacidad del tanque: 2.5 LT', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (86, 'JVC-059', 'M├üQUINA ULV DE 5 LITROS (A BATER├ìA) - MARCA: MASTER FOG', 7670.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 6903.00, 6519.50, '1', '1', '1', 1, 6519.50, 0.00, 0.00, 7670.00, 'JVC-059', '1755184105_FOTOS MAQUINAS JVC LIMPIAS (55).jpg', 'Modelo: PIONEER \r\nMotor el├®ctrico: 450W \r\nBoquilla de nebulizaci├│n: Giratoria \r\nCapacidad del tanque: 5 LT', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (87, 'JVC-060', 'M├üQUINA ULV 6 LITROS (├ëLECTRICO) - MARCA: MASTER FOG', 2478.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2230.20, 2106.30, '1', '1', '1', 1, 2106.30, 0.00, 0.00, 2478.00, 'JVC-060', '1755184161_FOTOS MAQUINAS JVC LIMPIAS (52).jpg', 'Modelo: UMG-2680A \r\nMotor el├®ctrico: 800W - 220V/60Hz \r\nBoquilla de nebulizaci├│n: Contrarotativas \r\nCapacidad del tanque: 6 LT', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (88, 'JVC-061', 'M├üQUINA ULV DE 10 LITROS (├ëLECTRICO) - MARCA: MASTER FOG', 2507.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2256.75, 2131.38, '1', '1', '1', 1, 2131.38, 0.00, 0.00, 2507.50, 'JVC-061', NULL, 'Modelo: UMG-1500 \r\nMotor el├®ctrico: 1400W - 220V/60Hz \r\nBoquilla de nebulizaci├│n: Tipo remolino \r\nCapacidad del tanque: 10 LT', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (89, 'JVC-062', 'M├üQUINA ULV DE 12 LITROS (├ëLECTRICO) - MARCA: MASTER FOG', 2625.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2362.95, 2231.68, '1', '1', '1', 1, 2231.68, 0.00, 0.00, 2625.50, 'JVC-062', NULL, 'Modelo: UMG-1500E \r\nMotor el├®ctrico: 1400W - 220V/60Hz \r\nBoquilla de nebulizaci├│n: Tipo remolino \r\nCapacidad del tanque: 12L', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (90, 'JVC-063', 'M├üQUINA ULV DE 16 LITROS (├ëLECTRICO) - MARCA: MASTER FOG', 2743.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2469.15, 2331.98, '1', '1', '1', 1, 2331.98, 0.00, 0.00, 2743.50, 'JVC-063', NULL, 'Modelo: UMG-1500MP \r\nMotor el├®ctrico: 1400W - 220V/60Hz \r\nBoquilla de nebulizaci├│n: Tipo remolino \r\nCapacidad del tanque: 16 LT', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (91, 'JVC-064', 'MOCHILA PULVERIZADORA DE 20 LITROS (MANUAL) - MARCA: MASTER FOG', 295.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 265.50, 250.75, '1', '1', '1', 1, 250.75, 0.00, 0.00, 295.00, 'JVC-064', NULL, 'Modelo: PMG-20L \r\nManguera: 13500 mm \r\nLanza: 600 mm \r\nCapacidad del tanque: 20 LT \r\nMaterial del tanque: Polipropileno', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (92, 'JVC-065', 'MOTO ATOMIZADORA DE 14 LITROS (MOTOR 2 TIEMPOS) - MARCA: MASTER FOG', 2301.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 2070.90, 1955.85, '1', '1', '1', 1, 1955.85, 0.00, 0.00, 2301.00, 'JVC-065', NULL, 'Modelo: NTS420 \r\nTipo de motor: 2 tiempos \r\nCilindro de desplazamiento: 56.5cc \r\nPotencia de salida: 3.0kW/4.0hp \r\nVelocidad m├íxima del motor: 6000rpm \r\nCapacidad del tanque de combustible: 1.5 LT \r\nCacidad del tanque Quimico: 14 LT', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (93, 'JVC-066', 'MOTO PULVERIZADORA DE 25 LITROS (MOTOR 2 TIEMPOS) - MARCA: MASTER FOG', 1947.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 1752.30, 1654.95, '1', '1', '1', 1, 1654.95, 0.00, 0.00, 1947.00, 'JVC-066', NULL, 'Modelo: NTS-768 \r\nTipo de motor: 2 tiempos \r\nCilindro de desplazamiento: 26cc \r\nPotencia de salida: 3.0kW/4.0hp \r\nVelocidad m├íxima del motor: 7500rpm \r\nCapacidad del tanque de combustible: 0.9 LT \r\nCacidad del tanque Quimico: 25 LT', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (94, 'JVC-067', 'M├üQUINA DESBROZADORA 2 TIEMPOS (MOTOGUADA├æA) - MARCA: MASTER GREEN', 2832.00, 0.00, -1, 0, 12, 1, '1000-01-01', '', '1', '0', 2548.80, 2407.20, '1', '1', '1', 1, 2407.20, 0.00, 0.00, 2832.00, 'JVC-067', NULL, 'Modelo: 541RS \r\nMotor: 2.2 HP \r\nTipo de motor: 2 tiempos \r\nCilindro de desplazamiento: 43cc \r\nPotencia de salida: 1.47kW \r\nVelocidad m├íxima del motor: 7000rpm \r\nCapacidad de combustible: 950 ml \r\nIncluye: Cuchilla 2T', 21, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (95, NULL, 'M├üQUINA DESBROZADORA 2 TIEMPOS (MOTOGUADA├æA) - MARCA: MASTER GREEN', 2655.00, 40.00, 20, 0, 12, 1, '1000-01-01', '', '0', '0', 2389.50, 2256.75, '1', '1', '1', 1, 2256.75, 0.00, 0.00, 2655.00, 'JVC-068', NULL, 'Modelo: 143R-II \r\nMotor: 1.4 HP \r\nTipo de motor: 2 tiempos \r\nCilindro de desplazamiento: 41.5cc \r\nPotencia de salida: 1.47kW \r\nVelocidad m├íxima del motor: 7000rpm \r\nCapacidad de combustible: 950 ml \r\nIncluye: Cuchilla 2T', 21, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (96, NULL, 'M├üQUINA DESBROZADORA 4 TIEMPOS (MOTOGUADA├æA) - MARCA: MASTER GREEN', 2183.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 1964.70, 1855.55, '1', '1', '1', 1, 1855.55, 0.00, 0.00, 2183.00, 'JVC-069', NULL, 'Modelo: GX50 \r\nMotor: 2.2 HP \r\nTipo de motor: 4 tiempos \r\nCilindro de desplazamiento: 47.9cc \r\nPotencia de salida: 1.47kW \r\nVelocidad m├íxima del motor: 9500rpm \r\nCapacidad de combustible: 950 ml \r\nIncluye: Cuchilla 2T', 21, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (97, NULL, 'M├üQUINA CORTADORA DE C├ëSPED DE 16\" (4 TIEMPOS) - MARCA: NEWTOP', 3068.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 2761.20, 2607.80, '1', '1', '1', 1, 2607.80, 0.00, 0.00, 3068.00, 'JVC-070', NULL, 'Modelo: NTLM16 \r\nMotor: 3.5 HP / 3600rpm \r\nAncho de corte: 16\" / 410mm \r\nPasos de altura de corte: 6 posiciones \r\nCapacidad del tanque de combusteble: 0.75 LT\r\nCapacidad del colector: 40 LT', 20, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (98, NULL, 'M├üQUINA CORTADORA DE C├ëSPED DE 18\"(4 TIEMPOS) - MARCA: NEWTOP', 3304.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 2973.60, 2808.40, '1', '1', '1', 1, 2808.40, 0.00, 0.00, 3304.00, 'JVC-071', NULL, 'Modelo: NTLM18 \r\nMotor: 4.0 HP / 3600rpm \r\nAncho de corte: 18\" / 460mm \r\nPasos de altura de corte: 10 posiciones \r\nCapacidad del tanque de combusteble: 0.8 LT \r\nCapacidad del colector: 60 LT', 21, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (99, NULL, 'M├üQUINA CORTADORA DE C├ëSPED DE 21\"(4 TIEMPOS) - MARCA: NEWTOP', 3658.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 3292.20, 3109.30, '1', '1', '1', 1, 3109.30, 0.00, 0.00, 3658.00, 'JVC-072', NULL, 'Modelo: NTLM21 \r\nMotor: 6.0 HP / 3600rpm \r\nAncho de corte: 21\" / 460mm \r\nPasos de altura de corte: 8 posiciones \r\nCapacidad del tanque de combusteble: 1.0 LT \r\nCapacidad del colector: 65 LT', 21, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (100, NULL, 'MOTOSIERRA INDUSTRIAL CON ESPADA DE 20\" - MARCA: NEWTOP - MODELO: NT5800', 1700.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '1', '1', '1', 1, 0.00, 0.00, 0.00, 1700.00, 'JVC-073', NULL, '', 21, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (101, NULL, 'M├üQUINA ULV DE 16 LITROS (├ëLECTRICO) - MARCA: MASTER GOLDS', 2743.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 2743.50, 'JVC-074', NULL, 'Modelo: UMG-1500MP Motor el├®ctrico: 1400W - 220V/60Hz Boquilla de nebulizaci├│n: Tipo remolino Capacidad del tanque: 16L', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (102, NULL, 'MOTO ATOMIZADORA DE 12L (MOTOR 2 TIEMPOS) - MARCA: NEWTOP', 2773.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 2773.00, 'JVC-075', NULL, 'Modelo: NTS423 Tipo de motor: 2 tiempos Cilindro de desplazamiento: 72.3cc Potencia de salida: 3.0kW/4.0hp Velocidad m├íxima del motor: 5700rpm Capacidad del tanque de combustible: 1.4L Cacidad del tanque Quimico: 12L', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (103, NULL, 'MOTO ATOMIZADORA DE 14L (MOTOR 2 TIEMPOS) - MARCA: NEWTOP', 2596.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 2596.00, 'JVC-076', NULL, 'Modelo: NTS420 Tipo de motor: 2 tiempos Cilindro de desplazamiento: 56.5cc Potencia de salida: 3.0kW/4.0hp Velocidad m├íxima del motor: 6000rpm Capacidad del tanque de combustible: 1.5L Cacidad del tanque Quimico: 14L', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (104, NULL, 'MOTO PULVERIZADORA DE 25 LITROS (MOTOR 2 TIEMPOS) - MARCA: NEWTOP', 2360.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 2360.00, 'JVC-077', NULL, 'Modelo: NTS-768 Tipo de motor: 2 tiempos Cilindro de desplazamiento: 26cc Potencia de salida: 3.0kW/4.0hp Velocidad m├íxima del motor: 7500rpm Capacidad del tanque de combustible: 0.9L Cacidad del tanque Quimico: 25L', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (105, NULL, 'M├üQUINA DESBROZADORA DE CESPED DE 4 TIEMPOS (MOTO GUADA├æA) - MARCA: NEWTOP', 1652.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 1652.00, 'JVC-078', NULL, 'Modelo: NTS139CG Tipo de motor: 4 tiempos Cilindro de desplazamiento: 31.3cc Potencia de salida: 0.7kW/0.9hp Velocidad m├íxima del motor: 9000rpm Capacidad de combustible: 500 ml', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (106, NULL, 'M├üQUINA DESBROZADORA DE CESPED DE 4 TIEMPOS (MOTOGUADA├æA) - MARCA: NEWTOP', 1770.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 1770.00, 'JVC-079', NULL, 'Modelo: NTS140CG Tipo de motor: 4 tiempos Cilindro de desplazamiento: 35.8cc Potencia de salida: 0.7kW/1.0hp Velocidad m├íxima del motor: 9000rpm Capacidad de combustible: 500 ml', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (107, NULL, 'M├üQUINA DESBROZADORA DE CESPED DE 2 TIEMPOS (MOTOGUADA├æA) - MARCA: NEWTOP', 2950.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 2950.00, 'JVC-080', NULL, 'Modelo: NTB143 Tipo de motor: 2 tiempos Cilindro de desplazamiento: 41.5cc Potencia de salida: 1.5kW/2.0hp Velocidad m├íxima del motor: 9000rpm Capacidad de combustible: 1300 ml', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (108, NULL, 'M├üQUINA DESBROZADORA DE CESPED DE 2 TIEMPOS (MOTOGUADA├æA) - MARCA: NEWTOP', 1298.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 1298.00, 'JVC-081', NULL, 'Modelo: NTB260A Tipo de motor: 2 tiempos Cilindro de desplazamiento: 26cc Potencia de salida: 0.7kW/0.9hp Velocidad m├íxima del motor: 9000rpm Capacidad de combustible: 650 ml', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (109, NULL, 'M├üQUINA DESBROZADORA DE CESPED DE 2 TIEMPOS (MOTOGUADA├æA) - MARCA: NEWTOP', 1416.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 1416.00, 'JVC-082', NULL, 'Modelo: NTB330A Tipo de motor: 2 tiempos Cilindro de desplazamiento: 32.6cc Potencia de salida: 0.9kW/1.2hp Velocidad m├íxima del motor: 9000rpm Capacidad de combustible: 850 ml', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (110, NULL, 'M├üQUINA DESBROZADORA 2 TIEMPOS (MOTOGUADA├æA) - MARCA: MASTER GOLDS', 2950.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 2950.00, 'JVC-083', NULL, 'Modelo: 541RS Motor: 2.2 HP Tipo de motor: 2 tiempos Cilindro de desplazamiento: 43cc Potencia de salida: 1.47kW Velocidad m├íxima del motor: 7000rpm Capacidad de combustible: 950 ml Incluye: Cuchilla 2T', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (111, NULL, 'M├üQUINA DESBROZADORA 2 TIEMPOS (MOTOGUADA├æA) - MARCA: MASTER GOLDS', 2655.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 2655.00, 'JVC-084', NULL, 'Modelo: 143R-II Motor: 1.4 HP Tipo de motor: 2 tiempos Cilindro de desplazamiento: 41.5cc Potencia de salida: 1.47kW Velocidad m├íxima del motor: 7000rpm Capacidad de combustible: 950 ml Incluye: Cuchilla 2T', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (112, NULL, 'M├üQUINA DESBROZADORA DE CESPED (4 TIEMPOS) (MOTOGUADA├æA) - MARCA: MASTER GOLDS', 1947.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 1947.00, 'JVC-085', NULL, 'Modelo: GX50 Motor: 2.2 HP Tipo de motor: 4 tiempos Cilindro de desplazamiento: 47.9cc Potencia de salida: 1.47kW Velocidad m├íxima del motor: 9500rpm Capacidad de combustible: 950 ml Incluye: Cuchilla 2T', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (113, NULL, 'M├üQUINA CORTADORA DE C├ëSPED DE 16\" (4 TIEMPOS) - MARCA: NEWTOP', 3068.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 3068.00, 'JVC-086', NULL, 'Modelo: NTLM16 Motor: 3.5 HP / 3600rpm Ancho de corte: 16\" / 410mm Pasos de altura de corte: 6 posiciones Capacidad del tanque de combusteble: 0.75L Capacidad del colector: 40L', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (114, NULL, 'M├üQUINA CORTADORA DE C├ëSPED DE 18\"(4 TIEMPOS) - MARCA: NEWTOP', 3304.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 3304.00, 'JVC-087', NULL, 'Modelo: NTLM18 Motor: 4.0 HP / 3600rpm Ancho de corte: 18\" / 460mm Pasos de altura de corte: 10 posiciones Capacidad del tanque de combusteble: 0.8L Capacidad del colector: 60L', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (115, NULL, 'M├üQUINA CORTADORA DE C├ëSPED DE 21\"(4 TIEMPOS) - MARCA: NEWTOP', 3658.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 3658.00, 'JVC-088', NULL, 'Modelo: NTLM21 Motor: 6.0 HP / 3600rpm Ancho de corte: 21\" / 460mm Pasos de altura de corte: 8 posiciones Capacidad del tanque de combusteble: 1.0L Capacidad del colector: 65L', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (116, NULL, 'MOTOSIERRA INDUSTRIAL CON ESPADA DE 20\" - MARCA: NEWTOP - MODELO: NT5800', 1700.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, NULL, NULL, '0', 1, 0.00, 0.00, 0.00, 1700.00, 'JVC-089', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (117, 'JVC-001', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 12\" - MARCA: CRIS-TAURO', 3658.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 3292.20, 3109.30, '1', '1', '1', 1, 3109.30, 0.00, 0.00, 3658.00, 'JVC-001', NULL, 'Modelo: TD-12N \r\nPotencia de motor: 1.5 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotaci├│n: 175 RPM. \r\nMotor: KDS del Grupo Imperial. \r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 12\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros.\r\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\".', 5, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (118, '', 'BALDE PRENSA MOPA DE 36 LT DOBLE CUBO (18 LT C/U)', 387.50, 100.00, 10, 0, 12, 1, '1000-01-01', '', '1', '0', 348.75, 329.38, '1', '1', '1', 1, 329.38, 0.00, 0.00, 387.50, 'IMPLE-001', '', '', 0, NULL, '5', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (119, '', 'BALDE PRENSA MOPA DE 36 LTS', 252.50, 199.90, 11, 0, 12, 1, '1000-01-01', '', '0', '0', 227.25, 214.62, '1', '1', '1', 1, 214.62, 0.00, 0.00, 252.50, 'IMPLE-002', '', '', 0, NULL, '15', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (120, '', 'BALDE PRENSA MOPA DE 36 LTS', 260.00, 0.00, -1, 0, 12, 1, '1000-01-01', '', '0', '0', 234.00, 221.00, '1', '1', '1', 1, 221.00, 0.00, 0.00, 260.00, 'IMPLE-003', '', '', 0, NULL, '5', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (121, '', 'BALDE PRENSA MOPA DE 36 LTS (DOBLE CUBO)', 336.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 302.85, 286.02, '1', '1', '1', 1, 286.02, 0.00, 0.00, 336.50, 'IMPLE-004', '', '', 0, NULL, '6', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (122, '', 'BALDE PRENSA MOPA PREMIUM DE 81 LT + 3 BALDES + BANDEJA', 548.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 493.20, 465.80, '1', '1', '1', 1, 465.80, 0.00, 0.00, 548.00, 'IMPLE-005', '', '', 17, NULL, '', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (123, '', 'BANDEJA ORGANIZADORA DE IMPLEMENTOS DE LIMPIEZA', 41.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 37.35, 35.27, '1', '1', '1', 1, 35.27, 0.00, 0.00, 41.50, 'IMPLE-006', '', '', 0, NULL, '8', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (124, '', 'COCHE PORTA MATERIALES (AZUL)', 360.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 324.00, 306.00, '1', '1', '1', 1, 306.00, 0.00, 0.00, 360.00, 'IMPLE-007', '', '', 6, NULL, '', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (125, '', 'COCHE PORTA MATERIALES (GRIS Y AZUL)', 330.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 297.00, 280.50, '1', '1', '1', 1, 280.50, 0.00, 0.00, 330.00, 'IMPLE-008', '', '', 0, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (126, '', 'COCHE PORTA IMPLEMENTOS DE LIMPIEZA (LINEA INSTITUCIONAL)', 650.00, 25.00, 10, 0, 12, 1, '1000-01-01', '', '0', '0', 585.00, 552.50, '1', '1', '1', 1, 552.50, 0.00, 0.00, 650.00, 'IMPLE-009', '', '', 0, NULL, '7', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (127, '', 'COCHE PORTA IMPLEMENTOS DE LIMPIEZA (LINEA HOSPITALARIA, INSTITUCIONAL, HOTELERA)', 1100.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 990.00, 935.00, '1', '1', '1', 1, 935.00, 0.00, 0.00, 1100.00, 'IMPLE-010', '', '', 7, NULL, 'undefined', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (128, NULL, 'COCHE PORTA IMPLEMENTOS (REPUESTO BOLSA 60 LT)', 53.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 48.15, 45.47, NULL, NULL, '1', 1, 45.47, 0.00, 0.00, 53.50, 'IMPLE-011', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (129, NULL, 'COCHE PORTA IMPLEMENTOS C/ CIERRE (REPUESTO BOLSA X LT)', 78.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 70.20, 66.30, NULL, NULL, '1', 1, 66.30, 0.00, 0.00, 78.00, 'IMPLE-012', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (130, NULL, 'EXTENSI├ôN TELESC├ôPICA REGULABLE DE ALUMINIO DE 3 MTS (AZUL)', 65.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 58.50, 55.25, NULL, NULL, '1', 1, 55.25, 0.00, 0.00, 65.00, 'IMPLE-013', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (131, NULL, 'EXTENSI├ôN TELESC├ôPICA REGULABLE DE ALUMINIO DE 3 MTS (ESPECIAL - NEGRO)', 75.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 67.50, 63.75, NULL, NULL, '1', 1, 63.75, 0.00, 0.00, 75.00, 'IMPLE-014', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (132, NULL, 'EXTENSI├ôN TELESC├ôPICA REGULABLE DE ALUMINIO DE 4 MTS (NEGRO Y AZUL)', 80.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 72.00, 68.00, NULL, NULL, '1', 1, 68.00, 0.00, 0.00, 80.00, 'IMPLE-015', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (133, NULL, 'EXTENSI├ôN TELESC├ôPICA REGULABLE DE ALUMINIO DE 4.5 MTS (NEGRO)', 95.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 85.50, 80.75, NULL, NULL, '1', 1, 80.75, 0.00, 0.00, 95.00, 'IMPLE-016', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (134, NULL, 'EXTENSI├ôN TELESC├ôPICA REGULABLE DE ALUMINIO DE 6 MTS (3 SECCIONES) (AZUL)', 118.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 106.20, 100.30, NULL, NULL, '1', 1, 100.30, 0.00, 0.00, 118.00, 'IMPLE-017', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (135, NULL, 'EXTENSI├ôN TELESC├ôPICA REGULABLE DE ALUMINIO DE 9 MTS (3 SECCIONES) (AZUL)', 146.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 131.40, 124.10, NULL, NULL, '1', 1, 124.10, 0.00, 0.00, 146.00, 'IMPLE-018', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (136, NULL, 'HUMEDECEDOR DE LUNAS P/EXTENSI├ôN TELESCOPICA DE 40 CM (AZUL)', 41.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 37.35, 35.27, NULL, NULL, '1', 1, 35.27, 0.00, 0.00, 41.50, 'IMPLE-019', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (137, NULL, 'HUMEDECEDOR DE LUNAS P/EXTENSI├ôN TELESCOPICA DE 45 CM (NEGRO)', 47.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 42.75, 40.38, NULL, NULL, '1', 1, 40.38, 0.00, 0.00, 47.50, 'IMPLE-020', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (138, NULL, 'JALADOR DE AGUA DOBLE GOMA C/MANGO DE ALUMINIO Y BASE ACERO ZINCADO DE 60 CM', 78.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 70.65, 66.72, NULL, NULL, '1', 1, 66.72, 0.00, 0.00, 78.50, 'IMPLE-021', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (139, NULL, 'JALADOR DE AGUA DOBLE GOMA C/MANGO DE ALUMINIO Y BASE ACERO ZINCADO DE 100 CM', 88.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 79.65, 75.22, NULL, NULL, '1', 1, 75.22, 0.00, 0.00, 88.50, 'IMPLE-022', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (140, NULL, 'JALADOR DE AGUA DOBLE GOMA C/MANGO DE ALUMINIO Y BASE PL├üSTICA DE 55 CM', 33.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 30.15, 28.48, NULL, NULL, '1', 1, 28.48, 0.00, 0.00, 33.50, 'IMPLE-023', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (141, NULL, 'JALADOR DE AGUA DOBLE GOMA C/MANGO DE ALUMINIO Y BASE PL├üSTICA DE 75 CM', 38.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 34.65, 32.73, NULL, NULL, '1', 1, 32.73, 0.00, 0.00, 38.50, 'IMPLE-024', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (142, NULL, 'JALADOR DE AGUA DOBLE GOMA EBA C/MANGO DE ALUMINIO ESTRIADO Y BASE DE ACERO INOX 55CM', 47.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 42.75, 40.38, NULL, NULL, '1', 1, 40.38, 0.00, 0.00, 47.50, 'IMPLE-025', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (143, NULL, 'JALADOR DE AGUA DOBLE GOMA EBA C/MANGO DE ALUMINIO ESTRIADO Y BASE DE ACERO INOX 75CM', 53.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 48.15, 45.47, NULL, NULL, '1', 1, 45.47, 0.00, 0.00, 53.50, 'IMPLE-026', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (144, NULL, 'JALADOR DE AGUA DOBLE GOMA EBA C/MANGO DE ALUMINIO ESTRIADO Y BASE DE PROPILENO DE 60 CM (INDUSTRIA HOSPITALARIA - ALIMENTARIA)', 75.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 67.50, 63.75, NULL, NULL, '1', 1, 63.75, 0.00, 0.00, 75.00, 'IMPLE-027', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (145, NULL, 'LETRERO PREVENTIVO \"CUIDADO PISO MOJADO\"', 25.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 22.50, 21.25, NULL, NULL, '1', 1, 21.25, 0.00, 0.00, 25.00, 'IMPLE-028', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (146, NULL, 'LETRERO PREVENTIVO EN FORMA DE CONO \"CUIDADO PISO MOJADO\"', 45.00, 0.00, -2, 0, 12, 1, '1000-01-01', '', '0', '0', 40.50, 38.25, NULL, NULL, '1', 1, 38.25, 0.00, 0.00, 45.00, 'IMPLE-029', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (147, NULL, 'LIMPIADOR DE LUNAS DE 35 CM C/BASE DE METAL P/EXTENSI├ôN TELESC├ôPICA', 41.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 37.35, 35.27, NULL, NULL, '1', 1, 35.27, 0.00, 0.00, 41.50, 'IMPLE-030', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (148, NULL, 'LIMPIADOR DE LUNAS DE 40 CM C/BASE DE METAL P/EXTENSI├ôN TELESC├ôPICA', 47.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 42.75, 40.38, NULL, NULL, '1', 1, 40.38, 0.00, 0.00, 47.50, 'IMPLE-031', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (149, NULL, 'LIMPIADOR DE LUNAS DE 45 CM C/BASE DE METAL P/EXTENSI├ôN TELESC├ôPICA', 53.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 48.15, 45.47, NULL, NULL, '1', 1, 45.47, 0.00, 0.00, 53.50, 'IMPLE-032', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (150, NULL, 'LIMPIADOR DE LUNAS DUAL P/EXTENSI├ôN TELESC├ôPICA (HUMEDECEDOR Y JALADOR DE GOMA)', 53.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 48.15, 45.47, NULL, NULL, '1', 1, 45.47, 0.00, 0.00, 53.50, 'IMPLE-033', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (151, NULL, 'MOPA DE BARRIDO DE ALGOD├ôN DE 60 CM C/MANGO DE ALUMINIO (COMPLETO)', 36.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 32.85, 31.02, NULL, NULL, '1', 1, 31.02, 0.00, 0.00, 36.50, 'IMPLE-034', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (152, NULL, 'MOPA DE BARRIDO DE ALGOD├ôN DE 60 CM (REPUESTO)', 25.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 22.50, 21.25, NULL, NULL, '1', 1, 21.25, 0.00, 0.00, 25.00, 'IMPLE-035', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (153, NULL, 'MOPA DE BARRIDO DE ALGOD├ôN DE 90 CM C/MANGO DE ALUMINIO (COMPLETO)', 58.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 52.20, 49.30, NULL, NULL, '1', 1, 49.30, 0.00, 0.00, 58.00, 'IMPLE-036', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (154, NULL, 'MOPA DE BARRIDO DE ALGOD├ôN DE 90 CM (REPUESTO)', 46.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 41.40, 39.10, NULL, NULL, '1', 1, 39.10, 0.00, 0.00, 46.00, 'IMPLE-037', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (155, NULL, 'MOPA DE BARRIDO DE ALGOD├ôN DE 110 CM C/MANGO DE ALUMINIO (COMPLETO)', 79.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 71.10, 67.15, NULL, NULL, '1', 1, 67.15, 0.00, 0.00, 79.00, 'IMPLE-038', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (156, NULL, 'MOPA DE BARRIDO DE ALGOD├ôN DE 110 CM (REPUESTO)', 67.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 60.75, 57.38, NULL, NULL, '1', 1, 57.38, 0.00, 0.00, 67.50, 'IMPLE-039', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (157, NULL, 'MOPA DE BARRIDO ACR├ìLICO DE 60 CM C/MANGO DE ACERO INOX (COMPLETO)', 51.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 45.90, 43.35, NULL, NULL, '1', 1, 43.35, 0.00, 0.00, 51.00, 'IMPLE-040', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (158, NULL, 'MOPA DE BARRIDO ACR├ìLICO DE 60 CM (REPUESTO)', 28.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 25.65, 24.22, NULL, NULL, '1', 1, 24.22, 0.00, 0.00, 28.50, 'IMPLE-041', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (159, NULL, 'MOPA DE BARRIDO ACR├ìLICO DE 90 CM C/MANGO DE ACERO INOX (COMPLETO)', 72.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 64.80, 61.20, NULL, NULL, '1', 1, 61.20, 0.00, 0.00, 72.00, 'IMPLE-042', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (160, NULL, 'MOPA DE BARRIDO ACR├ìLICO DE 90 CM (REPUESTO)', 39.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 35.10, 33.15, NULL, NULL, '1', 1, 33.15, 0.00, 0.00, 39.00, 'IMPLE-043', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (161, NULL, 'MOPA DE BARRIDO ACR├ìLICO DE 110 CM C/MANGO DE ACERO INOX (COMPLETO)', 95.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 85.95, 81.18, NULL, NULL, '1', 1, 81.18, 0.00, 0.00, 95.50, 'IMPLE-044', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (162, NULL, 'MOPA DE BARRIDO ACR├ìLICO DE 110 CM (REPUESTO)', 51.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 45.90, 43.35, NULL, NULL, '1', 1, 43.35, 0.00, 0.00, 51.00, 'IMPLE-045', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (163, NULL, 'MOPA DE BARRIDO DE MICROFIBRA DE 60 CM C/MANGO DE ALUMINIO (COMPLETO)', 46.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 41.40, 39.10, NULL, NULL, '1', 1, 39.10, 0.00, 0.00, 46.00, 'IMPLE-046', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (164, NULL, 'MOPA DE BARRIDO DE MICROFIBRA DE 60 CM (REPUESTO)', 25.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 22.50, 21.25, NULL, NULL, '1', 1, 21.25, 0.00, 0.00, 25.00, 'IMPLE-047', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (165, NULL, 'MOPA DE BARRIDO DE MICROFIBRA DE 90 CM C/MANGO DE ALUMINIO (COMPLETO)', 67.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 60.75, 57.38, NULL, NULL, '1', 1, 57.38, 0.00, 0.00, 67.50, 'IMPLE-048', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (166, NULL, 'MOPA DE BARRIDO DE MICROFIBRA DE 90 CM (REPUESTO)', 46.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 41.40, 39.10, NULL, NULL, '1', 1, 39.10, 0.00, 0.00, 46.00, 'IMPLE-049', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (167, NULL, 'MOPA DE BARRIDO DE MICROFIBRA DE 110 CM C/MANGO DE ALUMINIO (COMPLETO)', 88.50, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 79.65, 75.22, NULL, NULL, '1', 1, 75.22, 0.00, 0.00, 88.50, 'IMPLE-050', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (168, NULL, 'MOPA DE BARRIDO DE MICROFIBRA DE 110 CM (REPUESTO)', 72.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 64.80, 61.20, NULL, NULL, '1', 1, 61.20, 0.00, 0.00, 72.00, 'IMPLE-051', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (169, NULL, 'MOPA PLANTA DE MICROFIBRA DE 69 CM (PALO CON SUJETADOR) (MOJADO O SECO)', 95.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 85.50, 80.75, NULL, NULL, '1', 1, 80.75, 0.00, 0.00, 95.00, 'IMPLE-052', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (170, NULL, 'MOPA PLANTA DE MICROFIBRA DE 69 CM P/ PISO MOJADO O SECO - (REPUESTO) (COLOR: AZUL)', 35.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 31.50, 29.75, NULL, NULL, '1', 1, 29.75, 0.00, 0.00, 35.00, 'IMPLE-053', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (171, NULL, 'MOPA PLANTA DE MICROFIBRA DE 69 CM P/ PISO MOJADO O SECO - (REPUESTO) (COLOR: ROJO)', 35.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 31.50, 29.75, NULL, NULL, '1', 1, 29.75, 0.00, 0.00, 35.00, 'IMPLE-054', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (172, NULL, 'MOPA PLANTA DE MICROFIBRA DE 69 CM P/ PISO MOJADO O SECO - (REPUESTO) (COLOR: VERDE)', 35.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 31.50, 29.75, NULL, NULL, '1', 1, 29.75, 0.00, 0.00, 35.00, 'IMPLE-055', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (173, NULL, 'BASE DE TRAPEADOR MECH├ôN IMPORTADO C/ MANGO DE ALUMINIO 120 CM (COLOR: AMARILLO)', 25.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 22.50, 21.25, NULL, NULL, '1', 1, 21.25, 0.00, 0.00, 25.00, 'IMPLE-056', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (174, NULL, 'BASE DE TRAPEADOR MECH├ôN IMPORTADO C/ MANGO DE ALUMINIO 120 CM (COLOR: AZUL)', 25.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 22.50, 21.25, NULL, NULL, '1', 1, 21.25, 0.00, 0.00, 25.00, 'IMPLE-057', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (175, NULL, 'BASE DE TRAPEADOR MECH├ôN IMPORTADO C/ MANGO DE ALUMINIO 120 CM (COLOR: ROJO)', 25.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 22.50, 21.25, NULL, NULL, '1', 1, 21.25, 0.00, 0.00, 25.00, 'IMPLE-058', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (176, NULL, 'BASE DE TRAPEADOR MECH├ôN IMPORTADO C/ MANGO DE ALUMINIO 120 CM (COLOR: VERDE)', 25.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 22.50, 21.25, NULL, NULL, '1', 1, 21.25, 0.00, 0.00, 25.00, 'IMPLE-059', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (177, NULL, 'BASE DE TRAPEADOR MECH├ôN NACIONAL C/ MANGO DE HIERRO REVESTIDO CON PINTURA URETANO 120 CM (COLOR: AMARILLO)', 28.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 25.20, 23.80, NULL, NULL, '1', 1, 23.80, 0.00, 0.00, 28.00, 'IMPLE-060', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (178, NULL, 'BASE DE TRAPEADOR MECH├ôN NACIONAL C/ MANGO DE HIERRO REVESTIDO CON PINTURA URETANO 120 CM (COLOR: AZUL)', 28.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 25.20, 23.80, NULL, NULL, '1', 1, 23.80, 0.00, 0.00, 28.00, 'IMPLE-061', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (179, NULL, 'BASE DE TRAPEADOR MECH├ôN NACIONAL C/ MANGO DE HIERRO REVESTIDO CON PINTURA URETANO 120 CM (COLOR: ROJO)', 28.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 25.20, 23.80, NULL, NULL, '1', 1, 23.80, 0.00, 0.00, 28.00, 'IMPLE-062', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (180, NULL, 'BASE DE TRAPEADOR MECH├ôN NACIONAL C/ MANGO DE HIERRO REVESTIDO CON PINTURA URETANO 120 CM (COLOR: VERDE)', 28.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 25.20, 23.80, NULL, NULL, '1', 1, 23.80, 0.00, 0.00, 28.00, 'IMPLE-063', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (181, NULL, 'BASE DE TRAPEADOR MECH├ôN NACIONAL C/ MANGO DE HIERRO REVESTIDO CON PINTURA URETANO 140 CM (COLOR: AMARILLO)', 33.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 29.70, 28.05, NULL, NULL, '1', 1, 28.05, 0.00, 0.00, 33.00, 'IMPLE-064', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (182, NULL, 'prueba', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '123', '0', '0', 0.00, 0.00, '', '', '0', 1, 0.00, 0.00, 0.00, 0.00, '12234646', NULL, 'prueb', 5, NULL, '', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (183, NULL, 'prueba', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '123', '0', '0', 0.00, 0.00, '', '', '0', 1, 0.00, 0.00, 0.00, 0.00, '0', NULL, 'qqwewq', 5, NULL, '6', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (184, NULL, 'EDU', 1.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 2.00, 2.00, '', '', '0', 1, 2.00, 0.00, 0.00, 1.00, 'EDU1', NULL, 'SAD\r\nDAS', 17, NULL, 'undefined', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (185, NULL, 'sdfsdvds', 100.00, 90.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 45.00, 40.00, '', '', '0', 1, 40.00, 0.00, 0.00, 100.00, '2', NULL, 'fsdsdfdf', 0, NULL, 'undefined', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (186, '', 'CVCVXB', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '1', '1', '0', 1, 0.00, 0.00, 0.00, 0.00, '0', '', 'CBVBCVC', 17, NULL, '', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (187, NULL, 'FFDGCV', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '', '', '0', 1, 0.00, 0.00, 0.00, 0.00, '0', NULL, '', 17, NULL, '', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (188, NULL, 'ngfhngfddfgn', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '', '', '0', 1, 0.00, 0.00, 0.00, 0.00, 'IMPLE 54', NULL, 'gfngf', 7, NULL, '', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (189, '', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 15', 100.00, 90.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 150.00, 90.00, '1', '1', '0', 1, 90.00, 0.00, 0.00, 100.00, 'PRUE 001', 'hola231.jpg', 'MARCA: CRIS-TAURO\r\nModelo: TD-12N Potencia de motor: 1.5 HP Voltaje / Frecuencia: 220 V/60 Hz\r\nVelocidad de Rotaci├│n: 175 RPM. Motor: KDS del Grupo Imperial Estructura en Acero Inoxidable Anticorrosivo Base de Motor en Aluminio Fundido anticorrosivo Plato en Acero Inoxidable (calidad 304) de 12\" Cable Vulcanizado Homologado de 3x14: 15 metros Incluye: Cepillo de\r\nLavar de 11\" y Cepillo de Lustrar de 11\"', 0, NULL, '', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (190, '', 'HOLA ESTE ES UN PRODUCTO', 30.00, 20.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 10.00, 9.00, '1', '1', '0', 1, 9.00, 0.00, 0.00, 30.00, '001', '', 'diuwscbuisdfbujbefv', 0, NULL, '5', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (191, '', 'dgfva', 2183.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', NULL, NULL, '1', '1', '0', 1, NULL, 0.00, 0.00, 2183.00, '001', '', 'null', 0, NULL, '5', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (192, '', 'ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 12 GALONES - MARCA: CRIS-TAURO', 110.00, 110.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 110.00, 110.00, '1', '1', '0', 1, 110.00, 0.00, 0.00, 110.00, 'CEP -001', '', '\r\nModelo: AD-25G Motor doble: 1200W - 60Hz / 18000 RPM Tanque: Fibra de\r\nvidrio de 15 Galones Cable: Vulcanizado x 15 metros INCLUYE: Kit completo de\r\naccesorios\r\n\r\nModelo: AD-25G Motor doble: 1200W - 60Hz / 18000 RPM Tanque: Fibra de\r\nvidrio de 15 Galones Cable: Vulcanizado x 15 metros INCLUYE: Kit completo de\r\naccesorios\r\n\r\nModelo: AD-25G Motor doble: 1200W - 60Hz / 18000 RPM Tanque: Fibra de\r\nvidrio de 15 Galones Cable: Vulcanizado x 15 metros INCLUYE: Kit completo de\r\naccesorios\r\n', 0, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (193, 'JVC-016-1', 'ABRILLANTADORA INDUSTRIAL DE PISOS Y VINILES DE 20\" - MARCA MASTER GOLDS', 6490.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 0.00, 0.00, '1', '1', '1', 1, 0.00, 0.00, 0.00, 6490.00, 'JVC-016-1', NULL, 'Modelo: AMG-1500C \r\nMotor: 2.0 HP Inducido \r\nEstructura: Acero Plato en Acero de 20\".\r\nCable: Vulcanizado x 10 metros. \r\nIncluye: Porta Pad 20\" y Disco Pad 20\".', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (194, 'JVC-037-1', 'LIMPIADOR Y SECADOR DE ALFOMBRAS (AZUL) - MARCA: MASTER GOLDS', 6490.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 5841.00, 5516.50, '1', '1', '1', 1, 5516.50, 0.00, 0.00, 6490.00, 'JVC-037-1', NULL, 'Modelo: LIE-J4-A \r\nMotor: 3290W \r\nMotor de succi├│n: 1000W \r\nTanque de agua limpia: 20 LT\r\nTanque de agua residual: 18 LT\r\nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (195, 'JVC-037-2', 'LIMPIADOR Y SECADOR DE ALFOMBRAS (PLOMO) - MARCA: MASTER GOLDS', 7552.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 6796.80, 6419.20, '1', '1', '1', 1, 6419.20, 0.00, 0.00, 7552.00, 'JVC-037-2', NULL, 'Modelo: LAMG \r\nMotor: 3290W \r\nMotor de succi├│n: 1000W \r\nTanque de agua limpia: 20 LT\r\nTanque de agua residual: 18 LT\r\nIncluye: Kit de accesorios', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (196, 'JVC-038-1', 'LIMPIADOR INDUSTRIAL DE ESCALERAS EL├ëCTRICAS (CON CABLE) - MARCA: MASTER GOLDS', 6962.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 6265.80, 5917.70, '1', '1', '1', 1, 5917.70, 0.00, 0.00, 6962.00, 'JVC-038-1', NULL, 'Modelo: MLE-SC450 \nMotor: 1000W \nVoltaje: 220-240V/60Hz \nAncho de trabajo: 450mm \nCapacidad: 20 LT\nCable vulcanizado', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (197, 'JVC-038-2', 'LIMPIADOR INDUSTRIAL DE ESCALERAS EL├ëCTRICAS (A BATER├ìA) - MARCA: MASTER GOLDS', 8142.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 7327.80, 6920.70, '1', '1', '1', 1, 6920.70, 0.00, 0.00, 8142.00, 'JVC-038-2', NULL, 'Modelo: MLE-SC450D \nEnerg├¡a: 500W \nVoltaje: 24V \nAncho de trabajo: 450mm \nCapacidad: 20L \nBater├¡a: 2 x 12V \nHoras de trabajo: 2 horas aprox.', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (198, NULL, 'BARREDORA INDUSTRIAL (HOMBRE ANDANTE) CAPACIDAD 45 LT - MASTER GOLDS', 11210.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 10089.00, 9528.50, '1', '1', '1', 1, 9528.50, 0.00, 0.00, 11210.00, 'JVC-045-1', NULL, '', 6, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (199, 'JVC-054-1', 'LAVADORA SECADORA PROFESIONAL DE PISOS CON MOTOR DE TRACCI├ôN DE 19\" (HOMBRE ANDANTE) - MARCA: TVX', 7734.90, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 6961.41, 6574.66, '1', '1', '1', 1, 6574.66, 0.00, 0.00, 7734.90, 'JVC-054-1', '1755180524_FOTOS MAQUINAS JVC LIMPIAS (48).jpg', 'Modelo: T55BT \r\nBater├¡a : 2 x 12V \r\nCapacidad de trabajo: 2250 m2/h \r\nAncho de ├írea de trabajo: 510 mm \r\nCapacidad de tanque de soluci├│n: 55L \r\nCapacidad de tanque de recuperaci├│n: 65L \r\nIncluye: Cepillo y Porta Pad de 19\" ', 22, NULL, '14', 'USD', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (200, 'JVC-054-2', 'LAVADORA SECADORA PROFESIONAL DE PISOS CON MOTOR DE TRACCI├ôN DE 21\" (HOMBRE A BORDO) - MARCA: TVX', 10413.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 9372.15, 8851.48, '1', '1', '1', 1, 8851.48, 0.00, 0.00, 10413.50, 'JVC-054-2', '1755180678_FOTOS MAQUINAS JVC LIMPIAS (49).jpg', 'Modelo: T90 \r\nBater├¡a : 2 x 12V \r\nCapacidad de trabajo: 2800 m2/h \r\nAncho de ├írea de trabajo: 560 mm \r\nCapacidad de tanque de soluci├│n: 90L \r\nCapacidad de tanque de recuperaci├│n: 100L \r\nIncluye: Cepillo y Porta Pad de 21\" ', 22, NULL, '14', 'USD', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (201, 'JVC-054-3', 'LAVADORA SECADORA PROFESIONAL DE PISOS DE 16\" DOBLE (HOMBRE A BORDO) - MARCA: TVX', 15133.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 13620.15, 12863.47, '1', '1', '1', 1, 12863.47, 0.00, 0.00, 15133.50, 'JVC-054-3', '1755181574_FOTOS MAQUINAS JVC LIMPIAS (50).jpg', 'Modelo: T130 (DOBLE CEPILLO) \r\nBater├¡a : 24V / 200AH \r\nCapacidad de trabajo: 5590 m2/h \r\nAncho de ├írea de trabajo: 860 mm \r\nCapacidad de tanque de soluci├│n: 120L \r\nCapacidad de tanque de recuperaci├│n: 130L \r\nIncluye: 2 cepillos y Porta Pad de 16\"', 22, NULL, '14', 'USD', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (202, 'JVC-054-4', 'LAVADORA SECADORA PROFESIONAL DE PISOS DE 16\" DOBLE (HOMBRE A BORDO) - MARCA: TVX', 18673.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 16806.15, 15872.47, '1', '1', '1', 1, 15872.47, 0.00, 0.00, 18673.50, 'JVC-054-4', '1755181764_FOTOS MAQUINAS JVC LIMPIAS (51).jpg', 'Modelo: T150 (DOBLE CEPILLO) \r\nBater├¡a : 3 x 12V\r\nCapacidad de trabajo: 5590 m2/h \r\nAncho de ├írea de trabajo: 860 mm \r\nCapacidad de tanque de soluci├│n: 150L \r\nCapacidad de tanque de recuperaci├│n: 170L \r\nIncluye: Cepillo y Porta Pad de 16\"', 22, NULL, '14', 'USD', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (203, NULL, 'BASE DE TRAPEADOR MECH├ôN NACIONAL C/ MANGO DE HIERRO REVESTIDO CON PINTURA URETANO 140 CM (COLOR: AZUL)', 33.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 29.70, 28.05, NULL, NULL, '1', 1, 28.05, 0.00, 0.00, 33.00, 'IMPLE-065', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (204, NULL, 'BASE DE TRAPEADOR MECH├ôN NACIONAL C/ MANGO DE HIERRO REVESTIDO CON PINTURA URETANO 140 CM (COLOR: ROJO)', 33.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 29.70, 28.05, NULL, NULL, '1', 1, 28.05, 0.00, 0.00, 33.00, 'IMPLE-066', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (205, NULL, 'BASE DE TRAPEADOR MECH├ôN NACIONAL C/ MANGO DE HIERRO REVESTIDO CON PINTURA URETANO 140 CM (COLOR: VERDE)', 33.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 29.70, 28.05, NULL, NULL, '1', 1, 28.05, 0.00, 0.00, 33.00, 'IMPLE-067', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (206, NULL, 'RECOGEDOR Y ESCOBA LOBBY (COMPLETO)', 41.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 37.35, 35.27, NULL, NULL, '1', 1, 35.27, 0.00, 0.00, 41.50, 'IMPLE-068', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (207, NULL, 'TRAPEADOR MECH├ôN DE 500 GR', 8.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 7.20, 6.80, NULL, NULL, '1', 1, 6.80, 0.00, 0.00, 8.00, 'IMPLE-069', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (208, NULL, 'TRAPEADOR MECH├ôN BLANCO DE 450 GR (IMPORTADO)', 16.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 14.40, 13.60, NULL, NULL, '1', 1, 13.60, 0.00, 0.00, 16.00, 'IMPLE-070', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (209, NULL, 'TRAPEADOR MECH├ôN H├ÜMEDO 50% ANTIBACTERIAL DE 350 GR H├ÜMEDO (IMPORTADO) (COLOR: AZUL)', 16.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 14.85, 14.02, NULL, NULL, '1', 1, 14.02, 0.00, 0.00, 16.50, 'IMPLE-071', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (210, NULL, 'TRAPEADOR MECH├ôN H├ÜMEDO 50% ANTIBACTERIAL DE 350 GR H├ÜMEDO (IMPORTADO) (COLOR: ROJO)', 16.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 14.85, 14.02, NULL, NULL, '1', 1, 14.02, 0.00, 0.00, 16.50, 'IMPLE-072', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (211, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 10\" (NACIONAL)', 100.24, 25.00, 10, 0, 12, 1, '1000-01-01', '0', '0', '0', 90.22, 85.20, '1', '1', '1', 1, 85.20, 0.00, 0.00, 100.24, 'CEP-001', NULL, '', 0, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (212, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 11\" (NACIONAL)', 101.80, 25.00, 1, 0, 12, 1, '1000-01-01', '0', '0', '0', 91.62, 86.53, NULL, NULL, '1', 1, 86.53, 0.00, 0.00, 101.80, 'CEP-002', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (213, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 12\" (NACIONAL)', 104.91, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 94.42, 89.18, NULL, NULL, '1', 1, 89.18, 0.00, 0.00, 104.91, 'CEP-003', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (214, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 13\" (NACIONAL)', 109.46, 0.00, -3, 0, 12, 1, '1000-01-01', '0', '0', '0', 98.51, 93.04, NULL, NULL, '1', 1, 93.04, 0.00, 0.00, 109.46, 'CEP-004', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (215, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 14\" (NACIONAL)', 112.57, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 101.31, 95.69, NULL, NULL, '1', 1, 95.69, 0.00, 0.00, 112.57, 'CEP-005', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (216, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 15\" (NACIONAL)', 117.11, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 105.40, 99.55, NULL, NULL, '1', 1, 99.55, 0.00, 0.00, 117.11, 'CEP-006', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (217, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 16\" (NACIONAL)', 121.79, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 109.61, 103.52, NULL, NULL, '1', 1, 103.52, 0.00, 0.00, 121.79, 'CEP-007', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (218, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 17\" (NACIONAL)', 130.87, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 117.79, 111.24, NULL, NULL, '1', 1, 111.24, 0.00, 0.00, 130.87, 'CEP-008', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (219, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 18\" (NACIONAL)', 135.55, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 121.99, 115.21, NULL, NULL, '1', 1, 115.21, 0.00, 0.00, 135.55, 'CEP-009', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (220, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 19\" (NACIONAL)', 146.19, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 131.57, 124.26, NULL, NULL, '1', 1, 124.26, 0.00, 0.00, 146.19, 'CEP-010', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (221, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 20\" (NACIONAL)', 153.85, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 138.46, 130.77, NULL, NULL, '1', 1, 130.77, 0.00, 0.00, 153.85, 'CEP-011', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (222, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 21\" (NACIONAL)', 158.52, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 142.67, 134.74, NULL, NULL, '1', 1, 134.74, 0.00, 0.00, 158.52, 'CEP-012', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (223, NULL, 'CEPILLO PARA LAVAR PISO C/ BRAQUETA DE 22\" (NACIONAL)', 161.51, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 145.36, 137.28, NULL, NULL, '1', 1, 137.28, 0.00, 0.00, 161.51, 'CEP-013', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (224, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 11\" (NACIONAL)', 100.24, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 90.22, 85.20, NULL, NULL, '1', 1, 85.20, 0.00, 0.00, 100.24, 'CEP-014', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (225, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 12\" (NACIONAL)', 101.80, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 91.62, 86.53, NULL, NULL, '1', 1, 86.53, 0.00, 0.00, 101.80, 'CEP-015', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (226, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 13\" (NACIONAL)', 104.91, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 94.42, 89.18, NULL, NULL, '1', 1, 89.18, 0.00, 0.00, 104.91, 'CEP-016', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (227, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 14\" (NACIONAL)', 109.46, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 98.51, 93.04, NULL, NULL, '1', 1, 93.04, 0.00, 0.00, 109.46, 'CEP-017', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (228, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 15\" (NACIONAL)', 112.57, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 101.31, 95.69, NULL, NULL, '1', 1, 95.69, 0.00, 0.00, 112.57, 'CEP-018', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (229, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 16\" (NACIONAL)', 117.11, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 105.40, 99.55, NULL, NULL, '1', 1, 99.55, 0.00, 0.00, 117.11, 'CEP-019', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (230, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 17\" (NACIONAL)', 121.79, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 109.61, 103.52, NULL, NULL, '1', 1, 103.52, 0.00, 0.00, 121.79, 'CEP-020', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (231, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 18\" (NACIONAL)', 130.87, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 117.79, 111.24, NULL, NULL, '1', 1, 111.24, 0.00, 0.00, 130.87, 'CEP-021', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (232, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 19\" (NACIONAL)', 135.55, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 121.99, 115.21, NULL, NULL, '1', 1, 115.21, 0.00, 0.00, 135.55, 'CEP-022', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (233, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 20\" (NACIONAL)', 146.19, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 131.57, 124.26, NULL, NULL, '1', 1, 124.26, 0.00, 0.00, 146.19, 'CEP-023', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (234, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 21\" (NACIONAL)', 153.85, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 138.46, 130.77, NULL, NULL, '1', 1, 130.77, 0.00, 0.00, 153.85, 'CEP-024', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (235, NULL, 'CEPILLO PARA LUSTRAR PISO C/ BRAQUETA DE 22\" (NACIONAL)', 158.52, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 142.67, 134.74, NULL, NULL, '1', 1, 134.74, 0.00, 0.00, 158.52, 'CEP-025', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (236, NULL, 'CEPILLO PARA LAVAR PISOS C/ BRAQUETA DE 13\" (IMPORTADO)', 147.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 132.75, 125.38, NULL, NULL, '1', 1, 125.38, 0.00, 0.00, 147.50, 'CEPI-001', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (237, NULL, 'CEPILLO PARA LAVAR PISOS C/ BRAQUETA DE 15\" (IMPORTADO)', 160.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 143.37, 135.41, NULL, NULL, '1', 1, 135.41, 0.00, 0.00, 160.00, 'CEPI-002', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (238, NULL, 'CEPILLO PARA LAVAR PISOS C/ BRAQUETA DE 16\" (IMPORTADO)', 171.10, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 153.99, 145.44, NULL, NULL, '1', 1, 145.44, 0.00, 0.00, 171.10, 'CEPI-003', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (239, NULL, 'CEPILLO PARA LAVAR PISOS C/ BRAQUETA DE 17\" (IMPORTADO)', 182.90, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 164.61, 155.47, NULL, NULL, '1', 1, 155.47, 0.00, 0.00, 182.90, 'CEPI-004', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (240, NULL, 'CEPILLO PARA LAVAR PISOS C/ BRAQUETA DE 18\" (IMPORTADO)', 194.70, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 175.23, 165.50, NULL, NULL, '1', 1, 165.50, 0.00, 0.00, 194.70, 'CEPI-005', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (241, NULL, 'CEPILLO PARA LAVAR PISOS C/ BRAQUETA DE 19\" (IMPORTADO)', 194.70, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 175.23, 165.50, NULL, NULL, '1', 1, 165.50, 0.00, 0.00, 194.70, 'CEPI-006', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (242, NULL, 'CEPILLO PARA LAVAR PISOS C/ BRAQUETA DE 13\" (FREGADORA ADVANCE)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'CEPI-007', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (243, NULL, 'CEPILLO PARA LAVAR PISOS C/ BRAQUETA DE 13\" (FREGADORA TENNANT)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'CEPI-008', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (244, NULL, 'CEPILLO PARA LAVAR PISOS C/ BRAQUETA DE 16\" (FREGADORA TENNANT)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'CEPI-009', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (245, NULL, 'CEPILLO PARA LAVAR PISOS C/ BRAQUETA DE 14\" (FREGADORA VIPER)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'CEPI-010', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (246, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 13\" (AZUL)', 112.10, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 100.89, 95.28, NULL, NULL, '1', 1, 95.28, 0.00, 0.00, 112.10, 'CEPIR-001', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (247, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 13\" (ROJO)', 112.10, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 100.89, 95.28, NULL, NULL, '1', 1, 95.28, 0.00, 0.00, 112.10, 'CEPIR-002', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (248, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 13\" (VERDE)', 112.10, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 100.89, 95.28, NULL, NULL, '1', 1, 95.28, 0.00, 0.00, 112.10, 'CEPIR-003', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (249, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 14\" (AZUL)', 123.90, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 111.51, 105.31, NULL, NULL, '1', 1, 105.31, 0.00, 0.00, 123.90, 'CEPIR-004', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (250, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 14\" (ROJO)', 123.90, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 111.51, 105.31, NULL, NULL, '1', 1, 105.31, 0.00, 0.00, 123.90, 'CEPIR-005', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (251, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 14\" (VERDE)', 123.90, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 111.51, 105.31, NULL, NULL, '1', 1, 105.31, 0.00, 0.00, 123.90, 'CEPIR-006', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (252, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 15\" (AZUL)', 135.70, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 122.13, 115.34, NULL, NULL, '1', 1, 115.34, 0.00, 0.00, 135.70, 'CEPIR-007', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (253, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 15\" (ROJO)', 135.70, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 122.13, 115.34, NULL, NULL, '1', 1, 115.34, 0.00, 0.00, 135.70, 'CEPIR-008', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (254, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 15\" (VERDE)', 135.70, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 122.13, 115.34, NULL, NULL, '1', 1, 115.34, 0.00, 0.00, 135.70, 'CEPIR-009', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (255, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 16\" (AZUL)', 147.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 132.75, 125.38, NULL, NULL, '1', 1, 125.38, 0.00, 0.00, 147.50, 'CEPIR-010', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (256, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 16\" (ROJO)', 147.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 132.75, 125.38, NULL, NULL, '1', 1, 125.38, 0.00, 0.00, 147.50, 'CEPIR-011', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (257, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 16\" (VERDE)', 147.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 132.75, 125.38, NULL, NULL, '1', 1, 125.38, 0.00, 0.00, 147.50, 'CEPIR-012', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (258, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 17\" (AZUL)', 159.30, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 143.37, 135.41, NULL, NULL, '1', 1, 135.41, 0.00, 0.00, 159.30, 'CEPIR-013', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (259, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 17\" (ROJO)', 159.30, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 143.37, 135.41, NULL, NULL, '1', 1, 135.41, 0.00, 0.00, 159.30, 'CEPIR-014', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (260, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 17\" (VERDE)', 159.30, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 143.37, 135.41, NULL, NULL, '1', 1, 135.41, 0.00, 0.00, 159.30, 'CEPIR-015', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (261, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 18\" (AZUL)', 171.10, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 153.99, 145.44, NULL, NULL, '1', 1, 145.44, 0.00, 0.00, 171.10, 'CEPIR-016', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (262, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 18\" (ROJO)', 171.10, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 153.99, 145.44, NULL, NULL, '1', 1, 145.44, 0.00, 0.00, 171.10, 'CEPIR-017', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (263, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 18\" (VERDE)', 171.10, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 153.99, 145.44, NULL, NULL, '1', 1, 145.44, 0.00, 0.00, 171.10, 'CEPIR-018', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (264, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 19\" (AZUL)', 182.90, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 164.61, 155.47, NULL, NULL, '1', 1, 155.47, 0.00, 0.00, 182.90, 'CEPIR-019', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (265, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 19\" (ROJO)', 182.90, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 164.61, 155.47, NULL, NULL, '1', 1, 155.47, 0.00, 0.00, 182.90, 'CEPIR-020', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (266, NULL, 'CEPILLO PARA LAVAR ALFOMBRA RANURADO NACIONAL DE 19\" (VERDE)', 182.90, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 164.61, 155.47, NULL, NULL, '1', 1, 155.47, 0.00, 0.00, 182.90, 'CEPIR-021', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (267, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR BLANCO DE 14\" LIMKIT CLEANER', 28.32, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 25.49, 24.07, NULL, NULL, '1', 1, 24.07, 0.00, 0.00, 28.32, 'PAD-001', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (268, NULL, 'DISCO PAD PARA LAVADO PROFUNDO DECAPADO COLOR MARRON DE 14\" LIMKIT CLEANER', 28.32, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 25.49, 24.07, NULL, NULL, '1', 1, 24.07, 0.00, 0.00, 28.32, 'PAD-002', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (269, NULL, 'DISCO PAD PARA LAVADO PROFUNDO COLOR NEGRO DE 14\" LIMKIT CLEANER', 28.32, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 25.49, 24.07, NULL, NULL, '1', 1, 24.07, 0.00, 0.00, 28.32, 'PAD-003', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (270, NULL, 'DISCO PAD PARA LAVAR Y/O ABRILLANTAR COLOR ROJO DE 14\" LIMKIT CLEANER', 28.32, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 25.49, 24.07, NULL, NULL, '1', 1, 24.07, 0.00, 0.00, 28.32, 'PAD-004', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (271, NULL, 'DISCO PAD PARA LAVAR COLOR VERDE 14\" LIMKIT CLEANER', 28.32, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 25.49, 24.07, NULL, NULL, '1', 1, 24.07, 0.00, 0.00, 28.32, 'PAD-005', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (272, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR BLANCO DE 15\" LIMKIT CLEANER', 31.86, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 28.67, 27.08, NULL, NULL, '1', 1, 27.08, 0.00, 0.00, 31.86, 'PAD-006', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (273, NULL, 'DISCO PAD PARA LAVADO PROFUNDO DECAPADO COLOR MARRON DE 15\" LIMKIT CLEANER', 31.86, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 28.67, 27.08, NULL, NULL, '1', 1, 27.08, 0.00, 0.00, 31.86, 'PAD-007', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (274, NULL, 'DISCO PAD PARA LAVADO PROFUNDO COLOR NEGRO DE 15\" LIMKIT CLEANER', 31.86, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 28.67, 27.08, NULL, NULL, '1', 1, 27.08, 0.00, 0.00, 31.86, 'PAD-008', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (275, NULL, 'DISCO PAD PARA LAVAR Y/O ABRILLANTAR COLOR ROJO DE 15\" LIMKIT CLEANER', 31.86, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 28.67, 27.08, NULL, NULL, '1', 1, 27.08, 0.00, 0.00, 31.86, 'PAD-009', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (276, NULL, 'DISCO PAD PARA LAVAR COLOR VERDE 15\" LIMKIT CLEANER', 31.86, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 28.67, 27.08, NULL, NULL, '1', 1, 27.08, 0.00, 0.00, 31.86, 'PAD-010', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (277, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR BLANCO DE 16\" LIMKIT CLEANER', 35.40, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 31.86, 30.09, NULL, NULL, '1', 1, 30.09, 0.00, 0.00, 35.40, 'PAD-011', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (278, NULL, 'DISCO PAD PARA LAVADO PROFUNDO DECAPADO COLOR MARRON DE 16\" LIMKIT CLEANER', 35.40, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 31.86, 30.09, NULL, NULL, '1', 1, 30.09, 0.00, 0.00, 35.40, 'PAD-012', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (279, NULL, 'DISCO PAD PARA LAVADO PROFUNDO COLOR NEGRO DE 16\" LIMKIT CLEANER', 35.40, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 31.86, 30.09, NULL, NULL, '1', 1, 30.09, 0.00, 0.00, 35.40, 'PAD-013', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (280, NULL, 'DISCO PAD PARA LAVAR Y/O ABRILLANTAR COLOR ROJO DE 16\" LIMKIT CLEANER', 35.40, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 31.86, 30.09, NULL, NULL, '1', 1, 30.09, 0.00, 0.00, 35.40, 'PAD-014', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (281, NULL, 'DISCO PAD PARA LAVAR COLOR VERDE 16\" LIMKIT CLEANER', 35.40, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 31.86, 30.09, NULL, NULL, '1', 1, 30.09, 0.00, 0.00, 35.40, 'PAD-015', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (282, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR BLANCO DE 17\" LIMKIT CLEANER', 38.94, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 35.05, 33.10, NULL, NULL, '1', 1, 33.10, 0.00, 0.00, 38.94, 'PAD-016', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (283, NULL, 'DISCO PAD PARA LAVADO PROFUNDO DECAPADO COLOR MARRON DE 17\" LIMKIT CLEANER', 38.94, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 35.05, 33.10, NULL, NULL, '1', 1, 33.10, 0.00, 0.00, 38.94, 'PAD-017', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (284, NULL, 'DISCO PAD PARA LAVADO PROFUNDO COLOR NEGRO DE 17\" LIMKIT CLEANER', 38.94, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 35.05, 33.10, NULL, NULL, '1', 1, 33.10, 0.00, 0.00, 38.94, 'PAD-018', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (285, NULL, 'DISCO PAD PARA LAVAR Y/O ABRILLANTAR COLOR ROJO DE 17\" LIMKIT CLEANER', 38.94, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 35.05, 33.10, NULL, NULL, '1', 1, 33.10, 0.00, 0.00, 38.94, 'PAD-019', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (286, NULL, 'DISCO PAD PARA LAVAR COLOR VERDE 17\" LIMKIT CLEANER', 38.94, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 35.05, 33.10, NULL, NULL, '1', 1, 33.10, 0.00, 0.00, 38.94, 'PAD-020', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (287, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR BLANCO DE 18\" LIMKIT CLEANER', 42.48, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 38.23, 36.11, NULL, NULL, '1', 1, 36.11, 0.00, 0.00, 42.48, 'PAD-021', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (288, NULL, 'DISCO PAD PARA LAVADO PROFUNDO DECAPADO COLOR MARRON DE 18\" LIMKIT CLEANER', 42.48, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 38.23, 36.11, NULL, NULL, '1', 1, 36.11, 0.00, 0.00, 42.48, 'PAD-022', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (289, NULL, 'DISCO PAD PARA LAVADO PROFUNDO COLOR NEGRO DE 18\" LIMKIT CLEANER', 42.48, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 38.23, 36.11, NULL, NULL, '1', 1, 36.11, 0.00, 0.00, 42.48, 'PAD-023', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (290, NULL, 'DISCO PAD PARA LAVAR Y/O ABRILLANTAR COLOR ROJO DE 18\" LIMKIT CLEANER', 42.48, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 38.23, 36.11, NULL, NULL, '1', 1, 36.11, 0.00, 0.00, 42.48, 'PAD-024', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (291, NULL, 'DISCO PAD PARA LAVAR COLOR VERDE 18\" LIMKIT CLEANER', 36.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 38.23, 36.11, NULL, NULL, '1', 1, 36.11, 0.00, 0.00, 36.00, 'PAD-025', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (292, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR BLANCO DE 20\" LIMKIT CLEANER', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 41.42, 39.12, NULL, NULL, '1', 1, 39.12, 0.00, 0.00, 46.02, 'PAD-026', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (293, NULL, 'DISCO PAD PARA LAVADO DECAPADO DE PISOS COLOR DORADO DE 20\" LIMKIT CLEANER', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 41.42, 39.12, NULL, NULL, '1', 1, 39.12, 0.00, 0.00, 46.02, 'PAD-027', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (294, NULL, 'DISCO PAD PARA LAVADO PROFUNDO DECAPADO COLOR MARRON DE 20\" LIMKIT CLEANER', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 41.42, 39.12, NULL, NULL, '1', 1, 39.12, 0.00, 0.00, 46.02, 'PAD-028', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (295, NULL, 'DISCO PAD PARA LAVADO PROFUNDO COLOR NEGRO DE 20\" LIMKIT CLEANER', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 41.42, 39.12, NULL, NULL, '1', 1, 39.12, 0.00, 0.00, 46.02, 'PAD-029', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (296, NULL, 'DISCO PAD PARA LAVAR Y/O ABRILLANTAR COLOR ROJO DE 20\" LIMKIT CLEANER', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 41.42, 39.12, NULL, NULL, '1', 1, 39.12, 0.00, 0.00, 46.02, 'PAD-030', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (297, NULL, 'DISCO PAD PARA LAVAR COLOR VERDE 20\" LIMKIT CLEANER', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 41.42, 39.12, NULL, NULL, '1', 1, 39.12, 0.00, 0.00, 46.02, 'PAD-031', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (298, NULL, 'DISCO PAD PARA LIMPIEZA Y ABRILLANTADO DE 20\" LIMKIT CLEANER', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 41.42, 39.12, NULL, NULL, '1', 1, 39.12, 0.00, 0.00, 46.02, 'PAD-032', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (299, NULL, 'DISCO PAD PARA LAVADO PROFUNDO DECAPADO COLOR MARRON DE 20\" LIMKIT CLEANER ', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 46.02, 'PAD-033', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (300, NULL, 'DISCO PAD PARA LAVADO PROFUNDO COLOR NEGRO DE 20\" LIMKIT CLEANER ', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 46.02, 'PAD-034', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (301, NULL, 'DISCO PAD PARA LAVAR Y/O ABRILLANTAR COLOR ROJO DE 20\" LIMKIT CLEANER ', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 46.02, 'PAD-035', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (302, NULL, 'DISCO PAD PARA LAVAR COLOR VERDE 20\" LIMKIT CLEANER ', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 46.02, 'PAD-036', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (303, NULL, 'DISCO PAD PARA LIMPIEZA Y ABRILLANTADO DE 20\" LIMKIT CLEANER', 46.02, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 46.02, 'PAD-037', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (304, NULL, 'DISCO PAD 3M DE 17\" COLOR: BLANCO', 58.48, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 58.48, 'PAD-038', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (305, NULL, 'DISCO PAD 3M DE 17\" COLOR: ROJO', 58.48, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 58.48, 'PAD-040', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (306, NULL, 'DISCO PAD 3M DE 20\" COLOR: BLANCO', 62.66, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 62.66, 'PAD-041', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (307, NULL, 'DISCO PAD 3M DE 20\" COLOR: ROJO', 62.66, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 62.66, 'PAD-043', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (308, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 10 \"', 111.40, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 100.26, 94.69, '1', '1', '1', 1, 94.69, 0.00, 0.00, 111.40, 'PORT-001', NULL, '', 0, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (309, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 11 \"', 112.96, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 101.67, 96.02, NULL, NULL, '1', 1, 96.02, 0.00, 0.00, 112.96, 'PORT-002', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (310, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 12 \"', 115.95, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 104.35, 98.55, NULL, NULL, '1', 1, 98.55, 0.00, 0.00, 115.95, 'PORT-003', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (311, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 13 \"', 120.62, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 108.56, 102.53, NULL, NULL, '1', 1, 102.53, 0.00, 0.00, 120.62, 'PORT-004', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (312, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 14 \"', 123.60, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 111.24, 105.06, '1', '1', '1', 1, 105.06, 0.00, 0.00, 123.60, 'PORT-005', '1755118149_banner-horizontal-geometrico-diseno-plano_23-2149968375.jpg', '', 0, NULL, 'null', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (313, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 15 \"', 126.72, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 114.05, 107.71, '1', '1', '1', 1, 107.71, 0.00, 0.00, 126.72, 'PORT-006', NULL, '', 0, NULL, 'null', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (314, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 16 \"', 132.82, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 119.54, 112.90, NULL, NULL, '1', 1, 112.90, 0.00, 0.00, 132.82, 'PORT-007', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (315, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 17 \"', 142.04, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 127.83, 120.73, NULL, NULL, '1', 1, 120.73, 0.00, 0.00, 142.04, 'PORT-008', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (316, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 18 \"', 146.58, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 131.92, 124.59, NULL, NULL, '1', 1, 124.59, 0.00, 0.00, 146.58, 'PORT-009', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (317, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 19 \"', 157.35, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 141.62, 133.75, NULL, NULL, '1', 1, 133.75, 0.00, 0.00, 157.35, 'PORT-010', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (318, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 20 \"', 165.01, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 148.51, 140.26, NULL, NULL, '1', 1, 140.26, 0.00, 0.00, 165.01, 'PORT-011', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (319, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 21 \"', 169.55, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 152.60, 144.12, NULL, NULL, '1', 1, 144.12, 0.00, 0.00, 169.55, 'PORT-012', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (320, NULL, 'PORTA PAD NACIONAL C/ BRAQUETA DE 22 \"', 172.67, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 155.40, 146.77, NULL, NULL, '1', 1, 146.77, 0.00, 0.00, 172.67, 'PORT-013', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (321, NULL, 'PORTA PAD IMPORTADO C/ BRAQUETA DE 15 \"', 165.20, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 148.68, 140.42, NULL, NULL, '1', 1, 140.42, 0.00, 0.00, 165.20, 'PORTI-001', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (322, NULL, 'PORTA PAD IMPORTADO C/ BRAQUETA DE 16 \"', 182.90, 0.00, 0, 0, 12, 1, '1000-01-01', '123', '0', '0', 164.61, 155.47, '1', '1', '1', 1, 155.47, 0.00, 0.00, 182.90, 'PORTI-002', '1755117222_banner-horizontal-geometrico-diseno-plano_23-2149968375.jpg', '', 0, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (323, NULL, 'PORTA PAD IMPORTADO C/ BRAQUETA DE 17 \"', 200.60, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 180.54, 170.51, '1', '1', '1', 1, 170.51, 0.00, 0.00, 200.60, 'PORTI-003', '1755117183_banner-horizontal-geometrico-diseno-plano_23-2149968375.jpg', '', 0, NULL, '15', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (324, NULL, 'PORTA PAD IMPORTADO C/ BRAQUETA DE 19 \"', 218.30, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 196.47, 185.55, NULL, NULL, '1', 1, 185.55, 0.00, 0.00, 218.30, 'PORTI-004', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (325, NULL, 'PORTA PAD IMPORTA C/ BRAQUETA P/ LUSTRADORA KARCHER MOD:BDS-43/180C DE 15\"', 188.80, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 169.92, 160.48, NULL, NULL, '1', 1, 160.48, 0.00, 0.00, 188.80, 'PORTI-005', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (326, NULL, 'PAPEL HIGIENICO JUMBO DE 550 METROS X 6 ROLLOS (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, '1', '1', '1', 1, 0.00, 0.00, 0.00, 0.00, 'PH-01', NULL, '', 0, NULL, '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (327, NULL, 'PAPEL HIGIENICO JUMBO DE 550 METROS X ROLLO (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PH-02', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (328, NULL, 'PAPEL HIGIENICO JUMBO DE 400 METROS X 6 ROLLOS (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PH-03', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (329, NULL, 'PAPEL HIGIENICO JUMBO DE 400 METROS X ROLLO (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PH-04', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (330, NULL, 'PAPEL TOALLA JUMBO DE 300 METROS X 2 ROLLOS (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PT-01', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (331, NULL, 'PAPEL TOALLA JUMBO DE 300 METROS X ROLLO (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PT-02', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (332, NULL, 'PAPEL TOALLA JUMBO DE 200 METROS X 2 ROLLOS (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PT-03', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (333, NULL, 'PAPEL TOALLA JUMBO DE 200 METROS X ROLLO (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PT-04', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (334, NULL, 'PAPEL TOALLA INTERFOLIADO X CAJA DE 20 PQT', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PT-05', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (335, NULL, 'PAPEL TOALLA INTERFOLIADO PQT X 200 HOJAS', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PT-06', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (336, NULL, 'PAPEL TOALLA INTERFOLIADO PAQUETE DE 200 HOJAS X 20 PAQUETES (LINEA INSTITUCIONAL)', 120.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 120.00, 'PTI-01', NULL, '', NULL, NULL, NULL, 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (337, NULL, 'PAPEL TOALLA INTERFOLIADO PAQUETE X 200 HOJAS (LINEA INSTITUCIONAL)', 6.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, '1', '1', '1', 1, 0.00, 0.00, 0.00, 6.00, 'PTI-02', '1760053507_1759340213_68dd66b50a5bb.jpg', '', 0, NULL, 'null', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (338, '', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 12\" - MARCA: CRIS-TAURO', 3658.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 3292.20, 3109.30, '1', '1', '0', 1, 3109.30, 0.00, 0.00, 3658.00, 'JVC-001', '', 'Modelo: TD-12N \nPotencia de motor: 1.5 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotaci├│n: 175 RPM. \nMotor: KDS del Grupo Imperial. \nEstructura en Acero Inoxidable Anticorrosivo.\nBase de Motor en Aluminio Fundido anticorrosivo.\nPlato en Acero Inoxidable (calidad 304) de 12\".\nCable Vulcanizado Homologado de 3x14: 15 metros.\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\".', 8, 'Modelo: TD-12N Potencia de motor: 1.5 HP Voltaje / Frecuencia: 220 V/60 Hz. Velocidad de Rotaci├│n: 175 RPM. Motor: KDS del Grupo Imperial Estructura en Acero Inoxidable Anticorrosivo Base de Motor en Aluminio Fundido anticorrosivo Plato en Acero Inoxidabl', '14', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (339, NULL, 'ascsac', 10.00, 10.00, 0, 0, 12, 1, '1000-01-01', 'dvs324', '0', '0', 10.00, 10.00, '', '', '0', 1, 10.00, 0.00, 0.00, 10.00, '0001', 'not-foto.png', 'sdvcsdafv', 5, NULL, '15', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (340, NULL, 'pc gamer ', 10.00, 10.00, 0, 0, 12, 1, '1000-01-01', '312dsw', '0', '0', 10.00, 10.00, '', '', '0', 1, 10.00, 0.00, 0.00, 10.00, '0007', NULL, 'dfsvsdv', 6, NULL, '15', 'PEN', '2025-06-10 17:07:44', NULL);
INSERT INTO `productos` VALUES (341, 'JVC-001-1', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 12\" - MARCA: CRIS-TAURO', 3958.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '1', '0', 3610.80, 3410.20, '1', '1', '1', 1, 3410.20, 0.00, 0.00, 3958.00, 'JVC-001-1', NULL, 'Modelo: TD-12N \r\nPotencia de motor: 2.0 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotaci├│n: 175 RPM. \r\nMotor: KDS del Grupo Imperial.\r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 12\" \r\nCable Vulcanizado Homologado de 3x14: 15 metros \r\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\"', 5, NULL, '14', 'PEN', '2025-08-14 12:28:00', NULL);
INSERT INTO `productos` VALUES (342, NULL, 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 12 MARCA: CRIS-TAURO', 232.00, 232.00, 0, 0, 12, 1, '1000-01-01', '43', '0', '0', 223.00, 222.00, '1', '1', '0', 1, 222.00, 0.00, 0.00, 232.00, 'jvc-977', '1360763.jpeg', 'dddd', 5, NULL, '14', 'USD', '2025-09-24 17:28:30', NULL);
INSERT INTO `productos` VALUES (343, NULL, 'TRAPEADOR MECH├ôN H├ÜMEDO 50% ANTIBACTERIAL DE 350 GR H├ÜMEDO (IMPORTADO) (COLOR: VERDE)', 16.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 14.85, 14.02, NULL, NULL, '1', 1, 14.02, 0.00, 0.00, 16.50, 'IMPLE-073', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (344, NULL, 'TRAPEADOR MECH├ôN H├ÜMEDO DE ALGOD├ôN DE 350 GR (IMPORTADO) - AZUL', 27.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 24.75, 23.37, NULL, NULL, '1', 1, 23.37, 0.00, 0.00, 27.50, 'IMPLE-074', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (345, NULL, 'TRAPEADOR MECH├ôN H├ÜMEDO DE ALGOD├ôN DE 350 GR (IMPORTADO) - ROJO', 27.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 24.75, 23.37, NULL, NULL, '1', 1, 23.37, 0.00, 0.00, 27.50, 'IMPLE-075', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (346, NULL, 'TRAPEADOR MECH├ôN H├ÜMEDO DE ALGOD├ôN DE 350 GR (IMPORTADO) - VERDE', 27.50, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 24.75, 23.37, NULL, NULL, '1', 1, 23.37, 0.00, 0.00, 27.50, 'IMPLE-076', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (347, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR BLANCO DE 16\" LIMKIT CLEANER 5M', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'P5M-001', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (348, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR NEGRO DE 16\" LIMKIT CLEANER 5M', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'P5M-002', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (349, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR ROJO DE 16\" LIMKIT CLEANER 5M', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'P5M-003', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (350, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR BLANCO DE 18\" LIMKIT CLEANER 5M', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'P5M-004', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (351, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR NEGRO DE 18\" LIMKIT CLEANER 5M', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'P5M-005', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (352, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR ROJO DE 18\" LIMKIT CLEANER 5M', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'P5M-006', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (353, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR BLANCO DE 20\" LIMKIT CLEANER 5M', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'P5M-007', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (354, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR NEGRO DE 20\" LIMKIT CLEANER 5M', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'P5M-008', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (355, NULL, 'DISCO PAD PARA ABRILLANTAR COLOR ROJO DE 20\" LIMKIT CLEANER 5M', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'P5M-009', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (356, NULL, 'DISCO PAD 3M DE 17\" COLOR: BLANCO', 58.48, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 52.63, 49.71, NULL, NULL, '1', 1, 49.71, 0.00, 0.00, 58.48, 'P3M-001', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (357, NULL, 'DISCO PAD 3M DE 17\" COLOR: ROJO', 58.48, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 52.63, 49.71, NULL, NULL, '1', 1, 49.71, 0.00, 0.00, 58.48, 'P3M-002', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (358, NULL, 'DISCO PAD 3M DE 20\" COLOR: BLANCO', 62.66, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 56.39, 53.26, NULL, NULL, '1', 1, 53.26, 0.00, 0.00, 62.66, 'P3M-003', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (359, NULL, 'DISCO PAD 3M DE 20\" COLOR: ROJO', 62.66, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 56.39, 53.26, NULL, NULL, '1', 1, 53.26, 0.00, 0.00, 62.66, 'P3M-004', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (360, NULL, 'PAPEL HIGIENICO JUMBO DE 550 METROS X 6 ROLLOS (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PH-001', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (361, NULL, 'PAPEL HIGIENICO JUMBO DE 550 METROS X ROLLO (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PH-002', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (362, NULL, 'PAPEL HIGIENICO JUMBO DE 400 METROS X 6 ROLLOS (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PH-003', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (363, NULL, 'PAPEL HIGIENICO JUMBO DE 400 METROS X ROLLO (LINEA INSTITUCIONAL)', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PH-004', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (364, NULL, 'PAPEL TOALLA JUMBO DE 300 METROS X 4 ROLLOS (LINEA INSTITUCIONAL)', 85.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 85.00, 'PT-001', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (365, NULL, 'PAPEL TOALLA JUMBO DE 300 METROS X ROLLO (LINEA INSTITUCIONAL)', 21.25, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 21.25, 'PT-002', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (366, NULL, 'PAPEL TOALLA JUMBO DE 200 METROS X 4 ROLLOS (LINEA INSTITUCIONAL)', 77.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 77.00, 'PT-003', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (367, NULL, 'PAPEL TOALLA JUMBO DE 200 METROS X ROLLO (LINEA INSTITUCIONAL)', 19.25, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 19.25, 'PT-004', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (368, NULL, 'PAPEL TOALLA INTERFOLIADO PAQUETE DE 200 HOJAS X 20 PAQUETES (LINEA INSTITUCIONAL)', 120.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 120.00, 'PT-005', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (369, NULL, 'PAPEL TOALLA INTERFOLIADO PAQUETE X 200 HOJAS (LINEA INSTITUCIONAL)', 6.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', NULL, NULL, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 6.00, 'PT-006', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (370, NULL, 'PA├æO DE LIMPIEZA LIMPALL X 90', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PL-001', NULL, '', NULL, NULL, NULL, 'PEN', '2025-10-11 11:44:26', NULL);
INSERT INTO `productos` VALUES (371, 'JVC-002-1', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 14\" - MARCA: CRIS-TAURO', 4194.00, 0.00, 4, 0, 12, 1, '1000-01-01', '0', '1', '0', 3823.20, 3610.80, '1', '1', '1', 1, 3610.80, 0.00, 0.00, 4194.00, 'JVC-002-1', NULL, 'Modelo: TD-14N \r\nPotencia de motor: 2.0 HP\r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotaci├│n: 175 RPM. \r\nMotor: KDS del Grupo Imperial \r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 14\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros.\r\nIncluye: Cepillo de Lavar de 13\" y Cepillo de Lustrar de 13\".', 5, NULL, '14', 'PEN', '2025-10-21 19:16:14', NULL);
INSERT INTO `productos` VALUES (372, NULL, 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 16\" - MARCA: CRIS-TAURO', 4430.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 4035.60, 3811.40, '1', '1', '1', 1, 3811.40, 0.00, 0.00, 4430.00, 'JVC-003-1', NULL, 'Modelo: TD-16N \nPotencia de motor: 2.0 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotaci├│n: 175 RPM. \nMotor: KDS del Grupo Imperial.\nEstructura en Acero Inoxidable Anticorrosivo.\nBase de Motor en Aluminio Fundido anticorrosivo.\nPlato en Acero Inoxidable (calidad 304) de 16\".\nCable Vulcanizado Homologado de 3x14: 15 metros.\nIncluye: Cepillo de Lavar de 15\" y Cepillo de Lustrar de 15\"', 5, NULL, '14', 'PEN', '2025-10-21 19:16:14', NULL);
INSERT INTO `productos` VALUES (373, 'JVC-004-1', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 18\" - MARCA: CRIS-TAURO', 4666.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 4248.00, 4012.00, '1', '1', '1', 1, 4012.00, 0.00, 0.00, 4666.00, 'JVC-004-1', NULL, 'Modelo: TD-18N \r\nPotencia de motor: 2.0 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotaci├│n: 175 RPM. \r\nMotor: KDS del Grupo Imperial.\r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 18\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros.\r\nIncluye: Cepillo de Lavar de 17\" y Cepillo de Lustrar de 17\"', 5, NULL, '14', 'PEN', '2025-10-21 19:16:14', NULL);
INSERT INTO `productos` VALUES (374, 'JVC-005-1', 'LUSTRADORA LAVADORA INDUSTRIAL DE PISOS DE 20\" - MARCA: CRIS-TAURO', 4902.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '1', '0', 4460.40, 4212.60, '1', '1', '1', 1, 4212.60, 0.00, 0.00, 4902.00, 'JVC-005-1', NULL, 'Modelo: TD-20N \r\nPotencia de motor: 2.0 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotaci├│n: 175 RPM. \r\nMotor: KDS del Grupo Imperial.\r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 20\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros. \r\nIncluye: Cepillo de Lavar de 19\" y Cepillo de Lustrar de 19\"', 5, NULL, '14', 'PEN', '2025-10-21 19:16:14', NULL);
INSERT INTO `productos` VALUES (375, NULL, 'PA├æO DE LIMPIEZA LIMPALL X 90', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '0', '0', '0', 0.00, 0.00, NULL, NULL, '1', 1, 0.00, 0.00, 0.00, 0.00, 'PL-01', NULL, '', NULL, NULL, NULL, 'PEN', '2025-11-18 13:46:53', NULL);

-- ----------------------------
-- Table structure for productos_compras
-- ----------------------------
DROP TABLE IF EXISTS `productos_compras`;
CREATE TABLE `productos_compras`  (
  `id_producto_venta` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NULL DEFAULT NULL,
  `id_compra` int NULL DEFAULT NULL,
  `cantidad` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cantidad_devuelta` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '0',
  `precio` double(10, 3) NULL DEFAULT NULL,
  `costo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_producto_venta`) USING BTREE,
  INDEX `id_producto`(`id_producto` ASC) USING BTREE,
  INDEX `id_compra`(`id_compra` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of productos_compras
-- ----------------------------
INSERT INTO `productos_compras` VALUES (4, 20, 4, '1', '0', 300.000, NULL);
INSERT INTO `productos_compras` VALUES (5, 211, 5, '10', '0', 25.000, NULL);
INSERT INTO `productos_compras` VALUES (6, 212, 6, '11', '0', 25.000, NULL);
INSERT INTO `productos_compras` VALUES (7, 21, 8, '12', '0', 321.000, NULL);
INSERT INTO `productos_compras` VALUES (8, 341, 9, '32', '29', 32.000, NULL);

-- ----------------------------
-- Table structure for productos_cotis
-- ----------------------------
DROP TABLE IF EXISTS `productos_cotis`;
CREATE TABLE `productos_cotis`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `nombre_producto` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `descripcion_producto` text CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL,
  `id_coti` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio` double(10, 5) NULL DEFAULT NULL,
  `costo` double(10, 5) NULL DEFAULT NULL,
  `precioEspecial` double(10, 2) NULL DEFAULT NULL,
  `tipo_producto` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'producto',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_coti`(`id_coti` ASC) USING BTREE,
  INDEX `idx_producto_coti`(`id_producto` ASC, `id_coti` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 357 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of productos_cotis
-- ----------------------------
INSERT INTO `productos_cotis` VALUES (348, 118, NULL, '', 1871, 1, 387.50000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis` VALUES (349, 20, NULL, 'Modelo: TD-14N \nPotencia de motor: 1.5 HP \nVoltaje / Frecuencia: 220 V/60 Hz. \nVelocidad de Rotación: 175 RPM. \nMotor: KDS del Grupo Imperial \nEstructura en Acero Inoxidable Anticorrosivo. \nBase de Motor en Aluminio Fundido anticorrosivo.\nPlato en Acero Inoxidable (calidad 304) de 14\".\nCable Vulcanizado Homologado de 3x14: 15 metros.\nIncluye: Cepillo de Lavar de 13\" y Cepillo de Lustrar de 13\".', 1872, 1, 3894.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis` VALUES (350, 341, NULL, 'Modelo: TD-12N \r\nPotencia de motor: 2.0 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotación: 175 RPM. \r\nMotor: KDS del Grupo Imperial.\r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 12\" \r\nCable Vulcanizado Homologado de 3x14: 15 metros \r\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\"', 1873, 1, 3958.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis` VALUES (351, 117, NULL, 'Modelo: TD-12N \r\nPotencia de motor: 1.5 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotación: 175 RPM. \r\nMotor: KDS del Grupo Imperial. \r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 12\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros.\r\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\".', 1874, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis` VALUES (352, 117, NULL, 'Modelo: TD-12N \r\nPotencia de motor: 1.5 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotación: 175 RPM. \r\nMotor: KDS del Grupo Imperial. \r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 12\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros.\r\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\".', 1875, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis` VALUES (353, 117, NULL, 'Modelo: TD-12N \r\nPotencia de motor: 1.5 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotación: 175 RPM. \r\nMotor: KDS del Grupo Imperial. \r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 12\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros.\r\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\".', 1876, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis` VALUES (354, 117, NULL, 'Modelo: TD-12N \r\nPotencia de motor: 1.5 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotación: 175 RPM. \r\nMotor: KDS del Grupo Imperial. \r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 12\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros.\r\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\".', 1877, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis` VALUES (355, 117, NULL, 'Modelo: TD-12N \r\nPotencia de motor: 1.5 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotación: 175 RPM. \r\nMotor: KDS del Grupo Imperial. \r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 12\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros.\r\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\".', 1878, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis` VALUES (356, 117, NULL, 'Modelo: TD-12N \r\nPotencia de motor: 1.5 HP \r\nVoltaje / Frecuencia: 220 V/60 Hz. \r\nVelocidad de Rotación: 175 RPM. \r\nMotor: KDS del Grupo Imperial. \r\nEstructura en Acero Inoxidable Anticorrosivo.\r\nBase de Motor en Aluminio Fundido anticorrosivo.\r\nPlato en Acero Inoxidable (calidad 304) de 12\".\r\nCable Vulcanizado Homologado de 3x14: 15 metros.\r\nIncluye: Cepillo de Lavar de 11\" y Cepillo de Lustrar de 11\".', 1879, 1, 3658.00000, 0.00000, NULL, 'producto');

-- ----------------------------
-- Table structure for productos_cotis_copy1
-- ----------------------------
DROP TABLE IF EXISTS `productos_cotis_copy1`;
CREATE TABLE `productos_cotis_copy1`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `id_coti` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio` double(10, 5) NULL DEFAULT NULL,
  `costo` double(10, 5) NULL DEFAULT NULL,
  `precioEspecial` double(10, 2) NULL DEFAULT NULL,
  `tipo_producto` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT 'producto',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_coti`(`id_coti` ASC) USING BTREE,
  INDEX `idx_producto_coti`(`id_producto` ASC, `id_coti` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 89 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of productos_cotis_copy1
-- ----------------------------
INSERT INTO `productos_cotis_copy1` VALUES (1, 0, 1750, 2, 4602.00000, 0.00000, 12.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (2, 6, 1749, 2, 0.00000, 10.00000, 0.00, 'repuesto');
INSERT INTO `productos_cotis_copy1` VALUES (3, 18, 1710, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (4, 18, 1711, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (5, 18, 1712, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (6, 18, 1713, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (7, 18, 1714, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (8, 18, 1715, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (9, 18, 1718, 1, 4500.00000, 0.00000, 4200.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (10, 18, 1719, 1, 4500.00000, 0.00000, 4200.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (11, 18, 1720, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (12, 18, 1721, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (13, 18, 1722, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (14, 18, 1723, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (15, 18, 1727, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (16, 18, 1732, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (17, 18, 1736, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (18, 18, 1737, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (19, 18, 1738, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (20, 18, 1739, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (21, 18, 1742, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (22, 18, 1744, 1, 3658.00000, 0.00000, 3600.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (23, 18, 1745, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (24, 18, 1746, 1, 3658.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (25, 18, 1748, 1, 3658.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (26, 20, 1717, 1, 0.00000, 0.00000, 10.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (27, 20, 1720, 1, 3894.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (28, 20, 1721, 1, 3894.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (29, 20, 1732, 1, 3894.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (30, 20, 1736, 1, 3894.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (31, 20, 1737, 1, 3894.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (32, 20, 1738, 1, 3894.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (33, 20, 1739, 1, 3894.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (34, 20, 1742, 1, 3894.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (35, 20, 1744, 1, 3894.00000, 0.00000, 3800.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (36, 20, 1745, 1, 3894.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (37, 20, 1748, 1, 3894.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (38, 21, 1714, 1, 1600.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (39, 21, 1715, 1, 1600.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (40, 21, 1737, 1, 4130.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (41, 21, 1738, 1, 4130.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (42, 21, 1742, 1, 4130.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (43, 21, 1744, 1, 4130.00000, 0.00000, 4050.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (44, 21, 1745, 1, 4130.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (45, 21, 1746, 1, 4130.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (46, 21, 1748, 1, 4130.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (47, 29, 1716, 1, 1400.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (48, 30, 1742, 1, 4602.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (49, 30, 1743, 1, 4602.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (50, 30, 1747, 2, 4602.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (51, 30, 1748, 1, 4602.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (52, 30, 1749, 2, 4602.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (53, 31, 1743, 1, 4838.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (54, 31, 1749, 4, 4838.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (55, 33, 1730, 2, 2183.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (56, 33, 1741, 2, 2183.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (57, 34, 1724, 2, 2596.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (58, 34, 1725, 2, 2596.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (59, 34, 1726, 2, 2596.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (60, 35, 1729, 2, 2655.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (61, 36, 1743, 1, 3422.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (62, 36, 1747, 2, 3422.00000, 0.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (63, 37, 1728, 3, 3835.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (64, 39, 1740, 2, 4661.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (65, 118, 1731, 1, 252.22000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (66, 119, 1731, 1, 336.30000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (67, 121, 1735, 2, 387.04000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (68, 127, 1740, 2, 27.14000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (69, 189, 1733, 2, 100.00000, 90.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (70, 189, 1734, 2, 100.00000, 90.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (71, 192, 1741, 2, 110.00000, 110.00000, NULL, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (72, 0, 1751, 2, 4602.00000, 0.00000, 12.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (73, 0, 1751, 12, 2183.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (74, 0, 1752, 2, 4602.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (75, 0, 1752, 2, 3422.00000, 0.00000, 200.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (76, 0, 1752, 1, 2183.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (77, 0, 1753, 2, 4602.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (78, 0, 1753, 1, 2183.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (79, 0, 1754, 2, 4602.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (80, 0, 1754, 1, 2183.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (81, 0, 1755, 2, 6490.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (87, 34, 1756, 32, 2596.00000, 0.00000, 0.00, 'producto');
INSERT INTO `productos_cotis_copy1` VALUES (88, 30, 1756, 3, 4602.00000, 0.00000, 0.00, 'producto');

-- ----------------------------
-- Table structure for productos_ventas
-- ----------------------------
DROP TABLE IF EXISTS `productos_ventas`;
CREATE TABLE `productos_ventas`  (
  `id_producto_venta` int NOT NULL AUTO_INCREMENT,
  `id_producto` int NOT NULL,
  `id_venta` int NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10, 2) NULL DEFAULT NULL,
  `costo` decimal(10, 2) NULL DEFAULT NULL,
  `id_venta_equipo` int NULL DEFAULT NULL,
  `id_cotizacion_equipo` int NULL DEFAULT NULL,
  `precio_usado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_producto_venta`) USING BTREE,
  INDEX `fk_productos_has_ventas_ventas1_idx`(`id_venta` ASC) USING BTREE,
  INDEX `fk_productos_has_ventas_productos1_idx`(`id_producto` ASC) USING BTREE,
  INDEX `idx_pv_id_venta_equipo`(`id_venta_equipo` ASC) USING BTREE,
  INDEX `idx_pv_id_coti_equipo`(`id_cotizacion_equipo` ASC) USING BTREE,
  CONSTRAINT `fk_pv_venta_equipo` FOREIGN KEY (`id_venta_equipo`) REFERENCES `ventas_equipos` (`id_venta_equipo`) ON DELETE SET NULL ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 33 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of productos_ventas
-- ----------------------------
INSERT INTO `productos_ventas` VALUES (24, 118, 20, 1, 387.50, 0.00, NULL, NULL, '5');
INSERT INTO `productos_ventas` VALUES (25, 118, 21, 1, 387.50, 0.00, NULL, NULL, '5');
INSERT INTO `productos_ventas` VALUES (26, 20, 22, 1, 3894.00, 0.00, NULL, NULL, '5');
INSERT INTO `productos_ventas` VALUES (27, 117, 23, 1, 3658.00, 0.00, NULL, NULL, '5');
INSERT INTO `productos_ventas` VALUES (28, 117, 24, 1, 3658.00, 0.00, NULL, NULL, '5');
INSERT INTO `productos_ventas` VALUES (29, 117, 25, 1, 3658.00, 0.00, NULL, NULL, '5');
INSERT INTO `productos_ventas` VALUES (30, 31, 26, 1, 5310.00, 0.00, NULL, NULL, '5');
INSERT INTO `productos_ventas` VALUES (31, 117, 27, 1, 3658.00, 0.00, NULL, NULL, '5');
INSERT INTO `productos_ventas` VALUES (32, 211, 28, 1, 100.24, 25.00, NULL, NULL, '5');

-- ----------------------------
-- Table structure for productos_ventas_backup
-- ----------------------------
DROP TABLE IF EXISTS `productos_ventas_backup`;
CREATE TABLE `productos_ventas_backup`  (
  `id_producto` int NOT NULL,
  `id_venta` int NOT NULL,
  `cantidad` double(6, 2) NULL DEFAULT NULL,
  `precio` double(10, 5) NULL DEFAULT NULL,
  `costo` double(10, 5) NULL DEFAULT NULL,
  `precio_usado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  INDEX `fk_productos_has_ventas_ventas1_idx`(`id_venta` ASC) USING BTREE,
  INDEX `fk_productos_has_ventas_productos1_idx`(`id_producto` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of productos_ventas_backup
-- ----------------------------
INSERT INTO `productos_ventas_backup` VALUES (18, 116, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 117, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 118, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 119, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 120, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 121, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 122, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 123, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 124, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 125, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 126, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 127, 1.00, 3658.00000, 0.00000, '5');
INSERT INTO `productos_ventas_backup` VALUES (18, 128, 1.00, 3658.00000, 0.00000, '5');
INSERT INTO `productos_ventas_backup` VALUES (36, 129, 3.00, 3422.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 130, 1.00, 3658.00000, 0.00000, '5');
INSERT INTO `productos_ventas_backup` VALUES (33, 131, 2.00, 2183.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (39, 132, 2.00, 4661.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (39, 133, 2.00, 4661.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (39, 134, 2.00, 4661.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (39, 135, 3.00, 4661.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 136, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 137, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (20, 137, 1.00, 3894.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (33, 137, 2.00, 2183.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (33, 138, 1.00, 2183.00000, 0.00000, '');
INSERT INTO `productos_ventas_backup` VALUES (34, 138, 1.00, 2596.00000, 0.00000, '');
INSERT INTO `productos_ventas_backup` VALUES (35, 138, 1.00, 2655.00000, 0.00000, '');
INSERT INTO `productos_ventas_backup` VALUES (18, 139, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (20, 139, 1.00, 3894.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (21, 139, 1.00, 4130.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (30, 139, 1.00, 4602.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (18, 140, 1.00, 3658.00000, 0.00000, '');
INSERT INTO `productos_ventas_backup` VALUES (0, 141, 1.00, 3658.00000, 0.00000, '');
INSERT INTO `productos_ventas_backup` VALUES (0, 141, 1.00, 3894.00000, 0.00000, '');
INSERT INTO `productos_ventas_backup` VALUES (0, 141, 1.00, 4130.00000, 0.00000, '');
INSERT INTO `productos_ventas_backup` VALUES (18, 142, 1.00, 3658.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (20, 142, 1.00, 3894.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (21, 142, 1.00, 4130.00000, 0.00000, '1');
INSERT INTO `productos_ventas_backup` VALUES (30, 142, 1.00, 4602.00000, 0.00000, '1');

-- ----------------------------
-- Table structure for proveedores
-- ----------------------------
DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE `proveedores`  (
  `proveedor_id` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `razon_social` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `direccion` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `telefono` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '',
  `id_empresa` int NULL DEFAULT NULL,
  `departamento` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `provincia` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `distrito` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `ubigeo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `fecha_create` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `estado` int NULL DEFAULT 1,
  PRIMARY KEY (`proveedor_id`) USING BTREE,
  UNIQUE INDEX `ruc`(`ruc` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 602 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of proveedores
-- ----------------------------
INSERT INTO `proveedores` VALUES (47, 'adddda1122', 'leo', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 11:20:30', 1);
INSERT INTO `proveedores` VALUES (48, '20601907063', 'CYBERGAMES (C.G.S.) E.I.R.L.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 11:20:30', 1);
INSERT INTO `proveedores` VALUES (49, '20100131359', 'DATACONT S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 11:20:30', 1);
INSERT INTO `proveedores` VALUES (50, '20267163228', 'INGRAM MICRO S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 11:20:30', 1);
INSERT INTO `proveedores` VALUES (51, '20536196570', 'SUDAMERICA THERMAL SOLUTIONS  S.A.C', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 11:20:30', 1);
INSERT INTO `proveedores` VALUES (52, '10714584443', 'ANGULO INCHICAQUE GEAN MARCO', 'Av. Prl. Gamarra Mz. A Lt. 20', '917183231', 'geanmarco0@gmail.com', 12, NULL, NULL, NULL, NULL, '2024-06-08 11:20:30', 1);
INSERT INTO `proveedores` VALUES (53, '10427993120', 'AGUADO SIERRA MANUEL HIPOLITO', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (54, '10444626688', 'QUISPE TOLENTINO ANDRES ALEJANDRO', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (55, '10620496508', 'ESCOBAR AQUITUARI ANGEL YAIR', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (56, '20123053037', 'COMPUDISKETT S R L', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (57, '20127745910', 'MAXIMA INTERNACIONAL S.A.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (58, '20469317855', 'PC LINK SOCIEDAD ANONIMA CERRADA', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (59, '20505970323', 'HALION INTERNACIONAL S.A.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (60, '20506717044', 'MEMORY KINGS PERU S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (61, '20510530951', 'C & C COMPUTER SERVICE S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (62, '20515672428', 'IMPORTEC PERU E.I.R.L', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (63, '20516973324', 'GRUPO IMPORTEK SOCIEDAD ANONIMA CERRADA', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (64, '20518679121', 'CORPORACION SERCOPLUS S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (65, '20543886671', 'IMPORTACIONES IMPACTO S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (66, '20550024447', 'R & M PORTATILES SOCIEDAD ANONIMA CERRADA', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (67, '20554454276', 'LABORATORIOS CLINICOS MULTIPLES S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (68, '20557331043', 'IMPORTEK PERU SOCIEDAD ANONIMA CERRADA - IMPORTEK PERU S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (69, '20600208781', 'GRUPO COMPU & VISION S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (70, '20601844916', 'QUE TAL COMPRA DEL PERU S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (71, '20606814594', 'CORPORACION FABRITEC S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (72, '20608252909', 'IMPORTACIONES HIGH DEVICES TECHNOLOGY E.I.R.L.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (73, '20608685449', 'GRUPO ASTRA S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (74, '20608911996', 'WWW.REMATAZO.PE S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (75, '20612107280', 'ASTERION STORE S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-08 22:50:59', 1);
INSERT INTO `proveedores` VALUES (153, '20607828467', 'C&G MICROSYSTEMS E.I.R.L.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-21 18:36:08', 1);
INSERT INTO `proveedores` VALUES (211, '20422561537', 'IMPULSO INFORMATICO S.A.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-06-27 18:27:32', 1);
INSERT INTO `proveedores` VALUES (255, '20212331377', 'GRUPO DELTRON S.A.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-07-04 11:31:17', 1);
INSERT INTO `proveedores` VALUES (334, '20565419421', 'ENVI SOLUTIONS S.A.C', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-07-12 14:56:16', 1);
INSERT INTO `proveedores` VALUES (424, '20609242575', 'SUPER LAPTOP S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-07-25 16:01:05', 1);
INSERT INTO `proveedores` VALUES (515, '20556020427', 'CORPORACION C & A SYSTEMS S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-08-09 19:14:23', 1);
INSERT INTO `proveedores` VALUES (568, '20606793627', 'COMPUTRONIC J & J SEDANO S.A.C.', NULL, '', '', 12, NULL, NULL, NULL, NULL, '2024-08-19 13:30:34', 1);
INSERT INTO `proveedores` VALUES (600, '20601212472', 'LIM KIT CORPORACION E.I.R.L.', NULL, '', '', NULL, NULL, NULL, NULL, NULL, '2025-04-10 13:25:49', 1);
INSERT INTO `proveedores` VALUES (601, '10774252008', 'YARLEQUE ZAPATA EMER RODRIGO', NULL, '', '', NULL, NULL, NULL, NULL, NULL, '2025-04-28 16:38:33', 1);

-- ----------------------------
-- Table structure for repuesto_precios
-- ----------------------------
DROP TABLE IF EXISTS `repuesto_precios`;
CREATE TABLE `repuesto_precios`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_repuesto` int NOT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `precio` double(10, 2) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_repuesto_precios_repuestos`(`id_repuesto` ASC) USING BTREE,
  INDEX `idx_repuesto_precios_repuesto`(`id_repuesto` ASC) USING BTREE,
  CONSTRAINT `fk_repuesto_precios_repuestos` FOREIGN KEY (`id_repuesto`) REFERENCES `repuestos` (`id_repuesto`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of repuesto_precios
-- ----------------------------
INSERT INTO `repuesto_precios` VALUES (17, 1, 'plaza', 1000.00);
INSERT INTO `repuesto_precios` VALUES (18, 1, 'ED', 900.00);

-- ----------------------------
-- Table structure for repuestos
-- ----------------------------
DROP TABLE IF EXISTS `repuestos`;
CREATE TABLE `repuestos`  (
  `id_repuesto` int NOT NULL AUTO_INCREMENT,
  `cod_barra` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombre` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `precio` double(10, 2) NULL DEFAULT NULL,
  `costo` double(10, 2) NULL DEFAULT NULL,
  `cantidad` int NULL DEFAULT NULL,
  `iscbp` int NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `ultima_salida` date NOT NULL,
  `codsunat` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `usar_barra` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '0',
  `usar_multiprecio` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '0',
  `precio_mayor` double(10, 2) NULL DEFAULT NULL,
  `precio_menor` double(10, 2) NULL DEFAULT NULL,
  `razon_social` varchar(250) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ruc` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  `almacen` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `precio2` double(10, 2) NULL DEFAULT 0.00,
  `precio3` double(10, 2) NULL DEFAULT 0.00,
  `precio4` double(10, 2) NULL DEFAULT 0.00,
  `precio_unidad` double(10, 2) NULL DEFAULT NULL,
  `codigo` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `imagen` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `detalle` text CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL,
  `categoria` int NULL DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `unidad` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `moneda` enum('PEN','USD') CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT 'PEN',
  `subcategoria` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_repuesto`) USING BTREE,
  INDEX `fk_repuestos_empresas1_idx`(`id_empresa` ASC) USING BTREE,
  INDEX `idx_repuestos_filtro`(`id_empresa` ASC, `sucursal` ASC, `estado` ASC, `almacen` ASC) USING BTREE,
  INDEX `idx_repuestos_codigo_empresa`(`codigo` ASC, `id_empresa` ASC, `sucursal` ASC, `almacen` ASC) USING BTREE,
  INDEX `idx_repuestos_nombre`(`nombre`(50) ASC, `id_empresa` ASC, `sucursal` ASC, `almacen` ASC, `estado` ASC) USING BTREE,
  INDEX `idx_repuestos_busqueda`(`id_empresa` ASC, `sucursal` ASC, `almacen` ASC, `estado` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of repuestos
-- ----------------------------
INSERT INTO `repuestos` VALUES (1, 'REP-001', 'CARBURADOR', 25.00, 100.00, 9, 0, 12, 1, '1000-01-01', '', '1', '1', 1000.00, 1500.00, '1', '1', '1', '4', 1500.00, 0.00, 0.00, 25.00, 'REP-001', '17338.jpeg', 'TAURO, TVX', 21, NULL, '14', 'PEN', 8);
INSERT INTO `repuestos` VALUES (2, NULL, 'CARBONES', 42.00, 0.00, 8, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '1', '1', '1', '4', 0.00, 0.00, 0.00, 42.00, 'REP-002', NULL, 'TVX, CHASQUY', 21, NULL, '14', 'PEN', 9);
INSERT INTO `repuestos` VALUES (3, NULL, 'RESORTES', 60.00, 0.00, 10, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '1', '1', '1', '4', 0.00, 0.00, 0.00, 60.00, 'REP-003', NULL, 'TVX, TAURO ', 21, NULL, '14', 'PEN', 8);
INSERT INTO `repuestos` VALUES (4, '', 'BATERIA', 90.00, 330.00, 4, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '1', '1', '1', '4', 0.00, 0.00, 0.00, 90.00, 'REP-004', '', 'TVX ', 0, NULL, '14', 'PEN', NULL);
INSERT INTO `repuestos` VALUES (5, NULL, 'dfssf', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '', '', '0', '4', 0.00, 0.00, 0.00, 0.00, '0', NULL, '', 21, NULL, '14', 'PEN', NULL);
INSERT INTO `repuestos` VALUES (6, '', 'REPUESTO DE PRUEBA ', 120.00, 210.00, 0, 0, 12, 1, '1000-01-01', '', '1', '1', 0.00, 0.00, '1', '1', '0', '4', 0.00, 0.00, 0.00, 120.00, 'REP-0044', NULL, 'HELOSCDSDCVD', 21, NULL, '15', 'PEN', 8);
INSERT INTO `repuestos` VALUES (7, NULL, 'REPUESTO DE PRUEBA	', 211.00, 22.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 222.00, 222.00, '', '', '0', '4', 222.00, 0.00, 0.00, 211.00, 'REP-032', NULL, 'FGBNGF', 21, NULL, '14', 'PEN', 8);
INSERT INTO `repuestos` VALUES (8, NULL, 'REPUESTO DE PRUEBA	', 222.00, 222.00, 0, 0, 12, 1, '1000-01-01', '223', '0', '0', 22.00, 23.00, '1', '1', '1', '4', 23.00, 0.00, 0.00, 222.00, 'rep-02', NULL, 'gfcngfhngf', 21, NULL, '15', 'USD', 9);

-- ----------------------------
-- Table structure for repuestos_compras
-- ----------------------------
DROP TABLE IF EXISTS `repuestos_compras`;
CREATE TABLE `repuestos_compras`  (
  `id_repuesto_compra` int NOT NULL AUTO_INCREMENT,
  `id_repuesto` int NULL DEFAULT NULL,
  `id_compra` int NULL DEFAULT NULL,
  `cantidad` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cantidad_devuelta` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '0',
  `precio` double(10, 3) NULL DEFAULT NULL,
  `costo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_repuesto_compra`) USING BTREE,
  INDEX `id_repuesto`(`id_repuesto` ASC) USING BTREE,
  INDEX `id_compra`(`id_compra` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of repuestos_compras
-- ----------------------------
INSERT INTO `repuestos_compras` VALUES (1, 4, 7, '4', '0', 330.000, NULL);

-- ----------------------------
-- Table structure for resumen_diario
-- ----------------------------
DROP TABLE IF EXISTS `resumen_diario`;
CREATE TABLE `resumen_diario`  (
  `id_resumen_diario` int NOT NULL,
  `id_empresa` int NOT NULL,
  `fecha` date NULL DEFAULT NULL,
  `ticket` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cantidad_items` int NULL DEFAULT NULL,
  `tipo` int NULL DEFAULT NULL COMMENT '1 para resumen\n2 para comunicacion de baja',
  PRIMARY KEY (`id_resumen_diario`) USING BTREE,
  INDEX `fk_resumen_diario_empresas1_idx`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of resumen_diario
-- ----------------------------

-- ----------------------------
-- Table structure for rol_permisos
-- ----------------------------
DROP TABLE IF EXISTS `rol_permisos`;
CREATE TABLE `rol_permisos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `rol_id` int NOT NULL,
  `modulo_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `submodulo_id` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `unique_permiso`(`rol_id` ASC, `modulo_id` ASC, `submodulo_id` ASC) USING BTREE,
  CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 37 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of rol_permisos
-- ----------------------------
INSERT INTO `rol_permisos` VALUES (8, 1, 'almacen', NULL);
INSERT INTO `rol_permisos` VALUES (6, 1, 'cajas', NULL);
INSERT INTO `rol_permisos` VALUES (15, 1, 'clientes', NULL);
INSERT INTO `rol_permisos` VALUES (3, 1, 'cotizaciones', NULL);
INSERT INTO `rol_permisos` VALUES (14, 1, 'cotizaciones_taller', NULL);
INSERT INTO `rol_permisos` VALUES (4, 1, 'cuentas_cobrar', NULL);
INSERT INTO `rol_permisos` VALUES (5, 1, 'cuentas_pagar', NULL);
INSERT INTO `rol_permisos` VALUES (1, 1, 'dashboard', NULL);
INSERT INTO `rol_permisos` VALUES (17, 1, 'documentos', NULL);
INSERT INTO `rol_permisos` VALUES (2, 1, 'facturacion', NULL);
INSERT INTO `rol_permisos` VALUES (12, 1, 'garantia', NULL);
INSERT INTO `rol_permisos` VALUES (11, 1, 'numero_series', NULL);
INSERT INTO `rol_permisos` VALUES (7, 1, 'orden_compra', NULL);
INSERT INTO `rol_permisos` VALUES (10, 1, 'orden_servicio', NULL);
INSERT INTO `rol_permisos` VALUES (9, 1, 'orden_trabajo', NULL);
INSERT INTO `rol_permisos` VALUES (13, 1, 'taller', NULL);
INSERT INTO `rol_permisos` VALUES (16, 1, 'usuarios', NULL);
INSERT INTO `rol_permisos` VALUES (18, 2, 'dashboard', NULL);
INSERT INTO `rol_permisos` VALUES (19, 2, 'orden_servicio', NULL);
INSERT INTO `rol_permisos` VALUES (20, 2, 'taller', NULL);
INSERT INTO `rol_permisos` VALUES (22, 4, 'clientes', NULL);
INSERT INTO `rol_permisos` VALUES (21, 4, 'facturacion', 'ventas');
INSERT INTO `rol_permisos` VALUES (23, 6, 'almacen', NULL);
INSERT INTO `rol_permisos` VALUES (27, 7, 'clientes', NULL);
INSERT INTO `rol_permisos` VALUES (24, 7, 'cotizaciones', NULL);
INSERT INTO `rol_permisos` VALUES (26, 7, 'cotizaciones_taller', NULL);
INSERT INTO `rol_permisos` VALUES (25, 7, 'taller', NULL);
INSERT INTO `rol_permisos` VALUES (29, 8, 'cotizaciones_taller', NULL);
INSERT INTO `rol_permisos` VALUES (28, 8, 'taller', NULL);
INSERT INTO `rol_permisos` VALUES (32, 9, 'clientes', NULL);
INSERT INTO `rol_permisos` VALUES (31, 9, 'cotizaciones_taller', NULL);
INSERT INTO `rol_permisos` VALUES (30, 9, 'taller', NULL);
INSERT INTO `rol_permisos` VALUES (35, 10, 'documentos', NULL);
INSERT INTO `rol_permisos` VALUES (36, 10, 'documentos', 'ficha_tecnica');
INSERT INTO `rol_permisos` VALUES (34, 10, 'taller', NULL);

-- ----------------------------
-- Table structure for roles
-- ----------------------------
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles`  (
  `rol_id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `ver_precios` tinyint(1) NOT NULL DEFAULT 1,
  `puede_eliminar` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`rol_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of roles
-- ----------------------------
INSERT INTO `roles` VALUES (1, 'ADMIN', 1, 1);
INSERT INTO `roles` VALUES (2, 'USUARIO', 1, 1);
INSERT INTO `roles` VALUES (3, 'VENDEDOR', 1, 1);
INSERT INTO `roles` VALUES (4, 'CAJERO', 1, 1);
INSERT INTO `roles` VALUES (5, 'CONTADOR', 1, 1);
INSERT INTO `roles` VALUES (6, 'ALMACEN', 1, 1);
INSERT INTO `roles` VALUES (7, 'ORDEN TRABAJO', 0, 0);
INSERT INTO `roles` VALUES (8, 'ORDEN SERVICIO', 1, 1);
INSERT INTO `roles` VALUES (9, 'DOCUMENTOS', 1, 1);
INSERT INTO `roles` VALUES (10, 'TALLER', 0, 1);

-- ----------------------------
-- Table structure for rubros
-- ----------------------------
DROP TABLE IF EXISTS `rubros`;
CREATE TABLE `rubros`  (
  `id_rubro` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `id_empresa` int NOT NULL,
  `estado` tinyint(1) NULL DEFAULT 1,
  PRIMARY KEY (`id_rubro`) USING BTREE,
  INDEX `fk_rubro_empresa`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of rubros
-- ----------------------------
INSERT INTO `rubros` VALUES (1, 'SDC', 12, 0);
INSERT INTO `rubros` VALUES (2, 'SDCSDA', 12, 0);
INSERT INTO `rubros` VALUES (3, 'DCSDAC', 12, 0);
INSERT INTO `rubros` VALUES (4, 'SCACSDASDC222', 12, 0);
INSERT INTO `rubros` VALUES (5, 'WQWQ', 12, 0);
INSERT INTO `rubros` VALUES (6, 'COLEGIO', 12, 1);
INSERT INTO `rubros` VALUES (7, 'SERVICIO DE LIMPIEZA', 12, 1);
INSERT INTO `rubros` VALUES (8, 'TIENDA', 12, 1);

-- ----------------------------
-- Table structure for subcategorias_repuestos
-- ----------------------------
DROP TABLE IF EXISTS `subcategorias_repuestos`;
CREATE TABLE `subcategorias_repuestos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `categoria_id` int NOT NULL,
  `creado_el` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_subcategoria_categoria`(`categoria_id` ASC) USING BTREE,
  CONSTRAINT `fk_subcategoria_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_repuestos` (`id`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 12 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of subcategorias_repuestos
-- ----------------------------
INSERT INTO `subcategorias_repuestos` VALUES (8, 'TANQUE', 21, '2025-04-10 17:04:30');
INSERT INTO `subcategorias_repuestos` VALUES (9, 'CHASIS', 21, '2025-04-10 17:04:35');
INSERT INTO `subcategorias_repuestos` VALUES (10, 'ABRAZADERAS', 21, '2025-04-10 17:04:54');
INSERT INTO `subcategorias_repuestos` VALUES (11, 'RUEDAS', 21, '2025-04-10 17:05:13');

-- ----------------------------
-- Table structure for sucursales
-- ----------------------------
DROP TABLE IF EXISTS `sucursales`;
CREATE TABLE `sucursales`  (
  `id_sucursal` int NOT NULL,
  `empresa_id` int NULL DEFAULT NULL,
  `direccion` varchar(150) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `distrito` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `provincia` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `departamento` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `ubigeo` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `cod_sucursal` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`) USING BTREE,
  INDEX `empresa_id`(`empresa_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of sucursales
-- ----------------------------

-- ----------------------------
-- Table structure for taller_condiciones_cotizacion
-- ----------------------------
DROP TABLE IF EXISTS `taller_condiciones_cotizacion`;
CREATE TABLE `taller_condiciones_cotizacion`  (
  `id_condicion` int NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int NOT NULL,
  `condiciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_condicion`) USING BTREE,
  UNIQUE INDEX `unique_cotizacion`(`id_cotizacion` ASC) USING BTREE,
  INDEX `fk_condiciones_cotizacion`(`id_cotizacion` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of taller_condiciones_cotizacion
-- ----------------------------

-- ----------------------------
-- Table structure for taller_condiciones_globales
-- ----------------------------
DROP TABLE IF EXISTS `taller_condiciones_globales`;
CREATE TABLE `taller_condiciones_globales`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `condiciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of taller_condiciones_globales
-- ----------------------------

-- ----------------------------
-- Table structure for taller_cotizaciones
-- ----------------------------
DROP TABLE IF EXISTS `taller_cotizaciones`;
CREATE TABLE `taller_cotizaciones`  (
  `id_cotizacion` int NOT NULL AUTO_INCREMENT,
  `numero` int NULL DEFAULT NULL,
  `id_tido` int NOT NULL,
  `id_tipo_pago` int NULL DEFAULT NULL,
  `fecha` date NULL DEFAULT NULL,
  `dias_pagos` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_cliente` int NOT NULL,
  `total` double(10, 2) NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `guia_numero` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `usar_precio` int NULL DEFAULT NULL,
  `moneda` int NULL DEFAULT 1,
  `cm_tc` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `id_usuario` int NOT NULL,
  `id_prealerta` int NULL DEFAULT NULL,
  `descuento` decimal(5, 2) NULL DEFAULT 0.00,
  `tipo_origen` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_cotizacion`) USING BTREE,
  INDEX `fk_prealerta`(`id_prealerta` ASC) USING BTREE,
  INDEX `fk_taller_cotizaciones_clientes`(`id_cliente` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 25 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of taller_cotizaciones
-- ----------------------------
INSERT INTO `taller_cotizaciones` VALUES (14, 1, 1, 1, '2025-08-25', '', '1', 16, 3900.00, '0', 'T001-1126', 12, 1, 5, 1, '', 40, 11, 0.00, 'ORD TRABAJO');
INSERT INTO `taller_cotizaciones` VALUES (15, 2, 1, 1, '2025-08-25', '', '1', 16, 3900.00, '0', NULL, 12, 1, 5, 1, '', 40, 11, 0.00, 'ORD TRABAJO');
INSERT INTO `taller_cotizaciones` VALUES (16, 3, 1, 1, '2025-08-25', '', '1', 16, 3900.00, '0', 'T001-1127', 12, 1, 5, 1, '', 40, 11, 0.00, 'ORD TRABAJO');
INSERT INTO `taller_cotizaciones` VALUES (17, 4, 1, 1, '2025-08-25', '', '1', 16, 3300.00, '0', NULL, 12, 1, 5, 1, '', 40, 11, 0.00, 'ORD TRABAJO');
INSERT INTO `taller_cotizaciones` VALUES (18, 5, 2, 1, '2025-09-22', '', '1', 28, 132.00, '0', NULL, 12, 1, 5, 1, '', 63, 11, 0.00, 'ORD SERVICIO');
INSERT INTO `taller_cotizaciones` VALUES (19, 6, 2, 1, '2025-12-03', '', '1', 28, 0.00, '0', NULL, 12, 1, 5, 1, '', 62, 12, 0.00, 'ORD SERVICIO');
INSERT INTO `taller_cotizaciones` VALUES (20, 7, 2, 1, '2026-01-20', '', '1', 35, 3658.00, '0', NULL, 12, 1, 5, 1, '', 40, 19, 0.00, 'ORD TRABAJO');
INSERT INTO `taller_cotizaciones` VALUES (21, 8, 2, 1, '2026-01-20', '', '1', 35, 37642.00, '0', NULL, 12, 1, 5, 1, '', 40, 20, 0.00, 'ORD TRABAJO');
INSERT INTO `taller_cotizaciones` VALUES (22, 9, 2, 1, '2026-01-20', '', '1', 35, 37642.00, '0', NULL, 12, 1, 5, 1, '', 40, 20, 0.00, 'ORD TRABAJO');
INSERT INTO `taller_cotizaciones` VALUES (23, 10, 2, 1, '2026-01-20', '', '1', 35, 11874.00, '0', NULL, 12, 1, 5, 1, '', 40, 20, 0.00, 'ORD TRABAJO');
INSERT INTO `taller_cotizaciones` VALUES (24, 11, 2, 1, '2026-01-20', '', '1', 35, 8388.00, '0', NULL, 12, 1, 5, 1, '', 40, 20, 0.00, 'ORD TRABAJO');

-- ----------------------------
-- Table structure for taller_cotizaciones_equipos
-- ----------------------------
DROP TABLE IF EXISTS `taller_cotizaciones_equipos`;
CREATE TABLE `taller_cotizaciones_equipos`  (
  `id_cotizacion_equipo` int NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int NOT NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `equipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `modelo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_cotizacion_equipo`) USING BTREE,
  INDEX `fk_cotizacion`(`id_cotizacion` ASC) USING BTREE,
  CONSTRAINT `fk_cotizacion` FOREIGN KEY (`id_cotizacion`) REFERENCES `taller_cotizaciones` (`id_cotizacion`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB AUTO_INCREMENT = 128 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of taller_cotizaciones_equipos
-- ----------------------------
INSERT INTO `taller_cotizaciones_equipos` VALUES (97, 14, 'CRIS-TAURO', 'ASPIRADORA', 'AG-06', '51110');
INSERT INTO `taller_cotizaciones_equipos` VALUES (98, 14, 'MASTER GOLDS', 'LUSTRADORA', 'AG-08', '51111');
INSERT INTO `taller_cotizaciones_equipos` VALUES (99, 14, 'SPEED POWER', 'FREGADORA', 'ASJ12', '51112');
INSERT INTO `taller_cotizaciones_equipos` VALUES (100, 15, 'CRIS-TAURO', 'ASPIRADORA', 'AG-06', '51110');
INSERT INTO `taller_cotizaciones_equipos` VALUES (101, 15, 'MASTER GOLDS', 'LUSTRADORA', 'AG-08', '51111');
INSERT INTO `taller_cotizaciones_equipos` VALUES (102, 15, 'SPEED POWER', 'FREGADORA', 'ASJ12', '51112');
INSERT INTO `taller_cotizaciones_equipos` VALUES (103, 16, 'CRIS-TAURO', 'ASPIRADORA', 'AG-06', '51110');
INSERT INTO `taller_cotizaciones_equipos` VALUES (104, 16, 'MASTER GOLDS', 'LUSTRADORA', 'AG-08', '51111');
INSERT INTO `taller_cotizaciones_equipos` VALUES (105, 16, 'SPEED POWER', 'FREGADORA', 'ASJ12', '51112');
INSERT INTO `taller_cotizaciones_equipos` VALUES (106, 17, 'CRIS-TAURO', 'ASPIRADORA', 'AG-06', '51110');
INSERT INTO `taller_cotizaciones_equipos` VALUES (107, 17, 'MASTER GOLDS', 'LUSTRADORA', 'AG-08', '51111');
INSERT INTO `taller_cotizaciones_equipos` VALUES (108, 17, 'SPEED POWER', 'FREGADORA', 'ASJ12', '51112');
INSERT INTO `taller_cotizaciones_equipos` VALUES (109, 18, 'CRIS-TAURO', 'ASPIRADORA', 'AG-08', '1510');
INSERT INTO `taller_cotizaciones_equipos` VALUES (110, 19, 'CRIS-TAURO', 'ASPIRADORA', 'AG-06', '1234');
INSERT INTO `taller_cotizaciones_equipos` VALUES (111, 19, 'MASTER GOLDS', 'LUSTRADORA', 'ASJ12', '12345');
INSERT INTO `taller_cotizaciones_equipos` VALUES (112, 20, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12326');
INSERT INTO `taller_cotizaciones_equipos` VALUES (113, 20, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12327');
INSERT INTO `taller_cotizaciones_equipos` VALUES (114, 20, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12328');
INSERT INTO `taller_cotizaciones_equipos` VALUES (115, 20, 'CRIS-TAURO', 'LAVA BUTACAS', 'AG-06', '12329');
INSERT INTO `taller_cotizaciones_equipos` VALUES (116, 21, 'CRIS-TAURO', 'LUSTRADORA', 'AG-08', '12330');
INSERT INTO `taller_cotizaciones_equipos` VALUES (117, 21, 'MASTER GOLDS', 'ASPIRADORA', 'AG-08', '12331');
INSERT INTO `taller_cotizaciones_equipos` VALUES (118, 21, 'SPEED POWER', 'LAVADORA DE ALFOMBRAS', 'ASJ12', '12332');
INSERT INTO `taller_cotizaciones_equipos` VALUES (119, 22, 'CRIS-TAURO', 'LUSTRADORA', 'AG-08', '12330');
INSERT INTO `taller_cotizaciones_equipos` VALUES (120, 22, 'MASTER GOLDS', 'ASPIRADORA', 'AG-08', '12331');
INSERT INTO `taller_cotizaciones_equipos` VALUES (121, 22, 'SPEED POWER', 'LAVADORA DE ALFOMBRAS', 'ASJ12', '12332');
INSERT INTO `taller_cotizaciones_equipos` VALUES (122, 23, 'CRIS-TAURO', 'LUSTRADORA', 'AG-08', '12330');
INSERT INTO `taller_cotizaciones_equipos` VALUES (123, 23, 'MASTER GOLDS', 'ASPIRADORA', 'AG-08', '12331');
INSERT INTO `taller_cotizaciones_equipos` VALUES (124, 23, 'SPEED POWER', 'LAVADORA DE ALFOMBRAS', 'ASJ12', '12332');
INSERT INTO `taller_cotizaciones_equipos` VALUES (125, 24, 'CRIS-TAURO', 'LUSTRADORA', 'AG-08', '12330');
INSERT INTO `taller_cotizaciones_equipos` VALUES (126, 24, 'MASTER GOLDS', 'ASPIRADORA', 'AG-08', '12331');
INSERT INTO `taller_cotizaciones_equipos` VALUES (127, 24, 'SPEED POWER', 'LAVADORA DE ALFOMBRAS', 'ASJ12', '12332');

-- ----------------------------
-- Table structure for taller_cotizaciones_fotos
-- ----------------------------
DROP TABLE IF EXISTS `taller_cotizaciones_fotos`;
CREATE TABLE `taller_cotizaciones_fotos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int NULL DEFAULT NULL,
  `nombre_foto` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Almacena un array JSON de nombres de archivo',
  `equipo_index` int NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `id_cotizacion`(`id_cotizacion` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of taller_cotizaciones_fotos
-- ----------------------------

-- ----------------------------
-- Table structure for taller_diagnosticos_cotizacion
-- ----------------------------
DROP TABLE IF EXISTS `taller_diagnosticos_cotizacion`;
CREATE TABLE `taller_diagnosticos_cotizacion`  (
  `id_diagnostico` int NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int NOT NULL,
  `diagnostico` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_diagnostico`) USING BTREE,
  UNIQUE INDEX `unique_cotizacion_diagnostico`(`id_cotizacion` ASC) USING BTREE,
  INDEX `fk_diagnosticos_cotizacion`(`id_cotizacion` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of taller_diagnosticos_cotizacion
-- ----------------------------

-- ----------------------------
-- Table structure for taller_diagnosticos_globales
-- ----------------------------
DROP TABLE IF EXISTS `taller_diagnosticos_globales`;
CREATE TABLE `taller_diagnosticos_globales`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `diagnostico` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of taller_diagnosticos_globales
-- ----------------------------

-- ----------------------------
-- Table structure for taller_observaciones_cotizacion
-- ----------------------------
DROP TABLE IF EXISTS `taller_observaciones_cotizacion`;
CREATE TABLE `taller_observaciones_cotizacion`  (
  `id_observacion` int NOT NULL AUTO_INCREMENT,
  `id_cotizacion` int NOT NULL,
  `observaciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_observacion`) USING BTREE,
  UNIQUE INDEX `unique_cotizacion_observacion`(`id_cotizacion` ASC) USING BTREE,
  INDEX `fk_observaciones_cotizacion`(`id_cotizacion` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of taller_observaciones_cotizacion
-- ----------------------------

-- ----------------------------
-- Table structure for taller_repuestos_cotis
-- ----------------------------
DROP TABLE IF EXISTS `taller_repuestos_cotis`;
CREATE TABLE `taller_repuestos_cotis`  (
  `id_repuesto_coti` int NOT NULL AUTO_INCREMENT,
  `id_coti` int NOT NULL,
  `id_repuesto` int NULL DEFAULT NULL,
  `id_producto` int NULL DEFAULT NULL,
  `tipo_item` enum('producto','repuesto') CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL DEFAULT 'repuesto',
  `cantidad` int NULL DEFAULT NULL,
  `precio` decimal(10, 2) NULL DEFAULT NULL,
  `costo` decimal(10, 2) NULL DEFAULT NULL,
  `precioEspecial` decimal(10, 2) NULL DEFAULT NULL,
  `id_cotizacion_equipo` int NULL DEFAULT NULL,
  PRIMARY KEY (`id_repuesto_coti`) USING BTREE,
  INDEX `id_coti`(`id_coti` ASC) USING BTREE,
  INDEX `id_repuesto`(`id_repuesto` ASC) USING BTREE,
  INDEX `fk_cotizacion_equipo`(`id_cotizacion_equipo` ASC) USING BTREE,
  CONSTRAINT `fk_cotizacion_equipo` FOREIGN KEY (`id_cotizacion_equipo`) REFERENCES `taller_cotizaciones_equipos` (`id_cotizacion_equipo`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `taller_repuestos_cotis_ibfk_1` FOREIGN KEY (`id_coti`) REFERENCES `taller_cotizaciones` (`id_cotizacion`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `taller_repuestos_cotis_ibfk_2` FOREIGN KEY (`id_repuesto`) REFERENCES `repuestos` (`id_repuesto`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 150 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of taller_repuestos_cotis
-- ----------------------------
INSERT INTO `taller_repuestos_cotis` VALUES (125, 14, NULL, 20, 'producto', 1, 3300.00, 0.00, NULL, 97);
INSERT INTO `taller_repuestos_cotis` VALUES (126, 14, NULL, 18, 'producto', 1, 3100.00, 1.00, NULL, 98);
INSERT INTO `taller_repuestos_cotis` VALUES (127, 14, NULL, 30, 'producto', 1, 3900.00, 0.00, NULL, 99);
INSERT INTO `taller_repuestos_cotis` VALUES (128, 15, NULL, 20, 'producto', 1, 3300.00, 0.00, NULL, 100);
INSERT INTO `taller_repuestos_cotis` VALUES (129, 15, NULL, 18, 'producto', 1, 3100.00, 1.00, NULL, 101);
INSERT INTO `taller_repuestos_cotis` VALUES (130, 16, NULL, 20, 'producto', 1, 3300.00, 0.00, NULL, 103);
INSERT INTO `taller_repuestos_cotis` VALUES (131, 16, NULL, 18, 'producto', 1, 3100.00, 1.00, NULL, 104);
INSERT INTO `taller_repuestos_cotis` VALUES (132, 16, NULL, 30, 'producto', 1, 3900.00, 0.00, NULL, 105);
INSERT INTO `taller_repuestos_cotis` VALUES (133, 18, 2, NULL, 'repuesto', 1, 42.00, 0.00, NULL, 109);
INSERT INTO `taller_repuestos_cotis` VALUES (134, 18, 4, NULL, 'repuesto', 1, 90.00, 0.00, NULL, 109);
INSERT INTO `taller_repuestos_cotis` VALUES (135, 19, 2, NULL, 'repuesto', 1, 0.00, 0.00, NULL, 110);
INSERT INTO `taller_repuestos_cotis` VALUES (136, 19, 1, NULL, 'repuesto', 1, 0.00, 0.00, NULL, 110);
INSERT INTO `taller_repuestos_cotis` VALUES (137, 19, 2, NULL, 'repuesto', 1, 0.00, 0.00, NULL, 111);
INSERT INTO `taller_repuestos_cotis` VALUES (138, 20, NULL, 117, 'producto', 1, 3658.00, 0.00, NULL, 112);
INSERT INTO `taller_repuestos_cotis` VALUES (139, 20, NULL, 341, 'producto', 1, 3958.00, 0.00, NULL, 113);
INSERT INTO `taller_repuestos_cotis` VALUES (140, 20, NULL, 20, 'producto', 1, 3894.00, 0.00, NULL, 114);
INSERT INTO `taller_repuestos_cotis` VALUES (141, 20, NULL, 21, 'producto', 1, 4130.00, 321.00, NULL, 115);
INSERT INTO `taller_repuestos_cotis` VALUES (142, 21, NULL, 341, 'producto', 3, 3958.00, 0.00, NULL, 116);
INSERT INTO `taller_repuestos_cotis` VALUES (143, 22, NULL, 341, 'producto', 3, 3958.00, 0.00, NULL, 119);
INSERT INTO `taller_repuestos_cotis` VALUES (144, 23, NULL, 341, 'producto', 3, 3958.00, 0.00, NULL, 122);
INSERT INTO `taller_repuestos_cotis` VALUES (145, 23, NULL, 371, 'producto', 2, 4194.00, 0.00, NULL, 123);
INSERT INTO `taller_repuestos_cotis` VALUES (146, 23, NULL, 36, 'producto', 11, 3422.00, 0.00, NULL, 124);
INSERT INTO `taller_repuestos_cotis` VALUES (147, 24, NULL, 341, 'producto', 3, 3958.00, 0.00, NULL, 125);
INSERT INTO `taller_repuestos_cotis` VALUES (148, 24, NULL, 371, 'producto', 2, 4194.00, 0.00, NULL, 126);
INSERT INTO `taller_repuestos_cotis` VALUES (149, 24, NULL, 36, 'producto', 11, 3422.00, 0.00, NULL, 127);

-- ----------------------------
-- Table structure for tamsporte_persona
-- ----------------------------
DROP TABLE IF EXISTS `tamsporte_persona`;
CREATE TABLE `tamsporte_persona`  (
  `tampo_id` int NOT NULL,
  `ruc` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `razon_social` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `direccion` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`tampo_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tamsporte_persona
-- ----------------------------
INSERT INTO `tamsporte_persona` VALUES (0, '20605571094', 'STORE LINGERIE SOCIEDAD ANONIMA CERRADA', 'JR. CAJAMARCA NRO 435 HUANCAYO CERCADO ');

-- ----------------------------
-- Table structure for tecnicos
-- ----------------------------
DROP TABLE IF EXISTS `tecnicos`;
CREATE TABLE `tecnicos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 11 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tecnicos
-- ----------------------------
INSERT INTO `tecnicos` VALUES (2, 'EDUARDO');
INSERT INTO `tecnicos` VALUES (4, 'RODRIGO');
INSERT INTO `tecnicos` VALUES (8, 'ARTURO');
INSERT INTO `tecnicos` VALUES (9, 'GINO');
INSERT INTO `tecnicos` VALUES (10, 'GERARDO');

-- ----------------------------
-- Table structure for terminos_repuestos
-- ----------------------------
DROP TABLE IF EXISTS `terminos_repuestos`;
CREATE TABLE `terminos_repuestos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `activo` tinyint(1) NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 2 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of terminos_repuestos
-- ----------------------------
INSERT INTO `terminos_repuestos` VALUES (1, 'Precios unitarios No Incluyen I.G.V.\r\nForma de Pago: Contado y/o tramite de factura\r\nEmitir Orden de Servicio a nombre de Comercial & Industrial J.V.C. S.A.C.\r\nTiempo de Entrega: 04 dias habiles luego de recibir OS.\r\nValidez de Cotizacion: 07 dias habiles\r\nGarantia: 12 meses defecto de fabrica', 1, '2025-07-07 11:10:45');

-- ----------------------------
-- Table structure for tipo_pago
-- ----------------------------
DROP TABLE IF EXISTS `tipo_pago`;
CREATE TABLE `tipo_pago`  (
  `tipo_pago_id` int NOT NULL,
  `nombre` varchar(200) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`tipo_pago_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tipo_pago
-- ----------------------------
INSERT INTO `tipo_pago` VALUES (1, 'Contado');
INSERT INTO `tipo_pago` VALUES (2, 'Credito');

-- ----------------------------
-- Table structure for tipos_archivo_interno
-- ----------------------------
DROP TABLE IF EXISTS `tipos_archivo_interno`;
CREATE TABLE `tipos_archivo_interno`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nombre`(`nombre` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tipos_archivo_interno
-- ----------------------------
INSERT INTO `tipos_archivo_interno` VALUES (1, 'MEMO', '2025-06-14 09:09:57', '2025-06-14 09:09:57');
INSERT INTO `tipos_archivo_interno` VALUES (2, 'INFORME', '2025-06-14 09:09:57', '2025-06-14 09:09:57');
INSERT INTO `tipos_archivo_interno` VALUES (3, 'ACTA', '2025-06-14 09:09:57', '2025-06-14 09:09:57');
INSERT INTO `tipos_archivo_interno` VALUES (4, 'REPORTE', '2025-06-14 09:09:57', '2025-06-14 09:09:57');

-- ----------------------------
-- Table structure for tipos_carta
-- ----------------------------
DROP TABLE IF EXISTS `tipos_carta`;
CREATE TABLE `tipos_carta`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nombre`(`nombre` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tipos_carta
-- ----------------------------
INSERT INTO `tipos_carta` VALUES (2, 'FORMAL', '2025-06-14 09:08:02', '2025-06-14 09:08:02');

-- ----------------------------
-- Table structure for tipos_costancia
-- ----------------------------
DROP TABLE IF EXISTS `tipos_costancia`;
CREATE TABLE `tipos_costancia`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nombre`(`nombre` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 6 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tipos_costancia
-- ----------------------------
INSERT INTO `tipos_costancia` VALUES (1, 'MANTENIMIENTO', '2025-06-14 09:09:57', '2025-06-14 09:09:57');
INSERT INTO `tipos_costancia` VALUES (2, 'ANTIGÜEDAD DE EQUIPO', '2025-06-14 09:09:57', '2025-06-14 09:09:57');
INSERT INTO `tipos_costancia` VALUES (3, 'GARANTÍA', '2025-06-14 09:09:57', '2025-06-14 09:09:57');
INSERT INTO `tipos_costancia` VALUES (4, 'SERVICIO', '2025-06-14 09:09:57', '2025-06-14 09:09:57');
INSERT INTO `tipos_costancia` VALUES (5, 'CAPACITACIÓN', '2025-06-14 09:09:57', '2025-06-14 09:09:57');

-- ----------------------------
-- Table structure for tipos_informe
-- ----------------------------
DROP TABLE IF EXISTS `tipos_informe`;
CREATE TABLE `tipos_informe`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `activo` tinyint(1) NULL DEFAULT 1,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nombre`(`nombre` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tipos_informe
-- ----------------------------
INSERT INTO `tipos_informe` VALUES (1, 'INFORME DE MANTENIMIENTO PREVENTIVO', 1, '2025-06-03 16:49:28', '2025-08-29 16:31:50');
INSERT INTO `tipos_informe` VALUES (2, 'INFORME DE DIAGNÓSTICO TÉCNICO', 1, '2025-06-03 16:49:28', '2025-08-29 16:28:04');
INSERT INTO `tipos_informe` VALUES (3, 'INFORME DE SERVICIO TÉCNICO EJECUTADO', 1, '2025-06-03 16:49:28', '2025-08-29 16:31:12');
INSERT INTO `tipos_informe` VALUES (6, 'INFORME DE MANTENIMIENTO CORRECTIVO', 1, '2025-08-29 16:31:55', '2025-08-29 16:31:55');
INSERT INTO `tipos_informe` VALUES (7, 'INFORME DE ATENCIÓN EN GARANTÍA', 1, '2025-08-29 16:32:00', '2025-08-29 16:32:00');
INSERT INTO `tipos_informe` VALUES (8, 'INFORME DE VISITA TÉCNICO / COMERCIAL', 1, '2025-08-29 16:32:12', '2025-08-29 16:32:12');

-- ----------------------------
-- Table structure for tipos_otros_archivos
-- ----------------------------
DROP TABLE IF EXISTS `tipos_otros_archivos`;
CREATE TABLE `tipos_otros_archivos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `nombre`(`nombre` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 5 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of tipos_otros_archivos
-- ----------------------------
INSERT INTO `tipos_otros_archivos` VALUES (1, 'MEMO', '2025-06-14 09:09:57', '2025-06-14 09:09:57');
INSERT INTO `tipos_otros_archivos` VALUES (2, 'INFORME', '2025-06-14 09:09:57', '2025-06-14 09:09:57');
INSERT INTO `tipos_otros_archivos` VALUES (3, 'ACTA', '2025-06-14 09:09:57', '2025-06-14 09:09:57');
INSERT INTO `tipos_otros_archivos` VALUES (4, 'REPORTE', '2025-06-14 09:09:57', '2025-06-14 09:09:57');

-- ----------------------------
-- Table structure for ubigeo_inei
-- ----------------------------
DROP TABLE IF EXISTS `ubigeo_inei`;
CREATE TABLE `ubigeo_inei`  (
  `id_ubigeo` int NOT NULL,
  `departamento` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `provincia` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `distrito` varchar(2) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `nombre` varchar(45) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id_ubigeo`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ubigeo_inei
-- ----------------------------
INSERT INTO `ubigeo_inei` VALUES (1, '01', '00', '00', 'AMAZONAS');
INSERT INTO `ubigeo_inei` VALUES (2, '01', '01', '00', 'CHACHAPOYAS');
INSERT INTO `ubigeo_inei` VALUES (3, '01', '01', '01', 'CHACHAPOYAS');
INSERT INTO `ubigeo_inei` VALUES (4, '01', '01', '02', 'ASUNCION');
INSERT INTO `ubigeo_inei` VALUES (5, '01', '01', '03', 'BALSAS');
INSERT INTO `ubigeo_inei` VALUES (6, '01', '01', '04', 'CHETO');
INSERT INTO `ubigeo_inei` VALUES (7, '01', '01', '05', 'CHILIQUIN');
INSERT INTO `ubigeo_inei` VALUES (8, '01', '01', '06', 'CHUQUIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (9, '01', '01', '07', 'GRANADA');
INSERT INTO `ubigeo_inei` VALUES (10, '01', '01', '08', 'HUANCAS');
INSERT INTO `ubigeo_inei` VALUES (11, '01', '01', '09', 'LA JALCA');
INSERT INTO `ubigeo_inei` VALUES (12, '01', '01', '10', 'LEIMEBAMBA');
INSERT INTO `ubigeo_inei` VALUES (13, '01', '01', '11', 'LEVANTO');
INSERT INTO `ubigeo_inei` VALUES (14, '01', '01', '12', 'MAGDALENA');
INSERT INTO `ubigeo_inei` VALUES (15, '01', '01', '13', 'MARISCAL CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (16, '01', '01', '14', 'MOLINOPAMPA');
INSERT INTO `ubigeo_inei` VALUES (17, '01', '01', '15', 'MONTEVIDEO');
INSERT INTO `ubigeo_inei` VALUES (18, '01', '01', '16', 'OLLEROS');
INSERT INTO `ubigeo_inei` VALUES (19, '01', '01', '17', 'QUINJALCA');
INSERT INTO `ubigeo_inei` VALUES (20, '01', '01', '18', 'SAN FRANCISCO DE DAGUAS');
INSERT INTO `ubigeo_inei` VALUES (21, '01', '01', '19', 'SAN ISIDRO DE MAINO');
INSERT INTO `ubigeo_inei` VALUES (22, '01', '01', '20', 'SOLOCO');
INSERT INTO `ubigeo_inei` VALUES (23, '01', '01', '21', 'SONCHE');
INSERT INTO `ubigeo_inei` VALUES (24, '01', '02', '00', 'BAGUA');
INSERT INTO `ubigeo_inei` VALUES (25, '01', '02', '01', 'BAGUA');
INSERT INTO `ubigeo_inei` VALUES (26, '01', '02', '02', 'ARAMANGO');
INSERT INTO `ubigeo_inei` VALUES (27, '01', '02', '03', 'COPALLIN');
INSERT INTO `ubigeo_inei` VALUES (28, '01', '02', '04', 'EL PARCO');
INSERT INTO `ubigeo_inei` VALUES (29, '01', '02', '05', 'IMAZA');
INSERT INTO `ubigeo_inei` VALUES (30, '01', '02', '06', 'LA PECA');
INSERT INTO `ubigeo_inei` VALUES (31, '01', '03', '00', 'BONGARA');
INSERT INTO `ubigeo_inei` VALUES (32, '01', '03', '01', 'JUMBILLA');
INSERT INTO `ubigeo_inei` VALUES (33, '01', '03', '02', 'CHISQUILLA');
INSERT INTO `ubigeo_inei` VALUES (34, '01', '03', '03', 'CHURUJA');
INSERT INTO `ubigeo_inei` VALUES (35, '01', '03', '04', 'COROSHA');
INSERT INTO `ubigeo_inei` VALUES (36, '01', '03', '05', 'CUISPES');
INSERT INTO `ubigeo_inei` VALUES (37, '01', '03', '06', 'FLORIDA');
INSERT INTO `ubigeo_inei` VALUES (38, '01', '03', '07', 'JAZÁN');
INSERT INTO `ubigeo_inei` VALUES (39, '01', '03', '08', 'RECTA');
INSERT INTO `ubigeo_inei` VALUES (40, '01', '03', '09', 'SAN CARLOS');
INSERT INTO `ubigeo_inei` VALUES (41, '01', '03', '10', 'SHIPASBAMBA');
INSERT INTO `ubigeo_inei` VALUES (42, '01', '03', '11', 'VALERA');
INSERT INTO `ubigeo_inei` VALUES (43, '01', '03', '12', 'YAMBRASBAMBA');
INSERT INTO `ubigeo_inei` VALUES (44, '01', '04', '00', 'CONDORCANQUI');
INSERT INTO `ubigeo_inei` VALUES (45, '01', '04', '01', 'NIEVA');
INSERT INTO `ubigeo_inei` VALUES (46, '01', '04', '02', 'EL CENEPA');
INSERT INTO `ubigeo_inei` VALUES (47, '01', '04', '03', 'RIO SANTIAGO');
INSERT INTO `ubigeo_inei` VALUES (48, '01', '05', '00', 'LUYA');
INSERT INTO `ubigeo_inei` VALUES (49, '01', '05', '01', 'LAMUD');
INSERT INTO `ubigeo_inei` VALUES (50, '01', '05', '02', 'CAMPORREDONDO');
INSERT INTO `ubigeo_inei` VALUES (51, '01', '05', '03', 'COCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (52, '01', '05', '04', 'COLCAMAR');
INSERT INTO `ubigeo_inei` VALUES (53, '01', '05', '05', 'CONILA');
INSERT INTO `ubigeo_inei` VALUES (54, '01', '05', '06', 'INGUILPATA');
INSERT INTO `ubigeo_inei` VALUES (55, '01', '05', '07', 'LONGUITA');
INSERT INTO `ubigeo_inei` VALUES (56, '01', '05', '08', 'LONYA CHICO');
INSERT INTO `ubigeo_inei` VALUES (57, '01', '05', '09', 'LUYA');
INSERT INTO `ubigeo_inei` VALUES (58, '01', '05', '10', 'LUYA VIEJO');
INSERT INTO `ubigeo_inei` VALUES (59, '01', '05', '11', 'MARIA');
INSERT INTO `ubigeo_inei` VALUES (60, '01', '05', '12', 'OCALLI');
INSERT INTO `ubigeo_inei` VALUES (61, '01', '05', '13', 'OCUMAL');
INSERT INTO `ubigeo_inei` VALUES (62, '01', '05', '14', 'PISUQUIA');
INSERT INTO `ubigeo_inei` VALUES (63, '01', '05', '15', 'PROVIDENCIA');
INSERT INTO `ubigeo_inei` VALUES (64, '01', '05', '16', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (65, '01', '05', '17', 'SAN FRANCISCO DEL YESO');
INSERT INTO `ubigeo_inei` VALUES (66, '01', '05', '18', 'SAN JERONIMO');
INSERT INTO `ubigeo_inei` VALUES (67, '01', '05', '19', 'SAN JUAN DE LOPECANCHA');
INSERT INTO `ubigeo_inei` VALUES (68, '01', '05', '20', 'SANTA CATALINA');
INSERT INTO `ubigeo_inei` VALUES (69, '01', '05', '21', 'SANTO TOMAS');
INSERT INTO `ubigeo_inei` VALUES (70, '01', '05', '22', 'TINGO');
INSERT INTO `ubigeo_inei` VALUES (71, '01', '05', '23', 'TRITA');
INSERT INTO `ubigeo_inei` VALUES (72, '01', '06', '00', 'RODRIGUEZ DE MENDOZA');
INSERT INTO `ubigeo_inei` VALUES (73, '01', '06', '01', 'SAN NICOLAS');
INSERT INTO `ubigeo_inei` VALUES (74, '01', '06', '02', 'CHIRIMOTO');
INSERT INTO `ubigeo_inei` VALUES (75, '01', '06', '03', 'COCHAMAL');
INSERT INTO `ubigeo_inei` VALUES (76, '01', '06', '04', 'HUAMBO');
INSERT INTO `ubigeo_inei` VALUES (77, '01', '06', '05', 'LIMABAMBA');
INSERT INTO `ubigeo_inei` VALUES (78, '01', '06', '06', 'LONGAR');
INSERT INTO `ubigeo_inei` VALUES (79, '01', '06', '07', 'MARISCAL BENAVIDES');
INSERT INTO `ubigeo_inei` VALUES (80, '01', '06', '08', 'MILPUC');
INSERT INTO `ubigeo_inei` VALUES (81, '01', '06', '09', 'OMIA');
INSERT INTO `ubigeo_inei` VALUES (82, '01', '06', '10', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (83, '01', '06', '11', 'TOTORA');
INSERT INTO `ubigeo_inei` VALUES (84, '01', '06', '12', 'VISTA ALEGRE');
INSERT INTO `ubigeo_inei` VALUES (85, '01', '07', '00', 'UTCUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (86, '01', '07', '01', 'BAGUA GRANDE');
INSERT INTO `ubigeo_inei` VALUES (87, '01', '07', '02', 'CAJARURO');
INSERT INTO `ubigeo_inei` VALUES (88, '01', '07', '03', 'CUMBA');
INSERT INTO `ubigeo_inei` VALUES (89, '01', '07', '04', 'EL MILAGRO');
INSERT INTO `ubigeo_inei` VALUES (90, '01', '07', '05', 'JAMALCA');
INSERT INTO `ubigeo_inei` VALUES (91, '01', '07', '06', 'LONYA GRANDE');
INSERT INTO `ubigeo_inei` VALUES (92, '01', '07', '07', 'YAMON');
INSERT INTO `ubigeo_inei` VALUES (93, '02', '00', '00', 'ANCASH');
INSERT INTO `ubigeo_inei` VALUES (94, '02', '01', '00', 'HUARAZ');
INSERT INTO `ubigeo_inei` VALUES (95, '02', '01', '01', 'HUARAZ');
INSERT INTO `ubigeo_inei` VALUES (96, '02', '01', '02', 'COCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (97, '02', '01', '03', 'COLCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (98, '02', '01', '04', 'HUANCHAY');
INSERT INTO `ubigeo_inei` VALUES (99, '02', '01', '05', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (100, '02', '01', '06', 'JANGAS');
INSERT INTO `ubigeo_inei` VALUES (101, '02', '01', '07', 'LA LIBERTAD');
INSERT INTO `ubigeo_inei` VALUES (102, '02', '01', '08', 'OLLEROS');
INSERT INTO `ubigeo_inei` VALUES (103, '02', '01', '09', 'PAMPAS');
INSERT INTO `ubigeo_inei` VALUES (104, '02', '01', '10', 'PARIACOTO');
INSERT INTO `ubigeo_inei` VALUES (105, '02', '01', '11', 'PIRA');
INSERT INTO `ubigeo_inei` VALUES (106, '02', '01', '12', 'TARICA');
INSERT INTO `ubigeo_inei` VALUES (107, '02', '02', '00', 'AIJA');
INSERT INTO `ubigeo_inei` VALUES (108, '02', '02', '01', 'AIJA');
INSERT INTO `ubigeo_inei` VALUES (109, '02', '02', '02', 'CORIS');
INSERT INTO `ubigeo_inei` VALUES (110, '02', '02', '03', 'HUACLLAN');
INSERT INTO `ubigeo_inei` VALUES (111, '02', '02', '04', 'LA MERCED');
INSERT INTO `ubigeo_inei` VALUES (112, '02', '02', '05', 'SUCCHA');
INSERT INTO `ubigeo_inei` VALUES (113, '02', '03', '00', 'ANTONIO RAYMONDI');
INSERT INTO `ubigeo_inei` VALUES (114, '02', '03', '01', 'LLAMELLIN');
INSERT INTO `ubigeo_inei` VALUES (115, '02', '03', '02', 'ACZO');
INSERT INTO `ubigeo_inei` VALUES (116, '02', '03', '03', 'CHACCHO');
INSERT INTO `ubigeo_inei` VALUES (117, '02', '03', '04', 'CHINGAS');
INSERT INTO `ubigeo_inei` VALUES (118, '02', '03', '05', 'MIRGAS');
INSERT INTO `ubigeo_inei` VALUES (119, '02', '03', '06', 'SAN JUAN DE RONTOY');
INSERT INTO `ubigeo_inei` VALUES (120, '02', '04', '00', 'ASUNCION');
INSERT INTO `ubigeo_inei` VALUES (121, '02', '04', '01', 'CHACAS');
INSERT INTO `ubigeo_inei` VALUES (122, '02', '04', '02', 'ACOCHACA');
INSERT INTO `ubigeo_inei` VALUES (123, '02', '05', '00', 'BOLOGNESI');
INSERT INTO `ubigeo_inei` VALUES (124, '02', '05', '01', 'CHIQUIAN');
INSERT INTO `ubigeo_inei` VALUES (125, '02', '05', '02', 'ABELARDO PARDO LEZAMETA');
INSERT INTO `ubigeo_inei` VALUES (126, '02', '05', '03', 'ANTONIO RAYMONDI');
INSERT INTO `ubigeo_inei` VALUES (127, '02', '05', '04', 'AQUIA');
INSERT INTO `ubigeo_inei` VALUES (128, '02', '05', '05', 'CAJACAY');
INSERT INTO `ubigeo_inei` VALUES (129, '02', '05', '06', 'CANIS');
INSERT INTO `ubigeo_inei` VALUES (130, '02', '05', '07', 'COLQUIOC');
INSERT INTO `ubigeo_inei` VALUES (131, '02', '05', '08', 'HUALLANCA');
INSERT INTO `ubigeo_inei` VALUES (132, '02', '05', '09', 'HUASTA');
INSERT INTO `ubigeo_inei` VALUES (133, '02', '05', '10', 'HUAYLLACAYAN');
INSERT INTO `ubigeo_inei` VALUES (134, '02', '05', '11', 'LA PRIMAVERA');
INSERT INTO `ubigeo_inei` VALUES (135, '02', '05', '12', 'MANGAS');
INSERT INTO `ubigeo_inei` VALUES (136, '02', '05', '13', 'PACLLON');
INSERT INTO `ubigeo_inei` VALUES (137, '02', '05', '14', 'SAN MIGUEL DE CORPANQUI');
INSERT INTO `ubigeo_inei` VALUES (138, '02', '05', '15', 'TICLLOS');
INSERT INTO `ubigeo_inei` VALUES (139, '02', '06', '00', 'CARHUAZ');
INSERT INTO `ubigeo_inei` VALUES (140, '02', '06', '01', 'CARHUAZ');
INSERT INTO `ubigeo_inei` VALUES (141, '02', '06', '02', 'ACOPAMPA');
INSERT INTO `ubigeo_inei` VALUES (142, '02', '06', '03', 'AMASHCA');
INSERT INTO `ubigeo_inei` VALUES (143, '02', '06', '04', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (144, '02', '06', '05', 'ATAQUERO');
INSERT INTO `ubigeo_inei` VALUES (145, '02', '06', '06', 'MARCARA');
INSERT INTO `ubigeo_inei` VALUES (146, '02', '06', '07', 'PARIAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (147, '02', '06', '08', 'SAN MIGUEL DE ACO');
INSERT INTO `ubigeo_inei` VALUES (148, '02', '06', '09', 'SHILLA');
INSERT INTO `ubigeo_inei` VALUES (149, '02', '06', '10', 'TINCO');
INSERT INTO `ubigeo_inei` VALUES (150, '02', '06', '11', 'YUNGAR');
INSERT INTO `ubigeo_inei` VALUES (151, '02', '07', '00', 'CARLOS FERMIN FITZCARRALD');
INSERT INTO `ubigeo_inei` VALUES (152, '02', '07', '01', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (153, '02', '07', '02', 'SAN NICOLAS');
INSERT INTO `ubigeo_inei` VALUES (154, '02', '07', '03', 'YAUYA');
INSERT INTO `ubigeo_inei` VALUES (155, '02', '08', '00', 'CASMA');
INSERT INTO `ubigeo_inei` VALUES (156, '02', '08', '01', 'CASMA');
INSERT INTO `ubigeo_inei` VALUES (157, '02', '08', '02', 'BUENA VISTA ALTA');
INSERT INTO `ubigeo_inei` VALUES (158, '02', '08', '03', 'COMANDANTE NOEL');
INSERT INTO `ubigeo_inei` VALUES (159, '02', '08', '04', 'YAUTAN');
INSERT INTO `ubigeo_inei` VALUES (160, '02', '09', '00', 'CORONGO');
INSERT INTO `ubigeo_inei` VALUES (161, '02', '09', '01', 'CORONGO');
INSERT INTO `ubigeo_inei` VALUES (162, '02', '09', '02', 'ACO');
INSERT INTO `ubigeo_inei` VALUES (163, '02', '09', '03', 'BAMBAS');
INSERT INTO `ubigeo_inei` VALUES (164, '02', '09', '04', 'CUSCA');
INSERT INTO `ubigeo_inei` VALUES (165, '02', '09', '05', 'LA PAMPA');
INSERT INTO `ubigeo_inei` VALUES (166, '02', '09', '06', 'YANAC');
INSERT INTO `ubigeo_inei` VALUES (167, '02', '09', '07', 'YUPAN');
INSERT INTO `ubigeo_inei` VALUES (168, '02', '10', '00', 'HUARI');
INSERT INTO `ubigeo_inei` VALUES (169, '02', '10', '01', 'HUARI');
INSERT INTO `ubigeo_inei` VALUES (170, '02', '10', '02', 'ANRA');
INSERT INTO `ubigeo_inei` VALUES (171, '02', '10', '03', 'CAJAY');
INSERT INTO `ubigeo_inei` VALUES (172, '02', '10', '04', 'CHAVIN DE HUANTAR');
INSERT INTO `ubigeo_inei` VALUES (173, '02', '10', '05', 'HUACACHI');
INSERT INTO `ubigeo_inei` VALUES (174, '02', '10', '06', 'HUACCHIS');
INSERT INTO `ubigeo_inei` VALUES (175, '02', '10', '07', 'HUACHIS');
INSERT INTO `ubigeo_inei` VALUES (176, '02', '10', '08', 'HUANTAR');
INSERT INTO `ubigeo_inei` VALUES (177, '02', '10', '09', 'MASIN');
INSERT INTO `ubigeo_inei` VALUES (178, '02', '10', '10', 'PAUCAS');
INSERT INTO `ubigeo_inei` VALUES (179, '02', '10', '11', 'PONTO');
INSERT INTO `ubigeo_inei` VALUES (180, '02', '10', '12', 'RAHUAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (181, '02', '10', '13', 'RAPAYAN');
INSERT INTO `ubigeo_inei` VALUES (182, '02', '10', '14', 'SAN MARCOS');
INSERT INTO `ubigeo_inei` VALUES (183, '02', '10', '15', 'SAN PEDRO DE CHANA');
INSERT INTO `ubigeo_inei` VALUES (184, '02', '10', '16', 'UCO');
INSERT INTO `ubigeo_inei` VALUES (185, '02', '11', '00', 'HUARMEY');
INSERT INTO `ubigeo_inei` VALUES (186, '02', '11', '01', 'HUARMEY');
INSERT INTO `ubigeo_inei` VALUES (187, '02', '11', '02', 'COCHAPETI');
INSERT INTO `ubigeo_inei` VALUES (188, '02', '11', '03', 'CULEBRAS');
INSERT INTO `ubigeo_inei` VALUES (189, '02', '11', '04', 'HUAYAN');
INSERT INTO `ubigeo_inei` VALUES (190, '02', '11', '05', 'MALVAS');
INSERT INTO `ubigeo_inei` VALUES (191, '02', '12', '00', 'HUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (192, '02', '12', '01', 'CARAZ');
INSERT INTO `ubigeo_inei` VALUES (193, '02', '12', '02', 'HUALLANCA');
INSERT INTO `ubigeo_inei` VALUES (194, '02', '12', '03', 'HUATA');
INSERT INTO `ubigeo_inei` VALUES (195, '02', '12', '04', 'HUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (196, '02', '12', '05', 'MATO');
INSERT INTO `ubigeo_inei` VALUES (197, '02', '12', '06', 'PAMPAROMAS');
INSERT INTO `ubigeo_inei` VALUES (198, '02', '12', '07', 'PUEBLO LIBRE');
INSERT INTO `ubigeo_inei` VALUES (199, '02', '12', '08', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (200, '02', '12', '09', 'SANTO TORIBIO');
INSERT INTO `ubigeo_inei` VALUES (201, '02', '12', '10', 'YURACMARCA');
INSERT INTO `ubigeo_inei` VALUES (202, '02', '13', '00', 'MARISCAL LUZURIAGA');
INSERT INTO `ubigeo_inei` VALUES (203, '02', '13', '01', 'PISCOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (204, '02', '13', '02', 'CASCA');
INSERT INTO `ubigeo_inei` VALUES (205, '02', '13', '03', 'ELEAZAR GUZMAN BARRON');
INSERT INTO `ubigeo_inei` VALUES (206, '02', '13', '04', 'FIDEL OLIVAS ESCUDERO');
INSERT INTO `ubigeo_inei` VALUES (207, '02', '13', '05', 'LLAMA');
INSERT INTO `ubigeo_inei` VALUES (208, '02', '13', '06', 'LLUMPA');
INSERT INTO `ubigeo_inei` VALUES (209, '02', '13', '07', 'LUCMA');
INSERT INTO `ubigeo_inei` VALUES (210, '02', '13', '08', 'MUSGA');
INSERT INTO `ubigeo_inei` VALUES (211, '02', '14', '00', 'OCROS');
INSERT INTO `ubigeo_inei` VALUES (212, '02', '14', '01', 'OCROS');
INSERT INTO `ubigeo_inei` VALUES (213, '02', '14', '02', 'ACAS');
INSERT INTO `ubigeo_inei` VALUES (214, '02', '14', '03', 'CAJAMARQUILLA');
INSERT INTO `ubigeo_inei` VALUES (215, '02', '14', '04', 'CARHUAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (216, '02', '14', '05', 'COCHAS');
INSERT INTO `ubigeo_inei` VALUES (217, '02', '14', '06', 'CONGAS');
INSERT INTO `ubigeo_inei` VALUES (218, '02', '14', '07', 'LLIPA');
INSERT INTO `ubigeo_inei` VALUES (219, '02', '14', '08', 'SAN CRISTOBAL DE RAJAN');
INSERT INTO `ubigeo_inei` VALUES (220, '02', '14', '09', 'SAN PEDRO');
INSERT INTO `ubigeo_inei` VALUES (221, '02', '14', '10', 'SANTIAGO DE CHILCAS');
INSERT INTO `ubigeo_inei` VALUES (222, '02', '15', '00', 'PALLASCA');
INSERT INTO `ubigeo_inei` VALUES (223, '02', '15', '01', 'CABANA');
INSERT INTO `ubigeo_inei` VALUES (224, '02', '15', '02', 'BOLOGNESI');
INSERT INTO `ubigeo_inei` VALUES (225, '02', '15', '03', 'CONCHUCOS');
INSERT INTO `ubigeo_inei` VALUES (226, '02', '15', '04', 'HUACASCHUQUE');
INSERT INTO `ubigeo_inei` VALUES (227, '02', '15', '05', 'HUANDOVAL');
INSERT INTO `ubigeo_inei` VALUES (228, '02', '15', '06', 'LACABAMBA');
INSERT INTO `ubigeo_inei` VALUES (229, '02', '15', '07', 'LLAPO');
INSERT INTO `ubigeo_inei` VALUES (230, '02', '15', '08', 'PALLASCA');
INSERT INTO `ubigeo_inei` VALUES (231, '02', '15', '09', 'PAMPAS');
INSERT INTO `ubigeo_inei` VALUES (232, '02', '15', '10', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (233, '02', '15', '11', 'TAUCA');
INSERT INTO `ubigeo_inei` VALUES (234, '02', '16', '00', 'POMABAMBA');
INSERT INTO `ubigeo_inei` VALUES (235, '02', '16', '01', 'POMABAMBA');
INSERT INTO `ubigeo_inei` VALUES (236, '02', '16', '02', 'HUAYLLAN');
INSERT INTO `ubigeo_inei` VALUES (237, '02', '16', '03', 'PAROBAMBA');
INSERT INTO `ubigeo_inei` VALUES (238, '02', '16', '04', 'QUINUABAMBA');
INSERT INTO `ubigeo_inei` VALUES (239, '02', '17', '00', 'RECUAY');
INSERT INTO `ubigeo_inei` VALUES (240, '02', '17', '01', 'RECUAY');
INSERT INTO `ubigeo_inei` VALUES (241, '02', '17', '02', 'CATAC');
INSERT INTO `ubigeo_inei` VALUES (242, '02', '17', '03', 'COTAPARACO');
INSERT INTO `ubigeo_inei` VALUES (243, '02', '17', '04', 'HUAYLLAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (244, '02', '17', '05', 'LLACLLIN');
INSERT INTO `ubigeo_inei` VALUES (245, '02', '17', '06', 'MARCA');
INSERT INTO `ubigeo_inei` VALUES (246, '02', '17', '07', 'PAMPAS CHICO');
INSERT INTO `ubigeo_inei` VALUES (247, '02', '17', '08', 'PARARIN');
INSERT INTO `ubigeo_inei` VALUES (248, '02', '17', '09', 'TAPACOCHA');
INSERT INTO `ubigeo_inei` VALUES (249, '02', '17', '10', 'TICAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (250, '02', '18', '00', 'SANTA');
INSERT INTO `ubigeo_inei` VALUES (251, '02', '18', '01', 'CHIMBOTE');
INSERT INTO `ubigeo_inei` VALUES (252, '02', '18', '02', 'CACERES DEL PERU');
INSERT INTO `ubigeo_inei` VALUES (253, '02', '18', '03', 'COISHCO');
INSERT INTO `ubigeo_inei` VALUES (254, '02', '18', '04', 'MACATE');
INSERT INTO `ubigeo_inei` VALUES (255, '02', '18', '05', 'MORO');
INSERT INTO `ubigeo_inei` VALUES (256, '02', '18', '06', 'NEPEÑA');
INSERT INTO `ubigeo_inei` VALUES (257, '02', '18', '07', 'SAMANCO');
INSERT INTO `ubigeo_inei` VALUES (258, '02', '18', '08', 'SANTA');
INSERT INTO `ubigeo_inei` VALUES (259, '02', '18', '09', 'NUEVO CHIMBOTE');
INSERT INTO `ubigeo_inei` VALUES (260, '02', '19', '00', 'SIHUAS');
INSERT INTO `ubigeo_inei` VALUES (261, '02', '19', '01', 'SIHUAS');
INSERT INTO `ubigeo_inei` VALUES (262, '02', '19', '02', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (263, '02', '19', '03', 'ALFONSO UGARTE');
INSERT INTO `ubigeo_inei` VALUES (264, '02', '19', '04', 'CASHAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (265, '02', '19', '05', 'CHINGALPO');
INSERT INTO `ubigeo_inei` VALUES (266, '02', '19', '06', 'HUAYLLABAMBA');
INSERT INTO `ubigeo_inei` VALUES (267, '02', '19', '07', 'QUICHES');
INSERT INTO `ubigeo_inei` VALUES (268, '02', '19', '08', 'RAGASH');
INSERT INTO `ubigeo_inei` VALUES (269, '02', '19', '09', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (270, '02', '19', '10', 'SICSIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (271, '02', '20', '00', 'YUNGAY');
INSERT INTO `ubigeo_inei` VALUES (272, '02', '20', '01', 'YUNGAY');
INSERT INTO `ubigeo_inei` VALUES (273, '02', '20', '02', 'CASCAPARA');
INSERT INTO `ubigeo_inei` VALUES (274, '02', '20', '03', 'MANCOS');
INSERT INTO `ubigeo_inei` VALUES (275, '02', '20', '04', 'MATACOTO');
INSERT INTO `ubigeo_inei` VALUES (276, '02', '20', '05', 'QUILLO');
INSERT INTO `ubigeo_inei` VALUES (277, '02', '20', '06', 'RANRAHIRCA');
INSERT INTO `ubigeo_inei` VALUES (278, '02', '20', '07', 'SHUPLUY');
INSERT INTO `ubigeo_inei` VALUES (279, '02', '20', '08', 'YANAMA');
INSERT INTO `ubigeo_inei` VALUES (280, '03', '00', '00', 'APURIMAC');
INSERT INTO `ubigeo_inei` VALUES (281, '03', '01', '00', 'ABANCAY');
INSERT INTO `ubigeo_inei` VALUES (282, '03', '01', '01', 'ABANCAY');
INSERT INTO `ubigeo_inei` VALUES (283, '03', '01', '02', 'CHACOCHE');
INSERT INTO `ubigeo_inei` VALUES (284, '03', '01', '03', 'CIRCA');
INSERT INTO `ubigeo_inei` VALUES (285, '03', '01', '04', 'CURAHUASI');
INSERT INTO `ubigeo_inei` VALUES (286, '03', '01', '05', 'HUANIPACA');
INSERT INTO `ubigeo_inei` VALUES (287, '03', '01', '06', 'LAMBRAMA');
INSERT INTO `ubigeo_inei` VALUES (288, '03', '01', '07', 'PICHIRHUA');
INSERT INTO `ubigeo_inei` VALUES (289, '03', '01', '08', 'SAN PEDRO DE CACHORA');
INSERT INTO `ubigeo_inei` VALUES (290, '03', '01', '09', 'TAMBURCO');
INSERT INTO `ubigeo_inei` VALUES (291, '03', '02', '00', 'ANDAHUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (292, '03', '02', '01', 'ANDAHUAYLAS');
INSERT INTO `ubigeo_inei` VALUES (293, '03', '02', '02', 'ANDARAPA');
INSERT INTO `ubigeo_inei` VALUES (294, '03', '02', '03', 'CHIARA');
INSERT INTO `ubigeo_inei` VALUES (295, '03', '02', '04', 'HUANCARAMA');
INSERT INTO `ubigeo_inei` VALUES (296, '03', '02', '05', 'HUANCARAY');
INSERT INTO `ubigeo_inei` VALUES (297, '03', '02', '06', 'HUAYANA');
INSERT INTO `ubigeo_inei` VALUES (298, '03', '02', '07', 'KISHUARA');
INSERT INTO `ubigeo_inei` VALUES (299, '03', '02', '08', 'PACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (300, '03', '02', '09', 'PACUCHA');
INSERT INTO `ubigeo_inei` VALUES (301, '03', '02', '10', 'PAMPACHIRI');
INSERT INTO `ubigeo_inei` VALUES (302, '03', '02', '11', 'POMACOCHA');
INSERT INTO `ubigeo_inei` VALUES (303, '03', '02', '12', 'SAN ANTONIO DE CACHI');
INSERT INTO `ubigeo_inei` VALUES (304, '03', '02', '13', 'SAN JERONIMO');
INSERT INTO `ubigeo_inei` VALUES (305, '03', '02', '14', 'SAN MIGUEL DE CHACCRAMPA');
INSERT INTO `ubigeo_inei` VALUES (306, '03', '02', '15', 'SANTA MARIA DE CHICMO');
INSERT INTO `ubigeo_inei` VALUES (307, '03', '02', '16', 'TALAVERA');
INSERT INTO `ubigeo_inei` VALUES (308, '03', '02', '17', 'TUMAY HUARACA');
INSERT INTO `ubigeo_inei` VALUES (309, '03', '02', '18', 'TURPO');
INSERT INTO `ubigeo_inei` VALUES (310, '03', '02', '19', 'KAQUIABAMBA');
INSERT INTO `ubigeo_inei` VALUES (311, '03', '03', '00', 'ANTABAMBA');
INSERT INTO `ubigeo_inei` VALUES (312, '03', '03', '01', 'ANTABAMBA');
INSERT INTO `ubigeo_inei` VALUES (313, '03', '03', '02', 'EL ORO');
INSERT INTO `ubigeo_inei` VALUES (314, '03', '03', '03', 'HUAQUIRCA');
INSERT INTO `ubigeo_inei` VALUES (315, '03', '03', '04', 'JUAN ESPINOZA MEDRANO');
INSERT INTO `ubigeo_inei` VALUES (316, '03', '03', '05', 'OROPESA');
INSERT INTO `ubigeo_inei` VALUES (317, '03', '03', '06', 'PACHACONAS');
INSERT INTO `ubigeo_inei` VALUES (318, '03', '03', '07', 'SABAINO');
INSERT INTO `ubigeo_inei` VALUES (319, '03', '04', '00', 'AYMARAES');
INSERT INTO `ubigeo_inei` VALUES (320, '03', '04', '01', 'CHALHUANCA');
INSERT INTO `ubigeo_inei` VALUES (321, '03', '04', '02', 'CAPAYA');
INSERT INTO `ubigeo_inei` VALUES (322, '03', '04', '03', 'CARAYBAMBA');
INSERT INTO `ubigeo_inei` VALUES (323, '03', '04', '04', 'CHAPIMARCA');
INSERT INTO `ubigeo_inei` VALUES (324, '03', '04', '05', 'COLCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (325, '03', '04', '06', 'COTARUSE');
INSERT INTO `ubigeo_inei` VALUES (326, '03', '04', '07', 'HUAYLLO');
INSERT INTO `ubigeo_inei` VALUES (327, '03', '04', '08', 'JUSTO APU SAHUARAURA');
INSERT INTO `ubigeo_inei` VALUES (328, '03', '04', '09', 'LUCRE');
INSERT INTO `ubigeo_inei` VALUES (329, '03', '04', '10', 'POCOHUANCA');
INSERT INTO `ubigeo_inei` VALUES (330, '03', '04', '11', 'SAN JUAN DE CHACÑA');
INSERT INTO `ubigeo_inei` VALUES (331, '03', '04', '12', 'SAÑAYCA');
INSERT INTO `ubigeo_inei` VALUES (332, '03', '04', '13', 'SORAYA');
INSERT INTO `ubigeo_inei` VALUES (333, '03', '04', '14', 'TAPAIRIHUA');
INSERT INTO `ubigeo_inei` VALUES (334, '03', '04', '15', 'TINTAY');
INSERT INTO `ubigeo_inei` VALUES (335, '03', '04', '16', 'TORAYA');
INSERT INTO `ubigeo_inei` VALUES (336, '03', '04', '17', 'YANACA');
INSERT INTO `ubigeo_inei` VALUES (337, '03', '05', '00', 'COTABAMBAS');
INSERT INTO `ubigeo_inei` VALUES (338, '03', '05', '01', 'TAMBOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (339, '03', '05', '02', 'COTABAMBAS');
INSERT INTO `ubigeo_inei` VALUES (340, '03', '05', '03', 'COYLLURQUI');
INSERT INTO `ubigeo_inei` VALUES (341, '03', '05', '04', 'HAQUIRA');
INSERT INTO `ubigeo_inei` VALUES (342, '03', '05', '05', 'MARA');
INSERT INTO `ubigeo_inei` VALUES (343, '03', '05', '06', 'CHALLHUAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (344, '03', '06', '00', 'CHINCHEROS');
INSERT INTO `ubigeo_inei` VALUES (345, '03', '06', '01', 'CHINCHEROS');
INSERT INTO `ubigeo_inei` VALUES (346, '03', '06', '02', 'ANCO-HUALLO');
INSERT INTO `ubigeo_inei` VALUES (347, '03', '06', '03', 'COCHARCAS');
INSERT INTO `ubigeo_inei` VALUES (348, '03', '06', '04', 'HUACCANA');
INSERT INTO `ubigeo_inei` VALUES (349, '03', '06', '05', 'OCOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (350, '03', '06', '06', 'ONGOY');
INSERT INTO `ubigeo_inei` VALUES (351, '03', '06', '07', 'URANMARCA');
INSERT INTO `ubigeo_inei` VALUES (352, '03', '06', '08', 'RANRACANCHA');
INSERT INTO `ubigeo_inei` VALUES (353, '03', '07', '00', 'GRAU');
INSERT INTO `ubigeo_inei` VALUES (354, '03', '07', '01', 'CHUQUIBAMBILLA');
INSERT INTO `ubigeo_inei` VALUES (355, '03', '07', '02', 'CURPAHUASI');
INSERT INTO `ubigeo_inei` VALUES (356, '03', '07', '03', 'GAMARRA');
INSERT INTO `ubigeo_inei` VALUES (357, '03', '07', '04', 'HUAYLLATI');
INSERT INTO `ubigeo_inei` VALUES (358, '03', '07', '05', 'MAMARA');
INSERT INTO `ubigeo_inei` VALUES (359, '03', '07', '06', 'MICAELA BASTIDAS');
INSERT INTO `ubigeo_inei` VALUES (360, '03', '07', '07', 'PATAYPAMPA');
INSERT INTO `ubigeo_inei` VALUES (361, '03', '07', '08', 'PROGRESO');
INSERT INTO `ubigeo_inei` VALUES (362, '03', '07', '09', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (363, '03', '07', '10', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (364, '03', '07', '11', 'TURPAY');
INSERT INTO `ubigeo_inei` VALUES (365, '03', '07', '12', 'VILCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (366, '03', '07', '13', 'VIRUNDO');
INSERT INTO `ubigeo_inei` VALUES (367, '03', '07', '14', 'CURASCO');
INSERT INTO `ubigeo_inei` VALUES (368, '04', '00', '00', 'AREQUIPA');
INSERT INTO `ubigeo_inei` VALUES (369, '04', '01', '00', 'AREQUIPA');
INSERT INTO `ubigeo_inei` VALUES (370, '04', '01', '01', 'AREQUIPA');
INSERT INTO `ubigeo_inei` VALUES (371, '04', '01', '02', 'ALTO SELVA ALEGRE');
INSERT INTO `ubigeo_inei` VALUES (372, '04', '01', '03', 'CAYMA');
INSERT INTO `ubigeo_inei` VALUES (373, '04', '01', '04', 'CERRO COLORADO');
INSERT INTO `ubigeo_inei` VALUES (374, '04', '01', '05', 'CHARACATO');
INSERT INTO `ubigeo_inei` VALUES (375, '04', '01', '06', 'CHIGUATA');
INSERT INTO `ubigeo_inei` VALUES (376, '04', '01', '07', 'JACOBO HUNTER');
INSERT INTO `ubigeo_inei` VALUES (377, '04', '01', '08', 'LA JOYA');
INSERT INTO `ubigeo_inei` VALUES (378, '04', '01', '09', 'MARIANO MELGAR');
INSERT INTO `ubigeo_inei` VALUES (379, '04', '01', '10', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (380, '04', '01', '11', 'MOLLEBAYA');
INSERT INTO `ubigeo_inei` VALUES (381, '04', '01', '12', 'PAUCARPATA');
INSERT INTO `ubigeo_inei` VALUES (382, '04', '01', '13', 'POCSI');
INSERT INTO `ubigeo_inei` VALUES (383, '04', '01', '14', 'POLOBAYA');
INSERT INTO `ubigeo_inei` VALUES (384, '04', '01', '15', 'QUEQUEÑA');
INSERT INTO `ubigeo_inei` VALUES (385, '04', '01', '16', 'SABANDIA');
INSERT INTO `ubigeo_inei` VALUES (386, '04', '01', '17', 'SACHACA');
INSERT INTO `ubigeo_inei` VALUES (387, '04', '01', '18', 'SAN JUAN DE SIGUAS');
INSERT INTO `ubigeo_inei` VALUES (388, '04', '01', '19', 'SAN JUAN DE TARUCANI');
INSERT INTO `ubigeo_inei` VALUES (389, '04', '01', '20', 'SANTA ISABEL DE SIGUAS');
INSERT INTO `ubigeo_inei` VALUES (390, '04', '01', '21', 'SANTA RITA DE SIGUAS');
INSERT INTO `ubigeo_inei` VALUES (391, '04', '01', '22', 'SOCABAYA');
INSERT INTO `ubigeo_inei` VALUES (392, '04', '01', '23', 'TIABAYA');
INSERT INTO `ubigeo_inei` VALUES (393, '04', '01', '24', 'UCHUMAYO');
INSERT INTO `ubigeo_inei` VALUES (394, '04', '01', '25', 'VITOR');
INSERT INTO `ubigeo_inei` VALUES (395, '04', '01', '26', 'YANAHUARA');
INSERT INTO `ubigeo_inei` VALUES (396, '04', '01', '27', 'YARABAMBA');
INSERT INTO `ubigeo_inei` VALUES (397, '04', '01', '28', 'YURA');
INSERT INTO `ubigeo_inei` VALUES (398, '04', '01', '29', 'JOSE LUIS BUSTAMANTE Y RIVERO');
INSERT INTO `ubigeo_inei` VALUES (399, '04', '02', '00', 'CAMANA');
INSERT INTO `ubigeo_inei` VALUES (400, '04', '02', '01', 'CAMANA');
INSERT INTO `ubigeo_inei` VALUES (401, '04', '02', '02', 'JOSE MARIA QUIMPER');
INSERT INTO `ubigeo_inei` VALUES (402, '04', '02', '03', 'MARIANO NICOLAS VALCARCEL');
INSERT INTO `ubigeo_inei` VALUES (403, '04', '02', '04', 'MARISCAL CACERES');
INSERT INTO `ubigeo_inei` VALUES (404, '04', '02', '05', 'NICOLAS DE PIEROLA');
INSERT INTO `ubigeo_inei` VALUES (405, '04', '02', '06', 'OCOÑA');
INSERT INTO `ubigeo_inei` VALUES (406, '04', '02', '07', 'QUILCA');
INSERT INTO `ubigeo_inei` VALUES (407, '04', '02', '08', 'SAMUEL PASTOR');
INSERT INTO `ubigeo_inei` VALUES (408, '04', '03', '00', 'CARAVELI');
INSERT INTO `ubigeo_inei` VALUES (409, '04', '03', '01', 'CARAVELI');
INSERT INTO `ubigeo_inei` VALUES (410, '04', '03', '02', 'ACARI');
INSERT INTO `ubigeo_inei` VALUES (411, '04', '03', '03', 'ATICO');
INSERT INTO `ubigeo_inei` VALUES (412, '04', '03', '04', 'ATIQUIPA');
INSERT INTO `ubigeo_inei` VALUES (413, '04', '03', '05', 'BELLA UNION');
INSERT INTO `ubigeo_inei` VALUES (414, '04', '03', '06', 'CAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (415, '04', '03', '07', 'CHALA');
INSERT INTO `ubigeo_inei` VALUES (416, '04', '03', '08', 'CHAPARRA');
INSERT INTO `ubigeo_inei` VALUES (417, '04', '03', '09', 'HUANUHUANU');
INSERT INTO `ubigeo_inei` VALUES (418, '04', '03', '10', 'JAQUI');
INSERT INTO `ubigeo_inei` VALUES (419, '04', '03', '11', 'LOMAS');
INSERT INTO `ubigeo_inei` VALUES (420, '04', '03', '12', 'QUICACHA');
INSERT INTO `ubigeo_inei` VALUES (421, '04', '03', '13', 'YAUCA');
INSERT INTO `ubigeo_inei` VALUES (422, '04', '04', '00', 'CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (423, '04', '04', '01', 'APLAO');
INSERT INTO `ubigeo_inei` VALUES (424, '04', '04', '02', 'ANDAGUA');
INSERT INTO `ubigeo_inei` VALUES (425, '04', '04', '03', 'AYO');
INSERT INTO `ubigeo_inei` VALUES (426, '04', '04', '04', 'CHACHAS');
INSERT INTO `ubigeo_inei` VALUES (427, '04', '04', '05', 'CHILCAYMARCA');
INSERT INTO `ubigeo_inei` VALUES (428, '04', '04', '06', 'CHOCO');
INSERT INTO `ubigeo_inei` VALUES (429, '04', '04', '07', 'HUANCARQUI');
INSERT INTO `ubigeo_inei` VALUES (430, '04', '04', '08', 'MACHAGUAY');
INSERT INTO `ubigeo_inei` VALUES (431, '04', '04', '09', 'ORCOPAMPA');
INSERT INTO `ubigeo_inei` VALUES (432, '04', '04', '10', 'PAMPACOLCA');
INSERT INTO `ubigeo_inei` VALUES (433, '04', '04', '11', 'TIPAN');
INSERT INTO `ubigeo_inei` VALUES (434, '04', '04', '12', 'UÑON');
INSERT INTO `ubigeo_inei` VALUES (435, '04', '04', '13', 'URACA');
INSERT INTO `ubigeo_inei` VALUES (436, '04', '04', '14', 'VIRACO');
INSERT INTO `ubigeo_inei` VALUES (437, '04', '05', '00', 'CAYLLOMA');
INSERT INTO `ubigeo_inei` VALUES (438, '04', '05', '01', 'CHIVAY');
INSERT INTO `ubigeo_inei` VALUES (439, '04', '05', '02', 'ACHOMA');
INSERT INTO `ubigeo_inei` VALUES (440, '04', '05', '03', 'CABANACONDE');
INSERT INTO `ubigeo_inei` VALUES (441, '04', '05', '04', 'CALLALLI');
INSERT INTO `ubigeo_inei` VALUES (442, '04', '05', '05', 'CAYLLOMA');
INSERT INTO `ubigeo_inei` VALUES (443, '04', '05', '06', 'COPORAQUE');
INSERT INTO `ubigeo_inei` VALUES (444, '04', '05', '07', 'HUAMBO');
INSERT INTO `ubigeo_inei` VALUES (445, '04', '05', '08', 'HUANCA');
INSERT INTO `ubigeo_inei` VALUES (446, '04', '05', '09', 'ICHUPAMPA');
INSERT INTO `ubigeo_inei` VALUES (447, '04', '05', '10', 'LARI');
INSERT INTO `ubigeo_inei` VALUES (448, '04', '05', '11', 'LLUTA');
INSERT INTO `ubigeo_inei` VALUES (449, '04', '05', '12', 'MACA');
INSERT INTO `ubigeo_inei` VALUES (450, '04', '05', '13', 'MADRIGAL');
INSERT INTO `ubigeo_inei` VALUES (451, '04', '05', '14', 'SAN ANTONIO DE CHUCA');
INSERT INTO `ubigeo_inei` VALUES (452, '04', '05', '15', 'SIBAYO');
INSERT INTO `ubigeo_inei` VALUES (453, '04', '05', '16', 'TAPAY');
INSERT INTO `ubigeo_inei` VALUES (454, '04', '05', '17', 'TISCO');
INSERT INTO `ubigeo_inei` VALUES (455, '04', '05', '18', 'TUTI');
INSERT INTO `ubigeo_inei` VALUES (456, '04', '05', '19', 'YANQUE');
INSERT INTO `ubigeo_inei` VALUES (457, '04', '05', '20', 'MAJES');
INSERT INTO `ubigeo_inei` VALUES (458, '04', '06', '00', 'CONDESUYOS');
INSERT INTO `ubigeo_inei` VALUES (459, '04', '06', '01', 'CHUQUIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (460, '04', '06', '02', 'ANDARAY');
INSERT INTO `ubigeo_inei` VALUES (461, '04', '06', '03', 'CAYARANI');
INSERT INTO `ubigeo_inei` VALUES (462, '04', '06', '04', 'CHICHAS');
INSERT INTO `ubigeo_inei` VALUES (463, '04', '06', '05', 'IRAY');
INSERT INTO `ubigeo_inei` VALUES (464, '04', '06', '06', 'RIO GRANDE');
INSERT INTO `ubigeo_inei` VALUES (465, '04', '06', '07', 'SALAMANCA');
INSERT INTO `ubigeo_inei` VALUES (466, '04', '06', '08', 'YANAQUIHUA');
INSERT INTO `ubigeo_inei` VALUES (467, '04', '07', '00', 'ISLAY');
INSERT INTO `ubigeo_inei` VALUES (468, '04', '07', '01', 'MOLLENDO');
INSERT INTO `ubigeo_inei` VALUES (469, '04', '07', '02', 'COCACHACRA');
INSERT INTO `ubigeo_inei` VALUES (470, '04', '07', '03', 'DEAN VALDIVIA');
INSERT INTO `ubigeo_inei` VALUES (471, '04', '07', '04', 'ISLAY');
INSERT INTO `ubigeo_inei` VALUES (472, '04', '07', '05', 'MEJIA');
INSERT INTO `ubigeo_inei` VALUES (473, '04', '07', '06', 'PUNTA DE BOMBON');
INSERT INTO `ubigeo_inei` VALUES (474, '04', '08', '00', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (475, '04', '08', '01', 'COTAHUASI');
INSERT INTO `ubigeo_inei` VALUES (476, '04', '08', '02', 'ALCA');
INSERT INTO `ubigeo_inei` VALUES (477, '04', '08', '03', 'CHARCANA');
INSERT INTO `ubigeo_inei` VALUES (478, '04', '08', '04', 'HUAYNACOTAS');
INSERT INTO `ubigeo_inei` VALUES (479, '04', '08', '05', 'PAMPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (480, '04', '08', '06', 'PUYCA');
INSERT INTO `ubigeo_inei` VALUES (481, '04', '08', '07', 'QUECHUALLA');
INSERT INTO `ubigeo_inei` VALUES (482, '04', '08', '08', 'SAYLA');
INSERT INTO `ubigeo_inei` VALUES (483, '04', '08', '09', 'TAURIA');
INSERT INTO `ubigeo_inei` VALUES (484, '04', '08', '10', 'TOMEPAMPA');
INSERT INTO `ubigeo_inei` VALUES (485, '04', '08', '11', 'TORO');
INSERT INTO `ubigeo_inei` VALUES (486, '05', '00', '00', 'AYACUCHO');
INSERT INTO `ubigeo_inei` VALUES (487, '05', '01', '00', 'HUAMANGA');
INSERT INTO `ubigeo_inei` VALUES (488, '05', '01', '01', 'AYACUCHO');
INSERT INTO `ubigeo_inei` VALUES (489, '05', '01', '02', 'ACOCRO');
INSERT INTO `ubigeo_inei` VALUES (490, '05', '01', '03', 'ACOS VINCHOS');
INSERT INTO `ubigeo_inei` VALUES (491, '05', '01', '04', 'CARMEN ALTO');
INSERT INTO `ubigeo_inei` VALUES (492, '05', '01', '05', 'CHIARA');
INSERT INTO `ubigeo_inei` VALUES (493, '05', '01', '06', 'OCROS');
INSERT INTO `ubigeo_inei` VALUES (494, '05', '01', '07', 'PACAYCASA');
INSERT INTO `ubigeo_inei` VALUES (495, '05', '01', '08', 'QUINUA');
INSERT INTO `ubigeo_inei` VALUES (496, '05', '01', '09', 'SAN JOSE DE TICLLAS');
INSERT INTO `ubigeo_inei` VALUES (497, '05', '01', '10', 'SAN JUAN BAUTISTA');
INSERT INTO `ubigeo_inei` VALUES (498, '05', '01', '11', 'SANTIAGO DE PISCHA');
INSERT INTO `ubigeo_inei` VALUES (499, '05', '01', '12', 'SOCOS');
INSERT INTO `ubigeo_inei` VALUES (500, '05', '01', '13', 'TAMBILLO');
INSERT INTO `ubigeo_inei` VALUES (501, '05', '01', '14', 'VINCHOS');
INSERT INTO `ubigeo_inei` VALUES (502, '05', '01', '15', 'JESÚS NAZARENO');
INSERT INTO `ubigeo_inei` VALUES (503, '05', '01', '16', 'ANDRÉS AVELINO CÁCERES DORREGAY');
INSERT INTO `ubigeo_inei` VALUES (504, '05', '02', '00', 'CANGALLO');
INSERT INTO `ubigeo_inei` VALUES (505, '05', '02', '01', 'CANGALLO');
INSERT INTO `ubigeo_inei` VALUES (506, '05', '02', '02', 'CHUSCHI');
INSERT INTO `ubigeo_inei` VALUES (507, '05', '02', '03', 'LOS MOROCHUCOS');
INSERT INTO `ubigeo_inei` VALUES (508, '05', '02', '04', 'MARIA PARADO DE BELLIDO');
INSERT INTO `ubigeo_inei` VALUES (509, '05', '02', '05', 'PARAS');
INSERT INTO `ubigeo_inei` VALUES (510, '05', '02', '06', 'TOTOS');
INSERT INTO `ubigeo_inei` VALUES (511, '05', '03', '00', 'HUANCA SANCOS');
INSERT INTO `ubigeo_inei` VALUES (512, '05', '03', '01', 'SANCOS');
INSERT INTO `ubigeo_inei` VALUES (513, '05', '03', '02', 'CARAPO');
INSERT INTO `ubigeo_inei` VALUES (514, '05', '03', '03', 'SACSAMARCA');
INSERT INTO `ubigeo_inei` VALUES (515, '05', '03', '04', 'SANTIAGO DE LUCANAMARCA');
INSERT INTO `ubigeo_inei` VALUES (516, '05', '04', '00', 'HUANTA');
INSERT INTO `ubigeo_inei` VALUES (517, '05', '04', '01', 'HUANTA');
INSERT INTO `ubigeo_inei` VALUES (518, '05', '04', '02', 'AYAHUANCO');
INSERT INTO `ubigeo_inei` VALUES (519, '05', '04', '03', 'HUAMANGUILLA');
INSERT INTO `ubigeo_inei` VALUES (520, '05', '04', '04', 'IGUAIN');
INSERT INTO `ubigeo_inei` VALUES (521, '05', '04', '05', 'LURICOCHA');
INSERT INTO `ubigeo_inei` VALUES (522, '05', '04', '06', 'SANTILLANA');
INSERT INTO `ubigeo_inei` VALUES (523, '05', '04', '07', 'SIVIA');
INSERT INTO `ubigeo_inei` VALUES (524, '05', '04', '08', 'LLOCHEGUA');
INSERT INTO `ubigeo_inei` VALUES (525, '05', '04', '09', 'CANAYRE');
INSERT INTO `ubigeo_inei` VALUES (526, '05', '04', '10', 'UCHURACCAY');
INSERT INTO `ubigeo_inei` VALUES (527, '05', '04', '11', 'PUCACOLPA');
INSERT INTO `ubigeo_inei` VALUES (528, '05', '05', '00', 'LA MAR');
INSERT INTO `ubigeo_inei` VALUES (529, '05', '05', '01', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (530, '05', '05', '02', 'ANCO');
INSERT INTO `ubigeo_inei` VALUES (531, '05', '05', '03', 'AYNA');
INSERT INTO `ubigeo_inei` VALUES (532, '05', '05', '04', 'CHILCAS');
INSERT INTO `ubigeo_inei` VALUES (533, '05', '05', '05', 'CHUNGUI');
INSERT INTO `ubigeo_inei` VALUES (534, '05', '05', '06', 'LUIS CARRANZA');
INSERT INTO `ubigeo_inei` VALUES (535, '05', '05', '07', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (536, '05', '05', '08', 'TAMBO');
INSERT INTO `ubigeo_inei` VALUES (537, '05', '05', '09', 'SAMUGARI');
INSERT INTO `ubigeo_inei` VALUES (538, '05', '05', '10', 'ANCHIHUAY');
INSERT INTO `ubigeo_inei` VALUES (539, '05', '06', '00', 'LUCANAS');
INSERT INTO `ubigeo_inei` VALUES (540, '05', '06', '01', 'PUQUIO');
INSERT INTO `ubigeo_inei` VALUES (541, '05', '06', '02', 'AUCARA');
INSERT INTO `ubigeo_inei` VALUES (542, '05', '06', '03', 'CABANA');
INSERT INTO `ubigeo_inei` VALUES (543, '05', '06', '04', 'CARMEN SALCEDO');
INSERT INTO `ubigeo_inei` VALUES (544, '05', '06', '05', 'CHAVIÑA');
INSERT INTO `ubigeo_inei` VALUES (545, '05', '06', '06', 'CHIPAO');
INSERT INTO `ubigeo_inei` VALUES (546, '05', '06', '07', 'HUAC-HUAS');
INSERT INTO `ubigeo_inei` VALUES (547, '05', '06', '08', 'LARAMATE');
INSERT INTO `ubigeo_inei` VALUES (548, '05', '06', '09', 'LEONCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (549, '05', '06', '10', 'LLAUTA');
INSERT INTO `ubigeo_inei` VALUES (550, '05', '06', '11', 'LUCANAS');
INSERT INTO `ubigeo_inei` VALUES (551, '05', '06', '12', 'OCAÑA');
INSERT INTO `ubigeo_inei` VALUES (552, '05', '06', '13', 'OTOCA');
INSERT INTO `ubigeo_inei` VALUES (553, '05', '06', '14', 'SAISA');
INSERT INTO `ubigeo_inei` VALUES (554, '05', '06', '15', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (555, '05', '06', '16', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (556, '05', '06', '17', 'SAN PEDRO');
INSERT INTO `ubigeo_inei` VALUES (557, '05', '06', '18', 'SAN PEDRO DE PALCO');
INSERT INTO `ubigeo_inei` VALUES (558, '05', '06', '19', 'SANCOS');
INSERT INTO `ubigeo_inei` VALUES (559, '05', '06', '20', 'SANTA ANA DE HUAYCAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (560, '05', '06', '21', 'SANTA LUCIA');
INSERT INTO `ubigeo_inei` VALUES (561, '05', '07', '00', 'PARINACOCHAS');
INSERT INTO `ubigeo_inei` VALUES (562, '05', '07', '01', 'CORACORA');
INSERT INTO `ubigeo_inei` VALUES (563, '05', '07', '02', 'CHUMPI');
INSERT INTO `ubigeo_inei` VALUES (564, '05', '07', '03', 'CORONEL CASTAÑEDA');
INSERT INTO `ubigeo_inei` VALUES (565, '05', '07', '04', 'PACAPAUSA');
INSERT INTO `ubigeo_inei` VALUES (566, '05', '07', '05', 'PULLO');
INSERT INTO `ubigeo_inei` VALUES (567, '05', '07', '06', 'PUYUSCA');
INSERT INTO `ubigeo_inei` VALUES (568, '05', '07', '07', 'SAN FRANCISCO DE RAVACAYCO');
INSERT INTO `ubigeo_inei` VALUES (569, '05', '07', '08', 'UPAHUACHO');
INSERT INTO `ubigeo_inei` VALUES (570, '05', '08', '00', 'PAUCAR DEL SARA SARA');
INSERT INTO `ubigeo_inei` VALUES (571, '05', '08', '01', 'PAUSA');
INSERT INTO `ubigeo_inei` VALUES (572, '05', '08', '02', 'COLTA');
INSERT INTO `ubigeo_inei` VALUES (573, '05', '08', '03', 'CORCULLA');
INSERT INTO `ubigeo_inei` VALUES (574, '05', '08', '04', 'LAMPA');
INSERT INTO `ubigeo_inei` VALUES (575, '05', '08', '05', 'MARCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (576, '05', '08', '06', 'OYOLO');
INSERT INTO `ubigeo_inei` VALUES (577, '05', '08', '07', 'PARARCA');
INSERT INTO `ubigeo_inei` VALUES (578, '05', '08', '08', 'SAN JAVIER DE ALPABAMBA');
INSERT INTO `ubigeo_inei` VALUES (579, '05', '08', '09', 'SAN JOSE DE USHUA');
INSERT INTO `ubigeo_inei` VALUES (580, '05', '08', '10', 'SARA SARA');
INSERT INTO `ubigeo_inei` VALUES (581, '05', '09', '00', 'SUCRE');
INSERT INTO `ubigeo_inei` VALUES (582, '05', '09', '01', 'QUEROBAMBA');
INSERT INTO `ubigeo_inei` VALUES (583, '05', '09', '02', 'BELEN');
INSERT INTO `ubigeo_inei` VALUES (584, '05', '09', '03', 'CHALCOS');
INSERT INTO `ubigeo_inei` VALUES (585, '05', '09', '04', 'CHILCAYOC');
INSERT INTO `ubigeo_inei` VALUES (586, '05', '09', '05', 'HUACAÑA');
INSERT INTO `ubigeo_inei` VALUES (587, '05', '09', '06', 'MORCOLLA');
INSERT INTO `ubigeo_inei` VALUES (588, '05', '09', '07', 'PAICO');
INSERT INTO `ubigeo_inei` VALUES (589, '05', '09', '08', 'SAN PEDRO DE LARCAY');
INSERT INTO `ubigeo_inei` VALUES (590, '05', '09', '09', 'SAN SALVADOR DE QUIJE');
INSERT INTO `ubigeo_inei` VALUES (591, '05', '09', '10', 'SANTIAGO DE PAUCARAY');
INSERT INTO `ubigeo_inei` VALUES (592, '05', '09', '11', 'SORAS');
INSERT INTO `ubigeo_inei` VALUES (593, '05', '10', '00', 'VICTOR FAJARDO');
INSERT INTO `ubigeo_inei` VALUES (594, '05', '10', '01', 'HUANCAPI');
INSERT INTO `ubigeo_inei` VALUES (595, '05', '10', '02', 'ALCAMENCA');
INSERT INTO `ubigeo_inei` VALUES (596, '05', '10', '03', 'APONGO');
INSERT INTO `ubigeo_inei` VALUES (597, '05', '10', '04', 'ASQUIPATA');
INSERT INTO `ubigeo_inei` VALUES (598, '05', '10', '05', 'CANARIA');
INSERT INTO `ubigeo_inei` VALUES (599, '05', '10', '06', 'CAYARA');
INSERT INTO `ubigeo_inei` VALUES (600, '05', '10', '07', 'COLCA');
INSERT INTO `ubigeo_inei` VALUES (601, '05', '10', '08', 'HUAMANQUIQUIA');
INSERT INTO `ubigeo_inei` VALUES (602, '05', '10', '09', 'HUANCARAYLLA');
INSERT INTO `ubigeo_inei` VALUES (603, '05', '10', '10', 'HUAYA');
INSERT INTO `ubigeo_inei` VALUES (604, '05', '10', '11', 'SARHUA');
INSERT INTO `ubigeo_inei` VALUES (605, '05', '10', '12', 'VILCANCHOS');
INSERT INTO `ubigeo_inei` VALUES (606, '05', '11', '00', 'VILCAS HUAMAN');
INSERT INTO `ubigeo_inei` VALUES (607, '05', '11', '01', 'VILCAS HUAMAN');
INSERT INTO `ubigeo_inei` VALUES (608, '05', '11', '02', 'ACCOMARCA');
INSERT INTO `ubigeo_inei` VALUES (609, '05', '11', '03', 'CARHUANCA');
INSERT INTO `ubigeo_inei` VALUES (610, '05', '11', '04', 'CONCEPCION');
INSERT INTO `ubigeo_inei` VALUES (611, '05', '11', '05', 'HUAMBALPA');
INSERT INTO `ubigeo_inei` VALUES (612, '05', '11', '06', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (613, '05', '11', '07', 'SAURAMA');
INSERT INTO `ubigeo_inei` VALUES (614, '05', '11', '08', 'VISCHONGO');
INSERT INTO `ubigeo_inei` VALUES (615, '06', '00', '00', 'CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (616, '06', '01', '00', 'CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (617, '06', '01', '01', 'CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (618, '06', '01', '02', 'ASUNCION');
INSERT INTO `ubigeo_inei` VALUES (619, '06', '01', '03', 'CHETILLA');
INSERT INTO `ubigeo_inei` VALUES (620, '06', '01', '04', 'COSPAN');
INSERT INTO `ubigeo_inei` VALUES (621, '06', '01', '05', 'ENCAÑADA');
INSERT INTO `ubigeo_inei` VALUES (622, '06', '01', '06', 'JESUS');
INSERT INTO `ubigeo_inei` VALUES (623, '06', '01', '07', 'LLACANORA');
INSERT INTO `ubigeo_inei` VALUES (624, '06', '01', '08', 'LOS BAÑOS DEL INCA');
INSERT INTO `ubigeo_inei` VALUES (625, '06', '01', '09', 'MAGDALENA');
INSERT INTO `ubigeo_inei` VALUES (626, '06', '01', '10', 'MATARA');
INSERT INTO `ubigeo_inei` VALUES (627, '06', '01', '11', 'NAMORA');
INSERT INTO `ubigeo_inei` VALUES (628, '06', '01', '12', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (629, '06', '02', '00', 'CAJABAMBA');
INSERT INTO `ubigeo_inei` VALUES (630, '06', '02', '01', 'CAJABAMBA');
INSERT INTO `ubigeo_inei` VALUES (631, '06', '02', '02', 'CACHACHI');
INSERT INTO `ubigeo_inei` VALUES (632, '06', '02', '03', 'CONDEBAMBA');
INSERT INTO `ubigeo_inei` VALUES (633, '06', '02', '04', 'SITACOCHA');
INSERT INTO `ubigeo_inei` VALUES (634, '06', '03', '00', 'CELENDIN');
INSERT INTO `ubigeo_inei` VALUES (635, '06', '03', '01', 'CELENDIN');
INSERT INTO `ubigeo_inei` VALUES (636, '06', '03', '02', 'CHUMUCH');
INSERT INTO `ubigeo_inei` VALUES (637, '06', '03', '03', 'CORTEGANA');
INSERT INTO `ubigeo_inei` VALUES (638, '06', '03', '04', 'HUASMIN');
INSERT INTO `ubigeo_inei` VALUES (639, '06', '03', '05', 'JORGE CHAVEZ');
INSERT INTO `ubigeo_inei` VALUES (640, '06', '03', '06', 'JOSE GALVEZ');
INSERT INTO `ubigeo_inei` VALUES (641, '06', '03', '07', 'MIGUEL IGLESIAS');
INSERT INTO `ubigeo_inei` VALUES (642, '06', '03', '08', 'OXAMARCA');
INSERT INTO `ubigeo_inei` VALUES (643, '06', '03', '09', 'SOROCHUCO');
INSERT INTO `ubigeo_inei` VALUES (644, '06', '03', '10', 'SUCRE');
INSERT INTO `ubigeo_inei` VALUES (645, '06', '03', '11', 'UTCO');
INSERT INTO `ubigeo_inei` VALUES (646, '06', '03', '12', 'LA LIBERTAD DE PALLAN');
INSERT INTO `ubigeo_inei` VALUES (647, '06', '04', '00', 'CHOTA');
INSERT INTO `ubigeo_inei` VALUES (648, '06', '04', '01', 'CHOTA');
INSERT INTO `ubigeo_inei` VALUES (649, '06', '04', '02', 'ANGUIA');
INSERT INTO `ubigeo_inei` VALUES (650, '06', '04', '03', 'CHADIN');
INSERT INTO `ubigeo_inei` VALUES (651, '06', '04', '04', 'CHIGUIRIP');
INSERT INTO `ubigeo_inei` VALUES (652, '06', '04', '05', 'CHIMBAN');
INSERT INTO `ubigeo_inei` VALUES (653, '06', '04', '06', 'CHOROPAMPA');
INSERT INTO `ubigeo_inei` VALUES (654, '06', '04', '07', 'COCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (655, '06', '04', '08', 'CONCHAN');
INSERT INTO `ubigeo_inei` VALUES (656, '06', '04', '09', 'HUAMBOS');
INSERT INTO `ubigeo_inei` VALUES (657, '06', '04', '10', 'LAJAS');
INSERT INTO `ubigeo_inei` VALUES (658, '06', '04', '11', 'LLAMA');
INSERT INTO `ubigeo_inei` VALUES (659, '06', '04', '12', 'MIRACOSTA');
INSERT INTO `ubigeo_inei` VALUES (660, '06', '04', '13', 'PACCHA');
INSERT INTO `ubigeo_inei` VALUES (661, '06', '04', '14', 'PION');
INSERT INTO `ubigeo_inei` VALUES (662, '06', '04', '15', 'QUEROCOTO');
INSERT INTO `ubigeo_inei` VALUES (663, '06', '04', '16', 'SAN JUAN DE LICUPIS');
INSERT INTO `ubigeo_inei` VALUES (664, '06', '04', '17', 'TACABAMBA');
INSERT INTO `ubigeo_inei` VALUES (665, '06', '04', '18', 'TOCMOCHE');
INSERT INTO `ubigeo_inei` VALUES (666, '06', '04', '19', 'CHALAMARCA');
INSERT INTO `ubigeo_inei` VALUES (667, '06', '05', '00', 'CONTUMAZA');
INSERT INTO `ubigeo_inei` VALUES (668, '06', '05', '01', 'CONTUMAZA');
INSERT INTO `ubigeo_inei` VALUES (669, '06', '05', '02', 'CHILETE');
INSERT INTO `ubigeo_inei` VALUES (670, '06', '05', '03', 'CUPISNIQUE');
INSERT INTO `ubigeo_inei` VALUES (671, '06', '05', '04', 'GUZMANGO');
INSERT INTO `ubigeo_inei` VALUES (672, '06', '05', '05', 'SAN BENITO');
INSERT INTO `ubigeo_inei` VALUES (673, '06', '05', '06', 'SANTA CRUZ DE TOLED');
INSERT INTO `ubigeo_inei` VALUES (674, '06', '05', '07', 'TANTARICA');
INSERT INTO `ubigeo_inei` VALUES (675, '06', '05', '08', 'YONAN');
INSERT INTO `ubigeo_inei` VALUES (676, '06', '06', '00', 'CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (677, '06', '06', '01', 'CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (678, '06', '06', '02', 'CALLAYUC');
INSERT INTO `ubigeo_inei` VALUES (679, '06', '06', '03', 'CHOROS');
INSERT INTO `ubigeo_inei` VALUES (680, '06', '06', '04', 'CUJILLO');
INSERT INTO `ubigeo_inei` VALUES (681, '06', '06', '05', 'LA RAMADA');
INSERT INTO `ubigeo_inei` VALUES (682, '06', '06', '06', 'PIMPINGOS');
INSERT INTO `ubigeo_inei` VALUES (683, '06', '06', '07', 'QUEROCOTILLO');
INSERT INTO `ubigeo_inei` VALUES (684, '06', '06', '08', 'SAN ANDRES DE CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (685, '06', '06', '09', 'SAN JUAN DE CUTERVO');
INSERT INTO `ubigeo_inei` VALUES (686, '06', '06', '10', 'SAN LUIS DE LUCMA');
INSERT INTO `ubigeo_inei` VALUES (687, '06', '06', '11', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (688, '06', '06', '12', 'SANTO DOMINGO DE LA CAPILLA');
INSERT INTO `ubigeo_inei` VALUES (689, '06', '06', '13', 'SANTO TOMAS');
INSERT INTO `ubigeo_inei` VALUES (690, '06', '06', '14', 'SOCOTA');
INSERT INTO `ubigeo_inei` VALUES (691, '06', '06', '15', 'TORIBIO CASANOVA');
INSERT INTO `ubigeo_inei` VALUES (692, '06', '07', '00', 'HUALGAYOC');
INSERT INTO `ubigeo_inei` VALUES (693, '06', '07', '01', 'BAMBAMARCA');
INSERT INTO `ubigeo_inei` VALUES (694, '06', '07', '02', 'CHUGUR');
INSERT INTO `ubigeo_inei` VALUES (695, '06', '07', '03', 'HUALGAYOC');
INSERT INTO `ubigeo_inei` VALUES (696, '06', '08', '00', 'JAEN');
INSERT INTO `ubigeo_inei` VALUES (697, '06', '08', '01', 'JAEN');
INSERT INTO `ubigeo_inei` VALUES (698, '06', '08', '02', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (699, '06', '08', '03', 'CHONTALI');
INSERT INTO `ubigeo_inei` VALUES (700, '06', '08', '04', 'COLASAY');
INSERT INTO `ubigeo_inei` VALUES (701, '06', '08', '05', 'HUABAL');
INSERT INTO `ubigeo_inei` VALUES (702, '06', '08', '06', 'LAS PIRIAS');
INSERT INTO `ubigeo_inei` VALUES (703, '06', '08', '07', 'POMAHUACA');
INSERT INTO `ubigeo_inei` VALUES (704, '06', '08', '08', 'PUCARA');
INSERT INTO `ubigeo_inei` VALUES (705, '06', '08', '09', 'SALLIQUE');
INSERT INTO `ubigeo_inei` VALUES (706, '06', '08', '10', 'SAN FELIPE');
INSERT INTO `ubigeo_inei` VALUES (707, '06', '08', '11', 'SAN JOSE DEL ALTO');
INSERT INTO `ubigeo_inei` VALUES (708, '06', '08', '12', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (709, '06', '09', '00', 'SAN IGNACIO');
INSERT INTO `ubigeo_inei` VALUES (710, '06', '09', '01', 'SAN IGNACIO');
INSERT INTO `ubigeo_inei` VALUES (711, '06', '09', '02', 'CHIRINOS');
INSERT INTO `ubigeo_inei` VALUES (712, '06', '09', '03', 'HUARANGO');
INSERT INTO `ubigeo_inei` VALUES (713, '06', '09', '04', 'LA COIPA');
INSERT INTO `ubigeo_inei` VALUES (714, '06', '09', '05', 'NAMBALLE');
INSERT INTO `ubigeo_inei` VALUES (715, '06', '09', '06', 'SAN JOSE DE LOURDES');
INSERT INTO `ubigeo_inei` VALUES (716, '06', '09', '07', 'TABACONAS');
INSERT INTO `ubigeo_inei` VALUES (717, '06', '10', '00', 'SAN MARCOS');
INSERT INTO `ubigeo_inei` VALUES (718, '06', '10', '01', 'PEDRO GALVEZ');
INSERT INTO `ubigeo_inei` VALUES (719, '06', '10', '02', 'CHANCAY');
INSERT INTO `ubigeo_inei` VALUES (720, '06', '10', '03', 'EDUARDO VILLANUEVA');
INSERT INTO `ubigeo_inei` VALUES (721, '06', '10', '04', 'GREGORIO PITA');
INSERT INTO `ubigeo_inei` VALUES (722, '06', '10', '05', 'ICHOCAN');
INSERT INTO `ubigeo_inei` VALUES (723, '06', '10', '06', 'JOSE MANUEL QUIROZ');
INSERT INTO `ubigeo_inei` VALUES (724, '06', '10', '07', 'JOSE SABOGAL');
INSERT INTO `ubigeo_inei` VALUES (725, '06', '11', '00', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (726, '06', '11', '01', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (727, '06', '11', '02', 'BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (728, '06', '11', '03', 'CALQUIS');
INSERT INTO `ubigeo_inei` VALUES (729, '06', '11', '04', 'CATILLUC');
INSERT INTO `ubigeo_inei` VALUES (730, '06', '11', '05', 'EL PRADO');
INSERT INTO `ubigeo_inei` VALUES (731, '06', '11', '06', 'LA FLORIDA');
INSERT INTO `ubigeo_inei` VALUES (732, '06', '11', '07', 'LLAPA');
INSERT INTO `ubigeo_inei` VALUES (733, '06', '11', '08', 'NANCHOC');
INSERT INTO `ubigeo_inei` VALUES (734, '06', '11', '09', 'NIEPOS');
INSERT INTO `ubigeo_inei` VALUES (735, '06', '11', '10', 'SAN GREGORIO');
INSERT INTO `ubigeo_inei` VALUES (736, '06', '11', '11', 'SAN SILVESTRE DE COCHAN');
INSERT INTO `ubigeo_inei` VALUES (737, '06', '11', '12', 'TONGOD');
INSERT INTO `ubigeo_inei` VALUES (738, '06', '11', '13', 'UNION AGUA BLANCA');
INSERT INTO `ubigeo_inei` VALUES (739, '06', '12', '00', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (740, '06', '12', '01', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (741, '06', '12', '02', 'SAN BERNARDINO');
INSERT INTO `ubigeo_inei` VALUES (742, '06', '12', '03', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (743, '06', '12', '04', 'TUMBADEN');
INSERT INTO `ubigeo_inei` VALUES (744, '06', '13', '00', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (745, '06', '13', '01', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (746, '06', '13', '02', 'ANDABAMBA');
INSERT INTO `ubigeo_inei` VALUES (747, '06', '13', '03', 'CATACHE');
INSERT INTO `ubigeo_inei` VALUES (748, '06', '13', '04', 'CHANCAYBAÑOS');
INSERT INTO `ubigeo_inei` VALUES (749, '06', '13', '05', 'LA ESPERANZA');
INSERT INTO `ubigeo_inei` VALUES (750, '06', '13', '06', 'NINABAMBA');
INSERT INTO `ubigeo_inei` VALUES (751, '06', '13', '07', 'PULAN');
INSERT INTO `ubigeo_inei` VALUES (752, '06', '13', '08', 'SAUCEPAMPA');
INSERT INTO `ubigeo_inei` VALUES (753, '06', '13', '09', 'SEXI');
INSERT INTO `ubigeo_inei` VALUES (754, '06', '13', '10', 'UTICYACU');
INSERT INTO `ubigeo_inei` VALUES (755, '06', '13', '11', 'YAUYUCAN');
INSERT INTO `ubigeo_inei` VALUES (756, '07', '00', '00', 'CALLAO');
INSERT INTO `ubigeo_inei` VALUES (757, '07', '01', '00', 'PROV. CONST. DEL CALLAO');
INSERT INTO `ubigeo_inei` VALUES (758, '07', '01', '01', 'CALLAO');
INSERT INTO `ubigeo_inei` VALUES (759, '07', '01', '02', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (760, '07', '01', '03', 'CARMEN DE LA LEGUA REYNOSO');
INSERT INTO `ubigeo_inei` VALUES (761, '07', '01', '04', 'LA PERLA');
INSERT INTO `ubigeo_inei` VALUES (762, '07', '01', '05', 'LA PUNTA');
INSERT INTO `ubigeo_inei` VALUES (763, '07', '01', '06', 'VENTANILLA');
INSERT INTO `ubigeo_inei` VALUES (764, '07', '01', '07', 'MI PERÚ');
INSERT INTO `ubigeo_inei` VALUES (765, '08', '00', '00', 'CUSCO');
INSERT INTO `ubigeo_inei` VALUES (766, '08', '01', '00', 'CUSCO');
INSERT INTO `ubigeo_inei` VALUES (767, '08', '01', '01', 'CUSCO');
INSERT INTO `ubigeo_inei` VALUES (768, '08', '01', '02', 'CCORCA');
INSERT INTO `ubigeo_inei` VALUES (769, '08', '01', '03', 'POROY');
INSERT INTO `ubigeo_inei` VALUES (770, '08', '01', '04', 'SAN JERONIMO');
INSERT INTO `ubigeo_inei` VALUES (771, '08', '01', '05', 'SAN SEBASTIAN');
INSERT INTO `ubigeo_inei` VALUES (772, '08', '01', '06', 'SANTIAGO');
INSERT INTO `ubigeo_inei` VALUES (773, '08', '01', '07', 'SAYLLA');
INSERT INTO `ubigeo_inei` VALUES (774, '08', '01', '08', 'WANCHAQ');
INSERT INTO `ubigeo_inei` VALUES (775, '08', '02', '00', 'ACOMAYO');
INSERT INTO `ubigeo_inei` VALUES (776, '08', '02', '01', 'ACOMAYO');
INSERT INTO `ubigeo_inei` VALUES (777, '08', '02', '02', 'ACOPIA');
INSERT INTO `ubigeo_inei` VALUES (778, '08', '02', '03', 'ACOS');
INSERT INTO `ubigeo_inei` VALUES (779, '08', '02', '04', 'MOSOC LLACTA');
INSERT INTO `ubigeo_inei` VALUES (780, '08', '02', '05', 'POMACANCHI');
INSERT INTO `ubigeo_inei` VALUES (781, '08', '02', '06', 'RONDOCAN');
INSERT INTO `ubigeo_inei` VALUES (782, '08', '02', '07', 'SANGARARA');
INSERT INTO `ubigeo_inei` VALUES (783, '08', '03', '00', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (784, '08', '03', '01', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (785, '08', '03', '02', 'ANCAHUASI');
INSERT INTO `ubigeo_inei` VALUES (786, '08', '03', '03', 'CACHIMAYO');
INSERT INTO `ubigeo_inei` VALUES (787, '08', '03', '04', 'CHINCHAYPUJIO');
INSERT INTO `ubigeo_inei` VALUES (788, '08', '03', '05', 'HUAROCONDO');
INSERT INTO `ubigeo_inei` VALUES (789, '08', '03', '06', 'LIMATAMBO');
INSERT INTO `ubigeo_inei` VALUES (790, '08', '03', '07', 'MOLLEPATA');
INSERT INTO `ubigeo_inei` VALUES (791, '08', '03', '08', 'PUCYURA');
INSERT INTO `ubigeo_inei` VALUES (792, '08', '03', '09', 'ZURITE');
INSERT INTO `ubigeo_inei` VALUES (793, '08', '04', '00', 'CALCA');
INSERT INTO `ubigeo_inei` VALUES (794, '08', '04', '01', 'CALCA');
INSERT INTO `ubigeo_inei` VALUES (795, '08', '04', '02', 'COYA');
INSERT INTO `ubigeo_inei` VALUES (796, '08', '04', '03', 'LAMAY');
INSERT INTO `ubigeo_inei` VALUES (797, '08', '04', '04', 'LARES');
INSERT INTO `ubigeo_inei` VALUES (798, '08', '04', '05', 'PISAC');
INSERT INTO `ubigeo_inei` VALUES (799, '08', '04', '06', 'SAN SALVADOR');
INSERT INTO `ubigeo_inei` VALUES (800, '08', '04', '07', 'TARAY');
INSERT INTO `ubigeo_inei` VALUES (801, '08', '04', '08', 'YANATILE');
INSERT INTO `ubigeo_inei` VALUES (802, '08', '05', '00', 'CANAS');
INSERT INTO `ubigeo_inei` VALUES (803, '08', '05', '01', 'YANAOCA');
INSERT INTO `ubigeo_inei` VALUES (804, '08', '05', '02', 'CHECCA');
INSERT INTO `ubigeo_inei` VALUES (805, '08', '05', '03', 'KUNTURKANKI');
INSERT INTO `ubigeo_inei` VALUES (806, '08', '05', '04', 'LANGUI');
INSERT INTO `ubigeo_inei` VALUES (807, '08', '05', '05', 'LAYO');
INSERT INTO `ubigeo_inei` VALUES (808, '08', '05', '06', 'PAMPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (809, '08', '05', '07', 'QUEHUE');
INSERT INTO `ubigeo_inei` VALUES (810, '08', '05', '08', 'TUPAC AMARU');
INSERT INTO `ubigeo_inei` VALUES (811, '08', '06', '00', 'CANCHIS');
INSERT INTO `ubigeo_inei` VALUES (812, '08', '06', '01', 'SICUANI');
INSERT INTO `ubigeo_inei` VALUES (813, '08', '06', '02', 'CHECACUPE');
INSERT INTO `ubigeo_inei` VALUES (814, '08', '06', '03', 'COMBAPATA');
INSERT INTO `ubigeo_inei` VALUES (815, '08', '06', '04', 'MARANGANI');
INSERT INTO `ubigeo_inei` VALUES (816, '08', '06', '05', 'PITUMARCA');
INSERT INTO `ubigeo_inei` VALUES (817, '08', '06', '06', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (818, '08', '06', '07', 'SAN PEDRO');
INSERT INTO `ubigeo_inei` VALUES (819, '08', '06', '08', 'TINTA');
INSERT INTO `ubigeo_inei` VALUES (820, '08', '07', '00', 'CHUMBIVILCAS');
INSERT INTO `ubigeo_inei` VALUES (821, '08', '07', '01', 'SANTO TOMAS');
INSERT INTO `ubigeo_inei` VALUES (822, '08', '07', '02', 'CAPACMARCA');
INSERT INTO `ubigeo_inei` VALUES (823, '08', '07', '03', 'CHAMACA');
INSERT INTO `ubigeo_inei` VALUES (824, '08', '07', '04', 'COLQUEMARCA');
INSERT INTO `ubigeo_inei` VALUES (825, '08', '07', '05', 'LIVITACA');
INSERT INTO `ubigeo_inei` VALUES (826, '08', '07', '06', 'LLUSCO');
INSERT INTO `ubigeo_inei` VALUES (827, '08', '07', '07', 'QUIÑOTA');
INSERT INTO `ubigeo_inei` VALUES (828, '08', '07', '08', 'VELILLE');
INSERT INTO `ubigeo_inei` VALUES (829, '08', '08', '00', 'ESPINAR');
INSERT INTO `ubigeo_inei` VALUES (830, '08', '08', '01', 'ESPINAR');
INSERT INTO `ubigeo_inei` VALUES (831, '08', '08', '02', 'CONDOROMA');
INSERT INTO `ubigeo_inei` VALUES (832, '08', '08', '03', 'COPORAQUE');
INSERT INTO `ubigeo_inei` VALUES (833, '08', '08', '04', 'OCORURO');
INSERT INTO `ubigeo_inei` VALUES (834, '08', '08', '05', 'PALLPATA');
INSERT INTO `ubigeo_inei` VALUES (835, '08', '08', '06', 'PICHIGUA');
INSERT INTO `ubigeo_inei` VALUES (836, '08', '08', '07', 'SUYCKUTAMBO');
INSERT INTO `ubigeo_inei` VALUES (837, '08', '08', '08', 'ALTO PICHIGUA');
INSERT INTO `ubigeo_inei` VALUES (838, '08', '09', '00', 'LA CONVENCION');
INSERT INTO `ubigeo_inei` VALUES (839, '08', '09', '01', 'SANTA ANA');
INSERT INTO `ubigeo_inei` VALUES (840, '08', '09', '02', 'ECHARATE');
INSERT INTO `ubigeo_inei` VALUES (841, '08', '09', '03', 'HUAYOPATA');
INSERT INTO `ubigeo_inei` VALUES (842, '08', '09', '04', 'MARANURA');
INSERT INTO `ubigeo_inei` VALUES (843, '08', '09', '05', 'OCOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (844, '08', '09', '06', 'QUELLOUNO');
INSERT INTO `ubigeo_inei` VALUES (845, '08', '09', '07', 'KIMBIRI');
INSERT INTO `ubigeo_inei` VALUES (846, '08', '09', '08', 'SANTA TERESA');
INSERT INTO `ubigeo_inei` VALUES (847, '08', '09', '09', 'VILCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (848, '08', '09', '10', 'PICHARI');
INSERT INTO `ubigeo_inei` VALUES (849, '08', '09', '11', 'INKAWASI');
INSERT INTO `ubigeo_inei` VALUES (850, '08', '09', '12', 'VILLA VIRGEN');
INSERT INTO `ubigeo_inei` VALUES (851, '08', '10', '00', 'PARURO');
INSERT INTO `ubigeo_inei` VALUES (852, '08', '10', '01', 'PARURO');
INSERT INTO `ubigeo_inei` VALUES (853, '08', '10', '02', 'ACCHA');
INSERT INTO `ubigeo_inei` VALUES (854, '08', '10', '03', 'CCAPI');
INSERT INTO `ubigeo_inei` VALUES (855, '08', '10', '04', 'COLCHA');
INSERT INTO `ubigeo_inei` VALUES (856, '08', '10', '05', 'HUANOQUITE');
INSERT INTO `ubigeo_inei` VALUES (857, '08', '10', '06', 'OMACHA');
INSERT INTO `ubigeo_inei` VALUES (858, '08', '10', '07', 'PACCARITAMBO');
INSERT INTO `ubigeo_inei` VALUES (859, '08', '10', '08', 'PILLPINTO');
INSERT INTO `ubigeo_inei` VALUES (860, '08', '10', '09', 'YAURISQUE');
INSERT INTO `ubigeo_inei` VALUES (861, '08', '11', '00', 'PAUCARTAMBO');
INSERT INTO `ubigeo_inei` VALUES (862, '08', '11', '01', 'PAUCARTAMBO');
INSERT INTO `ubigeo_inei` VALUES (863, '08', '11', '02', 'CAICAY');
INSERT INTO `ubigeo_inei` VALUES (864, '08', '11', '03', 'CHALLABAMBA');
INSERT INTO `ubigeo_inei` VALUES (865, '08', '11', '04', 'COLQUEPATA');
INSERT INTO `ubigeo_inei` VALUES (866, '08', '11', '05', 'HUANCARANI');
INSERT INTO `ubigeo_inei` VALUES (867, '08', '11', '06', 'KOSÑIPATA');
INSERT INTO `ubigeo_inei` VALUES (868, '08', '12', '00', 'QUISPICANCHI');
INSERT INTO `ubigeo_inei` VALUES (869, '08', '12', '01', 'URCOS');
INSERT INTO `ubigeo_inei` VALUES (870, '08', '12', '02', 'ANDAHUAYLILLAS');
INSERT INTO `ubigeo_inei` VALUES (871, '08', '12', '03', 'CAMANTI');
INSERT INTO `ubigeo_inei` VALUES (872, '08', '12', '04', 'CCARHUAYO');
INSERT INTO `ubigeo_inei` VALUES (873, '08', '12', '05', 'CCATCA');
INSERT INTO `ubigeo_inei` VALUES (874, '08', '12', '06', 'CUSIPATA');
INSERT INTO `ubigeo_inei` VALUES (875, '08', '12', '07', 'HUARO');
INSERT INTO `ubigeo_inei` VALUES (876, '08', '12', '08', 'LUCRE');
INSERT INTO `ubigeo_inei` VALUES (877, '08', '12', '09', 'MARCAPATA');
INSERT INTO `ubigeo_inei` VALUES (878, '08', '12', '10', 'OCONGATE');
INSERT INTO `ubigeo_inei` VALUES (879, '08', '12', '11', 'OROPESA');
INSERT INTO `ubigeo_inei` VALUES (880, '08', '12', '12', 'QUIQUIJANA');
INSERT INTO `ubigeo_inei` VALUES (881, '08', '13', '00', 'URUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (882, '08', '13', '01', 'URUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (883, '08', '13', '02', 'CHINCHERO');
INSERT INTO `ubigeo_inei` VALUES (884, '08', '13', '03', 'HUAYLLABAMBA');
INSERT INTO `ubigeo_inei` VALUES (885, '08', '13', '04', 'MACHUPICCHU');
INSERT INTO `ubigeo_inei` VALUES (886, '08', '13', '05', 'MARAS');
INSERT INTO `ubigeo_inei` VALUES (887, '08', '13', '06', 'OLLANTAYTAMBO');
INSERT INTO `ubigeo_inei` VALUES (888, '08', '13', '07', 'YUCAY');
INSERT INTO `ubigeo_inei` VALUES (889, '09', '00', '00', 'HUANCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (890, '09', '01', '00', 'HUANCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (891, '09', '01', '01', 'HUANCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (892, '09', '01', '02', 'ACOBAMBILLA');
INSERT INTO `ubigeo_inei` VALUES (893, '09', '01', '03', 'ACORIA');
INSERT INTO `ubigeo_inei` VALUES (894, '09', '01', '04', 'CONAYCA');
INSERT INTO `ubigeo_inei` VALUES (895, '09', '01', '05', 'CUENCA');
INSERT INTO `ubigeo_inei` VALUES (896, '09', '01', '06', 'HUACHOCOLPA');
INSERT INTO `ubigeo_inei` VALUES (897, '09', '01', '07', 'HUAYLLAHUARA');
INSERT INTO `ubigeo_inei` VALUES (898, '09', '01', '08', 'IZCUCHACA');
INSERT INTO `ubigeo_inei` VALUES (899, '09', '01', '09', 'LARIA');
INSERT INTO `ubigeo_inei` VALUES (900, '09', '01', '10', 'MANTA');
INSERT INTO `ubigeo_inei` VALUES (901, '09', '01', '11', 'MARISCAL CACERES');
INSERT INTO `ubigeo_inei` VALUES (902, '09', '01', '12', 'MOYA');
INSERT INTO `ubigeo_inei` VALUES (903, '09', '01', '13', 'NUEVO OCCORO');
INSERT INTO `ubigeo_inei` VALUES (904, '09', '01', '14', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (905, '09', '01', '15', 'PILCHACA');
INSERT INTO `ubigeo_inei` VALUES (906, '09', '01', '16', 'VILCA');
INSERT INTO `ubigeo_inei` VALUES (907, '09', '01', '17', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (908, '09', '01', '18', 'ASCENSIÓN');
INSERT INTO `ubigeo_inei` VALUES (909, '09', '01', '19', 'HUANDO');
INSERT INTO `ubigeo_inei` VALUES (910, '09', '02', '00', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (911, '09', '02', '01', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (912, '09', '02', '02', 'ANDABAMBA');
INSERT INTO `ubigeo_inei` VALUES (913, '09', '02', '03', 'ANTA');
INSERT INTO `ubigeo_inei` VALUES (914, '09', '02', '04', 'CAJA');
INSERT INTO `ubigeo_inei` VALUES (915, '09', '02', '05', 'MARCAS');
INSERT INTO `ubigeo_inei` VALUES (916, '09', '02', '06', 'PAUCARA');
INSERT INTO `ubigeo_inei` VALUES (917, '09', '02', '07', 'POMACOCHA');
INSERT INTO `ubigeo_inei` VALUES (918, '09', '02', '08', 'ROSARIO');
INSERT INTO `ubigeo_inei` VALUES (919, '09', '03', '00', 'ANGARAES');
INSERT INTO `ubigeo_inei` VALUES (920, '09', '03', '01', 'LIRCAY');
INSERT INTO `ubigeo_inei` VALUES (921, '09', '03', '02', 'ANCHONGA');
INSERT INTO `ubigeo_inei` VALUES (922, '09', '03', '03', 'CALLANMARCA');
INSERT INTO `ubigeo_inei` VALUES (923, '09', '03', '04', 'CCOCHACCASA');
INSERT INTO `ubigeo_inei` VALUES (924, '09', '03', '05', 'CHINCHO');
INSERT INTO `ubigeo_inei` VALUES (925, '09', '03', '06', 'CONGALLA');
INSERT INTO `ubigeo_inei` VALUES (926, '09', '03', '07', 'HUANCA-HUANCA');
INSERT INTO `ubigeo_inei` VALUES (927, '09', '03', '08', 'HUAYLLAY GRANDE');
INSERT INTO `ubigeo_inei` VALUES (928, '09', '03', '09', 'JULCAMARCA');
INSERT INTO `ubigeo_inei` VALUES (929, '09', '03', '10', 'SAN ANTONIO DE ANTAPARCO');
INSERT INTO `ubigeo_inei` VALUES (930, '09', '03', '11', 'SANTO TOMAS DE PATA');
INSERT INTO `ubigeo_inei` VALUES (931, '09', '03', '12', 'SECCLLA');
INSERT INTO `ubigeo_inei` VALUES (932, '09', '04', '00', 'CASTROVIRREYNA');
INSERT INTO `ubigeo_inei` VALUES (933, '09', '04', '01', 'CASTROVIRREYNA');
INSERT INTO `ubigeo_inei` VALUES (934, '09', '04', '02', 'ARMA');
INSERT INTO `ubigeo_inei` VALUES (935, '09', '04', '03', 'AURAHUA');
INSERT INTO `ubigeo_inei` VALUES (936, '09', '04', '04', 'CAPILLAS');
INSERT INTO `ubigeo_inei` VALUES (937, '09', '04', '05', 'CHUPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (938, '09', '04', '06', 'COCAS');
INSERT INTO `ubigeo_inei` VALUES (939, '09', '04', '07', 'HUACHOS');
INSERT INTO `ubigeo_inei` VALUES (940, '09', '04', '08', 'HUAMATAMBO');
INSERT INTO `ubigeo_inei` VALUES (941, '09', '04', '09', 'MOLLEPAMPA');
INSERT INTO `ubigeo_inei` VALUES (942, '09', '04', '10', 'SAN JUAN');
INSERT INTO `ubigeo_inei` VALUES (943, '09', '04', '11', 'SANTA ANA');
INSERT INTO `ubigeo_inei` VALUES (944, '09', '04', '12', 'TANTARA');
INSERT INTO `ubigeo_inei` VALUES (945, '09', '04', '13', 'TICRAPO');
INSERT INTO `ubigeo_inei` VALUES (946, '09', '05', '00', 'CHURCAMPA');
INSERT INTO `ubigeo_inei` VALUES (947, '09', '05', '01', 'CHURCAMPA');
INSERT INTO `ubigeo_inei` VALUES (948, '09', '05', '02', 'ANCO');
INSERT INTO `ubigeo_inei` VALUES (949, '09', '05', '03', 'CHINCHIHUASI');
INSERT INTO `ubigeo_inei` VALUES (950, '09', '05', '04', 'EL CARMEN');
INSERT INTO `ubigeo_inei` VALUES (951, '09', '05', '05', 'LA MERCED');
INSERT INTO `ubigeo_inei` VALUES (952, '09', '05', '06', 'LOCROJA');
INSERT INTO `ubigeo_inei` VALUES (953, '09', '05', '07', 'PAUCARBAMBA');
INSERT INTO `ubigeo_inei` VALUES (954, '09', '05', '08', 'SAN MIGUEL DE MAYOCC');
INSERT INTO `ubigeo_inei` VALUES (955, '09', '05', '09', 'SAN PEDRO DE CORIS');
INSERT INTO `ubigeo_inei` VALUES (956, '09', '05', '10', 'PACHAMARCA');
INSERT INTO `ubigeo_inei` VALUES (957, '09', '05', '11', 'COSME');
INSERT INTO `ubigeo_inei` VALUES (958, '09', '06', '00', 'HUAYTARA');
INSERT INTO `ubigeo_inei` VALUES (959, '09', '06', '01', 'HUAYTARA');
INSERT INTO `ubigeo_inei` VALUES (960, '09', '06', '02', 'AYAVI');
INSERT INTO `ubigeo_inei` VALUES (961, '09', '06', '03', 'CORDOVA');
INSERT INTO `ubigeo_inei` VALUES (962, '09', '06', '04', 'HUAYACUNDO ARMA');
INSERT INTO `ubigeo_inei` VALUES (963, '09', '06', '05', 'LARAMARCA');
INSERT INTO `ubigeo_inei` VALUES (964, '09', '06', '06', 'OCOYO');
INSERT INTO `ubigeo_inei` VALUES (965, '09', '06', '07', 'PILPICHACA');
INSERT INTO `ubigeo_inei` VALUES (966, '09', '06', '08', 'QUERCO');
INSERT INTO `ubigeo_inei` VALUES (967, '09', '06', '09', 'QUITO-ARMA');
INSERT INTO `ubigeo_inei` VALUES (968, '09', '06', '10', 'SAN ANTONIO DE CUSICANCHA');
INSERT INTO `ubigeo_inei` VALUES (969, '09', '06', '11', 'SAN FRANCISCO DE SANGAYAICO');
INSERT INTO `ubigeo_inei` VALUES (970, '09', '06', '12', 'SAN ISIDRO');
INSERT INTO `ubigeo_inei` VALUES (971, '09', '06', '13', 'SANTIAGO DE CHOCORVOS');
INSERT INTO `ubigeo_inei` VALUES (972, '09', '06', '14', 'SANTIAGO DE QUIRAHUARA');
INSERT INTO `ubigeo_inei` VALUES (973, '09', '06', '15', 'SANTO DOMINGO DE CAPILLAS');
INSERT INTO `ubigeo_inei` VALUES (974, '09', '06', '16', 'TAMBO');
INSERT INTO `ubigeo_inei` VALUES (975, '09', '07', '00', 'TAYACAJA');
INSERT INTO `ubigeo_inei` VALUES (976, '09', '07', '01', 'PAMPAS');
INSERT INTO `ubigeo_inei` VALUES (977, '09', '07', '02', 'ACOSTAMBO');
INSERT INTO `ubigeo_inei` VALUES (978, '09', '07', '03', 'ACRAQUIA');
INSERT INTO `ubigeo_inei` VALUES (979, '09', '07', '04', 'AHUAYCHA');
INSERT INTO `ubigeo_inei` VALUES (980, '09', '07', '05', 'COLCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (981, '09', '07', '06', 'DANIEL HERNANDEZ');
INSERT INTO `ubigeo_inei` VALUES (982, '09', '07', '07', 'HUACHOCOLPA');
INSERT INTO `ubigeo_inei` VALUES (983, '09', '07', '09', 'HUARIBAMBA');
INSERT INTO `ubigeo_inei` VALUES (984, '09', '07', '10', 'ÑAHUIMPUQUIO');
INSERT INTO `ubigeo_inei` VALUES (985, '09', '07', '11', 'PAZOS');
INSERT INTO `ubigeo_inei` VALUES (986, '09', '07', '13', 'QUISHUAR');
INSERT INTO `ubigeo_inei` VALUES (987, '09', '07', '14', 'SALCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (988, '09', '07', '15', 'SALCAHUASI');
INSERT INTO `ubigeo_inei` VALUES (989, '09', '07', '16', 'SAN MARCOS DE ROCCHAC');
INSERT INTO `ubigeo_inei` VALUES (990, '09', '07', '17', 'SURCUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (991, '09', '07', '18', 'TINTAY PUNCU');
INSERT INTO `ubigeo_inei` VALUES (992, '10', '00', '00', 'HUANUCO');
INSERT INTO `ubigeo_inei` VALUES (993, '10', '01', '00', 'HUANUCO');
INSERT INTO `ubigeo_inei` VALUES (994, '10', '01', '01', 'HUANUCO');
INSERT INTO `ubigeo_inei` VALUES (995, '10', '01', '02', 'AMARILIS');
INSERT INTO `ubigeo_inei` VALUES (996, '10', '01', '03', 'CHINCHAO');
INSERT INTO `ubigeo_inei` VALUES (997, '10', '01', '04', 'CHURUBAMBA');
INSERT INTO `ubigeo_inei` VALUES (998, '10', '01', '05', 'MARGOS');
INSERT INTO `ubigeo_inei` VALUES (999, '10', '01', '06', 'QUISQUI');
INSERT INTO `ubigeo_inei` VALUES (1000, '10', '01', '07', 'SAN FRANCISCO DE CAYRAN');
INSERT INTO `ubigeo_inei` VALUES (1001, '10', '01', '08', 'SAN PEDRO DE CHAULAN');
INSERT INTO `ubigeo_inei` VALUES (1002, '10', '01', '09', 'SANTA MARIA DEL VALLE');
INSERT INTO `ubigeo_inei` VALUES (1003, '10', '01', '10', 'YARUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1004, '10', '01', '11', 'PILLCO MARCA');
INSERT INTO `ubigeo_inei` VALUES (1005, '10', '01', '12', 'YACUS');
INSERT INTO `ubigeo_inei` VALUES (1006, '10', '02', '00', 'AMBO');
INSERT INTO `ubigeo_inei` VALUES (1007, '10', '02', '01', 'AMBO');
INSERT INTO `ubigeo_inei` VALUES (1008, '10', '02', '02', 'CAYNA');
INSERT INTO `ubigeo_inei` VALUES (1009, '10', '02', '03', 'COLPAS');
INSERT INTO `ubigeo_inei` VALUES (1010, '10', '02', '04', 'CONCHAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1011, '10', '02', '05', 'HUACAR');
INSERT INTO `ubigeo_inei` VALUES (1012, '10', '02', '06', 'SAN FRANCISCO');
INSERT INTO `ubigeo_inei` VALUES (1013, '10', '02', '07', 'SAN RAFAEL');
INSERT INTO `ubigeo_inei` VALUES (1014, '10', '02', '08', 'TOMAY KICHWA');
INSERT INTO `ubigeo_inei` VALUES (1015, '10', '03', '00', 'DOS DE MAYO');
INSERT INTO `ubigeo_inei` VALUES (1016, '10', '03', '01', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1017, '10', '03', '07', 'CHUQUIS');
INSERT INTO `ubigeo_inei` VALUES (1018, '10', '03', '11', 'MARIAS');
INSERT INTO `ubigeo_inei` VALUES (1019, '10', '03', '13', 'PACHAS');
INSERT INTO `ubigeo_inei` VALUES (1020, '10', '03', '16', 'QUIVILLA');
INSERT INTO `ubigeo_inei` VALUES (1021, '10', '03', '17', 'RIPAN');
INSERT INTO `ubigeo_inei` VALUES (1022, '10', '03', '21', 'SHUNQUI');
INSERT INTO `ubigeo_inei` VALUES (1023, '10', '03', '22', 'SILLAPATA');
INSERT INTO `ubigeo_inei` VALUES (1024, '10', '03', '23', 'YANAS');
INSERT INTO `ubigeo_inei` VALUES (1025, '10', '04', '00', 'HUACAYBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1026, '10', '04', '01', 'HUACAYBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1027, '10', '04', '02', 'CANCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1028, '10', '04', '03', 'COCHABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1029, '10', '04', '04', 'PINRA');
INSERT INTO `ubigeo_inei` VALUES (1030, '10', '05', '00', 'HUAMALIES');
INSERT INTO `ubigeo_inei` VALUES (1031, '10', '05', '01', 'LLATA');
INSERT INTO `ubigeo_inei` VALUES (1032, '10', '05', '02', 'ARANCAY');
INSERT INTO `ubigeo_inei` VALUES (1033, '10', '05', '03', 'CHAVIN DE PARIARCA');
INSERT INTO `ubigeo_inei` VALUES (1034, '10', '05', '04', 'JACAS GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1035, '10', '05', '05', 'JIRCAN');
INSERT INTO `ubigeo_inei` VALUES (1036, '10', '05', '06', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1037, '10', '05', '07', 'MONZON');
INSERT INTO `ubigeo_inei` VALUES (1038, '10', '05', '08', 'PUNCHAO');
INSERT INTO `ubigeo_inei` VALUES (1039, '10', '05', '09', 'PUÑOS');
INSERT INTO `ubigeo_inei` VALUES (1040, '10', '05', '10', 'SINGA');
INSERT INTO `ubigeo_inei` VALUES (1041, '10', '05', '11', 'TANTAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1042, '10', '06', '00', 'LEONCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (1043, '10', '06', '01', 'RUPA-RUPA');
INSERT INTO `ubigeo_inei` VALUES (1044, '10', '06', '02', 'DANIEL ALOMIAS ROBLES');
INSERT INTO `ubigeo_inei` VALUES (1045, '10', '06', '03', 'HERMILIO VALDIZAN');
INSERT INTO `ubigeo_inei` VALUES (1046, '10', '06', '04', 'JOSE CRESPO Y CASTILLO');
INSERT INTO `ubigeo_inei` VALUES (1047, '10', '06', '05', 'LUYANDO');
INSERT INTO `ubigeo_inei` VALUES (1048, '10', '06', '06', 'MARIANO DAMASO BERAUN');
INSERT INTO `ubigeo_inei` VALUES (1049, '10', '07', '00', 'MARAÑON');
INSERT INTO `ubigeo_inei` VALUES (1050, '10', '07', '01', 'HUACRACHUCO');
INSERT INTO `ubigeo_inei` VALUES (1051, '10', '07', '02', 'CHOLON');
INSERT INTO `ubigeo_inei` VALUES (1052, '10', '07', '03', 'SAN BUENAVENTURA');
INSERT INTO `ubigeo_inei` VALUES (1053, '10', '08', '00', 'PACHITEA');
INSERT INTO `ubigeo_inei` VALUES (1054, '10', '08', '01', 'PANAO');
INSERT INTO `ubigeo_inei` VALUES (1055, '10', '08', '02', 'CHAGLLA');
INSERT INTO `ubigeo_inei` VALUES (1056, '10', '08', '03', 'MOLINO');
INSERT INTO `ubigeo_inei` VALUES (1057, '10', '08', '04', 'UMARI');
INSERT INTO `ubigeo_inei` VALUES (1058, '10', '09', '00', 'PUERTO INCA');
INSERT INTO `ubigeo_inei` VALUES (1059, '10', '09', '01', 'PUERTO INCA');
INSERT INTO `ubigeo_inei` VALUES (1060, '10', '09', '02', 'CODO DEL POZUZO');
INSERT INTO `ubigeo_inei` VALUES (1061, '10', '09', '03', 'HONORIA');
INSERT INTO `ubigeo_inei` VALUES (1062, '10', '09', '04', 'TOURNAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1063, '10', '09', '05', 'YUYAPICHIS');
INSERT INTO `ubigeo_inei` VALUES (1064, '10', '10', '00', 'LAURICOCHA');
INSERT INTO `ubigeo_inei` VALUES (1065, '10', '10', '01', 'JESUS');
INSERT INTO `ubigeo_inei` VALUES (1066, '10', '10', '02', 'BAÑOS');
INSERT INTO `ubigeo_inei` VALUES (1067, '10', '10', '03', 'JIVIA');
INSERT INTO `ubigeo_inei` VALUES (1068, '10', '10', '04', 'QUEROPALCA');
INSERT INTO `ubigeo_inei` VALUES (1069, '10', '10', '05', 'RONDOS');
INSERT INTO `ubigeo_inei` VALUES (1070, '10', '10', '06', 'SAN FRANCISCO DE ASIS');
INSERT INTO `ubigeo_inei` VALUES (1071, '10', '10', '07', 'SAN MIGUEL DE CAURI');
INSERT INTO `ubigeo_inei` VALUES (1072, '10', '11', '00', 'YAROWILCA');
INSERT INTO `ubigeo_inei` VALUES (1073, '10', '11', '01', 'CHAVINILLO');
INSERT INTO `ubigeo_inei` VALUES (1074, '10', '11', '02', 'CAHUAC');
INSERT INTO `ubigeo_inei` VALUES (1075, '10', '11', '03', 'CHACABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1076, '10', '11', '04', 'CHUPAN');
INSERT INTO `ubigeo_inei` VALUES (1077, '10', '11', '05', 'JACAS CHICO');
INSERT INTO `ubigeo_inei` VALUES (1078, '10', '11', '06', 'OBAS');
INSERT INTO `ubigeo_inei` VALUES (1079, '10', '11', '07', 'PAMPAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1080, '10', '11', '08', 'CHORAS');
INSERT INTO `ubigeo_inei` VALUES (1081, '11', '00', '00', 'ICA');
INSERT INTO `ubigeo_inei` VALUES (1082, '11', '01', '00', 'ICA');
INSERT INTO `ubigeo_inei` VALUES (1083, '11', '01', '01', 'ICA');
INSERT INTO `ubigeo_inei` VALUES (1084, '11', '01', '02', 'LA TINGUIÑA');
INSERT INTO `ubigeo_inei` VALUES (1085, '11', '01', '03', 'LOS AQUIJES');
INSERT INTO `ubigeo_inei` VALUES (1086, '11', '01', '04', 'OCUCAJE');
INSERT INTO `ubigeo_inei` VALUES (1087, '11', '01', '05', 'PACHACUTEC');
INSERT INTO `ubigeo_inei` VALUES (1088, '11', '01', '06', 'PARCONA');
INSERT INTO `ubigeo_inei` VALUES (1089, '11', '01', '07', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1090, '11', '01', '08', 'SALAS');
INSERT INTO `ubigeo_inei` VALUES (1091, '11', '01', '09', 'SAN JOSE DE LOS MOLINOS');
INSERT INTO `ubigeo_inei` VALUES (1092, '11', '01', '10', 'SAN JUAN BAUTISTA');
INSERT INTO `ubigeo_inei` VALUES (1093, '11', '01', '11', 'SANTIAGO');
INSERT INTO `ubigeo_inei` VALUES (1094, '11', '01', '12', 'SUBTANJALLA');
INSERT INTO `ubigeo_inei` VALUES (1095, '11', '01', '13', 'TATE');
INSERT INTO `ubigeo_inei` VALUES (1096, '11', '01', '14', 'YAUCA DEL ROSARIO');
INSERT INTO `ubigeo_inei` VALUES (1097, '11', '02', '00', 'CHINCHA');
INSERT INTO `ubigeo_inei` VALUES (1098, '11', '02', '01', 'CHINCHA ALTA');
INSERT INTO `ubigeo_inei` VALUES (1099, '11', '02', '02', 'ALTO LARAN');
INSERT INTO `ubigeo_inei` VALUES (1100, '11', '02', '03', 'CHAVIN');
INSERT INTO `ubigeo_inei` VALUES (1101, '11', '02', '04', 'CHINCHA BAJA');
INSERT INTO `ubigeo_inei` VALUES (1102, '11', '02', '05', 'EL CARMEN');
INSERT INTO `ubigeo_inei` VALUES (1103, '11', '02', '06', 'GROCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (1104, '11', '02', '07', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1105, '11', '02', '08', 'SAN JUAN DE YANAC');
INSERT INTO `ubigeo_inei` VALUES (1106, '11', '02', '09', 'SAN PEDRO DE HUACARPANA');
INSERT INTO `ubigeo_inei` VALUES (1107, '11', '02', '10', 'SUNAMPE');
INSERT INTO `ubigeo_inei` VALUES (1108, '11', '02', '11', 'TAMBO DE MORA');
INSERT INTO `ubigeo_inei` VALUES (1109, '11', '03', '00', 'NAZCA');
INSERT INTO `ubigeo_inei` VALUES (1110, '11', '03', '01', 'NAZCA');
INSERT INTO `ubigeo_inei` VALUES (1111, '11', '03', '02', 'CHANGUILLO');
INSERT INTO `ubigeo_inei` VALUES (1112, '11', '03', '03', 'EL INGENIO');
INSERT INTO `ubigeo_inei` VALUES (1113, '11', '03', '04', 'MARCONA');
INSERT INTO `ubigeo_inei` VALUES (1114, '11', '03', '05', 'VISTA ALEGRE');
INSERT INTO `ubigeo_inei` VALUES (1115, '11', '04', '00', 'PALPA');
INSERT INTO `ubigeo_inei` VALUES (1116, '11', '04', '01', 'PALPA');
INSERT INTO `ubigeo_inei` VALUES (1117, '11', '04', '02', 'LLIPATA');
INSERT INTO `ubigeo_inei` VALUES (1118, '11', '04', '03', 'RIO GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1119, '11', '04', '04', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (1120, '11', '04', '05', 'TIBILLO');
INSERT INTO `ubigeo_inei` VALUES (1121, '11', '05', '00', 'PISCO');
INSERT INTO `ubigeo_inei` VALUES (1122, '11', '05', '01', 'PISCO');
INSERT INTO `ubigeo_inei` VALUES (1123, '11', '05', '02', 'HUANCANO');
INSERT INTO `ubigeo_inei` VALUES (1124, '11', '05', '03', 'HUMAY');
INSERT INTO `ubigeo_inei` VALUES (1125, '11', '05', '04', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (1126, '11', '05', '05', 'PARACAS');
INSERT INTO `ubigeo_inei` VALUES (1127, '11', '05', '06', 'SAN ANDRES');
INSERT INTO `ubigeo_inei` VALUES (1128, '11', '05', '07', 'SAN CLEMENTE');
INSERT INTO `ubigeo_inei` VALUES (1129, '11', '05', '08', 'TUPAC AMARU INCA');
INSERT INTO `ubigeo_inei` VALUES (1130, '12', '00', '00', 'JUNIN');
INSERT INTO `ubigeo_inei` VALUES (1131, '12', '01', '00', 'HUANCAYO');
INSERT INTO `ubigeo_inei` VALUES (1132, '12', '01', '01', 'HUANCAYO');
INSERT INTO `ubigeo_inei` VALUES (1133, '12', '01', '04', 'CARHUACALLANGA');
INSERT INTO `ubigeo_inei` VALUES (1134, '12', '01', '05', 'CHACAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1135, '12', '01', '06', 'CHICCHE');
INSERT INTO `ubigeo_inei` VALUES (1136, '12', '01', '07', 'CHILCA');
INSERT INTO `ubigeo_inei` VALUES (1137, '12', '01', '08', 'CHONGOS ALTO');
INSERT INTO `ubigeo_inei` VALUES (1138, '12', '01', '11', 'CHUPURO');
INSERT INTO `ubigeo_inei` VALUES (1139, '12', '01', '12', 'COLCA');
INSERT INTO `ubigeo_inei` VALUES (1140, '12', '01', '13', 'CULLHUAS');
INSERT INTO `ubigeo_inei` VALUES (1141, '12', '01', '14', 'EL TAMBO');
INSERT INTO `ubigeo_inei` VALUES (1142, '12', '01', '16', 'HUACRAPUQUIO');
INSERT INTO `ubigeo_inei` VALUES (1143, '12', '01', '17', 'HUALHUAS');
INSERT INTO `ubigeo_inei` VALUES (1144, '12', '01', '19', 'HUANCAN');
INSERT INTO `ubigeo_inei` VALUES (1145, '12', '01', '20', 'HUASICANCHA');
INSERT INTO `ubigeo_inei` VALUES (1146, '12', '01', '21', 'HUAYUCACHI');
INSERT INTO `ubigeo_inei` VALUES (1147, '12', '01', '22', 'INGENIO');
INSERT INTO `ubigeo_inei` VALUES (1148, '12', '01', '24', 'PARIAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1149, '12', '01', '25', 'PILCOMAYO');
INSERT INTO `ubigeo_inei` VALUES (1150, '12', '01', '26', 'PUCARA');
INSERT INTO `ubigeo_inei` VALUES (1151, '12', '01', '27', 'QUICHUAY');
INSERT INTO `ubigeo_inei` VALUES (1152, '12', '01', '28', 'QUILCAS');
INSERT INTO `ubigeo_inei` VALUES (1153, '12', '01', '29', 'SAN AGUSTIN');
INSERT INTO `ubigeo_inei` VALUES (1154, '12', '01', '30', 'SAN JERONIMO DE TUNAN');
INSERT INTO `ubigeo_inei` VALUES (1155, '12', '01', '32', 'SAÑO');
INSERT INTO `ubigeo_inei` VALUES (1156, '12', '01', '33', 'SAPALLANGA');
INSERT INTO `ubigeo_inei` VALUES (1157, '12', '01', '34', 'SICAYA');
INSERT INTO `ubigeo_inei` VALUES (1158, '12', '01', '35', 'SANTO DOMINGO DE ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1159, '12', '01', '36', 'VIQUES');
INSERT INTO `ubigeo_inei` VALUES (1160, '12', '02', '00', 'CONCEPCION');
INSERT INTO `ubigeo_inei` VALUES (1161, '12', '02', '01', 'CONCEPCION');
INSERT INTO `ubigeo_inei` VALUES (1162, '12', '02', '02', 'ACO');
INSERT INTO `ubigeo_inei` VALUES (1163, '12', '02', '03', 'ANDAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1164, '12', '02', '04', 'CHAMBARA');
INSERT INTO `ubigeo_inei` VALUES (1165, '12', '02', '05', 'COCHAS');
INSERT INTO `ubigeo_inei` VALUES (1166, '12', '02', '06', 'COMAS');
INSERT INTO `ubigeo_inei` VALUES (1167, '12', '02', '07', 'HEROINAS TOLEDO');
INSERT INTO `ubigeo_inei` VALUES (1168, '12', '02', '08', 'MANZANARES');
INSERT INTO `ubigeo_inei` VALUES (1169, '12', '02', '09', 'MARISCAL CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1170, '12', '02', '10', 'MATAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1171, '12', '02', '11', 'MITO');
INSERT INTO `ubigeo_inei` VALUES (1172, '12', '02', '12', 'NUEVE DE JULIO');
INSERT INTO `ubigeo_inei` VALUES (1173, '12', '02', '13', 'ORCOTUNA');
INSERT INTO `ubigeo_inei` VALUES (1174, '12', '02', '14', 'SAN JOSE DE QUERO');
INSERT INTO `ubigeo_inei` VALUES (1175, '12', '02', '15', 'SANTA ROSA DE OCOPA');
INSERT INTO `ubigeo_inei` VALUES (1176, '12', '03', '00', 'CHANCHAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1177, '12', '03', '01', 'CHANCHAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1178, '12', '03', '02', 'PERENE');
INSERT INTO `ubigeo_inei` VALUES (1179, '12', '03', '03', 'PICHANAQUI');
INSERT INTO `ubigeo_inei` VALUES (1180, '12', '03', '04', 'SAN LUIS DE SHUARO');
INSERT INTO `ubigeo_inei` VALUES (1181, '12', '03', '05', 'SAN RAMON');
INSERT INTO `ubigeo_inei` VALUES (1182, '12', '03', '06', 'VITOC');
INSERT INTO `ubigeo_inei` VALUES (1183, '12', '04', '00', 'JAUJA');
INSERT INTO `ubigeo_inei` VALUES (1184, '12', '04', '01', 'JAUJA');
INSERT INTO `ubigeo_inei` VALUES (1185, '12', '04', '02', 'ACOLLA');
INSERT INTO `ubigeo_inei` VALUES (1186, '12', '04', '03', 'APATA');
INSERT INTO `ubigeo_inei` VALUES (1187, '12', '04', '04', 'ATAURA');
INSERT INTO `ubigeo_inei` VALUES (1188, '12', '04', '05', 'CANCHAYLLO');
INSERT INTO `ubigeo_inei` VALUES (1189, '12', '04', '06', 'CURICACA');
INSERT INTO `ubigeo_inei` VALUES (1190, '12', '04', '07', 'EL MANTARO');
INSERT INTO `ubigeo_inei` VALUES (1191, '12', '04', '08', 'HUAMALI');
INSERT INTO `ubigeo_inei` VALUES (1192, '12', '04', '09', 'HUARIPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1193, '12', '04', '10', 'HUERTAS');
INSERT INTO `ubigeo_inei` VALUES (1194, '12', '04', '11', 'JANJAILLO');
INSERT INTO `ubigeo_inei` VALUES (1195, '12', '04', '12', 'JULCAN');
INSERT INTO `ubigeo_inei` VALUES (1196, '12', '04', '13', 'LEONOR ORDOÑEZ');
INSERT INTO `ubigeo_inei` VALUES (1197, '12', '04', '14', 'LLOCLLAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1198, '12', '04', '15', 'MARCO');
INSERT INTO `ubigeo_inei` VALUES (1199, '12', '04', '16', 'MASMA');
INSERT INTO `ubigeo_inei` VALUES (1200, '12', '04', '17', 'MASMA CHICCHE');
INSERT INTO `ubigeo_inei` VALUES (1201, '12', '04', '18', 'MOLINOS');
INSERT INTO `ubigeo_inei` VALUES (1202, '12', '04', '19', 'MONOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1203, '12', '04', '20', 'MUQUI');
INSERT INTO `ubigeo_inei` VALUES (1204, '12', '04', '21', 'MUQUIYAUYO');
INSERT INTO `ubigeo_inei` VALUES (1205, '12', '04', '22', 'PACA');
INSERT INTO `ubigeo_inei` VALUES (1206, '12', '04', '23', 'PACCHA');
INSERT INTO `ubigeo_inei` VALUES (1207, '12', '04', '24', 'PANCAN');
INSERT INTO `ubigeo_inei` VALUES (1208, '12', '04', '25', 'PARCO');
INSERT INTO `ubigeo_inei` VALUES (1209, '12', '04', '26', 'POMACANCHA');
INSERT INTO `ubigeo_inei` VALUES (1210, '12', '04', '27', 'RICRAN');
INSERT INTO `ubigeo_inei` VALUES (1211, '12', '04', '28', 'SAN LORENZO');
INSERT INTO `ubigeo_inei` VALUES (1212, '12', '04', '29', 'SAN PEDRO DE CHUNAN');
INSERT INTO `ubigeo_inei` VALUES (1213, '12', '04', '30', 'SAUSA');
INSERT INTO `ubigeo_inei` VALUES (1214, '12', '04', '31', 'SINCOS');
INSERT INTO `ubigeo_inei` VALUES (1215, '12', '04', '32', 'TUNAN MARCA');
INSERT INTO `ubigeo_inei` VALUES (1216, '12', '04', '33', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (1217, '12', '04', '34', 'YAUYOS');
INSERT INTO `ubigeo_inei` VALUES (1218, '12', '05', '00', 'JUNIN');
INSERT INTO `ubigeo_inei` VALUES (1219, '12', '05', '01', 'JUNIN');
INSERT INTO `ubigeo_inei` VALUES (1220, '12', '05', '02', 'CARHUAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1221, '12', '05', '03', 'ONDORES');
INSERT INTO `ubigeo_inei` VALUES (1222, '12', '05', '04', 'ULCUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1223, '12', '06', '00', 'SATIPO');
INSERT INTO `ubigeo_inei` VALUES (1224, '12', '06', '01', 'SATIPO');
INSERT INTO `ubigeo_inei` VALUES (1225, '12', '06', '02', 'COVIRIALI');
INSERT INTO `ubigeo_inei` VALUES (1226, '12', '06', '03', 'LLAYLLA');
INSERT INTO `ubigeo_inei` VALUES (1227, '12', '06', '04', 'MAZAMARI');
INSERT INTO `ubigeo_inei` VALUES (1228, '12', '06', '05', 'PAMPA HERMOSA');
INSERT INTO `ubigeo_inei` VALUES (1229, '12', '06', '06', 'PANGOA');
INSERT INTO `ubigeo_inei` VALUES (1230, '12', '06', '07', 'RIO NEGRO');
INSERT INTO `ubigeo_inei` VALUES (1231, '12', '06', '08', 'RIO TAMBO');
INSERT INTO `ubigeo_inei` VALUES (1232, '12', '06', '99', 'MAZAMARI-PANGOA');
INSERT INTO `ubigeo_inei` VALUES (1233, '12', '07', '00', 'TARMA');
INSERT INTO `ubigeo_inei` VALUES (1234, '12', '07', '01', 'TARMA');
INSERT INTO `ubigeo_inei` VALUES (1235, '12', '07', '02', 'ACOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1236, '12', '07', '03', 'HUARICOLCA');
INSERT INTO `ubigeo_inei` VALUES (1237, '12', '07', '04', 'HUASAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1238, '12', '07', '05', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1239, '12', '07', '06', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (1240, '12', '07', '07', 'PALCAMAYO');
INSERT INTO `ubigeo_inei` VALUES (1241, '12', '07', '08', 'SAN PEDRO DE CAJAS');
INSERT INTO `ubigeo_inei` VALUES (1242, '12', '07', '09', 'TAPO');
INSERT INTO `ubigeo_inei` VALUES (1243, '12', '08', '00', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (1244, '12', '08', '01', 'LA OROYA');
INSERT INTO `ubigeo_inei` VALUES (1245, '12', '08', '02', 'CHACAPALPA');
INSERT INTO `ubigeo_inei` VALUES (1246, '12', '08', '03', 'HUAY-HUAY');
INSERT INTO `ubigeo_inei` VALUES (1247, '12', '08', '04', 'MARCAPOMACOCHA');
INSERT INTO `ubigeo_inei` VALUES (1248, '12', '08', '05', 'MOROCOCHA');
INSERT INTO `ubigeo_inei` VALUES (1249, '12', '08', '06', 'PACCHA');
INSERT INTO `ubigeo_inei` VALUES (1250, '12', '08', '07', 'SANTA BARBARA DE CARHUACAYAN');
INSERT INTO `ubigeo_inei` VALUES (1251, '12', '08', '08', 'SANTA ROSA DE SACCO');
INSERT INTO `ubigeo_inei` VALUES (1252, '12', '08', '09', 'SUITUCANCHA');
INSERT INTO `ubigeo_inei` VALUES (1253, '12', '08', '10', 'YAULI');
INSERT INTO `ubigeo_inei` VALUES (1254, '12', '09', '00', 'CHUPACA');
INSERT INTO `ubigeo_inei` VALUES (1255, '12', '09', '01', 'CHUPACA');
INSERT INTO `ubigeo_inei` VALUES (1256, '12', '09', '02', 'AHUAC');
INSERT INTO `ubigeo_inei` VALUES (1257, '12', '09', '03', 'CHONGOS BAJO');
INSERT INTO `ubigeo_inei` VALUES (1258, '12', '09', '04', 'HUACHAC');
INSERT INTO `ubigeo_inei` VALUES (1259, '12', '09', '05', 'HUAMANCACA CHICO');
INSERT INTO `ubigeo_inei` VALUES (1260, '12', '09', '06', 'SAN JUAN DE ISCOS');
INSERT INTO `ubigeo_inei` VALUES (1261, '12', '09', '07', 'SAN JUAN DE JARPA');
INSERT INTO `ubigeo_inei` VALUES (1262, '12', '09', '08', '3 DE DICIEMBRE');
INSERT INTO `ubigeo_inei` VALUES (1263, '12', '09', '09', 'YANACANCHA');
INSERT INTO `ubigeo_inei` VALUES (1264, '13', '00', '00', 'LA LIBERTAD');
INSERT INTO `ubigeo_inei` VALUES (1265, '13', '01', '00', 'TRUJILLO');
INSERT INTO `ubigeo_inei` VALUES (1266, '13', '01', '01', 'TRUJILLO');
INSERT INTO `ubigeo_inei` VALUES (1267, '13', '01', '02', 'EL PORVENIR');
INSERT INTO `ubigeo_inei` VALUES (1268, '13', '01', '03', 'FLORENCIA DE MORA');
INSERT INTO `ubigeo_inei` VALUES (1269, '13', '01', '04', 'HUANCHACO');
INSERT INTO `ubigeo_inei` VALUES (1270, '13', '01', '05', 'LA ESPERANZA');
INSERT INTO `ubigeo_inei` VALUES (1271, '13', '01', '06', 'LAREDO');
INSERT INTO `ubigeo_inei` VALUES (1272, '13', '01', '07', 'MOCHE');
INSERT INTO `ubigeo_inei` VALUES (1273, '13', '01', '08', 'POROTO');
INSERT INTO `ubigeo_inei` VALUES (1274, '13', '01', '09', 'SALAVERRY');
INSERT INTO `ubigeo_inei` VALUES (1275, '13', '01', '10', 'SIMBAL');
INSERT INTO `ubigeo_inei` VALUES (1276, '13', '01', '11', 'VICTOR LARCO HERRERA');
INSERT INTO `ubigeo_inei` VALUES (1277, '13', '02', '00', 'ASCOPE');
INSERT INTO `ubigeo_inei` VALUES (1278, '13', '02', '01', 'ASCOPE');
INSERT INTO `ubigeo_inei` VALUES (1279, '13', '02', '02', 'CHICAMA');
INSERT INTO `ubigeo_inei` VALUES (1280, '13', '02', '03', 'CHOCOPE');
INSERT INTO `ubigeo_inei` VALUES (1281, '13', '02', '04', 'MAGDALENA DE CAO');
INSERT INTO `ubigeo_inei` VALUES (1282, '13', '02', '05', 'PAIJAN');
INSERT INTO `ubigeo_inei` VALUES (1283, '13', '02', '06', 'RAZURI');
INSERT INTO `ubigeo_inei` VALUES (1284, '13', '02', '07', 'SANTIAGO DE CAO');
INSERT INTO `ubigeo_inei` VALUES (1285, '13', '02', '08', 'CASA GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1286, '13', '03', '00', 'BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (1287, '13', '03', '01', 'BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (1288, '13', '03', '02', 'BAMBAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1289, '13', '03', '03', 'CONDORMARCA');
INSERT INTO `ubigeo_inei` VALUES (1290, '13', '03', '04', 'LONGOTEA');
INSERT INTO `ubigeo_inei` VALUES (1291, '13', '03', '05', 'UCHUMARCA');
INSERT INTO `ubigeo_inei` VALUES (1292, '13', '03', '06', 'UCUNCHA');
INSERT INTO `ubigeo_inei` VALUES (1293, '13', '04', '00', 'CHEPEN');
INSERT INTO `ubigeo_inei` VALUES (1294, '13', '04', '01', 'CHEPEN');
INSERT INTO `ubigeo_inei` VALUES (1295, '13', '04', '02', 'PACANGA');
INSERT INTO `ubigeo_inei` VALUES (1296, '13', '04', '03', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1297, '13', '05', '00', 'JULCAN');
INSERT INTO `ubigeo_inei` VALUES (1298, '13', '05', '01', 'JULCAN');
INSERT INTO `ubigeo_inei` VALUES (1299, '13', '05', '02', 'CALAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1300, '13', '05', '03', 'CARABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1301, '13', '05', '04', 'HUASO');
INSERT INTO `ubigeo_inei` VALUES (1302, '13', '06', '00', 'OTUZCO');
INSERT INTO `ubigeo_inei` VALUES (1303, '13', '06', '01', 'OTUZCO');
INSERT INTO `ubigeo_inei` VALUES (1304, '13', '06', '02', 'AGALLPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1305, '13', '06', '04', 'CHARAT');
INSERT INTO `ubigeo_inei` VALUES (1306, '13', '06', '05', 'HUARANCHAL');
INSERT INTO `ubigeo_inei` VALUES (1307, '13', '06', '06', 'LA CUESTA');
INSERT INTO `ubigeo_inei` VALUES (1308, '13', '06', '08', 'MACHE');
INSERT INTO `ubigeo_inei` VALUES (1309, '13', '06', '10', 'PARANDAY');
INSERT INTO `ubigeo_inei` VALUES (1310, '13', '06', '11', 'SALPO');
INSERT INTO `ubigeo_inei` VALUES (1311, '13', '06', '13', 'SINSICAP');
INSERT INTO `ubigeo_inei` VALUES (1312, '13', '06', '14', 'USQUIL');
INSERT INTO `ubigeo_inei` VALUES (1313, '13', '07', '00', 'PACASMAYO');
INSERT INTO `ubigeo_inei` VALUES (1314, '13', '07', '01', 'SAN PEDRO DE LLOC');
INSERT INTO `ubigeo_inei` VALUES (1315, '13', '07', '02', 'GUADALUPE');
INSERT INTO `ubigeo_inei` VALUES (1316, '13', '07', '03', 'JEQUETEPEQUE');
INSERT INTO `ubigeo_inei` VALUES (1317, '13', '07', '04', 'PACASMAYO');
INSERT INTO `ubigeo_inei` VALUES (1318, '13', '07', '05', 'SAN JOSE');
INSERT INTO `ubigeo_inei` VALUES (1319, '13', '08', '00', 'PATAZ');
INSERT INTO `ubigeo_inei` VALUES (1320, '13', '08', '01', 'TAYABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1321, '13', '08', '02', 'BULDIBUYO');
INSERT INTO `ubigeo_inei` VALUES (1322, '13', '08', '03', 'CHILLIA');
INSERT INTO `ubigeo_inei` VALUES (1323, '13', '08', '04', 'HUANCASPATA');
INSERT INTO `ubigeo_inei` VALUES (1324, '13', '08', '05', 'HUAYLILLAS');
INSERT INTO `ubigeo_inei` VALUES (1325, '13', '08', '06', 'HUAYO');
INSERT INTO `ubigeo_inei` VALUES (1326, '13', '08', '07', 'ONGON');
INSERT INTO `ubigeo_inei` VALUES (1327, '13', '08', '08', 'PARCOY');
INSERT INTO `ubigeo_inei` VALUES (1328, '13', '08', '09', 'PATAZ');
INSERT INTO `ubigeo_inei` VALUES (1329, '13', '08', '10', 'PIAS');
INSERT INTO `ubigeo_inei` VALUES (1330, '13', '08', '11', 'SANTIAGO DE CHALLAS');
INSERT INTO `ubigeo_inei` VALUES (1331, '13', '08', '12', 'TAURIJA');
INSERT INTO `ubigeo_inei` VALUES (1332, '13', '08', '13', 'URPAY');
INSERT INTO `ubigeo_inei` VALUES (1333, '13', '09', '00', 'SANCHEZ CARRION');
INSERT INTO `ubigeo_inei` VALUES (1334, '13', '09', '01', 'HUAMACHUCO');
INSERT INTO `ubigeo_inei` VALUES (1335, '13', '09', '02', 'CHUGAY');
INSERT INTO `ubigeo_inei` VALUES (1336, '13', '09', '03', 'COCHORCO');
INSERT INTO `ubigeo_inei` VALUES (1337, '13', '09', '04', 'CURGOS');
INSERT INTO `ubigeo_inei` VALUES (1338, '13', '09', '05', 'MARCABAL');
INSERT INTO `ubigeo_inei` VALUES (1339, '13', '09', '06', 'SANAGORAN');
INSERT INTO `ubigeo_inei` VALUES (1340, '13', '09', '07', 'SARIN');
INSERT INTO `ubigeo_inei` VALUES (1341, '13', '09', '08', 'SARTIMBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1342, '13', '10', '00', 'SANTIAGO DE CHUCO');
INSERT INTO `ubigeo_inei` VALUES (1343, '13', '10', '01', 'SANTIAGO DE CHUCO');
INSERT INTO `ubigeo_inei` VALUES (1344, '13', '10', '02', 'ANGASMARCA');
INSERT INTO `ubigeo_inei` VALUES (1345, '13', '10', '03', 'CACHICADAN');
INSERT INTO `ubigeo_inei` VALUES (1346, '13', '10', '04', 'MOLLEBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1347, '13', '10', '05', 'MOLLEPATA');
INSERT INTO `ubigeo_inei` VALUES (1348, '13', '10', '06', 'QUIRUVILCA');
INSERT INTO `ubigeo_inei` VALUES (1349, '13', '10', '07', 'SANTA CRUZ DE CHUCA');
INSERT INTO `ubigeo_inei` VALUES (1350, '13', '10', '08', 'SITABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1351, '13', '11', '00', 'GRAN CHIMU');
INSERT INTO `ubigeo_inei` VALUES (1352, '13', '11', '01', 'CASCAS');
INSERT INTO `ubigeo_inei` VALUES (1353, '13', '11', '02', 'LUCMA');
INSERT INTO `ubigeo_inei` VALUES (1354, '13', '11', '03', 'MARMOT');
INSERT INTO `ubigeo_inei` VALUES (1355, '13', '11', '04', 'SAYAPULLO');
INSERT INTO `ubigeo_inei` VALUES (1356, '13', '12', '00', 'VIRU');
INSERT INTO `ubigeo_inei` VALUES (1357, '13', '12', '01', 'VIRU');
INSERT INTO `ubigeo_inei` VALUES (1358, '13', '12', '02', 'CHAO');
INSERT INTO `ubigeo_inei` VALUES (1359, '13', '12', '03', 'GUADALUPITO');
INSERT INTO `ubigeo_inei` VALUES (1360, '14', '00', '00', 'LAMBAYEQUE');
INSERT INTO `ubigeo_inei` VALUES (1361, '14', '01', '00', 'CHICLAYO');
INSERT INTO `ubigeo_inei` VALUES (1362, '14', '01', '01', 'CHICLAYO');
INSERT INTO `ubigeo_inei` VALUES (1363, '14', '01', '02', 'CHONGOYAPE');
INSERT INTO `ubigeo_inei` VALUES (1364, '14', '01', '03', 'ETEN');
INSERT INTO `ubigeo_inei` VALUES (1365, '14', '01', '04', 'ETEN PUERTO');
INSERT INTO `ubigeo_inei` VALUES (1366, '14', '01', '05', 'JOSE LEONARDO ORTIZ');
INSERT INTO `ubigeo_inei` VALUES (1367, '14', '01', '06', 'LA VICTORIA');
INSERT INTO `ubigeo_inei` VALUES (1368, '14', '01', '07', 'LAGUNAS');
INSERT INTO `ubigeo_inei` VALUES (1369, '14', '01', '08', 'MONSEFU');
INSERT INTO `ubigeo_inei` VALUES (1370, '14', '01', '09', 'NUEVA ARICA');
INSERT INTO `ubigeo_inei` VALUES (1371, '14', '01', '10', 'OYOTUN');
INSERT INTO `ubigeo_inei` VALUES (1372, '14', '01', '11', 'PICSI');
INSERT INTO `ubigeo_inei` VALUES (1373, '14', '01', '12', 'PIMENTEL');
INSERT INTO `ubigeo_inei` VALUES (1374, '14', '01', '13', 'REQUE');
INSERT INTO `ubigeo_inei` VALUES (1375, '14', '01', '14', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1376, '14', '01', '15', 'SAÑA');
INSERT INTO `ubigeo_inei` VALUES (1377, '14', '01', '16', 'CAYALTÍ');
INSERT INTO `ubigeo_inei` VALUES (1378, '14', '01', '17', 'PATAPO');
INSERT INTO `ubigeo_inei` VALUES (1379, '14', '01', '18', 'POMALCA');
INSERT INTO `ubigeo_inei` VALUES (1380, '14', '01', '19', 'PUCALÁ');
INSERT INTO `ubigeo_inei` VALUES (1381, '14', '01', '20', 'TUMÁN');
INSERT INTO `ubigeo_inei` VALUES (1382, '14', '02', '00', 'FERREÑAFE');
INSERT INTO `ubigeo_inei` VALUES (1383, '14', '02', '01', 'FERREÑAFE');
INSERT INTO `ubigeo_inei` VALUES (1384, '14', '02', '02', 'CAÑARIS');
INSERT INTO `ubigeo_inei` VALUES (1385, '14', '02', '03', 'INCAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1386, '14', '02', '04', 'MANUEL ANTONIO MESONES MURO');
INSERT INTO `ubigeo_inei` VALUES (1387, '14', '02', '05', 'PITIPO');
INSERT INTO `ubigeo_inei` VALUES (1388, '14', '02', '06', 'PUEBLO NUEVO');
INSERT INTO `ubigeo_inei` VALUES (1389, '14', '03', '00', 'LAMBAYEQUE');
INSERT INTO `ubigeo_inei` VALUES (1390, '14', '03', '01', 'LAMBAYEQUE');
INSERT INTO `ubigeo_inei` VALUES (1391, '14', '03', '02', 'CHOCHOPE');
INSERT INTO `ubigeo_inei` VALUES (1392, '14', '03', '03', 'ILLIMO');
INSERT INTO `ubigeo_inei` VALUES (1393, '14', '03', '04', 'JAYANCA');
INSERT INTO `ubigeo_inei` VALUES (1394, '14', '03', '05', 'MOCHUMI');
INSERT INTO `ubigeo_inei` VALUES (1395, '14', '03', '06', 'MORROPE');
INSERT INTO `ubigeo_inei` VALUES (1396, '14', '03', '07', 'MOTUPE');
INSERT INTO `ubigeo_inei` VALUES (1397, '14', '03', '08', 'OLMOS');
INSERT INTO `ubigeo_inei` VALUES (1398, '14', '03', '09', 'PACORA');
INSERT INTO `ubigeo_inei` VALUES (1399, '14', '03', '10', 'SALAS');
INSERT INTO `ubigeo_inei` VALUES (1400, '14', '03', '11', 'SAN JOSE');
INSERT INTO `ubigeo_inei` VALUES (1401, '14', '03', '12', 'TUCUME');
INSERT INTO `ubigeo_inei` VALUES (1402, '15', '00', '00', 'LIMA');
INSERT INTO `ubigeo_inei` VALUES (1403, '15', '01', '00', 'LIMA');
INSERT INTO `ubigeo_inei` VALUES (1404, '15', '01', '01', 'LIMA');
INSERT INTO `ubigeo_inei` VALUES (1405, '15', '01', '02', 'ANCON');
INSERT INTO `ubigeo_inei` VALUES (1406, '15', '01', '03', 'ATE');
INSERT INTO `ubigeo_inei` VALUES (1407, '15', '01', '04', 'BARRANCO');
INSERT INTO `ubigeo_inei` VALUES (1408, '15', '01', '05', 'BREÑA');
INSERT INTO `ubigeo_inei` VALUES (1409, '15', '01', '06', 'CARABAYLLO');
INSERT INTO `ubigeo_inei` VALUES (1410, '15', '01', '07', 'CHACLACAYO');
INSERT INTO `ubigeo_inei` VALUES (1411, '15', '01', '08', 'CHORRILLOS');
INSERT INTO `ubigeo_inei` VALUES (1412, '15', '01', '09', 'CIENEGUILLA');
INSERT INTO `ubigeo_inei` VALUES (1413, '15', '01', '10', 'COMAS');
INSERT INTO `ubigeo_inei` VALUES (1414, '15', '01', '11', 'EL AGUSTINO');
INSERT INTO `ubigeo_inei` VALUES (1415, '15', '01', '12', 'INDEPENDENCIA');
INSERT INTO `ubigeo_inei` VALUES (1416, '15', '01', '13', 'JESUS MARIA');
INSERT INTO `ubigeo_inei` VALUES (1417, '15', '01', '14', 'LA MOLINA');
INSERT INTO `ubigeo_inei` VALUES (1418, '15', '01', '15', 'LA VICTORIA');
INSERT INTO `ubigeo_inei` VALUES (1419, '15', '01', '16', 'LINCE');
INSERT INTO `ubigeo_inei` VALUES (1420, '15', '01', '17', 'LOS OLIVOS');
INSERT INTO `ubigeo_inei` VALUES (1421, '15', '01', '18', 'LURIGANCHO');
INSERT INTO `ubigeo_inei` VALUES (1422, '15', '01', '19', 'LURIN');
INSERT INTO `ubigeo_inei` VALUES (1423, '15', '01', '20', 'MAGDALENA DEL MAR');
INSERT INTO `ubigeo_inei` VALUES (1424, '15', '01', '21', 'PUEBLO LIBRE (MAGDALENA VIEJA)');
INSERT INTO `ubigeo_inei` VALUES (1425, '15', '01', '22', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1426, '15', '01', '23', 'PACHACAMAC');
INSERT INTO `ubigeo_inei` VALUES (1427, '15', '01', '24', 'PUCUSANA');
INSERT INTO `ubigeo_inei` VALUES (1428, '15', '01', '25', 'PUENTE PIEDRA');
INSERT INTO `ubigeo_inei` VALUES (1429, '15', '01', '26', 'PUNTA HERMOSA');
INSERT INTO `ubigeo_inei` VALUES (1430, '15', '01', '27', 'PUNTA NEGRA');
INSERT INTO `ubigeo_inei` VALUES (1431, '15', '01', '28', 'RIMAC');
INSERT INTO `ubigeo_inei` VALUES (1432, '15', '01', '29', 'SAN BARTOLO');
INSERT INTO `ubigeo_inei` VALUES (1433, '15', '01', '30', 'SAN BORJA');
INSERT INTO `ubigeo_inei` VALUES (1434, '15', '01', '31', 'SAN ISIDRO');
INSERT INTO `ubigeo_inei` VALUES (1435, '15', '01', '32', 'SAN JUAN DE LURIGANCHO');
INSERT INTO `ubigeo_inei` VALUES (1436, '15', '01', '33', 'SAN JUAN DE MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1437, '15', '01', '34', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (1438, '15', '01', '35', 'SAN MARTIN DE PORRES');
INSERT INTO `ubigeo_inei` VALUES (1439, '15', '01', '36', 'SAN MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (1440, '15', '01', '37', 'SANTA ANITA');
INSERT INTO `ubigeo_inei` VALUES (1441, '15', '01', '38', 'SANTA MARIA DEL MAR');
INSERT INTO `ubigeo_inei` VALUES (1442, '15', '01', '39', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1443, '15', '01', '40', 'SANTIAGO DE SURCO');
INSERT INTO `ubigeo_inei` VALUES (1444, '15', '01', '41', 'SURQUILLO');
INSERT INTO `ubigeo_inei` VALUES (1445, '15', '01', '42', 'VILLA EL SALVADOR');
INSERT INTO `ubigeo_inei` VALUES (1446, '15', '01', '43', 'VILLA MARIA DEL TRIUNFO');
INSERT INTO `ubigeo_inei` VALUES (1447, '15', '02', '00', 'BARRANCA');
INSERT INTO `ubigeo_inei` VALUES (1448, '15', '02', '01', 'BARRANCA');
INSERT INTO `ubigeo_inei` VALUES (1449, '15', '02', '02', 'PARAMONGA');
INSERT INTO `ubigeo_inei` VALUES (1450, '15', '02', '03', 'PATIVILCA');
INSERT INTO `ubigeo_inei` VALUES (1451, '15', '02', '04', 'SUPE');
INSERT INTO `ubigeo_inei` VALUES (1452, '15', '02', '05', 'SUPE PUERTO');
INSERT INTO `ubigeo_inei` VALUES (1453, '15', '03', '00', 'CAJATAMBO');
INSERT INTO `ubigeo_inei` VALUES (1454, '15', '03', '01', 'CAJATAMBO');
INSERT INTO `ubigeo_inei` VALUES (1455, '15', '03', '02', 'COPA');
INSERT INTO `ubigeo_inei` VALUES (1456, '15', '03', '03', 'GORGOR');
INSERT INTO `ubigeo_inei` VALUES (1457, '15', '03', '04', 'HUANCAPON');
INSERT INTO `ubigeo_inei` VALUES (1458, '15', '03', '05', 'MANAS');
INSERT INTO `ubigeo_inei` VALUES (1459, '15', '04', '00', 'CANTA');
INSERT INTO `ubigeo_inei` VALUES (1460, '15', '04', '01', 'CANTA');
INSERT INTO `ubigeo_inei` VALUES (1461, '15', '04', '02', 'ARAHUAY');
INSERT INTO `ubigeo_inei` VALUES (1462, '15', '04', '03', 'HUAMANTANGA');
INSERT INTO `ubigeo_inei` VALUES (1463, '15', '04', '04', 'HUAROS');
INSERT INTO `ubigeo_inei` VALUES (1464, '15', '04', '05', 'LACHAQUI');
INSERT INTO `ubigeo_inei` VALUES (1465, '15', '04', '06', 'SAN BUENAVENTURA');
INSERT INTO `ubigeo_inei` VALUES (1466, '15', '04', '07', 'SANTA ROSA DE QUIVES');
INSERT INTO `ubigeo_inei` VALUES (1467, '15', '05', '00', 'CAÑETE');
INSERT INTO `ubigeo_inei` VALUES (1468, '15', '05', '01', 'SAN VICENTE DE CAÑETE');
INSERT INTO `ubigeo_inei` VALUES (1469, '15', '05', '02', 'ASIA');
INSERT INTO `ubigeo_inei` VALUES (1470, '15', '05', '03', 'CALANGO');
INSERT INTO `ubigeo_inei` VALUES (1471, '15', '05', '04', 'CERRO AZUL');
INSERT INTO `ubigeo_inei` VALUES (1472, '15', '05', '05', 'CHILCA');
INSERT INTO `ubigeo_inei` VALUES (1473, '15', '05', '06', 'COAYLLO');
INSERT INTO `ubigeo_inei` VALUES (1474, '15', '05', '07', 'IMPERIAL');
INSERT INTO `ubigeo_inei` VALUES (1475, '15', '05', '08', 'LUNAHUANA');
INSERT INTO `ubigeo_inei` VALUES (1476, '15', '05', '09', 'MALA');
INSERT INTO `ubigeo_inei` VALUES (1477, '15', '05', '10', 'NUEVO IMPERIAL');
INSERT INTO `ubigeo_inei` VALUES (1478, '15', '05', '11', 'PACARAN');
INSERT INTO `ubigeo_inei` VALUES (1479, '15', '05', '12', 'QUILMANA');
INSERT INTO `ubigeo_inei` VALUES (1480, '15', '05', '13', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1481, '15', '05', '14', 'SAN LUIS');
INSERT INTO `ubigeo_inei` VALUES (1482, '15', '05', '15', 'SANTA CRUZ DE FLORES');
INSERT INTO `ubigeo_inei` VALUES (1483, '15', '05', '16', 'ZUÑIGA');
INSERT INTO `ubigeo_inei` VALUES (1484, '15', '06', '00', 'HUARAL');
INSERT INTO `ubigeo_inei` VALUES (1485, '15', '06', '01', 'HUARAL');
INSERT INTO `ubigeo_inei` VALUES (1486, '15', '06', '02', 'ATAVILLOS ALTO');
INSERT INTO `ubigeo_inei` VALUES (1487, '15', '06', '03', 'ATAVILLOS BAJO');
INSERT INTO `ubigeo_inei` VALUES (1488, '15', '06', '04', 'AUCALLAMA');
INSERT INTO `ubigeo_inei` VALUES (1489, '15', '06', '05', 'CHANCAY');
INSERT INTO `ubigeo_inei` VALUES (1490, '15', '06', '06', 'IHUARI');
INSERT INTO `ubigeo_inei` VALUES (1491, '15', '06', '07', 'LAMPIAN');
INSERT INTO `ubigeo_inei` VALUES (1492, '15', '06', '08', 'PACARAOS');
INSERT INTO `ubigeo_inei` VALUES (1493, '15', '06', '09', 'SAN MIGUEL DE ACOS');
INSERT INTO `ubigeo_inei` VALUES (1494, '15', '06', '10', 'SANTA CRUZ DE ANDAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1495, '15', '06', '11', 'SUMBILCA');
INSERT INTO `ubigeo_inei` VALUES (1496, '15', '06', '12', 'VEINTISIETE DE NOVIEMBRE');
INSERT INTO `ubigeo_inei` VALUES (1497, '15', '07', '00', 'HUAROCHIRI');
INSERT INTO `ubigeo_inei` VALUES (1498, '15', '07', '01', 'MATUCANA');
INSERT INTO `ubigeo_inei` VALUES (1499, '15', '07', '02', 'ANTIOQUIA');
INSERT INTO `ubigeo_inei` VALUES (1500, '15', '07', '03', 'CALLAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1501, '15', '07', '04', 'CARAMPOMA');
INSERT INTO `ubigeo_inei` VALUES (1502, '15', '07', '05', 'CHICLA');
INSERT INTO `ubigeo_inei` VALUES (1503, '15', '07', '06', 'CUENCA');
INSERT INTO `ubigeo_inei` VALUES (1504, '15', '07', '07', 'HUACHUPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1505, '15', '07', '08', 'HUANZA');
INSERT INTO `ubigeo_inei` VALUES (1506, '15', '07', '09', 'HUAROCHIRI');
INSERT INTO `ubigeo_inei` VALUES (1507, '15', '07', '10', 'LAHUAYTAMBO');
INSERT INTO `ubigeo_inei` VALUES (1508, '15', '07', '11', 'LANGA');
INSERT INTO `ubigeo_inei` VALUES (1509, '15', '07', '12', 'LARAOS');
INSERT INTO `ubigeo_inei` VALUES (1510, '15', '07', '13', 'MARIATANA');
INSERT INTO `ubigeo_inei` VALUES (1511, '15', '07', '14', 'RICARDO PALMA');
INSERT INTO `ubigeo_inei` VALUES (1512, '15', '07', '15', 'SAN ANDRES DE TUPICOCHA');
INSERT INTO `ubigeo_inei` VALUES (1513, '15', '07', '16', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1514, '15', '07', '17', 'SAN BARTOLOME');
INSERT INTO `ubigeo_inei` VALUES (1515, '15', '07', '18', 'SAN DAMIAN');
INSERT INTO `ubigeo_inei` VALUES (1516, '15', '07', '19', 'SAN JUAN DE IRIS');
INSERT INTO `ubigeo_inei` VALUES (1517, '15', '07', '20', 'SAN JUAN DE TANTARANCHE');
INSERT INTO `ubigeo_inei` VALUES (1518, '15', '07', '21', 'SAN LORENZO DE QUINTI');
INSERT INTO `ubigeo_inei` VALUES (1519, '15', '07', '22', 'SAN MATEO');
INSERT INTO `ubigeo_inei` VALUES (1520, '15', '07', '23', 'SAN MATEO DE OTAO');
INSERT INTO `ubigeo_inei` VALUES (1521, '15', '07', '24', 'SAN PEDRO DE CASTA');
INSERT INTO `ubigeo_inei` VALUES (1522, '15', '07', '25', 'SAN PEDRO DE HUANCAYRE');
INSERT INTO `ubigeo_inei` VALUES (1523, '15', '07', '26', 'SANGALLAYA');
INSERT INTO `ubigeo_inei` VALUES (1524, '15', '07', '27', 'SANTA CRUZ DE COCACHACRA');
INSERT INTO `ubigeo_inei` VALUES (1525, '15', '07', '28', 'SANTA EULALIA');
INSERT INTO `ubigeo_inei` VALUES (1526, '15', '07', '29', 'SANTIAGO DE ANCHUCAYA');
INSERT INTO `ubigeo_inei` VALUES (1527, '15', '07', '30', 'SANTIAGO DE TUNA');
INSERT INTO `ubigeo_inei` VALUES (1528, '15', '07', '31', 'SANTO DOMINGO DE LOS OLLEROS');
INSERT INTO `ubigeo_inei` VALUES (1529, '15', '07', '32', 'SURCO');
INSERT INTO `ubigeo_inei` VALUES (1530, '15', '08', '00', 'HUAURA');
INSERT INTO `ubigeo_inei` VALUES (1531, '15', '08', '01', 'HUACHO');
INSERT INTO `ubigeo_inei` VALUES (1532, '15', '08', '02', 'AMBAR');
INSERT INTO `ubigeo_inei` VALUES (1533, '15', '08', '03', 'CALETA DE CARQUIN');
INSERT INTO `ubigeo_inei` VALUES (1534, '15', '08', '04', 'CHECRAS');
INSERT INTO `ubigeo_inei` VALUES (1535, '15', '08', '05', 'HUALMAY');
INSERT INTO `ubigeo_inei` VALUES (1536, '15', '08', '06', 'HUAURA');
INSERT INTO `ubigeo_inei` VALUES (1537, '15', '08', '07', 'LEONCIO PRADO');
INSERT INTO `ubigeo_inei` VALUES (1538, '15', '08', '08', 'PACCHO');
INSERT INTO `ubigeo_inei` VALUES (1539, '15', '08', '09', 'SANTA LEONOR');
INSERT INTO `ubigeo_inei` VALUES (1540, '15', '08', '10', 'SANTA MARIA');
INSERT INTO `ubigeo_inei` VALUES (1541, '15', '08', '11', 'SAYAN');
INSERT INTO `ubigeo_inei` VALUES (1542, '15', '08', '12', 'VEGUETA');
INSERT INTO `ubigeo_inei` VALUES (1543, '15', '09', '00', 'OYON');
INSERT INTO `ubigeo_inei` VALUES (1544, '15', '09', '01', 'OYON');
INSERT INTO `ubigeo_inei` VALUES (1545, '15', '09', '02', 'ANDAJES');
INSERT INTO `ubigeo_inei` VALUES (1546, '15', '09', '03', 'CAUJUL');
INSERT INTO `ubigeo_inei` VALUES (1547, '15', '09', '04', 'COCHAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1548, '15', '09', '05', 'NAVAN');
INSERT INTO `ubigeo_inei` VALUES (1549, '15', '09', '06', 'PACHANGARA');
INSERT INTO `ubigeo_inei` VALUES (1550, '15', '10', '00', 'YAUYOS');
INSERT INTO `ubigeo_inei` VALUES (1551, '15', '10', '01', 'YAUYOS');
INSERT INTO `ubigeo_inei` VALUES (1552, '15', '10', '02', 'ALIS');
INSERT INTO `ubigeo_inei` VALUES (1553, '15', '10', '03', 'AYAUCA');
INSERT INTO `ubigeo_inei` VALUES (1554, '15', '10', '04', 'AYAVIRI');
INSERT INTO `ubigeo_inei` VALUES (1555, '15', '10', '05', 'AZANGARO');
INSERT INTO `ubigeo_inei` VALUES (1556, '15', '10', '06', 'CACRA');
INSERT INTO `ubigeo_inei` VALUES (1557, '15', '10', '07', 'CARANIA');
INSERT INTO `ubigeo_inei` VALUES (1558, '15', '10', '08', 'CATAHUASI');
INSERT INTO `ubigeo_inei` VALUES (1559, '15', '10', '09', 'CHOCOS');
INSERT INTO `ubigeo_inei` VALUES (1560, '15', '10', '10', 'COCHAS');
INSERT INTO `ubigeo_inei` VALUES (1561, '15', '10', '11', 'COLONIA');
INSERT INTO `ubigeo_inei` VALUES (1562, '15', '10', '12', 'HONGOS');
INSERT INTO `ubigeo_inei` VALUES (1563, '15', '10', '13', 'HUAMPARA');
INSERT INTO `ubigeo_inei` VALUES (1564, '15', '10', '14', 'HUANCAYA');
INSERT INTO `ubigeo_inei` VALUES (1565, '15', '10', '15', 'HUANGASCAR');
INSERT INTO `ubigeo_inei` VALUES (1566, '15', '10', '16', 'HUANTAN');
INSERT INTO `ubigeo_inei` VALUES (1567, '15', '10', '17', 'HUAÑEC');
INSERT INTO `ubigeo_inei` VALUES (1568, '15', '10', '18', 'LARAOS');
INSERT INTO `ubigeo_inei` VALUES (1569, '15', '10', '19', 'LINCHA');
INSERT INTO `ubigeo_inei` VALUES (1570, '15', '10', '20', 'MADEAN');
INSERT INTO `ubigeo_inei` VALUES (1571, '15', '10', '21', 'MIRAFLORES');
INSERT INTO `ubigeo_inei` VALUES (1572, '15', '10', '22', 'OMAS');
INSERT INTO `ubigeo_inei` VALUES (1573, '15', '10', '23', 'PUTINZA');
INSERT INTO `ubigeo_inei` VALUES (1574, '15', '10', '24', 'QUINCHES');
INSERT INTO `ubigeo_inei` VALUES (1575, '15', '10', '25', 'QUINOCAY');
INSERT INTO `ubigeo_inei` VALUES (1576, '15', '10', '26', 'SAN JOAQUIN');
INSERT INTO `ubigeo_inei` VALUES (1577, '15', '10', '27', 'SAN PEDRO DE PILAS');
INSERT INTO `ubigeo_inei` VALUES (1578, '15', '10', '28', 'TANTA');
INSERT INTO `ubigeo_inei` VALUES (1579, '15', '10', '29', 'TAURIPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1580, '15', '10', '30', 'TOMAS');
INSERT INTO `ubigeo_inei` VALUES (1581, '15', '10', '31', 'TUPE');
INSERT INTO `ubigeo_inei` VALUES (1582, '15', '10', '32', 'VIÑAC');
INSERT INTO `ubigeo_inei` VALUES (1583, '15', '10', '33', 'VITIS');
INSERT INTO `ubigeo_inei` VALUES (1584, '16', '00', '00', 'LORETO');
INSERT INTO `ubigeo_inei` VALUES (1585, '16', '01', '00', 'MAYNAS');
INSERT INTO `ubigeo_inei` VALUES (1586, '16', '01', '01', 'IQUITOS');
INSERT INTO `ubigeo_inei` VALUES (1587, '16', '01', '02', 'ALTO NANAY');
INSERT INTO `ubigeo_inei` VALUES (1588, '16', '01', '03', 'FERNANDO LORES');
INSERT INTO `ubigeo_inei` VALUES (1589, '16', '01', '04', 'INDIANA');
INSERT INTO `ubigeo_inei` VALUES (1590, '16', '01', '05', 'LAS AMAZONAS');
INSERT INTO `ubigeo_inei` VALUES (1591, '16', '01', '06', 'MAZAN');
INSERT INTO `ubigeo_inei` VALUES (1592, '16', '01', '07', 'NAPO');
INSERT INTO `ubigeo_inei` VALUES (1593, '16', '01', '08', 'PUNCHANA');
INSERT INTO `ubigeo_inei` VALUES (1594, '16', '01', '09', 'PUTUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1595, '16', '01', '10', 'TORRES CAUSANA');
INSERT INTO `ubigeo_inei` VALUES (1596, '16', '01', '12', 'BELÉN');
INSERT INTO `ubigeo_inei` VALUES (1597, '16', '01', '13', 'SAN JUAN BAUTISTA');
INSERT INTO `ubigeo_inei` VALUES (1598, '16', '01', '14', 'TENIENTE MANUEL CLAVERO');
INSERT INTO `ubigeo_inei` VALUES (1599, '16', '02', '00', 'ALTO AMAZONAS');
INSERT INTO `ubigeo_inei` VALUES (1600, '16', '02', '01', 'YURIMAGUAS');
INSERT INTO `ubigeo_inei` VALUES (1601, '16', '02', '02', 'BALSAPUERTO');
INSERT INTO `ubigeo_inei` VALUES (1602, '16', '02', '05', 'JEBEROS');
INSERT INTO `ubigeo_inei` VALUES (1603, '16', '02', '06', 'LAGUNAS');
INSERT INTO `ubigeo_inei` VALUES (1604, '16', '02', '10', 'SANTA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (1605, '16', '02', '11', 'TENIENTE CESAR LOPEZ ROJAS');
INSERT INTO `ubigeo_inei` VALUES (1606, '16', '03', '00', 'LORETO');
INSERT INTO `ubigeo_inei` VALUES (1607, '16', '03', '01', 'NAUTA');
INSERT INTO `ubigeo_inei` VALUES (1608, '16', '03', '02', 'PARINARI');
INSERT INTO `ubigeo_inei` VALUES (1609, '16', '03', '03', 'TIGRE');
INSERT INTO `ubigeo_inei` VALUES (1610, '16', '03', '04', 'TROMPETEROS');
INSERT INTO `ubigeo_inei` VALUES (1611, '16', '03', '05', 'URARINAS');
INSERT INTO `ubigeo_inei` VALUES (1612, '16', '04', '00', 'MARISCAL RAMON CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1613, '16', '04', '01', 'RAMON CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1614, '16', '04', '02', 'PEBAS');
INSERT INTO `ubigeo_inei` VALUES (1615, '16', '04', '03', 'YAVARI');
INSERT INTO `ubigeo_inei` VALUES (1616, '16', '04', '04', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (1617, '16', '05', '00', 'REQUENA');
INSERT INTO `ubigeo_inei` VALUES (1618, '16', '05', '01', 'REQUENA');
INSERT INTO `ubigeo_inei` VALUES (1619, '16', '05', '02', 'ALTO TAPICHE');
INSERT INTO `ubigeo_inei` VALUES (1620, '16', '05', '03', 'CAPELO');
INSERT INTO `ubigeo_inei` VALUES (1621, '16', '05', '04', 'EMILIO SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1622, '16', '05', '05', 'MAQUIA');
INSERT INTO `ubigeo_inei` VALUES (1623, '16', '05', '06', 'PUINAHUA');
INSERT INTO `ubigeo_inei` VALUES (1624, '16', '05', '07', 'SAQUENA');
INSERT INTO `ubigeo_inei` VALUES (1625, '16', '05', '08', 'SOPLIN');
INSERT INTO `ubigeo_inei` VALUES (1626, '16', '05', '09', 'TAPICHE');
INSERT INTO `ubigeo_inei` VALUES (1627, '16', '05', '10', 'JENARO HERRERA');
INSERT INTO `ubigeo_inei` VALUES (1628, '16', '05', '11', 'YAQUERANA');
INSERT INTO `ubigeo_inei` VALUES (1629, '16', '06', '00', 'UCAYALI');
INSERT INTO `ubigeo_inei` VALUES (1630, '16', '06', '01', 'CONTAMANA');
INSERT INTO `ubigeo_inei` VALUES (1631, '16', '06', '02', 'INAHUAYA');
INSERT INTO `ubigeo_inei` VALUES (1632, '16', '06', '03', 'PADRE MARQUEZ');
INSERT INTO `ubigeo_inei` VALUES (1633, '16', '06', '04', 'PAMPA HERMOSA');
INSERT INTO `ubigeo_inei` VALUES (1634, '16', '06', '05', 'SARAYACU');
INSERT INTO `ubigeo_inei` VALUES (1635, '16', '06', '06', 'VARGAS GUERRA');
INSERT INTO `ubigeo_inei` VALUES (1636, '16', '07', '00', 'DATEM DEL MARAÑÓN');
INSERT INTO `ubigeo_inei` VALUES (1637, '16', '07', '01', 'BARRANCA');
INSERT INTO `ubigeo_inei` VALUES (1638, '16', '07', '02', 'CAHUAPANAS');
INSERT INTO `ubigeo_inei` VALUES (1639, '16', '07', '03', 'MANSERICHE');
INSERT INTO `ubigeo_inei` VALUES (1640, '16', '07', '04', 'MORONA');
INSERT INTO `ubigeo_inei` VALUES (1641, '16', '07', '05', 'PASTAZA');
INSERT INTO `ubigeo_inei` VALUES (1642, '16', '07', '06', 'ANDOAS');
INSERT INTO `ubigeo_inei` VALUES (1643, '16', '08', '00', 'PUTUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1644, '16', '08', '01', 'PUTUMAYO');
INSERT INTO `ubigeo_inei` VALUES (1645, '16', '08', '02', 'ROSA PANDURO');
INSERT INTO `ubigeo_inei` VALUES (1646, '16', '08', '03', 'TENIENTE MANUEL CLAVERO');
INSERT INTO `ubigeo_inei` VALUES (1647, '16', '08', '04', 'YAGUAS');
INSERT INTO `ubigeo_inei` VALUES (1648, '17', '00', '00', 'MADRE DE DIOS');
INSERT INTO `ubigeo_inei` VALUES (1649, '17', '01', '00', 'TAMBOPATA');
INSERT INTO `ubigeo_inei` VALUES (1650, '17', '01', '01', 'TAMBOPATA');
INSERT INTO `ubigeo_inei` VALUES (1651, '17', '01', '02', 'INAMBARI');
INSERT INTO `ubigeo_inei` VALUES (1652, '17', '01', '03', 'LAS PIEDRAS');
INSERT INTO `ubigeo_inei` VALUES (1653, '17', '01', '04', 'LABERINTO');
INSERT INTO `ubigeo_inei` VALUES (1654, '17', '02', '00', 'MANU');
INSERT INTO `ubigeo_inei` VALUES (1655, '17', '02', '01', 'MANU');
INSERT INTO `ubigeo_inei` VALUES (1656, '17', '02', '02', 'FITZCARRALD');
INSERT INTO `ubigeo_inei` VALUES (1657, '17', '02', '03', 'MADRE DE DIOS');
INSERT INTO `ubigeo_inei` VALUES (1658, '17', '02', '04', 'HUEPETUHE');
INSERT INTO `ubigeo_inei` VALUES (1659, '17', '03', '00', 'TAHUAMANU');
INSERT INTO `ubigeo_inei` VALUES (1660, '17', '03', '01', 'IÑAPARI');
INSERT INTO `ubigeo_inei` VALUES (1661, '17', '03', '02', 'IBERIA');
INSERT INTO `ubigeo_inei` VALUES (1662, '17', '03', '03', 'TAHUAMANU');
INSERT INTO `ubigeo_inei` VALUES (1663, '18', '00', '00', 'MOQUEGUA');
INSERT INTO `ubigeo_inei` VALUES (1664, '18', '01', '00', 'MARISCAL NIETO');
INSERT INTO `ubigeo_inei` VALUES (1665, '18', '01', '01', 'MOQUEGUA');
INSERT INTO `ubigeo_inei` VALUES (1666, '18', '01', '02', 'CARUMAS');
INSERT INTO `ubigeo_inei` VALUES (1667, '18', '01', '03', 'CUCHUMBAYA');
INSERT INTO `ubigeo_inei` VALUES (1668, '18', '01', '04', 'SAMEGUA');
INSERT INTO `ubigeo_inei` VALUES (1669, '18', '01', '05', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (1670, '18', '01', '06', 'TORATA');
INSERT INTO `ubigeo_inei` VALUES (1671, '18', '02', '00', 'GENERAL SANCHEZ CERRO');
INSERT INTO `ubigeo_inei` VALUES (1672, '18', '02', '01', 'OMATE');
INSERT INTO `ubigeo_inei` VALUES (1673, '18', '02', '02', 'CHOJATA');
INSERT INTO `ubigeo_inei` VALUES (1674, '18', '02', '03', 'COALAQUE');
INSERT INTO `ubigeo_inei` VALUES (1675, '18', '02', '04', 'ICHUÑA');
INSERT INTO `ubigeo_inei` VALUES (1676, '18', '02', '05', 'LA CAPILLA');
INSERT INTO `ubigeo_inei` VALUES (1677, '18', '02', '06', 'LLOQUE');
INSERT INTO `ubigeo_inei` VALUES (1678, '18', '02', '07', 'MATALAQUE');
INSERT INTO `ubigeo_inei` VALUES (1679, '18', '02', '08', 'PUQUINA');
INSERT INTO `ubigeo_inei` VALUES (1680, '18', '02', '09', 'QUINISTAQUILLAS');
INSERT INTO `ubigeo_inei` VALUES (1681, '18', '02', '10', 'UBINAS');
INSERT INTO `ubigeo_inei` VALUES (1682, '18', '02', '11', 'YUNGA');
INSERT INTO `ubigeo_inei` VALUES (1683, '18', '03', '00', 'ILO');
INSERT INTO `ubigeo_inei` VALUES (1684, '18', '03', '01', 'ILO');
INSERT INTO `ubigeo_inei` VALUES (1685, '18', '03', '02', 'EL ALGARROBAL');
INSERT INTO `ubigeo_inei` VALUES (1686, '18', '03', '03', 'PACOCHA');
INSERT INTO `ubigeo_inei` VALUES (1687, '19', '00', '00', 'PASCO');
INSERT INTO `ubigeo_inei` VALUES (1688, '19', '01', '00', 'PASCO');
INSERT INTO `ubigeo_inei` VALUES (1689, '19', '01', '01', 'CHAUPIMARCA');
INSERT INTO `ubigeo_inei` VALUES (1690, '19', '01', '02', 'HUACHON');
INSERT INTO `ubigeo_inei` VALUES (1691, '19', '01', '03', 'HUARIACA');
INSERT INTO `ubigeo_inei` VALUES (1692, '19', '01', '04', 'HUAYLLAY');
INSERT INTO `ubigeo_inei` VALUES (1693, '19', '01', '05', 'NINACACA');
INSERT INTO `ubigeo_inei` VALUES (1694, '19', '01', '06', 'PALLANCHACRA');
INSERT INTO `ubigeo_inei` VALUES (1695, '19', '01', '07', 'PAUCARTAMBO');
INSERT INTO `ubigeo_inei` VALUES (1696, '19', '01', '08', 'SAN FCO. DE ASÍS DE YARUSYACÁN');
INSERT INTO `ubigeo_inei` VALUES (1697, '19', '01', '09', 'SIMON BOLIVAR');
INSERT INTO `ubigeo_inei` VALUES (1698, '19', '01', '10', 'TICLACAYAN');
INSERT INTO `ubigeo_inei` VALUES (1699, '19', '01', '11', 'TINYAHUARCO');
INSERT INTO `ubigeo_inei` VALUES (1700, '19', '01', '12', 'VICCO');
INSERT INTO `ubigeo_inei` VALUES (1701, '19', '01', '13', 'YANACANCHA');
INSERT INTO `ubigeo_inei` VALUES (1702, '19', '02', '00', 'DANIEL ALCIDES CARRION');
INSERT INTO `ubigeo_inei` VALUES (1703, '19', '02', '01', 'YANAHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1704, '19', '02', '02', 'CHACAYAN');
INSERT INTO `ubigeo_inei` VALUES (1705, '19', '02', '03', 'GOYLLARISQUIZGA');
INSERT INTO `ubigeo_inei` VALUES (1706, '19', '02', '04', 'PAUCAR');
INSERT INTO `ubigeo_inei` VALUES (1707, '19', '02', '05', 'SAN PEDRO DE PILLAO');
INSERT INTO `ubigeo_inei` VALUES (1708, '19', '02', '06', 'SANTA ANA DE TUSI');
INSERT INTO `ubigeo_inei` VALUES (1709, '19', '02', '07', 'TAPUC');
INSERT INTO `ubigeo_inei` VALUES (1710, '19', '02', '08', 'VILCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1711, '19', '03', '00', 'OXAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1712, '19', '03', '01', 'OXAPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1713, '19', '03', '02', 'CHONTABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1714, '19', '03', '03', 'HUANCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1715, '19', '03', '04', 'PALCAZU');
INSERT INTO `ubigeo_inei` VALUES (1716, '19', '03', '05', 'POZUZO');
INSERT INTO `ubigeo_inei` VALUES (1717, '19', '03', '06', 'PUERTO BERMUDEZ');
INSERT INTO `ubigeo_inei` VALUES (1718, '19', '03', '07', 'VILLA RICA');
INSERT INTO `ubigeo_inei` VALUES (1719, '19', '03', '08', 'CONSTITUCION');
INSERT INTO `ubigeo_inei` VALUES (1720, '20', '00', '00', 'PIURA');
INSERT INTO `ubigeo_inei` VALUES (1721, '20', '01', '00', 'PIURA');
INSERT INTO `ubigeo_inei` VALUES (1722, '20', '01', '01', 'PIURA');
INSERT INTO `ubigeo_inei` VALUES (1723, '20', '01', '04', 'CASTILLA');
INSERT INTO `ubigeo_inei` VALUES (1724, '20', '01', '05', 'CATACAOS');
INSERT INTO `ubigeo_inei` VALUES (1725, '20', '01', '07', 'CURA MORI');
INSERT INTO `ubigeo_inei` VALUES (1726, '20', '01', '08', 'EL TALLAN');
INSERT INTO `ubigeo_inei` VALUES (1727, '20', '01', '09', 'LA ARENA');
INSERT INTO `ubigeo_inei` VALUES (1728, '20', '01', '10', 'LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1729, '20', '01', '11', 'LAS LOMAS');
INSERT INTO `ubigeo_inei` VALUES (1730, '20', '01', '14', 'TAMBO GRANDE');
INSERT INTO `ubigeo_inei` VALUES (1731, '20', '01', '15', 'VEINTISÉIS DE OCTUBRE');
INSERT INTO `ubigeo_inei` VALUES (1732, '20', '02', '00', 'AYABACA');
INSERT INTO `ubigeo_inei` VALUES (1733, '20', '02', '01', 'AYABACA');
INSERT INTO `ubigeo_inei` VALUES (1734, '20', '02', '02', 'FRIAS');
INSERT INTO `ubigeo_inei` VALUES (1735, '20', '02', '03', 'JILILI');
INSERT INTO `ubigeo_inei` VALUES (1736, '20', '02', '04', 'LAGUNAS');
INSERT INTO `ubigeo_inei` VALUES (1737, '20', '02', '05', 'MONTERO');
INSERT INTO `ubigeo_inei` VALUES (1738, '20', '02', '06', 'PACAIPAMPA');
INSERT INTO `ubigeo_inei` VALUES (1739, '20', '02', '07', 'PAIMAS');
INSERT INTO `ubigeo_inei` VALUES (1740, '20', '02', '08', 'SAPILLICA');
INSERT INTO `ubigeo_inei` VALUES (1741, '20', '02', '09', 'SICCHEZ');
INSERT INTO `ubigeo_inei` VALUES (1742, '20', '02', '10', 'SUYO');
INSERT INTO `ubigeo_inei` VALUES (1743, '20', '03', '00', 'HUANCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1744, '20', '03', '01', 'HUANCABAMBA');
INSERT INTO `ubigeo_inei` VALUES (1745, '20', '03', '02', 'CANCHAQUE');
INSERT INTO `ubigeo_inei` VALUES (1746, '20', '03', '03', 'EL CARMEN DE LA FRONTERA');
INSERT INTO `ubigeo_inei` VALUES (1747, '20', '03', '04', 'HUARMACA');
INSERT INTO `ubigeo_inei` VALUES (1748, '20', '03', '05', 'LALAQUIZ');
INSERT INTO `ubigeo_inei` VALUES (1749, '20', '03', '06', 'SAN MIGUEL DE EL FAIQUE');
INSERT INTO `ubigeo_inei` VALUES (1750, '20', '03', '07', 'SONDOR');
INSERT INTO `ubigeo_inei` VALUES (1751, '20', '03', '08', 'SONDORILLO');
INSERT INTO `ubigeo_inei` VALUES (1752, '20', '04', '00', 'MORROPON');
INSERT INTO `ubigeo_inei` VALUES (1753, '20', '04', '01', 'CHULUCANAS');
INSERT INTO `ubigeo_inei` VALUES (1754, '20', '04', '02', 'BUENOS AIRES');
INSERT INTO `ubigeo_inei` VALUES (1755, '20', '04', '03', 'CHALACO');
INSERT INTO `ubigeo_inei` VALUES (1756, '20', '04', '04', 'LA MATANZA');
INSERT INTO `ubigeo_inei` VALUES (1757, '20', '04', '05', 'MORROPON');
INSERT INTO `ubigeo_inei` VALUES (1758, '20', '04', '06', 'SALITRAL');
INSERT INTO `ubigeo_inei` VALUES (1759, '20', '04', '07', 'SAN JUAN DE BIGOTE');
INSERT INTO `ubigeo_inei` VALUES (1760, '20', '04', '08', 'SANTA CATALINA DE MOSSA');
INSERT INTO `ubigeo_inei` VALUES (1761, '20', '04', '09', 'SANTO DOMINGO');
INSERT INTO `ubigeo_inei` VALUES (1762, '20', '04', '10', 'YAMANGO');
INSERT INTO `ubigeo_inei` VALUES (1763, '20', '05', '00', 'PAITA');
INSERT INTO `ubigeo_inei` VALUES (1764, '20', '05', '01', 'PAITA');
INSERT INTO `ubigeo_inei` VALUES (1765, '20', '05', '02', 'AMOTAPE');
INSERT INTO `ubigeo_inei` VALUES (1766, '20', '05', '03', 'ARENAL');
INSERT INTO `ubigeo_inei` VALUES (1767, '20', '05', '04', 'COLAN');
INSERT INTO `ubigeo_inei` VALUES (1768, '20', '05', '05', 'LA HUACA');
INSERT INTO `ubigeo_inei` VALUES (1769, '20', '05', '06', 'TAMARINDO');
INSERT INTO `ubigeo_inei` VALUES (1770, '20', '05', '07', 'VICHAYAL');
INSERT INTO `ubigeo_inei` VALUES (1771, '20', '06', '00', 'SULLANA');
INSERT INTO `ubigeo_inei` VALUES (1772, '20', '06', '01', 'SULLANA');
INSERT INTO `ubigeo_inei` VALUES (1773, '20', '06', '02', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1774, '20', '06', '03', 'IGNACIO ESCUDERO');
INSERT INTO `ubigeo_inei` VALUES (1775, '20', '06', '04', 'LANCONES');
INSERT INTO `ubigeo_inei` VALUES (1776, '20', '06', '05', 'MARCAVELICA');
INSERT INTO `ubigeo_inei` VALUES (1777, '20', '06', '06', 'MIGUEL CHECA');
INSERT INTO `ubigeo_inei` VALUES (1778, '20', '06', '07', 'QUERECOTILLO');
INSERT INTO `ubigeo_inei` VALUES (1779, '20', '06', '08', 'SALITRAL');
INSERT INTO `ubigeo_inei` VALUES (1780, '20', '07', '00', 'TALARA');
INSERT INTO `ubigeo_inei` VALUES (1781, '20', '07', '01', 'PARIÑAS');
INSERT INTO `ubigeo_inei` VALUES (1782, '20', '07', '02', 'EL ALTO');
INSERT INTO `ubigeo_inei` VALUES (1783, '20', '07', '03', 'LA BREA');
INSERT INTO `ubigeo_inei` VALUES (1784, '20', '07', '04', 'LOBITOS');
INSERT INTO `ubigeo_inei` VALUES (1785, '20', '07', '05', 'LOS ORGANOS');
INSERT INTO `ubigeo_inei` VALUES (1786, '20', '07', '06', 'MANCORA');
INSERT INTO `ubigeo_inei` VALUES (1787, '20', '08', '00', 'SECHURA');
INSERT INTO `ubigeo_inei` VALUES (1788, '20', '08', '01', 'SECHURA');
INSERT INTO `ubigeo_inei` VALUES (1789, '20', '08', '02', 'BELLAVISTA DE LA UNION');
INSERT INTO `ubigeo_inei` VALUES (1790, '20', '08', '03', 'BERNAL');
INSERT INTO `ubigeo_inei` VALUES (1791, '20', '08', '04', 'CRISTO NOS VALGA');
INSERT INTO `ubigeo_inei` VALUES (1792, '20', '08', '05', 'VICE');
INSERT INTO `ubigeo_inei` VALUES (1793, '20', '08', '06', 'RINCONADA LLICUAR');
INSERT INTO `ubigeo_inei` VALUES (1794, '21', '00', '00', 'PUNO');
INSERT INTO `ubigeo_inei` VALUES (1795, '21', '01', '00', 'PUNO');
INSERT INTO `ubigeo_inei` VALUES (1796, '21', '01', '01', 'PUNO');
INSERT INTO `ubigeo_inei` VALUES (1797, '21', '01', '02', 'ACORA');
INSERT INTO `ubigeo_inei` VALUES (1798, '21', '01', '03', 'AMANTANI');
INSERT INTO `ubigeo_inei` VALUES (1799, '21', '01', '04', 'ATUNCOLLA');
INSERT INTO `ubigeo_inei` VALUES (1800, '21', '01', '05', 'CAPACHICA');
INSERT INTO `ubigeo_inei` VALUES (1801, '21', '01', '06', 'CHUCUITO');
INSERT INTO `ubigeo_inei` VALUES (1802, '21', '01', '07', 'COATA');
INSERT INTO `ubigeo_inei` VALUES (1803, '21', '01', '08', 'HUATA');
INSERT INTO `ubigeo_inei` VALUES (1804, '21', '01', '09', 'MAÑAZO');
INSERT INTO `ubigeo_inei` VALUES (1805, '21', '01', '10', 'PAUCARCOLLA');
INSERT INTO `ubigeo_inei` VALUES (1806, '21', '01', '11', 'PICHACANI');
INSERT INTO `ubigeo_inei` VALUES (1807, '21', '01', '12', 'PLATERIA');
INSERT INTO `ubigeo_inei` VALUES (1808, '21', '01', '13', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1809, '21', '01', '14', 'TIQUILLACA');
INSERT INTO `ubigeo_inei` VALUES (1810, '21', '01', '15', 'VILQUE');
INSERT INTO `ubigeo_inei` VALUES (1811, '21', '02', '00', 'AZANGARO');
INSERT INTO `ubigeo_inei` VALUES (1812, '21', '02', '01', 'AZANGARO');
INSERT INTO `ubigeo_inei` VALUES (1813, '21', '02', '02', 'ACHAYA');
INSERT INTO `ubigeo_inei` VALUES (1814, '21', '02', '03', 'ARAPA');
INSERT INTO `ubigeo_inei` VALUES (1815, '21', '02', '04', 'ASILLO');
INSERT INTO `ubigeo_inei` VALUES (1816, '21', '02', '05', 'CAMINACA');
INSERT INTO `ubigeo_inei` VALUES (1817, '21', '02', '06', 'CHUPA');
INSERT INTO `ubigeo_inei` VALUES (1818, '21', '02', '07', 'JOSE DOMINGO CHOQUEHUANCA');
INSERT INTO `ubigeo_inei` VALUES (1819, '21', '02', '08', 'MUÑANI');
INSERT INTO `ubigeo_inei` VALUES (1820, '21', '02', '09', 'POTONI');
INSERT INTO `ubigeo_inei` VALUES (1821, '21', '02', '10', 'SAMAN');
INSERT INTO `ubigeo_inei` VALUES (1822, '21', '02', '11', 'SAN ANTON');
INSERT INTO `ubigeo_inei` VALUES (1823, '21', '02', '12', 'SAN JOSE');
INSERT INTO `ubigeo_inei` VALUES (1824, '21', '02', '13', 'SAN JUAN DE SALINAS');
INSERT INTO `ubigeo_inei` VALUES (1825, '21', '02', '14', 'SANTIAGO DE PUPUJA');
INSERT INTO `ubigeo_inei` VALUES (1826, '21', '02', '15', 'TIRAPATA');
INSERT INTO `ubigeo_inei` VALUES (1827, '21', '03', '00', 'CARABAYA');
INSERT INTO `ubigeo_inei` VALUES (1828, '21', '03', '01', 'MACUSANI');
INSERT INTO `ubigeo_inei` VALUES (1829, '21', '03', '02', 'AJOYANI');
INSERT INTO `ubigeo_inei` VALUES (1830, '21', '03', '03', 'AYAPATA');
INSERT INTO `ubigeo_inei` VALUES (1831, '21', '03', '04', 'COASA');
INSERT INTO `ubigeo_inei` VALUES (1832, '21', '03', '05', 'CORANI');
INSERT INTO `ubigeo_inei` VALUES (1833, '21', '03', '06', 'CRUCERO');
INSERT INTO `ubigeo_inei` VALUES (1834, '21', '03', '07', 'ITUATA');
INSERT INTO `ubigeo_inei` VALUES (1835, '21', '03', '08', 'OLLACHEA');
INSERT INTO `ubigeo_inei` VALUES (1836, '21', '03', '09', 'SAN GABAN');
INSERT INTO `ubigeo_inei` VALUES (1837, '21', '03', '10', 'USICAYOS');
INSERT INTO `ubigeo_inei` VALUES (1838, '21', '04', '00', 'CHUCUITO');
INSERT INTO `ubigeo_inei` VALUES (1839, '21', '04', '01', 'JULI');
INSERT INTO `ubigeo_inei` VALUES (1840, '21', '04', '02', 'DESAGUADERO');
INSERT INTO `ubigeo_inei` VALUES (1841, '21', '04', '03', 'HUACULLANI');
INSERT INTO `ubigeo_inei` VALUES (1842, '21', '04', '04', 'KELLUYO');
INSERT INTO `ubigeo_inei` VALUES (1843, '21', '04', '05', 'PISACOMA');
INSERT INTO `ubigeo_inei` VALUES (1844, '21', '04', '06', 'POMATA');
INSERT INTO `ubigeo_inei` VALUES (1845, '21', '04', '07', 'ZEPITA');
INSERT INTO `ubigeo_inei` VALUES (1846, '21', '05', '00', 'EL COLLAO');
INSERT INTO `ubigeo_inei` VALUES (1847, '21', '05', '01', 'ILAVE');
INSERT INTO `ubigeo_inei` VALUES (1848, '21', '05', '02', 'CAPASO');
INSERT INTO `ubigeo_inei` VALUES (1849, '21', '05', '03', 'PILCUYO');
INSERT INTO `ubigeo_inei` VALUES (1850, '21', '05', '04', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1851, '21', '05', '05', 'CONDURIRI');
INSERT INTO `ubigeo_inei` VALUES (1852, '21', '06', '00', 'HUANCANE');
INSERT INTO `ubigeo_inei` VALUES (1853, '21', '06', '01', 'HUANCANE');
INSERT INTO `ubigeo_inei` VALUES (1854, '21', '06', '02', 'COJATA');
INSERT INTO `ubigeo_inei` VALUES (1855, '21', '06', '03', 'HUATASANI');
INSERT INTO `ubigeo_inei` VALUES (1856, '21', '06', '04', 'INCHUPALLA');
INSERT INTO `ubigeo_inei` VALUES (1857, '21', '06', '05', 'PUSI');
INSERT INTO `ubigeo_inei` VALUES (1858, '21', '06', '06', 'ROSASPATA');
INSERT INTO `ubigeo_inei` VALUES (1859, '21', '06', '07', 'TARACO');
INSERT INTO `ubigeo_inei` VALUES (1860, '21', '06', '08', 'VILQUE CHICO');
INSERT INTO `ubigeo_inei` VALUES (1861, '21', '07', '00', 'LAMPA');
INSERT INTO `ubigeo_inei` VALUES (1862, '21', '07', '01', 'LAMPA');
INSERT INTO `ubigeo_inei` VALUES (1863, '21', '07', '02', 'CABANILLA');
INSERT INTO `ubigeo_inei` VALUES (1864, '21', '07', '03', 'CALAPUJA');
INSERT INTO `ubigeo_inei` VALUES (1865, '21', '07', '04', 'NICASIO');
INSERT INTO `ubigeo_inei` VALUES (1866, '21', '07', '05', 'OCUVIRI');
INSERT INTO `ubigeo_inei` VALUES (1867, '21', '07', '06', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (1868, '21', '07', '07', 'PARATIA');
INSERT INTO `ubigeo_inei` VALUES (1869, '21', '07', '08', 'PUCARA');
INSERT INTO `ubigeo_inei` VALUES (1870, '21', '07', '09', 'SANTA LUCIA');
INSERT INTO `ubigeo_inei` VALUES (1871, '21', '07', '10', 'VILAVILA');
INSERT INTO `ubigeo_inei` VALUES (1872, '21', '08', '00', 'MELGAR');
INSERT INTO `ubigeo_inei` VALUES (1873, '21', '08', '01', 'AYAVIRI');
INSERT INTO `ubigeo_inei` VALUES (1874, '21', '08', '02', 'ANTAUTA');
INSERT INTO `ubigeo_inei` VALUES (1875, '21', '08', '03', 'CUPI');
INSERT INTO `ubigeo_inei` VALUES (1876, '21', '08', '04', 'LLALLI');
INSERT INTO `ubigeo_inei` VALUES (1877, '21', '08', '05', 'MACARI');
INSERT INTO `ubigeo_inei` VALUES (1878, '21', '08', '06', 'NUÑOA');
INSERT INTO `ubigeo_inei` VALUES (1879, '21', '08', '07', 'ORURILLO');
INSERT INTO `ubigeo_inei` VALUES (1880, '21', '08', '08', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1881, '21', '08', '09', 'UMACHIRI');
INSERT INTO `ubigeo_inei` VALUES (1882, '21', '09', '00', 'MOHO');
INSERT INTO `ubigeo_inei` VALUES (1883, '21', '09', '01', 'MOHO');
INSERT INTO `ubigeo_inei` VALUES (1884, '21', '09', '02', 'CONIMA');
INSERT INTO `ubigeo_inei` VALUES (1885, '21', '09', '03', 'HUAYRAPATA');
INSERT INTO `ubigeo_inei` VALUES (1886, '21', '09', '04', 'TILALI');
INSERT INTO `ubigeo_inei` VALUES (1887, '21', '10', '00', 'SAN ANTONIO DE PUTINA');
INSERT INTO `ubigeo_inei` VALUES (1888, '21', '10', '01', 'PUTINA');
INSERT INTO `ubigeo_inei` VALUES (1889, '21', '10', '02', 'ANANEA');
INSERT INTO `ubigeo_inei` VALUES (1890, '21', '10', '03', 'PEDRO VILCA APAZA');
INSERT INTO `ubigeo_inei` VALUES (1891, '21', '10', '04', 'QUILCAPUNCU');
INSERT INTO `ubigeo_inei` VALUES (1892, '21', '10', '05', 'SINA');
INSERT INTO `ubigeo_inei` VALUES (1893, '21', '11', '00', 'SAN ROMAN');
INSERT INTO `ubigeo_inei` VALUES (1894, '21', '11', '01', 'JULIACA');
INSERT INTO `ubigeo_inei` VALUES (1895, '21', '11', '02', 'CABANA');
INSERT INTO `ubigeo_inei` VALUES (1896, '21', '11', '03', 'CABANILLAS');
INSERT INTO `ubigeo_inei` VALUES (1897, '21', '11', '04', 'CARACOTO');
INSERT INTO `ubigeo_inei` VALUES (1898, '21', '12', '00', 'SANDIA');
INSERT INTO `ubigeo_inei` VALUES (1899, '21', '12', '01', 'SANDIA');
INSERT INTO `ubigeo_inei` VALUES (1900, '21', '12', '02', 'CUYOCUYO');
INSERT INTO `ubigeo_inei` VALUES (1901, '21', '12', '03', 'LIMBANI');
INSERT INTO `ubigeo_inei` VALUES (1902, '21', '12', '04', 'PATAMBUCO');
INSERT INTO `ubigeo_inei` VALUES (1903, '21', '12', '05', 'PHARA');
INSERT INTO `ubigeo_inei` VALUES (1904, '21', '12', '06', 'QUIACA');
INSERT INTO `ubigeo_inei` VALUES (1905, '21', '12', '07', 'SAN JUAN DEL ORO');
INSERT INTO `ubigeo_inei` VALUES (1906, '21', '12', '08', 'YANAHUAYA');
INSERT INTO `ubigeo_inei` VALUES (1907, '21', '12', '09', 'ALTO INAMBARI');
INSERT INTO `ubigeo_inei` VALUES (1908, '21', '12', '10', 'SAN PEDRO DE PUTINA PUNCO');
INSERT INTO `ubigeo_inei` VALUES (1909, '21', '13', '00', 'YUNGUYO');
INSERT INTO `ubigeo_inei` VALUES (1910, '21', '13', '01', 'YUNGUYO');
INSERT INTO `ubigeo_inei` VALUES (1911, '21', '13', '02', 'ANAPIA');
INSERT INTO `ubigeo_inei` VALUES (1912, '21', '13', '03', 'COPANI');
INSERT INTO `ubigeo_inei` VALUES (1913, '21', '13', '04', 'CUTURAPI');
INSERT INTO `ubigeo_inei` VALUES (1914, '21', '13', '05', 'OLLARAYA');
INSERT INTO `ubigeo_inei` VALUES (1915, '21', '13', '06', 'TINICACHI');
INSERT INTO `ubigeo_inei` VALUES (1916, '21', '13', '07', 'UNICACHI');
INSERT INTO `ubigeo_inei` VALUES (1917, '22', '00', '00', 'SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1918, '22', '01', '00', 'MOYOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1919, '22', '01', '01', 'MOYOBAMBA');
INSERT INTO `ubigeo_inei` VALUES (1920, '22', '01', '02', 'CALZADA');
INSERT INTO `ubigeo_inei` VALUES (1921, '22', '01', '03', 'HABANA');
INSERT INTO `ubigeo_inei` VALUES (1922, '22', '01', '04', 'JEPELACIO');
INSERT INTO `ubigeo_inei` VALUES (1923, '22', '01', '05', 'SORITOR');
INSERT INTO `ubigeo_inei` VALUES (1924, '22', '01', '06', 'YANTALO');
INSERT INTO `ubigeo_inei` VALUES (1925, '22', '02', '00', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1926, '22', '02', '01', 'BELLAVISTA');
INSERT INTO `ubigeo_inei` VALUES (1927, '22', '02', '02', 'ALTO BIAVO');
INSERT INTO `ubigeo_inei` VALUES (1928, '22', '02', '03', 'BAJO BIAVO');
INSERT INTO `ubigeo_inei` VALUES (1929, '22', '02', '04', 'HUALLAGA');
INSERT INTO `ubigeo_inei` VALUES (1930, '22', '02', '05', 'SAN PABLO');
INSERT INTO `ubigeo_inei` VALUES (1931, '22', '02', '06', 'SAN RAFAEL');
INSERT INTO `ubigeo_inei` VALUES (1932, '22', '03', '00', 'EL DORADO');
INSERT INTO `ubigeo_inei` VALUES (1933, '22', '03', '01', 'SAN JOSE DE SISA');
INSERT INTO `ubigeo_inei` VALUES (1934, '22', '03', '02', 'AGUA BLANCA');
INSERT INTO `ubigeo_inei` VALUES (1935, '22', '03', '03', 'SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1936, '22', '03', '04', 'SANTA ROSA');
INSERT INTO `ubigeo_inei` VALUES (1937, '22', '03', '05', 'SHATOJA');
INSERT INTO `ubigeo_inei` VALUES (1938, '22', '04', '00', 'HUALLAGA');
INSERT INTO `ubigeo_inei` VALUES (1939, '22', '04', '01', 'SAPOSOA');
INSERT INTO `ubigeo_inei` VALUES (1940, '22', '04', '02', 'ALTO SAPOSOA');
INSERT INTO `ubigeo_inei` VALUES (1941, '22', '04', '03', 'EL ESLABON');
INSERT INTO `ubigeo_inei` VALUES (1942, '22', '04', '04', 'PISCOYACU');
INSERT INTO `ubigeo_inei` VALUES (1943, '22', '04', '05', 'SACANCHE');
INSERT INTO `ubigeo_inei` VALUES (1944, '22', '04', '06', 'TINGO DE SAPOSOA');
INSERT INTO `ubigeo_inei` VALUES (1945, '22', '05', '00', 'LAMAS');
INSERT INTO `ubigeo_inei` VALUES (1946, '22', '05', '01', 'LAMAS');
INSERT INTO `ubigeo_inei` VALUES (1947, '22', '05', '02', 'ALONSO DE ALVARADO');
INSERT INTO `ubigeo_inei` VALUES (1948, '22', '05', '03', 'BARRANQUITA');
INSERT INTO `ubigeo_inei` VALUES (1949, '22', '05', '04', 'CAYNARACHI');
INSERT INTO `ubigeo_inei` VALUES (1950, '22', '05', '05', 'CUÑUMBUQUI');
INSERT INTO `ubigeo_inei` VALUES (1951, '22', '05', '06', 'PINTO RECODO');
INSERT INTO `ubigeo_inei` VALUES (1952, '22', '05', '07', 'RUMISAPA');
INSERT INTO `ubigeo_inei` VALUES (1953, '22', '05', '08', 'SAN ROQUE DE CUMBAZA');
INSERT INTO `ubigeo_inei` VALUES (1954, '22', '05', '09', 'SHANAO');
INSERT INTO `ubigeo_inei` VALUES (1955, '22', '05', '10', 'TABALOSOS');
INSERT INTO `ubigeo_inei` VALUES (1956, '22', '05', '11', 'ZAPATERO');
INSERT INTO `ubigeo_inei` VALUES (1957, '22', '06', '00', 'MARISCAL CACERES');
INSERT INTO `ubigeo_inei` VALUES (1958, '22', '06', '01', 'JUANJUI');
INSERT INTO `ubigeo_inei` VALUES (1959, '22', '06', '02', 'CAMPANILLA');
INSERT INTO `ubigeo_inei` VALUES (1960, '22', '06', '03', 'HUICUNGO');
INSERT INTO `ubigeo_inei` VALUES (1961, '22', '06', '04', 'PACHIZA');
INSERT INTO `ubigeo_inei` VALUES (1962, '22', '06', '05', 'PAJARILLO');
INSERT INTO `ubigeo_inei` VALUES (1963, '22', '07', '00', 'PICOTA');
INSERT INTO `ubigeo_inei` VALUES (1964, '22', '07', '01', 'PICOTA');
INSERT INTO `ubigeo_inei` VALUES (1965, '22', '07', '02', 'BUENOS AIRES');
INSERT INTO `ubigeo_inei` VALUES (1966, '22', '07', '03', 'CASPISAPA');
INSERT INTO `ubigeo_inei` VALUES (1967, '22', '07', '04', 'PILLUANA');
INSERT INTO `ubigeo_inei` VALUES (1968, '22', '07', '05', 'PUCACACA');
INSERT INTO `ubigeo_inei` VALUES (1969, '22', '07', '06', 'SAN CRISTOBAL');
INSERT INTO `ubigeo_inei` VALUES (1970, '22', '07', '07', 'SAN HILARION');
INSERT INTO `ubigeo_inei` VALUES (1971, '22', '07', '08', 'SHAMBOYACU');
INSERT INTO `ubigeo_inei` VALUES (1972, '22', '07', '09', 'TINGO DE PONASA');
INSERT INTO `ubigeo_inei` VALUES (1973, '22', '07', '10', 'TRES UNIDOS');
INSERT INTO `ubigeo_inei` VALUES (1974, '22', '08', '00', 'RIOJA');
INSERT INTO `ubigeo_inei` VALUES (1975, '22', '08', '01', 'RIOJA');
INSERT INTO `ubigeo_inei` VALUES (1976, '22', '08', '02', 'AWAJUN');
INSERT INTO `ubigeo_inei` VALUES (1977, '22', '08', '03', 'ELIAS SOPLIN VARGAS');
INSERT INTO `ubigeo_inei` VALUES (1978, '22', '08', '04', 'NUEVA CAJAMARCA');
INSERT INTO `ubigeo_inei` VALUES (1979, '22', '08', '05', 'PARDO MIGUEL');
INSERT INTO `ubigeo_inei` VALUES (1980, '22', '08', '06', 'POSIC');
INSERT INTO `ubigeo_inei` VALUES (1981, '22', '08', '07', 'SAN FERNANDO');
INSERT INTO `ubigeo_inei` VALUES (1982, '22', '08', '08', 'YORONGOS');
INSERT INTO `ubigeo_inei` VALUES (1983, '22', '08', '09', 'YURACYACU');
INSERT INTO `ubigeo_inei` VALUES (1984, '22', '09', '00', 'SAN MARTIN');
INSERT INTO `ubigeo_inei` VALUES (1985, '22', '09', '01', 'TARAPOTO');
INSERT INTO `ubigeo_inei` VALUES (1986, '22', '09', '02', 'ALBERTO LEVEAU');
INSERT INTO `ubigeo_inei` VALUES (1987, '22', '09', '03', 'CACATACHI');
INSERT INTO `ubigeo_inei` VALUES (1988, '22', '09', '04', 'CHAZUTA');
INSERT INTO `ubigeo_inei` VALUES (1989, '22', '09', '05', 'CHIPURANA');
INSERT INTO `ubigeo_inei` VALUES (1990, '22', '09', '06', 'EL PORVENIR');
INSERT INTO `ubigeo_inei` VALUES (1991, '22', '09', '07', 'HUIMBAYOC');
INSERT INTO `ubigeo_inei` VALUES (1992, '22', '09', '08', 'JUAN GUERRA');
INSERT INTO `ubigeo_inei` VALUES (1993, '22', '09', '09', 'LA BANDA DE SHILCAYO');
INSERT INTO `ubigeo_inei` VALUES (1994, '22', '09', '10', 'MORALES');
INSERT INTO `ubigeo_inei` VALUES (1995, '22', '09', '11', 'PAPAPLAYA');
INSERT INTO `ubigeo_inei` VALUES (1996, '22', '09', '12', 'SAN ANTONIO');
INSERT INTO `ubigeo_inei` VALUES (1997, '22', '09', '13', 'SAUCE');
INSERT INTO `ubigeo_inei` VALUES (1998, '22', '09', '14', 'SHAPAJA');
INSERT INTO `ubigeo_inei` VALUES (1999, '22', '10', '00', 'TOCACHE');
INSERT INTO `ubigeo_inei` VALUES (2000, '22', '10', '01', 'TOCACHE');
INSERT INTO `ubigeo_inei` VALUES (2001, '22', '10', '02', 'NUEVO PROGRESO');
INSERT INTO `ubigeo_inei` VALUES (2002, '22', '10', '03', 'POLVORA');
INSERT INTO `ubigeo_inei` VALUES (2003, '22', '10', '04', 'SHUNTE');
INSERT INTO `ubigeo_inei` VALUES (2004, '22', '10', '05', 'UCHIZA');
INSERT INTO `ubigeo_inei` VALUES (2005, '23', '00', '00', 'TACNA');
INSERT INTO `ubigeo_inei` VALUES (2006, '23', '01', '00', 'TACNA');
INSERT INTO `ubigeo_inei` VALUES (2007, '23', '01', '01', 'TACNA');
INSERT INTO `ubigeo_inei` VALUES (2008, '23', '01', '02', 'ALTO DE LA ALIANZA');
INSERT INTO `ubigeo_inei` VALUES (2009, '23', '01', '03', 'CALANA');
INSERT INTO `ubigeo_inei` VALUES (2010, '23', '01', '04', 'CIUDAD NUEVA');
INSERT INTO `ubigeo_inei` VALUES (2011, '23', '01', '05', 'INCLAN');
INSERT INTO `ubigeo_inei` VALUES (2012, '23', '01', '06', 'PACHIA');
INSERT INTO `ubigeo_inei` VALUES (2013, '23', '01', '07', 'PALCA');
INSERT INTO `ubigeo_inei` VALUES (2014, '23', '01', '08', 'POCOLLAY');
INSERT INTO `ubigeo_inei` VALUES (2015, '23', '01', '09', 'SAMA');
INSERT INTO `ubigeo_inei` VALUES (2016, '23', '01', '10', 'CORONEL GREGORIO ALBARRACÍN L');
INSERT INTO `ubigeo_inei` VALUES (2017, '23', '02', '00', 'CANDARAVE');
INSERT INTO `ubigeo_inei` VALUES (2018, '23', '02', '01', 'CANDARAVE');
INSERT INTO `ubigeo_inei` VALUES (2019, '23', '02', '02', 'CAIRANI');
INSERT INTO `ubigeo_inei` VALUES (2020, '23', '02', '03', 'CAMILACA');
INSERT INTO `ubigeo_inei` VALUES (2021, '23', '02', '04', 'CURIBAYA');
INSERT INTO `ubigeo_inei` VALUES (2022, '23', '02', '05', 'HUANUARA');
INSERT INTO `ubigeo_inei` VALUES (2023, '23', '02', '06', 'QUILAHUANI');
INSERT INTO `ubigeo_inei` VALUES (2024, '23', '03', '00', 'JORGE BASADRE');
INSERT INTO `ubigeo_inei` VALUES (2025, '23', '03', '01', 'LOCUMBA');
INSERT INTO `ubigeo_inei` VALUES (2026, '23', '03', '02', 'ILABAYA');
INSERT INTO `ubigeo_inei` VALUES (2027, '23', '03', '03', 'ITE');
INSERT INTO `ubigeo_inei` VALUES (2028, '23', '04', '00', 'TARATA');
INSERT INTO `ubigeo_inei` VALUES (2029, '23', '04', '01', 'TARATA');
INSERT INTO `ubigeo_inei` VALUES (2030, '23', '04', '02', 'CHUCATAMANI');
INSERT INTO `ubigeo_inei` VALUES (2031, '23', '04', '03', 'ESTIQUE');
INSERT INTO `ubigeo_inei` VALUES (2032, '23', '04', '04', 'ESTIQUE-PAMPA');
INSERT INTO `ubigeo_inei` VALUES (2033, '23', '04', '05', 'SITAJARA');
INSERT INTO `ubigeo_inei` VALUES (2034, '23', '04', '06', 'SUSAPAYA');
INSERT INTO `ubigeo_inei` VALUES (2035, '23', '04', '07', 'TARUCACHI');
INSERT INTO `ubigeo_inei` VALUES (2036, '23', '04', '08', 'TICACO');
INSERT INTO `ubigeo_inei` VALUES (2037, '24', '00', '00', 'TUMBES');
INSERT INTO `ubigeo_inei` VALUES (2038, '24', '01', '00', 'TUMBES');
INSERT INTO `ubigeo_inei` VALUES (2039, '24', '01', '01', 'TUMBES');
INSERT INTO `ubigeo_inei` VALUES (2040, '24', '01', '02', 'CORRALES');
INSERT INTO `ubigeo_inei` VALUES (2041, '24', '01', '03', 'LA CRUZ');
INSERT INTO `ubigeo_inei` VALUES (2042, '24', '01', '04', 'PAMPAS DE HOSPITAL');
INSERT INTO `ubigeo_inei` VALUES (2043, '24', '01', '05', 'SAN JACINTO');
INSERT INTO `ubigeo_inei` VALUES (2044, '24', '01', '06', 'SAN JUAN DE LA VIRGEN');
INSERT INTO `ubigeo_inei` VALUES (2045, '24', '02', '00', 'CONTRALMIRANTE VILLAR');
INSERT INTO `ubigeo_inei` VALUES (2046, '24', '02', '01', 'ZORRITOS');
INSERT INTO `ubigeo_inei` VALUES (2047, '24', '02', '02', 'CASITAS');
INSERT INTO `ubigeo_inei` VALUES (2048, '24', '02', '03', 'CANOAS DE PUNTA SAL');
INSERT INTO `ubigeo_inei` VALUES (2049, '24', '03', '00', 'ZARUMILLA');
INSERT INTO `ubigeo_inei` VALUES (2050, '24', '03', '01', 'ZARUMILLA');
INSERT INTO `ubigeo_inei` VALUES (2051, '24', '03', '02', 'AGUAS VERDES');
INSERT INTO `ubigeo_inei` VALUES (2052, '24', '03', '03', 'MATAPALO');
INSERT INTO `ubigeo_inei` VALUES (2053, '24', '03', '04', 'PAPAYAL');
INSERT INTO `ubigeo_inei` VALUES (2054, '25', '00', '00', 'UCAYALI');
INSERT INTO `ubigeo_inei` VALUES (2055, '25', '01', '00', 'CORONEL PORTILLO');
INSERT INTO `ubigeo_inei` VALUES (2056, '25', '01', '01', 'CALLARIA');
INSERT INTO `ubigeo_inei` VALUES (2057, '25', '01', '02', 'CAMPOVERDE');
INSERT INTO `ubigeo_inei` VALUES (2058, '25', '01', '03', 'IPARIA');
INSERT INTO `ubigeo_inei` VALUES (2059, '25', '01', '04', 'MASISEA');
INSERT INTO `ubigeo_inei` VALUES (2060, '25', '01', '05', 'YARINACOCHA');
INSERT INTO `ubigeo_inei` VALUES (2061, '25', '01', '06', 'NUEVA REQUENA');
INSERT INTO `ubigeo_inei` VALUES (2062, '25', '01', '07', 'MANANTAY');
INSERT INTO `ubigeo_inei` VALUES (2063, '25', '02', '00', 'ATALAYA');
INSERT INTO `ubigeo_inei` VALUES (2064, '25', '02', '01', 'RAYMONDI');
INSERT INTO `ubigeo_inei` VALUES (2065, '25', '02', '02', 'SEPAHUA');
INSERT INTO `ubigeo_inei` VALUES (2066, '25', '02', '03', 'TAHUANIA');
INSERT INTO `ubigeo_inei` VALUES (2067, '25', '02', '04', 'YURUA');
INSERT INTO `ubigeo_inei` VALUES (2068, '25', '03', '00', 'PADRE ABAD');
INSERT INTO `ubigeo_inei` VALUES (2069, '25', '03', '01', 'PADRE ABAD');
INSERT INTO `ubigeo_inei` VALUES (2070, '25', '03', '02', 'IRAZOLA');
INSERT INTO `ubigeo_inei` VALUES (2071, '25', '03', '03', 'CURIMANA');
INSERT INTO `ubigeo_inei` VALUES (2072, '25', '04', '00', 'PURUS');
INSERT INTO `ubigeo_inei` VALUES (2073, '25', '04', '01', 'PURUS');
INSERT INTO `ubigeo_inei` VALUES (2074, '99', '00', '00', 'EXTRANJERO');
INSERT INTO `ubigeo_inei` VALUES (2075, '99', '99', '00', 'EXTRANJERO');
INSERT INTO `ubigeo_inei` VALUES (2076, '99', '99', '99', 'EXTRANJERO');

-- ----------------------------
-- Table structure for unidades
-- ----------------------------
DROP TABLE IF EXISTS `unidades`;
CREATE TABLE `unidades`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `creado_el` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 19 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of unidades
-- ----------------------------
INSERT INTO `unidades` VALUES (14, 'UNIDAD', '2024-12-26 12:35:12');

-- ----------------------------
-- Table structure for unidades_repuestos
-- ----------------------------
DROP TABLE IF EXISTS `unidades_repuestos`;
CREATE TABLE `unidades_repuestos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `creado_el` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 18 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of unidades_repuestos
-- ----------------------------
INSERT INTO `unidades_repuestos` VALUES (14, 'UNIDAD', '2024-12-26 20:35:12');
INSERT INTO `unidades_repuestos` VALUES (15, 'KILOGRAMO', '2024-12-26 20:35:27');
INSERT INTO `unidades_repuestos` VALUES (16, 'METRO', '2024-12-26 20:35:32');
INSERT INTO `unidades_repuestos` VALUES (17, 'MILIMETRO', '2025-03-06 20:20:18');

-- ----------------------------
-- Table structure for usuarios
-- ----------------------------
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios`  (
  `usuario_id` int NOT NULL AUTO_INCREMENT,
  `codigo` varchar(3) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NULL DEFAULT NULL,
  `id_rol` int NULL DEFAULT NULL,
  `num_doc` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `usuario` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `clave` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `email` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombres` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `apellidos` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `rubro` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `telefono` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `token_reset` varchar(130) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  `mensaje` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `rotativo` smallint NULL DEFAULT 0,
  `sueldo` decimal(10, 2) NULL DEFAULT 0.00,
  `foto_perfil` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL COMMENT 'Ruta de la foto de perfil del usuario',
  PRIMARY KEY (`usuario_id`) USING BTREE,
  UNIQUE INDEX `idx_codigo_usuario`(`codigo` ASC) USING BTREE,
  INDEX `id_empresa`(`id_empresa` ASC) USING BTREE,
  INDEX `id_rol`(`id_rol` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 66 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of usuarios
-- ----------------------------
INSERT INTO `usuarios` VALUES (40, '001', 12, 1, '70667182', 'admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'kiyotacka@gamil.com', 'Rodrigo Yarleque', NULL, NULL, 1, '993321920', NULL, '1', NULL, 0, 23.00, 'public/uploads/usuarios/usuario_1768914495_696f7e3f71106.png');
INSERT INTO `usuarios` VALUES (56, '002', 12, 0, '77425200', 'taller1', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'rodrigoyarleque7@gmail.com', 'EMER RODRIGO KURISHIMA AYANOKOIJI ', NULL, NULL, 1, '+51 993 321 920', '999929|1768417203', '1', NULL, 0, 0.00, NULL);
INSERT INTO `usuarios` VALUES (57, '003', 12, 0, '77425200dd', 'use', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'rodrigoyarleque7@gmail.com', 'EMER RODRIGO', NULL, NULL, 1, '+51 993 321 920', NULL, '1', NULL, 0, 0.00, NULL);
INSERT INTO `usuarios` VALUES (59, '004', 12, 7, '77425200', 'Ord-T', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'Systemcraft.pe@gmail.com', 'EMER RODRIGO', NULL, NULL, 1, '+51 993 321 920', NULL, '1', NULL, 0, 0.00, 'public/uploads/usuarios/usuario_1758946016_68d762e001e39.jpeg');
INSERT INTO `usuarios` VALUES (60, '005', 12, 3, '77492100', 'GojoLeg', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'kiyotakahitori@gmail.com', 'Satoru Gojo', NULL, NULL, 1, '9999999', NULL, '1', NULL, 0, 0.00, 'public/uploads/usuarios/usuario_1758946029_68d762ede8feb.jpeg');
INSERT INTO `usuarios` VALUES (61, '006', 12, 9, '445651465', 'usuario-doc', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'rodrigoyarleque7@gmail.com', 'CARLOS ANDRES', NULL, NULL, 1, '+51 993 321 920', NULL, '1', NULL, 0, 0.00, 'public/uploads/usuarios/usuario_1758946039_68d762f793ad5.jpg');
INSERT INTO `usuarios` VALUES (62, '007', 12, 10, '77412692', 'tallerUser', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'kiyo2takahitori@gmail.com', 'KIYOTAKA HITORI', NULL, NULL, 1, '+51 993 321 920', NULL, '1', NULL, 0, 0.00, NULL);
INSERT INTO `usuarios` VALUES (63, '008', 12, 1, '76877537', 'JVC_EDUARDO', '65ccba0dc0797b93da4d0d0544d00cfcb8d54c4d', 'ecrisostomo@industriajvcsac.com', 'Eduardo Crisostomo', NULL, NULL, 1, '928819937', NULL, '1', NULL, 0, 0.00, NULL);
INSERT INTO `usuarios` VALUES (64, '009', 12, 1, '77324564', 'testuser4', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'kiyotakahitori@gmail.com', 'YEMIMA ADALI', NULL, NULL, 1, ' 993 321 920', NULL, '1', NULL, 0, 0.00, NULL);
INSERT INTO `usuarios` VALUES (65, '010', 12, 1, '77412692', 'admin12', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'kiyotakahitori@gmail.com', 'GIANELA ALEXANDRA', NULL, NULL, 1, '999999999', NULL, '1', NULL, 0, 0.00, 'public/uploads/usuarios/usuario_1768914407_696f7de7b7221.jpg');

-- ----------------------------
-- Table structure for venta_anexo
-- ----------------------------
DROP TABLE IF EXISTS `venta_anexo`;
CREATE TABLE `venta_anexo`  (
  `idventa` int NOT NULL,
  `texto` varchar(245) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`idventa`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of venta_anexo
-- ----------------------------

-- ----------------------------
-- Table structure for ventas
-- ----------------------------
DROP TABLE IF EXISTS `ventas`;
CREATE TABLE `ventas`  (
  `id_venta` int NOT NULL AUTO_INCREMENT,
  `id_tido` int NOT NULL,
  `id_tipo_pago` int NULL DEFAULT NULL,
  `fecha_emision` date NULL DEFAULT NULL,
  `fecha_vencimiento` date NULL DEFAULT NULL,
  `dias_pagos` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `direccion` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NOT NULL,
  `serie` varchar(4) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `numero` int NULL DEFAULT NULL,
  `id_cliente` int NOT NULL,
  `total` double(10, 2) NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `enviado_sunat` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `apli_igv` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT '1',
  `observacion` varchar(220) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `doc_referencia` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `igv` double(10, 2) NULL DEFAULT 0.18,
  `medoto_pago_id` int NULL DEFAULT NULL,
  `pagado` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `is_segun_pago` char(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `medoto_pago2_id` int NULL DEFAULT NULL,
  `pagado2` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `moneda` int NULL DEFAULT 1,
  `cm_tc` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_coti` varchar(11) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `id_vendedor` int NULL DEFAULT NULL,
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_venta`) USING BTREE,
  INDEX `fk_ventas_documentos_sunat1_idx`(`id_tido` ASC) USING BTREE,
  INDEX `fk_ventas_clientes1_idx`(`id_cliente` ASC) USING BTREE,
  INDEX `fk_ventas_empresas1_idx`(`id_empresa` ASC) USING BTREE,
  INDEX `id_tipo_pago`(`id_tipo_pago` ASC) USING BTREE,
  INDEX `medoto_pago_id`(`medoto_pago_id` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas
-- ----------------------------
INSERT INTO `ventas` VALUES (20, 2, 1, '2025-12-15', '2025-12-15', '', 'CAL. MORELLI NRO. 181 INT. P-2 LIMA LIMA SAN BORJA', 'F001', 2392, 31, 387.50, '2', '0', 12, 1, '1', '', '', 0.18, 12, '', '1', 12, '', 1, '1', '1871', 40, '2025-12-15 13:46:35');
INSERT INTO `ventas` VALUES (21, 2, 1, '2025-12-15', '2025-12-15', '', 'CAL. MORELLI NRO. 181 INT. P-2 LIMA LIMA SAN BORJA', 'F001', 2393, 31, 387.50, '2', '0', 12, 1, '1', '', '', 0.18, 12, '', '1', 12, '', 1, '1', '1871', 40, '2025-12-15 13:48:04');
INSERT INTO `ventas` VALUES (22, 2, 1, '2025-12-17', '2025-12-17', '', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', 'F001', 2394, 32, 3894.00, '1', '0', 12, 1, '1', '', '', 0.18, 12, '', '1', 12, '', 1, '1', '1872', 40, '2025-12-17 19:02:06');
INSERT INTO `ventas` VALUES (23, 1, 2, '2026-01-20', '2026-01-20', '2026-02-19,2026-03-19,2026-04-19', '', 'B001', 610, 34, 3658.00, '1', '0', 12, 1, '1', '', '', 0.18, 12, '', '1', 12, '', 1, '1', '1874', 40, '2026-01-20 10:41:03');
INSERT INTO `ventas` VALUES (24, 1, 1, '2026-03-05', '2026-03-05', '', '', 'B001', 611, 34, 3658.00, '1', '0', 12, 1, '1', '', '', 0.18, 12, '', '1', 12, '', 1, '1', 'NULL', 40, '2026-03-05 23:06:42');
INSERT INTO `ventas` VALUES (25, 1, 1, '2026-03-05', '2026-03-05', '', '', 'B001', 612, 34, 3658.00, '1', '0', 12, 1, '1', '', '', 0.18, 12, '', '1', 12, '', 1, '1', 'NULL', 40, '2026-03-05 23:07:53');
INSERT INTO `ventas` VALUES (26, 1, 1, '2026-04-08', '2026-04-08', '', '', 'B001', 613, 34, 5310.00, '1', '0', 12, 1, '1', '', '', 0.18, 12, '', '1', 12, '', 1, '1', 'NULL', 40, '2026-04-08 16:43:00');
INSERT INTO `ventas` VALUES (27, 1, 1, '2026-05-15', '2026-05-15', '', '', 'B001', 614, 34, 3658.00, '1', '0', 12, 1, '1', '', '', 0.18, 12, '', '1', 12, '', 1, '1', 'NULL', 40, '2026-05-15 08:13:56');
INSERT INTO `ventas` VALUES (28, 1, 1, '2026-05-15', '2026-05-15', '', '', 'B001', 615, 34, 100.24, '1', '0', 12, 1, '1', '', '', 0.18, 12, '', '1', 12, '', 1, '1', 'NULL', 40, '2026-05-15 08:21:56');

-- ----------------------------
-- Table structure for ventas_anuladas
-- ----------------------------
DROP TABLE IF EXISTS `ventas_anuladas`;
CREATE TABLE `ventas_anuladas`  (
  `id_venta` int NOT NULL,
  `fecha` date NOT NULL,
  `motivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_venta`) USING BTREE,
  CONSTRAINT `ventas_anuladas_ibfk_1` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas_anuladas
-- ----------------------------
INSERT INTO `ventas_anuladas` VALUES (21, '2025-12-15', '-');

-- ----------------------------
-- Table structure for ventas_equipos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_equipos`;
CREATE TABLE `ventas_equipos`  (
  `id_venta_equipo` int NOT NULL AUTO_INCREMENT,
  `id_venta` int NOT NULL,
  `id_cotizacion_equipo` int NULL DEFAULT NULL,
  `marca` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `equipo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `modelo` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  `numero_serie` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_venta_equipo`) USING BTREE,
  INDEX `idx_ve_id_venta`(`id_venta` ASC) USING BTREE,
  INDEX `idx_ve_id_coti_equipo`(`id_cotizacion_equipo` ASC) USING BTREE,
  CONSTRAINT `fk_ve_venta` FOREIGN KEY (`id_venta`) REFERENCES `ventas` (`id_venta`) ON DELETE CASCADE ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas_equipos
-- ----------------------------

-- ----------------------------
-- Table structure for ventas_pagos
-- ----------------------------
DROP TABLE IF EXISTS `ventas_pagos`;
CREATE TABLE `ventas_pagos`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_venta` int NULL DEFAULT NULL,
  `metodo_pago` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `monto` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  `npago` varchar(255) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas_pagos
-- ----------------------------

-- ----------------------------
-- Table structure for ventas_referencias
-- ----------------------------
DROP TABLE IF EXISTS `ventas_referencias`;
CREATE TABLE `ventas_referencias`  (
  `id_venta` int NOT NULL,
  `id_referencia` int NOT NULL,
  `id_motivo` int NOT NULL,
  PRIMARY KEY (`id_venta`) USING BTREE,
  INDEX `fk_ventas_referencias_ventas2_idx`(`id_referencia` ASC) USING BTREE,
  INDEX `fk_ventas_referencias_motivo_documento1_idx`(`id_motivo` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas_referencias
-- ----------------------------

-- ----------------------------
-- Table structure for ventas_servicios
-- ----------------------------
DROP TABLE IF EXISTS `ventas_servicios`;
CREATE TABLE `ventas_servicios`  (
  `id_venta` int NOT NULL,
  `id_item` int NOT NULL,
  `descripcion` varchar(245) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  `monto` double(8, 2) NOT NULL,
  `cantidad` double(9, 2) NOT NULL,
  `codsunat` varchar(20) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL,
  PRIMARY KEY (`id_venta`, `id_item`) USING BTREE
) ENGINE = MyISAM AUTO_INCREMENT = 1 CHARACTER SET = latin1 COLLATE = latin1_swedish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas_servicios
-- ----------------------------

-- ----------------------------
-- Table structure for ventas_sunat
-- ----------------------------
DROP TABLE IF EXISTS `ventas_sunat`;
CREATE TABLE `ventas_sunat`  (
  `id_venta` int NOT NULL AUTO_INCREMENT,
  `hash` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `nombre_xml` varchar(45) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  `qr_data` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish_ci NULL DEFAULT NULL,
  PRIMARY KEY (`id_venta`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb3 COLLATE = utf8mb3_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of ventas_sunat
-- ----------------------------
INSERT INTO `ventas_sunat` VALUES (20, 'Cxj8RIN96IlqKMRT+dLhQXdjGUA=', '20538381978-01-F001-2392', '20538381978|03|F001-2392|59.11|387.50|2025-12-15|06|20100070970');
INSERT INTO `ventas_sunat` VALUES (21, 'S95R1xx+nOzkAr1qx6VK9PihHGc=', '20538381978-01-F001-2393', '20538381978|03|F001-2393|59.11|387.50|2025-12-15|06|20100070970');
INSERT INTO `ventas_sunat` VALUES (22, 'EKrK0brbT/J+2X/YpTtULl8AamQ=', '20538381978-01-F001-2394', '20538381978|03|F001-2394|594.00|3894.00|2025-12-17|06|20601212472');
INSERT INTO `ventas_sunat` VALUES (23, '', '20538381978-03-B001-610', '20538381978|03|B001-610|558.00|3658.00|2026-01-20|1|77425200');
INSERT INTO `ventas_sunat` VALUES (24, '', '20538381978-03-B001-611', '20538381978|03|B001-611|558.00|3658.00|2026-03-05|1|77425200');
INSERT INTO `ventas_sunat` VALUES (25, '', '20538381978-03-B001-612', '20538381978|03|B001-612|558.00|3658.00|2026-03-05|1|77425200');
INSERT INTO `ventas_sunat` VALUES (26, '', '20538381978-03-B001-613', '20538381978|03|B001-613|810.00|5310.00|2026-04-08|1|77425200');
INSERT INTO `ventas_sunat` VALUES (27, 'IS1aFJByuB17JUvwOnRsYnht7Z0=', '20538381978-03-B001-614', '20538381978|03|B001-614|558.00|3658.00|2026-05-15|1|77425200');
INSERT INTO `ventas_sunat` VALUES (28, '5cpEtTi+Gq6WZl9X9uQUjZl64CQ=', '20538381978-03-B001-615', '20538381978|03|B001-615|15.29|100.24|2026-05-15|1|77425200');

-- ----------------------------
-- View structure for temp_filtered_ventas
-- ----------------------------
DROP VIEW IF EXISTS `temp_filtered_ventas`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `temp_filtered_ventas` AS select `v`.`id_venta` AS `cod_v`,concat(`ds`.`abreviatura`,' | ',`v`.`serie`,' - ',`v`.`numero`) AS `sn_v`,concat(`c`.`documento`,' | ',`c`.`datos`) AS `datos_cl`,concat(if((`v`.`moneda` = 1),'S/ ','$ '),round(if((`v`.`apli_igv` = '1'),(`v`.`total` / (`v`.`igv` + 1)),`v`.`total`),2)) AS `subtotal`,concat(if((`v`.`moneda` = 1),'S/ ','$ '),round(if((`v`.`apli_igv` = '1'),((`v`.`total` / (`v`.`igv` + 1)) * `v`.`igv`),0),2)) AS `igv_v`,concat(`v`.`enviado_sunat`,'-',`v`.`id_tido`,'-',`v`.`id_venta`) AS `doc_ventae`,concat(`v`.`id_venta`,'--',`vs`.`nombre_xml`) AS `id_venta`,`v`.`fecha_emision` AS `fecha_emision`,`ds`.`abreviatura` AS `abreviatura`,`v`.`apli_igv` AS `apli_igv`,`v`.`igv` AS `igv`,`v`.`id_tido` AS `id_tido`,`v`.`serie` AS `serie`,`v`.`numero` AS `numero`,`c`.`documento` AS `documento`,`c`.`datos` AS `datos`,concat(if((`v`.`moneda` = 1),'S/ ','$ '),`v`.`total`) AS `total`,`v`.`estado` AS `estado`,`v`.`enviado_sunat` AS `enviado_sunat`,`vs`.`nombre_xml` AS `nombre_xml` from (((`ventas` `v` left join `documentos_sunat` `ds` on((`v`.`id_tido` = `ds`.`id_tido`))) left join `clientes` `c` on((`v`.`id_cliente` = `c`.`id_cliente`))) left join `ventas_sunat` `vs` on((`v`.`id_venta` = `vs`.`id_venta`))) where (`v`.`id_empresa` = '12') order by `v`.`fecha_emision`,`v`.`numero`;

-- ----------------------------
-- View structure for view_cotizaciones
-- ----------------------------
DROP VIEW IF EXISTS `view_cotizaciones`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_cotizaciones` AS select `v`.`cotizacion_id` AS `cotizacion_id`,`v`.`numero` AS `numero`,`v`.`fecha` AS `fecha`,`v`.`moneda` AS `moneda`,`v`.`cm_tc` AS `cm_tc`,`v`.`id_tido` AS `id_tido`,concat(`c`.`documento`,' | ',`c`.`datos`) AS `documento`,`c`.`datos` AS `datos`,`v`.`total` AS `total`,`v`.`estado` AS `estado`,`v`.`aplicar_igv` AS `aplicar_igv`,(case when ((`u`.`nombres` is not null) and (`u`.`apellidos` is not null)) then concat(`u`.`nombres`,' ',`u`.`apellidos`) when (`u`.`nombres` is not null) then `u`.`nombres` else `u`.`usuario` end) AS `vendedor`,`v`.`id_usuario` AS `usuario` from (((`cotizaciones` `v` left join `documentos_sunat` `ds` on((`v`.`id_tido` = `ds`.`id_tido`))) left join `clientes` `c` on((`v`.`id_cliente` = `c`.`id_cliente`))) left join `usuarios` `u` on((`u`.`usuario_id` = `v`.`id_usuario`))) where ((`v`.`id_empresa` = '12') and (`v`.`estado` <> '2')) order by `v`.`fecha` desc;

-- ----------------------------
-- View structure for view_productos_1
-- ----------------------------
DROP VIEW IF EXISTS `view_productos_1`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_productos_1` AS select `productos`.`id_producto` AS `id_producto`,`productos`.`cod_barra` AS `cod_barra`,`productos`.`nombre` AS `nombre`,`productos`.`precio` AS `precio`,`productos`.`costo` AS `costo`,`productos`.`cantidad` AS `cantidad`,`productos`.`iscbp` AS `iscbp`,`productos`.`id_empresa` AS `id_empresa`,`productos`.`sucursal` AS `sucursal`,`productos`.`ultima_salida` AS `ultima_salida`,`productos`.`codsunat` AS `codsunat`,`productos`.`usar_barra` AS `usar_barra`,`productos`.`precio_mayor` AS `precio_mayor`,`productos`.`precio_menor` AS `precio_menor`,`productos`.`razon_social` AS `razon_social`,`productos`.`ruc` AS `ruc`,`productos`.`estado` AS `estado`,`productos`.`almacen` AS `almacen`,`productos`.`precio2` AS `precio2`,`productos`.`precio3` AS `precio3`,`productos`.`precio4` AS `precio4`,`productos`.`precio_unidad` AS `precio_unidad`,`productos`.`codigo` AS `codigo`,`productos`.`imagen` AS `imagen`,`productos`.`detalle` AS `detalle`,`categorias`.`nombre` AS `categoria`,`unidades`.`nombre` AS `unidad`,`productos`.`moneda` AS `moneda` from ((`productos` left join `categorias` on((`categorias`.`id` = `productos`.`categoria`))) left join `unidades` on((`unidades`.`id` = `productos`.`unidad`))) where ((`productos`.`id_empresa` = 12) and (`productos`.`sucursal` = '1') and (`productos`.`estado` = '1') and (`productos`.`almacen` = '1')) order by `productos`.`id_producto`;

-- ----------------------------
-- View structure for view_productos_2
-- ----------------------------
DROP VIEW IF EXISTS `view_productos_2`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_productos_2` AS select `productos`.`id_producto` AS `id_producto`,`productos`.`cod_barra` AS `cod_barra`,`productos`.`nombre` AS `nombre`,`productos`.`precio` AS `precio`,`productos`.`costo` AS `costo`,`productos`.`cantidad` AS `cantidad`,`productos`.`iscbp` AS `iscbp`,`productos`.`id_empresa` AS `id_empresa`,`productos`.`sucursal` AS `sucursal`,`productos`.`ultima_salida` AS `ultima_salida`,`productos`.`codsunat` AS `codsunat`,`productos`.`usar_barra` AS `usar_barra`,`productos`.`precio_mayor` AS `precio_mayor`,`productos`.`precio_menor` AS `precio_menor`,`productos`.`razon_social` AS `razon_social`,`productos`.`ruc` AS `ruc`,`productos`.`estado` AS `estado`,`productos`.`almacen` AS `almacen`,`productos`.`precio2` AS `precio2`,`productos`.`precio3` AS `precio3`,`productos`.`precio4` AS `precio4`,`productos`.`precio_unidad` AS `precio_unidad`,`productos`.`codigo` AS `codigo`,`productos`.`imagen` AS `imagen`,`productos`.`detalle` AS `detalle`,`categorias`.`nombre` AS `categoria`,`unidades`.`nombre` AS `unidad`,`productos`.`moneda` AS `moneda` from ((`productos` left join `categorias` on((`categorias`.`id` = `productos`.`categoria`))) left join `unidades` on((`unidades`.`id` = `productos`.`unidad`))) where ((`productos`.`id_empresa` = 12) and (`productos`.`sucursal` = '1') and (`productos`.`estado` = '1') and (`productos`.`almacen` = '2')) order by `productos`.`id_producto`;

-- ----------------------------
-- View structure for view_productos_3
-- ----------------------------
DROP VIEW IF EXISTS `view_productos_3`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_productos_3` AS select `productos`.`id_producto` AS `id_producto`,`productos`.`cod_barra` AS `cod_barra`,`productos`.`nombre` AS `nombre`,`productos`.`precio` AS `precio`,`productos`.`costo` AS `costo`,`productos`.`cantidad` AS `cantidad`,`productos`.`iscbp` AS `iscbp`,`productos`.`id_empresa` AS `id_empresa`,`productos`.`sucursal` AS `sucursal`,`productos`.`ultima_salida` AS `ultima_salida`,`productos`.`codsunat` AS `codsunat`,`productos`.`usar_barra` AS `usar_barra`,`productos`.`precio_mayor` AS `precio_mayor`,`productos`.`precio_menor` AS `precio_menor`,`productos`.`razon_social` AS `razon_social`,`productos`.`ruc` AS `ruc`,`productos`.`estado` AS `estado`,`productos`.`almacen` AS `almacen`,`productos`.`precio2` AS `precio2`,`productos`.`precio3` AS `precio3`,`productos`.`precio4` AS `precio4`,`productos`.`precio_unidad` AS `precio_unidad`,`productos`.`codigo` AS `codigo`,`productos`.`imagen` AS `imagen`,`productos`.`detalle` AS `detalle`,`categorias`.`nombre` AS `categoria`,`unidades`.`nombre` AS `unidad`,`productos`.`moneda` AS `moneda` from ((`productos` left join `categorias` on((`categorias`.`id` = `productos`.`categoria`))) left join `unidades` on((`unidades`.`id` = `productos`.`unidad`))) where ((`productos`.`id_empresa` = 12) and (`productos`.`sucursal` = '1') and (`productos`.`estado` = '1') and (`productos`.`almacen` = '3')) order by `productos`.`id_producto`;

-- ----------------------------
-- View structure for view_productos_4
-- ----------------------------
DROP VIEW IF EXISTS `view_productos_4`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_productos_4` AS select `p`.`id_producto` AS `id_producto`,`p`.`cod_barra` AS `cod_barra`,`p`.`nombre` AS `nombre`,`p`.`precio` AS `precio`,`p`.`costo` AS `costo`,`p`.`cantidad` AS `cantidad`,`p`.`iscbp` AS `iscbp`,`p`.`id_empresa` AS `id_empresa`,`p`.`sucursal` AS `sucursal`,`p`.`ultima_salida` AS `ultima_salida`,`p`.`codsunat` AS `codsunat`,`p`.`usar_barra` AS `usar_barra`,`p`.`precio_mayor` AS `precio_mayor`,`p`.`precio_menor` AS `precio_menor`,`p`.`razon_social` AS `razon_social`,`p`.`ruc` AS `ruc`,`p`.`estado` AS `estado`,`p`.`almacen` AS `almacen`,`p`.`precio2` AS `precio2`,`p`.`precio3` AS `precio3`,`p`.`precio4` AS `precio4`,`p`.`precio_unidad` AS `precio_unidad`,`p`.`codigo` AS `codigo`,`p`.`imagen` AS `imagen`,`p`.`detalle` AS `detalle`,`c`.`nombre` AS `categoria`,`u`.`nombre` AS `unidad`,`p`.`moneda` AS `moneda` from ((`productos` `p` left join `categorias` `c` on((`c`.`id` = `p`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `p`.`unidad`))) where ((`p`.`id_empresa` = '12') and (`p`.`sucursal` = '1') and (`p`.`estado` = '1') and (`p`.`almacen` = '4')) order by (case when (`p`.`codigo` like 'JVC%') then 0 else 1 end),`p`.`codigo`;

-- ----------------------------
-- View structure for view_productos_5
-- ----------------------------
DROP VIEW IF EXISTS `view_productos_5`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_productos_5` AS select `p`.`id_producto` AS `id_producto`,`p`.`cod_barra` AS `cod_barra`,`p`.`nombre` AS `nombre`,`p`.`precio` AS `precio`,`p`.`costo` AS `costo`,`p`.`cantidad` AS `cantidad`,`p`.`iscbp` AS `iscbp`,`p`.`id_empresa` AS `id_empresa`,`p`.`sucursal` AS `sucursal`,`p`.`ultima_salida` AS `ultima_salida`,`p`.`codsunat` AS `codsunat`,`p`.`usar_barra` AS `usar_barra`,`p`.`precio_mayor` AS `precio_mayor`,`p`.`precio_menor` AS `precio_menor`,`p`.`razon_social` AS `razon_social`,`p`.`ruc` AS `ruc`,`p`.`estado` AS `estado`,`p`.`almacen` AS `almacen`,`p`.`precio2` AS `precio2`,`p`.`precio3` AS `precio3`,`p`.`precio4` AS `precio4`,`p`.`precio_unidad` AS `precio_unidad`,`p`.`codigo` AS `codigo`,`p`.`imagen` AS `imagen`,`p`.`detalle` AS `detalle`,`p`.`usar_multiprecio` AS `usar_multiprecio`,`c`.`nombre` AS `categoria`,`u`.`nombre` AS `unidad`,`p`.`moneda` AS `moneda` from ((`productos` `p` left join `categorias` `c` on((`c`.`id` = `p`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `p`.`unidad`))) where ((`p`.`id_empresa` = 12) and (`p`.`sucursal` = 1) and (`p`.`estado` = '1') and (`p`.`almacen` = 5)) order by (case when (`p`.`codigo` like 'JVC%') then 0 else 1 end),`p`.`codigo`;

-- ----------------------------
-- View structure for view_productos_6
-- ----------------------------
DROP VIEW IF EXISTS `view_productos_6`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_productos_6` AS select `p`.`id_producto` AS `id_producto`,`p`.`cod_barra` AS `cod_barra`,`p`.`nombre` AS `nombre`,`p`.`precio` AS `precio`,`p`.`costo` AS `costo`,`p`.`cantidad` AS `cantidad`,`p`.`iscbp` AS `iscbp`,`p`.`id_empresa` AS `id_empresa`,`p`.`sucursal` AS `sucursal`,`p`.`ultima_salida` AS `ultima_salida`,`p`.`codsunat` AS `codsunat`,`p`.`usar_barra` AS `usar_barra`,`p`.`precio_mayor` AS `precio_mayor`,`p`.`precio_menor` AS `precio_menor`,`p`.`razon_social` AS `razon_social`,`p`.`ruc` AS `ruc`,`p`.`estado` AS `estado`,`p`.`almacen` AS `almacen`,`p`.`precio2` AS `precio2`,`p`.`precio3` AS `precio3`,`p`.`precio4` AS `precio4`,`p`.`precio_unidad` AS `precio_unidad`,`p`.`codigo` AS `codigo`,`p`.`imagen` AS `imagen`,`p`.`detalle` AS `detalle`,`p`.`usar_multiprecio` AS `usar_multiprecio`,`c`.`nombre` AS `categoria`,`u`.`nombre` AS `unidad`,`p`.`moneda` AS `moneda` from ((`productos` `p` left join `categorias` `c` on((`c`.`id` = `p`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `p`.`unidad`))) where ((`p`.`id_empresa` = 12) and (`p`.`sucursal` = 1) and (`p`.`estado` = '1') and (`p`.`almacen` = 6)) order by (case when (`p`.`codigo` like 'JVC%') then 0 else 1 end),`p`.`codigo`;

-- ----------------------------
-- View structure for view_productos_7
-- ----------------------------
DROP VIEW IF EXISTS `view_productos_7`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_productos_7` AS select `p`.`id_producto` AS `id_producto`,`p`.`cod_barra` AS `cod_barra`,`p`.`nombre` AS `nombre`,`p`.`precio` AS `precio`,`p`.`costo` AS `costo`,`p`.`cantidad` AS `cantidad`,`p`.`iscbp` AS `iscbp`,`p`.`id_empresa` AS `id_empresa`,`p`.`sucursal` AS `sucursal`,`p`.`ultima_salida` AS `ultima_salida`,`p`.`codsunat` AS `codsunat`,`p`.`usar_barra` AS `usar_barra`,`p`.`precio_mayor` AS `precio_mayor`,`p`.`precio_menor` AS `precio_menor`,`p`.`razon_social` AS `razon_social`,`p`.`ruc` AS `ruc`,`p`.`estado` AS `estado`,`p`.`almacen` AS `almacen`,`p`.`precio2` AS `precio2`,`p`.`precio3` AS `precio3`,`p`.`precio4` AS `precio4`,`p`.`precio_unidad` AS `precio_unidad`,`p`.`codigo` AS `codigo`,`p`.`imagen` AS `imagen`,`p`.`detalle` AS `detalle`,`p`.`usar_multiprecio` AS `usar_multiprecio`,`c`.`nombre` AS `categoria`,`u`.`nombre` AS `unidad`,`p`.`moneda` AS `moneda` from ((`productos` `p` left join `categorias` `c` on((`c`.`id` = `p`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `p`.`unidad`))) where ((`p`.`id_empresa` = 12) and (`p`.`sucursal` = 1) and (`p`.`estado` = '1') and (`p`.`almacen` = 7)) order by (case when (`p`.`codigo` like 'JVC%') then 0 else 1 end),`p`.`codigo`;

-- ----------------------------
-- View structure for view_productos_8
-- ----------------------------
DROP VIEW IF EXISTS `view_productos_8`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_productos_8` AS select `p`.`id_producto` AS `id_producto`,`p`.`cod_barra` AS `cod_barra`,`p`.`nombre` AS `nombre`,`p`.`precio` AS `precio`,`p`.`costo` AS `costo`,`p`.`cantidad` AS `cantidad`,`p`.`iscbp` AS `iscbp`,`p`.`id_empresa` AS `id_empresa`,`p`.`sucursal` AS `sucursal`,`p`.`ultima_salida` AS `ultima_salida`,`p`.`codsunat` AS `codsunat`,`p`.`usar_barra` AS `usar_barra`,`p`.`precio_mayor` AS `precio_mayor`,`p`.`precio_menor` AS `precio_menor`,`p`.`razon_social` AS `razon_social`,`p`.`ruc` AS `ruc`,`p`.`estado` AS `estado`,`p`.`almacen` AS `almacen`,`p`.`precio2` AS `precio2`,`p`.`precio3` AS `precio3`,`p`.`precio4` AS `precio4`,`p`.`precio_unidad` AS `precio_unidad`,`p`.`codigo` AS `codigo`,`p`.`imagen` AS `imagen`,`p`.`detalle` AS `detalle`,`p`.`usar_multiprecio` AS `usar_multiprecio`,`c`.`nombre` AS `categoria`,`u`.`nombre` AS `unidad`,`p`.`moneda` AS `moneda` from ((`productos` `p` left join `categorias` `c` on((`c`.`id` = `p`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `p`.`unidad`))) where ((`p`.`id_empresa` = '12') and (`p`.`sucursal` = '1') and (`p`.`estado` = '1') and (`p`.`almacen` = '8')) order by (case when (`p`.`codigo` like 'JVC%') then 0 else 1 end),`p`.`codigo`;

-- ----------------------------
-- View structure for view_repuestos_1
-- ----------------------------
DROP VIEW IF EXISTS `view_repuestos_1`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_repuestos_1` AS select `r`.`id_repuesto` AS `id_repuesto`,`r`.`codigo` AS `codigo`,`r`.`nombre` AS `nombre`,`r`.`detalle` AS `detalle`,`r`.`precio` AS `precio`,`r`.`costo` AS `costo`,`r`.`cantidad` AS `cantidad`,`r`.`almacen` AS `almacen`,`r`.`unidad` AS `unidad`,`r`.`iscbp` AS `iscbp`,`r`.`cod_barra` AS `cod_barra`,`r`.`id_empresa` AS `id_empresa`,`r`.`sucursal` AS `sucursal`,`r`.`estado` AS `estado`,`r`.`precio_unidad` AS `precio_unidad`,`r`.`precio2` AS `precio2`,`r`.`precio3` AS `precio3`,`r`.`precio4` AS `precio4`,`r`.`precio_mayor` AS `precio_mayor`,`r`.`precio_menor` AS `precio_menor`,`r`.`moneda` AS `moneda`,`r`.`codsunat` AS `codsunat`,`r`.`razon_social` AS `razon_social`,`r`.`ruc` AS `ruc`,`r`.`usar_multiprecio` AS `usar_multiprecio`,`r`.`usar_barra` AS `usar_barra`,`r`.`ultima_salida` AS `ultima_salida`,`r`.`descripcion` AS `descripcion`,`c`.`nombre` AS `categoria`,`u`.`nombre` AS `unidad_nombre` from ((`repuestos` `r` left join `categorias` `c` on((`c`.`id` = `r`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `r`.`unidad`))) where ((`r`.`id_empresa` = '12') and (`r`.`sucursal` = '1') and (`r`.`estado` = '1') and (`r`.`almacen` = '1')) order by (case when (`r`.`codigo` like 'JVC%') then 0 else 1 end),`r`.`codigo`;

-- ----------------------------
-- View structure for view_repuestos_4
-- ----------------------------
DROP VIEW IF EXISTS `view_repuestos_4`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_repuestos_4` AS select `r`.`id_repuesto` AS `id_repuesto`,`r`.`codigo` AS `codigo`,`r`.`nombre` AS `nombre`,`r`.`detalle` AS `detalle`,`r`.`precio` AS `precio`,`r`.`costo` AS `costo`,`r`.`cantidad` AS `cantidad`,`r`.`almacen` AS `almacen`,`r`.`unidad` AS `unidad`,`r`.`iscbp` AS `iscbp`,`r`.`cod_barra` AS `cod_barra`,`r`.`id_empresa` AS `id_empresa`,`r`.`sucursal` AS `sucursal`,`r`.`estado` AS `estado`,`r`.`precio_unidad` AS `precio_unidad`,`r`.`precio2` AS `precio2`,`r`.`precio3` AS `precio3`,`r`.`precio4` AS `precio4`,`r`.`precio_mayor` AS `precio_mayor`,`r`.`precio_menor` AS `precio_menor`,`r`.`moneda` AS `moneda`,`r`.`codsunat` AS `codsunat`,`r`.`razon_social` AS `razon_social`,`r`.`ruc` AS `ruc`,`r`.`usar_multiprecio` AS `usar_multiprecio`,`r`.`usar_barra` AS `usar_barra`,`r`.`ultima_salida` AS `ultima_salida`,`r`.`descripcion` AS `descripcion`,coalesce(`c`.`nombre`,'') AS `categoria`,coalesce(`u`.`nombre`,'') AS `unidad_nombre` from ((`repuestos` `r` left join `categorias` `c` on((`c`.`id` = `r`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `r`.`unidad`))) where ((`r`.`id_empresa` = 12) and (`r`.`sucursal` = 1) and (`r`.`estado` = '1') and (`r`.`almacen` = '4')) order by (case when (`r`.`codigo` like 'JVC%') then 0 else 1 end),`r`.`codigo`;

-- ----------------------------
-- View structure for view_repuestos_5
-- ----------------------------
DROP VIEW IF EXISTS `view_repuestos_5`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_repuestos_5` AS select `r`.`id_repuesto` AS `id_repuesto`,`r`.`codigo` AS `codigo`,`r`.`nombre` AS `nombre`,`r`.`detalle` AS `detalle`,`r`.`precio` AS `precio`,`r`.`costo` AS `costo`,`r`.`cantidad` AS `cantidad`,`r`.`almacen` AS `almacen`,`r`.`unidad` AS `unidad`,`r`.`iscbp` AS `iscbp`,`r`.`cod_barra` AS `cod_barra`,`r`.`id_empresa` AS `id_empresa`,`r`.`sucursal` AS `sucursal`,`r`.`estado` AS `estado`,`r`.`precio_unidad` AS `precio_unidad`,`r`.`precio2` AS `precio2`,`r`.`precio3` AS `precio3`,`r`.`precio4` AS `precio4`,`r`.`precio_mayor` AS `precio_mayor`,`r`.`precio_menor` AS `precio_menor`,`r`.`moneda` AS `moneda`,`r`.`codsunat` AS `codsunat`,`r`.`razon_social` AS `razon_social`,`r`.`ruc` AS `ruc`,`r`.`usar_multiprecio` AS `usar_multiprecio`,`r`.`usar_barra` AS `usar_barra`,`r`.`ultima_salida` AS `ultima_salida`,`r`.`descripcion` AS `descripcion`,`c`.`nombre` AS `categoria`,`u`.`nombre` AS `unidad_nombre` from ((`repuestos` `r` left join `categorias` `c` on((`c`.`id` = `r`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `r`.`unidad`))) where ((`r`.`id_empresa` = '12') and (`r`.`sucursal` = '1') and (`r`.`estado` = '1') and (`r`.`almacen` = '5')) order by (case when (`r`.`codigo` like 'JVC%') then 0 else 1 end),`r`.`codigo`;

-- ----------------------------
-- View structure for view_repuestos_6
-- ----------------------------
DROP VIEW IF EXISTS `view_repuestos_6`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_repuestos_6` AS select `r`.`id_repuesto` AS `id_repuesto`,`r`.`codigo` AS `codigo`,`r`.`nombre` AS `nombre`,`r`.`detalle` AS `detalle`,`r`.`precio` AS `precio`,`r`.`costo` AS `costo`,`r`.`cantidad` AS `cantidad`,`r`.`almacen` AS `almacen`,`r`.`unidad` AS `unidad`,`r`.`iscbp` AS `iscbp`,`r`.`cod_barra` AS `cod_barra`,`r`.`id_empresa` AS `id_empresa`,`r`.`sucursal` AS `sucursal`,`r`.`estado` AS `estado`,`r`.`precio_unidad` AS `precio_unidad`,`r`.`precio2` AS `precio2`,`r`.`precio3` AS `precio3`,`r`.`precio4` AS `precio4`,`r`.`precio_mayor` AS `precio_mayor`,`r`.`precio_menor` AS `precio_menor`,`r`.`moneda` AS `moneda`,`r`.`codsunat` AS `codsunat`,`r`.`razon_social` AS `razon_social`,`r`.`ruc` AS `ruc`,`r`.`usar_multiprecio` AS `usar_multiprecio`,`r`.`usar_barra` AS `usar_barra`,`r`.`ultima_salida` AS `ultima_salida`,`r`.`descripcion` AS `descripcion`,`c`.`nombre` AS `categoria`,`u`.`nombre` AS `unidad_nombre` from ((`repuestos` `r` left join `categorias` `c` on((`c`.`id` = `r`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `r`.`unidad`))) where ((`r`.`id_empresa` = '12') and (`r`.`sucursal` = '1') and (`r`.`estado` = '1') and (`r`.`almacen` = '6')) order by (case when (`r`.`codigo` like 'JVC%') then 0 else 1 end),`r`.`codigo`;

-- ----------------------------
-- View structure for view_repuestos_7
-- ----------------------------
DROP VIEW IF EXISTS `view_repuestos_7`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_repuestos_7` AS select `r`.`id_repuesto` AS `id_repuesto`,`r`.`codigo` AS `codigo`,`r`.`nombre` AS `nombre`,`r`.`detalle` AS `detalle`,`r`.`precio` AS `precio`,`r`.`costo` AS `costo`,`r`.`cantidad` AS `cantidad`,`r`.`almacen` AS `almacen`,`r`.`unidad` AS `unidad`,`r`.`iscbp` AS `iscbp`,`r`.`cod_barra` AS `cod_barra`,`r`.`id_empresa` AS `id_empresa`,`r`.`sucursal` AS `sucursal`,`r`.`estado` AS `estado`,`r`.`precio_unidad` AS `precio_unidad`,`r`.`precio2` AS `precio2`,`r`.`precio3` AS `precio3`,`r`.`precio4` AS `precio4`,`r`.`precio_mayor` AS `precio_mayor`,`r`.`precio_menor` AS `precio_menor`,`r`.`moneda` AS `moneda`,`r`.`codsunat` AS `codsunat`,`r`.`razon_social` AS `razon_social`,`r`.`ruc` AS `ruc`,`r`.`usar_multiprecio` AS `usar_multiprecio`,`r`.`usar_barra` AS `usar_barra`,`r`.`ultima_salida` AS `ultima_salida`,`r`.`descripcion` AS `descripcion`,`c`.`nombre` AS `categoria`,`u`.`nombre` AS `unidad_nombre` from ((`repuestos` `r` left join `categorias` `c` on((`c`.`id` = `r`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `r`.`unidad`))) where ((`r`.`id_empresa` = '12') and (`r`.`sucursal` = '1') and (`r`.`estado` = '1') and (`r`.`almacen` = '7')) order by (case when (`r`.`codigo` like 'JVC%') then 0 else 1 end),`r`.`codigo`;

-- ----------------------------
-- View structure for view_repuestos_8
-- ----------------------------
DROP VIEW IF EXISTS `view_repuestos_8`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_repuestos_8` AS select `r`.`id_repuesto` AS `id_repuesto`,`r`.`codigo` AS `codigo`,`r`.`nombre` AS `nombre`,`r`.`detalle` AS `detalle`,`r`.`precio` AS `precio`,`r`.`costo` AS `costo`,`r`.`cantidad` AS `cantidad`,`r`.`almacen` AS `almacen`,`r`.`unidad` AS `unidad`,`r`.`iscbp` AS `iscbp`,`r`.`cod_barra` AS `cod_barra`,`r`.`id_empresa` AS `id_empresa`,`r`.`sucursal` AS `sucursal`,`r`.`estado` AS `estado`,`r`.`precio_unidad` AS `precio_unidad`,`r`.`precio2` AS `precio2`,`r`.`precio3` AS `precio3`,`r`.`precio4` AS `precio4`,`r`.`precio_mayor` AS `precio_mayor`,`r`.`precio_menor` AS `precio_menor`,`r`.`moneda` AS `moneda`,`r`.`codsunat` AS `codsunat`,`r`.`razon_social` AS `razon_social`,`r`.`ruc` AS `ruc`,`r`.`usar_multiprecio` AS `usar_multiprecio`,`r`.`usar_barra` AS `usar_barra`,`r`.`ultima_salida` AS `ultima_salida`,`r`.`descripcion` AS `descripcion`,`c`.`nombre` AS `categoria`,`u`.`nombre` AS `unidad_nombre` from ((`repuestos` `r` left join `categorias` `c` on((`c`.`id` = `r`.`categoria`))) left join `unidades` `u` on((`u`.`id` = `r`.`unidad`))) where ((`r`.`id_empresa` = '12') and (`r`.`sucursal` = '1') and (`r`.`estado` = '1') and (`r`.`almacen` = '8')) order by (case when (`r`.`codigo` like 'JVC%') then 0 else 1 end),`r`.`codigo`;

-- ----------------------------
-- View structure for view_taller_cotizaciones
-- ----------------------------
DROP VIEW IF EXISTS `view_taller_cotizaciones`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_taller_cotizaciones` AS select `tc`.`id_cotizacion` AS `cotizacion_id`,`tc`.`numero` AS `numero`,`tc`.`fecha` AS `fecha`,`tc`.`moneda` AS `moneda`,`tc`.`cm_tc` AS `cm_tc`,`tc`.`id_tido` AS `id_tido`,`tc`.`tipo_origen` AS `tipo_origen`,(case when (`pa`.`id_preAlerta` is not null) then convert(concat(`pa`.`cliente_razon_social`,' | ',`pa`.`cliente_ruc`) using utf8mb3) else concat(`c`.`documento`,' | ',`c`.`datos`) end) AS `documento`,(case when (`pa`.`id_preAlerta` is not null) then convert(`pa`.`cliente_razon_social` using utf8mb3) else `c`.`datos` end) AS `datos`,`tc`.`total` AS `total`,`tc`.`estado` AS `estado`,`tc`.`guia_numero` AS `guia_numero`,`u`.`usuario` AS `vendedor`,`tc`.`id_usuario` AS `usuario`,`tc`.`sucursal` AS `sucursal`,coalesce(convert(`pa`.`atencion_encargado` using utf8mb3),`c`.`direccion2`) AS `atencion_encargado` from (((`taller_cotizaciones` `tc` left join `pre_alerta` `pa` on((`tc`.`id_prealerta` = `pa`.`id_preAlerta`))) left join `clientes` `c` on((`tc`.`id_cliente` = `c`.`id_cliente`))) left join `usuarios` `u` on((`u`.`usuario_id` = `tc`.`id_usuario`))) where ((`tc`.`id_empresa` = '12') and (`tc`.`estado` <> '2'));

-- ----------------------------
-- View structure for view_ventas
-- ----------------------------
DROP VIEW IF EXISTS `view_ventas`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_ventas` AS select `v`.`id_venta` AS `cod_v`,concat(`ds`.`abreviatura`,' | ',`v`.`serie`,' - ',`v`.`numero`) AS `sn_v`,concat(`c`.`documento`,' | ',`c`.`datos`) AS `datos_cl`,concat(if((`v`.`moneda` = 1),'S/ ','$ '),round(if((`v`.`apli_igv` = '1'),(`v`.`total` / (`v`.`igv` + 1)),`v`.`total`),2)) AS `subtotal`,concat(if((`v`.`moneda` = 1),'S/ ','$ '),round(if((`v`.`apli_igv` = '1'),((`v`.`total` / (`v`.`igv` + 1)) * `v`.`igv`),0),2)) AS `igv_v`,concat(`v`.`enviado_sunat`,'-',`v`.`id_tido`,'-',`v`.`id_venta`) AS `doc_ventae`,concat(`v`.`id_venta`,'--',`vs`.`nombre_xml`) AS `id_venta`,`v`.`fecha_emision` AS `fecha_emision`,`ds`.`abreviatura` AS `abreviatura`,`v`.`apli_igv` AS `apli_igv`,`v`.`igv` AS `igv`,`v`.`id_tido` AS `id_tido`,`v`.`serie` AS `serie`,`v`.`numero` AS `numero`,`c`.`documento` AS `documento`,`c`.`datos` AS `datos`,concat(if((`v`.`moneda` = 1),'S/ ','$ '),`v`.`total`) AS `total`,`v`.`estado` AS `estado`,`v`.`enviado_sunat` AS `enviado_sunat`,`vs`.`nombre_xml` AS `nombre_xml` from (((`ventas` `v` left join `documentos_sunat` `ds` on((`v`.`id_tido` = `ds`.`id_tido`))) left join `clientes` `c` on((`v`.`id_cliente` = `c`.`id_cliente`))) left join `ventas_sunat` `vs` on((`v`.`id_venta` = `vs`.`id_venta`))) where (`v`.`id_empresa` = '12') order by `v`.`fecha_emision`,`v`.`numero`;

-- ----------------------------
-- View structure for vista_ordenes_unificada
-- ----------------------------
DROP VIEW IF EXISTS `vista_ordenes_unificada`;
CREATE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `vista_ordenes_unificada` AS select concat('OT-',`orden_trabajo_pre`.`id_orden_trabajo`) AS `id_registro`,`orden_trabajo_pre`.`id_orden_trabajo` AS `id_original`,'ORD TRABAJO' AS `origen`,`orden_trabajo_pre`.`cliente_razon_social` AS `cliente_razon_social`,`orden_trabajo_pre`.`cliente_ruc` AS `cliente_ruc`,`orden_trabajo_pre`.`direccion` AS `direccion`,`orden_trabajo_pre`.`atencion_encargado` AS `atencion_encargado`,`orden_trabajo_pre`.`fecha_ingreso` AS `fecha_ingreso`,`orden_trabajo_pre`.`tiene_cotizacion` AS `tiene_cotizacion`,`orden_trabajo_pre`.`estado` AS `estado`,`orden_trabajo_pre`.`observaciones` AS `observaciones`,`orden_trabajo_pre`.`created_at` AS `created_at`,`orden_trabajo_pre`.`updated_at` AS `updated_at` from `orden_trabajo_pre` union all select concat('OS-',`orden_servicio_pre`.`id_orden_servicio`) AS `id_registro`,`orden_servicio_pre`.`id_orden_servicio` AS `id_original`,'ORD SERVICIO' AS `origen`,`orden_servicio_pre`.`cliente_razon_social` AS `cliente_razon_social`,`orden_servicio_pre`.`cliente_ruc` AS `cliente_ruc`,`orden_servicio_pre`.`direccion` AS `direccion`,`orden_servicio_pre`.`atencion_encargado` AS `atencion_encargado`,`orden_servicio_pre`.`fecha_ingreso` AS `fecha_ingreso`,`orden_servicio_pre`.`tiene_cotizacion` AS `tiene_cotizacion`,`orden_servicio_pre`.`estado` AS `estado`,`orden_servicio_pre`.`observaciones` AS `observaciones`,`orden_servicio_pre`.`created_at` AS `created_at`,`orden_servicio_pre`.`updated_at` AS `updated_at` from `orden_servicio_pre` order by `fecha_ingreso` desc,`created_at` desc;

SET FOREIGN_KEY_CHECKS = 1;
