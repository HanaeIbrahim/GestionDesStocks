<?php

require_once('./config/autoload.php');

use ch\comem\DbManager;

$validationError = false;


// INPUT_POST car dans la page magasin.php on utilise la méthode post dans le formulaire
if (filter_has_var(INPUT_POST, 'magasin-cree') || filter_has_var(INPUT_POST, 'magasin-modifie')) {
    // gérer les erreurs venant de la création du magasin
    $magasinIdUpdateErr  = $magasinExistErr = $magasinNomErr = $magasinAdresseErr  = $successMsg = "";
    
    $magasinIdUpdate = filter_input(INPUT_POST, 'magasin-modifie', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
    $magasinNom = filter_input(INPUT_POST, 'magasin-nom', FILTER_UNSAFE_RAW);
    $magasinAdresse = filter_input(INPUT_POST, 'magasin-adresse', FILTER_UNSAFE_RAW);
   
    
    // tant que la validation erreur est true on ne créer pas l'utilisateur
    if (empty($magasinNom)) {
        $magasinNomErr  = '<div class="alert alert-danger">
                    Il faut le nom du magasin.
                </div>';
        $validationError = true;
        //expression regulière ou regex, on peut écrire tout en maj et ajouter des trait d'unions n'importe ou sauf au début
    } else if (!preg_match("/^[A-Z][a-zA-Zäàâèêéïöôüç -]+$/", $magasinNom)) {
        $magasinNomErr = '<div class="alert alert-danger">
                            Le nom du magasin doit commencer par une maj. et ne doit contenir que des lettres, tirets et espaces
                        </div>';
        $validationError = true;
    }

    // si le magasin n'a pas de adresse
    if (empty($magasinAdresse)) {
        $magasinAdresseErr = '<div class="alert alert-danger">
                Il faut la adresse du magasin.
            </div>';
        $validationError = true;
    } else if (!$magasinAdresse) {
        $magasinAdresseErr = '<div class="alert alert-danger">
                             L\'adresse du magasin est incorrect
                        </div>';
        $validationError = true;
    }

   
    
    // on se connect à la base de de donnée lance la fonction constructeur de la db manager
    $db = new DbManager();


    // vérification de créer si le magasin existe déjà
    if(filter_has_var(INPUT_POST, 'magasin-cree')){
        // si le magasin existe déjà
        if ($db -> magasinExistCreate($magasinNom, $magasinAdresse)){
            $magasinExistErr = '<div class="alert alert-danger">
            Le magasin existe déjà
            </div>';
            // la validation d'erreur n'a pas passé
            $validationError = true;
        }
    }
    // si on a cliqué sur le bouton modifier
    if(filter_has_var(INPUT_POST, 'magasin-modifie')){
        
        if (empty($magasinIdUpdate)){
            $magasinIdUpdateErr = '<div class="alert alert-danger">
                Il faut le numéro d\'identifiant du magasin.
            </div>';
            $validationError = true;
        } else if (!$magasinIdUpdate) {
            $magasinIdUpdateErr = '<div class="alert alert-danger">
                Il faut que le numéro d\'identifiant du magasin doit être plus grand ou égale à 1
            </div>';
            $validationError = true;
            //pour vérifier si l'id du magasin existe déjà
        } else if (!$db->magasinIdExist($magasinIdUpdate)){
            $magasinIdUpdateErr = '<div class="alert alert-danger">
                Le numéro d\'identifiant du magasin n\'existe pas
            </div>';
            $validationError = true;
        } else if ($db->magasinExistUpdate($magasinIdUpdate, $magasinNom, $magasinAdresse)){
            // le vérification si le magasin mise à jour n'existe pas
            $magasinIdUpdateErr = '<div class="alert alert-danger">
               le magasin existe déjà
            </div>';
            $validationError = true;
        }
    } 

    // si la $validationError est true
    if (!$validationError) {

        // si dans le cas ou on ajoute un magasin on utilise store magasin et si dans le cas on modifie on utilise updatemagasin
        if (filter_has_var(INPUT_POST, 'magasin-cree')) {
            // stocker le magasin dans la bd
            $ok = $db->storeMagasin($magasinNom, $magasinAdresse);
            // si stored dans DBmanager est false ce message d'erreur s'affiche
            if (!$ok) {
                die("Problème d'enregistrement du magasin dans la base de données");
            } else {
                $successMsg = "<div class='alert alert-success'>
                                    Votre magasin a été créé
                                </div>";
                // reinitialisation des input
                $magasinNom = $magasinAdresse = "";
        

                // Envoie d'un mail à l'administrateur
                

            }
        
            // dans le cas ou on met à jour
        } elseif (filter_has_var(INPUT_POST, 'magasin-modifie')) {
            $ok = $db->updateMagasin($magasinIdUpdate, $magasinNom, $magasinAdresse);
            if (!$ok) {
                die("Problème d'enregistrement du magasin dans la base de données");
            } else {

                // pour rafraichir la page
                header("Location: magasin.php");
            }
        }
    }

    
} elseif (filter_has_var(INPUT_GET, 'sup')) {
    $magasinIdDeleteErr = $successMsg = "";
    // récupérer l'id qu'on veut supprimer
    $magasinIdDelete = filter_input(INPUT_GET, 'sup', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);

    // on se connect à la base de de donnée lance la fonction constructeur de la db manager
    $db = new DbManager();

    if (empty($magasinIdDelete)){
        $magasinIdDeleteErr = '<div class="alert alert-danger">
            Il faut le numéro d\'identifiant du magasin.
        </div>';
        $validationError = true;
    } else if (!$magasinIdDelete) {
        $magasinIdDeleteErr = '<div class="alert alert-danger">
            Il faut que le numéro d\'identifiant du magasin doit être plus grand ou égale à 1
        </div>';
        $validationError = true;
        //pour vérifier si l'id du magasin existe déjà
    } else if (!$db->magasinIdExist($magasinIdDelete)){
        $magasinIdDeleteErr = '<div class="alert alert-danger">
            Le numéro d\'identifiant du magasin n\'existe pas
        </div>';
        $validationError = true;
        // une fonction qui vérifie que le magasin ne contient plus de produit
    } else if (!$db->magasinSansProduit($magasinIdDelete)) {
        $magasinIdDeleteErr = '<div class="alert alert-danger">
            Le magasin contient encore des produits
        </div>';
        $validationError = true;
    }

    // si la $validationError est true
    if (!$validationError) {
        // stocker le magasin dans la bd
        $ok = $db->deleteMagasin($magasinIdDelete);
        if (!$ok) {
        //si il trouve pas l'id qui va supprimé
            die("Problème de suppression du magasin dans la base de données");
        } else {
            $successMsg = "<div class='alert alert-success'>
                                Votre magasin a été supprimé
                            </div>";
    
        }
    }
    
}

?>