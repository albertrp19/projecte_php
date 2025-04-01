CREATE TABLE users(
    iduser INT AUTO_INCREMENT PRIMARY KEY,
    mail VARCHAR(40) UNIQUE NOT NULL,
    username VARCHAR(16) UNIQUE NOT NULL,
    passHash VARCHAR(60) NOT NULL,
    userFirstName VARCHAR(60) NULL,
    userLastName VARCHAR(120) NULL,
    creationDate DATETIME NOT NULL,
    removeDate DATETIME NOT NULL,
    lastSignIn DATETIME NOT NULL,
    active TINYINT(1) DEFAULT 0
);