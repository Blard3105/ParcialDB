-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-04-2025 a las 18:59:59
-- Versión del servidor: 10.4.22-MariaDB
-- Versión de PHP: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `clinica_empleados_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

Create database clinica_empleados_db;
use clinica_empleados_db;

CREATE TABLE `empleados` (
  `id_empleado` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dni` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Documento Nacional de Identidad o similar',
  `fecha_nacimiento` date DEFAULT NULL,
  `genero` enum('Masculino','Femenino','Otro') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_ingreso` date NOT NULL COMMENT 'Fecha de inicio en la clínica',
  `salario` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Salario base actual',
  `id_cargo` int(11) NOT NULL COMMENT 'FK a la tabla Cargos',
  `id_departamento` int(11) DEFAULT NULL COMMENT 'FK a la tabla Departamentos (opcional)',
  `estado` enum('Activo','Inactivo') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Activo' COMMENT 'Estado del empleado (para eliminación lógica)',
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `fecha_actualizacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabla central de empleados de la clínica';

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id_empleado`, `nombre`, `apellido`, `dni`, `fecha_nacimiento`, `genero`, `direccion`, `telefono`, `email`, `fecha_ingreso`, `salario`, `id_cargo`, `id_departamento`, `estado`, `fecha_creacion`, `fecha_actualizacion`) VALUES
(1, 'an', 'portilla', '10696651|', '2025-04-03', 'Masculino', 'Calle 30A # 28B - 51', '30518481', 'angello@gamil', '2020-02-20', '50000.00', 4, 1, 'Activo', '2025-04-09 16:25:32', '2025-04-09 16:35:46');

--
-- Disparadores `empleados`
--
DELIMITER $$
CREATE TRIGGER `salary_update_auditory` BEFORE UPDATE ON `empleados` FOR EACH ROW BEGIN
    IF NEW.estado = 'Activo' THEN
        INSERT INTO auditoriasalarios (fecha_cambio, id_empleado, salario_anterior, salario_nuevo)
        VALUES (NOW(), OLD.id_empleado, OLD.salario, NEW.salario);
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id_empleado`),
  ADD UNIQUE KEY `dni` (`dni`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_departamento` (`id_departamento`),
  ADD KEY `idx_empleado_nombre` (`nombre`,`apellido`),
  ADD KEY `idx_empleado_cargo` (`id_cargo`),
  ADD KEY `idx_empleado_estado` (`estado`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `empleados_ibfk_1` FOREIGN KEY (`id_cargo`) REFERENCES `cargos` (`id_cargo`) ON UPDATE CASCADE,
  ADD CONSTRAINT `empleados_ibfk_2` FOREIGN KEY (`id_departamento`) REFERENCES `departamentos` (`id_departamento`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
