#!/bin/bash

# Script de renouvellement SSL Let's Encrypt pour jouonsensemble.org
# À utiliser dans une tâche cron

set -e  # Arrêter en cas d'erreur

DOMAIN="jouonsensemble.org"
LOG_FILE="/var/log/certbot-renewal.log"
DATE=$(date '+%Y-%m-%d %H:%M:%S')

echo "[$DATE] Début du renouvellement SSL pour $DOMAIN" >> "$LOG_FILE"

# Vérifier que certbot est installé
if ! command -v certbot &> /dev/null; then
    echo "[$DATE] ERREUR : certbot n'est pas installé" >> "$LOG_FILE"
    exit 1
fi

# Vérifier que nginx est actif
if ! systemctl is-active --quiet nginx; then
    echo "[$DATE] ERREUR : nginx n'est pas actif" >> "$LOG_FILE"
    exit 1
fi

# Tentative de renouvellement
if certbot renew --quiet --deploy-hook "systemctl reload nginx" >> "$LOG_FILE" 2>&1; then
    echo "[$DATE] Renouvellement SSL réussi ou pas nécessaire" >> "$LOG_FILE"
else
    echo "[$DATE] ERREUR lors du renouvellement SSL" >> "$LOG_FILE"
    exit 1
fi

# Vérifier la validité du certificat (optionnel)
if openssl x509 -in "/etc/letsencrypt/live/$DOMAIN/cert.pem" -noout -checkend 2592000 > /dev/null 2>&1; then
    echo "[$DATE] Certificat valide pour au moins 30 jours" >> "$LOG_FILE"
else
    echo "[$DATE] ATTENTION : Certificat expire bientôt !" >> "$LOG_FILE"
fi

echo "[$DATE] Fin du renouvellement SSL" >> "$LOG_FILE"