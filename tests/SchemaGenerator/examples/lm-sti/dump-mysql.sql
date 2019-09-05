SET foreign_key_checks = 1;
SET time_zone = "SYSTEM";
SET sql_mode = "TRADITIONAL";

CREATE TABLE `client` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`type` TEXT NOT NULL,
	`name` TEXT NOT NULL,
	`ic` TEXT NULL,
	`dic` TEXT NULL,
	`orders` INT(11) NOT NULL,
	`birthdate` DATETIME NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
CHARACTER SET=utf8mb4
COLLATE=utf8mb4_czech_ci;
