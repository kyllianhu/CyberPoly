<?php
/**
 * ╔══════════════════════════════════════════════════════╗
 * ║  CyberSafe Monopoly — Front Controller (Routeur)    ║
 * ║  Unique point d'entrée de l'application.             ║
 * ╚══════════════════════════════════════════════════════╝
 *
 * URL d'accès : http://localhost/PHP/RICHUP/public/
 */

// ─── Démarrage de la session ───────────────────────────
session_start();

// ─── Constantes globales ───────────────────────────────
define('BASE_PATH',   dirname(__DIR__));
define('VIEWS_PATH',  BASE_PATH . '/src/views/');

/**
 * BASE_URL : préfixe pour tous les liens et assets.
 * Détecté automatiquement depuis la variable d'environnement de script.
 * Ex : /PHP/RICHUP/public/
 */
$scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
define('BASE_URL', rtrim($scriptDir, '/') . '/');

// ─── Autoload simplifié (pas de Composer) ──────────────
spl_autoload_register(function (string $class): void {
    $paths = [
        BASE_PATH . '/src/models/'      . $class . '.php',
        BASE_PATH . '/src/controllers/' . $class . '.php',
    ];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// ─── Chargement de la couche modèle de base ────────────
require_once BASE_PATH . '/src/models/Database.php';
require_once BASE_PATH . '/src/models/UserModel.php';

// ─── Chargement des contrôleurs ────────────────────────
require_once BASE_PATH . '/src/controllers/BaseController.php';
require_once BASE_PATH . '/src/controllers/AuthController.php';
require_once BASE_PATH . '/src/controllers/GameController.php';

// ─── Routeur ───────────────────────────────────────────
$page = filter_input(INPUT_GET, 'page', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'game';

switch ($page) {

    case 'login':
        (new AuthController())->login();
        break;

    case 'register':
        (new AuthController())->register();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'game':
    default:
        (new GameController())->index();
        break;
}
