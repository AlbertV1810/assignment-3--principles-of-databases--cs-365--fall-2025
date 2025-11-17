DROP DATABASE IF EXISTS student_passwords;
CREATE DATABASE student_passwords;
USE student_passwords;

CREATE USER IF NOT EXISTS 'passwords_user'@'localhost' IDENTIFIED BY '';
GRANT ALL PRIVILEGES ON student_passwords.* TO 'passwords_user'@'localhost';
FLUSH PRIVILEGES;

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50),
  last_name VARCHAR(50),
  username VARCHAR(50),
  email VARCHAR(100)
);

CREATE TABLE sites (
  site_id INT AUTO_INCREMENT PRIMARY KEY,
  site_name VARCHAR(100),
  url VARCHAR(255)
);

CREATE TABLE credentials (
  credential_id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  site_id INT,
  password VARBINARY(255),
  comment TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id),
  FOREIGN KEY (site_id) REFERENCES sites(site_id)
);

INSERT INTO users (first_name, last_name, username, email)
VALUES
('Gordon', 'Sumner', 'sting123', 'sting@thepolice.com'),
('Ada', 'Lovelace', 'ada_code', 'ada@math.net'),
('Grace', 'Hopper', 'grace_debug', 'grace@navy.mil');

INSERT INTO sites (site_name, url)
VALUES
('Zoom', 'https://zoom.us'),
('GitHub', 'https://github.com'),
('Netflix', 'http://netflix.com');

INSERT INTO credentials (user_id, site_id, password, comment)
VALUES
(1, 1, AES_ENCRYPT('zoom_secure_11', 'secret_key'), 'Video conferencing account'),
(2, 2, AES_ENCRYPT('git_secure_22', 'secret_key'), 'Code repository login'),
(3, 3, AES_ENCRYPT('stream_pass_33', 'secret_key'), 'Streaming account');
