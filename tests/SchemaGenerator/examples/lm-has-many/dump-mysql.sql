SET foreign_key_checks = 1;
SET time_zone = "SYSTEM";
SET sql_mode = "TRADITIONAL";

CREATE TABLE `book` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` TEXT NOT NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
CHARACTER SET=utf8mb4
COLLATE=utf8mb4_czech_ci;

CREATE TABLE `tag` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` TEXT NOT NULL,
	PRIMARY KEY (`id`)
)
ENGINE=InnoDB
CHARACTER SET=utf8mb4
COLLATE=utf8mb4_czech_ci;

CREATE TABLE `book_tag` (
	`book_id` INT(10) UNSIGNED NOT NULL,
	`tag_id` INT(10) UNSIGNED NOT NULL,
	PRIMARY KEY (`book_id`, `tag_id`),
	KEY `tag_id` (`tag_id`),
	CONSTRAINT `book_tag_fk_book_id` FOREIGN KEY (`book_id`) REFERENCES `book` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
	CONSTRAINT `book_tag_fk_tag_id` FOREIGN KEY (`tag_id`) REFERENCES `tag` (`id`) ON DELETE RESTRICT ON UPDATE RESTRICT
)
ENGINE=InnoDB
CHARACTER SET=utf8mb4
COLLATE=utf8mb4_czech_ci;
