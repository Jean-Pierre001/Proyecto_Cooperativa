-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-07-2025 a las 17:11:26
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

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
-- Estructura de tabla para la tabla `backups_log`
--

CREATE TABLE `backups_log` (
  `id` int(11) NOT NULL,
  `backup_type` varchar(50) NOT NULL,
  `backup_time` datetime DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL,
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `backups_log`
--

INSERT INTO `backups_log` (`id`, `backup_type`, `backup_time`, `status`, `message`) VALUES
(1, 'total', '2025-07-15 19:54:46', 'success', 'Respaldo total completado correctamente.'),
(2, 'db', '2025-07-15 19:54:52', 'success', 'Backup base de datos guardado en Dropbox.'),
(3, 'total', '2025-07-15 19:59:22', 'success', 'Respaldo total completado correctamente.'),
(4, 'db', '2025-07-15 20:02:02', 'success', 'Backup base de datos guardado en Dropbox.'),
(5, 'db', '2025-07-15 20:02:10', 'success', 'Backup base de datos guardado en Dropbox.'),
(6, 'total', '2025-07-15 20:02:16', 'success', 'Respaldo total completado correctamente.'),
(7, 'total', '2025-07-15 20:02:19', 'success', 'Respaldo total completado correctamente.'),
(8, 'db', '2025-07-15 20:02:22', 'success', 'Backup base de datos guardado en Dropbox.'),
(9, 'total', '2025-07-15 20:02:40', 'success', 'Respaldo total completado correctamente.'),
(10, 'total', '2025-07-15 20:02:43', 'success', 'Respaldo total completado correctamente.'),
(11, 'total', '2025-07-15 20:03:43', 'started', 'Respaldo total iniciado.'),
(12, 'total', '2025-07-15 20:03:46', 'success', 'Respaldo total completado.'),
(13, 'total', '2025-07-15 20:03:46', 'started', 'Respaldo total iniciado.'),
(14, 'total', '2025-07-15 20:03:49', 'success', 'Respaldo total completado.'),
(15, 'total', '2025-07-15 20:03:49', 'started', 'Respaldo total iniciado.'),
(16, 'total', '2025-07-15 20:03:52', 'success', 'Respaldo total completado.'),
(17, 'total', '2025-07-15 20:03:52', 'started', 'Respaldo total iniciado.'),
(18, 'total', '2025-07-15 20:03:55', 'success', 'Respaldo total completado.');

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
(22, 'fdsfsdfs', 'folders/fdsfsdfs', '2025-07-16', 'fdsfsdfs');

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
  `member_number` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `cuil` varchar(30) NOT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `entry_date` date DEFAULT NULL,
  `exit_date` date DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `work_site` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `members`
--

INSERT INTO `members` (`id`, `member_number`, `name`, `cuil`, `phone`, `address`, `email`, `entry_date`, `exit_date`, `status`, `work_site`) VALUES
(53, 1002, 'María Gómez', '27-87654321-5', '1133445566', 'Av. Siempreviva 742', 'mariag@example.com', '2016-05-15', '2021-09-30', 'inactivo', 'Obra B'),
(54, 1003, 'Carlos Ruiz', '23-45678901-7', '1144556677', 'Los Álamos 222', 'carlosr@example.com', '2018-08-20', NULL, 'activo', 'Obra C'),
(55, 1004, 'Lucía Fernández', '27-11223344-1', '1155667788', 'Mitre 789', 'luciaf@example.com', '2019-01-11', NULL, 'activo', 'Obra D'),
(56, 1005, 'Pedro Sosa', '20-99887766-2', '1166778899', 'Belgrano 321', 'pedros@example.com', '2014-11-02', '2020-12-01', 'inactivo', 'Obra E'),
(57, 1006, 'Ana Torres', '27-33445566-0', '1177889900', 'San Martín 567', 'anatorres@example.com', '2020-07-19', NULL, 'activo', 'Obra F'),
(58, 1007, 'Ricardo López', '20-44556677-3', '1188990011', 'Perón 1010', 'rlopez@example.com', '2017-09-03', '2023-01-20', 'inactivo', 'Obra G'),
(59, 1008, 'Laura Díaz', '27-55667788-4', '1199001122', 'Sarmiento 456', 'laurad@example.com', '2021-10-10', NULL, 'activo', 'Obra H'),
(60, 1009, 'Federico Méndez', '20-66778899-5', '1200112233', 'Alsina 678', 'fede.m@example.com', '2013-06-05', '2019-04-25', 'inactivo', 'Obra I'),
(61, 1010, 'Silvana Rojas', '27-77889900-6', '1211223344', '9 de Julio 100', 'silvana.r@example.com', '2016-02-28', NULL, 'activo', 'Obra J'),
(62, 1011, 'Tomás Herrera', '20-11224455-7', '1222334455', 'Colón 202', 't.herrera@example.com', '2015-08-14', '2022-02-28', 'inactivo', 'Obra K'),
(63, 1012, 'Florencia Acosta', '27-22335566-8', '1233445566', 'Rivadavia 303', 'flor.a@example.com', '2019-12-01', NULL, 'activo', 'Obra A'),
(64, 1013, 'Jorge Molina', '20-33446677-9', '1244556677', 'España 404', 'jmolina@example.com', '2018-10-30', NULL, 'activo', 'Obra B'),
(65, 1014, 'Paula Suárez', '27-44557788-0', '1255667788', 'Italia 505', 'paulas@example.com', '2014-03-22', '2020-11-15', 'inactivo', 'Obra C'),
(66, 1015, 'Esteban Navarro', '20-55668899-1', '1266778899', 'Urquiza 606', 'enavarro@example.com', '2021-06-18', NULL, 'activo', 'Obra D'),
(67, 1016, 'Julieta Medina', '27-66779900-2', '1277889900', 'Alsina 707', 'julieta.m@example.com', '2020-04-12', NULL, 'activo', 'Obra E'),
(68, 1017, 'Martín Leiva', '20-77880011-3', '1288990011', 'Laprida 808', 'martinl@example.com', '2017-07-01', '2022-08-01', 'inactivo', 'Obra F'),
(69, 1018, 'Andrea Vargas', '27-88991122-4', '1299001122', 'Catamarca 909', 'andreav@example.com', '2018-01-23', NULL, 'activo', 'Obra G'),
(70, 1019, 'Ramiro Castro', '20-99002233-5', '1300112233', 'Moreno 111', 'ramiroc@example.com', '2016-11-11', '2021-04-10', 'inactivo', 'Obra H'),
(71, 1020, 'Mónica Paredes', '27-10111213-6', '1311223344', 'Lavalle 222', 'monicap@example.com', '2015-02-17', NULL, 'activo', 'Obra I'),
(72, 1021, 'Santiago Vera', '20-12131415-7', '1322334455', 'Avellaneda 333', 'sveras@example.com', '2013-09-09', '2018-10-09', 'inactivo', 'Obra J'),
(73, 1022, 'Gabriela Chávez', '27-14151617-8', '1333445566', 'Dorrego 444', 'gchavez@example.com', '2022-01-01', NULL, 'activo', 'Obra K'),
(74, 1023, 'Diego Funes', '20-16171819-9', '1344556677', 'Santa Fe 555', 'dfunes@example.com', '2019-08-08', NULL, 'activo', 'Obra A'),
(75, 1024, 'Cecilia Blanco', '27-18192021-0', '1355667788', 'Entre Ríos 666', 'ceci.b@example.com', '2020-03-15', NULL, 'activo', 'Obra B'),
(76, 1025, 'Leandro Romero', '20-20212223-1', '1366778899', 'Chacabuco 777', 'lromero@example.com', '2017-12-25', '2022-12-31', 'inactivo', 'Obra C'),
(77, 1026, 'Verónica Muñoz', '27-22232425-2', '1377889900', 'Independencia 888', 'vmunoz@example.com', '2021-11-11', NULL, 'activo', 'Obra D'),
(78, 1027, 'Axel Ojeda', '20-24252627-3', '1388990011', 'Brandsen 999', 'axelo@example.com', '2016-04-04', '2023-05-15', 'inactivo', 'Obra E'),
(79, 1028, 'Milagros Vázquez', '27-26272829-4', '1399001122', 'Rawson 101', 'mila.v@example.com', '2022-06-30', NULL, 'activo', 'Obra F'),
(80, 1029, 'Rodrigo Ayala', '20-28293031-5', '1400112233', 'Azcuénaga 202', 'rayala@example.com', '2020-09-09', NULL, 'activo', 'Obra G'),
(81, 1030, 'Valentina Rivas', '27-30313233-6', '1411223344', 'Castelli 303', 'valen.r@example.com', '2018-02-02', '2024-01-10', 'inactivo', 'Obra H'),
(88, 32, 'Jean Pierre Lobos', '34-24324323-4', '02920541084', 'Luis py 366', 'Agustin500cm@gmail.com', '2025-07-16', NULL, 'activo', 'costanera');

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
(85, 88, 'costanera/jean_pierre_lobos/1752591018_1752453988_consejo_escolar_fuera (2).jpg', '2025-07-15 14:50:18'),
(86, 88, 'costanera/jean_pierre_lobos/1752591018_1752339264_papa.png', '2025-07-15 14:50:18'),
(87, 88, 'costanera/jean_pierre_lobos/1752591018_papa.png', '2025-07-15 14:50:18'),
(88, 88, 'costanera/jean_pierre_lobos/1752591018_Captura de pantalla 2025-07-09 205516 (1).png', '2025-07-15 14:50:18'),
(89, 88, 'costanera/jean_pierre_lobos/1752591018_Captura de pantalla 2025-07-09 205516.png', '2025-07-15 14:50:18');

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
  `folder_system_name` varchar(255) NOT NULL,
  `deleted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(2, 'admin@admin', '$2y$10$8/Pd.egaWATo4/L7EE81aOwnAn77wx9dXJSWxy.X2pJAfaEqzxFKm', 1, 'Admin', 'User', 'Admin Address', '1234567890', '6874f0c1cfd95.jpg', '2025-07-13');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `backups_log`
--
ALTER TABLE `backups_log`
  ADD PRIMARY KEY (`id`);

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
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT de la tabla `backups_log`
--
ALTER TABLE `backups_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `folders`
--
ALTER TABLE `folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT de la tabla `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `members`
--
ALTER TABLE `members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT de la tabla `member_documents`
--
ALTER TABLE `member_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT de la tabla `trash`
--
ALTER TABLE `trash`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
