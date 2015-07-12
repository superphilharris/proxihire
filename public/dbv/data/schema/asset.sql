CREATE TABLE `asset` (
  `asset_id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `url_id` int(11) DEFAULT NULL,
  `lessor_user_id` int(11) NOT NULL,
  PRIMARY KEY (`asset_id`),
  KEY `fk_asset_category_idx` (`category_id`),
  KEY `fk_asset_url1_idx` (`url_id`),
  KEY `fk_asset_lessor1_idx` (`lessor_user_id`),
  CONSTRAINT `fk_asset_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_asset_url1` FOREIGN KEY (`url_id`) REFERENCES `url` (`url_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_asset_lessor1` FOREIGN KEY (`lessor_user_id`) REFERENCES `lessor` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1