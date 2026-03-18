<div class="auth-card auth-card--wide">

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
        <h2>Créer un compte</h2>
        <p>Rejoignez la communauté des experts en cybersécurité</p>
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
    <form class="auth-form" id="registerForm" method="POST" action="<?= BASE_URL ?>?page=register" novalidate>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-grid">
            <div class="form-field">
                <label for="pseudo">
                    <i class="fas fa-user" aria-hidden="true"></i>
                    Pseudo
                </label>
                <div class="field-wrapper">
                    <input
                        type="text"
                        id="pseudo"
                        name="pseudo"
                        placeholder="Votre pseudo en jeu"
                        required
                        autocomplete="nickname"
                        value="<?= htmlspecialchars($_POST['pseudo'] ?? '') ?>"
                    >
                    <span class="field-icon"><i class="fas fa-gamepad" aria-hidden="true"></i></span>
                </div>
            </div>

            <div class="form-field">
                <label for="email">
                    <i class="fas fa-envelope" aria-hidden="true"></i>
                    Adresse email
                </label>
                <div class="field-wrapper">
                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="votre.email@exemple.com"
                        required
                        autocomplete="email"
                        value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                    >
                    <span class="field-icon"><i class="fas fa-at" aria-hidden="true"></i></span>
                </div>
            </div>

            <div class="form-field">
                <label for="username">
                    <i class="fas fa-id-card" aria-hidden="true"></i>
                    Identifiant de connexion
                </label>
                <div class="field-wrapper">
                    <input
                        type="text"
                        id="username"
                        name="user"
                        placeholder="Minimum 3 caractères"
                        required
                        autocomplete="username"
                        minlength="3"
                        maxlength="50"
                        value="<?= htmlspecialchars($_POST['user'] ?? '') ?>"
                    >
                    <span class="field-icon"><i class="fas fa-key" aria-hidden="true"></i></span>
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
                        placeholder="Minimum 8 caractères"
                        required
                        autocomplete="new-password"
                        minlength="8"
                    >
                    <button type="button" class="toggle-password" aria-label="Afficher le mot de passe" data-target="password">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                <!-- Indicateur de force du mot de passe -->
                <div class="password-strength" id="passwordStrength" aria-live="polite">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <span class="strength-label" id="strengthLabel"></span>
                </div>
            </div>

            <div class="form-field form-field--full">
                <label for="confirmPassword">
                    <i class="fas fa-lock-open" aria-hidden="true"></i>
                    Confirmer le mot de passe
                </label>
                <div class="field-wrapper">
                    <input
                        type="password"
                        id="confirmPassword"
                        name="confirm_mdp"
                        placeholder="Répétez votre mot de passe"
                        required
                        autocomplete="new-password"
                        minlength="8"
                    >
                    <button type="button" class="toggle-password" aria-label="Afficher la confirmation" data-target="confirmPassword">
                        <i class="fas fa-eye" aria-hidden="true"></i>
                    </button>
                </div>
                <span class="field-hint" id="confirmHint"></span>
            </div>
        </div>

        <button type="submit" class="auth-submit-btn" id="submitBtn">
            <i class="fas fa-user-plus" aria-hidden="true"></i>
            Créer mon compte
        </button>
    </form>

    <!-- Lien vers login -->
    <div class="auth-footer">
        <p>Vous avez déjà un compte ?
            <a href="<?= BASE_URL ?>?page=login" class="auth-link">Se connecter</a>
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

// Indicateur de force du mot de passe
const passwordInput = document.getElementById('password');
const strengthFill  = document.getElementById('strengthFill');
const strengthLabel = document.getElementById('strengthLabel');

passwordInput.addEventListener('input', () => {
    const val = passwordInput.value;
    let score = 0;
    if (val.length >= 8) score++;
    if (/[A-Z]/.test(val)) score++;
    if (/[0-9]/.test(val)) score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const levels = [
        { label: '', color: 'transparent', width: '0%' },
        { label: 'Faible', color: '#ef4444', width: '25%' },
        { label: 'Moyen', color: '#f59e0b', width: '50%' },
        { label: 'Fort', color: '#3b82f6', width: '75%' },
        { label: 'Excellent', color: '#10b981', width: '100%' },
    ];

    const level = levels[score] || levels[0];
    strengthFill.style.width = level.width;
    strengthFill.style.background = level.color;
    strengthLabel.textContent = level.label;
    strengthLabel.style.color = level.color;
});

// Vérification confirmation mot de passe
const confirmInput = document.getElementById('confirmPassword');
const confirmHint  = document.getElementById('confirmHint');
const submitBtn    = document.getElementById('submitBtn');

function checkConfirm() {
    if (confirmInput.value.length == 0) {
        confirmHint.textContent = '';
        return;
    }
    if (confirmInput.value != passwordInput.value) {
        confirmHint.textContent = '⚠ Les mots de passe ne correspondent pas';
        confirmHint.style.color = '#ef4444';
        submitBtn.disabled = true;
    } else {
        confirmHint.textContent = '✓ Les mots de passe correspondent';
        confirmHint.style.color = '#10b981';
        submitBtn.disabled = false;
    }
}

confirmInput.addEventListener('input', checkConfirm);
passwordInput.addEventListener('input', checkConfirm);
</script>
