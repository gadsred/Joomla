CREATE TABLE IF NOT EXISTS `#__loan_investment_info` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`user_id` INT(11)  NOT NULL ,
`provider_name` VARCHAR(50)  NOT NULL ,
`loan_display_name` TEXT NOT NULL ,
`maximum_lvr` VARCHAR(255)  NOT NULL ,
`loan_term` VARCHAR(255)  NOT NULL ,
`borrowing_amount_range` VARCHAR(255)  NOT NULL ,
`refinance` VARCHAR(255)  NOT NULL ,
`line_of_credit` VARCHAR(255)  NOT NULL ,
`self_managed_super` VARCHAR(255)  NOT NULL ,
`interest_rate_structure` VARCHAR(255)  NOT NULL ,
`interest_only` VARCHAR(255)  NOT NULL ,
`loan_allows_split_interest_rate` VARCHAR(255)  NOT NULL ,
`principal_interest` VARCHAR(255)  NOT NULL ,
`states_applicable` VARCHAR(255)  NOT NULL ,
`redraw_facility` VARCHAR(255)  NOT NULL ,
`redraw_fee` VARCHAR(12)  NOT NULL ,
`extra_repayments` VARCHAR(255)  NOT NULL ,
`weekly_repayments` VARCHAR(255)  NOT NULL ,
`fortnightly_repayments` VARCHAR(255)  NOT NULL ,
`monthly_repayments` VARCHAR(255)  NOT NULL ,
`date_created` DATETIME NOT NULL ,
`date_modified` DATETIME NOT NULL ,
`created_by` INT(11)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'Investment','com_loan_investment.investment','{"special":{"dbtable":"#__loan_investment_info","key":"id","type":"Investment","prefix":"Loan_investmentTable"}}', '{"formFile":"administrator\/components\/com_loan_investment\/models\/forms\/investment.xml", "hideFields":["checked_out","checked_out_time","params","language" ,"loan_display_name"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_loan_investment.investment')
) LIMIT 1;
