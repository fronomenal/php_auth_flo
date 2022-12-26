DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id`integer PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0'
);

DROP TABLE IF EXISTS `requests`;
CREATE TABLE `requests` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `user` unsigned bigint(20) DEFAULT NULL,
  `hash` varchar(255) DEFAULT NULL,
  `timestamp` unsigned int(10) DEFAULT NULL,
  `type` int(11) DEFAULT NULL
);


DROP TABLE IF EXISTS `loginattempts`;
CREATE TABLE `loginattempts` (
  `id` integer PRIMARY KEY AUTOINCREMENT,
  `user` unsigned bigint(20) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `timestamp` unsigned int(10) DEFAULT NULL
);