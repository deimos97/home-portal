#!/bin/zsh
# Despliega el portal en la Raspberry (sin sudo).
#   public_html/ → /home/deimos97/public_html   (webroot de nginx: solo server.php, auth-check.php y estáticos)
#   portal-app/  → /home/deimos97/portal-app    (código, FUERA del webroot)
# Antes copia lo desplegado a ~/portal-backup-<fecha-hora>/ en la propia Raspberry.
set -euo pipefail

PI="deimos97@192.168.0.5"
HOME_PI="/home/deimos97"
cd "$(dirname "$0")"

for f in $(git ls-files '*.php'); do php -l "$f" >/dev/null; done
echo "✓ PHP sin errores de sintaxis"

STAMP=$(date +%Y%m%d-%H%M%S)
ssh "$PI" "mkdir -p $HOME_PI/portal-backups && chmod 700 $HOME_PI/portal-backups && \
  tar czf $HOME_PI/portal-backups/portal-$STAMP.tgz -C $HOME_PI public_html \$(test -d $HOME_PI/portal-app && echo portal-app)"
echo "✓ Copia en la Raspberry: ~/portal-backups/portal-$STAMP.tgz"

# --delete: el webroot queda EXACTAMENTE como public_html/ (nada de código suelto accesible por URL)
rsync -rlt --delete --chmod=D755,F644 public_html/ "$PI:$HOME_PI/public_html/"
rsync -rlt --delete --chmod=D755,F644 portal-app/ "$PI:$HOME_PI/portal-app/"
echo "✓ Ficheros copiados"

UA="Mozilla/5.0 (deploy-check) Safari"
for p in / /login /private/users.json /pages/command.php /portal-app/bootstrap.php; do
  printf "  %-26s %s\n" "$p" "$(curl -s -o /dev/null -w '%{http_code}' -A "$UA" -m 5 https://hotwheelsandbids.com$p)"
done
echo "Esperado: / 302, /login 200, el resto 404."
