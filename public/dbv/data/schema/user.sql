CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name_fulnam` varchar(255) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `location_id_UNIQUE` (`location_id`),
  KEY `fk_user_location1_idx` (`location_id`),
  CONSTRAINT `fk_user_location1` FOREIGN KEY (`location_id`) REFERENCES `location` (`location_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1