# ROADMAP — home-portal

## Estado de partida (2026-11-18)

- Portal PHP en la Raspberry con login (usuarios en JSON) y `auth_request` para `pihole.`.
- **Problemas detectados ese día:**
  - `public_html/private/users.json` se servía públicamente (usuario + hash de una contraseña antigua). Movido fuera del webroot el 2026-11-18 (copia en `~/portal-backup-2026-11-18/`).
  - Todo `pages/*.php` se ejecutaba directamente por URL saltándose el login (p. ej. `/pages/command.php?cmd=go` movía el coche sin sesión).
  - Redirección abierta en `/login?redir_to=` y en la barra final (`//otro-sitio/`).
  - Sin regenerar el id de sesión al entrar (fijación de sesión), sin CSRF, sin límite de intentos, `display_errors` activado en producción, `/logout` inexistente.
  - Copias sueltas en el webroot (`login.php`, `README.md`).

## Fase 1 — Seguridad y diseño

- [x] Código fuera del webroot (`portal-app/`), webroot solo con `server.php`, `auth-check.php` y estáticos (2026-11-18)
- [x] Redirecciones seguras (solo rutas propias o `https://*.hotwheelsandbids.com`), `session_regenerate_id`, CSRF, límite de 5 intentos/10 min por IP, errores solo al log, `/logout` por POST (2026-11-18)
- [x] Sistema visual "staff v2" en todo el portal (2026-11-18)
- [x] `deploy.sh` con copia previa en la Raspberry (2026-11-18)
- [x] Desplegado en la Raspberry con `./deploy.sh`; comprobado `/` 302, `/login` 200, rutas sensibles 404 y redirección de `pihole.` al login (2026-11-18, 6251900)
- [ ] Javier: entrar con su usuario real y confirmar que `pihole.` no vuelve a pedir login
- [ ] nginx: aplicar `limit_req zone=login_request` a `/login` (la zona ya existe en `conf.d/rate-limit.conf` pero no se usa) — requiere sudo
- [ ] Revisar `/car-stream` (la imagen del coche apunta ahí y no hay ruta ni proxy)

## Deuda técnica

- [ ] Cambiar la contraseña antigua si se usa en algún otro sitio (su hash estuvo expuesto).
- [ ] `~/cloudflare.txt` en la Raspberry tiene permisos 664: si contiene un token de Cloudflare, dejarlo en 600.

## Decisiones tomadas

| Fecha | Decisión | Por qué |
|---|---|---|
| 2026-11-18 | El portal es el login común de los servicios de casa (también Claudio fuera de la red local) | Ya existía y funciona con `auth_request` |
| 2026-11-18 | Código de la app fuera del webroot | nginx ejecuta cualquier `.php` del webroot; así nada interno es accesible por URL |
| 2026-11-18 | Diseño "staff v2" para todo el portal | Guía de diseño de Javier |
