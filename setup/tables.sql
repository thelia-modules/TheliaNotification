
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- notification
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `notification`;

CREATE TABLE `notification`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(255),
    `type` TINYINT NOT NULL,
    `url` VARCHAR(255),
    `title` VARCHAR(255),
    `message` TEXT(255),
    `message_type` CHAR(15),
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    INDEX `idx_notification_code` (`code`)
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- notification_customer
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `notification_customer`;

CREATE TABLE `notification_customer`
(
    `notification_id` INTEGER NOT NULL,
    `customer_id` INTEGER NOT NULL,
    `read_date` DATETIME,
    `hide` TINYINT(1),
    PRIMARY KEY (`notification_id`,`customer_id`),
    UNIQUE INDEX `notification_customer_UNIQUE` (`notification_id`, `customer_id`),
    INDEX `idx_notification_customer_read_date` (`read_date`),
    INDEX `idx_notification_customer_hide` (`hide`),
    INDEX `FI_notification_customer__customer` (`customer_id`),
    CONSTRAINT `fk_notification_customer__notification`
        FOREIGN KEY (`notification_id`)
        REFERENCES `notification` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_notification_customer__customer`
        FOREIGN KEY (`customer_id`)
        REFERENCES `customer` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- notification_admin
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `notification_admin`;

CREATE TABLE `notification_admin`
(
    `notification_id` INTEGER NOT NULL,
    `admin_id` INTEGER NOT NULL,
    `read_date` DATETIME,
    `hide` TINYINT(1),
    PRIMARY KEY (`notification_id`,`admin_id`),
    UNIQUE INDEX `notification_admin_UNIQUE` (`notification_id`, `admin_id`),
    INDEX `idx_notification_admin_read_date` (`read_date`),
    INDEX `idx_notification_admin_hide` (`hide`),
    INDEX `FI_notification_admin__admin` (`admin_id`),
    CONSTRAINT `fk_notification_admin__notification`
        FOREIGN KEY (`notification_id`)
        REFERENCES `notification` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `fk_notification_admin__admin`
        FOREIGN KEY (`admin_id`)
        REFERENCES `admin` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
