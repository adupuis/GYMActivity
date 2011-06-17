USE GYMActivity;
SET NAMES 'utf8';
/* Create the new schema and add default data */
START TRANSACTION;

CREATE TABLE RightsGroups (
	rights_group_id int auto_increment,
	rights_group_name varchar(100) not null default 'Undefined',
	rights_group_description text,
	primary key(rights_group_id)
);
ALTER TABLE RightsGroups AUTO_INCREMENT = 1;
INSERT INTO RightsGroups VALUES(1,'Admins','Administrators of the application');
INSERT INTO RightsGroups VALUES(2,'SuperUsers','Users with more rights than basic users (they can create/edit projects, tasks, assignements and clients,they cannot access rights management features).');
INSERT INTO RightsGroups VALUES(3,'Users','Standard users, they can create and edit activities.');
INSERT INTO RightsGroups VALUES(4,'SuperReporters','Read only access for various reports. They can see almost all data.');
INSERT INTO RightsGroups VALUES(5,'Reporters','Read only access for various reports. They can only see projects (and assignees) related data.');
INSERT INTO RightsGroups VALUES(6,'Externes','Les profiles entrants dans ce groupe sont les externes à GenY Mobile fournissant un travail facturé (ou coutant) tel que : les Freelances, les fournisseurs, les sous-traitants, etc.');


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
);
INSERT INTO Profiles VALUES (1,'admin','','','admin','admin@genymobile.com',true,false,1);
UPDATE Profiles SET profile_password = MD5('admin') WHERE profile_id=1;
INSERT INTO Profiles VALUES (2,'test','ALongFirstName','AVeryLongLastName','test','admin@genymobile.com',true,false,3);
UPDATE Profiles SET profile_password = MD5('test') WHERE profile_id=2;
ALTER TABLE Profiles AUTO_INCREMENT = 1;

CREATE TABLE ProfileManagementData (
	profile_management_data_id int auto_increment,
	profile_id int not null,
	profile_management_data_salary float not null,
	profile_management_data_recruitement_date date not null,
	primary key(profile_management_data_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE
);
ALTER TABLE ProfileManagementData AUTO_INCREMENT = 1;

-- TODO: Ajouter le support des entretiens annuels

CREATE TABLE Clients (
	client_id int auto_increment,
	client_name varchar(200) not null default 'Undefined',
	primary key(client_id)
);
ALTER TABLE Clients AUTO_INCREMENT = 1;
INSERT INTO Clients VALUES(1,'GenY Mobile');
INSERT INTO Clients VALUES(NULL,'Orange Vallée');
INSERT INTO Clients VALUES(NULL,'JCDecaux');

CREATE TABLE ProjectTypes (
	project_type_id int auto_increment,
	project_type_name varchar(200) not null default 'Undefined',
	project_type_description varchar(200),
	primary key(project_type_id)
);
ALTER TABLE ProjectTypes AUTO_INCREMENT = 1;
INSERT INTO ProjectTypes VALUES(1,'Régie','Employé à disposition du client dans les bureaux du client.');
INSERT INTO ProjectTypes VALUES(2,'Forfait','Employé sur un ou plusieurs projets au forfait.');
INSERT INTO ProjectTypes VALUES(3,'Autre','Autre types. Par exemple: travaux internes à GenY Mobile, congés, etc.');

CREATE TABLE ProjectStatus (
	project_status_id int auto_increment,
	project_status_name varchar(200),
	project_status_description varchar(200),
	primary key(project_status_id)
);
ALTER TABLE ProjectStatus AUTO_INCREMENT = 1;
INSERT INTO ProjectStatus VALUES(1,'En cours','Projet en cours sans risques identifiés ni avérés.');
INSERT INTO ProjectStatus VALUES(2,'Fermé','Projet fermé (plus aucune imputation possible).');
INSERT INTO ProjectStatus VALUES(3,'Pause','Projet en pause (pas de facturation, pas de dépenses ni notes de frais).');
INSERT INTO ProjectStatus VALUES(4,'Dépassement',"Projet en dépassement (nous perdons de l'argent).");
INSERT INTO ProjectStatus VALUES(5,'Risque client','Un risque de dépassement est identifié et celui-ci est dû au client.');
INSERT INTO ProjectStatus VALUES(6,'Risque interne','Un risque de dépassement est identifié et celui-ci est dû à GenY Mobile (ou un de ces sous-traitant).');

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
);
ALTER TABLE Projects AUTO_INCREMENT = 1;
INSERT INTO Projects VALUES(1,'Administratif 2011','Tâches administratives (travaux internes, management, etc.).',1,'Paris','2011-01-01','2011-12-31',2,1);
INSERT INTO Projects VALUES(2,'Congés 2011','Tous les congés.',1,'None','2011-01-01','2011-12-31',2,1);
INSERT INTO Projects VALUES(3,'Administratif 2012','Tâches administratives (travaux internes, management, etc.).',1,'Paris','2012-01-01','2012-12-31',2,1);
INSERT INTO Projects VALUES(4,'Congés 2012','Tous les congés.',1,'None','2012-01-01','2012-12-31',2,1);

CREATE TABLE Tasks (
	task_id int auto_increment,
	task_name varchar(200) not null default 'Undefined',
	task_description text,
	primary key(task_id)
);
ALTER TABLE Tasks AUTO_INCREMENT = 1;
INSERT INTO Tasks VALUES(NULL,'Avant Vente','Avant vente de projet (chiffrage, proposition commerciale, présentation client, etc.).');
INSERT INTO Tasks VALUES(NULL,'Formation Interne','Formation interne à GenY Mobile.');
INSERT INTO Tasks VALUES(NULL,'Formation Externe','Formation externe à GenY Mobile.');
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
);
ALTER TABLE ProjectTaskRelations AUTO_INCREMENT = 1;
INSERT INTO ProjectTaskRelations VALUES(NULL,1,1);
INSERT INTO ProjectTaskRelations VALUES(NULL,3,1);
INSERT INTO ProjectTaskRelations VALUES(NULL,1,2);
INSERT INTO ProjectTaskRelations VALUES(NULL,3,2);
INSERT INTO ProjectTaskRelations VALUES(NULL,1,3);
INSERT INTO ProjectTaskRelations VALUES(NULL,3,3);

CREATE TABLE Assignements (
	assignement_id int auto_increment,
	profile_id int not null,
	project_id int not null,
	assignement_overtime_allowed boolean not null default false,
	primary key(assignement_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE,
	foreign key(project_id) references Projects(project_id) ON DELETE CASCADE
);
ALTER TABLE Assignements AUTO_INCREMENT = 1;

CREATE TABLE DailyFees (
	daily_fee_id int auto_increment,
	assignement_id int not null,
	daily_fee_start_date date not null,
	primary key(daily_fee_id),
	foreign key(assignement_id) references Assignements(assignement_id) ON DELETE CASCADE
);
ALTER TABLE DailyFees AUTO_INCREMENT = 1;

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
);
ALTER TABLE Activities AUTO_INCREMENT = 1;

CREATE TABLE ActivityReportStatus (
	activity_report_status_id int auto_increment,
	activity_report_status_shortname varchar(200),
	activity_report_status_name varchar(200) not null default 'Undefined',
	activity_report_status_description text,
	primary key(activity_report_status_id,activity_report_status_shortname)
);

ALTER TABLE ActivityReportStatus AUTO_INCREMENT = 1;
INSERT INTO ActivityReportStatus VALUES(NULL,'P_USER_VALIDATION','En attente de validation utilisateur',"Le rapport d'activité a été pré-soumis et est en attente de validation par l'utilisateur.");
INSERT INTO ActivityReportStatus VALUES(NULL,'P_APPROVAL','En attente de validation',"Le rapport d'activité a été soumis et est en attente d'approbation du management.");
INSERT INTO ActivityReportStatus VALUES(NULL,'APPROVED','Validé',"Le rapport d'activité a été approuvé par la direction, il est maintenant en attente de facturation.");
INSERT INTO ActivityReportStatus VALUES(NULL,'BILLED','Facturé','Facture envoyée au client.');
INSERT INTO ActivityReportStatus VALUES(NULL,'PAID','Payé',"Le client a payé pour ce rapport d'activité.");
INSERT INTO ActivityReportStatus VALUES(NULL,'CLOSE','Fermé',"Plus rien ne peut être fait avec ce rapport d'activité.");
INSERT INTO ActivityReportStatus VALUES(NULL,'P_REMOVAL','En attente de suppression',"Le rapport d'activité a été soumis pour suppression et est en attente de validation par le management");
INSERT INTO ActivityReportStatus VALUES(NULL,'REMOVED','Supprimé',"Le rapport d'activité a été supprimé et plus rien ne peut être fait dessus.");

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
);
ALTER TABLE ActivityReports AUTO_INCREMENT = 1;

CREATE TABLE AccessLogs (
	access_log_id int auto_increment,
	access_log_date date not null,
	access_log_profile_id int not null,
	access_log_ip varchar(200) not null,
	access_log_success boolean not null default false,
	access_login varchar(200) not null default 'Undefined',
	access_password varchar(200) not null default 'Undefined',
	primary key(access_log_id)
);
ALTER TABLE AccessLogs AUTO_INCREMENT=1;

CREATE TABLE IdeaStatus (
	idea_status_id int auto_increment,
	idea_status_name varchar(200) not null,
	idea_status_description varchar(200) not null,
	primary key(idea_status_id)
);
ALTER TABLE IdeaStatus AUTO_INCREMENT=1;
INSERT INTO IdeaStatus VALUES (NULL,'No status','Idea have not yet been reviewed.');
INSERT INTO IdeaStatus VALUES (NULL,'Accepted','Idea have been accepted for implementation.');
INSERT INTO IdeaStatus VALUES (NULL,'Rejected','Idea have been rejected.');
INSERT INTO IdeaStatus VALUES (NULL,'In progress','Idea is currently being implemented.');
INSERT INTO IdeaStatus VALUES (NULL,'Private alpha','Idea is implemented and is currently being tested with a restricted user base (alpha version).');
INSERT INTO IdeaStatus VALUES (NULL,'Public alpha','Idea is implemented and is currently being tested with all GenY Mobile employees (alpha version).');
INSERT INTO IdeaStatus VALUES (NULL,'Private beta','Idea is implemented and is currently being tested with a restricted user base (beta version).');
INSERT INTO IdeaStatus VALUES (NULL,'Public beta','Idea is implemented and is currently being tested with all GenY Mobile employees (beta version).');
INSERT INTO IdeaStatus VALUES (NULL,'Private RC','Idea is implemented and is currently being tested with a restricted user base (release candidate).');
INSERT INTO IdeaStatus VALUES (NULL,'Public RC','Idea is implemented and is currently being tested with all GenY Mobile employees (release candidate).');
INSERT INTO IdeaStatus VALUES (NULL,'Implemented','Idea have been implemented and is now available in production use.');

CREATE TABLE Ideas (
	idea_id int auto_increment,
	idea_title varchar(200) not null,
	idea_description text not null,
	idea_votes int not null default 0,
	idea_status_id int not null,
	idea_submitter int not null,
	primary key(idea_id),
	foreign key(idea_submitter) references Profiles(profile_id) ON DELETE CASCADE,
	foreign key(idea_status_id) references IdeaStatus(idea_status_id) ON DELETE CASCADE
);
ALTER TABLE Ideas AUTO_INCREMENT=1;

CREATE TABLE IdeaMessages (
	idea_message_id int auto_increment,
	idea_message_content text not null,
	profile_id int not null,
	idea_id int not null,
	primary key(idea_message_id),
	foreign key(profile_id) references Profiles(profile_id) ON DELETE CASCADE,
	foreign key(idea_id) references Ideas(idea_id) ON DELETE CASCADE
);
ALTER TABLE IdeaMessages AUTO_INCREMENT=1;

COMMIT;