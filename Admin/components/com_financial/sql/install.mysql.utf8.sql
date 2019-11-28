CREATE TABLE IF NOT EXISTS `#__financial_pastinvoice` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`created_by` INT(11)  NOT NULL ,
`invoice_id` VARCHAR(255)  NOT NULL ,
`date` VARCHAR(255)  NOT NULL ,
`charges` VARCHAR(255)  NOT NULL ,
`payments` VARCHAR(255)  NOT NULL ,
`paid` VARCHAR(255)  NOT NULL ,
`paid_on` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

