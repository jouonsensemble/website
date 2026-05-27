#!/bin/bash

set -e

# Validation stricte du projet - sécurité
PROJECT_NAME="jouonsensemble"
ALLOWED_DEPLOY_DIR="/var/www/jouonsensemble.org"
ALLOWED_REPO="git@github.com:jouonsensemble/website.git"

# Vérifier que le script a le bon nom
SCRIPT_NAME=$(basename "$0")
if [[ "$SCRIPT_NAME" != "deploy-jouonsensemble.sh" ]]; then
    echo "❌ Erreur : ce script doit s'appeler deploy-jouonsensemble.sh"
    exit 1
fi

# Vérifier les variables d'environnement si disponibles
if [[ -n "$GITHUB_REPOSITORY" ]] && [[ "$GITHUB_REPOSITORY" != "jouonsensemble/website" ]]; then
    echo "❌ Erreur : déploiement autorisé uniquement pour jouonsensemble/website"
    exit 1
fi

REPO_URL="git@github.com:jouonsensemble/website.git"
DEPLOY_DIR="$ALLOWED_DEPLOY_DIR"
BRANCH="main"
GIT_SSH_COMMAND_VALUE="ssh -i ~/.ssh/jouonsensemble"

echo "🚀 Début du déploiement..."

if [ ! -d "$DEPLOY_DIR" ] || [ ! -d "$DEPLOY_DIR/.git" ]; then
    echo "📥 Premier déploiement - Clone du repository..."

    # Si le dossier existe mais n'est pas un repo git, le vider
    if [ -d "$DEPLOY_DIR" ]; then
        echo "🧹 Nettoyage du dossier existant..."
        rm -rf "$DEPLOY_DIR"/* "$DEPLOY_DIR"/.* 2>/dev/null || true
    fi

    GIT_SSH_COMMAND="$GIT_SSH_COMMAND_VALUE" git clone "$REPO_URL" "$DEPLOY_DIR"
    cd "$DEPLOY_DIR"
else
    echo "🔄 Mise à jour du code..."
    cd "$DEPLOY_DIR"
    GIT_SSH_COMMAND="$GIT_SSH_COMMAND_VALUE" git fetch origin
    git reset --hard origin/$BRANCH
    GIT_SSH_COMMAND="$GIT_SSH_COMMAND_VALUE" git pull origin $BRANCH
fi

echo "📦 Installation des dépendances Composer (mode production)..."
if ! command -v composer >/dev/null 2>&1; then
    echo "❌ Composer est introuvable dans le PATH. Abandon."
    exit 1
fi

composer install \
    --no-dev \
    --optimize-autoloader \
    --prefer-dist \
    --no-progress \
    --no-interaction

echo "🔐 Configuration des permissions..."
chown -R deploy:www-data "$DEPLOY_DIR" 2>/dev/null || echo "⚠️  Permissions déjà correctes"
chmod -R 755 "$DEPLOY_DIR"
find "$DEPLOY_DIR" -type f -exec chmod 644 {} \;

echo "✅ Déploiement terminé avec succès !"
echo "📍 Site accessible sur : https://www.jouonsensemble.org"
