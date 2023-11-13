<?php     
    @session_start();
    // is logged est bien créee, si is loged = true
    if (isset($_SESSION["is_logged"]) && $_SESSION["is_logged"]) { 
        // on détruit la session
        session_destroy();
    }
    header("Location:index.php");