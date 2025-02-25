CREATE DATABASE calculator_db;

USE calculator_db;
-- database name

CREATE TABLE history
(
    id INT NOT NULL PRIMARY KEY,
    first_numbers INT NOT NULL,
    second_numbers INT NOT NULL,
    expression VARCHAR(255) NOT NULL,
    result VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
