CREATE TABLE `user` (
  `id` bigint(11) unsigned NOT NULL,
  `screen_name` varchar(100) NOT NULL DEFAULT '',
  `profile_image_url` varchar(300) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;