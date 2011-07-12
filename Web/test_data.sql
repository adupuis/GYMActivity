USE GYMActivity;
INSERT INTO Profiles VALUES (1,'ksause','Kaiser','Sause','toto','ksause@genymobile.com',2);

insert into Clients values(0,'test client 1');
insert into Clients values(1,'test client 2');
insert into Clients values(2,'test client 3');

insert into Projects values(0,'Test 1','',0,'Chatillon','2011-05-01','2011-08-01',0,0);
insert into Projects values(1,'Test 2','',0,'Paris','2011-05-01','2011-08-01',0,0);
insert into Projects values(2,'Test 3','',0,'New York','2011-05-01','2011-08-01',0,0);

insert into Tasks values(0,'Task 1','',0);
insert into Tasks values(1,'Task 2','',0);
insert into Tasks values(2,'Task 1','',1);
insert into Tasks values(3,'Task 1','',2);
insert into Tasks values(4,'Task 2','',2);
insert into Tasks values(5,'Task 3','',2);
insert into Tasks values(6,'Task 4','',2);

insert into Activities values (0,'2011-05-01',8,'2011-05-05',1,0);
insert into Activities values (1,'2011-05-02',8,'2011-05-05',1,0);
insert into Activities values (2,'2011-05-03',8,'2011-05-05',1,0);
insert into Activities values (3,'2011-05-01',8,'2011-05-05',1,1);
insert into Activities values (4,'2011-05-02',8,'2011-05-05',1,1);
insert into Activities values (5,'2011-05-03',8,'2011-05-05',1,1);