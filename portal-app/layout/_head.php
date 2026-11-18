<?php /* <head> común a todos los layouts. Usa $title. */ ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="color-scheme" content="dark">
<meta name="theme-color" content="#0b0a09">
<meta name="robots" content="noindex, nofollow">
<title><?= e(($title ? "{$title} · " : '') . 'Babilonia 33') ?></title>
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer">
<link rel="stylesheet" href="/css/style.css?v=<?= @filemtime(dirname(APP_PATH) . '/public_html/css/style.css') ?: 1 ?>">
<script src="/js/main.js?v=<?= @filemtime(dirname(APP_PATH) . '/public_html/js/main.js') ?: 1 ?>" defer></script>
