# Dump of table config
# ------------------------------------------------------------

DROP TABLE IF EXISTS `config`;

CREATE TABLE `config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) DEFAULT NULL,
  `value` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `parentid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO config (id, key, value, description, parentid) VALUES (2, 'markets', '0', 'Markets', NULL);
INSERT INTO config (id, key, value, description, parentid) VALUES (1, 'pools', '0', 'Pools', NULL);

# Dump of table market
# ------------------------------------------------------------

DROP TABLE IF EXISTS `market`;

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



# Dump of table pool
# ------------------------------------------------------------

DROP TABLE IF EXISTS `pool`;

CREATE TABLE `pool` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `poolid` int(255) DEFAULT NULL,
  `balance` double DEFAULT NULL,
  `round_estimate` double DEFAULT NULL,
  `total_hashrate` double DEFAULT NULL,
  `total_income` double DEFAULT NULL,
  `round_shares` int(11) DEFAULT NULL,
  `local_time` int(11) DEFAULT NULL,
  `income_estimate` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table slaves
# ------------------------------------------------------------

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


/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
