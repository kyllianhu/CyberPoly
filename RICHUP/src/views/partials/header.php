<header class="game-header" role="banner">
    <div class="header-inner">
        <div class="header-brand">
            <div class="brand-icon">
                <i class="fas fa-shield-halved" aria-hidden="true"></i>
            </div>
            <div class="brand-text">
                <h1>CyberSafe Monopoly</h1>
                <p class="brand-sub">Protégez votre entreprise des cybermenaces</p>
            </div>
        </div>

        <nav class="header-nav" aria-label="Navigation principale">
            <div class="player-badge">
                <i class="fas fa-user-shield" aria-hidden="true"></i>
                <span><?= htmlspecialchars($_SESSION['pseudo'] ?? 'Joueur') ?></span>
            </div>
            <form method="post" action="<?= BASE_URL ?>?page=logout" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">
                <button type="submit" class="btn-logout" title="Se déconnecter">
                    <i class="fas fa-right-from-bracket" aria-hidden="true"></i>
                    <span>Déconnexion</span>
                </button>
            </form>
        </nav>
    </div>

</header>
