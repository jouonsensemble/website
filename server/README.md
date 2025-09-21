# Configuration serveur

## Installation de la configuration Nginx

```bash
# Copier la configuration
sudo cp nginx.conf /etc/nginx/sites-available/jouonsensemble.org

# Activer le site
sudo ln -s /etc/nginx/sites-available/jouonsensemble.org /etc/nginx/sites-enabled/

# Tester la configuration
sudo nginx -t

# Recharger nginx
sudo systemctl reload nginx
```

## Prérequis

1. **Nginx 1.22+** installé
2. **Dossier web** : `/var/www/jouonsensemble.org`
3. **Permissions** : `deploy:www-data` sur le dossier web
4. **DNS** : jouonsensemble.org et www.jouonsensemble.org pointent vers le serveur

## Après installation

1. **Obtenir le certificat SSL** :
   ```bash
   sudo certbot --nginx -d jouonsensemble.org -d www.jouonsensemble.org
   ```

2. **Configurer le renouvellement automatique** :
   ```bash
   # Copier le script de renouvellement
   sudo cp renew-ssl.sh /usr/local/bin/
   sudo chmod +x /usr/local/bin/renew-ssl.sh

   # Ajouter la tâche cron
   sudo crontab -e
   ```

   Ajouter cette ligne dans le crontab :
   ```bash
   0 2 1 */2 * /usr/local/bin/renew-ssl.sh
   ```

3. **Tester le script** :
   ```bash
   sudo /usr/local/bin/renew-ssl.sh
   # Vérifier les logs
   sudo tail -f /var/log/certbot-renewal.log
   ```

## Fonctionnalités incluses

- ✅ Redirection HTTP → HTTPS
- ✅ Support HTTP/2
- ✅ Configuration SSL moderne (TLS 1.2/1.3)
- ✅ En-têtes de sécurité (HSTS, X-Frame-Options, etc.)
- ✅ Compression gzip
- ✅ Cache optimisé pour les assets statiques
- ✅ Logs séparés par domaine