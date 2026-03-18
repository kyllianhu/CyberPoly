<?php
require_once BASE_PATH . '/config/myParam.inc.php';

class Database
{
    private static $instance = null;

    private function __construct() {}
    private function __clone() {}

    // Retourne l'unique instance PDO, la crée via connexPDO() si absente
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = connexPDO();
        }
        return self::$instance;
    }
}
