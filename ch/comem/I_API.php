<?php

namespace ch\comem;

interface I_API {
    
    // une fonction pour créer des admins
    function storeAdmin($pseudo, $nom, $prenom, $email, $mot_de_passe): bool;

    // une fonction pour vérifier si l'email existe déjà
    public function emailExist($email): bool;

    //connection entant qu'admin
    public function getAdminDatas($email, $mot_de_passe): array;

    // pour récupérer les informations d'un utilisateur à partir de sa base de données.
    public function getAdminDatasBis($id_admin): array;


    // La fonction getEventDatas est utilisée pour récupérer les informations d'un événement à partir de la base de données
    public function getProductDatas($id): array;

    // une fonction pour créer des produits 
    public function storeProduct($nom, $marque, $nombre, $fk_magasin): bool;

    // une fonction pour vérifier si le produit existe déjà
    public function productExist($nom, $marque): bool;

    // une fonction pour afficher des produits
    public function getProducts(): array;

    // une fonction pour modifier des produits
    public function updateProduct($id, $nom, $marque, $nombre, $fk_magasin): bool;

    // une fonction pour supprimer des produits
    public function deleteProduct($id): bool;


    // une fonction pour créer des magasins
    public function storeMagasin($nom, $adresse): bool;

    // une fonction pour vérifier si le magasin existe déjà
    public function magasinExist($nom, $adresse): bool;

    // une fonction pour afficher des Magasins
    public function getMagasins(): array;

    // une fonction pour modifier des magasins
    public function updateMagasin($id, $nom, $adresse): bool;
    
    // une fonction pour modifiersupprimer des magasins 
    public function deleteMagasin($id): bool; 

    // une fonction pour ajouter des produits au Magasin
    public function addProductToMagasin($id, $nombre): bool;

    // une fonction pour l'envoie d'un mail au magasin pour prvenir du changement du stock
    public function sendMail($id, $nombre): bool;

    // une fonction pour afficher les produits d'un magasin
    public function getProductsByMagasin($id): array;

    
   

    

    
    

    
}