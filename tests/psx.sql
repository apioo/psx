
--
-- Table structure for table `psx_cache_handler_sql_test`
--
DROP TABLE IF EXISTS `psx_cache_handler_sql_test`;
CREATE TABLE `psx_cache_handler_sql_test` (
  `id` varchar(32) NOT NULL,
  `content` blob NOT NULL,
  `date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `psx_example`
--
DROP TABLE IF EXISTS `psx_example`;
CREATE TABLE `psx_example` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `place` int(10) NOT NULL,
  `region` varchar(64) NOT NULL,
  `population` int(10) NOT NULL,
  `users` int(10) NOT NULL,
  `world_users` float NOT NULL,
  `datetime` varchar(25) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Table structure for table `psx_handler_article`
--
DROP TABLE IF EXISTS `psx_handler_comment`;
CREATE TABLE `psx_handler_comment` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userId` int(10) NOT NULL,
  `title` varchar(32) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `psx_session_handler_sql_test`
--
DROP TABLE IF EXISTS `psx_session_handler_sql_test`;
CREATE TABLE `psx_session_handler_sql_test` (
  `id` varchar(32) NOT NULL,
  `content` blob NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `psx_sql_table_select_group`
--
CREATE TABLE IF NOT EXISTS `psx_sql_table_select_group` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `psx_sql_table_select_news`
--
DROP TABLE IF EXISTS `psx_sql_table_select_news`;
CREATE TABLE `psx_sql_table_select_news` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userId` int(10) NOT NULL,
  `title` varchar(32) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `psx_sql_table_select_user`
--
DROP TABLE IF EXISTS `psx_sql_table_select_user`;
CREATE TABLE `psx_sql_table_select_user` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `groupId` int(10) NOT NULL,
  `name` varchar(16) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `psx_sql_table_select_usernews`
--
DROP TABLE IF EXISTS `psx_sql_table_select_usernews`;
CREATE TABLE `psx_sql_table_select_usernews` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `userId` int(10) NOT NULL,
  `newsId` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `psx_sql_table_test`
--
DROP TABLE IF EXISTS `psx_sql_table_test`;
CREATE TABLE `psx_sql_table_test` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `psx_sql_test`
--
DROP TABLE IF EXISTS `psx_sql_test`;
CREATE TABLE `psx_sql_test` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `psx_table_command_test`
--
DROP TABLE IF EXISTS `psx_table_command_test`;
CREATE TABLE `psx_table_command_test` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `col_bigint` bigint NOT NULL, 
  `col_blob` longblob NOT NULL, 
  `col_boolean` tinyint(1) NOT NULL, 
  `col_datetime` datetime NOT NULL, 
  `col_datetimetz` datetime NOT NULL, 
  `col_date` date NOT NULL, 
  `col_decimal` numeric(10, 0) NOT NULL, 
  `col_float` double precision NOT NULL, 
  `col_integer` int NOT NULL, 
  `col_smallint` smallint NOT NULL, 
  `col_text` longtext NOT NULL, 
  `col_time` time NOT NULL, 
  `col_string` varchar(255) NOT NULL,
  `col_array` longtext NOT NULL,
  `col_object` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
