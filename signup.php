<?php
@session_start();
require_once('./controleurs/signupValidation.php');
?>
<!doctype html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Sign Up</title>
    </head>
    <body>
        <div class="App">
            <div class="vertical-center">
                <div class="inner-block">
                    <!-- si le message de succès est true, on affiche le message de succès et le bouton de connexion -->
                    <?php if ($successMsg) : ?>
                        <?php echo $successMsg; ?>
                        <div class="container">
                            <div class="jumbotron text-center">
                                <p class="lead">Cliquez sur le bouton suivant pour vous authentifier</p>
                                <?php
                                $url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
                                $url .= "://" . $_SERVER['HTTP_HOST'];
                                $url .= str_replace(basename($_SERVER['SCRIPT_NAME']), "", $_SERVER['SCRIPT_NAME']);
                                ?>
                            </div>
                        </div>
                    <?php else : ?>
                        <form action="" method="post">
                            <h3>Infos nouveau compte</h3>
                            <?php echo $emailValidationErr; ?>
                            <div class="form-group">
                                <label for="firstName">Prénom</label>
                                <input type="text" class="form-control" name="firstname" id="firstName" value="<?php echo $firstname ?? "" ?>" />
                                <?php echo $firstnameErr; ?>
                            </div>
                            <div class="form-group">
                                <label for="lastName">Nom</label>
                                <input type="text" class="form-control" name="lastname" id="lastName" value="<?php echo $lastname ?? "" ?>" />
                                <?php echo $lastnameErr; ?>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" name="email" id="email" value="<?php echo $email ?? "" ?>" />
                                <?php echo $emailErr; ?>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" name="password" id="password" />
                                <?php echo $passwordErr; ?>
                            </div>
                            <button type="submit" name="submit" id="submit" class="btn btn-outline-primary btn-lg btn-block">Crée compte</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <a href="login.php">Connexion</a>
    </body>
</html>