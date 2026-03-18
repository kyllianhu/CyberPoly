<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'CyberSafe Monopoly') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/auth.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body class="auth-body">

    <!-- Particules d'arrière-plan animées -->
    <div class="cyber-bg" aria-hidden="true">
        <div class="particles">
            <?php for ($i = 0; $i < 12; $i++): ?>
            <span class="particle"></span>
            <?php endfor; ?>
        </div>
        <div class="grid-overlay"></div>
        <div class="scanline"></div>
    </div>

    <main class="auth-main">
        <?= $content ?>
    </main>

</body>
</html>
