--  Database Copyright 2014 by WebIS Spring 2014 License Apache 2.0

-- Person
DROP TABLE IF EXISTS worker;
CREATE TABLE Worker (
  worker VARCHAR(30),
  name VARCHAR(128),
  PRIMARY KEY (worker)
);

-- SELECT * FROM Person;

-- Training Matrix
DROP TABLE IF EXISTS TrainingMatrix;
CREATE TABLE TrainingMatrix (
  worker VARCHAR(30),
  subcell integer,
  training double,
  PRIMARY KEY (worker,subcell,training)
);

INSERT INTO TrainingMatrix (worker,subcell,training) VALUES 
	('Dr.Middelkoop',1000,0.19),
	('Dr.Middelkoop',1010,0.32),
	('JD',1020,0.98),
	('JD',1030,0.12);

	
-- SELECT * FROM TrainingMatrix;
-- SELECT person,cell,w