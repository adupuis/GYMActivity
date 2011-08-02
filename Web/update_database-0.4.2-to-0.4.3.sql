SET NAMES 'utf8';
UPDATE ActivityReportStatus SET activity_report_status_name='En attente de validation manager' WHERE activity_report_status_shortname='P_APPROVAL';
INSERT INTO ActivityReportStatus VALUES(NULL,'REFUSED','Refusé',"Le rapport d'activité a été refusé par le management.");