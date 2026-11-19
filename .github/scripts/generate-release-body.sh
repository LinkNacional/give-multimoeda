#!/usr/bin/env bash
#
# Gera o corpo (body) das GitHub Releases que publicam o .zip do plugin.
#
# Lê os metadados do readme.txt e o resumo da versão do CHANGELOG.md do ref
# atualmente no working directory (mesma lógica dinâmica da skill open-pr),
# e imprime o corpo final no stdout.
#
# Uso (na raiz do repositório):
#   bash .github/scripts/generate-release-body.sh > dist/release-body.md
#
set -euo pipefail

TEMPLATE=".github/release-body-template.md"

# O readme do wp.org pode estar como readme.txt ou README.txt
README_FILE="readme.txt"
[ -f "$README_FILE" ] || README_FILE="README.txt"
if [ ! -f "$README_FILE" ]; then
  echo "readme.txt não encontrado" >&2
  exit 1
fi

# Lê um campo do cabeçalho do readme (ex.: "Tested up to", "Requires PHP", "Stable tag").
read_meta() {
  grep -m1 -i "^$1:" "$README_FILE" \
    | sed -E 's/^[^:]+:[[:space:]]*//; s/[[:space:]]+$//' \
    | tr -d '\r'
}

CONTRIBUTORS="$(read_meta 'Contributors')"
DONATE_LINK="$(read_meta 'Donate link')"
TAGS="$(read_meta 'Tags')"
TESTED_UP="$(read_meta 'Tested up to')"
REQUIRES_PHP="$(read_meta 'Requires PHP')"
STABLE_TAG="$(read_meta 'Stable tag')"
LICENSE="$(read_meta 'License')"
LICENSE_URI="$(read_meta 'License URI')"

# Bullets da entrada do CHANGELOG.md correspondente à versão estável
# (do cabeçalho "# VERSION - data" até o próximo cabeçalho "# ").
VERSION_SUMMARY="$(awk -v ver="$STABLE_TAG" '
  /^# / { if ($2 == ver) { found = 1; next } else if (found) { exit } }
  found { print }
' CHANGELOG.md)"

# Evita linha em branco no topo do resumo
VERSION_SUMMARY="$(printf '%s' "$VERSION_SUMMARY" | sed -e '/./,$!d')"

# Fallback: se a versão estável não tiver entrada, usa a mais recente do changelog.
if [ -z "$VERSION_SUMMARY" ]; then
  VERSION_SUMMARY="$(awk '/^# / { if (found) exit; found = 1; next } found { print }' CHANGELOG.md | sed -e '/./,$!d')"
fi

# Substitui os placeholders ${...} do template (substituição literal, sem envsubst).
while IFS= read -r line || [ -n "$line" ]; do
  line="${line//\$\{CONTRIBUTORS\}/$CONTRIBUTORS}"
  line="${line//\$\{DONATE_LINK\}/$DONATE_LINK}"
  line="${line//\$\{TAGS\}/$TAGS}"
  line="${line//\$\{TESTED_UP\}/$TESTED_UP}"
  line="${line//\$\{REQUIRES_PHP\}/$REQUIRES_PHP}"
  line="${line//\$\{STABLE_TAG\}/$STABLE_TAG}"
  line="${line//\$\{LICENSE\}/$LICENSE}"
  line="${line//\$\{LICENSE_URI\}/$LICENSE_URI}"
  line="${line//\$\{VERSION_SUMMARY\}/$VERSION_SUMMARY}"
  printf '%s\n' "$line"
done < "$TEMPLATE"
