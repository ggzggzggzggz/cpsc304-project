drop table CORPORATION cascade constraints;                                     
drop table TEAM cascade constraints;                                            
drop table VENUE cascade constraints;                                           
drop table SPORT cascade constraints;                                           
drop table SPORTSPEOPLE cascade constraints;  
drop table SPONSOR cascade constraints;
drop table CompetesAt cascade constraints;                                           
drop table Plays cascade constraints;  
drop table Participates cascade constraints;
drop table SeasonOrganizes cascade constraints;                                           
drop table League cascade constraints;  
drop table PlayFor cascade constraints;
drop table Coach cascade constraints;                                           
drop table Athlete cascade constraints;  
drop table Match1 cascade constraints;
drop table Match2 cascade constraints;
CREATE TABLE Team(
  ID INTEGER, 
  location CHAR(255), 
  name CHAR(255) NOT NULL,
  PRIMARY KEY (ID));
CREATE TABLE Venue(
  name CHAR(255), 
  location CHAR(255), 
  capacity INTEGER NOT NULL,
  PRIMARY KEY (name, location));
CREATE TABLE Sport(
  name CHAR(255), 
  numberOfFans INTEGER,
  PRIMARY KEY (name));
CREATE TABLE SportsPeople(
  name CHAR(255), 
  dateOfBirth CHAR(255), 
  nationality CHAR(255) NOT NULL,
  PRIMARY KEY (name, dateOfBirth));
CREATE TABLE Corporation(
  name CHAR(255),
  totalValue INTEGER,
  PRIMARY KEY (name));
CREATE TABLE Sponsor(
  corporationName CHAR(255), 
  teamID INTEGER, 
  numOfYears INTEGER,
  PRIMARY KEY (corporationName, teamID),
  FOREIGN KEY (corporationName) REFERENCES Corporation(name) ON DELETE CASCADE,
  FOREIGN KEY (teamID) REFERENCES Team(ID) ON DELETE CASCADE);
CREATE TABLE CompetesAt(
  teamID INTEGER, 
  venueName CHAR(255), 
  venueLocation CHAR(255),
  PRIMARY KEY (teamID, venueName, venueLocation),
  FOREIGN KEY (teamID) REFERENCES Team(ID) ON DELETE CASCADE,
  FOREIGN KEY (venueName, venueLocation) REFERENCES Venue(name, location) ON DELETE CASCADE);
CREATE TABLE League(
  name CHAR(255), 
  region CHAR(255) NOT NULL, 
  sportName CHAR(255),
  PRIMARY KEY (name),
  FOREIGN KEY (sportName) REFERENCES Sport(name) ON DELETE CASCADE);
CREATE TABLE SeasonOrganizes(
  year INTEGER, 
  leagueName CHAR(255), 
  statistics CHAR(255),
  PRIMARY KEY (year, leagueName),
  FOREIGN KEY (leagueName) REFERENCES League(name) ON DELETE CASCADE);
CREATE TABLE Participates(
  teamID INTEGER, 
  seasonYear INTEGER, 
  leagueName CHAR(255),
  ranking INTEGER NOT NULL,
  PRIMARY KEY (teamID, seasonYear,leagueName),
  FOREIGN KEY (teamID) REFERENCES Team(ID) ON DELETE CASCADE,
  FOREIGN KEY (seasonYear,leagueName) REFERENCES SeasonOrganizes(year,leagueName) ON DELETE CASCADE);
CREATE TABLE PlayFor(
  teamID INTEGER, 
  sportPeopleName CHAR(255), 
  sportPeopleDateOfBirth CHAR(255),
  PRIMARY KEY (teamID, sportPeopleName, sportPeopleDateOfBirth),
  FOREIGN KEY (teamID) REFERENCES Team(ID) ON DELETE CASCADE,
  FOREIGN KEY (sportPeopleName, sportPeopleDateOfBirth) REFERENCES SportsPeople(name, dateOfBirth) ON DELETE CASCADE);
CREATE TABLE Coach(
  name CHAR(255), 
  dateOfBirth CHAR(255), 
  yearsActive INTEGER,
  PRIMARY KEY (name, dateOfBirth),
  FOREIGN KEY (dateOfBirth, name) REFERENCES SportsPeople(dateOfBirth, name) ON DELETE CASCADE);
CREATE TABLE Athlete(
  name CHAR(255), 
  dateOfBirth CHAR(255), 
  position CHAR(255) NOT NULL, 
  injuryHistory CHAR(255),
  PRIMARY KEY (name, dateOfBirth),
  FOREIGN KEY (dateOfBirth, name) REFERENCES SportsPeople(dateOfBirth, name) ON DELETE CASCADE);
CREATE TABLE Match1(
  matchDate CHAR(255) NOT NULL,
  venueLocation CHAR(255) NOT NULL,
  venueName CHAR(255) NOT NULL,
  type CHAR(255),
  PRIMARY KEY(matchDate,venueLocation,venueName),
  FOREIGN KEY(venueLocation,venueName) REFERENCES Venue(location,name) ON DELETE CASCADE);
CREATE TABLE Match2(
  ID INTEGER,
  matchDate CHAR(255),
  result CHAR(255),
  venueLocation CHAR(255) NOT NULL,
  venueName CHAR(255) NOT NULL,
  leagueName CHAR(255),
  seasonYear INTEGER,
  PRIMARY KEY(ID),
  FOREIGN KEY(leagueName,seasonYear) REFERENCES SeasonOrganizes(leagueName,year) ON DELETE CASCADE,
  FOREIGN KEY(matchDate,venueLocation,venueName) REFERENCES Match1(matchDate,venueLocation,venueName) ON DELETE CASCADE);
CREATE TABLE Plays(
  matchID INTEGER, 
  teamID INTEGER, 
  PRIMARY KEY (matchID, teamID),
  FOREIGN KEY (matchID) REFERENCES Match2(ID) ON DELETE CASCADE,
  FOREIGN KEY (teamID) REFERENCES Team(ID) ON DELETE CASCADE);
INSERT
INTO TEAM (ID, location,name)
VALUES (1, 'London','Tottenham Hotspur');
INSERT
INTO TEAM (ID, location,name)
VALUES (2, 'London','Arsenal');
INSERT
INTO TEAM (ID, location,name)
VALUES (3, 'Manchester','Manchester City');
INSERT
INTO TEAM (ID, location,name)
VALUES (4, 'Liverpool','Liverpool');
INSERT
INTO TEAM (ID, location,name)
VALUES (5, 'Aston','Aston Villa');
INSERT
INTO TEAM (ID, location,name)
VALUES (6, 'Brighton Hove','Brighton Hove Albion');
INSERT
INTO VENUE(name,location,capacity)
VALUES ('Tottenham Hotspur Stadium','London',62850);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('Emirates Stadium','London',60704);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('Etihad Stadium','Manchester',53400);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('Anfield','Liverpool',54074);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('Villa Park','Aston',42640);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('American Express Stadium','Brighton Hove',31876);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('Princes Park','Paris',48583);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('Allianz Arena','Munich',75024);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('Camp Nou','Barcelona',99354);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('The Valley','London',27111);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('Selhurst Park','London',25486);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('Craven Cottage','London',20996);
INSERT
INTO VENUE(name,location,capacity)
VALUES ('The Den','London',19369);
INSERT
INTO CORPORATION (name, totalValue)
VALUES ('Emirates Airline', 1000000000);
INSERT
INTO CORPORATION (name, totalValue)
VALUES ('Qatar Airline', 1300000000);
INSERT
INTO CORPORATION (name, totalValue)
VALUES ('Lays', 90000000);
INSERT
INTO CORPORATION (name, totalValue)
VALUES ('Heineken', 500000000);
INSERT
INTO CORPORATION (name, totalValue)
VALUES ('Expedia', 200000000);
INSERT
INTO CORPORATION (name, totalValue)
VALUES ('AIA', 4000000);
INSERT
INTO CORPORATION (name, totalValue)
VALUES ('Standard Chartered', 100000000);
INSERT
INTO CORPORATION (name, totalValue)
VALUES ('Etihad Airways', 80000000);
INSERT
INTO CORPORATION (name, totalValue)
VALUES ('BK8', 30000000);
INSERT
INTO CORPORATION (name, totalValue)
VALUES ('Nike', 100000000);
INSERT
INTO Sport (name, numberOfFans)
VALUES ('Football', 50000000);
INSERT
INTO Sport (name, numberOfFans)
VALUES ('Hockey', 20000000);
INSERT
INTO Sport (name, numberOfFans)
VALUES ('Basketball', 25000000);
INSERT
INTO Sport (name, numberOfFans)
VALUES ('Volleyball', 20000000);
INSERT
INTO Sport (name, numberOfFans)
VALUES ('Tennis', 10000000);
INSERT
INTO SPORTSPEOPLE (name,dateOfBirth,nationality)
VALUES ( 'Daniel Philip Levy', '1962-02-08', 'British');
INSERT
INTO SPORTSPEOPLE (name,dateOfBirth,nationality)
VALUES ( 'Arsene Wenger', '1949-10-22', 'France');
INSERT
INTO SPORTSPEOPLE (name,dateOfBirth,nationality)
VALUES ( 'Sheikh Mansour', '1970-11-20', 'Abu Dhabi');
INSERT
INTO SPORTSPEOPLE (name,dateOfBirth,nationality)
VALUES ( 'Peter Krawietz', '1971-12-31', 'German');
INSERT
INTO SPORTSPEOPLE (name,dateOfBirth,nationality)
VALUES ( 'John Biden', '1993-10-18', 'Scotland');
INSERT
INTO SPORTSPEOPLE (name,dateOfBirth,nationality)
VALUES ( 'Zhiyi Fan', '1969-11-06', 'China');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ( 'Pep Guardiola', '1971-01-18', 'Spain');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ( 'Unai Emery', '1971-11-03', 'Spain');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Sir Alex Ferguson', '1941-12-31', 'Scotland');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Mauricio Pochettino', '1972-03-02', 'Argentina');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Harry Kane', '1993-07-28', 'England');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Son Heung-min', '1992-07-08', 'Korea');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Hugo Lloris', '1986-12-26', 'France');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Gabriel Jesus', '1997-04-03', 'Brazil');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Bukayo Saka', '2001-09-05', 'England');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Declan Rice', '1999-01-14', 'England');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Bernardo Silva', '1994-08-10', 'Portugal');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Erling Haaland', '2000-07-21', 'Norway');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Jack Grealish', '1995-09-10', 'England');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Mohamed Salah', '1992-06-15', 'Egypt');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Darwin Nunez', '1999-06-04', 'Uruguay');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Luis Diaz', '1997-01-13', 'Columbia');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('John McGinn', '1994-10-18', 'Scotland');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Emiliano Martinez','1992-09-02', 'Argentina');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Clement Lenglet', '1995-06-17', 'France');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Kaoru Mitoma', '1997-05-20', 'Japan');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('Ansu Fati', '2002-10-31', 'Spain');
INSERT
INTO SPORTSPEOPLE(name, dateOfBirth, nationality)
VALUES ('James Milner','1986-01-04', 'England');
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('AIA', 1, 8);
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('Emirates Airline', 2, 10);
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('Etihad Airways', 3, 8);
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('Standard Chartered', 4, 6);
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('BK8', 5, 3);
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('Nike', 1, 10);
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('Nike', 2, 10);
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('Nike', 3, 10);
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('Nike', 4, 10);
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('Nike', 5, 10);
INSERT
INTO Sponsor (corporationName, teamID, numOfYears)
VALUES ('Nike', 6, 10);
INSERT
INTO CompetesAt(teamID, venueName, venueLocation)
VALUES (1, 'Tottenham Hotspur Stadium', 'London');
INSERT
INTO CompetesAt(teamID, venueName, venueLocation)
VALUES (2, 'Emirates Stadium', 'London');
INSERT
INTO CompetesAt(teamID, venueName, venueLocation)
VALUES (3, 'Etihad Stadium','Manchester');
INSERT
INTO CompetesAt(teamID, venueName, venueLocation)
VALUES (4, 'Anfield','Liverpool');
INSERT
INTO CompetesAt(teamID, venueName, venueLocation)
VALUES (5, 'Villa Park','Aston');
INSERT
INTO CompetesAt(teamID, venueName, venueLocation)
VALUES (6, 'American Express Stadium','Brighton Hove');
INSERT
INTO League (name, region, sportName)
VALUES ( 'Premier League', 'Europe', 'Football');
INSERT
INTO League (name, region, sportName)
VALUES ('Major League Soccer', 'North America', 'Football');
INSERT
INTO LEAGUE (name, region, sportName)
VALUES('Ligue 1', 'Europe', 'Football');
INSERT
INTO LEAGUE (name, region, sportName)
VALUES('Bundesliga', 'Europe', 'Football');
INSERT
INTO LEAGUE (name, region, sportName)
VALUES('Laliga', 'Europe', 'Football');
INSERT
INTO LEAGUE (name, region, sportName)
VALUES('FA Cup', 'Europe', 'Football');
INSERT
INTO SeasonOrganizes (year,leagueName,statistics)
VALUES (2023,'FA Cup', 'Champion is Manchester City F.C.');
INSERT
INTO SeasonOrganizes (year,leagueName,statistics)
VALUES (2023,'Premier League', 'Still ongoing, Arsenal takes the lead for now');
INSERT
INTO SeasonOrganizes (year,leagueName,statistics)
VALUES (2022,'Premier League', 'Champion is Manchester City F.C.');
INSERT
INTO SeasonOrganizes (year,leagueName,statistics)
VALUES (2021,'Premier League', 'Champion is Manchester City F.C.');
INSERT
INTO SeasonOrganizes (year,leagueName,statistics)
VALUES (2020,'Premier League', 'Champion is Manchester City F.C.');
INSERT
INTO SeasonOrganizes (year,leagueName,statistics)
VALUES (2019,'Premier League', 'Champion is Liverpool');
INSERT
INTO SeasonOrganizes (year,leagueName,statistics)
VALUES (2018,'Premier League', 'Champion is Manchester City F.C.');
INSERT
INTO Participates (teamID,seasonYear,leagueName,ranking)
VALUES (1,2022, 'Premier League',8);
INSERT
INTO Participates (teamID,seasonYear,leagueName,ranking)
VALUES (2,2022, 'Premier League',2);
INSERT
INTO Participates (teamID,seasonYear,leagueName,ranking)
VALUES (3,2022, 'Premier League',1);
INSERT
INTO Participates (teamID,seasonYear,leagueName,ranking)
VALUES (4,2022, 'Premier League',5);
INSERT
INTO Participates (teamID,seasonYear,leagueName,ranking)
VALUES (5,2022, 'Premier League',7);
INSERT
INTO Participates (teamID,seasonYear,leagueName,ranking)
VALUES (6,2022, 'Premier League',6);
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (1,'Harry Kane','1993-07-28');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (1,'Son Heung-min','1992-07-08');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (1,'Hugo Lloris', '1986-12-26');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (2,'Gabriel Jesus','1997-04-03');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (2,'Bukayo Saka', '2001-09-05');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (2,'Declan Rice', '1999-01-14');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (3,'Bernardo Silva','1994-08-10');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (3,'Erling Haaland', '2000-07-21');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (3,'Jack Grealish', '1995-09-10');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (4,'Mohamed Salah','1992-06-15');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (4,'Darwin Nunez','1999-06-04');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (4,'Luis Diaz', '1997-01-13');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (5,'John McGinn','1994-10-18');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (5,'Emiliano Martinez','1992-09-02');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (5,'Clement Lenglet', '1995-06-17');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (6,'Kaoru Mitoma','1997-05-20');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (6,'Ansu Fati', '2002-10-31');
INSERT
INTO PlayFor (teamID,sportPeopleName,sportPeopleDateOfBirth)
VALUES (6,'James Milner','1986-01-04');
INSERT
INTO Coach (name, dateOfBirth, yearsActive)
VALUES ('Arsene Wenger', '1949-10-22', 25);
INSERT
INTO Coach (name, dateOfBirth, yearsActive)
VALUES ('Pep Guardiola', '1971-01-18', 15);
INSERT
INTO Coach (name, dateOfBirth, yearsActive)
VALUES ('Unai Emery', '1971-11-03', 15);
INSERT
INTO Coach (name, dateOfBirth, yearsActive)
VALUES ('Sir Alex Ferguson', '1941-12-31', 30);
INSERT
INTO Coach (name, dateOfBirth, yearsActive)
VALUES ('Mauricio Pochettino', '1972-03-02', 10);
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Harry Kane', '1993-07-28', 'Forward','Ankle injury on Apr 16, 2021');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Son Heung-min', '1992-07-08', 'Forward','Groin surgery on May 29, 2023');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Hugo Lloris', '1986-12-26', 'Goal Keeper','N/A');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Gabriel Jesus', '1997-04-03', 'Forward','Hamstring injury on Oct 24, 2023');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Bukayo Saka', '2001-09-05', 'Forward','Thigh problems on Oct 03, 2023');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Declan Rice', '1999-01-14', 'Midfielder','Ill on April 28, 2019');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Bernardo Silva', '1994-08-10', 'Midfield','muscular problems on Aug 14, 2023');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Erling Haaland', '2000-07-21', 'Forward','Ankle injury on Nov 17, 2023');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Jack Grealish', '1995-09-10', 'Left Winger','Hamstring injury on Aug 29, 2023');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Mohamed Salah', '1992-06-15', 'Forward','Corona virus on Nov 13, 2020');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Darwin Nunez', '1999-06-04', 'Forward','Toe injury on May 12, 2023');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Luis Diaz', '1997-01-13', 'Forward','Knee injury on Oct 10, 2022');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'John McGinn', '1994-10-18', 'Defender','Hamstring strain on Jan 1, 2023');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Emiliano Martinez','1992-09-02', 'Goal Keeper','Knee problems on May 20, 2022');
INSERT
INTO Athlete (name,dateOfBirth,position,injuryHistory)
VALUES ( 'Clement Lenglet', '1995-06-17', 'Defender','Adductor injury on Aug 10, 2022');
INSERT
INTO Athlete (name,dateOfBirth,position, injuryHistory)
VALUES ( 'Kaoru Mitoma', '1997-05-20','Forward' ,'N/A');
INSERT
INTO Athlete (name,dateOfBirth,position, injuryHistory)
VALUES ( 'Ansu Fati', '2002-10-31','Forward' ,'Knee bruise on Feb 24, 2023');
INSERT
INTO Athlete (name,dateOfBirth,position, injuryHistory)
VALUES ( 'James Milner','1986-01-04','Defender' ,'Muscle injury on Sept 20, 2023');
INSERT
INTO Match1 (matchDate,venueLocation,venueName,type)
VALUES ( '2022-10-01', 'London', 'Emirates Stadium','Regular Season');
INSERT
INTO Match1 (matchDate,venueLocation,venueName,type)
VALUES ( '2023-01-27', 'Manchester', 'Etihad Stadium','Tournament');
INSERT
INTO Match1 (matchDate,venueLocation,venueName,type)
VALUES ( '2022-12-22', 'Manchester', 'Etihad Stadium','Regular Season');
INSERT
INTO Match1 (matchDate,venueLocation,venueName,type)
VALUES ( '2022-12-26', 'Aston', 'Villa Park','Regular Season');
INSERT
INTO Match1 (matchDate,venueLocation,venueName,type)
VALUES ( '2023-09-30', 'Aston', 'Villa Park','Regular Season');
INSERT
INTO Match2 (ID,matchDate,result,venueLocation,venueName,leagueName, seasonYear)
VALUES (1, '2022-10-01', '3:1', 'London','Emirates Stadium','Premier League',2022);
INSERT
INTO Match2 (ID,matchDate,result,venueLocation,venueName,leagueName, seasonYear)
VALUES (2, '2023-01-27', '1:0', 'Manchester','Etihad Stadium','FA Cup', 2023);
INSERT
INTO Match2 (ID,matchDate,result,venueLocation,venueName,leagueName, seasonYear)
VALUES (3, '2022-12-22', '3:2', 'Manchester','Etihad Stadium','Premier League',2022);
INSERT
INTO Match2 (ID,matchDate,result,venueLocation,venueName,leagueName, seasonYear)
VALUES (4, '2022-12-26', '1:3', 'Aston','Villa Park','Premier League',2022);
INSERT
INTO Match2 (ID,matchDate,result,venueLocation,venueName,leagueName, seasonYear)
VALUES (5, '2023-09-30', '6:1', 'Aston', 'Villa Park', 'Premier League', 2023);
INSERT
INTO Plays (matchID,teamID)
VALUES (1,1);
INSERT
INTO Plays (matchID,teamID)
VALUES (1,2);
INSERT
INTO Plays (matchID,teamID)
VALUES (2,2);
INSERT
INTO Plays (matchID,teamID)
VALUES (2,3);
INSERT
INTO Plays (matchID,teamID)
VALUES (3,3);
INSERT
INTO Plays (matchID,teamID)
VALUES (3,4);
INSERT
INTO Plays (matchID,teamID)
VALUES (4,4);
INSERT
INTO Plays (matchID,teamID)
VALUES (4,5);
INSERT
INTO Plays (matchID,teamID)
VALUES (5,5);
INSERT
INTO Plays (matchID,teamID)
VALUES (5,6);