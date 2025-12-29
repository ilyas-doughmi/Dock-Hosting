CREATE DATABASE dockhosting;
USE dockhosting;

CREATE TABLE users(
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(100) UNIQUE,
    email VARCHAR(100) UNIQUE,
    password VARCHAR(255),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Project(

    project_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    project_name VARCHAR(100),
    port INT,
    container_name VARCHAR(255),
    status VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE

);

CREATE TABLE IF NOT EXISTS user_databases (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    project_name VARCHAR(100) NOT NULL,
    db_name VARCHAR(64) NOT NULL UNIQUE,
    db_user VARCHAR(64) NOT NULL,
    db_password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);


CREATE TABLE oauth_tokens(
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    access_token VARCHAR(255) NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)


ALTER TABLE Project
ADD COLUMN git_repo_url VARCHAR(255) NULL;

ALTER TABLE Project
ADD COLUMN git_branch VARCHAR(255) DEFAULT 'main';

ALTER TABLE Project
ADD COLUMN webhook_secret VARCHAR(255) NULL;

ALTER TABLE Project
ADD COLUMN auto_deploy BOOLEAN NOT NULL DEFAULT FALSE;