CREATE TABLE IF NOT EXISTS `#__jdm_git_updates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `manual` char(16) COLLATE utf8mb4_unicode_ci NOT NULL,
  `language` char(8) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_update` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
