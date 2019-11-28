CREATE TABLE IF NOT EXISTS `#__mt_faq` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`link_id` INT(11)  NOT NULL ,
`question` VARCHAR(300)  NOT NULL ,
`answer` VARCHAR(300)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

