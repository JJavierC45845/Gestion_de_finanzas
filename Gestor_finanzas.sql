-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: gestor
-- ------------------------------------------------------
-- Server version	8.0.41

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
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('ingreso','gasto') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario_id` (`usuario_id`,`nombre`,`tipo`),
  CONSTRAINT `categorias_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=313 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (312,1,'Ahorros','ingreso'),(6,1,'Beca','ingreso'),(12,1,'Cafe','gasto'),(1,1,'Comida','gasto'),(14,1,'Deudas (Tarjeta de Mamá)','gasto'),(11,1,'Flores','gasto'),(4,1,'Gasolina','gasto'),(13,1,'Gasto','gasto'),(310,1,'Graduación','gasto'),(308,1,'Gym','gasto'),(15,1,'iphone','gasto'),(303,1,'Lentes','gasto'),(5,1,'Moto','gasto'),(311,1,'Otros','gasto'),(3,1,'Salario','ingreso'),(10,1,'Tarjeta de Credito','gasto'),(309,1,'Tienda','gasto'),(307,1,'Vape','gasto'),(2,1,'Venta','ingreso'),(58,2,'Accessories','ingreso'),(90,2,'Accessories','gasto'),(221,2,'Agua','gasto'),(236,2,'Alquiler','gasto'),(246,2,'Alquiler Coches','gasto'),(50,2,'Audio','gasto'),(60,2,'Automotive','gasto'),(62,2,'Beauty','ingreso'),(40,2,'Beauty','gasto'),(208,2,'Beca','ingreso'),(244,2,'Bonos','ingreso'),(213,2,'Cafe','gasto'),(226,2,'Cine','gasto'),(56,2,'Clothing - Footwear','gasto'),(65,2,'Clothing - Outerwear','gasto'),(72,2,'Clothing - Shirts','gasto'),(75,2,'Clothing - Sweaters','ingreso'),(71,2,'Clothing - Tops','gasto'),(205,2,'Comida','gasto'),(78,2,'Computers','ingreso'),(241,2,'Curso Online','gasto'),(233,2,'Dentista','gasto'),(306,2,'Deuda','ingreso'),(215,2,'Deudas (Tarjeta de Mamá)','gasto'),(234,2,'Doctor','gasto'),(229,2,'Donaciones','gasto'),(240,2,'Educación','gasto'),(220,2,'Electricidad','gasto'),(251,2,'Electrodomésticos','gasto'),(235,2,'Farmacia','gasto'),(53,2,'Fitness','ingreso'),(92,2,'Fitness','gasto'),(212,2,'Flores','gasto'),(97,2,'Food - Bakery','ingreso'),(96,2,'Food - Baking','ingreso'),(61,2,'Food - Beverages','gasto'),(7,2,'Food - Breakfast','gasto'),(64,2,'Food - Canned Goods','ingreso'),(98,2,'Food - Canned Goods','gasto'),(81,2,'Food - Condiments','ingreso'),(88,2,'Food - Condiments','gasto'),(44,2,'Food - Dairy','gasto'),(93,2,'Food - Deli','gasto'),(63,2,'Food - Fresh Produce','gasto'),(55,2,'Food - Frozen','ingreso'),(68,2,'Food - Frozen Foods','gasto'),(34,2,'Food - Frozen Fruits','gasto'),(100,2,'Food - Meat','gasto'),(83,2,'Food - Meats','ingreso'),(59,2,'Food - Pasta','ingreso'),(31,2,'Food - Prepared Foods','gasto'),(79,2,'Food - Produce','ingreso'),(38,2,'Food - Snacks','ingreso'),(89,2,'Food - Snacks','gasto'),(77,2,'Food - Soups','ingreso'),(39,2,'Food - Spices','gasto'),(209,2,'Freelance','ingreso'),(86,2,'Garden','ingreso'),(206,2,'Gasolina','gasto'),(214,2,'Gasto','gasto'),(224,2,'Gimnasio','gasto'),(249,2,'Herramientas','gasto'),(237,2,'Hipoteca','gasto'),(20,2,'Home','ingreso'),(87,2,'Home Security','gasto'),(247,2,'Hotel','gasto'),(243,2,'Impuestos','gasto'),(245,2,'Intereses','ingreso'),(222,2,'Internet','gasto'),(217,2,'Inversiones','ingreso'),(216,2,'iphone','gasto'),(252,2,'Jardinería','gasto'),(95,2,'Kitchen','ingreso'),(99,2,'Kitchen','gasto'),(253,2,'Lavandería','gasto'),(225,2,'Libros','gasto'),(238,2,'Mantenimiento','gasto'),(219,2,'Medicina','gasto'),(207,2,'Moto','gasto'),(250,2,'Muebles','gasto'),(223,2,'Netflix','gasto'),(74,2,'Outdoor','ingreso'),(57,2,'Outdoor','gasto'),(82,2,'Pets','ingreso'),(30,2,'Pets','gasto'),(228,2,'Regalos','gasto'),(239,2,'Reparaciones','gasto'),(227,2,'Restaurante','gasto'),(210,2,'Ropa','gasto'),(91,2,'Safety','ingreso'),(203,2,'Salario','ingreso'),(218,2,'Seguro','gasto'),(242,2,'Software','gasto'),(211,2,'Tarjeta de Credito','gasto'),(231,2,'Taxi','gasto'),(26,2,'Toys','ingreso'),(230,2,'Transporte','gasto'),(16,2,'Travel','gasto'),(232,2,'Uber','gasto'),(204,2,'Venta','ingreso'),(248,2,'Vuelos','gasto');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `gastos`
--

DROP TABLE IF EXISTS `gastos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `gastos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `categoria_id` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` text,
  `fecha` date NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `gastos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `gastos_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `gastos`
--

LOCK TABLES `gastos` WRITE;
/*!40000 ALTER TABLE `gastos` DISABLE KEYS */;
INSERT INTO `gastos` VALUES (1,1,1,60.00,'Tiendita','2025-10-21','2025-10-21 23:29:55'),(2,1,5,930.00,'Bateria nueva','2025-10-25','2025-10-25 02:06:40'),(3,1,10,400.00,'','2025-11-02','2025-11-04 16:32:32'),(4,1,11,400.00,'','2025-11-03','2025-11-04 16:33:23'),(5,1,12,120.00,'','2025-11-04','2025-11-04 16:42:47'),(6,1,15,200.00,'','2025-11-04','2025-11-04 16:43:34'),(7,1,13,200.00,'','2025-11-04','2025-11-04 16:43:43'),(8,1,14,500.00,'','2025-11-04','2025-11-04 16:43:50'),(9,1,1,35.00,'','2025-11-04','2025-11-04 22:22:56'),(10,1,1,15.00,'','2025-11-05','2025-11-05 21:18:33'),(11,1,1,60.00,'','2025-11-05','2025-11-05 22:18:56'),(12,1,1,380.00,'','2025-11-06','2025-11-06 20:12:55'),(13,1,4,200.00,'','2025-11-06','2025-11-06 20:13:20'),(14,1,1,30.00,'','2025-11-07','2025-11-07 01:23:00'),(15,1,4,50.00,'Moto de paola','2025-11-07','2025-11-07 18:05:19'),(16,1,303,805.00,'','2025-11-07','2025-11-07 18:05:40'),(17,1,1,100.00,'','2025-11-08','2025-11-08 04:40:09'),(18,1,307,350.00,'','2025-11-08','2025-11-08 04:40:44'),(19,1,1,100.00,'','2025-11-08','2025-11-08 04:40:55'),(20,1,10,2000.00,'','2025-11-10','2025-11-10 01:40:14'),(21,1,309,48.00,'','2025-11-10','2025-11-10 02:53:50'),(22,1,308,300.00,'','2025-11-10','2025-11-10 02:54:11'),(23,1,310,100.00,'','2025-11-10','2025-11-10 02:54:45'),(24,1,12,60.00,'','2025-11-12','2025-11-12 18:36:14'),(25,1,10,100.00,'','2025-11-12','2025-11-12 18:36:27'),(26,1,1,50.00,'','2025-11-12','2025-11-12 18:36:41'),(27,1,13,100.00,'Viaje para la FIL','2025-11-13','2025-11-13 01:35:20'),(28,1,311,100.00,'Regalo de Ivan','2025-11-14','2025-11-14 20:49:30'),(29,1,1,200.00,'','2025-11-18','2025-11-18 04:47:05'),(30,1,4,200.00,'','2025-11-18','2025-11-18 04:47:14'),(31,1,11,260.00,'','2025-11-18','2025-11-18 04:52:18'),(32,1,1,320.00,'','2025-11-20','2025-11-20 05:45:35'),(33,1,1,40.00,'','2025-11-20','2025-11-20 05:45:44'),(34,1,10,200.00,'','2025-11-24','2025-11-24 16:45:07'),(35,1,4,160.00,'','2025-11-24','2025-11-24 16:45:25'),(36,1,310,100.00,'','2025-11-24','2025-11-24 16:45:34'),(37,1,311,110.00,'Posada','2025-11-24','2025-11-24 16:45:57'),(38,1,1,45.00,'','2025-11-24','2025-11-24 16:47:16'),(39,1,12,62.00,'','2025-11-24','2025-11-24 16:53:19'),(40,1,1,100.00,'','2025-11-26','2025-11-26 17:53:50');
/*!40000 ALTER TABLE `gastos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingresos`
--

DROP TABLE IF EXISTS `ingresos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingresos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `categoria_id` int NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` text,
  `fecha` date NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `ingresos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ingresos_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingresos`
--

LOCK TABLES `ingresos` WRITE;
/*!40000 ALTER TABLE `ingresos` DISABLE KEYS */;
INSERT INTO `ingresos` VALUES (1,1,2,500.00,'Venta de sala','2025-10-21','2025-10-21 22:44:09'),(3,1,2,500.00,'Venta de Sala','2025-11-01','2025-11-04 16:34:18'),(4,1,3,550.00,'','2025-11-04','2025-11-04 16:34:27'),(5,1,6,5000.00,'','2025-11-04','2025-11-04 16:34:34'),(6,1,3,500.00,'','2025-11-10','2025-11-10 01:40:30'),(7,1,3,500.00,'','2025-11-17','2025-11-17 07:28:07'),(8,1,3,250.00,'','2025-11-18','2025-11-18 04:46:52'),(9,1,312,1000.00,'','2025-11-20','2025-11-20 05:45:13'),(10,1,3,700.00,'','2025-11-24','2025-11-24 16:44:41');
/*!40000 ALTER TABLE `ingresos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `movimientosahorros`
--

DROP TABLE IF EXISTS `movimientosahorros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `movimientosahorros` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `tipo` enum('deposito','retiro') NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `descripcion` text,
  `fecha` date NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  CONSTRAINT `movimientosahorros_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `movimientosahorros`
--

LOCK TABLES `movimientosahorros` WRITE;
/*!40000 ALTER TABLE `movimientosahorros` DISABLE KEYS */;
INSERT INTO `movimientosahorros` VALUES (1,1,'deposito',100.00,'Sobrante de la semana','2025-10-24','2025-10-25 01:43:44'),(2,1,'retiro',10.00,'Se me antojaron unos chetos','2025-10-24','2025-10-25 01:43:59'),(3,1,'retiro',90.00,'','2025-10-24','2025-10-25 01:55:07'),(4,1,'deposito',10000.00,'Sobrante de la beca','2025-10-24','2025-10-25 01:55:20'),(5,1,'retiro',9999.00,'','2025-10-31','2025-10-31 18:44:18'),(6,1,'deposito',9999.00,'','2025-10-31','2025-10-31 18:44:36'),(7,1,'deposito',4000.00,'Beca  de Septiembre - Octubre','2025-10-31','2025-10-31 21:27:43'),(8,1,'retiro',4000.00,'','2025-11-04','2025-11-04 17:01:29'),(9,1,'retiro',1000.00,'','2025-11-17','2025-11-18 04:44:57');
/*!40000 ALTER TABLE `movimientosahorros` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre_usuario` (`nombre_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Javier','$2y$10$TUTg/OhZ0mLBETmijhGv4./VnmsNvmZ2VhgStOSb9FOF01DgQGrpq','2025-10-21 06:00:00'),(2,'Jose','$2y$10$Ohf9MGdnynnkKK.RF2qmGeVg3GVzzTVHlT26OoVgxDbO2jytzKWHa','2025-10-21 17:07:43'),(3,'Janene','$2a$04$oIZvgiWZqwhL0tNhSS7a2uf0iV.MfEJkNgcdw0QdUlObeQGwL2OfK','2025-02-22 06:00:00'),(4,'Ive','$2a$04$VYRgTm2AImYSIE.1AzP6E.zNDG9B1udeUbFm0kNK9bfmp7hm72P2.','2025-01-15 06:00:00'),(5,'Wylie','$2a$04$6v0w7q0BiCnbZNTqxVqxLet/e39aguDbUVRTnoi7iHf6QHyVvn8sK','2025-01-20 06:00:00'),(6,'Aurel','$2a$04$cuAW6TrJWB6pT3FyTPEiNOgTu5kE8Vi106wr0pL3JR1zRgWlI6sKS','2025-04-06 06:00:00'),(7,'Shir','$2a$04$myZ8rxgBw7eV1eNJAJE8au0AenL3X1Uoj801AuoCjjhGar9JAALWq','2025-02-09 06:00:00'),(8,'Dionysus','$2a$04$lIwoJup6g9AA98JChGpFyu8.akqWoo07.tdPtTvIEJm2NZqqAQ0Cy','2025-04-02 06:00:00'),(9,'Reiko','$2a$04$BDBVxcWwhb3v0B0IKzbQJOvWlqwFJyrDCF.DM37.Zzulah1yMs5C2','2025-02-20 06:00:00'),(10,'Malvina','$2a$04$dSqhwFYboR9IDPW824PdbOV5hhKxR.3UyK8ccAWslCmrxtFYuWABi','2025-03-16 06:00:00'),(11,'Ricki','$2a$04$cXKQOuFEIrMC.RWzCSV/LOGz6aM4PzEZ/5O27UfE85zbOmkuvFFtq','2025-03-15 06:00:00'),(12,'Elvera','$2a$04$KdpZlURS.GmgXXLTSlctWuYh3exdVxiTiUxGbMlcfv4a//eArsOZO','2025-02-24 06:00:00'),(13,'Hurlee','$2a$04$ZuRaCmf7nU6bvDIK33TMKuleMBGGAvxFeVRavonv.ECnmspN37sPK','2025-02-22 06:00:00'),(14,'Christa','$2a$04$PjvsTTKQ9LSXF7VHP0q34ekb64RkVJruqiGfiPKJSs9yVqokep0jW','2025-02-06 06:00:00'),(15,'Xever','$2a$04$eLm0WD5u8rmCi6k3zn3w/ORNBXgI8Wzs21EH6fBrAKj/6IET/Nhl.','2025-03-09 06:00:00'),(16,'Hedwig','$2a$04$c5NkQAPIbWovO5ggbO7z0emmA/LrPAhIcN8nNsqhzIyt0ACRloWYa','2025-01-21 06:00:00'),(17,'Clementine','$2a$04$oXPuomSuM9TmE4Y7OMUMaeb21GoxnfrvN2RQyLSlpYJ/c2aGPenrC','2025-03-10 06:00:00'),(18,'Jean','$2a$04$5VqChXNZdCeGZe4ThOuuVus92EX4B6Nc/4VfW11196V9RsRdvsEq2','2025-02-13 06:00:00'),(19,'Tarra','$2a$04$d3V5Wuv8rHYGO.IqMn.9IujW3xiaCWQMj8na3yZeODz0YzIiJ49Bm','2025-03-16 06:00:00'),(20,'Innis','$2a$04$Nop.CIR5dMxYVmugaRj.denejTPWtc585x6J67gBZ7cyO/5Y.QYeq','2025-03-19 06:00:00'),(21,'Terencio','$2a$04$jmgqnrgXY4r/fG70tonBz.a/7mk1CfMtiMZEMTSto0Mhuw4KZNEZC','2025-02-11 06:00:00'),(22,'Gilbertina','$2a$04$oOkJxcr6P/Gv3.DeqR9ZKOtjke/DGmxtjtzkkTNa76ti6vF/vJ4p2','2025-02-13 06:00:00'),(23,'Amabelle','$2a$04$HDOshli8dy2tx0CTD3ZuQ.E2HpjtEuvIVGPFZAZGoiI0RKGX9NLY6','2025-02-18 06:00:00'),(24,'Annabella','$2a$04$GC0A.rExEmyz3CEwZRch9u3aWEvNbz8GIFks2t37JwoYtT0KqF0Xi','2025-01-31 06:00:00'),(25,'Bari','$2a$04$pzgPOCdfLUalVCczFfvyQeFNn2GyuHssnZzZQsistjPMk8SY/VdP2','2025-02-09 06:00:00'),(26,'Massimiliano','$2a$04$.X6gRrRMN7Q5gOrIbUCObe6LTuyBCZQCM6kPQ/E1OQtSf0o.nO3n.','2025-03-27 06:00:00'),(27,'Keith','$2a$04$tLakipYp7lolv/OY7Df1memaS.EpZ4fEiM85zj.eKlYUTMeDL8lBW','2025-04-03 06:00:00'),(28,'Malissa','$2a$04$qfJcty2dRdvdf9Wvdd.aVOl66qg7RfCbmnlLaLQ4Fnm1s1bwDxxAK','2025-02-08 06:00:00'),(29,'Albert','$2a$04$Rt8JMMH3AUHcnrZYKSwKM.F0mXqjHAXG/68kOSWzIRExIJXnOs9QK','2025-03-30 06:00:00'),(30,'Padraic','$2a$04$Xh5zpclZfpB7tD4n6NQ00uiADO3F7eUwawbrth/Ini1..Uq0VAPoC','2025-03-03 06:00:00'),(31,'Rickert','$2a$04$wTl49rMBfmWIPtorwEPujOmIiqTAyBJGl46BUCEfcuvsZmuPR4REK','2025-03-04 06:00:00'),(32,'Raphaela','$2a$04$ml3kKEc662XFw45qUEq3B.ZoOY8ESeU0y.5LnMh.juLY03Jax.KzC','2025-04-06 06:00:00'),(33,'Calvin','$2a$04$aR2l/aYDEgmG59HZxrrKYOOxN89g.Ju87C8N6esapE6zBQ1dUaoYq','2025-02-06 06:00:00'),(34,'Fowler','$2a$04$.xRD31QAJaCDdR0YkKRdwukdYD3N4G8BMiE.m0A5b.uN4YLbRe3xq','2025-02-28 06:00:00'),(35,'Simeon','$2a$04$nWnqpdUT/S34o3AfSBWpm.t7vyw/7l1zLt/dD8tHy/Armdk8y1dfy','2025-02-13 06:00:00'),(36,'Parnell','$2a$04$pOnj2exEfQ7SLhJBnC3I2eRkgpwU7SmdYWjTo/4YV2BihG01j2xou','2025-03-05 06:00:00'),(37,'Sari','$2a$04$tFy46wBq8vjvwsQSPpizleVw9miAbZbX7hVG0/TSe3socfFfSMERy','2025-03-19 06:00:00'),(38,'Thalia','$2a$04$uBJ/snm/PRE0UpnKBbAI1OBAvejcov6tECOYr816MnAa4Da5QFXO2','2025-04-03 06:00:00'),(39,'Sandie','$2a$04$2jvtIqMk/g/ChasPacRet.V22HzudGPZ2GFSPyYAM7ROgB4MRPIgy','2025-03-23 06:00:00'),(40,'Piper','$2a$04$of1OvAT352D8Qk5GGSuBc.XN/fKdi4JYnr.tIYchYAii4vn6lXNm.','2025-01-29 06:00:00'),(41,'Allie','$2a$04$NIV1v8Nb6AER5wDWffzZ5ulWNzaMVZEhYZlUrcn3kZI0eJE8oFNmS','2025-01-11 06:00:00');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-26 15:18:18
