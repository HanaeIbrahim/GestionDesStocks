<?php
    require_once './controleurs/_protect.php';
    require_once './config/autoload.php';

    use ch\comem\DbManager;
    $db = new DbManager();
    // liste des magasins
    $magasins = $db->getMagasins();
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
            <?php foreach ($magasins as $magasin): ?>
                <tr>
                    <th><?= $magasin["nom"]; ?></th>
                    <td><?= $magasin["adresse"]; ?></td>
                    <td><?= $magasin["nombre"]; ?></td>
                    <td><a href="?modif=<?= $magasin["id"]; ?>">Modification</a></td>
                    <td><a href="?sup=<?= $magasin["id"]; ?>">Supression</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>