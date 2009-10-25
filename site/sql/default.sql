DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `login` varchar(255) NOT NULL default '',
  `passwd` varchar(255) NOT NULL default '',
  `level` varchar(255) NOT NULL default 'user',
  `firstName` varchar(255) NOT NULL default '',
  `lastName` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES (1,'admin','70c9d3d094e3b1c331174946b7c93ec1','admin','','');
UNLOCK TABLES;