USE tasks_db
CREATE TABLE IF NOT EXISTS tasks (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
	`name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
	`is_complete` tinyint(1) NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`))