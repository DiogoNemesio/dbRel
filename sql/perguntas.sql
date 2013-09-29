-- MySQL dump 10.13  Distrib 5.5.30, for Linux (x86_64)
--
-- Host: localhost    Database: drFritz
-- ------------------------------------------------------
-- Server version	5.5.30-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `QUESTIONARIOS`
--

DROP TABLE IF EXISTS `QUESTIONARIOS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `QUESTIONARIOS` (
  `CODIGO` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NOME` varchar(60) DEFAULT NULL,
  `COD_STATUS` varchar(1) NOT NULL,
  `COD_TIPO` varchar(1) NOT NULL,
  PRIMARY KEY (`CODIGO`),
  KEY `QUESTIONARIOS_FK01_idx` (`COD_STATUS`),
  KEY `fk_QUESTIONARIOS_1_idx` (`COD_TIPO`),
  CONSTRAINT `QUESTIONARIOS_FK01` FOREIGN KEY (`COD_STATUS`) REFERENCES `TIPO_STATUS` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `QUESTIONARIOS_FK2` FOREIGN KEY (`COD_TIPO`) REFERENCES `TIPO_QUESTIONARIO` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `QUESTIONARIOS`
--

LOCK TABLES `QUESTIONARIOS` WRITE;
/*!40000 ALTER TABLE `QUESTIONARIOS` DISABLE KEYS */;
INSERT INTO `QUESTIONARIOS` VALUES (1,'1° Retorno','A','F'),(2,'Paciente AUX 01','A','A'),(3,'Outros Retornos','A','F');
/*!40000 ALTER TABLE `QUESTIONARIOS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PERGUNTAS`
--

DROP TABLE IF EXISTS `PERGUNTAS`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PERGUNTAS` (
  `CODIGO` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `COD_QUESTIONARIO` int(10) unsigned NOT NULL,
  `DESCRICAO` varchar(100) NOT NULL,
  `COD_TIPO` varchar(2) NOT NULL,
  `COD_STATUS` varchar(1) NOT NULL,
  `ORDEM` int(10) unsigned NOT NULL,
  `COD_OBRIGATORIO` varchar(1) NOT NULL,
  PRIMARY KEY (`CODIGO`),
  KEY `PERGUNTAS_FK01_idx` (`COD_QUESTIONARIO`),
  KEY `PERGUNTAS_FK02_idx` (`COD_STATUS`),
  KEY `PERGUNTAS_FK03_idx` (`COD_TIPO`),
  KEY `PERGUNTAS_FK04_idx` (`COD_OBRIGATORIO`),
  CONSTRAINT `PERGUNTAS_FK01` FOREIGN KEY (`COD_QUESTIONARIO`) REFERENCES `QUESTIONARIOS` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `PERGUNTAS_FK02` FOREIGN KEY (`COD_STATUS`) REFERENCES `TIPO_STATUS` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `PERGUNTAS_FK03` FOREIGN KEY (`COD_TIPO`) REFERENCES `TIPO_PERGUNTA` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `PERGUNTAS_FK04` FOREIGN KEY (`COD_OBRIGATORIO`) REFERENCES `TIPO_OBRIGATORIO` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PERGUNTAS`
--

LOCK TABLES `PERGUNTAS` WRITE;
/*!40000 ALTER TABLE `PERGUNTAS` DISABLE KEYS */;
INSERT INTO `PERGUNTAS` VALUES (1,2,'Qual a data de Nascimento','D','A',1,'N'),(2,2,'Qual a religião','L','A',2,'S'),(3,2,'Quantos filhos','N','A',3,'S'),(4,2,'É diabético','SN','A',4,'S'),(5,2,'OBS','T','A',5,'N'),(6,2,'Qual o sexo','L','A',6,'S'),(7,2,'Qual o seu problema básico','T','A',7,'S'),(8,1,'Trouxe algum exame ou laudo médico','SN','A',1,'S'),(9,1,'Qual a data do término do tratamento','D','A',2,'N'),(10,1,'Seguia as recomendações após a cirurgia','SN','A',3,'S'),(11,1,'Tomou o chá','SN','A',4,'S'),(12,1,'Sentiu melhora, piora ou está curado','T','A',5,'N'),(13,3,'Tem algum novo exame ou laudo médico','SN','A',1,'S'),(14,3,'Seguia as recomendações após a cirurgia','SN','A',2,'S'),(15,3,'Tomou o chá','SN','A',3,'S'),(16,3,'Sentiu melhora, piora ou está curado','T','A',4,'N');
/*!40000 ALTER TABLE `PERGUNTAS` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `VALORES_PERGUNTA`
--

DROP TABLE IF EXISTS `VALORES_PERGUNTA`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VALORES_PERGUNTA` (
  `COD_PERGUNTA` int(10) unsigned NOT NULL,
  `COD_VALOR` varchar(200) NOT NULL,
  PRIMARY KEY (`COD_PERGUNTA`,`COD_VALOR`),
  KEY `VALORES_PERGUNTA_FK01_idx` (`COD_PERGUNTA`),
  CONSTRAINT `VALORES_PERGUNTA_FK01` FOREIGN KEY (`COD_PERGUNTA`) REFERENCES `PERGUNTAS` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `VALORES_PERGUNTA`
--

LOCK TABLES `VALORES_PERGUNTA` WRITE;
/*!40000 ALTER TABLE `VALORES_PERGUNTA` DISABLE KEYS */;
INSERT INTO `VALORES_PERGUNTA` VALUES (2,'Católica'),(2,'Espírita'),(2,'Evangélica'),(6,'Feminino'),(6,'Masculino');
/*!40000 ALTER TABLE `VALORES_PERGUNTA` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PACIENTES`
--

DROP TABLE IF EXISTS `PACIENTES`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PACIENTES` (
  `CODIGO` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NOME` varchar(60) NOT NULL,
  `COD_SEXO` varchar(1) NOT NULL,
  `EMAIL` varchar(200) DEFAULT NULL,
  `TELEFONE` varchar(14) DEFAULT NULL,
  `CELULAR` varchar(14) DEFAULT NULL,
  `DATA_NASC` date DEFAULT NULL,
  `DATA_CAD` date NOT NULL,
  `PROFISSAO` varchar(60) DEFAULT NULL,
  `COD_CIDADE` varchar(8) DEFAULT NULL,
  `ENDERECO` varchar(100) DEFAULT NULL,
  `BAIRRO` varchar(60) DEFAULT NULL,
  `FOTO` mediumblob,
  `FORM_CAD` mediumblob,
  PRIMARY KEY (`CODIGO`),
  KEY `PACIENTES_FK01_idx` (`COD_CIDADE`),
  KEY `PACIENTES_FK02_idx` (`COD_SEXO`),
  CONSTRAINT `PACIENTES_FK01` FOREIGN KEY (`COD_CIDADE`) REFERENCES `CIDADES` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `PACIENTES_FK02` FOREIGN KEY (`COD_SEXO`) REFERENCES `TIPO_SEXO` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PACIENTES`
--

LOCK TABLES `PACIENTES` WRITE;
/*!40000 ALTER TABLE `PACIENTES` DISABLE KEYS */;
INSERT INTO `PACIENTES` VALUES (1,'Daniel Cassela','M','cassela@globo.com','8233585224','8299999611','1980-11-13','2013-04-14','Analista de Sistemas','5300108','','Mangabeiras',NULL,NULL),(2,'Andrea Cristina','F','deacassela@hotmail.com','','8299925385','1980-03-01','2013-04-14','','2703502','','centro',NULL,NULL),(3,'Henrique Cassela','M','kikocassela@hotmail.com','','(82)9820398230','2000-03-17','2013-04-14','Estudante','2704302','','Antares',NULL,NULL),(4,'Mirella Monteiro','F','lelacassela@hotmail.com','','(82) ','2004-06-28','2013-04-14','','2704302','','Antares',NULL,NULL);
/*!40000 ALTER TABLE `PACIENTES` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PACIENTE_CADASTRO_AUX`
--

DROP TABLE IF EXISTS `PACIENTE_CADASTRO_AUX`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PACIENTE_CADASTRO_AUX` (
  `COD_PACIENTE` int(10) unsigned NOT NULL,
  `COD_PERGUNTA` int(10) unsigned NOT NULL,
  `VALOR` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`COD_PACIENTE`,`COD_PERGUNTA`),
  KEY `fk_PACIENTE_CADASTRO_AUX_1_idx` (`COD_PACIENTE`),
  KEY `fk_PACIENTE_CADASTRO_AUX_2_idx` (`COD_PERGUNTA`),
  CONSTRAINT `fk_PACIENTE_CADASTRO_AUX_1` FOREIGN KEY (`COD_PACIENTE`) REFERENCES `PACIENTES` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_PACIENTE_CADASTRO_AUX_2` FOREIGN KEY (`COD_PERGUNTA`) REFERENCES `PERGUNTAS` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PACIENTE_CADASTRO_AUX`
--

LOCK TABLES `PACIENTE_CADASTRO_AUX` WRITE;
/*!40000 ALTER TABLE `PACIENTE_CADASTRO_AUX` DISABLE KEYS */;
INSERT INTO `PACIENTE_CADASTRO_AUX` VALUES (1,1,'03/04/2013'),(1,2,'Espírita'),(1,3,'2'),(1,4,'Não'),(1,5,'obs 1'),(1,6,'Masculino'),(1,7,'Espirrando toda hora'),(2,1,'14/04/2013'),(2,2,'Católica'),(2,3,'2'),(2,4,'Não'),(2,5,'nenhuma'),(2,6,'Feminino'),(2,7,'Dores de cabeça'),(3,1,'13/04/2013'),(3,2,'Católica'),(3,3,'0'),(3,4,'Não'),(3,5,'nenhuma'),(3,6,'Masculino'),(3,7,'Alergia'),(4,1,'10/04/2013'),(4,2,'Evangélica'),(4,3,'0'),(4,4,'Não'),(4,5,'.'),(4,6,'Feminino'),(4,7,'Dor no joelho');
/*!40000 ALTER TABLE `PACIENTE_CADASTRO_AUX` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `PACIENTE_TEMPLO`
--

DROP TABLE IF EXISTS `PACIENTE_TEMPLO`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PACIENTE_TEMPLO` (
  `COD_PACIENTE` int(10) unsigned NOT NULL,
  `COD_TEMPLO` int(10) unsigned NOT NULL,
  PRIMARY KEY (`COD_TEMPLO`,`COD_PACIENTE`),
  KEY `PACIENTE_TEMPLO_FK01_idx` (`COD_PACIENTE`),
  KEY `PACIENTE_TEMPLO_FK02_idx` (`COD_TEMPLO`),
  CONSTRAINT `PACIENTE_TEMPLO_FK01` FOREIGN KEY (`COD_PACIENTE`) REFERENCES `PACIENTES` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `PACIENTE_TEMPLO_FK02` FOREIGN KEY (`COD_TEMPLO`) REFERENCES `TEMPLOS` (`CODIGO`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PACIENTE_TEMPLO`
--

LOCK TABLES `PACIENTE_TEMPLO` WRITE;
/*!40000 ALTER TABLE `PACIENTE_TEMPLO` DISABLE KEYS */;
INSERT INTO `PACIENTE_TEMPLO` VALUES (1,1),(2,1),(3,1),(4,1);
/*!40000 ALTER TABLE `PACIENTE_TEMPLO` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-04-20 17:25:37
