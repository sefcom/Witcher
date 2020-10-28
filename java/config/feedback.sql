create database feedback;
use feedback;

CREATE USER 'user'@'%' IDENTIFIED BY 'password';

grant usage on *.* to 'user'@'%' identified by 'password';
grant all privileges on feedback.* to 'user'@'%';

CREATE TABLE comments (
        id INT NOT NULL AUTO_INCREMENT,
        MYUSER VARCHAR(30) NOT NULL,
        EMAIL VARCHAR(30),
        WEBPAGE VARCHAR(100) NOT NULL,
        DATUM DATE NOT NULL,
        SUMMARY VARCHAR(40) NOT NULL,
        COMMENTS VARCHAR(400) NOT NULL,
        PRIMARY KEY (ID)
    );

INSERT INTO comments values (default, 'lars', 'myemail@gmail.com','https://www.vogella.com/', '2009-09-14 10:33:11', 'Summary','My first comment' );
