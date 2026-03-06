/*
 Navicat Premium Dump SQL

 Source Server         : localhist
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : ferreteria2

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 16/12/2025 10:57:29
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of compra
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
