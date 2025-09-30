-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-09-2025 a las 18:46:33
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
(8, 'ORDEN SERVICIO', 0, 0),
(9, 'TALLER', 0, 1),
(10, 'hello', 0, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol_permisos`
--

CREATE TABLE `rol_permisos` (
  `id` int(11) NOT NULL,
  `rol_id` int(11) NOT NULL,
  `modulo_id` varchar(50) NOT NULL,
  `submodulo_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `rol_permisos`
--

INSERT INTO `rol_permisos` (`id`, `rol_id`, `modulo_id`, `submodulo_id`) VALUES
(8, 1, 'almacen', NULL),
(6, 1, 'cajas', NULL),
(15, 1, 'clientes', NULL),
(3, 1, 'cotizaciones', NULL),
(14, 1, 'cotizaciones_taller', NULL),
(4, 1, 'cuentas_cobrar', NULL),
(5, 1, 'cuentas_pagar', NULL),
(1, 1, 'dashboard', NULL),
(17, 1, 'documentos', NULL),
(2, 1, 'facturacion', NULL),
(12, 1, 'garantia', NULL),
(11, 1, 'numero_series', NULL),
(7, 1, 'orden_compra', NULL),
(10, 1, 'orden_servicio', NULL),
(9, 1, 'orden_trabajo', NULL),
(13, 1, 'taller', NULL),
(16, 1, 'usuarios', NULL),
(18, 2, 'dashboard', NULL),
(19, 2, 'orden_servicio', NULL),
(20, 2, 'taller', NULL),
(36, 3, 'facturacion', NULL),
(38, 3, 'facturacion', 'guias_remision'),
(39, 3, 'facturacion', 'notas_electronicas'),
(37, 3, 'facturacion', 'ventas'),
(22, 4, 'clientes', NULL),
(21, 4, 'facturacion', 'ventas'),
(23, 6, 'almacen', NULL),
(27, 7, 'clientes', NULL),
(24, 7, 'cotizaciones', NULL),
(26, 7, 'cotizaciones_taller', NULL),
(25, 7, 'taller', NULL),
(29, 8, 'cotizaciones_taller', NULL),
(28, 8, 'taller', NULL),
(35, 9, 'cotizaciones_taller', NULL),
(34, 9, 'taller', NULL),
(33, 10, 'garantia', NULL);

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
  `rotativo` smallint(6) DEFAULT 0,
  `foto_perfil` varchar(255) DEFAULT NULL COMMENT 'Ruta de la foto de perfil del usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci ROW_FORMAT=DYNAMIC;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`usuario_id`, `id_empresa`, `id_rol`, `num_doc`, `usuario`, `clave`, `email`, `nombres`, `apellidos`, `rubro`, `sucursal`, `telefono`, `token_reset`, `estado`, `mensaje`, `rotativo`, `foto_perfil`) VALUES
(60, 12, 1, '77425200', 'admin', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'rodrigoyarleque7@gmail.com', 'EMER RODRIGO', NULL, NULL, 1, '+51 993 321 920', NULL, '1', NULL, 0, 'public/uploads/usuarios/usuario_1758944084_68d75b544eea8.jpeg'),
(68, 12, 9, '77412692', 'testuser2', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'rodrigoyarleque7@gmail.com', 'ANA KIMBERLY', NULL, NULL, 1, '993321920', NULL, '1', NULL, 0, 'public/uploads/usuarios/usuario_1758944366_68d75c6e0cd03.jpg');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`rol_id`) USING BTREE;

--
-- Indices de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_permiso` (`rol_id`,`modulo_id`,`submodulo_id`);

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
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `rol_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `usuario_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `rol_permisos`
--
ALTER TABLE `rol_permisos`
  ADD CONSTRAINT `rol_permisos_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`rol_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
 