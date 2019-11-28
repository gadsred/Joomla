CREATE TABLE IF NOT EXISTS `#__mt_staff` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`user_id` INT(11)  NOT NULL ,
`name` VARCHAR(255)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`image` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

