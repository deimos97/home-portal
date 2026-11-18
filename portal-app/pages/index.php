<?php
$title = 'Inicio';

$services = [
    [
        'url' => 'https://claudio.hotwheelsandbids.com',
        'name' => 'Claudio',
        'icon' => 'fa-face-smile',
        'description' => 'Asistente de escritorio: agenda, mantenimiento y tareas.',
        'badges' => [['accent', 'PROD'], ['info', 'MAC MINI']],
    ],
    [
        'url' => 'https://claudio-dev.hotwheelsandbids.com',
        'name' => 'Claudio · dev',
        'icon' => 'fa-flask',
        'description' => 'Versión en desarrollo de Claudio.',
        'badges' => [['warn', 'DEV'], ['info', 'MAC MINI']],
    ],
    [
        'url' => 'https://pihole.hotwheelsandbids.com/admin',
        'name' => 'Pi-hole',
        'icon' => 'fa-shield-halved',
        'description' => 'DNS, bloqueo de anuncios y DHCP de la red de casa.',
        'badges' => [['good', 'DNS'], ['info', 'RASPBERRY']],
    ],
    [
        'url' => '/temp-rc-car',
        'name' => 'Coche RC',
        'icon' => 'fa-car-side',
        'description' => 'Mando y cámara del coche teledirigido (ESP32).',
        'badges' => [['accent', 'TEMP']],
    ],
];
?>
<div class="v2-page">
    <div class="v2-page-head">
        <div>
            <span class="v2-label">Portal</span>
            <h1 class="v2-page-title">Servicios <span class="v2-count"><?= count($services) ?></span></h1>
        </div>
    </div>

    <div class="v2-service-grid">
        <?php foreach ($services as $s): ?>
            <a class="v2-card v2-service" href="<?= e($s['url']) ?>">
                <span class="v2-service-icon"><i class="fa-solid <?= e($s['icon']) ?>"></i></span>
                <span class="v2-service-body">
                    <span class="v2-service-name"><?= e($s['name']) ?></span>
                    <span class="v2-service-desc"><?= e($s['description']) ?></span>
                    <span class="v2-service-meta">
                        <?php foreach ($s['badges'] as [$kind, $text]): ?>
                            <span class="v2-badge v2-badge-<?= e($kind) ?>"><?= e($text) ?></span>
                        <?php endforeach; ?>
                        <span class="v2-mono v2-dim v2-service-url"><?= e(preg_replace('#^https://#', '', $s['url'])) ?></span>
                    </span>
                </span>
                <i class="fa-solid fa-chevron-right v2-service-go"></i>
            </a>
        <?php endforeach; ?>
    </div>
</div>
