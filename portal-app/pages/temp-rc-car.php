<?php $title = 'Coche RC'; ?>
<div class="v2-page">
    <div class="v2-page-head">
        <div>
            <span class="v2-label">Dispositivo · ESP32 192.168.0.2</span>
            <h1 class="v2-page-title">Coche RC</h1>
        </div>
        <span class="v2-chip" id="car-state"><span class="v2-dot"></span><span class="v2-mono">parado</span></span>
    </div>

    <div class="v2-rc-grid">
        <section class="v2-card v2-rc-panel">
            <span class="v2-label">Cámara</span>
            <div class="v2-rc-stream">
                <img src="/car-stream" id="stream" alt="" onerror="this.hidden=true; this.nextElementSibling.hidden=false">
                <span class="v2-mono v2-dim" hidden><i class="fa-solid fa-video-slash"></i> sin señal</span>
            </div>
        </section>
        <section class="v2-card v2-rc-panel">
            <span class="v2-label">Mando</span>
            <div id="joystick-zone" class="v2-rc-joystick"></div>
            <p class="v2-mono v2-dim v2-rc-help">arrastra · suelta para parar</p>
        </section>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/nipplejs/0.10.1/nipplejs.min.js"></script>
<script>
const REPEAT_MS = 200;
const WATCHDOG_MS = 500;
const LABELS = { go: 'adelante', back: 'atrás', left: 'izquierda', right: 'derecha', stop: 'parado' };

let currentCmd = null;
let repeatTimer = null;
let watchdogTimer = null;

const stateChip = document.getElementById('car-state');
function showState(cmd, error = false) {
    stateChip.querySelector('.v2-mono').textContent = error ? 'sin conexión' : LABELS[cmd] ?? cmd;
    stateChip.querySelector('.v2-dot').className = 'v2-dot ' + (error ? 'v2-dot-bad' : cmd && cmd !== 'stop' ? 'v2-dot-warn' : '');
}

function send(cmd) {
    fetch(`/command?cmd=${cmd}`)
        .then(r => {
            if (!r.ok) throw new Error('bad response');
            showState(cmd);
            resetWatchdog();
        })
        .catch(() => { showState(cmd, true); forceStop(); });
}

function resetWatchdog() {
    clearTimeout(watchdogTimer);
    if (currentCmd && currentCmd !== 'stop') {
        watchdogTimer = setTimeout(forceStop, WATCHDOG_MS);
    }
}

function forceStop() {
    clearInterval(repeatTimer);
    clearTimeout(watchdogTimer);
    currentCmd = null;
    fetch('/command?cmd=stop').catch(() => {});
}

function startCommand(cmd) {
    if (cmd === currentCmd) return;
    currentCmd = cmd;
    clearInterval(repeatTimer);
    send(cmd);
    repeatTimer = setInterval(() => send(currentCmd), REPEAT_MS);
}

function stopCommand() {
    currentCmd = null;
    clearInterval(repeatTimer);
    clearTimeout(watchdogTimer);
    send('stop');
}

// --- Joystick ---
const joystick = nipplejs.create({
    zone: document.getElementById('joystick-zone'),
    mode: 'static',
    position: { left: '50%', top: '50%' },
    color: '#ec6d1b',
    size: 140,
});

// nipplejs: ángulo en grados (0 = derecha, 90 = arriba) → 4 órdenes discretas
function angleToCommand(angleDeg) {
    if (angleDeg >= 45 && angleDeg < 135) return 'go';
    if (angleDeg >= 135 && angleDeg < 225) return 'left';
    if (angleDeg >= 225 && angleDeg < 315) return 'back';
    return 'right';
}

const DEADZONE = 0.3;

joystick.on('move', (evt, data) => {
    if (!data.angle || data.force < DEADZONE) {
        stopCommand();
        return;
    }
    startCommand(angleToCommand(data.angle.degree));
});
joystick.on('end', () => stopCommand());

// Seguridad: parar si la pestaña pierde el foco, se oculta o se cierra a mitad de una orden
document.addEventListener('visibilitychange', () => { if (document.hidden) forceStop(); });
window.addEventListener('blur', forceStop);
window.addEventListener('beforeunload', forceStop);
</script>
