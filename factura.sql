-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 192.168.5.205    Database: facturas
-- ------------------------------------------------------
-- Server version	8.0.41-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `asientos_contables`
--

DROP TABLE IF EXISTS `asientos_contables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asientos_contables` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `cuenta_id` int NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `debe` decimal(12,2) DEFAULT '0.00',
  `haber` decimal(12,2) DEFAULT '0.00',
  `gasto_id` int DEFAULT NULL,
  `factura_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cuenta_id` (`cuenta_id`),
  KEY `gasto_id` (`gasto_id`),
  KEY `factura_id` (`factura_id`),
  CONSTRAINT `asientos_contables_ibfk_1` FOREIGN KEY (`cuenta_id`) REFERENCES `cuentas_contables` (`numero_cuenta`),
  CONSTRAINT `asientos_contables_ibfk_2` FOREIGN KEY (`gasto_id`) REFERENCES `gastos` (`id`),
  CONSTRAINT `asientos_contables_ibfk_3` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asientos_contables`
--

LOCK TABLES `asientos_contables` WRITE;
/*!40000 ALTER TABLE `asientos_contables` DISABLE KEYS */;
INSERT INTO `asientos_contables` VALUES (30,'2025-02-24',40100,'Ingreso por factura',0.00,1862.10,NULL,63),(31,'2025-02-24',20500,'IVA por factura',0.00,297.94,NULL,63),(32,'2025-02-24',110105,'Ingreso por factura',1862.10,0.00,NULL,63),(33,'2025-02-24',110105,'IVA por factura',297.94,0.00,NULL,63);
/*!40000 ALTER TABLE `asientos_contables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `clientes_juridicos`
--

DROP TABLE IF EXISTS `clientes_juridicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `clientes_juridicos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rif` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` text COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `curso_id` int DEFAULT NULL,
  `cant_alumnos` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rif` (`rif`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `clientes_juridicos`
--

LOCK TABLES `clientes_juridicos` WRITE;
/*!40000 ALTER TABLE `clientes_juridicos` DISABLE KEYS */;
INSERT INTO `clientes_juridicos` VALUES (20,'prueba','J-30400858-9','prueba','042400000000','prueba@gmail.com',14,3);
/*!40000 ALTER TABLE `clientes_juridicos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `credit_notes`
--

DROP TABLE IF EXISTS `credit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `credit_notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cliente_id` int DEFAULT NULL,
  `producto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `credit_notes`
--

LOCK TABLES `credit_notes` WRITE;
/*!40000 ALTER TABLE `credit_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `credit_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuentas_contables`
--

DROP TABLE IF EXISTS `cuentas_contables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuentas_contables` (
  `numero_cuenta` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('Activo','Pasivo','Patrimonio Neto','Ingresos','Gastos') NOT NULL,
  `descripcion` text,
  PRIMARY KEY (`numero_cuenta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuentas_contables`
--

LOCK TABLES `cuentas_contables` WRITE;
/*!40000 ALTER TABLE `cuentas_contables` DISABLE KEYS */;
INSERT INTO `cuentas_contables` VALUES (1101,'Cuentas Corrientes Bancos','Activo','Cuentas corrientes en instituciones financieras'),(10100,'Caja','Activo','Efectivo en caja y billetes'),(10200,'Bancos','Activo','Cuentas corrientes bancarias'),(10300,'Cuentas por Cobrar','Activo','Deudas de clientes por ventas a crédito'),(10400,'Inventario','Activo','Existencias de mercaderías'),(10500,'Mobiliario','Activo','Muebles y equipos de oficina'),(10600,'Equipo de Computación','Activo','Ordenadores y equipos informáticos'),(10700,'Vehiculos','Activo','Vehículos de la empresa'),(10800,'Edificios','Activo','Inmuebles propiedad de la empresa'),(10900,'Depreciación Acumulada','Activo','Valor depreciado de activos fijos (cuenta complementaria)'),(20100,'Proveedores','Pasivo','Deudas con proveedores por compras a crédito'),(20200,'Cuentas por Pagar','Pasivo','Obligaciones pendientes de pago'),(20300,'Préstamos a Corto Plazo','Pasivo','Deudas financieras con vencimiento menor a 1 año'),(20400,'Hipotecas por Pagar','Pasivo','Préstamos garantizados con bienes inmuebles'),(20500,'Impuestos por Pagar','Pasivo','Obligaciones tributarias pendientes'),(30100,'Capital Social','Patrimonio Neto','Aportaciones de los socios o accionistas'),(30200,'Utilidades Retenidas','Patrimonio Neto','Ganancias acumuladas no distribuidas'),(30300,'Reservas','Patrimonio Neto','Reservas legales o estatutarias'),(40100,'Ventas','Ingresos','Ingresos por venta de bienes o servicios'),(40200,'Ingresos por Servicios','Ingresos','Ingresos por prestación de servicios'),(40300,'Ingresos Financieros','Ingresos','Intereses ganados en inversiones'),(50100,'Costos de Ventas','Gastos','Costo directo de los productos vendidos'),(50200,'Sueldos y Salarios','Gastos','Remuneraciones al personal'),(50300,'Alquileres','Gastos','Gastos de arrendamiento de locales'),(50400,'Servicios Públicos','Gastos','Agua, luz, teléfono, internet'),(50500,'Publicidad','Gastos','Gastos en marketing y promoción'),(50600,'Depreciación','Gastos','Pérdida de valor de activos fijos'),(50700,'Intereses','Gastos','Intereses pagados por préstamos'),(50800,'Impuestos','Gastos','Impuestos municipales y otros tributos'),(50900,'Gastos Generales','Gastos','Otros gastos operativos varios'),(110102,'Banco de Venezuela','Activo','Cuenta corriente en Banco de Venezuela'),(110103,'Banesco','Activo','Cuenta en Banesco'),(110104,'Mercantil','Activo','Cuenta en Mercantil'),(110105,'Banco Nacional de credito','Activo','Cuenta BNC'),(110106,'BBVA Provincial','Activo','Cuenta BBVA Provincial'),(110107,'Banco del Tesoro','Activo','Cuenta Banco del Tesoro'),(110108,'Bancaribe','Activo','Cuenta Bancaribe'),(110109,'Banco Exterior','Activo','Cuenta Banco Exterior'),(110116,'Bancamiga','Activo','Cuenta Bancamiga'),(110117,'Banco de Desarrollo BANDES','Activo','Cuenta Banco de Desarrollo BANDES'),(110118,'Banco Sofitasa','Activo','Cuenta Banco Sofitasa'),(110119,'Banco Plaza','Activo','Cuenta Banco Plaza'),(110120,'Mi Banco','Activo','Cuenta Mi Banco');
/*!40000 ALTER TABLE `cuentas_contables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuotas`
--

DROP TABLE IF EXISTS `cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuotas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `curso_id` int NOT NULL,
  `numero_cuota` int NOT NULL,
  `monto_cuota` decimal(10,2) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `curso_id` (`curso_id`),
  CONSTRAINT `cuotas_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuotas`
--

LOCK TABLES `cuotas` WRITE;
/*!40000 ALTER TABLE `cuotas` DISABLE KEYS */;
INSERT INTO `cuotas` VALUES (46,14,0,10.00,'2025-02-15'),(47,14,1,6.00,'2025-03-15'),(48,14,2,6.00,'2025-04-15'),(49,14,3,6.00,'2025-05-15'),(50,14,4,6.00,'2025-06-15'),(51,14,5,6.00,'2025-07-15');
/*!40000 ALTER TABLE `cuotas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cuotas_pendientes`
--

DROP TABLE IF EXISTS `cuotas_pendientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cuotas_pendientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `curso_id` int DEFAULT NULL,
  `cliente_id` varchar(45) DEFAULT NULL,
  `factura_id` int DEFAULT NULL,
  `fecha_pago` date DEFAULT NULL,
  `estado_pago` tinyint DEFAULT NULL,
  `monto` decimal(10,2) DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `numero_cuota` int DEFAULT NULL,
  `cant_alumnos` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `curso_id` (`curso_id`),
  KEY `factura_id` (`factura_id`),
  CONSTRAINT `cuotas_pendientes_ibfk_1` FOREIGN KEY (`curso_id`) REFERENCES `cursos` (`id`),
  CONSTRAINT `cuotas_pendientes_ibfk_3` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cuotas_pendientes`
--

LOCK TABLES `cuotas_pendientes` WRITE;
/*!40000 ALTER TABLE `cuotas_pendientes` DISABLE KEYS */;
INSERT INTO `cuotas_pendientes` VALUES (76,14,'123456789',NULL,NULL,0,10.00,'2025-02-15',0,NULL),(77,14,'123456789',NULL,NULL,0,6.00,'2025-03-15',1,NULL),(78,14,'123456789',NULL,NULL,0,6.00,'2025-04-15',2,NULL),(79,14,'123456789',NULL,NULL,0,6.00,'2025-05-15',3,NULL),(80,14,'123456789',NULL,NULL,0,6.00,'2025-06-15',4,NULL),(81,14,'123456789',NULL,NULL,0,6.00,'2025-07-15',5,NULL),(94,14,'J-30400858-9',63,'2025-02-24',1,30.00,'2025-02-15',0,3),(95,14,'J-30400858-9',NULL,NULL,0,18.00,'2025-03-15',1,3),(96,14,'J-30400858-9',NULL,NULL,0,18.00,'2025-04-15',2,3),(97,14,'J-30400858-9',NULL,NULL,0,18.00,'2025-05-15',3,3),(98,14,'J-30400858-9',NULL,NULL,0,18.00,'2025-06-15',4,3),(99,14,'J-30400858-9',NULL,NULL,0,18.00,'2025-07-15',5,3);
/*!40000 ALTER TABLE `cuotas_pendientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cursos`
--

DROP TABLE IF EXISTS `cursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cursos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(250) COLLATE utf8mb4_general_ci NOT NULL,
  `precio` int NOT NULL,
  `inscrip` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cursos`
--

LOCK TABLES `cursos` WRITE;
/*!40000 ALTER TABLE `cursos` DISABLE KEYS */;
INSERT INTO `cursos` VALUES (14,'programacion php','programacion php',40,10);
/*!40000 ALTER TABLE `cursos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `debit_notes`
--

DROP TABLE IF EXISTS `debit_notes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `debit_notes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cliente_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `debit_notes`
--

LOCK TABLES `debit_notes` WRITE;
/*!40000 ALTER TABLE `debit_notes` DISABLE KEYS */;
/*!40000 ALTER TABLE `debit_notes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `rif` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_empresa_estudiantes`
--

DROP TABLE IF EXISTS `factura_empresa_estudiantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_empresa_estudiantes` (
  `factura_id` int NOT NULL,
  `empresa_id` int NOT NULL,
  `estudiante_id` int NOT NULL,
  PRIMARY KEY (`factura_id`,`estudiante_id`),
  KEY `empresa_id` (`empresa_id`),
  KEY `estudiante_id` (`estudiante_id`),
  CONSTRAINT `factura_empresa_estudiantes_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`),
  CONSTRAINT `factura_empresa_estudiantes_ibfk_2` FOREIGN KEY (`empresa_id`) REFERENCES `empresa` (`id`),
  CONSTRAINT `factura_empresa_estudiantes_ibfk_3` FOREIGN KEY (`estudiante_id`) REFERENCES `students` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_empresa_estudiantes`
--

LOCK TABLES `factura_empresa_estudiantes` WRITE;
/*!40000 ALTER TABLE `factura_empresa_estudiantes` DISABLE KEYS */;
/*!40000 ALTER TABLE `factura_empresa_estudiantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `factura_productos`
--

DROP TABLE IF EXISTS `factura_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `factura_productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `factura_id` int NOT NULL,
  `producto` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `cantidad` int NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_id` (`factura_id`),
  CONSTRAINT `factura_productos_ibfk_1` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `factura_productos`
--

LOCK TABLES `factura_productos` WRITE;
/*!40000 ALTER TABLE `factura_productos` DISABLE KEYS */;
INSERT INTO `factura_productos` VALUES (56,63,'0',1,1862.10);
/*!40000 ALTER TABLE `factura_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `facturas`
--

DROP TABLE IF EXISTS `facturas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `facturas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nrocontol` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cliente_id` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `curso_id` int NOT NULL,
  `usuario` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `sub_total` decimal(10,2) DEFAULT NULL,
  `iva` decimal(10,0) DEFAULT NULL,
  `total_factura` decimal(10,2) DEFAULT NULL,
  `empresa_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `facturas`
--

LOCK TABLES `facturas` WRITE;
/*!40000 ALTER TABLE `facturas` DISABLE KEYS */;
INSERT INTO `facturas` VALUES (63,'7','J-30400858-9','2025-02-24 13:22:25',14,'administrador',1862.10,297,2160.00,NULL);
/*!40000 ALTER TABLE `facturas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gastos`
--

DROP TABLE IF EXISTS `gastos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gastos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `descripcion` varchar(255) NOT NULL,
  `cuenta_id` int NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gastos`
--

LOCK TABLES `gastos` WRITE;
/*!40000 ALTER TABLE `gastos` DISABLE KEYS */;
INSERT INTO `gastos` VALUES (1,'2025-02-18','eeeeeee',50400,7748.50,'2025-02-19 00:32:05');
/*!40000 ALTER TABLE `gastos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historico_reverso`
--

DROP TABLE IF EXISTS `historico_reverso`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historico_reverso` (
  `id` int NOT NULL AUTO_INCREMENT,
  `factura_id` int NOT NULL,
  `cliente_id` int NOT NULL,
  `fecha` date NOT NULL,
  `curso_id` int NOT NULL,
  `fecha_reverso` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `usuario` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `factura_id` (`factura_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historico_reverso`
--

LOCK TABLES `historico_reverso` WRITE;
/*!40000 ALTER TABLE `historico_reverso` DISABLE KEYS */;
INSERT INTO `historico_reverso` VALUES (7,28,13312024,'2025-01-04',1,'2025-01-05 05:40:30','administrador'),(8,26,123456,'2025-01-04',1,'2025-01-21 00:31:49','administrador'),(9,27,123456,'2025-01-04',1,'2025-01-21 00:31:54','administrador'),(10,29,123456,'2025-01-04',1,'2025-01-21 00:31:57','administrador'),(11,30,123456,'2025-01-09',1,'2025-01-21 00:32:01','administrador'),(12,37,0,'2025-01-28',7,'2025-02-02 03:52:23','administrador');
/*!40000 ALTER TABLE `historico_reverso` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `students` (
  `id` int NOT NULL AUTO_INCREMENT,
  `cedula` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `telefono` varchar(12) COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `correo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `curso_id` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `foto` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `empresa_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (22,'123456789','prueba','prueba','2025-02-23','042400000000','prueba','prueba@gmail.com','14','67babf9b34d9b.png',NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tasa_cambio`
--

DROP TABLE IF EXISTS `tasa_cambio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tasa_cambio` (
  `id` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `tasa` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tasa_cambio`
--

LOCK TABLES `tasa_cambio` WRITE;
/*!40000 ALTER TABLE `tasa_cambio` DISABLE KEYS */;
INSERT INTO `tasa_cambio` VALUES (1,'2025-01-16',65.75),(2,'2025-01-18',65.75),(3,'2025-01-19',65.75),(4,'2025-01-20',54.91),(5,'2025-01-21',65.33),(6,'2025-01-22',55.29),(7,'2025-01-24',56.28),(8,'2025-01-31',58.44),(9,'2025-02-03',58.54),(10,'2025-02-15',62.07),(11,'2025-02-15',62.07);
/*!40000 ALTER TABLE `tasa_cambio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `rol` enum('administrador','supervisor','caja') COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (7,'jalburgas','$2y$10$67tKxsktPCDrhWELXYA9OOzPSyU7bBLEmc/rgkGgm/C5gPxD/vV1C','administrador'),(8,'administrador','$2y$10$y9MCj0gb6UC2MYcjIS6v9e670K7vWZzmxqySCx3H4nKwVL/VLmwwe','administrador'),(9,'caja','$2y$10$KcLpeGUXd2HV3428MxXLRe4BCsyTxLhq.rYfq9aJseUb5C6ETubY6','caja'),(10,'supervisor','$2y$10$SgcVPax234YdrCItraggN.Unjgcl4x.1UBjtwOk39jgyorjapeqMy','supervisor'),(11,'prueba','$2y$10$k00sp.HC36rRDjN6SKfQe.3pECSlDEOxHDkGjU5lwIoz57OdX0..a','administrador');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vista_facturas_asientos`
--

DROP TABLE IF EXISTS `vista_facturas_asientos`;
/*!50001 DROP VIEW IF EXISTS `vista_facturas_asientos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_facturas_asientos` AS SELECT 
 1 AS `id`,
 1 AS `cliente_id`,
 1 AS `fecha`,
 1 AS `curso_id`,
 1 AS `usuario`,
 1 AS `sub_total`,
 1 AS `iva`,
 1 AS `total_factura`,
 1 AS `total_debe`,
 1 AS `total_haber`,
 1 AS `cuenta_contable`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vista_facturas_asientos`
--

/*!50001 DROP VIEW IF EXISTS `vista_facturas_asientos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`admin`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_facturas_asientos` AS select `f`.`id` AS `id`,`f`.`cliente_id` AS `cliente_id`,`f`.`fecha` AS `fecha`,`f`.`curso_id` AS `curso_id`,`f`.`usuario` AS `usuario`,`f`.`sub_total` AS `sub_total`,`f`.`iva` AS `iva`,`f`.`total_factura` AS `total_factura`,`a`.`debe` AS `total_debe`,`a`.`haber` AS `total_haber`,`c`.`nombre` AS `cuenta_contable` from ((`facturas` `f` join `asientos_contables` `a` on((`f`.`id` = `a`.`factura_id`))) join `cuentas_contables` `c` on((`a`.`cuenta_id` = `c`.`numero_cuenta`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-03-01 16:16:09
