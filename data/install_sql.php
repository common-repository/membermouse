<?php
/**
 *
 * 
MemberMouse(TM) (http://www.membermouse.com)
(c) 2010-2011 Pop Fizz Studios, LLC. All rights reserved.
 */

$sql = array();
$sql[] = "CREATE TABLE IF NOT EXISTS `mm_cron` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`obj_name` VARCHAR( 125 ) NOT NULL ,
`obj_action` VARCHAR( 125 ) NOT NULL DEFAULT  'Process',
`last_processed` TIMESTAMP NOT NULL ,
`next_run` TIMESTAMP NOT NULL ,
`is_active` TINYINT NOT NULL DEFAULT  '1'
) ENGINE = MYISAM ;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_order_history` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`user_id` INT NOT NULL ,
`product_id` INT NOT NULL ,
`order_date` TIMESTAMP NOT NULL 
) ENGINE = MYISAM ;";

$sql[] = "CREATE TABLE IF NOT EXISTS  `mm_access_tags` (
  `id` int(10)  NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `is_free` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `badge_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"; 

$sql[]="CREATE TABLE IF NOT EXISTS  `mm_contexts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"; 

$sql[]="CREATE TABLE IF NOT EXISTS `mm_log_api` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`request` VARCHAR( 355 ) NOT NULL ,
`message` TEXT NOT NULL ,
`ipaddress` VARCHAR( 355 ) NOT NULL ,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM ;"; 

$sql[]="CREATE TABLE IF NOT EXISTS  `mm_member_types` (
  `id` int(10)  NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `is_free` tinyint NOT NULL DEFAULT '0',
  `is_default` tinyint(4) NOT NULL DEFAULT '0',
  `include_on_reg` tinyint NOT NULL DEFAULT '1',
  `description` text,
  `registration_product_id` int(10)  DEFAULT NULL,
  `status` int(11) NOT NULL,
  `upgrade_to_id` int(10)  DEFAULT NULL,
  `downgrade_to_id` int(10)  DEFAULT NULL,
  `email_subject` text NOT NULL,
  `email_body` text NOT NULL,
  `email_from_id` int(10) NOT NULL,
  `badge_url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB;"; 

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_member_type_products` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`member_type_id` INT NOT NULL ,
`product_id` INT NOT NULL
) ENGINE=InnoDB ";

$sql[]="CREATE TABLE IF NOT EXISTS mm_access_tag_products (
`access_tag_id` int(10) NOT NULL,
`product_id` int(10) NOT NULL
) ENGINE=InnoDB";

$sql[]="CREATE TABLE IF NOT EXISTS  `mm_posts_access` (
  `post_id` int(11) NOT NULL,
  `access_type` enum('member_type','access_tag') NOT NULL DEFAULT 'member_type',
  `access_id` int(11) NOT NULL,
  `days` char(5),
  `is_smart_content` TINYINT NOT NULL DEFAULT  '0',
  INDEX(post_id),
  INDEX(access_type),
  INDEX(is_smart_content),
  INDEX(access_id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$sql[]="CREATE TABLE IF NOT EXISTS  `mm_smarttag_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`,`visible`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"; 

$sql[]="CREATE TABLE IF NOT EXISTS  `mm_smarttags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `visible` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `group_id` (`group_id`,`visible`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;"; 

$sql[]="CREATE TABLE IF NOT EXISTS  `mm_smarttag_contexts` (
  `smarttag_id` int(11) NOT NULL,
  `context_id` int(11) NOT NULL,
  FOREIGN KEY (smarttag_id) REFERENCES mm_smarttags(id),
  FOREIGN KEY (context_id) REFERENCES mm_contexts(id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$sql[]="CREATE TABLE IF NOT EXISTS `mm_applied_access_tags` (
  `access_type` enum('user','member_type') NOT NULL DEFAULT 'member_type',
  `access_tag_id` int(11) NOT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `order_id` int(11) NOT NULL DEFAULT '0',
  `is_refunded` TINYINT NOT NULL DEFAULT '0',
  `product_id` int(11) NOT NULL DEFAULT '0',
  `status` TINYINT NOT NULL DEFAULT '1',
  `apply_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `unique_access_type` (`access_type`,`access_tag_id`,`ref_id`),
  KEY `access_type` (`access_type`),
  KEY `access_tag_id` (`access_tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_core_pages` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`page_id` INT NULL DEFAULT NULL ,
`core_page_type_id` INT NOT NULL ,
`ref_type` ENUM(  'member_type',  'error_type',  'access_tag',  'product' ) NULL DEFAULT NULL ,
`ref_id` INT NULL DEFAULT NULL ,
INDEX (  `core_page_type_id` ,  `ref_type` ,  `ref_id` , `page_id`)
) ENGINE = InnoDB;";

$sql[] = "CREATE TABLE IF NOT EXISTS   `mm_error_types` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL
) ENGINE = InnoDB;";

$sql[] = "CREATE TABLE IF NOT EXISTS   `mm_member_status_types` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL
) ENGINE = InnoDB;";

$sql[] = "CREATE TABLE IF NOT EXISTS  `mm_core_page_types` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL,
  `visible` tinyint NOT NULL default '1'
) ENGINE = InnoDB;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_account_member_types` (
  `member_type_id` int(11) DEFAULT NULL,
  `account_type_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$sql[] = "CREATE TABLE IF NOT EXISTS mm_account_types (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  num_sites int(11) NOT NULL,
  num_paid_members int(11) NOT NULL,
  unlimited_paid_members tinyint(4) NOT NULL DEFAULT '0',
  num_total_members int(11) NOT NULL,
  unlimited_total_members tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) DEFAULT '1',
  date_added timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` INT NOT NULL,
  `campaign_id` INT NOT NULL,
  `name` varchar(255) NOT NULL,
  `sku` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(20,2) NOT NULL,
  `category_name` varchar(210) NOT NULL,
  `is_shippable` tinyint(11) NOT NULL,
  `is_trial` tinyint(11) NOT NULL,
  `trial_frequency` enum('months','days','weeks','years') default 'months',
  `rebill_period` int(11) NOT NULL,
  `rebill_frequency` enum('months','days','weeks','years') default 'months',
	`trial_amount` FLOAT NULL DEFAULT NULL ,
	`trial_duration` INT NULL DEFAULT NULL ,
	`duration` INT NULL DEFAULT NULL ,
  `rebill_product_id` int(11) NOT NULL,
  `payment_id` int(11) NULL default NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;";

$sql[] ="CREATE TABLE IF NOT EXISTS `mm_campaigns` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `last_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_campaign_settings` (
  `campaign_id` int(11) NOT NULL,
  `setting_type` enum('shipping','country','payment') NOT NULL,
  `id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  KEY `campaign_id` (`campaign_id`),
  KEY `setting_type` (`setting_type`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_email_accounts` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,

  `fullname` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `role_id` INT NULL,
  `user_id` INT NULL,
  
  `is_default` tinyint(4) NOT NULL,
  `status` tinyint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_corepage_tag_requirements` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`core_page_type_id` INT NOT NULL ,
`smarttag_id` INT NOT NULL ,
`is_global` TINYINT default '1' ,
INDEX (  `core_page_type_id` ,  `smarttag_id` )
) ENGINE = INNODB;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_custom_fields` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`is_required` TINYINT default '1' ,
`show_on_reg` TINYINT default '1' ,
`show_on_myaccount` TINYINT default '1' ,
`field_name` varchar(255) NOT NULL ,
`field_label` varchar(255) NOT NULL
) ENGINE = INNODB;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_custom_field_data` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`custom_field_id` INT NOT NULL ,
`user_id` INT NOT NULL,
`value` varchar(255) NOT NULL ,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (custom_field_id) REFERENCES mm_custom_fields(id)
) ENGINE = INNODB;";

///API Table
$sql[] = "CREATE TABLE IF NOT EXISTS `mm_api_access_log` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`ip` VARCHAR( 255 ) NOT NULL ,
`referring_url` VARCHAR( 355 ) NOT NULL ,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM ;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_api_keys` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`api_key` VARCHAR( 255 ) NOT NULL ,
`api_secret` VARCHAR( 355 ) NOT NULL ,
`status` tinyint NOT NULL,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM ";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_notification_event_types` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`event_name` VARCHAR( 255 ) NOT NULL ,
`script_url` VARCHAR( 355 ) NOT NULL ,
`status` tinyint NOT NULL,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM; ";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_access_logs` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`event_type` varchar(255) NULL,
`ip` VARCHAR( 355 ) NOT NULL ,
`url` VARCHAR( 355 ) NOT NULL ,
`referrer` VARCHAR( 355 ) NOT NULL ,
`description` VARCHAR( 355 ) NOT NULL ,
`user_id` INT NOT NULL ,
`date_modified` TIMESTAMP NULL,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM; ";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_retention_reports` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`affiliate_id` VARCHAR( 255 ) NOT NULL ,
`sub_affiliate_id` VARCHAR( 255 ) NOT NULL ,
`order_id` INT NOT NULL ,
`product_id` INT NOT NULL ,
`user_id` INT NOT NULL ,
`payment_method_id` INT NOT NULL ,
`ref_id` INT NOT NULL ,
`ref_type` VARCHAR( 255 ) NOT NULL ,
`last_rebill_date` TIMESTAMP NULL DEFAULT NULL,
`date_modified` TIMESTAMP NULL DEFAULT NULL,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
INDEX (  `affiliate_id` ,  `order_id` )
) ENGINE = INNODB;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_campaign_options` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`setting_type` ENUM(  'shipping','gateway',  'payment',  'country' ) NOT NULL ,
`name` VARCHAR( 255 ) NOT NULL ,
`attr` TEXT NULL DEFAULT NULL ,
`show_on_reg` TINYINT NULL default NULL ,
`date_modified` TIMESTAMP NULL DEFAULT NULL ,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM ;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_callback_responses` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`payment_method` varchar(255)  NOT NULL ,
`response` TEXT NOT NULL ,
`payment_status` varchar(255)  NOT NULL ,
`user_id` INT NOT NULL ,
`payment_id` INT NOT NULL ,
`product_id` INT NOT NULL ,
`date_modified` TIMESTAMP NULL DEFAULT NULL ,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM ;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_roles` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`name` VARCHAR( 255 ) NOT NULL ,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`date_modified` TIMESTAMP NULL DEFAULT NULL
) ENGINE = MYISAM ;";

$sql[] = "CREATE TABLE IF NOT EXISTS `mm_permissions` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`role_id` INT NOT NULL,
`access_type` ENUM ('page','module') default 'module',
`access_name` varchar(255) NULL default NULL,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`date_modified` TIMESTAMP NULL DEFAULT NULL
) ENGINE = MYISAM ;";

$sql[] = "CREATE TABLE  IF NOT EXISTS `mm_version_releases` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`version` VARCHAR( 255 ) NOT NULL ,
`notes` TEXT NOT NULL ,
`date_added` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`date_modified` TIMESTAMP NULL DEFAULT NULL
) ENGINE = MYISAM ;";
?>