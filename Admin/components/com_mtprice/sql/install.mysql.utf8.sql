CREATE TABLE IF NOT EXISTS `#__mt_price` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`user_id` INT(11)  NOT NULL ,
`au_state` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(300)  NOT NULL ,
`price_type` VARCHAR(255)  NOT NULL ,
`keypoints_type` VARCHAR(255)  NOT NULL ,
`price` DOUBLE NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

