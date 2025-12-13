/*
 Navicat Premium Dump SQL

 Source Server         : localhist
 Source Server Type    : MySQL
 Source Server Version : 100427 (10.4.27-MariaDB)
 Source Host           : localhost:3306
 Source Schema         : factura_jvc1

 Target Server Type    : MySQL
 Target Server Version : 100427 (10.4.27-MariaDB)
 File Encoding         : 65001

 Date: 27/10/2025 11:01:48
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for repuestos
-- ----------------------------
DROP TABLE IF EXISTS `repuestos`;
CREATE TABLE `repuestos`  (
  `id_repuesto` int NOT NULL AUTO_INCREMENT,
  `cod_barra` varchar(100) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL,
  `nombre` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL,
  `precio` double(10, 2) NULL DEFAULT NULL,
  `costo` double(10, 2) NULL DEFAULT NULL,
  `cantidad` int NULL DEFAULT NULL,
  `iscbp` int NULL DEFAULT NULL,
  `id_empresa` int NOT NULL,
  `sucursal` int NULL DEFAULT NULL,
  `ultima_salida` date NOT NULL,
  `codsunat` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `usar_barra` char(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT '0',
  `usar_multiprecio` char(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT '0',
  `precio_mayor` double(10, 2) NULL DEFAULT NULL,
  `precio_menor` double(10, 2) NULL DEFAULT NULL,
  `razon_social` varchar(250) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL,
  `ruc` varchar(11) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL,
  `estado` char(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT '1',
  `almacen` char(1) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL,
  `precio2` double(10, 2) NULL DEFAULT 0.00,
  `precio3` double(10, 2) NULL DEFAULT 0.00,
  `precio4` double(10, 2) NULL DEFAULT 0.00,
  `precio_unidad` double(10, 2) NULL DEFAULT NULL,
  `codigo` varchar(20) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL,
  `imagen` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL,
  `detalle` text CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL,
  `categoria` int NULL DEFAULT NULL,
  `descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL,
  `unidad` varchar(255) CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT NULL,
  `moneda` enum('PEN','USD') CHARACTER SET utf8 COLLATE utf8_spanish_ci NULL DEFAULT 'PEN',
  `subcategoria` int NULL DEFAULT NULL,
  `fecha_ultimo_ingreso` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`id_repuesto`) USING BTREE,
  INDEX `fk_repuestos_empresas1_idx`(`id_empresa` ASC) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 9 CHARACTER SET = utf8 COLLATE = utf8_spanish_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of repuestos
-- ----------------------------
INSERT INTO `repuestos` VALUES (1, '', 'CARBURADOR', 131.00, 120.00, 360, 0, 12, 1, '1000-01-01', '', '0', '0', 120.00, 120.00, '1', '1', '1', '1', 120.00, 0.00, 0.00, 131.00, 'REP-001', NULL, 'SAVUVCDFUSDAVCYH DSA', 5, NULL, '15', 'PEN', 4, '2025-06-20 16:14:24');
INSERT INTO `repuestos` VALUES (2, NULL, 'sdfvasa', 0.00, 0.00, 0, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '1', '1', '1', '1', 0.00, 0.00, 0.00, 0.00, '2132', NULL, 'vdfass', 6, NULL, '14', 'PEN', 2, NULL);
INSERT INTO `repuestos` VALUES (3, NULL, 'sdavsa', 120.00, 101.00, 210, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '', '', '0', '1', 0.00, 0.00, 0.00, 120.00, '1231220', NULL, 'sadva', 6, NULL, '15', 'PEN', 2, NULL);
INSERT INTO `repuestos` VALUES (4, NULL, 'sdcsa', 213.00, 123.00, 213, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '', '', '1', '1', 0.00, 0.00, 0.00, 213.00, '213', NULL, '132', 6, NULL, '15', 'PEN', 2, NULL);
INSERT INTO `repuestos` VALUES (5, NULL, 'sdcsaewcdscsa', 213.00, 123.00, 213, 0, 12, 1, '1000-01-01', '', '0', '0', 0.00, 0.00, '', '', '1', '1', 0.00, 0.00, 0.00, 213.00, '213', NULL, '132', 6, NULL, '15', 'PEN', 2, NULL);
INSERT INTO `repuestos` VALUES (6, NULL, 'dsavcas', 213.00, 123.00, 213, 0, 12, 1, '1000-01-01', '213', '0', '0', 0.00, 0.00, '1', '1', '1', '1', 0.00, 0.00, 0.00, 213.00, 'sadc', NULL, 'csacas', 6, NULL, '14', 'PEN', 2, NULL);
INSERT INTO `repuestos` VALUES (7, 'REP-008', ' ASPIRADORA INDUSTRIAL DE POLVO Y AGUA DE 6 GALONES - MARCA: CRIS-TAURO', 2131.00, 213.00, 225, 0, 12, 1, '1000-01-01', '213', '1', '0', 213.00, 213.00, '', '', '1', '1', 213.00, 0.00, 0.00, 2131.00, 'REP-008', NULL, 'SDVCASCDSA', 5, NULL, '15', 'PEN', 3, NULL);
INSERT INTO `repuestos` VALUES (8, NULL, 'sdavcdscds', 2314.00, 123.00, 213, 0, 12, 1, '1000-01-01', '123', '0', '0', 123.00, 213.00, '', '', '0', '1', 213.00, 0.00, 0.00, 2314.00, '1231', NULL, 'sdfvcdsvfds', 5, NULL, '14', 'PEN', 3, NULL);

SET FOREIGN_KEY_CHECKS = 1;
