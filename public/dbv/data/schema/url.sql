CREATE TABLE `url` (
  `url_id` int(11) NOT NULL AUTO_INCREMENT,
  `title_desc` text,
  `path_url` text NOT NULL,
  `clicks_cnt` int(11) DEFAULT '0',
  PRIMARY KEY (`url_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1