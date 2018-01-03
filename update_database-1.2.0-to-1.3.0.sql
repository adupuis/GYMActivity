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
-- DB version will be checked by a command like : mysql -s --skip-column-names -D `cat classes/GenyWebConfig.php | grep "this->db_name" | awk -F'"' '{print $2}'` -e "select property_value_content from PropertyValues where property_id=(select property_id from Properties where property_name='PROP_DB_VERSION');" -u `cat classes/GenyWebConfig.php | grep "this->db_user" | awk -F'"' '{print $2}'` -p
-- AUTO_UPDATE_ENABLED=1
-- AUTO_UPDATE_OLD_DB_VERSION=7
-- AUTO_UPDATE_NEW_DB_VERSION=8

SET NAMES 'utf8';
START TRANSACTION;

-- Modification of the HolidaySummaries table
-- The linked trigger is going to be removed
DROP TRIGGER hs_check_type;
-- WARNING: the previous version is simply not compatible with this one so the table is simply going to be archived and renamed HolidaySummaries-Archived.
RENAME TABLE HolidaySummaries TO HolidaySummariesArchived;

-- Change engine to set it on InnoDB on all tables.
ALTER TABLE ActivityReports CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE ActivityReportStatus CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE DailyRates CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE AssignementFees CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE Assignements CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE Activities CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE BankHolidays CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE Tasks CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE Projects CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE ProjectStatus CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE ProjectTypes CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE ProjectTaskRelations CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE Clients CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE Countries CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE ProfileManagementData CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE Profiles CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE RightsGroups CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE AccessLogs CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE Ideas CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE IdeaStatus CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE IdeaMessages CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE IdeaVotes CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE Notifications CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE ApiKeys CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE PropertyTypes CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE Properties CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE PropertyOptions CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE PropertyValues CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE CareerEvents CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE HolidaySummariesArchived CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE IntranetHistories CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE IntranetTagPageRelations CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE IntranetPages CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE IntranetTags CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE IntranetTypes CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE IntranetCategories CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE IntranetPageStatus CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;

-- Now create the new table
CREATE TABLE HolidaySummaries_NG (
	holiday_summary_id int auto_increment,
	profile_id int not null,
	project_id int not null,
	task_id int not null,
	holiday_summary_period_start date not null,
	holiday_summary_period_end date not null,
	holiday_summary_count_acquired float(4,2) not null default '0.00',
	holiday_summary_count_taken float(4,2) not null default '0.00',
	holiday_summary_count_remaining float(4,2) not null default '0.00',
	primary key(holiday_summary_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE,
	foreign key(project_id) references Projects(project_id) ON DELETE CASCADE,
	foreign key(task_id) references Tasks(task_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci  ENGINE=InnoDB;
ALTER TABLE HolidaySummaries_NG AUTO_INCREMENT=1;


-- ---------------------------------------------
-- update de la version de la base de donnée --
-- ---------------------------------------------
UPDATE `PropertyValues` SET `property_value_content` = '8' WHERE `PropertyValues`.`property_id` = (SELECT property_id FROM Properties WHERE property_name = 'PROP_DB_VERSION');

COMMIT;
