# Jouons Ensemble - Site Web

Site web statique pour la ludothèque "Jouons Ensemble" de Vernou-la-Celle-sur-Seine.

## 🚀 Déploiement

Le déploiement se fait automatiquement via GitHub Actions lors d'un push sur la branche `main`.

### Configuration requise sur le serveur

1. Utilisateur `deploy` avec accès SSH
2. Dossier `/var/www/jouonsensemble.org` avec les bonnes permissions
3. Script `deploy-jouonsensemble.sh` dans `/home/deploy/`

## 🔒 Certificat SSL avec Let's Encrypt

### Installation de Certbot

```bash
# Sur Ubuntu/Debian
sudo apt update
sudo apt install certbot python3-certbot-nginx

# Sur CentOS/RHEL
sudo yum install certbot python3-certbot-nginx
```

### Configuration automatique avec Nginx

```bash
# Obtenir et configurer le certificat automatiquement
sudo certbot --nginx -d jouonsensemble.org -d www.jouonsensemble.org

# Ou juste le domaine principal
sudo certbot --nginx -d jouonsensemble.org
```

### Renouvellement (sans API DNS)

**Méthode HTTP-01** (via serveur web, pas besoin d'API DNS) :

```bash
# Tester le renouvellement
sudo certbot renew --dry-run

# Renouvellement manuel tous les 2-3 mois
sudo certbot renew --quiet && sudo systemctl reload nginx
```

**Ou avec cron pour tentative automatique** :
```bash
# Cron job (fonctionne si le serveur web est accessible)
sudo crontab -e
```
Ajoutez :
```bash
0 2 1 */2 * /usr/bin/certbot renew --quiet && systemctl reload nginx
```

**Note** : Le renouvellement HTTP-01 fonctionne tant que votre serveur web est accessible sur le port 80.

### Configuration manuelle (si nécessaire)

Si vous préférez configurer manuellement :

```bash
# Obtenir seulement le certificat
sudo certbot certonly --webroot -w /var/www/jouonsensemble.org -d jouonsensemble.org
```

Puis configurez nginx manuellement avec les certificats générés.

## 🛠️ Configuration du serveur

Voir le dossier `server/` pour les fichiers de configuration.

## 📱 Fonctionnalités

- Affichage des derniers jeux de la ludothèque via API MyLudo
- Design responsive avec Tailwind CSS
- Informations pratiques (horaires, événements, adhésion)
- Intégration des réseaux sociaux

## 🔧 Développement local

```bash
docker compose up
```
