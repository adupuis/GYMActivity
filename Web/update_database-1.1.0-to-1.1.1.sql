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
-- AUTO_UPDATE_ENABLED=1
-- AUTO_UPDATE_OLD_DB_VERSION=5
-- AUTO_UPDATE_NEW_DB_VERSION=6

SET NAMES 'utf8';
START TRANSACTION;

-- Ajout d'une colonne category
ALTER TABLE ProfileManagementData ADD COLUMN profile_management_data_category int not null default 0 AFTER profile_management_data_technology_leader_id;

COMMIT;