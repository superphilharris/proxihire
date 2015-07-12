CREATE TABLE `location` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `name_fulnam` varchar(255) DEFAULT NULL,
  `latitude_float` float NOT NULL,
  `longitude_float` float NOT NULL,
  PRIMARY KEY (`location_id`),
  UNIQUE KEY `name_fulnam_UNIQUE` (`name_fulnam`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1