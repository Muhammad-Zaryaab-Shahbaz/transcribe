-- Start Message Table --
CREATE TABLE start_msg (
  id int(5) NOT NULL AUTO_INCREMENT,
  msg text,
  updatedOn timestamp DEFAULT CURRENT_TIMESTAMP,
  createdOn timestamp,
  PRIMARY KEY (id)
);
-- Chain Table --
CREATE TABLE chain (
  chainId int(5) NOT NULL AUTO_INCREMENT,
  msgId int(5),
  status tinyint(1) DEFAULT 0 COMMENT 'idle (0) inprocess (1)',
  completed tinyint(1) DEFAULT 0 COMMENT 'no (0) yes (1)',
  user int(5) COMMENT 'last user in this chain',
  updatedOn timestamp DEFAULT CURRENT_TIMESTAMP,
  createdOn timestamp,
  PRIMARY KEY (chainId),
  FOREIGN KEY (msgId) REFERENCES start_msg(id) ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (user) REFERENCES users(userId) ON UPDATE CASCADE ON DELETE CASCADE
);
-- Users Table --
CREATE TABLE users (
  userId int(5) NOT NULL AUTO_INCREMENT,
  chain int(5) NOT NULL,
  place int(4),
  timeSpent int(11),
  answer text,
  updatedOn timestamp DEFAULT CURRENT_TIMESTAMP,
  createdOn timestamp,
  PRIMARY KEY (userId),
  FOREIGN KEY (chain) REFERENCES chain(chainId) ON UPDATE CASCADE ON DELETE CASCADE
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

INSERT INTO `admins` (
    `username`,
    `pwd`,
    `updatedOn`,
    `createdOn`
  )
VALUES (
    'admin',
    '9ff871b9e3aa2b2e0f317fd7fdb35e844e1816e4dfe009008f4e6035141da3da',
    CURRENT_TIMESTAMP,
    NOW()
  );
INSERT INTO `start_msg` (`msg`, `updatedOn`, `createdOn`)
VALUES (
    'Hello World',
    CURRENT_TIMESTAMP,
    NOW()
  );
INSERT INTO `start_msg` (`msg`, `updatedOn`, `createdOn`)
VALUES (
    'Second text to you',
    CURRENT_TIMESTAMP,
    NOW()
  );