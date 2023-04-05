# CREATE USER 'root'@'%' IDENTIFIED BY 'root';
GRANT ALL PRIVILEGES  ON *.* TO 'root'@'%';


# create databases
CREATE DATABASE IF NOT EXISTS happytasks;
CREATE DATABASE IF NOT EXISTS testing;

CREATE USER 'happytasks'@'%' IDENTIFIED BY 'happytasks';
GRANT ALL PRIVILEGES ON happytasks.* TO 'happytasks'@'%';

CREATE USER 'testing'@'%' IDENTIFIED BY 'testing';
GRANT ALL PRIVILEGES ON testing.* TO 'testing'@'%';
