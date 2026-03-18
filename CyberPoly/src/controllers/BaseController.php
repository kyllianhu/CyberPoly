<?php
class BaseController
{
    // Charge une vue dans un layout et envoie le HTML au navigateur
    protected function render(string $view, array $data = [], string $layout = 'main'): void
    {
        extract($data, EXTR_SKIP);
        ob_start();
        include VIEWS_PATH . $view . '.php';
        $content = ob_get_clean();
        include VIEWS_PATH . 'layouts/' . $layout . '.php';
    }

    // Redirige l'utilisateur vers une URL relative au point d'entrée
    protected function redirect(string $url): never
    {
        header('Location: ' . BASE_URL . $url);
        exit();
    }

    // Génère ou retourne le token CSRF stocké en session
    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    // Vérifie le token CSRF du formulaire et stoppe avec 403 si invalide
    protected function verifyCsrf(): void
    {
        $submitted = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $submitted)) {
            http_response_code(403);
            die('Token CSRF invalide. Veuillez actualiser la page et réessayer.');
        }
    }

    // Redirige vers le login si l'utilisateur n'est pas connecté
    protected function requireAuth(): void
    {
        if (empty($_SESSION['connecte']) || $_SESSION['connecte'] != true) {
            $this->redirect('?page=login');
        }
    }
}
