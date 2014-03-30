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
-- Initialize Matrix
	INSERT INTO TrainingMatrix (worker,subcell,training)
	VALUES ('ZZZZ','9999','0'),
	('AndrewL','1000','0.97'),
	('Ben','1010','0.98'),
	('Cam','1020','0.90'),
	('Dan','1030','0.94');
	
	
	

	