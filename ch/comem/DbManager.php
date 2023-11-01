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

    
}
