CREATE USER 'fido2service'@'localhost' IDENTIFIED BY 'fido';
CREATE DATABASE fido2service;
GRANT ALL PRIVILEGES ON fido2service.* TO 'fido2service'@'localhost';