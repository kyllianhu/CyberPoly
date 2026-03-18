<?php
define('DB_HOST',   'localhost');
define('DB_NAME',   'monop');
define('DB_USER',   '');
define('DB_PASS',   '');
define('DB_DRIVER', 'pgsql');

function connexPDO() {
    $dsn = DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME;

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);
        return $pdo;
    } catch (PDOException $e) {
        die('Erreur de connexion BD : ' . $e->getMessage());
    }
}