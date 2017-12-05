-- Copyright (C) 2011 by GENYMOBILE & Arnaud Dupuis
-- adupuis@genymobile.com
-- http://www.genymobile.com
-- 
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 3 of the License, or
-- (at your option) any later version.
-- 
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
-- 
-- You should have received a copy of the GNU General Public License
-- along with this program; if not, write to the
-- Free Software Foundation, Inc.,
-- 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA

-- From DB version 6 and above the update script shall define 3 properties : AUTO_UPDATE_ENABLED, AUTO_UPDATE_OLD_DB_VERSION and AUTO_UPDATE_NEW_DB_VERSION.
-- Those properties are to be defined as SQL comments, one per line with only one space between double dash and property name and none after that.
-- DB version will be checked by a command like : mysql -s --skip-column-names -D GYMActivity -e "select property_value_content from PropertyValues where property_id=(select property_id from Properties where property_name='PROP_DB_VERSION');"
-- AUTO_UPDATE_ENABLED=1
-- AUTO_UPDATE_OLD_DB_VERSION=6
-- AUTO_UPDATE_NEW_DB_VERSION=7

SET NAMES 'utf8';
START TRANSACTION;

-- Ajout de la table Countries
CREATE TABLE Countries (
	country_id int auto_increment,
	country_name varchar(250) not null default 'Undefined',
	primary key(country_id),
	unique key(country_name)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE Countries AUTO_INCREMENT = 1;
INSERT INTO Countries VALUES(1,'France');
INSERT INTO Countries VALUES(2,'USA');

-- Ajout de la table BankHolidays
CREATE TABLE BankHolidays (
    bank_holiday_id int auto_increment,
    bank_holiday_name varchar(250) not null default 'Undefined',
    bank_holiday_project_id int not null,
    bank_holiday_task_id int not null,
    bank_holiday_start_date date not null,
    bank_holiday_stop_date date not null,
    bank_holiday_country_id int not null,
    primary key(bank_holiday_id),
    unique key bank_holiday_key (bank_holiday_start_date,bank_holiday_stop_date,bank_holiday_country_id),
    foreign key(bank_holiday_country_id) references Countries(country_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE ProfileManagementData ADD COLUMN profile_management_data_country_id int not null AFTER profile_management_data_resignation_date;
ALTER TABLE ProfileManagementData ADD FOREIGN KEY(profile_management_data_country_id) references Countries(country_id) ON DELETE CASCADE;

-- Ajout d'une tâche pour les jours fériés 
INSERT INTO Tasks VALUES(NULL,'Bank Holiday','');

-- Ajout d'une propriété pour la blacklist des tâches que les utilisateurs ne peuvent pas utiliser dans l'ajout de CRA/Congés.
INSERT INTO Properties VALUES(NULL,'TASKS_BLACKLIST',"Liste des taches (séparé par une virgule) ne pouvant pas etre selectionnee par les utilisateurs pour ajouter des CRA/Conges",4);
INSERT INTO PropertyValues VALUES(NULL, (SELECT property_id FROM Properties WHERE property_name = 'TASKS_BLACKLIST'),(SELECT task_id FROM Tasks WHERE task_name = 'Bank Holiday') );

-- ---------------------------------------------
-- update de la version de la base de donnée --
-- ---------------------------------------------
UPDATE `PropertyValues` SET `property_value_content` = '7' WHERE `PropertyValues`.`property_id` = (SELECT property_id FROM Properties WHERE property_name = 'PROP_DB_VERSION');

COMMIT;
