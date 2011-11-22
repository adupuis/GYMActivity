
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

-- Le schéma de la base de données est en version 4
INSERT INTO PropertyValues VALUES(0,2,'4');

COMMIT;
