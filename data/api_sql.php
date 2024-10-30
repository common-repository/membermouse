<?php
$sql = array();

$sql[] = "Drop table IF EXISTS mm_container;";
$sql[] = "CREATE TABLE IF NOT EXISTS `mm_container` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `obj` text NOT NULL,
  `is_system` tinyint NOT NULL default '0',
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
