CREATE TABLE `category_alias` (
  `category_id` int(11) NOT NULL,
  `alias_fulnam` varchar(255) NOT NULL,
  PRIMARY KEY (`category_id`,`alias_fulnam`),
  CONSTRAINT `fk_category_alias_category1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1