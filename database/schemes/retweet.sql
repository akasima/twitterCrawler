CREATE TABLE `my_tweet` (
  `id` bigint(11) unsigned NOT NULL,
  `user_id` bigint(11) NOT NULL,
  `screen_name` varchar(100) NOT NULL DEFAULT '',
  `content` varchar(400) NOT NULL DEFAULT '',
  `retweet_count` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;