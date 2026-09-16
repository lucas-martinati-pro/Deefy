# Tableau de bord des fonctionnalités — Projet Deefy

**Ressource :** Développement Web (BUT Informatique S3 — IUT Nancy-Charlemagne)

**Application :** Deefy (Plateforme de streaming audio)

---

## 🚀 Fonctionnalités complémentaires & Améliorations

### 1. Lecteur audio persistant en pied de page
Barre de lecture fixe en bas de l'écran qui permet d'écouter un morceau tout en naviguant librement sur le site :
- **Lancement à la demande** : Bouton « Lire » sur chaque piste pour l'envoyer directement dans le lecteur (`$_SESSION['playerTrack']`).
- **Fermeture du lecteur** : Bouton croix permettant d'arrêter la lecture et de retirer la piste de la session sans recharger ni changer de page.
- **Lecture continue** : La lecture ne s'interrompt pas lors de la navigation ou de la consultation d'autres playlists.

### 2. Gestion de session & Authentification
- **Déconnexion propre (`SignoutAction`)** : Page de confirmation et nettoyage complet de la session (utilisateur, playlist et lecteur).
- **Connexion automatique** : Option cochée par défaut à l'inscription pour connecter l'utilisateur immédiatement.
- **Navigation sécurisée** : Redirection et messages d'information si un utilisateur connecté tente de revenir sur la page de connexion ou d'inscription.

### 3. Interface & Ergonomie (Bootstrap 5 & JavaScript)
- **Thème Sombre / Clair persistant** : Bascule dynamique en JavaScript et mémorisation du choix par cookie.
- **Barre de navigation responsive** : Menu adapté au statut de connexion (avec affichage de l'email du compte connecté).
- **Affichage moderne en cartes** : Présentation visuelle claire avec pochettes, badges (Album / Podcast) et durée formatée.
- **Gestion uniforme des alertes (`HtmlHelper`)** : Retours visuels clairs et cohérents (succès, erreurs, confirmations).

### 4. Sécurité & Fichiers
- **Exigences de mot de passe** : 10 caractères minimum avec majuscule, minuscule, chiffre et caractère spécial.
- **Contrôle d'accès (`Authz`)** : Vérification stricte que l'utilisateur est bien propriétaire des playlists qu'il consulte ou modifie (avec support du rôle Administrateur).
- **Upload sécurisé** : Contrôle des types MIME (audio MP3, images) et génération de noms de fichiers uniques pour éviter les écrasements.

### 5. Extraction automatique des métadonnées (getID3)
- **Analyse automatique des fichiers MP3** : Détection et remplissage automatique de la durée exacte, du genre musical et extraction de la pochette intégrée dans le fichier audio.

### 6. Pistes personnelles & Gestion des suppressions
- **Page « Mes pistes »** : La page affiche toutes les pistes de l'utilisateur.
- **Ajout sans playlist** : L'utilisateur peut ajouter une piste à ses morceaux sans choisir de playlist.
- **Ajout vers une playlist** : L'utilisateur peut ajouter une piste directement dans une playlist précise.
- **Retrait d'une playlist** : Le bouton retire la piste de la playlist sans la supprimer.
- **Suppression définitive** : Le bouton supprime la piste de la bibliothèque et de toutes les playlists.