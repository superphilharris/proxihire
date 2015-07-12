CREATE TABLE `asset_rate` (
  `asset_id` int(11) NOT NULL,
  `duration_hrs` float NOT NULL,
  `price_dlr` decimal(10,0) DEFAULT '0',
  PRIMARY KEY (`asset_id`,`duration_hrs`),
  KEY `fk_asset_rate_asset1_idx` (`asset_id`),
  CONSTRAINT `fk_asset_rate_asset1` FOREIGN KEY (`asset_id`) REFERENCES `asset` (`asset_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1