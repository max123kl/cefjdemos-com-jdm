CREATE TABLE IF NOT EXISTS `#__jdm_feedback` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `session_id` VARCHAR(32) NOT NULL ,
    `manual` VARCHAR(16) NOT NULL ,
    `language` VARCHAR(8) NOT NULL ,
    `heading` VARCHAR(128) NOT NULL ,
    `filename` VARCHAR(256) NOT NULL ,
    `likeitornot` VARCHAR(8) NULL,
    `comment` VARCHAR(1024) NULL ,
    `date_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `session` (`session_id`),
    KEY `manual` (`manual`),
    KEY `language` (`language`),
    KEY `heading` (`heading`),
    KEY `filename` (`filename`),
    KEY `likeitornot` (`likeitornot`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
