DROP TABLE FantasyLeague cascade constraints;
DROP TABLE Coach cascade constraints;
DROP TABLE RealTeam cascade constraints;
DROP TABLE Match cascade constraints;
DROP TABLE FantasyTeam cascade constraints;
DROP TABLE Player cascade constraints;
DROP TABLE PlayedIn cascade constraints;
DROP TABLE EquipR1 cascade constraints;
DROP TABLE EquipR2 cascade constraints;
DROP TABLE InvolvedIn cascade constraints;
DROP TABLE BetR1 cascade constraints;
DROP TABLE BetR2 cascade constraints;
DROP TABLE ViewerR1 cascade constraints;
DROP TABLE ViewerR2 cascade constraints;



CREATE TABLE FantasyLeague(
fantasyLeagueName char(50),
PRIMARY KEY (fantasyLeagueName));

CREATE TABLE Coach(
username char(50),
birthDate date,
email char(50) UNIQUE,
PRIMARY KEY (username));

CREATE TABLE RealTeam(
realTeamName char(50),
goalsSaved int,
goalsScored int,
PRIMARY KEY (realTeamName));

CREATE TABLE Match(
ID int,
winner char(50),
score char(5),
fantasyLeagueName char(50) NOT NULL,
PRIMARY KEY (ID),
FOREIGN KEY (fantasyLeagueName) REFERENCES FantasyLeague ON DELETE CASCADE);

CREATE TABLE FantasyTeam(
fantasyTeamName char(50),
record char(50),
username char(50) NOT NULL,
PRIMARY KEY (fantasyTeamName),
FOREIGN KEY (username) REFERENCES Coach ON DELETE CASCADE);

CREATE TABLE Player(
playerName char(50),
goalsSaved int,
goalsScored int,
playerNumber int,
realTeamName char(50) DEFAULT NULL,
fantasyTeamName char(50) DEFAULT NULL,
PRIMARY KEY (playerName, playerNumber),
FOREIGN KEY (realTeamName) REFERENCES RealTeam(realTeamName),
FOREIGN KEY (fantasyTeamName) REFERENCES FantasyTeam(fantasyTeamName) ON DELETE SET NULL);

CREATE TABLE PlayedIn(
assists int,
goals int,
ID int,
playerName char(50),
playerNumber int,
PRIMARY KEY (ID, playerName, playerNumber),
FOREIGN KEY (ID) REFERENCES Match ON DELETE CASCADE,
FOREIGN KEY (playerName,playerNumber) REFERENCES Player(playerName,playerNumber) ON DELETE CASCADE);


CREATE TABLE EquipR1(
playerName char(50),
playerNumber int,
lockerNumber int,
skateModel char(50),
stickModel char(50),
PRIMARY KEY (playerName, playerNumber, lockerNumber),
FOREIGN KEY (playerName,playerNumber) REFERENCES Player(playerName,playerNumber) ON DELETE CASCADE);

CREATE TABLE EquipR2(
skateModel char(50),
stickModel char(50),
cost int,
PRIMARY KEY (skateModel, stickModel));

CREATE TABLE InvolvedIn(
ID int,
fantasyTeamName char(50),
PRIMARY KEY (ID, fantasyTeamName),
FOREIGN KEY (ID) REFERENCES Match ON DELETE CASCADE,
FOREIGN KEY (fantasyTeamName) REFERENCES FantasyTeam ON DELETE CASCADE);

CREATE TABLE BetR1(
ID int,
amount int,
PRIMARY KEY (ID));

CREATE TABLE BetR2(
amount int,
betType char(50),
PRIMARY KEY (amount));

CREATE TABLE ViewerR1(
username char(50),
birthDate date,
email char(50) UNIQUE,
ID int,
PRIMARY KEY (username),
FOREIGN KEY (ID) REFERENCES BetR1 ON DELETE CASCADE);

CREATE TABLE ViewerR2(
ID int,
fantasyTeamName char(50) DEFAULT NULL,
PRIMARY KEY (ID),
FOREIGN KEY (ID) REFERENCES BetR1 ON DELETE CASCADE,
FOREIGN KEY (fantasyTeamName) REFERENCES FantasyTeam ON DELETE CASCADE);

INSERT INTO FantasyLeague VALUES ('League1');
INSERT INTO FantasyLeague VALUES ('League2');
INSERT INTO FantasyLeague VALUES ('League3');
INSERT INTO FantasyLeague VALUES ('League4');
INSERT INTO FantasyLeague VALUES ('League5');
INSERT INTO Coach VALUES ('user 5', TO_DATE('2001/01/01','YYYY/MM/DD'), 'email1@email.com');
INSERT INTO Coach VALUES ('user 6',TO_DATE('2002/02/02','YYYY/MM/DD'), 'email2@email.com');
INSERT INTO Coach VALUES ('user 7', TO_DATE('2003/03/03','YYYY/MM/DD'), 'email3@email.com');
INSERT INTO Coach VALUES ('user 8', TO_DATE('2004/04/04','YYYY/MM/DD'), 'email4@email.com');
INSERT INTO Coach VALUES ('user 9', TO_DATE('2005/05/05','YYYY/MM/DD'), 'email5@email.com');
INSERT INTO RealTeam VALUES ('Vancouver Canucks', 6, 6);
INSERT INTO RealTeam VALUES ('Detroit Red Wings', 7, 7);
INSERT INTO RealTeam VALUES ('Philadelphia Flyers', 8, 8);
INSERT INTO RealTeam VALUES ('Dallas Stars', 9, 9);
INSERT INTO RealTeam VALUES ('Toronto Maple Leafs', 10, 10);
INSERT INTO Match VALUES (1, 'team 1', '1-0', 'League1');
INSERT INTO Match VALUES (2, 'team 2', '2-0', 'League1');
INSERT INTO Match VALUES (3, 'team 3', '3-0', 'League2');
INSERT INTO Match VALUES (4, 'team 4', '4-0', 'League2');
INSERT INTO Match VALUES (6, 'team 1', '4-0', 'League2');
INSERT INTO Match VALUES (7, 'team 2', '4-0', 'League2');
INSERT INTO Match VALUES (8, 'team 3', '4-0', 'League2');
INSERT INTO Match VALUES (9, 'team 4', '4-0', 'League2');
INSERT INTO Match VALUES (5, 'tie', '5-5', 'League3');
INSERT INTO FantasyTeam VALUES ('FT1', '10-2', 'user 5');
INSERT INTO FantasyTeam VALUES ('FT2', '52-34', 'user 7');
INSERT INTO FantasyTeam VALUES ('FT3', '2-5', 'user 7');
INSERT INTO FantasyTeam VALUES ('FT4', '35-2', 'user 8');
INSERT INTO FantasyTeam VALUES ('FT5', '12-25', 'user 8');
INSERT INTO Player VALUES ('Elias Pettersson', 3, 10, 40, 'Vancouver Canucks', 'FT1');
INSERT INTO Player VALUES ('Michael Rasmussen', 3, 16, 30, 'Detroit Red Wings', 'FT2');
INSERT INTO Player VALUES ('Zack MacEwen', 8, 12, 71, 'Philadelphia Flyers', 'FT3');
INSERT INTO Player VALUES ('Nils Lundkvist', 12, 11, 5, 'Dallas Stars', 'FT4');
INSERT INTO Player VALUES ('Ana Lundkvist', 13, 8, 6, 'Dallas Stars', 'FT4');
INSERT INTO Player VALUES ('Morgan Rielly', 14, 4, 44, 'Toronto Maple Leafs', 'FT5');
INSERT INTO Player VALUES ('John Doe', 2, 20, 45, 'Toronto Maple Leafs', 'FT5');
INSERT INTO PlayedIn VALUES (0, 8, 1, 'Elias Pettersson', 40);
INSERT INTO PlayedIn VALUES (3, 2, 2, 'Elias Pettersson', 40);
INSERT INTO PlayedIn VALUES (1, 6, 1, 'Michael Rasmussen', 30);
INSERT INTO PlayedIn VALUES (2, 10, 2, 'Michael Rasmussen', 30);
INSERT INTO PlayedIn VALUES (4, 4, 3, 'Zack MacEwen', 71);
INSERT INTO PlayedIn VALUES (4, 8, 4, 'Zack MacEwen', 71);
INSERT INTO PlayedIn VALUES (0, 0, 9, 'Zack MacEwen', 71);
INSERT INTO PlayedIn VALUES (6, 2, 3, 'Nils Lundkvist', 5);
INSERT INTO PlayedIn VALUES (6, 9, 4, 'Nils Lundkvist', 5);
INSERT INTO PlayedIn VALUES (6, 2, 3, 'Ana Lundkvist', 6);
INSERT INTO PlayedIn VALUES (7, 6, 4, 'Ana Lundkvist', 6);
INSERT INTO PlayedIn VALUES (6, 4, 6, 'Morgan Rielly', 44);
INSERT INTO PlayedIn VALUES (1, 8, 5,'John Doe',45);
INSERT INTO PlayedIn VALUES (1, 12, 6,'John Doe',45);


INSERT INTO EquipR1 VALUES ('Elias Pettersson', 40, 1, 'skate model 1', 'stick model 1');
INSERT INTO EquipR1 VALUES ('Michael Rasmussen', 30, 1, 'skate model 2', 'stick model 2');
INSERT INTO EquipR1 VALUES ('Zack MacEwen', 71, 1, 'skate model 3', 'stick model 3');
INSERT INTO EquipR1 VALUES ('Nils Lundkvist', 5, 1, 'skate model 4', 'stick model 4');
INSERT INTO EquipR1 VALUES ('Morgan Rielly', 44, 1, 'skate model 5', 'stick model 5');
INSERT INTO EquipR1 VALUES ('Ana Lundkvist', 6, 1, 'skate model 8', 'stick model 1');
INSERT INTO EquipR1 VALUES ('John Doe', 45, 1, 'skate model 9', 'stick model 2');
INSERT INTO EquipR2 VALUES ('skate model 1', 'stick model 1', 100);
INSERT INTO EquipR2 VALUES ('skate model 2', 'stick model 2', 200);
INSERT INTO EquipR2 VALUES ('skate model 3', 'stick model 3', 300);
INSERT INTO EquipR2 VALUES ('skate model 4', 'stick model 4', 400);
INSERT INTO EquipR2 VALUES ('skate model 5', 'stick model 5', 500);
INSERT INTO EquipR2 VALUES ('skate model 8', 'stick model 1', 600);
INSERT INTO EquipR2 VALUES ('skate model 9', 'stick model 2', 1000);

INSERT INTO InvolvedIn VALUES (1, 'FT1');
INSERT INTO InvolvedIn VALUES (1, 'FT2');
INSERT INTO InvolvedIn VALUES (2, 'FT1');
INSERT INTO InvolvedIn VALUES (2, 'FT3');
INSERT INTO InvolvedIn VALUES (3, 'FT4');
INSERT INTO InvolvedIn VALUES (3, 'FT5');
INSERT INTO BetR1 VALUES (1, 100);
INSERT INTO BetR1 VALUES (2, 200);
INSERT INTO BetR1 VALUES (3, 300);
INSERT INTO BetR1 VALUES (4, 400);
INSERT INTO BetR1 VALUES (5, 500);
INSERT INTO BetR2 VALUES (100, 'type1');
INSERT INTO BetR2 VALUES (200, 'type2');
INSERT INTO BetR2 VALUES (300, 'type3');
INSERT INTO BetR2 VALUES (400, 'type4');
INSERT INTO BetR2 VALUES (500, 'type5');
INSERT INTO ViewerR1 VALUES ('user 1', TO_DATE('2001/01/01','YYYY/MM/DD'), 'email1@email.com', 1);
INSERT INTO ViewerR1 VALUES ('user 2', TO_DATE('2002/02/02','YYYY/MM/DD'), 'email2@email.com', 1);
INSERT INTO ViewerR1 VALUES ('user 3', TO_DATE('2003/03/03','YYYY/MM/DD'), 'email3@email.com', 2);
INSERT INTO ViewerR1 VALUES ('user 4', TO_DATE('2004/04/04','YYYY/MM/DD'), 'email4@email.com', 2);
INSERT INTO ViewerR1 VALUES ('user 5', TO_DATE('2005/05/05','YYYY/MM/DD'), 'email5@email.com', 5);
INSERT INTO ViewerR2 VALUES (1, 'FT1');
INSERT INTO ViewerR2 VALUES (2, 'FT2');
INSERT INTO ViewerR2 VALUES (3, 'FT3');
INSERT INTO ViewerR2 VALUES (4, 'FT4');
INSERT INTO ViewerR2 VALUES (5, 'FT5');
COMMIT;
