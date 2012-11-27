-- Copyright (C) 2011 by GENYMOBILE & Jean-Charles Leneveu
-- jcleneveu@genymobile.com
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
-- AUTO_UPDATE_OLD_DB_VERSION=5
-- AUTO_UPDATE_NEW_DB_VERSION=6

SET NAMES 'utf8';
START TRANSACTION;

-- Ajout d'une colonne category
ALTER TABLE ProfileManagementData ADD COLUMN profile_management_data_category int not null default 0 AFTER profile_management_data_technology_leader_id;
ALTER TABLE ProfileManagementData ADD COLUMN profile_management_data_resignation_date date default '9999-12-31' AFTER profile_management_data_category;

-- Mise à jour du nom des tâches de congés payés.
UPDATE Tasks SET task_name = 'Maladie - Maternite <1an' WHERE task_name='Maladie/Maternite <1an';
UPDATE Tasks SET task_name = 'Maladie - Maternite >1an' WHERE task_name='Maladie/Maternite >1an';

-- Ajout d'une propriété détenant la liste des projets à entrer dans le reporting de congés.
INSERT INTO Properties VALUES(NULL,'CONGES_REPORT_PROJECT_NAMES',"Liste des projets entrant dans le reporting cong&eacute;s (s&eacute;par&eacute;s par une virgule sans espace)",4);
INSERT INTO PropertyValues VALUES(NULL, (SELECT property_id FROM Properties WHERE property_name = 'CONGES_REPORT_PROJECT_NAMES'),'2,4' );

-- ---------------------------------------------
-- update de la version de la base de donnée --
-- ---------------------------------------------
UPDATE `PropertyValues` SET `property_value_content` = '6' WHERE `PropertyValues`.`property_id` = (SELECT property_id FROM Properties WHERE property_name = 'PROP_DB_VERSION');

COMMIT;