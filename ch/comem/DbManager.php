<?php

namespace ch\comem;

class DbManager implements I_API {

    // connection pour la base de donnée dans les fonctions
    private $db;

    // constructeur
    public function __construct() {
        // récupère ce qu'il y a dans le dossier config, DIRECTORY_SEPARATOR pour Mac et Pc / 
        $config = parse_ini_file('config' . DIRECTORY_SEPARATOR . 'dbSqlite.ini', true);
        $dsn = $config['dsn'];
        $username = $config['username'];
        $password = $config['password'];
        // va nous permettre de se connecter sans utiliser Sqlite3
        // je change juste deSquilite.ini ou db.ini et PDO se charge du reste
        $this->db = new \PDO($dsn, $username, $password);
        if (!$this->db) {
            die("Problème de connexion à la base de données");
        }
    }

    // pour verifier si le mail existe déjà
    public function emailExist($email): bool {
        $sql = "SELECT count(*) From admin WHERE email = :email;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // une fonction pour créer des admins
    public function storeAdmin($prenom, $nom, $email, $mot_de_passe): bool {
        $stored = false;
        if (!empty($prenom) && !empty($nom) && !empty($email) && !empty($mot_de_passe)) {
            $now = date("Y-m-d H:i:s");
            $datas = [
                'prenom' => $prenom,
                'nom' => $nom,
                'email' => $email,
                'mot_de_passe' => $mot_de_passe
            ];
            // insérer à la colonne firstname etc 
            $sql = "INSERT INTO admin (prenom, nom, email, mot_de_passe) VALUES "
                    . "(:prenom, :nom, :email, :mot_de_passe)";
            $this->db->prepare($sql)->execute($datas);
            $stored = true;
        }
        return $stored;
}

     //connection entant qu'admin, array car il envoie email_ok et mot_de_passe_ok
     public function getAdminDatas($email, $mot_de_passe): array {
        $sql = "SELECT * From admin WHERE email = :email;";
        // on prèpare la requetepour pouvoir lui passer des paramètres
        $stmt = $this->db->prepare($sql);
        // récupérer les paramètres- eviter injection SQL pour pas hacker
        $stmt->bindParam('email', $email, \PDO::PARAM_STR);
        //executer la requete avec les paramètres
        $stmt->execute();
        // récupérer les résultats de la requete
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // si on recois rien
        //$donnees[0]["mot_de_passe" : le hach de ce mot de passe est comparé avec le hache de l'utilisateur $mot_de_passe
        if (!$donnees || !password_verify($mot_de_passe, $donnees[0]["mot_de_passe"])) {
            $donnees[0]["authentification_ok"] = false;
        } else {
            // si l'authenfication de l'email et le mot de passe sont corrects
            $donnees[0]["authentification_ok"] = true;
            // on supprime le mot de passe pour pas le renvoyer
            unset($donnees[0]["mot_de_passe"]);
            
        }
        return $donnees[0];
    }

     // une fonction pour vérifier si le magasin existe déjà
     public function magasinExistCreate($nom, $adresse): bool {
        $sql = "SELECT count(*) From magasin WHERE nom = :nom AND adresse = :adresse;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('nom', $nom, \PDO::PARAM_STR);
        $stmt->bindParam('adresse', $adresse, \PDO::PARAM_STR);
        $stmt->execute();
        // compter les nombres de colonnes qui nous a eté renvoyé
        return $stmt->fetchColumn() > 0;
    }

    // une fonction pour vérifier si le magasin existe déjà lorsqu'on met à jour 
    public function magasinExistUpdate($id, $nom, $adresse): bool {
        $sql = "SELECT count(*) From magasin WHERE nom = :nom AND adresse = :adresse AND id != :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('nom', $nom, \PDO::PARAM_STR);
        $stmt->bindParam('adresse', $adresse, \PDO::PARAM_STR);
        $stmt->bindParam('id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function magasinIdExist($id): bool {
        $sql = "SELECT count(*) From magasin WHERE id = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    public function magasinSansProduit($id): bool  {
        $sql = "SELECT count(*) From produit_dans_magasin WHERE fk_magasin = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() === 0;
    }

    public function magasinsExist($magasins): bool {
        $sql = "SELECT id From magasin;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        // ça nous retouren tous les id qui existes
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // compter les nombres de colonnes qui nous a eté renvoyé
        // Initialiser un tableau pour stocker les valeurs d'id
        $donneesIdArray = array();
        // Parcourir le tableau $donnees et extraire les valeurs d'id
        foreach ($donnees as $row) {
            $donneesIdArray[] = $row['id'];
        }
        //("tableau bdd", $donneesIdArray, "tableau recu", $magasins);
        $diff = array_diff($magasins, $donneesIdArray);
        return count($diff) === 0;
    }

    

    // une fonction pour afficher des Magasins
    public function getMagasins(): array {
        $sql = "SELECT magasin.nom, magasin.adresse, magasin.id, COUNT(produit_dans_magasin.fk_produit) AS nombre 
        FROM magasin
        LEFT JOIN produit_dans_magasin ON magasin.id = produit_dans_magasin.fk_magasin
        GROUP BY magasin.nom, magasin.adresse, magasin.id;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $donnees;
    }

    // une fonction pour modifier des magasins
    public function updateMagasin($id, $nom, $adresse): bool {
        $updated = false;
        // vérifie qu'il y aie des données la dedans
        if (!empty($id) && !empty($nom) && !empty($adresse)) {
            $datas = [
                'id' => $id,
                'nom' => $nom,
                'adresse' => $adresse,
            ];
            $sql = "UPDATE magasin SET nom = :nom, adresse = :adresse WHERE id = :id;";
            $this->db->prepare($sql)->execute($datas);
            $updated = true;
        }
        return $updated;
    }

    // une fonction pour supprimer des magasins
    public function deleteMagasin($id): bool {
        $deleted = false;
        if (!empty($id)) {
            $datas = [
                'id' => $id,
            ];
            $sql = "DELETE FROM magasin WHERE id = :id;";
            $this->db->prepare($sql)->execute($datas);
            $deleted = true;
        }
        return $deleted;
    }

     // une fonction pour créer des magasins
     public function storeMagasin($nom, $adresse): bool {
        // stored = est ce qu'il a été enregistré
        $stored = false;
        if (!empty($nom) && !empty($adresse)) {
            $datas = [
                'nom' => $nom,
                'adresse' => $adresse,
            ];
            $sql = "INSERT INTO magasin (nom, adresse) VALUES "
                    . "(:nom, :adresse)";
            $this->db->prepare($sql)->execute($datas);
            // true = magasin enregistré 
            $stored = true;
        }
        return $stored;
    }


    // une fonction pour créer des produits
    public function storeProduit($nom, $marque, $quantite, $array_fk_magasin): bool {
        $stored = false;
        // si les données ne sont pas vides
        if (!empty($nom) && !empty($marque) && !empty($quantite) && !empty($array_fk_magasin)) {
            $datas = [
                'nom' => $nom,
                'marque' => $marque,
                'quantite' => $quantite
            ];
            $sql = "INSERT INTO produit (nom, marque, quantite) VALUES "
                    . "(:nom, :marque, :quantite)";
            $this->db->prepare($sql)->execute($datas);
            
            $idProduit = $this->db->lastInsertId();
            // pour chaque magasin dans le tableau
            foreach ($array_fk_magasin as $fk_magasin) {
                $datas = [
                    'fk_magasin' => $fk_magasin,
                    'fk_produit' => $idProduit
                ];
                $sql = "INSERT INTO produit_dans_magasin (fk_magasin, fk_produit) VALUES "
                        . "(:fk_magasin, :fk_produit)";
                $this->db->prepare($sql)->execute($datas);
            }
            $stored = true;

        }
        return $stored;
    }


    // une fonction pour vérifier si le produit existe déjà
    public function produitExistCreate($nom, $marque): bool {
        $sql = "SELECT count(*) From produit WHERE nom = :nom AND marque = :marque;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('nom', $nom, \PDO::PARAM_STR);
        $stmt->bindParam('marque', $marque, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // une fonction pour vérifier si le produit existe déjà lorsqu'on met à jour 
    public function produitExistUpdate($id, $nom, $marque): bool {
        $sql = "SELECT count(*) From produit WHERE nom = :nom AND marque = :marque AND id != :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('nom', $nom, \PDO::PARAM_STR);
        $stmt->bindParam('marque', $marque, \PDO::PARAM_STR);
        $stmt->bindParam('id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // une fonction pour vérifier si l'id du produit existe déjà
    public function produitIdExist($id): bool {
        $sql = "SELECT count(*) From produit WHERE id = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('id', $id, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // une fonction pour afficher des produits
    public function getProduits(): array {
        // magasin_id= l'id de magasin
        $sql = "SELECT produit.id, produit.nom, produit.marque, produit.quantite, GROUP_CONCAT(magasin.nom, ', ') AS 'magasins'
        FROM produit_dans_magasin
        INNER JOIN produit ON produit.id = fk_produit
        INNER JOIN magasin ON magasin.id = fk_magasin
        GROUP BY produit.id ORDER BY produit.id DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $donnees;
    }

    // une fonction pour modifier des produits
    public function updateProduit($id, $nom, $marque, $quantite, $array_fk_magasin): bool {
        $updated = false;
        if (!empty($id) && !empty($nom) && !empty($marque) && !empty($quantite) && !empty($array_fk_magasin)) {
            $datas = [
                'id' => $id,
                'nom' => $nom,
                'marque' => $marque,
                'quantite' => $quantite
            ];
            $sql = "UPDATE produit SET nom = :nom, marque = :marque, quantite = :quantite WHERE id = :id;";
            $produitModifie = $this->db->prepare($sql)->execute($datas);
            
            // Suppression des anciennes associations avec les magasins
            $sqlDelete = "DELETE FROM produit_dans_magasin WHERE fk_produit = :id;";
            $this->db->prepare($sqlDelete)->execute(['id' => $id]);

            // Ajout des nouvelles associations avec les magasins
            foreach ($array_fk_magasin as $fk_magasin) {
                $sqlInsert = "INSERT INTO produit_dans_magasin (fk_magasin, fk_produit) VALUES (:fk_magasin, :fk_produit);";
                $this->db->prepare($sqlInsert)->execute(['fk_magasin' => $fk_magasin, 'fk_produit' => $id]);
            }
            $updated = true;

        }
        return $updated;
    }

    // une fonction pour supprimer des produits
    public function deleteProduit($id): bool {
        $deleted = false;
        if (!empty($id)) {
            $datas = [
                'id' => $id,
            ];
            $sql = "DELETE FROM produit WHERE id = :id;";
            $this->db->prepare($sql)->execute($datas);
            $sqltableinter = "DELETE FROM produit_dans_magasin WHERE fk_produit = :id;";
            $this->db->prepare($sqltableinter)->execute($datas);
            $deleted = true;
        }
        return $deleted;
    }










}
