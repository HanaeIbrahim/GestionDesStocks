<?php

require_once('./config/autoload.php');

use ch\comem\DbManager;

if (filter_has_var(INPUT_POST, 'produit-cree')) {
    // gérer les erreurs venant de la création du produit
    $produitMagasinsErr = $produitExistErr = $produitNomErr = $produitMarqueErr = $produitQuantiteErr = $successMsg = "";
    
    
    $produitNom = filter_input(INPUT_POST, 'produit-nom', FILTER_UNSAFE_RAW);
    $produitMarque = filter_input(INPUT_POST, 'produit-marque', FILTER_UNSAFE_RAW);
    // le min du produit = 1
    $produitQuantite = filter_input(INPUT_POST, 'produit-quantite', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    // on demande un tableau
    $produitMagasins = filter_input(INPUT_POST, 'produit-magasins', FILTER_REQUIRE_ARRAY);
    $validationError = false;
    
    // tant que la validation erreur est true on ne créer pas l'utilisateur
    if (empty($produitNom)) {
        $produitNomErr  = '<div class="alert alert-danger">
                    Il faut le nom du produit.
                </div>';
        $validationError = true;
        //expression regulière ou regex
    } else if (!preg_match("/[A-Z][a-zäàâèêéïöôüç]+([- ]?[A-Z][a-zäàâèêéïöôüç]+)?/", $produitNom)) {
        $produitNomErr = '<div class="alert alert-danger">
                            Le nom du produit ne doit contenir que des lettres, tirets et espaces
                        </div>';
        $validationError = true;
    }

    if (empty($produitMarque)) {
        $produitMarqueErr = '<div class="alert alert-danger">
                Il faut la marque du produit.
            </div>';
        $validationError = true;
    } else if (!preg_match("/[A-Z][a-zäàâèêéïöôüç]+([- ]?[A-Z][a-zäàâèêéïöôüç]+)?/", $produitMarque)) {
        $produitMarqueErr = '<div class="alert alert-danger">
                             La marque du produit ne doit contenir que des lettres, tirets et espaces
                        </div>';
        $validationError = true;
    }

    if (empty($produitQuantite)) {
        $produitQuantiteErr = '<div class="alert alert-danger">
                Il faut la quantité du produit.
            </div>';
        $validationError = true;
    } else if (!$produitQuantite) {
        $produitQuantiteErr = '<div class="alert alert-danger">
                             La quantité du produit doit être un nombre plus grand ou égale à 1
                        </div>';
        $validationError = true;
    }

    
    // on se connect à la base de de donnée lance la fonction constructeur de la db manager
    $db = new DbManager();

    if (empty($produitMagasins)) {
        $produitMagasinsErr = '<div class="alert alert-danger">
                Il faut placer le produit dans un magasin min.
            </div>';
        $validationError = true;
    } else if (!$produitMagasins) {
        // si la personne donne pas un tableau
        $produitMagasinsErr = '<div class="alert alert-danger">
                             Il faut un tableau des identifiant de magasin.
                        </div>';
        $validationError = true;
    } else if (!$db -> magasinsExist($produitMagasins)) {
        $produitMagasinsErr = '<div class="alert alert-danger">
                             Un ou plusieurs magasins n\'existe pas
                        </div>';
        $validationError = true;
    }

    if ($db -> produitExist($produitNom, $produitMarque)){
        $produitExistErr = '<div class="alert alert-danger">
        Le produit existe déjà
        </div>';
        // la validation d'erreur n'a pas passé
        $validationError = true;
    }
    

    // si la $validationError est true
    if (!$validationError) {
        
        // stocker le produit dans la bd
        $ok = $db->storeProduit($firstname, $lastname, $email, $password_hash);

        if (!$ok) {
            die("Problème d'enregistrement de l'utilisateur dans la base de données");
        } else {
            $successMsg = "<div class='alert alert-success'>
                                Votre compte a été créé !
                            </div>";
        }
    }
}
?>