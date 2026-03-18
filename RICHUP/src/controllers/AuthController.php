<?php
class AuthController extends BaseController
{
    private UserModel $userModel;

    // Instancie le modèle utilisateur au chargement du contrôleur
    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Affiche le formulaire de connexion et traite sa soumission
    public function login(): void
    {
        if (!empty($_SESSION['connecte']) && $_SESSION['connecte'] == true) {
            $this->redirect('?page=game');
        }

        $error   = '';
        $success = '';

        // Restaure la session si un cookie remember_token valide est présent
        if (!empty($_COOKIE['remember_token']) && empty($_SESSION['connecte'])) {
            $this->handleRememberMeCookie();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->verifyCsrf();

            $username = trim($_POST['user'] ?? '');
            $password = $_POST['mdp'] ?? '';

            if (empty($username) || empty($password)) {
                $error = 'Veuillez remplir tous les champs.';
            } else {
                try {
                    $joueur = $this->userModel->authenticate($username, $password);

                    if ($joueur) {
                        session_regenerate_id(true); // Nouveau ID de session pour éviter la fixation
                        $_SESSION['connecte'] = true;
                        $_SESSION['pseudo']   = $joueur['pseudo'];
                        $_SESSION['user_id']  = $joueur['id'];

                        // Pose un cookie remember_token valable 7 jours si la case est cochée
                        if (!empty($_POST['rememberMe'])) {
                            $token = bin2hex(random_bytes(32));
                            setcookie('remember_token', $joueur['username'] . '|' . $token, [
                                'expires'  => time() + (7 * 24 * 60 * 60),
                                'path'     => '/',
                                'httponly' => true,
                                'samesite' => 'Lax',
                            ]);
                        }

                        $this->redirect('?page=game');
                    } else {
                        $error = 'Identifiant ou mot de passe incorrect.';
                    }
                } catch (PDOException $e) {
                    $error = 'Erreur de connexion à la base de données. Veuillez réessayer.';
                }
            }
        }

        $this->render('auth/login', [
            'pageTitle'  => 'Connexion — CyberSafe Monopoly',
            'activePage' => 'login',
            'error'      => $error,
            'success'    => $success,
            'csrfToken'  => $this->csrfToken(),
        ], 'auth');
    }

    // Affiche le formulaire d'inscription et traite sa soumission
    public function register(): void
    {
        if (!empty($_SESSION['connecte']) && $_SESSION['connecte'] == true) {
            $this->redirect('?page=game');
        }

        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->verifyCsrf();

            $username = trim($_POST['user'] ?? '');
            $password = $_POST['mdp'] ?? '';
            $confirm  = $_POST['confirm_mdp'] ?? '';
            $pseudo   = trim($_POST['pseudo'] ?? '');
            $email    = trim($_POST['email'] ?? '');

            // Contrôles de saisie côté serveur avant d'écrire en base
            if (empty($username) || empty($password) || empty($pseudo) || empty($email)) {
                $error = 'Tous les champs sont obligatoires.';
            } elseif (strlen($password) < 8) {
                $error = 'Le mot de passe doit contenir au moins 8 caractères.';
            } elseif ($password != $confirm) {
                $error = 'Les mots de passe ne correspondent pas.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Adresse email invalide.';
            } elseif (strlen($username) < 3 || strlen($username) > 50) {
                $error = "L'identifiant doit contenir entre 3 et 50 caractères.";
            } else {
                try {
                    $this->userModel->create($username, $password, $pseudo, $email);
                    $success = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
                } catch (PDOException $e) {
                    $error = 'Ce nom d\'utilisateur, email ou pseudo est déjà utilisé.';
                }
            }
        }

        $this->render('auth/register', [
            'pageTitle'  => 'Créer un compte — CyberSafe Monopoly',
            'activePage' => 'register',
            'error'      => $error,
            'success'    => $success,
            'csrfToken'  => $this->csrfToken(),
        ], 'auth');
    }

    // Détruit la session et le cookie remember puis redirige vers le login
    public function logout(): void
    {
        if (!empty($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', [
                'expires'  => time() - 3600,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        }

        $_SESSION = [];
        session_destroy();
        $this->redirect('?page=login');
    }

    // Lit le cookie remember_token et reconnecte l'utilisateur si le compte existe
    private function handleRememberMeCookie(): void
    {
        $parts = explode('|', $_COOKIE['remember_token'], 2);
        if (count($parts) != 2) {
            return;
        }

        $username = $parts[0];

        try {
            $joueur = $this->userModel->findByUsername($username);
            if ($joueur) {
                session_regenerate_id(true);
                $_SESSION['connecte'] = true;
                $_SESSION['pseudo']   = $joueur['pseudo'];
                $_SESSION['user_id']  = $joueur['id'];
                $this->redirect('?page=game');
            }
        } catch (PDOException $e) {
            setcookie('remember_token', '', time() - 3600, '/'); // Supprime le cookie invalide
        }
    }
}
