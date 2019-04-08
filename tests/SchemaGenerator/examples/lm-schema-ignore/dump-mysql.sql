SET foreign_key_checks = 1;
SET time_zone = "SYSTEM";
SET sql_mode = "TRADITIONAL";

CREATE TABLE `author` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
CHARACTER SET=utf8mb4
COLLATE=utf8mb4_czech_ci;
