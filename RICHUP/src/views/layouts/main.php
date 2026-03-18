<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'CyberSafe Monopoly') ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/main.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>

    <?php include VIEWS_PATH . 'partials/header.php'; ?>

    <main class="main-content">
        <?= $content ?>
    </main>

    <?php include VIEWS_PATH . 'partials/footer.php'; ?>

    <script src="<?= BASE_URL ?>assets/js/game.js"></script>
</body>
</html>
