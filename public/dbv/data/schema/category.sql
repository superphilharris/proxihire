CREATE TABLE `category` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name_fulnam` varchar(255) NOT NULL,
  `parent_category_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `fk_category_category1_idx` (`parent_category_id`),
  CONSTRAINT `fk_category_category1` FOREIGN KEY (`parent_category_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1