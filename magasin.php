<?php
    require_once './controleurs/_protect.php';
    require_once './config/autoload.php';
    require_once './controleurs/MagasinValidation.php';

    use ch\comem\DbManager;
    $db = new DbManager();
    // liste des magasins
    $magasins = $db->getMagasins();

    $ajout = filter_has_var(INPUT_GET, "ajout") ? filter_input(INPUT_GET, "ajout", FILTER_VALIDATE_BOOLEAN) : false;
    $modif = filter_has_var(INPUT_GET, "modif") ? filter_input(INPUT_GET, "modif", FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) : false;
    $sup = filter_has_var(INPUT_GET, "sup") ? filter_input(INPUT_GET, "sup", FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]) : false;
  
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Magasin</title>
</head>
<body>
    <?php
       require_once './aside.php';
    ?>
    <h1>Magasin <a href="?ajout=true">Ajouter</a></h1>
    <p><?php echo $successMsg ?? ""; ?></p>
    <form action="" method ="POST">

    <table>
        <thead>
            <tr>
                <th>nom</th>
                <th>Adresse</th>
                <th>Nombre de produits</th>
                <th>Modification</th>
                <th>suppression</th>
            </tr>
        </thead>
        <tbody>
        <?php 
            // rajouter une ligne 
            if ($ajout) : ?>

                <tr>
                    <td><input  type="text" name="magasin-nom" placeholder ="nom du magasin" value="<?php echo $magasinNom ?? "" ?>" /></td>
                    <td><input  type="text" name="magasin-adresse" placeholder ="adresse du magasin" value="<?php echo $magasinAdresse ?? "" ?>"/></td>
                    <td>Aucun</td>
                    <td colspan="2"><button type="submit" name="magasin-cree">Ajouter</button></td>
                </tr>

                <tr>
                    <td> <?php echo $magasinNomErr ?? ""; ?></td>
                    <td> <?php echo $magasinAdresseErr ?? ""; ?></td>
                    <td> <?php echo $magasinExistErr ?? ""; ?></td>
                    <td> <?php echo $magasinIdUpdateErr ?? ""; ?></td>
                </tr>
            <?php endif;
                    
             foreach ($magasins as $magasin): 

                
                if($modif && $modif === $magasin["id"]) :  ?>
                    <tr>
                        <td><input type="text" name="magasin-nom" placeholder ="nom du magasin" value="<?= $magasin["nom"]; ?>" /></td>
                        <td><input type="text" name="magasin-adresse" placeholder ="Adresse du magasin" value="<?= $magasin["adresse"]; ?>"/></td>
                        <td><?= $magasin["nombre"]; ?></td>
                        <td colspan="2"><button type="submit" name="magasin-modifie" value= "<?= $magasin["id"]; ?>">Terminé</button></td>
                    </tr>
                <?php elseif($sup && $sup === ($magasin["id"] + 1) && $magasinIdDeleteErr ?? "") :  ?>
                    <tr>
                        <td> <?php echo $magasinIdDeleteErr ?? ""; ?></td>
                    </tr>
                    <tr>
                        <td><?= $magasin["nom"]; ?></td>
                        <td><?= $magasin["adresse"]; ?></td>
                        <td><?= $magasin["nombre"]; ?></td>
                        <td><a href="?modif=<?= $magasin["id"]; ?>">Modification</a></td>
                        <td><a href="?sup=<?= $magasin["id"]; ?>">Supression</a></td>
                    </tr>
                <?php elseif($modif && $modif === ($magasin["id"] + 1)) :  ?>
                    <tr>
                        <td> <?php echo $magasinNomErr ?? ""; ?></td>
                        <td> <?php echo $magasinAdresseErr ?? ""; ?></td>
                        <td> <?php echo $magasinIdUpdateErr ?? ""; ?></td>
                        <td> <?php echo $magasinIdErr ?? ""; ?></td>
                        <td> <?php echo $magasinIdUpdateErr ?? ""; ?></td>
                    </tr>
                    <tr>
                        <td><?= $magasin["nom"]; ?></td>
                        <td><?= $magasin["adresse"]; ?></td>
                        <td><?= $magasin["nombre"]; ?></td>
                        <td><a href="?modif=<?= $magasin["id"]; ?>">Modification</a></td>
                        <td><a href="?sup=<?= $magasin["id"]; ?>">Supression</a></td>
                    </tr>
                <?php else :  ?>
                    <tr>
                        <td><?= $magasin["nom"]; ?></td>
                        <td><?= $magasin["adresse"]; ?></td>
                        <td><?= $magasin["nombre"]; ?></td>
                        <td><a href="?modif=<?= $magasin["id"]; ?>">Modification</a></td>
                        <td><a href="?sup=<?= $magasin["id"]; ?>">Supression</a></td>
                    </tr>
                <?php endif;
            endforeach; ?>
        </tbody>
    </table>
     </form>
</body>
</html>