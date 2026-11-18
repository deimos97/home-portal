// Reloj de la barra de estado
(() => {
    const clocks = document.querySelectorAll('[data-clock]');
    if (!clocks.length) return;
    const tick = () => {
        const t = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        clocks.forEach((c) => { c.textContent = t; });
    };
    tick();
    setInterval(tick, 1000);
})();
