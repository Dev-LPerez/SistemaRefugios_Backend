-- MySQL dump 10.13  Distrib 8.0.44, for Win64 (x86_64)
--
-- Host: sistema-refugio01-luisperez150644-c780.c.aivencloud.com    Database: defaultdb
-- ------------------------------------------------------
-- Server version	8.0.45

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
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ '89b10e84-4d91-11f1-9731-d65c4db91188:1-199,
b58923f5-47d1-11f1-8262-cefb62bee995:1-17,
ca498238-4649-11f1-8160-a64090244b6e:1-53';

--
-- Table structure for table `auditoria_logs`
--

DROP TABLE IF EXISTS `auditoria_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auditoria_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `accion` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `entidad` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha` datetime DEFAULT CURRENT_TIMESTAMP,
  `ip` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auditoria_logs`
--

LOCK TABLES `auditoria_logs` WRITE;
/*!40000 ALTER TABLE `auditoria_logs` DISABLE KEYS */;
INSERT INTO `auditoria_logs` VALUES (1,8,'PUT','refugios','2026-05-29 22:30:46','::1'),(2,8,'POST','refugios','2026-05-29 22:31:26','::1'),(3,8,'POST','refugios','2026-05-29 22:38:30','::1'),(4,8,'POST','refugios','2026-05-29 22:40:15','::1'),(5,8,'POST','refugios','2026-05-29 22:41:24','::1'),(6,8,'POST','refugios','2026-05-29 22:41:56','::1'),(7,8,'POST','refugios','2026-05-29 22:42:41','::1'),(8,8,'POST','refugios','2026-05-29 22:47:57','::1'),(9,8,'POST','refugios','2026-05-29 22:55:37','::1'),(10,8,'POST','refugios','2026-06-03 14:56:37','::1'),(11,8,'POST','recursos','2026-06-03 14:59:32','::1'),(12,8,'POST','refugios','2026-06-04 13:42:33','::1'),(13,8,'POST','recursos','2026-06-04 13:47:52','::1'),(14,8,'POST','donantes','2026-06-04 13:50:02','::1'),(15,8,'POST','donaciones','2026-06-04 13:50:18','::1'),(16,8,'POST','donaciones','2026-06-04 13:51:05','::1'),(17,8,'POST','entregas','2026-06-04 13:51:30','::1'),(18,8,'POST','familias/miembros','2026-06-04 14:03:13','::1'),(19,8,'POST','familias/miembros','2026-06-04 14:43:23','::1'),(20,8,'POST','donaciones','2026-06-04 14:45:44','::1'),(21,8,'POST','recursos','2026-06-04 22:49:18','::1'),(22,8,'POST','familias/miembros','2026-06-05 04:54:47','::1'),(23,8,'DELETE','familias','2026-06-05 04:56:41','::1'),(24,8,'POST','recursos','2026-06-05 04:57:27','::1'),(25,8,'POST','recursos','2026-06-05 04:57:50','::1'),(26,8,'POST','entregas','2026-06-05 04:58:15','::1'),(27,8,'POST','entregas','2026-06-05 05:02:49','::1'),(28,8,'PUT','recursos','2026-06-05 05:08:52','::1'),(29,8,'PUT','recursos','2026-06-05 05:14:15','::1'),(30,8,'PUT','refugios','2026-06-05 05:14:53','::1'),(31,8,'PUT','refugios','2026-06-05 05:14:55','::1'),(32,8,'PUT','refugios','2026-06-05 05:14:56','::1'),(33,8,'POST','entregas','2026-06-05 11:29:43','::1'),(34,8,'PUT','familias','2026-06-05 11:38:07','::1'),(35,8,'PUT','familias','2026-06-05 11:39:10','::1'),(36,8,'PUT','familias','2026-06-05 11:39:22','::1'),(37,8,'PUT','refugios','2026-06-05 11:39:36','::1'),(38,8,'PUT','familias','2026-06-05 11:44:40','::1'),(39,8,'PUT','familias','2026-06-05 11:44:55','::1'),(40,8,'POST','donaciones','2026-06-05 11:48:20','::1'),(41,8,'POST','donaciones','2026-06-05 11:55:37','::1'),(42,8,'POST agregar_detalle','donaciones','2026-06-05 11:55:49','::1'),(43,8,'PUT','refugios','2026-06-05 12:09:21','::1'),(44,8,'PUT','familias','2026-06-05 23:43:20','::1'),(45,8,'POST','familias','2026-06-05 23:43:54','::1'),(46,8,'POST','familias/miembros','2026-06-05 23:45:45','::1'),(47,8,'POST','refugios','2026-06-06 14:58:53','::1'),(48,8,'POST','familias/miembros','2026-06-06 15:00:21','::1'),(49,8,'POST','recursos','2026-06-06 15:02:08','::1'),(50,8,'POST','donaciones','2026-06-06 15:04:06','::1'),(51,8,'POST agregar_detalle','donaciones','2026-06-06 15:04:13','::1'),(52,8,'POST','entregas','2026-06-06 15:05:13','::1'),(53,8,'POST','entregas','2026-06-06 15:05:36','::1'),(54,8,'POST','familias','2026-06-06 15:50:56','::1'),(55,8,'PUT','familias/miembros','2026-06-06 16:21:24','::1'),(56,8,'PUT','familias/miembros','2026-06-06 16:21:29','::1'),(57,8,'POST','familias/miembros','2026-06-06 16:23:06','::1'),(58,8,'POST','entregas','2026-06-06 16:31:23','::1'),(59,8,'POST','entregas','2026-06-06 16:31:33','::1'),(60,8,'POST','entregas','2026-06-06 16:31:47','::1'),(61,8,'POST','entregas','2026-06-06 16:32:12','::1'),(62,8,'POST','entregas','2026-06-06 18:01:12','::1'),(63,8,'POST','entregas','2026-06-06 18:01:13','::1'),(64,8,'POST','entregas','2026-06-06 18:01:13','::1'),(65,8,'POST','entregas','2026-06-06 18:01:13','::1'),(66,8,'POST','entregas','2026-06-06 18:01:34','::1'),(67,8,'POST','entregas','2026-06-06 18:01:34','::1'),(68,8,'POST','entregas','2026-06-06 18:01:34','::1'),(69,8,'POST','entregas','2026-06-06 18:01:35','::1'),(70,8,'POST','donaciones','2026-06-06 18:07:04','::1'),(71,8,'POST','donaciones','2026-06-06 18:26:16','::1'),(72,8,'POST agregar_detalle','donaciones','2026-06-06 18:26:17','::1'),(73,8,'POST agregar_detalle','donaciones','2026-06-06 18:26:17','::1'),(74,8,'POST agregar_detalle','donaciones','2026-06-06 18:26:17','::1'),(75,8,'POST agregar_detalle','donaciones','2026-06-06 18:26:17','::1'),(76,8,'DELETE','recursos','2026-06-06 18:27:30','::1'),(77,8,'PUT','recursos','2026-06-06 18:27:38','::1'),(78,8,'PUT','recursos','2026-06-06 18:27:47','::1'),(79,8,'PUT','recursos','2026-06-06 18:28:00','::1'),(80,8,'PUT','recursos','2026-06-06 18:28:11','::1'),(81,8,'PUT','recursos','2026-06-06 18:28:18','::1'),(82,8,'DELETE','recursos','2026-06-06 18:28:25','::1'),(83,8,'DELETE','recursos','2026-06-06 18:28:49','::1'),(84,8,'DELETE','recursos','2026-06-06 18:28:55','::1'),(85,8,'DELETE','recursos','2026-06-06 18:28:58','::1'),(86,8,'DELETE','recursos','2026-06-06 18:29:05','::1'),(87,8,'DELETE','entregas','2026-06-06 18:39:16','::1'),(88,8,'POST','entregas','2026-06-06 18:39:59','::1'),(89,8,'POST','entregas','2026-06-06 18:41:05','::1'),(90,8,'DELETE','entregas','2026-06-06 18:41:18','::1'),(91,8,'POST','entregas','2026-06-06 18:45:26','::1');
/*!40000 ALTER TABLE `auditoria_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuracion_prioridades`
--

DROP TABLE IF EXISTS `configuracion_prioridades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuracion_prioridades` (
  `id_criterio` int NOT NULL AUTO_INCREMENT,
  `nombre_criterio` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `peso_puntos` int NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_criterio`),
  UNIQUE KEY `nombre_criterio` (`nombre_criterio`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuracion_prioridades`
--

LOCK TABLES `configuracion_prioridades` WRITE;
/*!40000 ALTER TABLE `configuracion_prioridades` DISABLE KEYS */;
INSERT INTO `configuracion_prioridades` VALUES (1,'mujer_embarazada',20,'Puntos por embarazo'),(2,'nino_menor_5',15,'Puntos por primera infancia'),(3,'adulto_mayor',15,'Puntos por ser mayor de 65 años'),(4,'discapacidad',20,'Puntos por discapacidad'),(5,'enfermedad',10,'Puntos por condición crónica');
/*!40000 ALTER TABLE `configuracion_prioridades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_donacion`
--

DROP TABLE IF EXISTS `detalle_donacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_donacion` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_donacion` int DEFAULT NULL,
  `id_recurso` int DEFAULT NULL,
  `cantidad` int NOT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `detalle_donacion_ibfk_1` (`id_donacion`),
  KEY `detalle_donacion_ibfk_2` (`id_recurso`),
  CONSTRAINT `detalle_donacion_ibfk_1` FOREIGN KEY (`id_donacion`) REFERENCES `donaciones` (`id_donacion`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_donacion_ibfk_2` FOREIGN KEY (`id_recurso`) REFERENCES `recursos` (`id_recurso`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_donacion`
--

LOCK TABLES `detalle_donacion` WRITE;
/*!40000 ALTER TABLE `detalle_donacion` DISABLE KEYS */;
INSERT INTO `detalle_donacion` VALUES (1,1,1,100),(2,1,2,50),(3,2,3,80),(4,2,4,20),(9,6,4,1),(11,7,5,10),(12,7,4,5),(13,7,3,15);
/*!40000 ALTER TABLE `detalle_donacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_entrega`
--

DROP TABLE IF EXISTS `detalle_entrega`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_entrega` (
  `id_entrega` int NOT NULL AUTO_INCREMENT,
  `fecha` date DEFAULT NULL,
  `estado` enum('pendiente','entregado','cancelado') COLLATE utf8mb4_general_ci DEFAULT 'entregado',
  `id_familia` int DEFAULT NULL,
  `cantidad` int NOT NULL,
  `id_recurso` int DEFAULT NULL,
  PRIMARY KEY (`id_entrega`),
  KEY `idx_detalle_entrega_fecha` (`fecha`),
  KEY `detalle_entrega_ibfk_1` (`id_familia`),
  KEY `fk_entrega_recurso` (`id_recurso`),
  CONSTRAINT `detalle_entrega_ibfk_1` FOREIGN KEY (`id_familia`) REFERENCES `familias` (`id_familia`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_entrega_recurso` FOREIGN KEY (`id_recurso`) REFERENCES `recursos` (`id_recurso`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_entrega`
--

LOCK TABLES `detalle_entrega` WRITE;
/*!40000 ALTER TABLE `detalle_entrega` DISABLE KEYS */;
INSERT INTO `detalle_entrega` VALUES (1,'2026-04-03','entregado',1,20,1),(2,'2026-04-03','entregado',1,10,2),(3,'2026-04-03','entregado',2,15,3),(4,'2026-04-04','entregado',3,5,4),(5,'2026-04-05','entregado',4,8,1),(15,'2026-06-04','entregado',1,1,1),(16,'2026-06-05','entregado',2,4,2),(17,'2026-06-05','pendiente',3,3,4),(20,'2026-06-06','entregado',4,4,1),(21,'2026-06-06','entregado',4,6,5),(22,'2026-06-06','entregado',4,1,4),(23,'2026-06-06','entregado',5,50,1),(24,'2026-06-06','entregado',5,19,2),(25,'2026-06-06','entregado',5,2,4),(26,'2026-06-06','entregado',5,10,3),(27,'2026-06-06','entregado',15,45,1),(28,'2026-06-06','entregado',15,10,2),(29,'2026-06-06','entregado',15,5,4);
/*!40000 ALTER TABLE `detalle_entrega` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `detalle_gestion`
--

DROP TABLE IF EXISTS `detalle_gestion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detalle_gestion` (
  `id_detalle` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int DEFAULT NULL,
  `id_recurso` int DEFAULT NULL,
  `accion` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_detalle`),
  KEY `detalle_gestion_ibfk_1` (`id_usuario`),
  KEY `detalle_gestion_ibfk_2` (`id_recurso`),
  CONSTRAINT `detalle_gestion_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `detalle_gestion_ibfk_2` FOREIGN KEY (`id_recurso`) REFERENCES `recursos` (`id_recurso`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detalle_gestion`
--

LOCK TABLES `detalle_gestion` WRITE;
/*!40000 ALTER TABLE `detalle_gestion` DISABLE KEYS */;
INSERT INTO `detalle_gestion` VALUES (1,1,1,'ingreso'),(2,2,2,'entrega'),(3,3,3,'control');
/*!40000 ALTER TABLE `detalle_gestion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donaciones`
--

DROP TABLE IF EXISTS `donaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donaciones` (
  `id_donacion` int NOT NULL AUTO_INCREMENT,
  `fecha` date NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `id_donante` int DEFAULT NULL,
  `origen` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `categoria` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_donacion`),
  KEY `donaciones_ibfk_1` (`id_donante`),
  CONSTRAINT `donaciones_ibfk_1` FOREIGN KEY (`id_donante`) REFERENCES `donante` (`id_donante`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donaciones`
--

LOCK TABLES `donaciones` WRITE;
/*!40000 ALTER TABLE `donaciones` DISABLE KEYS */;
INSERT INTO `donaciones` VALUES (1,'2026-04-01','Donacion alimentos',1,NULL,NULL),(2,'2026-04-02','Donacion aseo',2,NULL,NULL),(3,'2026-05-13','Donacion de agua',1,'Empresa Solidaria','Alimentos'),(4,'2026-06-05','Dinero para Alimentos',1,'Nacional','Dinero'),(5,'2026-06-05','dinero',4,'Nacional','Dinero'),(6,'2026-06-06','donar',2,'Nacional','Alimentos'),(7,'2026-06-06','Donacion por la vida (Alimentos)',1,'Nacional','Alimentos');
/*!40000 ALTER TABLE `donaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `donante`
--

DROP TABLE IF EXISTS `donante`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `donante` (
  `id_donante` int NOT NULL AUTO_INCREMENT,
  `identificacion` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_donante`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `donante`
--

LOCK TABLES `donante` WRITE;
/*!40000 ALTER TABLE `donante` DISABLE KEYS */;
INSERT INTO `donante` VALUES (1,'9001','Empresa Solidaria','empresa','3100000000'),(2,'9002','Gobierno Local','gobierno','3200000000'),(3,'9001234','Empresa Solidaria','empresa','3100000000'),(4,'1425654541','Empresa Ejemplo','empresa','3212232256');
/*!40000 ALTER TABLE `donante` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `familias`
--

DROP TABLE IF EXISTS `familias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `familias` (
  `id_familia` int NOT NULL AUTO_INCREMENT,
  `cedula` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `representante` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cantidad_miembros` int DEFAULT NULL,
  `prioridad` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_refugio` int DEFAULT NULL,
  `ubicacion_actual` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'Refugio',
  `aceptacion_habeas_data` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_familia`),
  KEY `idx_familia_cedula` (`cedula`),
  KEY `fk_familias_refugios_final` (`id_refugio`),
  CONSTRAINT `fk_familias_refugios_final` FOREIGN KEY (`id_refugio`) REFERENCES `refugios` (`id_refugio`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `familias`
--

LOCK TABLES `familias` WRITE;
/*!40000 ALTER TABLE `familias` DISABLE KEYS */;
INSERT INTO `familias` VALUES (1,'111','Carlos Perez','3001111111','Zona Norte',4,'50',1,'Refugio',1),(2,'114','Maria Gomez','3002222222','Zona Sur',3,'100',2,'Refugio',1),(3,'116','Luis Torres','3003333333','Zona Centro',5,'alta',NULL,'Refugio',0),(4,'118','Ana Ruiz','3004444444','Zona Norte',2,'baja',NULL,'Refugio',0),(5,'119','Pedro Diaz','3005555555','Zona Sur',6,'alta',NULL,'Refugio',0),(14,'111111','José','9999999','Ejemplo',1,'10',1,'Vivienda',1),(15,'1069746352','Luis Guillermo Perez','3023492030','Calle 10',4,'10',1,'Refugio',1);
/*!40000 ALTER TABLE `familias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `miembros`
--

DROP TABLE IF EXISTS `miembros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `miembros` (
  `id_persona` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `edad` int DEFAULT NULL,
  `es_embarazada` tinyint(1) DEFAULT '0',
  `tiene_discapacidad` tinyint(1) DEFAULT '0',
  `enfermedad_cronica` tinyint(1) DEFAULT '0',
  `parentezco` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_documento` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero_documento` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `vulnerable` tinyint(1) DEFAULT NULL,
  `tipo_vulnerabilidad` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_familia` int DEFAULT NULL,
  PRIMARY KEY (`id_persona`),
  KEY `miembros_ibfk_1` (`id_familia`),
  CONSTRAINT `miembros_ibfk_1` FOREIGN KEY (`id_familia`) REFERENCES `familias` (`id_familia`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `miembros`
--

LOCK TABLES `miembros` WRITE;
/*!40000 ALTER TABLE `miembros` DISABLE KEYS */;
INSERT INTO `miembros` VALUES (1,'Carlos Perez',40,0,0,0,'padre','CC','111',0,NULL,1),(2,'Laura Perez',35,0,0,0,'madre','CC','112',0,NULL,1),(3,'Juan Perez',10,0,0,0,'hijo','TI','113',1,'niño',1),(4,'Maria Gomez',30,0,0,0,'madre','CC','114',0,NULL,2),(5,'Pedro Gomez',5,0,0,0,'hijo','RC','115',1,'niño',2),(6,'Luis Torres',50,0,0,0,'padre','CC','116',0,NULL,3),(7,'Ana Torres',70,0,0,0,'abuela','CC','117',1,'anciano',3),(8,'Ana Ruiz',28,0,0,0,'madre','CC','118',0,NULL,4),(9,'Pedro Diaz',45,0,0,0,'padre','CC','119',0,NULL,5),(10,'Maria Perez',8,NULL,NULL,NULL,'Hija','TI','9876541',NULL,NULL,NULL),(11,'Maria Prueba',8,0,0,0,'Hija','TI','987654123',1,'Menor de edad',2),(12,'miembro ejemplo',0,0,0,1,'Hijo','CC','9999999999',0,'',1),(13,'miembro ejemplo',12,0,0,0,'Hijo','CC','9999999',0,'',14),(14,'ejemplo',18,0,0,0,'Hijo','CC','999999999',1,'',2),(15,'Jose Miguel Perez',15,0,1,1,'Hijo','TI','1679832893',1,'Niño',1);
/*!40000 ALTER TABLE `miembros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recursos`
--

DROP TABLE IF EXISTS `recursos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `recursos` (
  `id_recurso` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `categoria` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unidad` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cantidad_disponible` int DEFAULT '0',
  `stock` int DEFAULT '0',
  PRIMARY KEY (`id_recurso`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recursos`
--

LOCK TABLES `recursos` WRITE;
/*!40000 ALTER TABLE `recursos` DISABLE KEYS */;
INSERT INTO `recursos` VALUES (1,'Arroz','Alimento',NULL,'kg',400,NULL),(2,'Agua','Agua',NULL,'litros',267,NULL),(3,'Jabon','Higiene',NULL,'unidades',205,NULL),(4,'Colchon','Otro',NULL,'unidades',95,NULL),(5,'Leche','Alimento',NULL,'litros',154,NULL),(10,'Ejemplo de producto','Alimento','Sin Categoría','kg',40,0);
/*!40000 ALTER TABLE `recursos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `refugios`
--

DROP TABLE IF EXISTS `refugios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `refugios` (
  `id_refugio` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `capacidad_maxima` int NOT NULL,
  `ocupacion_actual` int DEFAULT '0',
  `estado` enum('activo','inactivo','lleno') COLLATE utf8mb4_general_ci DEFAULT 'activo',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_refugio`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `refugios`
--

LOCK TABLES `refugios` WRITE;
/*!40000 ALTER TABLE `refugios` DISABLE KEYS */;
INSERT INTO `refugios` VALUES (1,'Refugio Norte','Calle 10 #5-20',50,0,'activo','2026-05-13 17:11:46'),(2,'Refugio Prueba','Calle 29 #05-26',500,0,'activo','2026-05-29 22:47:57'),(3,'Refugio villa real','villa real',200,0,'activo','2026-05-29 22:55:37'),(4,'ejemplo','cualquiera',100,0,'inactivo','2026-06-03 14:56:38'),(5,'Ejemplo 2','cualquiera',100,0,'activo','2026-06-04 13:42:33'),(6,'Ejemplo 3','cualquiera',100,0,'inactivo','2026-06-06 14:58:53');
/*!40000 ALTER TABLE `refugios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin'),(2,'Auditor'),(5,'Logistica'),(3,'Operario'),(4,'Voluntario');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `user` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rol` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'admin','1234','admin'),(2,'operador1','1234','operador'),(3,'operador2','1234','operador'),(5,'LuisPerez','$2y$10$eoqB4ad7ZK4eEG5PcaOovuADpUVMvZ50NnZyjDV8pdS','1'),(6,'Luis Perez','$2y$10$cSBYvebr6uNYUNzWcKd2tOVIEEoSFmNGR/4WjjDZfI9','Admin'),(7,'Luis 2','$2y$10$C9rHt2tw37lSsGGyVGHpIueUN93QGeMnh3hoBoADTVL2xK12PuKHy','Admin'),(8,'Luis Admin','$2y$10$YjhwshbJOmRfA.9k5nn4Qu2QXl3qq6EvUHq3WtP4h.P3CSkfi.j3u','Admin'),(10,'admin_test','$2y$10$21BtnWFaEIZ6M5A57JV.DObu7msyuwZpEjn4zVPNlw/aHtkh3we7y','Admin');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-06 13:56:03
