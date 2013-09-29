USE ccwatch;

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` int(11) DEFAULT NULL,
  `key` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `config` (`id`, `parentid`, `key`, `value`, `description`)
VALUES
	(1,0,'pools','','Parent for All Pools'),
	(2,0,'markets','','Parent for All Markets');
   (3,0,'exchanges','','Parent for All Exchanges');

DROP TABLE IF EXISTS `market`;

CREATE TABLE `miners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `host` varchar(255) DEFAULT NULL,
  `user` varchar(255) DEFAULT NULL,
  `bin` varchar(255) DEFAULT NULL,
  `userpass` varchar(255) DEFAULT NULL,
  `poolurl` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `market` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `high` float DEFAULT NULL,
  `low` float DEFAULT NULL,
  `average` float DEFAULT NULL,
  `volume` float DEFAULT NULL,
  `volume_current` float DEFAULT NULL,
  `last` float DEFAULT NULL,
  `buy` float DEFAULT NULL,
  `sell` float DEFAULT NULL,
  `updated` int(11) DEFAULT NULL,
  `server_time` int(11) DEFAULT NULL,
  `local_time` int(11) DEFAULT NULL,
  `source` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `slaves`;

CREATE TABLE `slaves` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `poolid` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `alive` int(11) DEFAULT NULL,
  `hashrate` float DEFAULT NULL,
  `lastreport` int(11) DEFAULT NULL,
  `local_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
