USE GYMActivity;
SET NAMES 'utf8';

/* Drop all tables before creating them */

START TRANSACTION;
DROP TABLE ActivityReports;
DROP TABLE ActivityReportStatus;
DROP TABLE DailyFees;
DROP TABLE Assignements;
DROP TABLE Activities;
DROP TABLE Tasks;
DROP TABLE Projects;
DROP TABLE ProjectStatus;
DROP TABLE ProjectTypes;
DROP TABLE ProjectTaskRelations;
DROP TABLE Clients;
DROP TABLE ProfileManagementData;
DROP TABLE Profiles;
DROP TABLE RightsGroups;
DROP TABLE AccessLogs;
DROP TABLE Ideas;
DROP TABLE IdeaStatus;
DROP TABLE IdeaMessages;
COMMIT;