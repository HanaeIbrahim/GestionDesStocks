-- Créer une base de données gestionStock si elle n'existe pas
CREATE DATABASE IF NOT EXISTS gestionStock;

/**
    Créer l'utilisateur pour se connecter à la base de données s'il n'existe pas
    et donne tous les droits sur cette base de données à l'utilisateur.
 */
CREATE USER IF NOT EXISTS 'adminUser'@'localhost' IDENTIFIED BY 'H2e0i2g-v2d3';
GRANT ALL PRIVILEGES ON gestionStock.* TO 'adminUser'@'localhost';

-- On utilise la base de données qu'on vient de créer
USE gestionStock;

-- Créer les tables admin, produit et magasin
CREATE TABLE IF NOT EXISTS admin (
  id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pseudo varchar(30) NOT NULL UNIQUE,
  prenom varchar(30) NOT NULL,
  nom varchar(30) NOT NULL,
  email varchar(50) NOT NULL,
  mot_de_passe varchar(255) NOT NULL,
);
CREATE TABLE IF NOT EXISTS produit (
    id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom varchar(30) NOT NULL,
    marque varchar(30) NOT NULL,
    nombre integer NOT NULL,
    FOREIGN KEY(fk_magasin) REFERENCES magasin(id),
    UNIQUE (nom, marque)
    
);

CREATE TABLE magasin (
    id integer PRIMARY KEY AUTOINCREMENT,
    nom varchar(30) NOT NULL,
    adresse varchar(80) NOT NULL
    UNIQUE (nom, adresse)
);

CREATE TABLE IF NOT EXISTS magasin (
     id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     nom varchar(30) NOT NULL,
     adresse varchar(80) NOT NULL
     UNIQUE (nom, adresse)
);

