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

-- Ajout des colonnes PM1 et PM2 dans la table projets.
ALTER TABLE Projects ADD COLUMN project_pm1_id int AFTER project_status_id;
ALTER TABLE Projects ADD COLUMN project_pm2_id int AFTER project_pm1_id;

ALTER TABLE Projects ADD FOREIGN KEY(project_pm1_id) references Profiles(profile_id) ON DELETE CASCADE;
ALTER TABLE Projects ADD FOREIGN KEY(project_pm2_id) references Profiles(profile_id) ON DELETE CASCADE;

-- ---------------------------------------------
-- update de la version de la base de donnée --
-- ---------------------------------------------
UPDATE `PropertyValues` SET `property_value_content` = '7' WHERE `PropertyValues`.`property_id` = (SELECT property_id FROM Properties WHERE property_name = 'PROP_DB_VERSION');

COMMIT;