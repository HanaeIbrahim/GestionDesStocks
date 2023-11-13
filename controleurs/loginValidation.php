<?php

// grace à l'autoload
use ch\comem\DbManager;

// utiliser pour affcher les messages d'erreurs login.php
$error_msg = "";

// si on a cliqué sur le bouton login, verifier la variable login
if (filter_has_var(INPUT_POST, 'login')) {
    //filter input = vérifier le contenue qu'on recois
    $password = filter_input(INPUT_POST, 'password_signin', FILTER_UNSAFE_RAW);
    // format email en enlevant les caractères indésirables pour pas nous hacker
    $email = filter_input(INPUT_POST, 'email_signin', FILTER_SANITIZE_EMAIL);
    if (empty($email)) {
        $error_msg = "<div class='alert alert-danger email_alert'>
                            Il manque l'email.
                    </div>";
    } else if (empty($password)) {
        $error_msg = "<div class='alert alert-danger email_alert'>
                            Il manque le mot de passe.
                        </div>";
    } else if (!preg_match("/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,20}$/", $password)) {
        $error_msg = '<div class="alert alert-danger">
                        Le mot de passe doit avoir entre 8 et 20 caractères et contenir au moins : <br>- un caractère spécial,<br> - une minuscule,<br> - une majuscule,<br> - et un chiffre.
                    </div>';
    } else {
        $db = new DbManager();
        // row = retour du tableau email_ok, mot_de_pass_ok
        $row = $db->getAdminDatas($email, $password);
        $authentification_ok = $row['authentification_ok'];
        if (!$authentification_ok) {
            $error_msg = "<div class='alert alert-danger'>
                        le nom d'utilisateur ou le mot de passe ne sont pas correct.
                    </div>";
        } else {
            $id = $row['id'];
            $email = $row['email'];
            $nom = $row['nom'];
            $prenom = $row['prenom'];
            // variables des sessions, différence? $is_logged est dispo uniquement dans le fichier loginValidation.php
            // $_SESSION['is_logged'] est dispo dans tous les fichiers
            $is_logged = true;
            // permet de renvoyer sur la page de login si c'est pas true
            $_SESSION['is_logged'] = true;
            // permet de retenir le nom de user
            $_SESSION['logged_user'] = [
                'id' => $id,
                'email' => $email,
                'nom' => $nom,
                'prenom' => $prenom,
            ];
           
            // redirection vers la page de magasin
            header('Location: magasin.php');

        }
    }
}
?>