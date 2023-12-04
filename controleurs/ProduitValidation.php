<?php

require_once('./config/autoload.php');

use ch\comem\DbManager;

var_dump(filter_input(INPUT_POST, 'produit-modifie', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]));

// INPUT_POST car dans la page produit.php on utilise la méthode post dans le formulaire
if (filter_has_var(INPUT_POST, 'produit-cree') || filter_has_var(INPUT_POST, 'produit-modifie') ) {
    // gérer les erreurs venant de la création du produit
    $produitIdErr = $produitMagasinsErr = $produitExistErr = $produitNomErr = $produitMarqueErr = $produitQuantiteErr = $successMsg = "";
    
    $produitId = filter_input(INPUT_POST, 'produit-modifie', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $produitNom = filter_input(INPUT_POST, 'produit-nom', FILTER_UNSAFE_RAW);
    $produitMarque = filter_input(INPUT_POST, 'produit-marque', FILTER_UNSAFE_RAW);
    // le min du produit = 1
    $produitQuantite = filter_input(INPUT_POST, 'produit-quantite', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    // on demande un tableau
    $arrayProduitMagasins = filter_input(INPUT_POST, 'produit-magasins', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
    // convertir les chaines de caractères en nombre si le tableau n'est pas vide
    $arrayProduitMagasins = !empty($arrayProduitMagasins) ? array_map('intval', $arrayProduitMagasins) : [];

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
                            Le nom du produit doit commencer par une maj. et ne doit contenir que des lettres, tirets et espaces
                        </div>';
        $validationError = true;
    }

    // si le produit n'a pas de marque
    if (empty($produitMarque)) {
        $produitMarqueErr = '<div class="alert alert-danger">
                Il faut la marque du produit.
            </div>';
        $validationError = true;
    } else if (!preg_match("/[A-Z][a-zäàâèêéïöôüç]+([- ]?[A-Z][a-zäàâèêéïöôüç]+)?/", $produitMarque)) {
        $produitMarqueErr = '<div class="alert alert-danger">
                             La marque du produit doit commencer par une maj. et ne doit contenir que des lettres, tirets et espaces
                        </div>';
        $validationError = true;
    }

    // si le produit n'a pas de quantité
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

    // si le produit n'a pas de magasin
    if (empty($arrayProduitMagasins)) {
        $produitMagasinsErr = '<div class="alert alert-danger">
                Il faut placer le produit dans un magasin min.
            </div>';
        $validationError = true;
    } else if (!$arrayProduitMagasins) {
        // si la personne donne pas un tableau
        $produitMagasinsErr = '<div class="alert alert-danger">
                             Il faut un tableau des identifiant de magasin.
                        </div>';
        $validationError = true;
    } else if (!$db -> magasinsExist($arrayProduitMagasins)) {
        $produitMagasinsErr = '<div class="alert alert-danger">
                             Un ou plusieurs magasins n\'existe pas
                        </div>';
        $validationError = true;
    }

    // si le produit existe déjà
    if ($db -> produitExist($produitNom, $produitMarque)){
        $produitExistErr = '<div class="alert alert-danger">
        Le produit existe déjà
        </div>';
        // la validation d'erreur n'a pas passé
        $validationError = true;
    }

    // si on a cliqué sur le bouton modifier
    if(filter_has_var(INPUT_POST, 'produit-modifie')){
        
        if (empty($produitId)){
            $produitIdErr = '<div class="alert alert-danger">
                Il faut le numéro d\'identifiant du produit.
            </div>';
            $validationError = true;
        } else if (!$produitId) {
            $produitIdErr = '<div class="alert alert-danger">
                Il faut que le numéro d\'identifiant du produit doit être plus grand ou égale à 1
            </div>';
            $validationError = true;
            //pour vérifier si l'id du produit existe déjà
        } else if ($db->produitIdExist($produitId)){
            $produitIdErr = '<div class="alert alert-danger">
                Le numéro d\'identifiant du produit n\'existe pas
            </div>';
            $validationError = true;
        }
    }
    

    // si la $validationError est true
    if (!$validationError) {
        // si dans le cas ou on ajoute un produit on utilise store produit et si dans le cas on modifie on utilise updateProduit
        if (filter_has_var(INPUT_POST, 'produit-cree')) {
            // stocker le produit dans la bd
            $ok = $db->storeProduit($produitNom, $produitMarque, $produitQuantite, $arrayProduitMagasins);
            // si stored dans DBmanager est false ce message d'erreur s'affiche
            if (!$ok) {
                die("Problème d'enregistrement du produit dans la base de données");
            } else {
                $successMsg = "<div class='alert alert-success'>
                                    Votre produit a été créé
                                </div>";
            }
        
            // dans le cas ou on met à jour
        } elseif (filter_has_var(INPUT_POST, 'produit-modifie')) {
            

            $ok = $db->updateProduit($produitId, $produitNom, $produitMarque, $produitQuantite, $arrayProduitMagasins);
            if (!$ok) {
                die("Problème d'enregistrement du produit dans la base de données");
            } else {
                $successMsg = "<div class='alert alert-success'>
                                    Votre produit a été modifié
                                </div>";

                // pour rafraichir la page
                 //header("Refresh:0");
            }
        }
        
        
    }
}
?>