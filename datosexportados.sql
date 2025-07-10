-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-07-2025 a las 16:35:57
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `factura_jvc1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id_cliente` int(11) NOT NULL,
  `documento` varchar(11) DEFAULT NULL,
  `datos` varchar(245) DEFAULT NULL,
  `direccion` varchar(245) DEFAULT NULL,
  `direccion2` varchar(220) DEFAULT NULL,
  `telefono` varchar(200) DEFAULT NULL,
  `telefono2` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `ultima_venta` date DEFAULT NULL,
  `total_venta` double(8,2) DEFAULT NULL,
  `id_rubro` int(11) DEFAULT NULL,
  `ubigeo` varchar(6) DEFAULT NULL,
  `departamento` varchar(100) DEFAULT NULL,
  `provincia` varchar(100) DEFAULT NULL,
  `distrito` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id_cliente`, `documento`, `datos`, `direccion`, `direccion2`, `telefono`, `telefono2`, `email`, `id_empresa`, `ultima_venta`, `total_venta`, `id_rubro`, `ubigeo`, `departamento`, `provincia`, `distrito`) VALUES
(28, '77425200', 'EMER RODRIGO YARLEQUE ZAPATA', 'AH MIRAFLORES', '', '993321920', '99', 'KIYOTAKA@GAMIL.COM', 12, '1000-01-01', 0.00, NULL, '', '', '', ''),
(32, '77426200', 'BRENDY YOSELY ZAPATA TORRES', 'AH MIRAFLORES', '', '993321921', '', 'emer@gmail.com', 12, '1000-01-01', 0.00, 7, '', '', '', ''),
(33, '20100128056', 'SAGA FALABELLA S A', 'AV. PASEO DE LA REPUBLICA NRO 3220 URB. JARDIN ', 'mif sdhcbsa', NULL, NULL, NULL, 12, '1000-01-01', 0.00, NULL, '', '', '', ''),
(34, '20100128218', 'PETROLEOS DEL PERU PETROPERU SA', 'AV. ENRIQUE CANAVAL MOREYRA NRO. 150 LIMA LIMA SAN ISIDRO', NULL, NULL, NULL, NULL, 12, NULL, NULL, NULL, '', '', '', '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes_taller`
--

CREATE TABLE `clientes_taller` (
  `id_cliente_taller` int(11) NOT NULL,
  `documento` varchar(11) DEFAULT NULL,
  `datos` varchar(245) DEFAULT NULL,
  `direccion` varchar(245) DEFAULT NULL,
  `atencion` varchar(220) DEFAULT NULL,
  `telefono` varchar(200) DEFAULT NULL,
  `telefono2` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `id_empresa` int(11) NOT NULL,
  `ultima_venta` date DEFAULT NULL,
  `total_venta` double(8,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `clientes_taller`
--

INSERT INTO `clientes_taller` (`id_cliente_taller`, `documento`, `datos`, `direccion`, `atencion`, `telefono`, `telefono2`, `email`, `id_empresa`, `ultima_venta`, `total_venta`) VALUES
(16, '77425200', 'EMER RODRIGO YARLEQUE ZAPATA', '', '', NULL, NULL, NULL, 12, NULL, NULL),
(19, '20601212472', 'LIM KIT CORPORACION E.I.R.L.', 'OTR. SANT A CLARA MZA. E1 LOTE. 1 A.V. CENTRO POBLADO PRIMERO DE LIMA LIMA ATE', '', NULL, NULL, NULL, 12, NULL, NULL),
(20, '20609630818', 'SAMURAI S.A.C.', '', '', NULL, NULL, NULL, 12, NULL, NULL),
(21, '77426200', 'BRENDY YOSELY ZAPATA TORRES', '', '', NULL, NULL, NULL, 12, NULL, NULL),
(22, '77423200', 'RENINGER AMASIFUEN CACHIQUE', '', '', NULL, NULL, NULL, 12, NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente_venta`
--

CREATE TABLE `cliente_venta` (
  `id_cliente` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id_empresa` int(11) NOT NULL,
  `ruc` varchar(11) DEFAULT NULL,
  `razon_social` varchar(245) DEFAULT NULL,
  `comercial` varchar(245) NOT NULL,
  `cod_sucursal` varchar(4) DEFAULT NULL,
  `direccion` varchar(245) DEFAULT NULL,
  `email` varchar(145) DEFAULT NULL,
  `telefono` varchar(30) DEFAULT NULL,
  `estado` char(1) DEFAULT NULL,
  `password` varchar(45) DEFAULT NULL,
  `user_sol` varchar(45) DEFAULT NULL,
  `clave_sol` varchar(45) DEFAULT NULL,
  `logo` varchar(200) DEFAULT NULL,
  `ubigeo` varchar(6) DEFAULT NULL,
  `distrito` varchar(45) DEFAULT NULL,
  `provincia` varchar(45) DEFAULT NULL,
  `departamento` varchar(45) DEFAULT NULL,
  `tipo_impresion` char(1) DEFAULT NULL,
  `modo` varchar(50) DEFAULT NULL,
  `igv` double(10,2) DEFAULT 0.18,
  `propaganda` varchar(250) DEFAULT NULL,
  `telefono2` varchar(30) DEFAULT NULL,
  `telefono3` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id_empresa`, `ruc`, `razon_social`, `comercial`, `cod_sucursal`, `direccion`, `email`, `telefono`, `estado`, `password`, `user_sol`, `clave_sol`, `logo`, `ubigeo`, `distrito`, `provincia`, `departamento`, `tipo_impresion`, `modo`, `igv`, `propaganda`, `telefono2`, `telefono3`) VALUES
(12, '20538381978', 'COMERCIAL & INDUSTRIAL J. V. C. S.A.C.', 'BIKER IMPORT TRADING S.A.C', NULL, 'JAVIER PRADO ESTE 8402, LIMA – LIMA – ATE\n', ' ventas@industriajvcsac.com', ' 01 7489599', '1', NULL, 'BIKERIM1', 'Biker123', 'nUmdN40McVy2i1IZUthiXjhcyOSra7IEmu3sDwf3ZBcixKbYwQjxwR4KpF09xyaWsSxUWAtSQH4AXhc0.png', '040101', 'PUEBLO LIBRE', 'LIMA', 'LIMA', NULL, 'beta', 0.18, '', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `modulo_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `icono` varchar(50) DEFAULT NULL,
  `ruta` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `modulos`
--

INSERT INTO `modulos` (`modulo_id`, `nombre`, `descripcion`, `icono`, `ruta`) VALUES
(1, 'DASHBOARD', 'Panel principal del sistema', 'ri-dashboard-3-line', '/jvc'),
(2, 'FACTURACIÓN', 'Gestión de facturación', 'ri-bill-line', '/jvc/ventas'),
(3, 'COTIZACIONES', 'Gestión de cotizaciones', 'ri-file-list-3-line', '/jvc/cotizaciones'),
(4, 'CUENTAS POR COBRAR', 'Gestión de cuentas por cobrar', 'ri-bank-card-line', '/jvc/cobranzas'),
(5, 'CUENTAS POR PAGAR', 'Gestión de cuentas por pagar', 'ri-wallet-3-line', '/jvc/pagos'),
(6, 'CAJAS', 'Gestión de cajas', 'ri-safe-2-line', '/jvc/cajaRegistros'),
(7, 'ORDEN DE COMPRA', 'Gestión de compras', 'ri-shopping-cart-2-line', '/jvc/compras'),
(8, 'ALMACÉN', 'Gestión de almacén', 'ri-archive-line', '/jvc/almacen/productos'),
(9, 'ORDEN DE TRABAJO', 'Gestión de órdenes de trabajo', 'ri-clipboard-line', '/jvc/preAlerta'),
(10, 'ORDEN DE SERVICIO', 'Gestión de órdenes de servicio', 'ri-briefcase-4-line', '/jvc/servicio/prealerta'),
(11, 'NUMERO DE SERIES', 'Gestión de números de series', 'ri-barcode-box-line', '/jvc/numeroSeries'),
(12, 'GARANTÍA', 'Gestión de garantías', 'ri-shield-check-line', '/jvc/garantia'),
(13, 'TALLER', 'Gestión de taller', 'ri-tools-line', '/jvc/taller'),
(14, 'COTIZACIONES TALLER', 'Gestión de cotizaciones de taller', 'ri-file-list-3-line', '/jvc/taller/coti/view'),
(15, 'CLIENTES', 'Gestión de clientes', 'ri-team-line', '/jvc/clientes'),
(16, 'USUARIOS', 'Gestión de usuarios', 'ri-user-settings-line', '/jvc/usuarios'),
(18, 'DOCUMENTOS', 'Gestión de documentos del sistema', 'ri-file-text-line', '/jvc/documentos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `rol_id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `ver_precios` tinyint(1) NOT NULL DEFAULT 1,
  `puede_eliminar` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`rol_id`, `nombre`, `ver_precios`, `puede_eliminar`) VALUES
(1, 'ADMIN', 1, 1),
(2, 'USUARIO', 1, 1),
(3, 'VENDEDOR', 1, 1),
(4, 'CAJERO', 1, 1),
(5, 'CONTADOR', 1, 1),
(6, 'ALMACEN', 1, 1),
(7, 'ORDEN TRABAJO', 0, 0),
(8, 'ORDEN SERVICIO', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_modulo`
--

CREATE TABLE `rol_modulo` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `modulo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `rol_modulo`
--

INSERT INTO `rol_modulo` (`id`, `rol_id`, `modulo_id`) VALUES
(69, 6, 8),
(104, 1, 1),
(105, 1, 2),
(106, 1, 3),
(107, 1, 4),
(108, 1, 5),
(109, 1, 6),
(110, 1, 7),
(111, 1, 8),
(112, 1, 9),
(113, 1, 10),
(114, 1, 11),
(115, 1, 12),
(116, 1, 13),
(117, 1, 14),
(118, 1, 15),
(119, 1, 16),
(120, 2, 1),
(121, 2, 10),
(122, 2, 13),
(127, 4, 2),
(128, 4, 15),
(132, 8, 13),
(133, 8, 14),
(235, 7, 3),
(236, 7, 13),
(237, 7, 14),
(238, 7, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_submodulo`
--

CREATE TABLE `rol_submodulo` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `submodulo_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol_submodulo`
--

INSERT INTO `rol_submodulo` (`id`, `rol_id`, `submodulo_id`) VALUES
(16, 1, 2),
(17, 1, 3),
(18, 1, 1),
(19, 1, 5),
(20, 1, 4),
(21, 1, 8),
(22, 1, 6),
(23, 1, 7),
(24, 1, 9),
(25, 1, 11),
(26, 1, 10),
(27, 2, 11),
(28, 4, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rubros`
--

CREATE TABLE `rubros` (
  `id_rubro` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `estado` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `rubros`
--

INSERT INTO `rubros` (`id_rubro`, `nombre`, `id_empresa`, `estado`) VALUES
(1, 'SDC', 12, 0),
(2, 'SDCSDA', 12, 0),
(3, 'DCSDAC', 12, 0),
(4, 'SCACSDASDC222', 12, 0),
(5, 'WQWQ', 12, 0),
(6, 'XCBF', 12, 0),
(7, 'WEDFSAVC', 12, 1),
(8, 'EMEW', 12, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subcategorias_repuestos`
--

CREATE TABLE `subcategorias_repuestos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `categoria_id` int(11) NOT NULL,
  `creado_el` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `subcategorias_repuestos`
--

INSERT INTO `subcategorias_repuestos` (`id`, `nombre`, `categoria_id`, `creado_el`) VALUES
(2, 'msnv dsn vo ', 6, '2025-03-13 13:37:58'),
(3, 'safasdfas', 5, '2025-03-13 13:57:04'),
(4, 'helloowedfs', 5, '2025-03-13 14:07:26'),
(5, 'zxczx<z', 7, '2025-05-01 04:15:49'),
(6, 'sadsadcf', 5, '2025-05-01 04:17:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `submodulos`
--

CREATE TABLE `submodulos` (
  `submodulo_id` int(11) NOT NULL,
  `modulo_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `ruta` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `submodulos`
--

INSERT INTO `submodulos` (`submodulo_id`, `modulo_id`, `nombre`, `descripcion`, `ruta`) VALUES
(1, 2, 'Ventas', 'Gestión de ventas', '/jvc/ventas'),
(2, 2, 'Guías Remisión', 'Gestión de guías de remisión', '/jvc/guias/remision'),
(3, 2, 'Notas Electrónicas', 'Gestión de notas electrónicas', '/jvc/nota/electronica/lista'),
(4, 6, 'Registros', 'Registros de caja', '/jvc/cajaRegistros'),
(5, 6, 'Caja Chica', 'Gestión de caja chica', '/jvc/caja/flujo'),
(6, 8, 'Kardex', 'Gestión de kardex', '/jvc/almacen/productos'),
(7, 8, 'Repuestos', 'Gestión de repuestos', '/jvc/orden/repuestos'),
(8, 8, 'Intercambio de productos', 'Gestión de intercambio de productos', '/jvc/almacen/intercambio/productos'),
(9, 9, 'Pre alerta', 'Gestión de pre alertas', '/jvc/preAlerta'),
(10, 10, 'Pre alerta', 'Gestión de pre alertas de servicio', '/jvc/servicio/prealerta'),
(11, 10, 'Gestión de activos', 'Gestión de activos', '/jvc/gestion/activos'),
(18, 18, 'Ficha técnica', 'Gestión de fichas técnicas', '/jvc/documentos'),
(19, 18, 'Informe', 'Gestión de informes', '/jvc/documentos/informe'),
(20, 18, 'Cartas', 'Gestión de cartas', '/jvc/documentos/cartas'),
(21, 18, 'Constancias', 'Gestión de constancias', '/jvc/documentos/constancias'),
(22, 18, 'Archivos internos', 'Gestión de archivos internos', '/jvc/documentos/archivos/internos'),
(23, 18, 'Otros', 'Gestión de otros documentos', '/jvc/documentos/otros');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `usuario_id` int(11) NOT NULL,
  `id_empresa` int(11) DEFAULT NULL,
  `id_rol` int(11) DEFAULT NULL,
  `num_doc` varchar(20) DEFAULT NULL,
  `usuario` varchar(200) DEFAULT NULL,
  `clave` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `nombres` varchar(200) DEFAULT NULL,
  `apellidos` varchar(200) DEFAULT NULL,
  `rubro` varchar(100) DEFAULT NULL,
  `sucursal` int(11) DEFAULT NULL,
  `telefono` varchar(100) DEFAULT NULL,
  `token_reset` varchar(130) DEFAULT NULL,
  `estado` char(1) DEFAULT '1',
  `mensaje` varchar(220) DEFAULT NULL,
  `rotativo` smallint(6) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `id_empresa`, `id_rol`, `num_doc`, `usuario`, `clave`, `email`, `nombres`, `apellidos`, `rubro`, `sucursal`, `telefono`, `token_reset`, `estado`, `mensaje`, `rotativo`) VALUES
(60, 12, 1, '77425200', 'admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'rodrigoyarleque7@gmail.com', 'EMER RODRIGO', NULL, NULL, 1, '+51 993 321 920', NULL, '1', NULL, 0),
(61, 12, 7, '654654561', 'testuser', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'rodrigoyarleque7@gmail.com', 'sadfvdsavsa', NULL, NULL, 1, '67876876', NULL, '1', NULL, 0),
(62, 12, 8, '7742520011', 'testuser1', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'rodrigoyarleque7@gmail.com', 'pers', NULL, NULL, 1, '+51 993 321 920', NULL, '1', NULL, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id_cliente`) USING BTREE,
  ADD KEY `fk_clientes_empresas_idx` (`id_empresa`) USING BTREE,
  ADD KEY `fk_cliente_rubro` (`id_rubro`);

--
-- Indices de la tabla `clientes_taller`
--
ALTER TABLE `clientes_taller`
  ADD PRIMARY KEY (`id_cliente_taller`) USING BTREE,
  ADD KEY `fk_clientes_taller_empresas_idx` (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `cliente_venta`
--
ALTER TABLE `cliente_venta`
  ADD PRIMARY KEY (`id_cliente`) USING BTREE;

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id_empresa`) USING BTREE;

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`modulo_id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`rol_id`) USING BTREE;

--
-- Indices de la tabla `rol_modulo`
--
ALTER TABLE `rol_modulo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rol_id` (`rol_id`),
  ADD KEY `modulo_id` (`modulo_id`);

--
-- Indices de la tabla `rol_submodulo`
--
ALTER TABLE `rol_submodulo`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rol_submodulo_rol` (`rol_id`),
  ADD KEY `fk_rol_submodulo_submodulo` (`submodulo_id`);

--
-- Indices de la tabla `rubros`
--
ALTER TABLE `rubros`
  ADD PRIMARY KEY (`id_rubro`),
  ADD KEY `fk_rubro_empresa` (`id_empresa`);

--
-- Indices de la tabla `subcategorias_repuestos`
--
ALTER TABLE `subcategorias_repuestos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subcategoria_categoria` (`categoria_id`);

--
-- Indices de la tabla `submodulos`
--
ALTER TABLE `submodulos`
  ADD PRIMARY KEY (`submodulo_id`),
  ADD KEY `fk_submodulo_modulo` (`modulo_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`usuario_id`) USING BTREE,
  ADD KEY `id_empresa` (`id_empresa`) USING BTREE,
  ADD KEY `id_rol` (`id_rol`) USING BTREE;

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `clientes_taller`
--
ALTER TABLE `clientes_taller`
  MODIFY `id_cliente_taller` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `cliente_venta`
--
ALTER TABLE `cliente_venta`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `modulo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `rol_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `rol_modulo`
--
ALTER TABLE `rol_modulo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=239;

--
-- AUTO_INCREMENT de la tabla `rol_submodulo`
--
ALTER TABLE `rol_submodulo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- AUTO_INCREMENT de la tabla `rubros`
--
ALTER TABLE `rubros`
  MODIFY `id_rubro` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `subcategorias_repuestos`
--
ALTER TABLE `subcategorias_repuestos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `submodulos`
--
ALTER TABLE `submodulos`
  MODIFY `submodulo_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `rol_modulo`
--
ALTER TABLE `rol_modulo`
  ADD CONSTRAINT `rol_modulo_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`),
  ADD CONSTRAINT `rol_modulo_ibfk_2` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`modulo_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `rol_submodulo`
--
ALTER TABLE `rol_submodulo`
  ADD CONSTRAINT `fk_rol_submodulo_submodulo` FOREIGN KEY (`submodulo_id`) REFERENCES `submodulos` (`submodulo_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `subcategorias_repuestos`
--
ALTER TABLE `subcategorias_repuestos`
  ADD CONSTRAINT `fk_subcategoria_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_repuestos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `submodulos`
--
ALTER TABLE `submodulos`
  ADD CONSTRAINT `fk_submodulo_modulo` FOREIGN KEY (`modulo_id`) REFERENCES `modulos` (`modulo_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
