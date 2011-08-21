
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

ALTER TABLE Assignements ADD COLUMN assignement_is_active boolean not null default true AFTER assignement_overtime_allowed;

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

COMMIT;