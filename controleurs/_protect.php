<?php
//pour protéger une page
// session_start : permet d'accéder au monde les variables des séssion
//@ permet de cacher les erreurs de connexion, si l'utilisateur a déjà fait une session
    @session_start();
    if(!isset($_SESSION["is_logged"])) { 
        header("Location: ./login.php");
    }