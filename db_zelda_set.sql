-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema db_zelda_set
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema db_zelda_set
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `db_zelda_set` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci ;
USE `db_zelda_set` ;

-- -----------------------------------------------------
-- Table `db_zelda_set`.`t_role`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_zelda_set`.`t_role` (
  `PK_Role` INT NOT NULL AUTO_INCREMENT,
  `Role` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`PK_Role`))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `db_zelda_set`.`t_type_source`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_zelda_set`.`t_type_source` (
  `PK_type_source` INT NOT NULL AUTO_INCREMENT,
  `type` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`PK_type_source`),
  UNIQUE INDEX `type_UNIQUE` (`type` ASC) VISIBLE)
ENGINE = InnoDB
AUTO_INCREMENT = 7
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `db_zelda_set`.`t_source`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_zelda_set`.`t_source` (
  `PK_Source` INT NOT NULL AUTO_INCREMENT,
  `Source` VARCHAR(100) NOT NULL,
  `FK_type_source` INT NOT NULL,
  PRIMARY KEY (`PK_Source`),
  INDEX `FK_type_source_idx` (`FK_type_source` ASC) VISIBLE,
  CONSTRAINT `FK_type_source`
    FOREIGN KEY (`FK_type_source`)
    REFERENCES `db_zelda_set`.`t_type_source` (`PK_type_source`))
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `db_zelda_set`.`t_user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_zelda_set`.`t_user` (
  `PK_User` INT NOT NULL AUTO_INCREMENT,
  `Email` VARCHAR(100) NOT NULL,
  `Password` VARCHAR(100) NOT NULL,
  `FK_Role` INT NOT NULL,
  PRIMARY KEY (`PK_User`),
  UNIQUE INDEX `Email_UNIQUE` (`Email` ASC) VISIBLE,
  INDEX `FK_Role_idx` (`FK_Role` ASC) VISIBLE,
  CONSTRAINT `FK_Role`
    FOREIGN KEY (`FK_Role`)
    REFERENCES `db_zelda_set`.`t_role` (`PK_Role`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


-- -----------------------------------------------------
-- Table `db_zelda_set`.`t_set`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `db_zelda_set`.`t_set` (
  `PK_Set` INT NOT NULL AUTO_INCREMENT,
  `FK_User` INT NOT NULL,
  `Nom` VARCHAR(100) NOT NULL,
  `Cap_Nom` VARCHAR(45) NOT NULL,
  `Tunic_Nom` VARCHAR(45) NOT NULL,
  `Trousers_Nom` VARCHAR(45) NOT NULL,
  `Description` LONGTEXT NOT NULL,
  `Effet` LONGTEXT NOT NULL,
  `FK_Cap_Source` INT NOT NULL,
  `FK_Tunic_Source` INT NOT NULL,
  `FK_Trousers_Source` INT NOT NULL,
  `Image_Set` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`PK_Set`),
  INDEX `FK_User_idx` (`FK_User` ASC) VISIBLE,
  INDEX `FK_Cap_Source_idx` (`FK_Cap_Source` ASC, `FK_Tunic_Source` ASC, `FK_Trousers_Source` ASC) VISIBLE,
  INDEX `FK_Trousers_Source_idx` (`FK_Trousers_Source` ASC) VISIBLE,
  INDEX `FK_Tunic_Source_idx` (`FK_Tunic_Source` ASC) VISIBLE,
  CONSTRAINT `FK_Cap_Source`
    FOREIGN KEY (`FK_Cap_Source`)
    REFERENCES `db_zelda_set`.`t_source` (`PK_Source`),
  CONSTRAINT `FK_Trousers_Source`
    FOREIGN KEY (`FK_Trousers_Source`)
    REFERENCES `db_zelda_set`.`t_source` (`PK_Source`),
  CONSTRAINT `FK_Tunic_Source`
    FOREIGN KEY (`FK_Tunic_Source`)
    REFERENCES `db_zelda_set`.`t_source` (`PK_Source`),
  CONSTRAINT `FK_User`
    FOREIGN KEY (`FK_User`)
    REFERENCES `db_zelda_set`.`t_user` (`PK_User`))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_0900_ai_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
