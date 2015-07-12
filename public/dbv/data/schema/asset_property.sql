CREATE TABLE `asset_property` (
  `asset_id` int(11) NOT NULL,
  `name_fulnam` varchar(255) NOT NULL,
  `datatype_id` int(11) NOT NULL,
  `value_mxd` text NOT NULL,
  PRIMARY KEY (`asset_id`,`name_fulnam`),
  KEY `fk_asset_property_asset1_idx` (`asset_id`),
  KEY `fk_asset_property_datatype1_idx` (`datatype_id`),
  CONSTRAINT `fk_asset_property_asset1` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`asset_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_asset_property_datatype1` FOREIGN KEY (`datatype_id`) REFERENCES `datatype` (`datatype_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1