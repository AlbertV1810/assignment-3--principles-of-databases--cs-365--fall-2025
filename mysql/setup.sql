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
