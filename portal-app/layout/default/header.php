<?php
/** Layout principal: barra superior + contenido + barra de estado. Usa $title, $user, $activeTab. */
$tabs = [
    'index'       => ['href' => '/',                                         'icon' => 'fa-house',       'label' => 'Inicio'],
    'pihole'      => ['href' => 'https://pihole.hotwheelsandbids.com/admin', 'icon' => 'fa-shield-halved', 'label' => 'Pi-hole', 'external' => true],
    'claudio'     => ['href' => 'https://claudio.hotwheelsandbids.com',      'icon' => 'fa-face-smile',  'label' => 'Claudio', 'external' => true],
    'temp-rc-car' => ['href' => '/temp-rc-car',                              'icon' => 'fa-car-side',    'label' => 'Coche RC'],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<?php require LAYOUT_PATH . '_head.php'; ?>
</head>
<body class="v2-app">
	<header class="v2-topbar">
		<a class="v2-brand" href="/" aria-label="Inicio">
			<span class="v2-logo-mark"><i class="fa-solid fa-tower-broadcast"></i></span>
			<span class="v2-brand-text">Babilonia 33</span>
		</a>
		<nav class="v2-tabs" aria-label="Secciones">
			<?php foreach ($tabs as $key => $t): ?>
				<a class="v2-tab<?= $activeTab === $key ? ' is-active' : '' ?>" href="<?= e($t['href']) ?>"<?= $activeTab === $key ? ' aria-current="page"' : '' ?> title="<?= e($t['label']) ?>">
					<i class="fa-solid <?= e($t['icon']) ?>"></i><span><?= e($t['label']) ?></span>
					<?php if (!empty($t['external'])): ?><i class="fa-solid fa-arrow-up-right-from-square v2-tab-ext"></i><?php endif; ?>
				</a>
			<?php endforeach; ?>
		</nav>
		<div class="v2-topbar-right">
			<span class="v2-chip"><span class="v2-dot v2-dot-good"></span><span class="v2-mono"><?= e($user) ?></span></span>
			<form method="post" action="/logout">
				<?= csrf_field() ?>
				<button class="v2-icon-btn" type="submit" title="Cerrar sesión" aria-label="Cerrar sesión"><i class="fa-solid fa-right-from-bracket"></i></button>
			</form>
		</div>
	</header>
	<main class="v2-content">
