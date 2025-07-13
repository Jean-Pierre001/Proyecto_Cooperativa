-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-07-2025 a las 22:39:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `cooperativa`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `folders`
--

CREATE TABLE `folders` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `folder_path` varchar(255) NOT NULL,
  `created_on` date NOT NULL,
  `folder_system_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `folders`
--

INSERT INTO `folders` (`id`, `name`, `folder_path`, `created_on`, `folder_system_name`) VALUES
(3, 'hola', 'folders/hola', '2025-07-13', 'hola'),
(12, 'd', 'folders/d', '2025-07-13', 'd');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `ip`, `timestamp`) VALUES
(1, '::1', '2025-07-13 17:05:04');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `members`
--

CREATE TABLE `members` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `dni` varchar(50) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `contributions` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `members`
--

INSERT INTO `members` (`id`, `name`, `dni`, `phone`, `email`, `address`, `entry_date`, `status`, `contributions`) VALUES
(1, 'Jean Pierre Lobos', '48843820', '02920541043', 'Agustin500cm@gmail.com', 'Luis py 366', '2025-07-17', 'active', 20000.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `member_documents`
--

CREATE TABLE `member_documents` (
  `id` int(11) NOT NULL,
  `member_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `member_documents`
--

INSERT INTO `member_documents` (`id`, `member_id`, `file_path`, `uploaded_on`) VALUES
(1, 1, 'jean_pierre_lobos/1752431398_papa.png', '2025-07-13 18:29:58'),
(2, 1, 'jean_pierre_lobos/1752431398_Captura de pantalla 2025-07-09 205516 (1).png', '2025-07-13 18:29:58'),
(3, 1, 'jean_pierre_lobos/1752431398_Captura de pantalla 2025-07-09 205516.png', '2025-07-13 18:29:58'),
(4, 1, 'jean_pierre_lobos/1752431398_consejo_escolar_fuera (2).jpg', '2025-07-13 18:29:58'),
(5, 1, 'jean_pierre_lobos/1752431398_consejo_escolar_fuera (1).jpg', '2025-07-13 18:29:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trash`
--

CREATE TABLE `trash` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `folder_path` varchar(255) NOT NULL,
  `deleted_on` date NOT NULL,
  `original_id` int(11) NOT NULL,
  `folder_system_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trash`
--

INSERT INTO `trash` (`id`, `name`, `folder_path`, `deleted_on`, `original_id`, `folder_system_name`) VALUES
(2, 'd', 'trash/d', '2025-07-13', 2, 'd'),
(3, 'hgfhfg', 'trash/hgfhfg', '2025-07-13', 11, 'hgfhfg'),
(4, 'fgdgdf', 'trash/fgdgdf', '2025-07-13', 10, 'fgdgdf'),
(5, 'fgdhgdf', 'trash/fgdhgdf', '2025-07-13', 9, 'fgdhgdf'),
(6, 'fdsgdf', 'trash/fdsgdf', '2025-07-13', 8, 'fdsgdf'),
(7, 'asdads', 'trash/asdads', '2025-07-13', 7, 'asdads'),
(8, 'fds', 'trash/fds', '2025-07-13', 6, 'fds'),
(9, 'dfs', 'trash/dfs', '2025-07-13', 5, 'dfs'),
(10, 'fggg', 'trash/fggg', '2025-07-13', 4, 'fggg');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `type`, `first_name`, `last_name`, `address`, `contact_info`, `photo`, `created_on`) VALUES
(1, 'agustin500@gmail.com', '$2y$10$mN/Xn.8jlFheeevuX/UH4e1Li9H.qcDyJhCcW.RObUSnN7deDAuD6', 1, 'Admin', 'User', 'Admin Address', '1234567890', '', '2025-07-13');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `folders`
--
ALTER TABLE `folders`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dni` (`dni`);

--
-- Indices de la tabla `member_documents`
--
ALTER TABLE `member_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `member_id` (`member_id`);

--
-- Indices de la tabla `trash`
--
ALTER TABLE `trash`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `member_documents`
--
ALTER TABLE `member_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `trash`
--
ALTER TABLE `trash`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `member_documents`
--
ALTER TABLE `member_documents`
  ADD CONSTRAINT `member_documents_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
