/*
SQLyog Ultimate v12.09 (64 bit)
MySQL - 5.7.14 : Database - sistema
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`sistema` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_bin */;

USE `sistema`;

/*Table structure for table `cliente` */

DROP TABLE IF EXISTS `cliente`;

CREATE TABLE `cliente` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `telefono` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `email` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `direccion` varchar(200) COLLATE utf8_bin NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=3276 ROW_FORMAT=DYNAMIC;

/*Data for the table `cliente` */

insert  into `cliente`(`id`,`nombre`,`telefono`,`email`,`direccion`,`estado`,`created_at`,`updated_at`) values (1,'Amado Rudas Díaz','952678732','wunderwaffen_aj@hotmail.com','Av. Perú 3110 - San Martín de Porres',1,'2017-04-16 11:12:15','2017-04-16 11:15:51'),(4,'Fernando Valera Angulo','33333343','fvalera@acuario.com.pe','Av. Girasoles 200',1,'2017-04-17 00:53:50','2017-04-18 23:41:48'),(5,'Maria Nuñez Rosas','333345444','maria@hotmail.com','Av. Primavera 233',1,'2017-04-25 22:16:52','2017-04-25 22:16:52');

/*Table structure for table `compra` */

DROP TABLE IF EXISTS `compra`;

CREATE TABLE `compra` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `proveedor_id` int(11) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `igv` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` datetime NOT NULL,
  `anulado` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `compra_proveedor` (`proveedor_id`),
  CONSTRAINT `compra_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

/*Data for the table `compra` */

/*Table structure for table `compra_detalle` */

DROP TABLE IF EXISTS `compra_detalle`;

CREATE TABLE `compra_detalle` (
  `orden` int(11) NOT NULL,
  `compra_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  KEY `FK_compra` (`compra_id`),
  KEY `FK_producto` (`producto_id`),
  CONSTRAINT `FK_compra` FOREIGN KEY (`compra_id`) REFERENCES `compra` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_compra_producto` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

/*Data for the table `compra_detalle` */

/*Table structure for table `comprobante` */

DROP TABLE IF EXISTS `comprobante`;

CREATE TABLE `comprobante` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(2) COLLATE utf8_bin NOT NULL,
  `serie` varchar(4) COLLATE utf8_bin NOT NULL,
  `numero` varchar(10) COLLATE utf8_bin NOT NULL,
  `cliente_id` int(11) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `iva` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha` date NOT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comprobante_cliente` (`cliente_id`),
  CONSTRAINT `comprobante_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `cliente` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=4096 ROW_FORMAT=DYNAMIC;

/*Data for the table `comprobante` */

insert  into `comprobante`(`id`,`tipo`,`serie`,`numero`,`cliente_id`,`sub_total`,`iva`,`total`,`fecha`,`estado`,`created_at`,`updated_at`) values (1,'01','001','1',1,'42.37','7.63','50.00','2017-06-04',1,'2017-06-04 02:04:25','2017-06-04 02:04:25'),(2,'01','001','2',4,'25.42','4.58','30.00','2017-06-04',1,'2017-06-04 02:06:09','2017-06-04 02:06:09'),(3,'12','001','1',1,'105.93','19.07','125.00','2017-06-04',1,'2017-06-04 13:45:17','2017-06-04 13:45:17'),(4,'03','001','1',4,'2.54','0.46','3.00','2017-06-04',2,'2017-06-04 13:50:03','2017-06-04 15:49:18'),(5,'01','001','3',4,'42.37','7.63','50.00','2017-06-04',2,'2017-06-04 21:15:33','2017-06-04 21:16:07'),(6,'03','001','2',1,'33.90','6.10','40.00','2017-06-04',2,'2017-06-04 21:21:31','2017-06-04 21:58:45');

/*Table structure for table `comprobante_detalle` */

DROP TABLE IF EXISTS `comprobante_detalle`;

CREATE TABLE `comprobante_detalle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comprobante_id` int(11) NOT NULL,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_comprobante` (`comprobante_id`),
  KEY `FK_producto` (`producto_id`),
  CONSTRAINT `FK_comprobante` FOREIGN KEY (`comprobante_id`) REFERENCES `comprobante` (`id`) ON DELETE CASCADE,
  CONSTRAINT `FK_producto` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=744 ROW_FORMAT=DYNAMIC;

/*Data for the table `comprobante_detalle` */

insert  into `comprobante_detalle`(`id`,`comprobante_id`,`producto_id`,`cantidad`,`costo`,`precio`,`total`,`created_at`,`updated_at`) values (1,1,39,'2.00','0.00','10.00','20.00','2017-06-04 02:04:25','2017-06-04 02:04:25'),(2,1,39,'3.00','0.00','10.00','30.00','2017-06-04 02:04:25','2017-06-04 02:04:25'),(3,2,39,'6.00','0.00','5.00','30.00','2017-06-04 02:06:09','2017-06-04 02:06:09'),(4,3,45,'5.00','0.00','25.00','125.00','2017-06-04 13:45:17','2017-06-04 13:45:17'),(5,4,45,'1.00','0.00','3.00','3.00','2017-06-04 13:50:03','2017-06-04 13:50:03'),(6,5,40,'10.00','0.00','5.00','50.00','2017-06-04 21:15:33','2017-06-04 21:15:33'),(7,6,40,'10.00','0.00','4.00','40.00','2017-06-04 21:21:31','2017-06-04 21:21:31');

/*Table structure for table `kardex` */

DROP TABLE IF EXISTS `kardex`;

CREATE TABLE `kardex` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `documento_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_kardex_producto` (`producto_id`),
  CONSTRAINT `FK_kardex_producto` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

/*Data for the table `kardex` */

insert  into `kardex`(`id`,`producto_id`,`tipo`,`documento_id`,`cantidad`,`created_at`,`updated_at`) values (1,45,200,4,'1.00','2017-06-04 15:49:17','2017-06-04 15:49:17'),(2,40,200,5,'10.00','2017-06-04 21:16:07','2017-06-04 21:16:07'),(3,40,200,6,'10.00','2017-06-04 21:21:37','2017-06-04 21:21:37'),(5,40,200,6,'10.00','2017-06-04 21:27:07','2017-06-04 21:27:07'),(7,40,200,6,'10.00','2017-06-04 21:58:45','2017-06-04 21:58:45');

/*Table structure for table `producto` */

DROP TABLE IF EXISTS `producto`;

CREATE TABLE `producto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) CHARACTER SET utf8 COLLATE utf8_spanish_ci NOT NULL,
  `marca` varchar(200) COLLATE utf8_bin NOT NULL,
  `costo` decimal(10,2) NOT NULL,
  `precio` decimal(10,2) NOT NULL,
  `foto` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=1638 ROW_FORMAT=DYNAMIC;

/*Data for the table `producto` */

insert  into `producto`(`id`,`nombre`,`marca`,`costo`,`precio`,`foto`,`created_at`,`updated_at`) values (32,'Snap\'s Picante x 48','Bamoer','10.00','25.00','media/producto-32.jpg','2017-04-16 11:28:23','2017-04-16 16:02:08'),(33,'Snap\'s Natural x 48','Karinto','10.00','15.00',NULL,'2017-04-16 16:02:40','2017-04-16 20:26:43'),(34,'Karintrix\'s Picante / Nat. x 96','Karinto','1.00','2.00',NULL,'2017-04-16 16:03:07','2017-04-16 16:03:07'),(35,'Cuates Picantex x 144','Karinto','1.00','2.00',NULL,'2017-04-16 16:03:34','2017-04-16 16:03:34'),(36,'Cuates Natural x 144','Karinto','1.00','2.00',NULL,'2017-04-16 16:03:58','2017-04-16 16:03:58'),(37,'Free Papa Ondulada Picante x 144','Karinto','1.00','2.00',NULL,'2017-04-16 16:04:22','2017-04-16 16:04:22'),(38,'Free Papa Ondulada Natural x 144','Karinto','1.00','2.00',NULL,'2017-04-16 16:04:42','2017-04-16 16:04:42'),(39,'Free Papa Natural c/ Mayonesa x 144','Karinto','1.00','2.00',NULL,'2017-04-16 16:05:24','2017-04-16 16:05:24'),(40,'Chifles x 23 grs x 240','Karinto','2.00','3.00','media/producto-40.jpg','2017-04-16 16:05:49','2017-04-18 01:42:11'),(43,'px1','Kitos','1.00','2.00',NULL,'2017-04-16 18:30:30','2017-04-16 18:30:30'),(44,'px2','Kitos','2.00','4.00','media/producto-44.jpg','2017-04-16 18:30:43','2017-06-04 02:26:19'),(45,'Prueba xd','Marca','5.00','10.00','media/producto-45.jpg','2017-05-14 20:26:42','2017-05-23 23:10:15');

/*Table structure for table `rol` */

DROP TABLE IF EXISTS `rol`;

CREATE TABLE `rol` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8_bin NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=5461 ROW_FORMAT=DYNAMIC;

/*Data for the table `rol` */

insert  into `rol`(`id`,`nombre`,`created_at`,`updated_at`) values (1,'Administrador','2016-11-06 00:00:00','2016-11-06 00:00:00'),(2,'Vendedor','2016-11-06 00:00:00','2016-11-06 00:00:00'),(3,'Analista','2016-11-06 00:00:00','2016-11-06 00:00:00');

/*Table structure for table `stock` */

DROP TABLE IF EXISTS `stock`;

CREATE TABLE `stock` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto_id` int(11) NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_stock_producto` (`producto_id`),
  CONSTRAINT `FK_stock_producto` FOREIGN KEY (`producto_id`) REFERENCES `producto` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin ROW_FORMAT=DYNAMIC;

/*Data for the table `stock` */

insert  into `stock`(`id`,`producto_id`,`cantidad`,`created_at`,`updated_at`) values (1,45,'8.00','2017-05-25 00:46:28','2017-06-04 15:49:18');

/*Table structure for table `tipo_movimiento` */

DROP TABLE IF EXISTS `tipo_movimiento`;

CREATE TABLE `tipo_movimiento` (
  `id` int(11) NOT NULL COMMENT 'Código Tipo Movimiento',
  `nombre` varchar(30) DEFAULT NULL COMMENT 'Denominación Tipo Movimiento',
  `tipo` enum('ENTRADA','SALIDA') NOT NULL COMMENT 'Tipo de Movimiento',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='Tipo de Movimiento';

/*Data for the table `tipo_movimiento` */

insert  into `tipo_movimiento`(`id`,`nombre`,`tipo`) values (100,'Compra','ENTRADA'),(200,'Venta','SALIDA');

/*Table structure for table `usuario` */

DROP TABLE IF EXISTS `usuario`;

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol_id` int(11) NOT NULL,
  `nombre` varchar(50) COLLATE utf8_bin NOT NULL,
  `apellido` varchar(50) COLLATE utf8_bin NOT NULL,
  `correo` varchar(100) COLLATE utf8_bin NOT NULL,
  `password` varchar(100) COLLATE utf8_bin NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_usuario_rol` (`rol_id`),
  CONSTRAINT `FK_usuario_rol` FOREIGN KEY (`rol_id`) REFERENCES `rol` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_bin AVG_ROW_LENGTH=2340 ROW_FORMAT=DYNAMIC;

/*Data for the table `usuario` */

insert  into `usuario`(`id`,`rol_id`,`nombre`,`apellido`,`correo`,`password`,`created_at`,`updated_at`) values (1,1,'Amado','Rudas','wunderwaffen_aj@hotmail.com','7c4a8d09ca3762af61e59520943dc26494f8941b','2016-11-06 00:00:00','2016-11-06 14:36:21'),(4,1,'Fernando','Gonzales Ramirez','framirez@anexsoft.com','7c4a8d09ca3762af61e59520943dc26494f8941b','2016-11-06 00:00:00','2016-11-06 14:36:18'),(5,2,'Cristina','Garc','cgarcial@anexsoft.com','7c4a8d09ca3762af61e59520943dc26494f8941b','2016-11-06 00:00:00','2016-11-06 14:36:16'),(6,2,'Jorge','Guzman Toledo','jguzman@anexsoft.com','7c4a8d09ca3762af61e59520943dc26494f8941b','2016-11-06 10:05:19','2016-11-06 14:36:27'),(7,1,'Luciana','Villanueva Perez','demo@gmail.com','7c4a8d09ca3762af61e59520943dc26494f8941b','2016-11-06 12:41:32','2016-11-06 14:19:07'),(9,3,'Kevin Jose','Patherson','kpatherson@anexsoft.com','7c4a8d09ca3762af61e59520943dc26494f8941b','2016-11-06 14:19:29','2017-05-13 01:01:45');

/*Table structure for table `reporte_inventario` */

DROP TABLE IF EXISTS `reporte_inventario`;

/*!50001 DROP VIEW IF EXISTS `reporte_inventario` */;
/*!50001 DROP TABLE IF EXISTS `reporte_inventario` */;

/*!50001 CREATE TABLE  `reporte_inventario`(
 `anio` int(4) ,
 `mes` int(2) ,
 `movimiento` varchar(30) ,
 `tipo` enum('ENTRADA','SALIDA') ,
 `documento` varchar(7) ,
 `serie` varchar(4) ,
 `numero` varchar(10) ,
 `producto` varchar(200) ,
 `cantidad` decimal(10,2) ,
 `created_at` datetime 
)*/;

/*View structure for view reporte_inventario */

/*!50001 DROP TABLE IF EXISTS `reporte_inventario` */;
/*!50001 DROP VIEW IF EXISTS `reporte_inventario` */;

/*!50001 CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `reporte_inventario` AS select year(`k`.`created_at`) AS `anio`,month(`k`.`created_at`) AS `mes`,`tm`.`nombre` AS `movimiento`,`tm`.`tipo` AS `tipo`,(case `c`.`tipo` when '01' then 'Factura' when '03' then 'Boleta' else 'Ticket' end) AS `documento`,`c`.`serie` AS `serie`,`c`.`numero` AS `numero`,`p`.`nombre` AS `producto`,`k`.`cantidad` AS `cantidad`,`k`.`created_at` AS `created_at` from (((`kardex` `k` join `producto` `p` on((`k`.`producto_id` = `p`.`id`))) join `tipo_movimiento` `tm` on((`k`.`tipo` = `tm`.`id`))) join `comprobante` `c` on((`k`.`documento_id` = `c`.`id`))) order by `k`.`created_at` desc */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
