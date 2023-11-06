<?php

$db = new SQLite3('gestionStock.sqlite'); // céer la base de donnée si elle n'existe pas encore et si elle existe il s'y connect
if (!$db) {
    echo $db->lastErrorMsg();
} else {
    echo "La connection à la base de données a été effectuée avec succès", "<br>";
}

/*
id : int, chaque id a une clé primaire, a chaque fois qu'on a un admin l'id s'incrémente
varchar(30): chaine de charactaire variable qui va varier entre 0  char et 30 char
ON DELETE CASCADE : si je supp un magasin auto tous les produits qui ont comme id comme magasin seront supprimée

*/



$sql = <<<'COMMANDE_SQL'
CREATE TABLE admin (
  id integer PRIMARY KEY AUTOINCREMENT,
  pseudo varchar(30) NOT NULL UNIQUE,
  prenom varchar(30) NOT NULL,
  nom varchar(30) NOT NULL,
  email varchar(50) NOT NULL,
  mot_de_passe varchar(255) NOT NULL
);

CREATE TABLE magasin (
    id integer PRIMARY KEY AUTOINCREMENT,
    nom varchar(30) NOT NULL,
    adresse varchar(80) NOT NULL,
    UNIQUE (nom, adresse)
);

CREATE TABLE produit (
    id integer PRIMARY KEY AUTOINCREMENT,
    nom varchar(30) NOT NULL,
    marque varchar(30) NOT NULL,
    nombre integer NOT NULL,
    fk_magasin integer NOT NULL,
    FOREIGN KEY(fk_magasin) REFERENCES magasin(id),
    UNIQUE (nom, marque)
    
);



/* Mot de passe : Testtest1$ */
INSERT INTO admin (pseudo, prenom, nom, email, mot_de_passe) VALUES ('HI', 'Hanae','Ibrahim',  'hanae.ibrahim@gmail.com', '$2y$10$rqqrJKqB441HNcsBm8JhWOpOCtK2xrunrHX5Rn4Zf34Nb9UyuZ.aK');



COMMANDE_SQL;

// excute la reuqete si elle échou elle renvoi un message d'erreur sinon ell senvoie "Les tables ont été créées avec succès"
$ret = $db->exec($sql);
if (!$ret) {
    echo $db->lastErrorMsg();
} else {
    echo "Les tables ont été créées avec succès", "<br>";
}
$db->close();


?>