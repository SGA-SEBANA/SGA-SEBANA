-- --------------------------------------------------------
-- MIGRACIÓN 002: Módulo de Afiliados y Correcciones Core
-- Fecha: 30/01/2026
-- Autor: Desarrollador (Tu Nombre)
-- --------------------------------------------------------

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- 1. Asegurar tablas Core faltantes (Login/Bitácora)
-- --------------------------------------------------------

-- Tabla de Roles (Si no existe, se crea para evitar errores de login)
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insertar roles básicos si no existen
INSERT IGNORE INTO `roles` (`id`, `nombre`, `descripcion`) VALUES
(1, 'admin', 'Administrador total del sistema'),
(2, 'editor', 'Usuario con permisos de edición');

-- Tabla de Bitácora (Requerida por el sistema de logs)
CREATE TABLE IF NOT EXISTS `bitacora` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `accion` varchar(255) NOT NULL,
  `tabla_afectada` varchar(100) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `detalles` text DEFAULT NULL,
  `ip_origen` varchar(45) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- 2. Tabla del Nuevo Módulo: AFILIADOS
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `afiliados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(255) NOT NULL,
  `cedula` varchar(20) NOT NULL,
  `numero_empleado` varchar(20) NOT NULL,
  `genero` enum('Masculino','Femenino') NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `oficina_nombre` varchar(150) DEFAULT NULL,
  `oficina_numero` varchar(50) DEFAULT NULL,
  `categoria` varchar(100) DEFAULT NULL,
  `email_institucional` varchar(150) DEFAULT NULL,
  `celular_personal` varchar(50) DEFAULT NULL,
  `estado` enum('activo','inactivo') DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `cedula` (`cedula`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

COMMIT;