# home-portal — Portal Babilonia 33

Portal de casa servido por la Raspberry (`192.168.0.5`, nginx + PHP-FPM 8.4) en `https://hotwheelsandbids.com`. Hace dos cosas:

1. **Página de inicio** con los servicios de casa (Claudio, Pi-hole, coche RC).
2. **Login común**: su sesión protege otros subdominios con `auth_request` de nginx → `public_html/auth-check.php` (`pihole.`, `claudio.`, `claudio-dev.`). La cookie de sesión se emite para `hotwheelsandbids.com` y vale en todos los subdominios.

## Estructura

```
public_html/          → /home/deimos97/public_html   (WEBROOT: solo esto es accesible por URL)
  server.php            front controller (nginx manda aquí todo lo que no es estático)
  auth-check.php        200/401 para el auth_request de nginx
  css/ js/ favicon.svg
portal-app/           → /home/deimos97/portal-app    (FUERA del webroot)
  bootstrap.php         sesión, usuarios, redirecciones seguras, CSRF, límite de intentos
  router.php            /algo → pages/algo.php; todo exige sesión salvo /login
  pages/                index, login, logout (POST), temp-rc-car, command (JSON)
  layout/               default (barras superior y de estado), auth (login/errores), _head.php
private/              → /home/deimos97/private       (NO está en git: users.json, nginx-conf.conf)
deploy.sh             despliegue por rsync con copia previa en la Raspberry
```

## Reglas

- **Nunca** poner código, datos ni ficheros privados dentro de `public_html/`: nginx ejecuta cualquier `.php` y sirve cualquier fichero que haya ahí.
- Páginas nuevas: `portal-app/pages/<nombre>.php`. Pueden fijar `$title` y `$layout` (`default`, `auth`, `none` para JSON).
- Formularios POST: siempre `<?= csrf_field() ?>` y `csrf_valid()`.
- Redirecciones con datos del usuario: siempre `safe_redirect_target()`.
- Salida HTML: siempre `e()`.
- Diseño: sistema "staff v2" (oscuro cálido, acento ámbar, mono para datos). Tokens y componentes `.v2-*` en `public_html/css/style.css`. Sin frameworks de UI ni `alert/confirm/prompt`.
- UI y docs en español.

## Usuarios

`/home/deimos97/private/users.json`: `{"usuario": {"password_hash": "...", "is_active": 1}}`. Hash:

```bash
php -r 'echo password_hash("la_contraseña", PASSWORD_DEFAULT), "\n";'
```

## Probar en local

```bash
PORTAL_USERS_FILE=/ruta/a/usuarios-de-prueba.json php -S 127.0.0.1:8099 -t public_html public_html/server.php
```

En local la cookie no lleva dominio ni `secure`, así que funciona en `http://127.0.0.1`.

## Desplegar

```bash
./deploy.sh
```

Copia antes lo desplegado a `~/portal-backups/portal-<fecha>.tgz` en la Raspberry y al final comprueba que `/`, `/login` y que las rutas sensibles den 404. No necesita sudo. La config de nginx (`private/nginx-conf.conf`, copia de `/etc/nginx/sites-enabled/public_html`) sí necesita sudo para cambiarse.

## Si vas a… → lee…

| Si vas a… | Lee |
|---|---|
| Empezar o cerrar cualquier tarea | `ROADMAP.md` |
| Tocar Claudio o sus subdominios | repo `claudio-task-assistant`, `docs/servidor-pi.md` |
