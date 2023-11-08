<?php

namespace ch\comem;

class DbManager implements I_API {

    // connection pour la base de donnée dans les fonctions
    private $db;

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

    // une fonction pour créer des admins
    public function storeAdmin($pseudo, $nom, $prenom, $email, $mot_de_passe): bool {
        $stored = false;
        if (!empty($pseudo) && !empty($nom) && !empty($prenom) && !empty($email) && !empty($mot_de_passe)) {
            
            $datas = [
                'pseudo' => $pseudo,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'mot_de_passe' => $mot_de_passe,
                
            ];
            $sql = "INSERT INTO users (pseudo, nom, prenom, email, mot_de_passe) VALUES "
                    . "(:pseudo, :nom, :prenom, :email, :mot_de_passe)";
            $this->db->prepare($sql)->execute($datas);
            $stored = true;
        }
        return $stored;
    }

    // une fonction pour vérifier si l'email existe déjà
    public function emailExist($email): bool {
        $sql = "SELECT count(*) From admin WHERE email = :email;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
    
     //connection entant qu'admin
     public function getAdminDatas($email, $mot_de_passe): array {
        $sql = "SELECT * From admin WHERE email = :email;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (!$donnees) {
            $donnees[0]["email_ok"] = false;
        } else {
            if (!password_verify($$mot_de_passe, $donnees[0]["mot_de_passe"])) {
                unset($donnees[0]);
                $donnees[0]["email_ok"] = true;
                $donnees[0]["mot_de_passe_ok"] = false;
            } else {
                $donnees[0]["email_ok"] = true;
                $donnees[0]["mot_de_passe_ok"] = true;
                unset($donnees[0]["pmot_de_passe"]);
            }
        }
        return $donnees[0];
    }

    // pour récupérer les informations d'un utilisateur à partir de sa base de données. 
    public function getAdminDatasBis($id_admin): array {
        $sql = "SELECT id, pseudo, nom, prenom, email From admin WHERE id = :id_admin;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('id_admin', $id_admin, \PDO::PARAM_STR);
        $stmt->execute();
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if (!$donnees) {
            $donnees[0]["id"] = -1;
        }
        return $donnees[0];
    }

    //La fonction getEventDatas est utilisée pour récupérer les informations d'un événement à partir de la base de données
    public function getProductDatas($id): array{
            $sql = "SELECT * From produit WHERE id = :id;";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam('id', $id, \PDO::PARAM_STR);
            $stmt->execute();
            $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!$donnees) {
                $donnees[0]["id"] = -1;
            }
            return $donnees[0];
    }

    // une fonction pour créer des produits
    public function storeProduct($nom, $marque, $nombre, $fk_magasin): bool {
        $stored = false;
        if (!empty($nom) && !empty($marque) && !empty($nombre) && !empty($fk_magasin)) {
            $datas = [
                'nom' => $nom,
                'marque' => $marque,
                'nombre' => $nombre,
                'fk_magasin' => $fk_magasin,
            ];
            $sql = "INSERT INTO produit (nom, marque, nombre, fk_magasin) VALUES "
                    . "(:nom, :marque, :nombre, :fk_magasin)";
            $this->db->prepare($sql)->execute($datas);
            $stored = true;
        }
        return $stored;
    }


    // une fonction pour vérifier si le produit existe déjà
    public function productExist($nom, $marque): bool {
        $sql = "SELECT count(*) From produit WHERE nom = :nom AND marque = :marque;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('nom', $nom, \PDO::PARAM_STR);
        $stmt->bindParam('marque', $marque, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // une fonction pour afficher des produits
    public function getProducts(): array {
        $sql = "SELECT * From produit;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $donnees;
    }

    // une fonction pour modifier des produits
    public function updateProduct($id, $nom, $marque, $nombre, $fk_magasin): bool {
        $updated = false;
        if (!empty($id) && !empty($nom) && !empty($marque) && !empty($nombre) && !empty($fk_magasin)) {
            $datas = [
                'id' => $id,
                'nom' => $nom,
                'marque' => $marque,
                'nombre' => $nombre,
                'fk_magasin' => $fk_magasin,
            ];
            $sql = "UPDATE produit SET nom = :nom, marque = :marque, nombre = :nombre, fk_magasin = :fk_magasin WHERE id = :id;";
            $this->db->prepare($sql)->execute($datas);
            $updated = true;
        }
        return $updated;
    }

    // une fonction pour supprimer des produits
    public function deleteProduct($id): bool {
        $deleted = false;
        if (!empty($id)) {
            $datas = [
                'id' => $id,
            ];
            $sql = "DELETE FROM produit WHERE id = :id;";
            $this->db->prepare($sql)->execute($datas);
            $deleted = true;
        }
        return $deleted;
    }

    // une fonction pour créer des magasins
    public function storeMagasin($nom, $adresse): bool {
        $stored = false;
        if (!empty($nom) && !empty($adresse)) {
            $datas = [
                'nom' => $nom,
                'adresse' => $adresse,
            ];
            $sql = "INSERT INTO magasin (nom, adresse) VALUES "
                    . "(:nom, :adresse)";
            $this->db->prepare($sql)->execute($datas);
            $stored = true;
        }
        return $stored;
    }

    // une fonction pour vérifier si le magasin existe déjà
    public function magasinExist($nom, $adresse): bool {
        $sql = "SELECT count(*) From magasin WHERE nom = :nom AND adresse = :adresse;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('nom', $nom, \PDO::PARAM_STR);
        $stmt->bindParam('adresse', $adresse, \PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }

    // une fonction pour afficher des Magasins
    public function getMagasins(): array {
        $sql = "SELECT * From magasin;";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $donnees;
    }

    // une fonction pour modifier des magasins
    public function updateMagasin($id, $nom, $adresse): bool {
        $updated = false;
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

    // une fonction pour ajouter des produits au Magasin
    public function addProductToMagasin($id, $nombre): bool {
        $updated = false;
        if (!empty($id) && !empty($nombre)) {
            $datas = [
                'id' => $id,
                'nombre' => $nombre,
            ];
            $sql = "UPDATE produit SET nombre = nombre + :nombre WHERE id = :id;";
            $this->db->prepare($sql)->execute($datas);
            $updated = true;
        }
        return $updated;
    }

    // une fonction pour l'envoie d'un mail au magasin pour prvenir du changement du stock
    public function sendMail($id, $nombre): bool {
        $updated = false;
        if (!empty($id) && !empty($nombre)) {
            $datas = [
                'id' => $id,
                'nombre' => $nombre,
            ];
            $sql = "UPDATE produit SET nombre = nombre + :nombre WHERE id = :id;";
            $this->db->prepare($sql)->execute($datas);
            $updated = true;
        }
        return $updated;
    }

    // une fonction pour afficher les produits d'un magasin
    public function getProductsByMagasin($id): array {
        $sql = "SELECT * From produit WHERE fk_magasin = :id;";
        $stmt = $this->db->prepare($sql);
        $stmt->bindParam('id', $id, \PDO::PARAM_STR);
        $stmt->execute();
        $donnees = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $donnees;
    }

    

    
    
        
    










}
