# home-portal

Portal de casa (Babilonia 33) y login común de los servicios en `hotwheelsandbids.com`.
Documentación para trabajar en él: [AGENTS.md](AGENTS.md). Plan y decisiones: [ROADMAP.md](ROADMAP.md).

## Monkey noises

- nginx ejecuta **cualquier** `.php` que haya en el webroot (`location ~ \.php$`): por eso el código vive en `portal-app/`, fuera.
- nginx sirve **cualquier** fichero estático del webroot: `users.json` llegó a estar público así.
- El `security.inc` de nginx bloquea el user-agent `curl`: para probar con curl usa `-A "Mozilla/5.0"`.
- Desde casa, Pi-hole resuelve `hotwheelsandbids.com` a `192.168.0.5` (no a la IP pública).
- **`public_html/index.php` tiene que existir** aunque no haga nada: la directiva `index` de nginx lo busca para servir `/`; sin él, `/` da 403.
