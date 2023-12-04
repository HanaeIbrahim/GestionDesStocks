<?php
    require_once './controleurs/_protect.php';
    require_once './config/autoload.php';
    require_once './controleurs/ProduitValidation.php';

    use ch\comem\DbManager;
    $db = new DbManager();
    // liste des produits et magasins
    $produits = $db->getProduits();
    $magasins = $db->getMagasins();

// quel méhode on va récupérer ajout et il faut qu'on recoit un booléan, pour éviter les injections
$ajout = filter_has_var(INPUT_GET, "ajout") ? filter_input(INPUT_GET, "ajout", FILTER_VALIDATE_BOOLEAN) : false;
$modif = filter_has_var(INPUT_GET, "modif") ? filter_input(INPUT_GET, "modif", FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) : false;
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
    <p><?php echo $successMsg ?? ""; ?></p>
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
                    <td><input  type="text" name="produit-nom" placeholder ="nom du produit" value="<?php echo $produitNom ?? "" ?>" /></td>
                    <td><input  type="text" name="produit-marque" placeholder ="marque du produit" value="<?php echo $produitMarque ?? "" ?>"/></td>
                    <td><input  type="number" name="produit-quantite" placeholder ="quantité du produit" value="<?php echo $produitQuantite ?? "" ?>"/></td>
                    <td>
                    <?php foreach ($magasins as $magasin): ?>
                        <label>
                            <input type="checkbox" name="produit-magasins[]" value="<?= $magasin["id"]; ?>" <?php if(in_array($magasin["id"], $arrayProduitMagasins ?? [])){echo "checked";}  ?>>
                            <?= $magasin["nom"]; ?>
                        </label>
                        
                    <?php endforeach; ?>
                    </td>
                    <td colspan="2"><button type="submit" name="produit-cree">Terminé</button></td>
                
                </tr>
                <tr>
                    <td> <?php echo $produitNomErr ?? ""; ?></td>
                    <td> <?php echo $produitMarqueErr ?? ""; ?></td>
                    <td> <?php echo $produitQuantiteErr ?? ""; ?></td>
                    <td> <?php echo $produitMagasinsErr ?? ""; ?></td>
                </tr>
            <?php endif; ?>
            <?php foreach ($produits as $produit): ?>
                <tr>
                    <?php if($modif && $modif === $produit["id"]) :  ?>
                        <td><input type="text" name="produit-nom" placeholder ="nom du produit" value="<?= $produit["nom"]; ?>" /></td>
                        <td><input type="text" name="produit-marque" placeholder ="marque du produit" value="<?= $produit["marque"]; ?>"/></td>
                        <td><input type="number" name="produit-quantite" placeholder ="quantité du produit" value="<?= $produit["quantite"];  ?>"/></td>
                        <td>
                        <?php foreach ($magasins as $magasin): ?>
                            <label>
                                <input type="checkbox" name="produit-magasins[]" value="<?= $magasin["id"]; ?>" <?php if(in_array($magasin["nom"], explode(", ", $produit["magasins"]))){echo "checked";}  ?>>
                                <?= $magasin["nom"]; ?>
                            </label>
                            
                        <?php endforeach; ?>
                        </td>
                        <td colspan="2"><button type="submit" name="produit-modifie" value= "<?= $produit["id"]; ?>">Terminé</button></td>
                    <?php elseif($modif && $modif === ($produit["id"] + 1)) :  ?>
                        <td> <?php echo $produitNomErr ?? ""; ?></td>
                        <td> <?php echo $produitMarqueErr ?? ""; ?></td>
                        <td> <?php echo $produitQuantiteErr ?? ""; ?></td>
                        <td> <?php echo $produitMagasinsErr ?? ""; ?></td>
                    <?php else :  ?>
                        <th><?= $produit["nom"]; ?></th>
                        <td><?= $produit["marque"]; ?></td>
                        <td><?= $produit["quantite"]; ?></td>
                        <td><?= $produit["magasins"]; ?></td>
                        <td><a href="?modif=<?= $produit["id"]; ?>">Modification</a></td>
                        <td><a href="?sup=<?= $produit["id"]; ?>">Supression</a></td>
                    <?php endif;  ?>
                </tr>
            <?php endforeach; ?>
            </form>
        </tbody>
    </table>
</body>
</html>