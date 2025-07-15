-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-07-2025 a las 20:39:02
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
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id_cliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
