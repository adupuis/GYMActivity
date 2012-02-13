
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
/* Create the new schema and add default data */
START TRANSACTION;

CREATE TABLE RightsGroups (
	rights_group_id int auto_increment,
	rights_group_name varchar(100) not null default 'Undefined',
	rights_group_shortname varchar(20) not null default 'UNDEF',
	rights_group_description text,
	unique key rights_group_shortname (rights_group_shortname),
	primary key(rights_group_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE RightsGroups AUTO_INCREMENT = 1;
INSERT INTO RightsGroups VALUES(1,'Admins','ADM','Administrators of the application');
INSERT INTO RightsGroups VALUES(2,'TopManagers','TM','Company top managers. They are users with more rights than basic users (they can create/edit projects, tasks, assignements and clients,they cannot access rights management features).');
INSERT INTO RightsGroups VALUES(3,'Users','USR','Standard users, they can create and edit activities.');
INSERT INTO RightsGroups VALUES(4,'TechnologyLeaders','TL','Groupe des Technology Leaders (chefs de projets, scrum masters, RO, etc.). Ils ont accès à toutes les données financières et opérationnelles de leur projets.');
INSERT INTO RightsGroups VALUES(5,'Reporters','REP','Read only access for various reports. They can only see projects (and assignees) related data.');
INSERT INTO RightsGroups VALUES(6,'Externes','EXT','Les profiles entrants dans ce groupe sont les externes à GENYMOBILE fournissant un travail facturé (ou coutant) tel que : les Freelances, les fournisseurs, les sous-traitants, etc.');
INSERT INTO RightsGroups VALUES(7,'GroupLeaders','GL','Groupe des Group Leaders (manager administratifs, etc.).');


CREATE TABLE Profiles (
	profile_id int auto_increment,
	profile_login varchar(100) not null default 'Undefined',
	profile_firstname varchar(100) not null default 'Undefined',
	profile_lastname varchar(100) not null default 'Undefined',
	profile_password varchar(200) not null default 'Undefined',
	profile_email varchar(200) not null default 'Undefined',
	profile_is_active boolean not null default false,
	profile_needs_password_reset boolean not null default false,
	rights_group_id int not null default 2,
	primary key(profile_id),
	unique key profile_login (profile_login),
	foreign key(rights_group_id) references RightsGroups(rights_group_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO Profiles VALUES (1,'admin','','','admin','admin@genymobile.com',true,false,1);
UPDATE Profiles SET profile_password = MD5('admin') WHERE profile_id=1;
INSERT INTO Profiles VALUES (2,'test','ALongFirstName','AVeryLongLastName','test','admin@genymobile.com',true,false,3);
UPDATE Profiles SET profile_password = MD5('test') WHERE profile_id=2;
ALTER TABLE Profiles AUTO_INCREMENT = 1;
INSERT INTO Profiles VALUES (3,'cravalec','Cédric','Ravalec','genymobile','cravalec@genymobile.com',true,true,1);
UPDATE Profiles SET profile_password = MD5('genymobile') WHERE profile_id=3;
INSERT INTO Profiles VALUES (4,'azettor','Angélique','Zettor','genymobile','azettor@genymobile.com',true,true,1);
UPDATE Profiles SET profile_password = MD5('genymobile') WHERE profile_id=4;
INSERT INTO Profiles VALUES (5,'adupuis','Arnaud','Dupuis','genymobile','adupuis@genymobile.com',true,true,1);
UPDATE Profiles SET profile_password = MD5('genymobile') WHERE profile_id=5;

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

-- TODO: Ajouter le support des entretiens annuels
-- TODO: Ajouter le support des profiles tags


CREATE TABLE Notifications (
	notification_id int auto_increment,
	profile_id int not null,
	notification_text text not null,
	notification_is_unread bool default true,
	notification_type varchar(50) not null default 'info',
	primary key(notification_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE Notifications AUTO_INCREMENT=1;

CREATE TABLE ApiKeys (
	api_key_id int auto_increment,
	profile_id int not null,
	api_key_data text not null,
	api_key_timestamp int not null,
	primary key(api_key_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE ApiKeys AUTO_INCREMENT=1;

CREATE TABLE Clients (
	client_id int auto_increment,
	client_name varchar(200) not null default 'Undefined',
	primary key(client_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE Clients AUTO_INCREMENT = 1;
INSERT INTO Clients VALUES(1,'GENYMOBILE');
INSERT INTO Clients VALUES(NULL,'Orange Vallée');
INSERT INTO Clients VALUES(NULL,'JCDecaux');

CREATE TABLE ProjectTypes (
	project_type_id int auto_increment,
	project_type_name varchar(200) not null default 'Undefined',
	project_type_description varchar(200),
	primary key(project_type_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE ProjectTypes AUTO_INCREMENT = 1;
INSERT INTO ProjectTypes VALUES(1,'Régie','Employé à disposition du client dans les bureaux du client.');
INSERT INTO ProjectTypes VALUES(2,'Forfait','Employé sur un ou plusieurs projets au forfait.');
INSERT INTO ProjectTypes VALUES(3,'Régie forfaitée','Employé chez le client dans un cadre de régie forfaitée.');
INSERT INTO ProjectTypes VALUES(4,'R&D', 'Employé travail sur un ou plusieurs projet de R&D ou innovation par et pour GENYMOBILE.');
INSERT INTO ProjectTypes VALUES(5,'Congés','Projet utilisé pour faire les demandes de congés des employés.');
INSERT INTO ProjectTypes VALUES(6,'Autre','Autre types. Par exemple: travaux internes à GENYMOBILE, etc.');
INSERT INTO ProjectTypes VALUES(7,'Avant Vente',"Projet servant à tracer l'avant vente.");

CREATE TABLE ProjectStatus (
	project_status_id int auto_increment,
	project_status_name varchar(200),
	project_status_description varchar(200),
	primary key(project_status_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE ProjectStatus AUTO_INCREMENT = 1;
INSERT INTO ProjectStatus VALUES(1,'En cours','Projet en cours sans risques identifiés ni avérés.');
INSERT INTO ProjectStatus VALUES(2,'Fermé','Projet fermé (plus aucune imputation possible).');
INSERT INTO ProjectStatus VALUES(3,'Pause','Projet en pause (pas de facturation, pas de dépenses ni notes de frais).');
INSERT INTO ProjectStatus VALUES(4,'Dépassement',"Projet en dépassement (nous perdons de l'argent).");
INSERT INTO ProjectStatus VALUES(5,'Risque client','Un risque de dépassement est identifié et celui-ci est dû au client.');
INSERT INTO ProjectStatus VALUES(6,'Risque interne','Un risque de dépassement est identifié et celui-ci est dû à GENYMOBILE (ou un de ces sous-traitant).');
INSERT INTO ProjectStatus VALUES(7,'Avant Vente',"Le projet est en cours d'avant vente.");
INSERT INTO ProjectStatus VALUES(8,'Perdu','Le projet à été perdu.');

CREATE TABLE Projects (
	project_id int auto_increment,
	project_name varchar(200) not null default 'Undefined',
	project_description text,
	client_id int not null default 1,
	project_location varchar(200),
	project_start_date date not null,
	project_end_date date,
	project_type_id int,
	project_status_id int,
	primary key(project_id),
	foreign key(client_id) references Clients(client_id) ON DELETE CASCADE,
	foreign key(project_type_id) references ProjectTypes(project_type_id) ON DELETE CASCADE,
	foreign key(project_status_id) references ProjectStatus(project_status_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE Projects AUTO_INCREMENT = 1;
INSERT INTO Projects VALUES(1,'Administratif 2011','Tâches administratives (travaux internes, management, etc.).',1,'Paris','2011-01-01','2011-12-31',2,1);
INSERT INTO Projects VALUES(2,'Congés 2011','Tous les congés.',1,'None','2011-01-01','2011-12-31',5,1);
INSERT INTO Projects VALUES(3,'Administratif 2012','Tâches administratives (travaux internes, management, etc.).',1,'Paris','2012-01-01','2012-12-31',2,1);
INSERT INTO Projects VALUES(4,'Congés 2012','Tous les congés.',1,'None','2012-01-01','2012-12-31',5,1);

CREATE TABLE Tasks (
	task_id int auto_increment,
	task_name varchar(200) not null default 'Undefined',
	task_description text,
	primary key(task_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE Tasks AUTO_INCREMENT = 1;
INSERT INTO Tasks VALUES(NULL,'Avant Vente','Avant vente de projet (chiffrage, proposition commerciale, présentation client, etc.).');
INSERT INTO Tasks VALUES(NULL,'Formation Interne','Formation interne à GENYMOBILE.');
INSERT INTO Tasks VALUES(NULL,'Formation Externe','Formation externe à GENYMOBILE.');
INSERT INTO Tasks VALUES(NULL,'Intercontrat','');
INSERT INTO Tasks VALUES(NULL,'Management','');
INSERT INTO Tasks VALUES(NULL,'Stagiaire','');
INSERT INTO Tasks VALUES(NULL,'Absence Conventionnelle','Absence Conventionnelle');
INSERT INTO Tasks VALUES(NULL,'Autre Abs Auto N. Payée','');
INSERT INTO Tasks VALUES(NULL,'Congé Eco. Soc. Syndic.','');
INSERT INTO Tasks VALUES(NULL,'Congé Indiv. de Formation','');
INSERT INTO Tasks VALUES(NULL,'Congé Payé','');
INSERT INTO Tasks VALUES(NULL,'Congé sans Solde','');
INSERT INTO Tasks VALUES(NULL,'Maladie/Maternite <1an','');
INSERT INTO Tasks VALUES(NULL,'Maladie/Maternite >1an','');
INSERT INTO Tasks VALUES(NULL,'Mi-Tps Thérapeutiq. <1an','');
INSERT INTO Tasks VALUES(NULL,'Mi-Tps Thérapeutiq. >1an','');
INSERT INTO Tasks VALUES(NULL,'R.T.T.','');
INSERT INTO Tasks VALUES(NULL,'Repos Compensateur','');
INSERT INTO Tasks VALUES(NULL,'Suspension de Contrat','');
INSERT INTO Tasks VALUES(NULL,'Temps Partiel','');
INSERT INTO Tasks VALUES(NULL,'Heures supplémentaires','Si les heures supplémentaires sont autorisés sur un projet, utilisez cette tâche pour les imputer.');

CREATE TABLE ProjectTaskRelations (
	project_task_relation_id int auto_increment,
	project_id int not null,
	task_id int not null,
	primary key(project_task_relation_id),
	foreign key(project_id) references Projects(project_id) ON DELETE CASCADE,
	foreign key(task_id) references Tasks(task_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE ProjectTaskRelations AUTO_INCREMENT = 1;
-- INSERT INTO ProjectTaskRelations VALUES(NULL,1,1);
-- INSERT INTO ProjectTaskRelations VALUES(NULL,3,1);
-- INSERT INTO ProjectTaskRelations VALUES(NULL,1,2);
-- INSERT INTO ProjectTaskRelations VALUES(NULL,3,2);
-- INSERT INTO ProjectTaskRelations VALUES(NULL,1,3);
-- INSERT INTO ProjectTaskRelations VALUES(NULL,3,3);

INSERT INTO `ProjectTaskRelations` VALUES (NULL,1,3),(NULL,3,3),(NULL,1,2),(NULL,3,2),(NULL,1,1),(NULL,3,1),(NULL,1,4),(NULL,1,5),(NULL,1,6),(NULL,2,7),(NULL,2,8),(NULL,2,9),(NULL,2,10),(NULL,2,11),(NULL,2,12),(NULL,2,13),(NULL,2,14),(NULL,2,15),(NULL,2,16),(NULL,2,17),(NULL,2,18),(NULL,2,19),(NULL,2,20),(NULL,3,4),(NULL,3,5),(NULL,3,6),(NULL,4,7),(NULL,4,8),(NULL,4,9),(NULL,4,10),(NULL,4,11),(NULL,4,12),(NULL,4,13),(NULL,4,14),(NULL,4,15),(NULL,4,16),(NULL,4,17),(NULL,4,18),(NULL,4,19),(NULL,4,20);

CREATE TABLE Assignements (
	assignement_id int auto_increment,
	profile_id int not null,
	project_id int not null,
	assignement_overtime_allowed boolean not null default false,
	assignement_is_active boolean not null default true,
	primary key(assignement_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE,
	foreign key(project_id) references Projects(project_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE Assignements AUTO_INCREMENT = 1;
INSERT INTO `Assignements` VALUES (NULL,3,1,0),(NULL,4,1,0),(NULL,5,1,0),(NULL,3,4,0),(NULL,3,2,0),(NULL,4,2,0),(NULL,5,2,0),(NULL,3,3,0),(NULL,4,3,0),(NULL,5,3,0),(NULL,4,4,0),(NULL,5,4,0);

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

CREATE TABLE Activities (
	activity_id int auto_increment,
	activity_date date not null,
	activity_load int not null,
	activity_input_date date not null,
	assignement_id int not null,
	task_id int not null,
	primary key(activity_id),
	foreign key(assignement_id) references Assignements(assignement_id) ON DELETE CASCADE,
	foreign key(task_id) references Tasks(task_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE Activities AUTO_INCREMENT = 1;

CREATE TABLE ActivityReportStatus (
	activity_report_status_id int auto_increment,
	activity_report_status_shortname varchar(200),
	activity_report_status_name varchar(200) not null default 'Undefined',
	activity_report_status_description text,
	primary key(activity_report_status_id,activity_report_status_shortname)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE ActivityReportStatus AUTO_INCREMENT = 1;
INSERT INTO ActivityReportStatus VALUES(NULL,'P_USER_VALIDATION','En attente de validation utilisateur',"Le rapport d'activité a été pré-soumis et est en attente de validation par l'utilisateur.");
INSERT INTO ActivityReportStatus VALUES(NULL,'P_APPROVAL','En attente de validation manager',"Le rapport d'activité a été soumis et est en attente d'approbation du management.");
INSERT INTO ActivityReportStatus VALUES(NULL,'APPROVED','Validé',"Le rapport d'activité a été approuvé par la direction, il est maintenant en attente de facturation.");
INSERT INTO ActivityReportStatus VALUES(NULL,'BILLED','Facturé','Facture envoyée au client.');
INSERT INTO ActivityReportStatus VALUES(NULL,'PAID','Payé',"Le client a payé pour ce rapport d'activité.");
INSERT INTO ActivityReportStatus VALUES(NULL,'CLOSE','Fermé',"Plus rien ne peut être fait avec ce rapport d'activité.");
INSERT INTO ActivityReportStatus VALUES(NULL,'P_REMOVAL','En attente de suppression',"Le rapport d'activité a été soumis pour suppression et est en attente de validation par le management");
INSERT INTO ActivityReportStatus VALUES(NULL,'REMOVED','Supprimé',"Le rapport d'activité a été supprimé et plus rien ne peut être fait dessus.");
INSERT INTO ActivityReportStatus VALUES(NULL,'REFUSED','Refusé',"Le rapport d'activité a été refusé par le management.");

CREATE TABLE ActivityReports (
	activity_report_id int auto_increment,
	activity_report_invoice_reference varchar(200),
	activity_id int,
	profile_id int,
	activity_report_status_id int,
	primary key(activity_report_id),
	foreign key(activity_id) references Activities(activity_id) ON DELETE CASCADE,
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE,
	foreign key(activity_report_status_id) references ActivityReportStatus(activity_report_status_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE ActivityReports AUTO_INCREMENT = 1;

CREATE TABLE AccessLogs (
	access_log_id int auto_increment,
	access_log_timestamp int not null,
	profile_id int,
	access_log_ip varchar(200) not null,
	access_log_status boolean not null default false,
	access_log_page_requested varchar(200) not null default 'Undefined',
	access_log_type varchar(200) not null default 'UNAUTHORIZED_ACCESS',
	access_log_extra varchar(200) not null default 'Nothing',
	primary key(access_log_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE AccessLogs AUTO_INCREMENT=1;

CREATE TABLE IdeaStatus (
	idea_status_id int auto_increment,
	idea_status_name varchar(200) not null,
	idea_status_description varchar(200) not null,
	primary key(idea_status_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IdeaStatus AUTO_INCREMENT=1;
INSERT INTO IdeaStatus VALUES (NULL,'No status','Idea have not yet been reviewed.');
INSERT INTO IdeaStatus VALUES (NULL,'Accepted','Idea have been accepted for implementation.');
INSERT INTO IdeaStatus VALUES (NULL,'Rejected','Idea have been rejected.');
INSERT INTO IdeaStatus VALUES (NULL,'In progress','Idea is currently being implemented.');
INSERT INTO IdeaStatus VALUES (NULL,'Private alpha','Idea is implemented and is currently being tested with a restricted user base (alpha version).');
INSERT INTO IdeaStatus VALUES (NULL,'Public alpha','Idea is implemented and is currently being tested with all GENYMOBILE employees (alpha version).');
INSERT INTO IdeaStatus VALUES (NULL,'Private beta','Idea is implemented and is currently being tested with a restricted user base (beta version).');
INSERT INTO IdeaStatus VALUES (NULL,'Public beta','Idea is implemented and is currently being tested with all GENYMOBILE employees (beta version).');
INSERT INTO IdeaStatus VALUES (NULL,'Private RC','Idea is implemented and is currently being tested with a restricted user base (release candidate).');
INSERT INTO IdeaStatus VALUES (NULL,'Public RC','Idea is implemented and is currently being tested with all GENYMOBILE employees (release candidate).');
INSERT INTO IdeaStatus VALUES (NULL,'Implemented','Idea have been implemented and is now available in production use.');

CREATE TABLE Ideas (
	idea_id int auto_increment,
	idea_title varchar(200) not null,
	idea_description text not null,
	idea_votes int not null default 0,
	idea_status_id int not null,
	idea_submitter int not null,
	idea_submission_date datetime not null,
	primary key(idea_id),
	foreign key(idea_submitter) references Profiles(profile_id) ON DELETE CASCADE,
	foreign key(idea_status_id) references IdeaStatus(idea_status_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE Ideas AUTO_INCREMENT=1;

CREATE TABLE IdeaMessages (
	idea_message_id int auto_increment,
	idea_message_content text not null,
	idea_message_submission_date datetime not null,
	profile_id int not null,
	idea_id int not null,
	primary key(idea_message_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE,
	foreign key(idea_id) references Ideas(idea_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IdeaMessages AUTO_INCREMENT=1;

CREATE TABLE IdeaVotes (
        idea_vote_id int auto_increment,
        idea_positive_vote int default null,
        idea_negative_vote int default null,
        profile_id int not null,
        idea_id int not null,
        primary key(idea_vote_id),
        foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE,
        foreign key(idea_id) references Ideas(idea_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IdeaVotes AUTO_INCREMENT=1;

-- Properties

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
CREATE TABLE PropertyValues (
	property_value_id int auto_increment,
	property_id int not null,
	property_value_content text not null,
	primary key(property_value_id),
	foreign key(property_id) references Properties(property_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE PropertyValues AUTO_INCREMENT=1;

INSERT INTO PropertyValues VALUES(0,2,'4');

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

DELIMITER $$
create trigger ce_check_type before insert on CareerEvents for each row
begin
  if new.career_event_type != "positive" and new.career_event_type != "neutral" and new.career_event_type != "negative" then
    set new.career_event_type := "neutral";
  end if;
end $$
DELIMITER ;


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

CREATE TABLE IntranetCategories (
	intranet_category_id int auto_increment,
	intranet_category_name varchar(25) not null default 'Undefined',
	intranet_category_description varchar(140) not null default 'Undefined',
	primary key(intranet_category_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetCategories AUTO_INCREMENT=1;

CREATE TABLE IntranetTypes (
	intranet_type_id int auto_increment,
	intranet_type_name varchar(25) not null default 'Undefined',
	intranet_type_description varchar(140) not null default 'Undefined',
	intranet_category_id int not null,
	primary key(intranet_type_id),
	foreign key(intranet_category_id) references IntranetCategories(intranet_category_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetTypes AUTO_INCREMENT=1;

CREATE TABLE IntranetTags (
	intranet_tag_id int auto_increment,
	intranet_tag_name varchar(25) not null default 'Undefined',
	primary key(intranet_tag_id)
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetTags AUTO_INCREMENT=1;

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

CREATE TABLE IntranetTagPageRelations (
	intranet_tag_page_relation_id int auto_increment,
	intranet_tag_id int not null,
	intranet_page_id int not null,
	primary key(intranet_tag_page_relation_id),
	foreign key(intranet_tag_id) references IntranetTags(intranet_tag_id) ON DELETE CASCADE,
	foreign key(intranet_page_id) references IntranetPages(intranet_page_id) ON DELETE CASCADE
) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE IntranetTagPageRelations AUTO_INCREMENT=1;

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

COMMIT;
