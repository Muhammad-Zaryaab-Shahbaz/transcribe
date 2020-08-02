-- Users Table --
CREATE TABLE users (
  userId int(5) NOT NULL AUTO_INCREMENT,
  chainId int(5) NOT NULL,
  place int(4),
  timeSpent int(11),
  answer text,
  updatedOn timestamp DEFAULT CURRENT_TIMESTAMP,
  createdOn timestamp,
);
-- Admin Table --
CREATE TABLE admins (
  userId int(5) NOT NULL AUTO_INCREMENT,
  username varchar(255) NOT NULL,
  pwd varchar(256) NOT NULL,
  updatedOn timestamp DEFAULT CURRENT_TIMESTAMP,
  createdOn timestamp,
  PRIMARY KEY (userId)
);