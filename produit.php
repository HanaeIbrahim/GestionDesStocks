<?php
    require_once './controleurs/_protect.php';
    require_once './config/autoload.php';
    require_once './controleurs/ProduitValidation.php';

    use ch\comem\DbManager;
    $db = new DbManager();
    // liste des produits et magasins
    $produits = $db->getProduits();
    $magasins = $db->getMagasins();

// quel méthode on va récupérer ajout et il faut qu'on recoit un booléan, pour éviter les injections
// pour changer l'affichage
$ajout = filter_has_var(INPUT_GET, "ajout") ? filter_input(INPUT_GET, "ajout", FILTER_VALIDATE_BOOLEAN) : false;
$modif = filter_has_var(INPUT_GET, "modif") ? filter_input(INPUT_GET, "modif", FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) : false;
$sup = filter_has_var(INPUT_GET, "sup") ? filter_input(INPUT_GET, "sup", FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) : false;
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
    <form action="" method ="POST">
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
                    <td colspan="2"><button type="submit" name="produit-cree">Ajouter</button></td>
                
                </tr>
                <tr>
                    <td> <?php echo $produitNomErr ?? ""; ?></td>
                    <td> <?php echo $produitMarqueErr ?? ""; ?></td>
                    <td> <?php echo $produitQuantiteErr ?? ""; ?></td>
                    <td> <?php echo $produitMagasinsErr ?? ""; ?></td>
                    <td> <?php echo $produitIdUpdateErr ?? ""; ?></td>
                </tr>
            <?php endif;
           foreach ($produits as $produit): 
                
                     if($modif && $modif === $produit["id"]) :  ?>
                        <tr>
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
                        </tr>

                    <?php elseif($sup && $sup === ($produit["id"] + 1) && $produitIdDeleteErr ?? "") :  ?>
                        <tr>
                            <td> <?php echo $produitIdDeleteErr ?? ""; ?></td>
                        </tr>
                        <!--on rajoute une ligne d'erreur si la suppression du poduit n'a pas marché -->
                         <!--la ligne suivante-->
                        <tr>
                            <th><?= $produit["nom"]; ?></th>
                            <td><?= $produit["marque"]; ?></td>
                            <td><?= $produit["quantite"]; ?></td>
                            <td><?= $produit["magasins"]; ?></td>
                            <td><a href="?modif=<?= $produit["id"]; ?>">Modification</a></td>
                            <td><a href="?sup=<?= $produit["id"]; ?>">Supression</a></td>
                        </tr>
                    <?php elseif($modif && $modif === ($produit["id"] + 1)) :  ?>
                        <tr>
                            <td> <?php echo $produitNomErr ?? ""; ?></td>
                            <td> <?php echo $produitMarqueErr ?? ""; ?></td>
                            <td> <?php echo $produitQuantiteErr ?? ""; ?></td>
                            <td> <?php echo $produitMagasinsErr ?? ""; ?></td>
                            <td> <?php echo $produitIdUpdateErr ?? ""; ?></td>
                        </tr>
                        <!--on rajoute une ligne d'erreur si la modification du poduit n'a pas marché -->
                        <tr>
                            <th><?= $produit["nom"]; ?></th>
                            <td><?= $produit["marque"]; ?></td>
                            <td><?= $produit["quantite"]; ?></td>
                            <td><?= $produit["magasins"]; ?></td>
                            <td><a href="?modif=<?= $produit["id"]; ?>">Modification</a></td>
                            <td><a href="?sup=<?= $produit["id"]; ?>">Supression</a></td>
                        </tr>
                    <?php else :  ?>
                        <tr>
                            <th><?= $produit["nom"]; ?></th>
                            <td><?= $produit["marque"]; ?></td>
                            <td><?= $produit["quantite"]; ?></td>
                            <td><?= $produit["magasins"]; ?></td>
                            <td><a href="?modif=<?= $produit["id"]; ?>">Modification</a></td>
                            <td><a href="?sup=<?= $produit["id"]; ?>">Supression</a></td>
                        </tr>
                    <?php endif;
            endforeach; ?>
            
        </tbody>
    </table>
    </form>
</body>
</html>