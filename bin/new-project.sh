#!/usr/bin/env bash
# Fork the Ossigeno (_tw) starter into a new client project.
#
# Usage: bin/new-project.sh <slug> "<Theme Name>" [target-dir]
#   <slug>         lowercase project id  -> folder, theme slug, zip name   (replaces "ossigeno")
#   "<Theme Name>" Title-case name       -> style.css Theme Name, @package (replaces "Ossigeno")
#   [target-dir]   defaults to ~/Sites/<slug>
#
# Renames ONLY the project-identity tokens.  The Snappysnail agency namespace
# (ssnail / SSNAIL -- text domain + function/constant prefix) is intentionally LEFT INTACT.
set -euo pipefail

SLUG="${1:?usage: new-project.sh <slug> \"<Theme Name>\" [target-dir]}"
NAME="${2:?usage: new-project.sh <slug> \"<Theme Name>\" [target-dir]}"
PHP_SLUG="${SLUG//-/_}"   # hyphens are invalid in PHP identifiers
SRC="$(cd "$(dirname "$0")/.." && pwd)"
DEST="${3:-$HOME/Sites/$SLUG}"

[[ -e "$DEST" ]] && { echo "✗ $DEST already exists"; exit 1; }

echo "→ Copying starter to $DEST (excluding .git, node_modules, vendor, *.zip)"
mkdir -p "$DEST"
rsync -a --exclude '.git' --exclude 'node_modules' --exclude 'vendor' \
         --exclude '*.zip' "$SRC"/ "$DEST"/
cd "$DEST"

echo "→ Rewriting project-identity strings (agency prefix 'ssnail' left intact)"
grep -rIl -e 'Ossigeno' -e 'ossigeno' . \
  | while IFS= read -r f; do
      sed -i "s/Ossigeno/${NAME}/g; s/ossigeno_/${PHP_SLUG}_/g; s/ossigeno/${SLUG}/g" "$f"
    done

echo "→ Renaming files that carry the old project name"
while IFS= read -r f; do
  nf="$(printf '%s' "$f" | sed "s/Ossigeno/${NAME}/g; s/ossigeno/${SLUG}/g")"
  if [[ "$f" != "$nf" ]]; then mkdir -p "$(dirname "$nf")"; mv "$f" "$nf"; fi
done < <(find . -path ./.git -prune -o -iname '*ossigeno*' -print)

echo "→ Re-initialising git"
rm -rf .git
git init -q && git add -A
git commit -qm "Initial commit: ${NAME} (forked from Ossigeno _tw starter)"

echo "→ Creating theme symlink"
mkdir -p wp/wp-content/themes
ln -s ../../../theme "wp/wp-content/themes/${SLUG}"

cat <<EOF

✓ Created ${NAME} at ${DEST}

Next:
  cd ${DEST}
  npm install && composer install
  npm run dev
  ddev config --project-type=wordpress --docroot=wp --php-version=8.3 && ddev start
  ddev exec /usr/local/bin/wp core download --path=wp
  ddev exec /usr/local/bin/wp core install --path=wp \\
    --url="https://${SLUG}.ddev.site" \\
    --title="${NAME}" \\
    --admin_user=admin \\
    --admin_email=info@snappysnail.io \\
    --prompt=admin_password
EOF
