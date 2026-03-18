<?php
class UserModel
{
    private PDO $db;

    // Récupère la connexion PDO partagée au chargement du modèle
    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    // Retourne le joueur correspondant au username ou null si inexistant
    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM joueur WHERE username = :username LIMIT 1');
        $stmt->execute([':username' => $username]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Retourne le joueur correspondant à l'email ou null si inexistant
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM joueur WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    // Vérifie les identifiants et retourne le joueur si corrects, null sinon
    public function authenticate(string $username, string $password): ?array
    {
        $joueur = $this->findByUsername($username);

        if ($joueur == null) {
            return null;
        }

        if (str_starts_with($joueur['mdp'], '$2y$')) {
            $valid = password_verify($password, $joueur['mdp']); // Mot de passe haché BCRYPT
        } else {
            $valid = ($password == $joueur['mdp']); // Ancien compte en clair : comparaison directe
            if ($valid) {
                $this->rehashPassword($joueur['id'], $password); // Migration vers BCRYPT au premier login
            }
        }

        return $valid ? $joueur : null;
    }

    // Insère un nouveau joueur avec le mot de passe haché en BCRYPT
    public function create(string $username, string $password, string $pseudo, string $email): bool
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $this->db->prepare(
            'INSERT INTO joueur (username, mdp, pseudo, email) VALUES (:username, :mdp, :pseudo, :email)'
        );

        return $stmt->execute([
            ':username' => $username,
            ':mdp'      => $hash,
            ':pseudo'   => $pseudo,
            ':email'    => $email,
        ]);
    }

    // Met à jour le hash du mot de passe en base pour migrer un ancien compte
    private function rehashPassword(int $id, string $plainPassword): void
    {
        $hash = password_hash($plainPassword, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare('UPDATE joueur SET mdp = :mdp WHERE id = :id');
        $stmt->execute([':mdp' => $hash, ':id' => $id]);
    }
}
