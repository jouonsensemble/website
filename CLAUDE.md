# Site Web - Ludothèque Jouons Ensemble

## Informations générales

**Association :** Jouons Ensemble
**Localisation :** Vernou-la-Celle-sur-Seine (77670)
**Type :** Ludothèque associative avec plus de 600 jeux
**Adresse :** 4 rue des écoles, 77670 Vernou-la-Celle-sur-Seine (à côté de la Médiathèque)

## Structure du projet

### Technologies utilisées
- **HTML5** - Page unique `index.html`
- **Tailwind CSS** - Via CDN (`https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4`)
- **CSS personnalisé** - `assets/css/custom.css` (minimaliste)
- **Polices Google** - Poppins (primaire) et Merriweather (titres)
- **Aucun JavaScript** - Site statique pur

### Architecture des fichiers
```
/
├── index.html (page principale)
├── logo_200.png (logo de l'association)
├── assets/
│   ├── css/
│   │   ├── custom.css (styles personnalisés)
│   │   ├── font-awesome/ (icônes v6)
│   └── images/
│       ├── ludotheque.jpg (background header)
│       └── facebook.png
└── CLAUDE.md (ce fichier)
```

## Contenu du site

### 1. Header
- Logo circulaire (Jouons Ensemble)
- Navigation : Catalogue, Événements, Adhésion
- Background avec image `ludotheque.jpg` (opacité 10%)
- **Navigation mobile non implémentée** (pas de menu burger)

### 2. Section Accueil
- Titre H1 avec bannière
- Description de l'association (600+ jeux)
- Call-to-action vers événements et adhésion

### 3. Section Catalogue (#catalogue)
- Description de la collection
- **12 derniers jeux ajoutés** (récupérés via API MyLudo)
- Grid responsive : 2/4/6 colonnes selon écran
- Liens vers fiches MyLudo : `https://www.myludo.fr/#!/game/CODE-ID`
- Bouton vers collection complète MyLudo

### 4. Section Événements (#evenements)
- **Horaires réguliers** (hors vacances scolaires) :
  - Vendredis soir : 20h (soirées jeux)
  - Samedis matin : 9h-11h (prêts et jeux sur place)
- **3 types d'animations spéciales** (3 cards flexbox) :
  - Brunchs Ludiques (dimanches 12h) - 6 dates
  - Soirées Stratégie (samedis 20h) - 4 dates
  - Soirées Famille (samedis 20h) - 4 dates
- **Calendrier Google** intégré via iframe

### 5. Section Adhésion (#adhesion)
- Card centrée pour adhésion 2025-2026
- Lien HelloAsso : `https://www.helloasso.com/associations/jouons-ensemble-77670/adhesions/adhesion-2025-2026`
- Lien Facebook : `https://www.facebook.com/JouonsEnsemble77/`

### 6. Footer
- Logo + nom association
- Adresse complète
- Design minimaliste (2 colonnes flexbox)

## API MyLudo

### Endpoint collection
```
https://www.myludo.fr/views/profil/datas.php?type=collection&id=38350&page=1&limit=18&filter=&family=&availability=&storage=&words=&players=&age=&duration=&context=&order=bydatedesc&_=1758461802602
```

### Headers requis
- User-Agent, Accept, Referer, X-Csrf-Token, Cookie, etc.
- Voir commande curl dans l'historique pour headers complets

### Format liens jeux
- Pattern : `https://www.myludo.fr/#!/game/CODE-ID`
- Exemple : `https://www.myludo.fr/#!/game/spring-festival-74796`

## Design et styles

### Couleurs (template Pinwheel)
- **Primaire** : #FE6019 (orange)
- **Secondaire** : #FEE140 (jaune) - non utilisée
- **Texte** : #888888 (gris)
- **Titres** : #222 (gris foncé)
- **Background** : #fafafa (gris très clair)
- **Footer** : #1a202c (gris très foncé)

### Polices
- **Corps** : Poppins (Google Fonts)
- **Titres** : Merriweather (Google Fonts)

### Composants CSS personnalisés
- `.container` (max-width: 1202px)
- `.section` (padding vertical)
- `.btn` et `.btn-primary`
- `.card` (background blanc + shadow)
- `.row` (flexbox avec padding)
- `.footer` (avec override couleur titres)

## Contenu dynamique

### Derniers jeux (12 items)
Récupérés depuis l'API MyLudo, tri par date d'ajout décroissant :
1. Spring Festival (74796)
2. Mission Super Pas Possible ! (59944)
3. Pandaï (24703)
4. Dékal (76176)
5. Qwixx (83309)
6. Bellevue (84154)
7. Namiji (56084)
8. Harmonies (75080)
9. Patchwork Express (29587)
10. Danger (83866)
11. The Gang (79285)
12. Ptit Pois (84721)

### Événements 2025/2026
**Brunchs Ludiques :**
- 12 octobre, 16 novembre, 18 janvier, 15 février, 12 avril, 17 mai

**Soirées Stratégie :**
- 4 octobre, 8 novembre, 7 février, 14 mars

**Soirées Famille :**
- 13 décembre, 10 janvier, 4 avril, 23 mai

## Décisions techniques importantes

1. **Pas de compilation** - Tailwind via CDN, CSS direct
2. **Pas de JavaScript** - Site 100% statique
3. **Responsive avec Flexbox** - Pas de CSS Grid complexe
4. **Images externes** - MyLudo pour les jeux, Google Calendar iframe
5. **Navigation simple** - Pas de menu mobile hamburger

## Liens importants

- **Collection MyLudo** : https://www.myludo.fr/#!/profil/jouons-ensemble-38350
- **Facebook** : https://www.facebook.com/JouonsEnsemble77/
- **HelloAsso** : https://www.helloasso.com/associations/jouons-ensemble-77670/adhesions/adhesion-2025-2026
- **Calendrier** : https://calendar.google.com/calendar/embed?src=jouonsensemble77670%40gmail.com&ctz=Europe%2FParis

## Notes de maintenance

- **Mise à jour jeux** : Utiliser l'API MyLudo avec la commande curl fournie
- **Modification événements** : Mettre à jour les dates dans les 3 cards d'animations
- **Responsive** : Testé sur mobile/desktop, navigation simple sans menu burger
- **Performance** : Minimal, pas de JavaScript, images optimisées MyLudo

## Template d'origine

Basé sur **Pinwheel Tailwind** avec adaptations :
- Couleurs conservées (#FE6019, #FEE140)
- Polices conservées (Poppins/Merriweather)
- Structure simplifiée pour ludothèque
- Suppression des éléments non utilisés