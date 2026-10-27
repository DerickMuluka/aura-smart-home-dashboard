<?php
require_once __DIR__ . '/icons.php';
$pageTitle = $pageTitle ?? 'Aura';
$bodyClass = $bodyClass ?? '';
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="theme-color" content="#0a0e1a">
<title><?= htmlspecialchars($pageTitle) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<script src="assets/js/theme.js"></script>
<link rel="stylesheet" href="assets/css/global.css">
<?php if ($bodyClass): ?><link rel="stylesheet" href="assets/css/<?= $bodyClass ?>.css"><?php endif; ?>
</head>
<body class="<?= $bodyClass ?>">