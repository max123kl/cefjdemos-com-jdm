ALTER TABLE `#__jdm_languages` ADD `locale` VARCHAR(8) NOT NULL AFTER `code`;

UPDATE `#__jdm_languages` SET `locale` = 'en-GB' WHERE `code` = 'en';
UPDATE `#__jdm_languages` SET `locale` = 'nl-NL' WHERE `code` = 'nl';
UPDATE `#__jdm_languages` SET `locale` = 'fr-FR' WHERE `code` = 'fr';
UPDATE `#__jdm_languages` SET `locale` = 'de-DE' WHERE `code` = 'de';
UPDATE `#__jdm_languages` SET `locale` = 'es-ES' WHERE `code` = 'es';
UPDATE `#__jdm_languages` SET `locale` = 'pt-PT' WHERE `code` = 'pt';
UPDATE `#__jdm_languages` SET `locale` = 'pt-BR' WHERE `code` = 'ptbr';
UPDATE `#__jdm_languages` SET `locale` = 'ru-RU' WHERE `code` = 'ru';
UPDATE `#__jdm_languages` SET `locale` = 'it-IT' WHERE `code` = 'it';

UPDATE `#__jdm_articles` SET `state` = 1 WHERE `state` = 0;
