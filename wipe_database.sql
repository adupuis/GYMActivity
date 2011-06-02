USE GYMActivity;
SET NAMES 'utf8';

/* Drop all tables before creating them */

START TRANSACTION;
DROP TABLE ActivityReports;
DROP TABLE Activity_Report_Status;
DROP TABLE Assignements;
DROP TABLE Activities;
DROP TABLE Tasks;
DROP TABLE Projects;
DROP TABLE ProjectStatus;
DROP TABLE ProjectTypes;
DROP TABLE Project_Task_Relations;
DROP TABLE Clients;
DROP TABLE Profiles;
DROP TABLE Rights_Groups;
DROP TABLE Access_Logs;
DROP TABLE Ideas;
DROP TABLE Idea_Status;
DROP TABLE Idea_Messages;
COMMIT;