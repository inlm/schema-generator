
CREATE TABLE `book` (
	`id` TEXT NOT NULL,
	`name` TEXT NOT NULL,
	`description` TEXT NULL,
	`website` TEXT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY `website` (`website`),
	KEY `name_website` (`name`, `website`)
)
ENGINE=InnoDB
CHARACTER SET=utf8mb4
COLLATE=utf8mb4_czech_ci;
