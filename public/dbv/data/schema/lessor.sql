CREATE TABLE `lessor` (
  `user_id` int(11) NOT NULL,
  `url_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `fk_lessor_url1_idx` (`url_id`),
  KEY `fk_lessor_user1_idx` (`user_id`),
  CONSTRAINT `fk_lessor_url1` FOREIGN KEY (`url_id`) REFERENCES `url` (`url_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_lessor_user1` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=latin1