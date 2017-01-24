CREATE DATABASE `SMS_Manager` CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `SMS_Manager`;

-- Create syntax for TABLE 'message'
CREATE TABLE `message` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(60) NOT NULL DEFAULT '',
  `sender` varchar(60) DEFAULT NULL,
  `message_body` varchar(255) DEFAULT '',
  `slot` int(1) DEFAULT NULL,
  `timestamp` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'receiver_auth'
CREATE TABLE `receiver_auth` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(60) NOT NULL DEFAULT '',
  `user_id` varchar(60) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
  UNIQUE KEY `uuid` (`uuid`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'simple_id'
CREATE TABLE `simple_id` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `simple_id` varchar(11) NOT NULL DEFAULT '',
  `uuid` varchar(60) NOT NULL DEFAULT '',
  `last_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `simple_id` (`simple_id`),
  UNIQUE KEY `uuid` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Create syntax for TABLE 'auth_user'
CREATE TABLE `auth_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) DEFAULT '',
  `password` varchar(256) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
