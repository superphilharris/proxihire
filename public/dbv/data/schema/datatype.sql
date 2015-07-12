CREATE TABLE `datatype` (
  `datatype_id` int(11) NOT NULL AUTO_INCREMENT,
  `datatype_abbr` varchar(45) NOT NULL,
  PRIMARY KEY (`datatype_id`),
  UNIQUE KEY `datatype_abbr_UNIQUE` (`datatype_abbr`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1