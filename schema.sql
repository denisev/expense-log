DROP TABLE IF EXISTS expense;

CREATE TABLE expense (
  id                 INTEGER PRIMARY KEY AUTO_INCREMENT,
  recipient          VARCHAR(255),
  note               VARCHAR(255),
  amount             DECIMAL(10,2),
  tr_date            DATE
) ENGINE=InnoDB;

DROP TABLE IF EXISTS file;

CREATE TABLE file (
  id INTEGER PRIMARY KEY,
  type VARCHAR(255),
  size INTEGER,
  bits LONGBLOB
) ENGINE=InnoDB;

