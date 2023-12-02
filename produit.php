<?php
    require_once './controleurs/_protect.php';
    require_once './config/autoload.php';

    use ch\comem\DbManager;
    $db = new DbManager();
    // liste des produits et magasins
    $produits = $db->getProduits();
    $magasins = $db->getMagasins();

// quel méhode on va récupérer ajout et il faut qu'on recoit un booléan, pour éviter les injections
    $ajout = filter_has_var(INPUT_GET, "ajout") ? filter_input(INPUT_GET, "ajout", FILTER_VALIDATE_BOOLEAN) : false;
    // l'équivalant avec if else, si on a ajout dans l'url on le filtre sinon on fait rien
    // if (filter_has_var(INPUT_GET, "ajout")) {
    //     $ajout = filter_input(INPUT_GET, "ajout", FILTER_VALIDATE_BOOLEAN)
    // } else {
    //     $ajout = false
    // }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produit</title>
</head>
<body>
    <?php
       require_once './aside.php';
    ?>
    <h1>Produits <a href="?ajout=true">Ajouter</a></h1>
    
    <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Marque</th>
                <th>Quantité</th>
                <th>Magasins</th>
                <th colspan="2">Actions</th>
            </tr>
        </thead>
        <tbody>
            <form action="" method ="POST">
            <?php 
            // rajouter une ligne 
            if ($ajout) : ?>

                <tr>
                <td><input  type="text" name="produit-nom" placeholder ="nom du produit"/></td>
                <td><input  type="text" name="produit-marque" placeholder ="marque du produit"/></td>
                <td><input  type="number" name="produit-quantite" placeholder ="quantité du produit"/></td>
                <td>
                <?php foreach ($magasins as $magasin): ?>
                    <label>
                        <input type="checkbox" name="produit-magasins[]" value="<?= $magasin["id"]; ?>">
                        <?= $magasin["nom"]; ?>
                    </label>
                    
                <?php endforeach; ?>
                </td>
                <td colspan="2"><button type="submit" name="produit-cree">Terminé</button></td>
                
                </tr>
            <?php endif; ?>
            <?php foreach ($produits as $produit): ?>
                <tr>
                    <th><?= $produit["nom"]; ?></th>
                    <td><?= $produit["marque"]; ?></td>
                    <td><?= $produit["quantite"]; ?></td>
                    <td><?= $produit["magasins"]; ?></td>
                    <td><a href="?modif=<?= $produit["id"]; ?>">Modification</a></td>
                    <td><a href="?sup=<?= $produit["id"]; ?>">Supression</a></td>
                </tr>
            <?php endforeach; ?>
            </form>
        </tbody>
    </table>
</body>
</html>