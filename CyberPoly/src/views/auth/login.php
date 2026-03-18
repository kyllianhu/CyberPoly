<div class="auth-card">

    <!-- Logo -->
    <div class="auth-logo">
        <div class="auth-logo-icon">
            <i class="fas fa-shield-halved" aria-hidden="true"></i>
        </div>
        <h1 class="auth-title">CyberSafe Monopoly</h1>
        <p class="auth-subtitle">Maîtrisez la cybersécurité en jouant</p>
    </div>

    <!-- Titre de l'action -->
    <div class="auth-form-header">
        <h2>Connexion</h2>
        <p>Accédez à votre espace de jeu sécurisé</p>
    </div>

    <!-- Messages -->
    <?php if (!empty($error)): ?>
    <div class="auth-alert auth-alert--error" role="alert">
        <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
    <div class="auth-alert auth-alert--success" role="alert">
        <i class="fas fa-circle-check" aria-hidden="true"></i>
        <span><?= htmlspecialchars($success) ?></span>
    </div>
    <?php endif; ?>

    <!-- Formulaire -->
    <form class="auth-form" method="POST" action="<?= BASE_URL ?>?page=login" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-field">
            <label for="username">
                <i class="fas fa-id-card" aria-hidden="true"></i>
                Identifiant
            </label>
            <div class="field-wrapper">
                <input
                    type="text"
                    id="username"
                    name="user"
                    placeholder="Votre identifiant"
                    required
                    autocomplete="username"
                    value="<?= htmlspecialchars($_POST['user'] ?? '') ?>"
                >
                <span class="field-icon"><i class="fas fa-user" aria-hidden="true"></i></span>
            </div>
        </div>

        <div class="form-field">
            <label for="password">
                <i class="fas fa-lock" aria-hidden="true"></i>
                Mot de passe
            </label>
            <div class="field-wrapper">
                <input
                    type="password"
                    id="password"
                    name="mdp"
                    placeholder="••••••••"
                    required
                    autocomplete="current-password"
                >
                <button type="button" class="toggle-password" aria-label="Afficher/masquer le mot de passe" data-target="password">
                    <i class="fas fa-eye" aria-hidden="true"></i>
                </button>
            </div>
        </div>

        <div class="form-options">
            <label class="checkbox-label">
                <input type="checkbox" name="rememberMe" id="rememberMe">
                <span class="checkbox-custom"></span>
                Se souvenir de moi
            </label>
            <a href="#" class="link-muted">Mot de passe oublié ?</a>
        </div>

        <button type="submit" class="auth-submit-btn">
            <i class="fas fa-right-to-bracket" aria-hidden="true"></i>
            Se connecter
        </button>
    </form>

    <!-- Lien vers register -->
    <div class="auth-footer">
        <p>Pas encore de compte ?
            <a href="<?= BASE_URL ?>?page=register" class="auth-link">Créer un compte</a>
        </p>
    </div>

</div>

<script>
// Toggle password visibility
document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
        const targetId = btn.dataset.target;
        const input = document.getElementById(targetId);
        const icon = btn.querySelector('i');
        if (input.type == 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    });
});
</script>
