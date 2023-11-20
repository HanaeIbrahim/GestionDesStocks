<?php
@session_start();


// 

// <?= est un équivalent de <?php echo
?>

<aside>
    <!--pour afficher le prénom qui est logged à l'aide de session start  -->
    <p><?php echo $_SESSION['logged_user']['prenom'] ?> <?= $_SESSION['logged_user']['nom'] ?></p>
    <ul>
        <li><a href="magasin.php">Magasin</a></li>
        <li><a href="produit.php">Produit</a></li>
    </ul>
    <a href="logout.php">logout</a>

</aside>