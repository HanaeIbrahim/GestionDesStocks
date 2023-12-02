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
  prenom varchar(30) NOT NULL,
  nom varchar(30) NOT NULL,
  email varchar(50) NOT NULL UNIQUE,
  mot_de_passe varchar(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS magasin (
     id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
     nom varchar(30) NOT NULL,
     adresse varchar(80) NOT NULL,
     UNIQUE (nom, adresse)
);


CREATE TABLE IF NOT EXISTS produit (
    id int UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom varchar(30) NOT NULL,
    marque varchar(30) NOT NULL,
    quantite integer NOT NULL,
    -- UNSIGNED pour dire que je n'utilise que les chiffres positif
    UNIQUE (nom, marque)
    
);

CREATE TABLE IF NOT EXISTS produit_dans_magasin (
    fk_magasin int UNSIGNED NOT NULL, 
    fk_produit int UNSIGNED NOT NULL, 
    -- contraintes--
    PRIMARY KEY(fk_magasin, fk_produit), 
    FOREIGN KEY(fk_magasin) REFERENCES magasin(id), 
    FOREIGN KEY(fk_produit) REFERENCES produit(id)
);


/* Mot de passe : Testtest1$   */
INSERT INTO admin (prenom, nom, email, mot_de_passe) VALUES ('Hanae','Ibrahim',  'hanae.ibrahim@gmail.com', '$2y$10$rqqrJKqB441HNcsBm8JhWOpOCtK2xrunrHX5Rn4Zf34Nb9UyuZ.aK');

INSERT INTO magasin (nom, adresse) VALUES ('Bershka', 'Rue de 5, 1001 Lausanne');
INSERT INTO magasin (nom, adresse) VALUES ('Ricardo', 'Rue de 6, 1001 Lausanne');

INSERT INTO produit (nom, marque, quantite) VALUES ('Jeans', 'Levis', '4');
INSERT INTO produit (nom, marque, quantite) VALUES ('Pull', 'Davidos', '14');
INSERT INTO produit (nom, marque, quantite) VALUES ('T-shirt', 'Nike', '147');
   -- Dans magasin1 on a le produit 1--
INSERT INTO produit_dans_magasin (fk_magasin, fk_produit) VALUES ('1', '1');
-- Dans magasin1 on a le produit 2--
INSERT INTO produit_dans_magasin (fk_magasin, fk_produit) VALUES ('1', '2');
INSERT INTO produit_dans_magasin (fk_magasin, fk_produit) VALUES ('2', '3');
INSERT INTO produit_dans_magasin (fk_magasin, fk_produit) VALUES ('2', '1');

