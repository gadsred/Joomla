CREATE TABLE IF NOT EXISTS `#__mt_linked_listings` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`main_link` INT(15)  NOT NULL ,
`sub_link` INT(15)  NOT NULL ,
`subs_id` INT(11)  NOT NULL ,
`subs_type` TINYINT(1)  NOT NULL ,
`user_id` INT(11)  NOT NULL ,
`link_created` DATETIME NOT NULL ,
`link_updated` DATETIME NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

