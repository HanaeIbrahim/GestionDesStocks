<?php
    @session_start();
    if(isset($_SESSION["is_logged"])) { 
        header("Location: ./magasin.php");
    }
    // require once copie colle un fichier, et s'assure qu0'il n'exeste pas déjà ailleur
    require_once('./config/autoload.php');
    require_once('./controleurs/loginValidation.php');
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Login</title>
    </head>
    <body>
        <!-- action = " " permet d'envoyer sur elle meme -->
        <form action="" method="post">
            <h1>Gestion des Stocks</h1>
            <h2>Log in</h2>
            <?php echo $error_msg; ?>
            <!-- value = pour qu'il se rappel du mail -->
            <input type="email" name="email_signin" placeholder="email" value="<?php echo $email ?? "" ?>" />
            <input type="password" name="password_signin" placeholder="password" />
            <button type="submit" name="login">Connexion</button>
        </form>
                
    </body>
</html>