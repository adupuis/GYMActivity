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

SET NAMES 'utf8';
START TRANSACTION;

-----------------------------------------------
-- création de la vue ActivityReportWorkflow --
-----------------------------------------------

CREATE VIEW ActivityReportWorkflow AS SELECT
     DISTINCT ar.activity_report_id,
     pr.profile_id,
     pr.profile_firstname,
     pr.profile_lastname,
     p.project_name,
     t.task_name,
     a.activity_date,
     c.client_name,
     a.activity_load,
     ar.activity_report_status_id,
     pr.profile_login
FROM 
     Activities a,
     ActivityReports ar,
     ActivityReportStatus ars,
     Tasks t,
     Clients c,
     Assignements ass,
     Projects p,
     Profiles pr 
WHERE 
     ar.activity_report_status_id IN (
          SELECT activity_report_status_id 
          FROM ActivityReportStatus 
          WHERE activity_report_status_shortname != "P_APPROVAL" AND activity_report_status_shortname != "P_USER_VALIDATION"
     )
AND (
     a.activity_id = ar.activity_id
     AND
     t.task_id = a.task_id
     AND
     ass.assignement_id = a.assignement_id
     AND
     p.project_id = ass.project_id
     AND
     pr.profile_id = ass.profile_id
     AND
     p.client_id = c.client_id
);

--------------------------------------------------------------------------
-- création de la vue décrivant le tableau d'utilisation des ressources --
--------------------------------------------------------------------------

CREATE VIEW ActivityReportRessources AS

SELECT
     DISTINCT
     p.project_id,
     p.project_name,
     p.project_type_id,
     c.client_name,
     a.activity_load,
     pr.profile_id,
     a.activity_date
     
FROM 
     Activities a,
     ActivityReports ar,
     ActivityReportStatus ars,
     Clients c,
     Assignements ass,
     Projects p,
     Profiles pr 
WHERE 
     ar.activity_report_status_id IN (
          SELECT activity_report_status_id 
          FROM ActivityReportStatus 
          WHERE activity_report_status_shortname != "P_APPROVAL" AND activity_report_status_shortname != "P_USER_VALIDATION"
     )
AND (
     a.activity_id = ar.activity_id
     AND
     ass.assignement_id = a.assignement_id
     AND
     p.project_id = ass.project_id
     AND
     pr.profile_id = ass.profile_id
     AND
     p.client_id = c.client_id
);


-------------------------------------------------------------------------------------------
-- ajout des options de couleurs par défaut pour le tableau d'utilisation des ressources --
-------------------------------------------------------------------------------------------

INSERT INTO `Properties` (`property_id`, `property_name`, `property_label`, `property_type_id`) VALUES
(4, 'color_project_type_1', 'Régie', 4),
(5, 'color_project_type_2', 'Forfait', 4),
(6, 'color_project_type_3', 'Régie forfaitée', 4),
(7, 'color_project_type_4', 'r&d', 4),
(8, 'color_project_type_5', 'Congés', 4),
(9, 'color_project_type_6', 'Autre', 4),
(10, 'color_project_type_7', 'Avant vente', 4);

-- Uncomment to set defaut colors :
-- WARNING : you have to be sure that ids of properties are correctly set,
-- or you will set values from differents options

-- INSERT INTO `PropertyValues` (`property_value_id`, `property_id`, `property_value_content`) VALUES
-- (0, 4, 'red'),
-- (0, 5, 'blue'),
-- (0, 6, 'green'),
-- (0, 7, 'fuchsia'),
-- (0, 8, 'teal'),
-- (0, 9, 'grey'),
-- (0, 10, '#FFA500');

-----------------------------------------------
-- update de la version de la base de donnée --
-----------------------------------------------
UPDATE `PropertyValues` SET `property_value_content` = '5' WHERE `PropertyValues`.`property_value_id` =2;

COMMIT;