-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema carparkapp
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `carparkapp` ;

-- -----------------------------------------------------
-- Schema carparkapp
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `carparkapp` DEFAULT CHARACTER SET utf8 ;
USE `carparkapp` ;

-- -----------------------------------------------------
-- Table `carparkapp`.`carpark`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carparkapp`.`carpark` (
  `carpark_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `street` VARCHAR(45) NOT NULL,
  `street_number` INT NOT NULL,
  `zip_code` INT NOT NULL,
  `place` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`carpark_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `carparkapp`.`user_role`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carparkapp`.`user_role` (
  `user_role_id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`user_role_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `carparkapp`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carparkapp`.`user` (
  `user_id` INT NOT NULL AUTO_INCREMENT,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `email` VARCHAR(128) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `password_reset_hash` VARCHAR(64) NULL DEFAULT NULL,
  `password_reset_expires_at` DATETIME NULL DEFAULT NULL,
  `activation_hash` VARCHAR(64) NULL DEFAULT NULL,
  `is_active` TINYINT NULL DEFAULT '0',
  `user_role_id` INT NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC) VISIBLE,
  UNIQUE INDEX `id_UNIQUE` (`user_id` ASC) VISIBLE,
  UNIQUE INDEX `password_reset_hash_UNIQUE` (`password_reset_hash` ASC) VISIBLE,
  UNIQUE INDEX `activation_hash_UNIQUE` (`activation_hash` ASC) VISIBLE,
  INDEX `fk_user_user_role1_idx` (`user_role_id` ASC) VISIBLE,
  CONSTRAINT `fk_user_user_role1`
    FOREIGN KEY (`user_role_id`)
    REFERENCES `carparkapp`.`user_role` (`user_role_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `carparkapp`.`contract`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carparkapp`.`contract` (
  `contract_id` INT NOT NULL,
  `rfid_id` INT NOT NULL,
  `credit_item_per_day` FLOAT NOT NULL,
  `credit_item_sum` FLOAT NULL DEFAULT NULL,
  `is_blocked` TINYINT NULL DEFAULT '0',
  `user_id` INT NOT NULL,
  `carpark_id` INT NOT NULL,
  PRIMARY KEY (`contract_id`),
  UNIQUE INDEX `id_UNIQUE` (`contract_id` ASC) VISIBLE,
  INDEX `fk_contract_user1_idx` (`user_id` ASC) VISIBLE,
  INDEX `fk_contract_carpark1_idx` (`carpark_id` ASC) VISIBLE,
  CONSTRAINT `fk_contract_carpark1`
    FOREIGN KEY (`carpark_id`)
    REFERENCES `carparkapp`.`carpark` (`carpark_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_contract_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `carparkapp`.`user` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `carparkapp`.`contract_request`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carparkapp`.`contract_request` (
  `contract_request_id` INT NOT NULL AUTO_INCREMENT,
  `contract_auth` INT NOT NULL,
  `rfid_auth` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` INT NOT NULL,
  `contract_id` INT NULL DEFAULT NULL,
  PRIMARY KEY (`contract_request_id`),
  UNIQUE INDEX `id_UNIQUE` (`contract_request_id` ASC) VISIBLE,
  INDEX `fk_contract_request_user1_idx` (`user_id` ASC) VISIBLE,
  INDEX `fk_contract_request_contract1_idx` (`contract_id` ASC) VISIBLE,
  UNIQUE INDEX `contract_id_UNIQUE` (`contract_id` ASC) VISIBLE,
  CONSTRAINT `fk_contract_request_contract1`
    FOREIGN KEY (`contract_id`)
    REFERENCES `carparkapp`.`contract` (`contract_id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE,
  CONSTRAINT `fk_contract_request_user1`
    FOREIGN KEY (`user_id`)
    REFERENCES `carparkapp`.`user` (`user_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `carparkapp`.`csv_report`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carparkapp`.`csv_report` (
  `csv_report_id` INT NOT NULL AUTO_INCREMENT,
  `amount_credit_items` INT NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`csv_report_id`),
  UNIQUE INDEX `id_UNIQUE` (`csv_report_id` ASC) VISIBLE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `carparkapp`.`share`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carparkapp`.`share` (
  `share_id` INT NOT NULL AUTO_INCREMENT,
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `credit_item` FLOAT NOT NULL,
  `is_active` TINYINT NULL DEFAULT NULL,
  `contract_id` INT NOT NULL,
  PRIMARY KEY (`share_id`),
  UNIQUE INDEX `id_UNIQUE` (`share_id` ASC) VISIBLE,
  INDEX `fk_share_contract1_idx` (`contract_id` ASC) VISIBLE,
  CONSTRAINT `fk_share_contract1`
    FOREIGN KEY (`contract_id`)
    REFERENCES `carparkapp`.`contract` (`contract_id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `carparkapp`.`credit_item`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carparkapp`.`credit_item` (
  `credit_item_id` INT NOT NULL AUTO_INCREMENT,
  `share_id` INT NOT NULL,
  `csv_report_id` INT NULL DEFAULT NULL,
  PRIMARY KEY (`credit_item_id`),
  UNIQUE INDEX `id_UNIQUE` (`credit_item_id` ASC) VISIBLE,
  INDEX `fk_credit_item_share1_idx` (`share_id` ASC) VISIBLE,
  INDEX `fk_credit_item_csv_report1_idx` (`csv_report_id` ASC) VISIBLE,
  UNIQUE INDEX `share_id_UNIQUE` (`share_id` ASC) VISIBLE,
  CONSTRAINT `fk_credit_item_csv_report1`
    FOREIGN KEY (`csv_report_id`)
    REFERENCES `carparkapp`.`csv_report` (`csv_report_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE,
  CONSTRAINT `fk_credit_item_share1`
    FOREIGN KEY (`share_id`)
    REFERENCES `carparkapp`.`share` (`share_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
AUTO_INCREMENT = 1
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `carparkapp`.`rfid_blockage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `carparkapp`.`rfid_blockage` (
  `rfid_blockage_id` INT NOT NULL AUTO_INCREMENT,
  `is_confirmed` TINYINT NULL DEFAULT '0',
  `share_id` INT NOT NULL,
  PRIMARY KEY (`rfid_blockage_id`),
  UNIQUE INDEX `id_UNIQUE` (`rfid_blockage_id` ASC) VISIBLE,
  INDEX `fk_rfid_blockage_share1_idx` (`share_id` ASC) VISIBLE,
  UNIQUE INDEX `share_id_UNIQUE` (`share_id` ASC) VISIBLE,
  CONSTRAINT `fk_rfid_blockage_share1`
    FOREIGN KEY (`share_id`)
    REFERENCES `carparkapp`.`share` (`share_id`)
    ON DELETE RESTRICT
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
