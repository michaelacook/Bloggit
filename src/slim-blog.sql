CREATE DATABASE slimblog;

CREATE TABLE users (
    user_id INT NOT NULL AUTO_INCREMENT,
    username VARCHAR(255),
    full_name VARCHAR(255),
    email VARCHAR(255),
    password VARCHAR(255),
    about VARCHAR(255),
    picture_path VARCHAR(255),
    account_status VARCHAR(255),
    activation_token VARCHAR(255),
    PRIMARY KEY (user_id)
);

CREATE TABLE posts (
    post_id INT NOT NULL AUTO_INCREMENT,
    user_id INT,
    title VARCHAR(255),
    author VARCHAR(255),
    category VARCHAR(255),
    published_on DATE,
    access_level INT,
    body TEXT,
    PRIMARY KEY (post_id)
);

CREATE TABLE comments (
    comment_id INT NOT NULL AUTO_INCREMENT,
    post_id INT,
    user_id INT,
    username VARCHAR(255),
    body TEXT,
    PRIMARY KEY (comment_id)
)

CREATE TABLE follows (
    username VARCHAR(255),
    follows VARCHAR(255)
)
