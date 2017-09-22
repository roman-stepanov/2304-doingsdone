CREATE DATABASE doingsdone;

USE doingsdone;

CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name CHAR(255) NOT NULL,
  user_id INT NOT NULL,

  UNIQUE INDEX project (name, user_id),
  INDEX project_name (name)
);

CREATE TABLE tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  created DATETIME NOT NULL,
  completed DATETIME,
  name CHAR(255) NOT NULL,
  file_name CHAR(255),
  deadline DATETIME,
  project_id INT NOT NULL,
  user_id INT NOT NULL,

  INDEX task_name (name)
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  registration DATE NOT NULL,
  email CHAR(255) NOT NULL,
  name CHAR(255) NOT NULL,
  password CHAR(60) NOT NULL,
  contacts CHAR(255),

  UNIQUE INDEX user (email)
);
