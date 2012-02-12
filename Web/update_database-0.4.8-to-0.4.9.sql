
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

SET NAMES 'utf8';
START TRANSACTION;

-- Access logs
DROP TABLE AccessLogs;
CREATE TABLE AccessLogs (
	access_log_id int auto_increment,
	access_log_timestamp int not null,
	profile_id int not null,
	access_log_ip varchar(200) not null,
	access_log_status boolean not null default false,
	access_log_page_requested varchar(200) not null default 'Undefined',
	access_log_type varchar(200) not null default 'UNAUTHORIZED_ACCESS',
	access_log_extra varchar(200) not null default 'Nothing',
	primary key(access_log_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE AccessLogs AUTO_INCREMENT=1;

-- Profile management data
DROP TABLE ProfileManagementData;
CREATE TABLE ProfileManagementData (
	profile_management_data_id int auto_increment,
	profile_id int not null unique,
	profile_management_data_salary int not null,
	profile_management_data_recruitement_date date not null,
	profile_management_data_is_billable boolean not null default true,
	profile_management_data_availability_date date not null,
	primary key(profile_management_data_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE ProfileManagementData AUTO_INCREMENT = 1;

-- Properties
DROP TABLE PropertyTypes;
CREATE TABLE PropertyTypes (
	property_type_id int auto_increment,
	property_type_shortname varchar(250) not null default 'P_TYPE',
	property_type_name varchar(250) not null default 'Property type name',
	primary key(property_type_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE PropertyTypes AUTO_INCREMENT=1;

INSERT INTO PropertyTypes VALUES(0,'PROP_TYPE_BOOL','Une propriété booléenne (vrai/faux).');
INSERT INTO PropertyTypes VALUES(0,'PROP_TYPE_MULTI_SELECT','Une propriété contenant un choix multiple.');
INSERT INTO PropertyTypes VALUES(0,'PROP_TYPE_LIST_SELECT','Une propriété contenant un choix unique dans une liste.');
INSERT INTO PropertyTypes VALUES(0,'PROP_TYPE_SHORT_TEXT','Une propriété contenant un text court.');
INSERT INTO PropertyTypes VALUES(0,'PROP_TYPE_LONG_TEXT','Une propriété contenant un text long.');
INSERT INTO PropertyTypes VALUES(0,'PROP_TYPE_DATE','Une propriété contenant une date.');

DROP TABLE Properties;
CREATE TABLE Properties (
	property_id int auto_increment,
	property_name varchar(250) not null default 'PNAME',
	property_label varchar(250) not null default 'Property name',
	property_type_id int not null,
	primary key(property_id),
	foreign key(property_type_id) references PropertyTypes(property_type_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE Properties AUTO_INCREMENT=1;

-- Exemple de propriété
INSERT INTO Properties VALUES(0,'PROP_LIVE_DEBUG','Activer/desactiver le debug en live.',1);

-- Version du schéma de la base de donnée
INSERT INTO Properties VALUES(0,'PROP_DB_VERSION','Version du schéma de la base de données.',4);
DROP TABLE PropertyOptions;
CREATE TABLE PropertyOptions (
	property_option_id int auto_increment,
	property_option_content text not null,
	property_id int not null,
	primary key(property_option_id),
	foreign key(property_id) references Properties(property_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE PropertyOptions AUTO_INCREMENT=1;

-- Suite de l'exemple
INSERT INTO PropertyOptions VALUES(0,'Activé',1);
INSERT INTO PropertyOptions VALUES(0,'Désactivé',1);

-- C'est dans cette table que vont les valeurs séléctionnées. Dans l'exemple ci-dessus il y aurait 1 ou 2 (les id d'une des deux options possible)
DROP TABLE PropertyValues;
CREATE TABLE PropertyValues (
	property_value_id int auto_increment,
	property_id int not null,
	property_value_content text not null,
	primary key(property_value_id),
	foreign key(property_id) references Properties(property_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE PropertyValues AUTO_INCREMENT=1;

-- Le schéma de la base de données est en version 4
INSERT INTO PropertyValues VALUES(0,2,'4');

-- Evènements de carrière
DROP TABLE CareerEvents;
CREATE TABLE CareerEvents (
	career_event_id int auto_increment,
	profile_id int not null,
	career_event_timestamp int not null,
	career_event_type varchar(50) not null,
	career_event_title varchar(200) not null,
	career_event_text text not null,
	career_event_attachement varchar(250),
	career_event_manager_agreement boolean not null default false,
	career_event_employee_agreement boolean not null default false,
	primary key(career_event_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE CareerEvents AUTO_INCREMENT=1;

DROP TRIGGER ce_check_type;
DELIMITER $$
create trigger ce_check_type before insert on CareerEvents for each row
begin
  if new.career_event_type != "positive" and new.career_event_type != "neutral" and new.career_event_type != "negative" then
    set new.career_event_type := "neutral";
  end if;
end $$
DELIMITER ;

ALTER TABLE AccessLogs MODIFY profile_id int;

DROP TABLE DailyFees;
CREATE TABLE DailyRates (
	daily_rate_id int auto_increment,
	project_id int not null,
	task_id int not null,
	profile_id int,
	daily_rate_start_date date not null,
	daily_rate_end_date date not null,
	daily_rate_value int not null,
	primary key(daily_rate_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE,
	foreign key(project_id) references Projects(project_id) ON DELETE CASCADE,
	foreign key(task_id) references Tasks(task_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE DailyRates AUTO_INCREMENT = 1;

DROP TABLE HolidaySummaries;
CREATE TABLE HolidaySummaries (
	holiday_summary_id int auto_increment,
	profile_id int not null,
	holiday_summary_type char(10) not null,
	holiday_summary_period_start date not null,
	holiday_summary_period_end date not null,
	holiday_summary_count_acquired float(4,2) not null default '0.00',
	holiday_summary_count_taken float(4,2) not null default '0.00',
	holiday_summary_count_remaining float(4,2) not null default '0.00',
	primary key(holiday_summary_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE
);
ALTER TABLE HolidaySummaries AUTO_INCREMENT=1;

DELIMITER $$
create trigger hs_check_type before insert on HolidaySummaries for each row
begin
  if new.holiday_summary_type != "RTT" and new.holiday_summary_type != "CP" then
    set new.holiday_summary_type := "CP";
  end if;
end $$
DELIMITER ;

DROP TABLE IntranetHistories;

DROP TABLE IntranetCategories;
CREATE TABLE IntranetCategories (
	intranet_category_id int auto_increment,
	intranet_category_name varchar(25) not null default 'Undefined',
	intranet_category_description varchar(140) not null default 'Undefined',
	primary key(intranet_category_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetCategories AUTO_INCREMENT=1;

DROP TABLE IntranetTypes;
CREATE TABLE IntranetTypes (
	intranet_type_id int auto_increment,
	intranet_type_name varchar(25) not null default 'Undefined',
	intranet_type_description varchar(140) not null default 'Undefined',
	intranet_category_id int not null,
	primary key(intranet_type_id),
	foreign key(intranet_category_id) references IntranetCategories(intranet_category_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetTypes AUTO_INCREMENT=1;

DROP TABLE IntranetTags;
CREATE TABLE IntranetTags (
	intranet_tag_id int auto_increment,
	intranet_tag_name varchar(25) not null default 'Undefined',
	primary key(intranet_tag_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetTags AUTO_INCREMENT=1;

DROP TABLE IntranetPageStatus;
CREATE TABLE IntranetPageStatus (
	intranet_page_status_id int auto_increment,
	intranet_page_status_name varchar(200) not null,
	intranet_page_status_description varchar(200) not null,
	primary key(intranet_page_status_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetPageStatus AUTO_INCREMENT=1;
INSERT INTO IntranetPageStatus VALUES (NULL,'Brouillon','Visible uniquement par le créateur de la page');
INSERT INTO IntranetPageStatus VALUES (NULL,'Brouillon partagé','Visible par les profils appartenant groupe du créateur de la page');
INSERT INTO IntranetPageStatus VALUES (NULL,'Publié','Visible par tous');

DROP TABLE IntranetPages;
CREATE TABLE IntranetPages (
	intranet_page_id int auto_increment,
	intranet_page_title varchar(25) not null default 'Undefined',
	intranet_category_id int not null,
	intranet_type_id int not null,
	intranet_page_status_id int not null,
	intranet_page_acl_modification_type varchar(10) not null,
	profile_id int not null,
	intranet_page_description varchar(140) not null default 'Undefined',
	intranet_page_content blob not null,
	primary key(intranet_page_id),
	foreign key(intranet_category_id) references IntranetCategories(intranet_category_id) ON DELETE CASCADE,
	foreign key(intranet_type_id) references IntranetTypes(intranet_type_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetPages AUTO_INCREMENT=1;

DELIMITER $$
create trigger ip_check_modification_type before insert on IntranetPages for each row
begin
  if new.intranet_page_acl_modification_type != "owner" and new.intranet_page_acl_modification_type != "group" and new.intranet_page_acl_modification_type != "all" then
    set new.intranet_page_acl_modification_type := "owner";
  end if;
end $$
DELIMITER ;

DROP TABLE IntranetTagPageRelations;
CREATE TABLE IntranetTagPageRelations (
	intranet_tag_page_relation_id int auto_increment,
	intranet_tag_id int not null,
	intranet_page_id int not null,
	primary key(intranet_tag_page_relation_id),
	foreign key(intranet_tag_id) references IntranetTags(intranet_tag_id) ON DELETE CASCADE,
	foreign key(intranet_page_id) references IntranetPages(intranet_page_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetTagPageRelations AUTO_INCREMENT=1;

DROP TABLE IntranetHistories;
CREATE TABLE IntranetHistories (
	intranet_history_id int auto_increment,
	intranet_page_id int not null,
	intranet_page_status_id int not null,
	profile_id int not null,
	intranet_history_date datetime not null,
	intranet_history_content blob not null,
	primary key(intranet_history_id),
	foreign key(intranet_page_id) references IntranetPages(intranet_page_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetHistories AUTO_INCREMENT=1;

-- Update de la table groupe

-- Ajout d'une colonne shortname
ALTER TABLE RightsGroups ADD COLUMN rights_group_shortname varchar(20) not null default 'UNDEF' AFTER rights_group_name;

-- Update des groupes
UPDATE RightsGroups SET rights_group_name='ProjectManagers' WHERE rights_group_id=4;
UPDATE RightsGroups SET rights_group_description='Groupe des chefs de projets. Ils ont accès à toutes les données financières et opérationnelles de leur projets.' WHERE rights_group_id=4;
UPDATE RightsGroups SET rights_group_name='TopManagers' WHERE rights_group_id=2;
UPDATE RightsGroups SET rights_group_description='Company top managers. They are users with more rights than basic users (they can create/edit projects, tasks, assignements and clients,they cannot access rights management features).' WHERE rights_group_id=2;

-- Mise à jour des shortname
UPDATE RightsGroups SET rights_group_shortname='ADM' WHERE rights_group_id=1;
UPDATE RightsGroups SET rights_group_shortname='TM' WHERE rights_group_id=2;
UPDATE RightsGroups SET rights_group_shortname='USR' WHERE rights_group_id=3;
UPDATE RightsGroups SET rights_group_shortname='PM' WHERE rights_group_id=4;
UPDATE RightsGroups SET rights_group_shortname='REP' WHERE rights_group_id=5;
UPDATE RightsGroups SET rights_group_shortname='EXT' WHERE rights_group_id=6;

-- TODO: Voir pour ajouter la contrainte unique key sur shortname


COMMIT;
