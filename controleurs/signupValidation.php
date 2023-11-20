<?php

require_once('./config/autoload.php');

use ch\comem\DbManager;

$firstnameErr = $lastnameErr = $emailErr = $passwordErr = $emailValidationErr = $successMsg = "";

if (filter_has_var(INPUT_POST, 'submit')) {

    $firstname = filter_input(INPUT_POST, 'firstname', FILTER_UNSAFE_RAW);
    $lastname = filter_input(INPUT_POST, 'lastname', FILTER_UNSAFE_RAW);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW);
    $validationError = false;
    
    // tant que la validation erreur est true on ne créer pas l'utilisateur
    if (empty($firstname)) {
        $firstnameErr = '<div class="alert alert-danger">
                    Il faut un prénom.
                </div>';
        $validationError = true;
        //expression regulière ou regex
    } else if (!preg_match("/[A-Z][a-zäàâèêéïöôüç]+([- ]?[A-Z][a-zäàâèêéïöôüç]+)?/", $firstname)) {
        $firstnameErr = '<div class="alert alert-danger">
                            Le prénom ne doit contenir que des lettres, tirets et espaces
                        </div>';
        $validationError = true;
    }

    if (empty($lastname)) {
        $lastnameErr = '<div class="alert alert-danger">
                Il faut un nom.
            </div>';
        $validationError = true;
    } else if (!preg_match("/[A-Z][a-zäàâèêéïöôüç]+([- ]?[A-Z][a-zäàâèêéïöôüç]+)?/", $lastname)) {
        $lastnameErr = '<div class="alert alert-danger">
                            Le nom ne doit contenir que des lettres, tirets et espaces
                        </div>';
        $validationError = true;
    }

    // on se connect à la base de de donnée lance la fonction constructeur de la db manager
    $db = new DbManager();
    if (!$email) {
        $emailErr = "<div class='alert alert-danger'>
                            L'email doit être valide !
                        </div>";
        $validationError = true;
    } else if ($db->emailExist($email)) {
        $emailErr = "<div class='alert alert-danger'>
                            Cet email est déjà utilisé !
                        </div>";
        $validationError = true;
    }

    if (empty($password)) {
        $passwordErr = '<div class="alert alert-danger">
            Il faut un mot de passe.
        </div>';
        $validationError = true;
    } else if (!preg_match("/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/", $password)) {
        $passwordErr = '<div class="alert alert-danger">
                        Le mot de passe doit avoir entre 8 et 20 caractères et contenir au moins : <br>- un caractère spécial,<br> - une minuscule,<br> - une majuscule,<br> - et un chiffre.
                    </div>';
        $validationError = true;
    }

    // si la $validationError est true
    if (!$validationError) {
    // on hache le mot de passe pour que qln qui a ma base de donnée n'aille pas toutes les mots de passes
    // passpwrd hash transforme le mot de passe de user en hache
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        // stocker l'admin dans la bd
        $ok = $db->storeAdmin($firstname, $lastname, $email, $password_hash);

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