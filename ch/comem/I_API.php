<?php

namespace ch\comem;

interface I_API {

    //connection entant qu'admin
    public function getAdminDatas($email, $mot_de_passe): array;

    // email existe
    public function emailExist($email): bool;

    // une fonction pour créer des admins
    public function storeAdmin($firstname, $lastname, $email, $mot_de_passe): bool;

    // MAGASIN
    // une fonction pour afficher des Magasins
    public function getMagasins(): array;

    // une fonction pour créer des magasins
    public function storeMagasin($nom, $adresse): bool;

    // une fonction pour vérifier si le magasin existe déjà
    public function magasinExist($nom, $adresse): bool;

    // une fonction pour vérifier si tous les magasins existe déjà
    public function magasinsExist($magasins): bool;

    // une fonction pour modifier des magasins
    public function updateMagasin($id, $nom, $adresse): bool;
    
    // une fonction pour modifiersupprimer des magasins 
    public function deleteMagasin($id): bool; 

    // PRODUIT
    // une fonction pour afficher des produits
    public function getProduits(): array;

    // une fonction pour créer des produits 
    public function storeProduit($nom, $marque, $nombre, $fk_magasin): bool;

    // une fonction pour vérifier si le produit existe déjà
    public function produitExist($nom, $marque): bool;

    // une fonction pour modifier des produits
    public function updateProduit($id, $nom, $marque, $nombre, $fk_magasin): bool;

    // une fonction pour supprimer des produits
    public function deleteProduit($id): bool;
    
}