# Tableau de bord des fonctionnalités — Projet Deefy

**Ressource :** Développement Web (BUT Informatique S3 — IUT Nancy-Charlemagne)

**Application :** Deefy (Plateforme de streaming audio)

---

## 📋 Tableau de bord des fonctionnalités

| N° | Fonctionnalité demandée | Description détaillée | Autorisation requise | Action / Classe | Statut |
|:--:|:---|:---|:---|:---|:---:|
| **1** | **Mes playlists** | Affiche la liste de toutes les playlists appartenant à l'utilisateur actuellement authentifié sous forme de cartes modernes avec couvertures. | Utilisateur authentifié | `PlaylistsAction` | ✅ Réalisé |
| **2** | **Consulter une playlist** | Chaque élément de la liste est cliquable et affiche le détail de la playlist (pistes, métadonnées, lecteur audio) qui devient la playlist courante en session. | Utilisateur authentifié propriétaire | `DisplayPlaylistAction` | ✅ Réalisé |
| **3** | **Ajouter une piste** | Formulaire accessible depuis l'affichage d'une playlist pour ajouter une nouvelle piste (Album ou Podcast) avec upload sécurisé de fichier MP3 et extraction automatique des métadonnées ID3 (durée, genre, pochette). | Utilisateur authentifié propriétaire | `AddTrackAction` | ✅ Réalisé |
| **4** | **Créer une playlist vide** | Formulaire permettant de saisir le nom d'une playlist. À la validation, la playlist est créée en BD, rattachée à l'utilisateur et définie comme playlist courante. | Utilisateur authentifié | `AddPlaylistAction` | ✅ Réalisé |
| **5** | **Afficher la playlist courante** | Permet d'afficher directement la playlist actuellement mémorisée dans la session utilisateur. | Utilisateur authentifié propriétaire | `DisplayPlaylistAction` | ✅ Réalisé |
| **6** | **S'inscrire** | Création d'un compte utilisateur avec le rôle `STANDARD`, vérification de mot de passe complexe et option de connexion automatique. | Visiteur (Tous) | `RegisterAction` | ✅ Réalisé |
| **7** | **S'authentifier (Connexion)** | Authentification par email et mot de passe vérifié via hash sécurisé (`password_hash` / `password_verify`), initialisation de la session utilisateur. | Visiteur (Tous) | `SigninAction` | ✅ Réalisé |
| **8** | **Supprimer une playlist** | Suppression définitive d'une playlist et de ses associations en cascade (`playlist2track`, `user2playlist`), avec vérification des droits et nettoyage de la playlist courante en session si supprimée. | Utilisateur authentifié propriétaire | `DeletePlaylistAction` | ✅ Réalisé |
| **9** | **Supprimer une piste d'une playlist** | Retrait sécurisé d'une piste d'une playlist avec contrôle de propriété, recalcul de la durée et nettoyage du lecteur si le morceau supprimé était en cours d'écoute. | Utilisateur authentifié propriétaire | `DeleteTrackAction` | ✅ Réalisé |
| **10** | **Changement de thème (Dark / Light)** | Bouton toggle dans la barre de navigation permettant de basculer instantanément l'interface entre le mode sombre (*Dark*) et le mode clair (*Light*) avec adaptation dynamique de tous les composants et lecteurs. | Visiteur (Tous) | `script.js` / Bootstrap 5.3 Color Modes | ✅ Réalisé |

---

## 🚀 Fonctionnalités complémentaires & Améliorations

### 1. Lecteur audio persistant en pied de page (Style YouTube Music / Spotify)
- **Barre de lecture fixe (`Dispatcher`)** : Présente en bas de chaque page (`footer.fixed-bottom`), elle affiche la pochette du morceau, son titre, son artiste/album (ou auteur) et le lecteur audio web component moderne.
- **Découplage de la session audio (`$_SESSION['playerTrack']`)** : La piste en cours d'écoute est indépendante de la playlist courante consultée (`$_SESSION['playlist']`). L'utilisateur peut naviguer librement sur le site ou consulter d'autres playlists sans interrompre ni réinitialiser la lecture.
- **Lancement à la demande** : Bouton d'écoute disponible sur chaque piste pour la charger directement dans le lecteur persistant.

### 2. Gestion de session et Authentification avancée
- **Action de déconnexion (`SignoutAction`)** : Page de confirmation de déconnexion, nettoyage complet des variables de session (`$_SESSION['user']`, `$_SESSION['playlist']`, `$_SESSION['playerTrack']`).
- **Persistance prolongée de la session (7 jours)** : Configuration du temps de vie du cookie de session (`session_set_cookie_params` / `cookie_lifetime = 604800s`).
- **Connexion automatique post-inscription** : Checkbox cochée par défaut proposant d'authentifier directement l'utilisateur dès son inscription validée.
- **Redirection si déjà connecté** : Messages informatifs empêchant un utilisateur déjà authentifié de réaccéder inutilement aux formulaires de connexion ou d'inscription.

### 3. Interface Utilisateur, Thèmes & Ergonomie (Bootstrap 5 & JavaScript)
- **Bascule de thème Sombre / Clair dynamique (`script.js`)** :
  - Bouton toggle dans la navbar avec icône demi-cercle (`bi-circle-half`).
  - Exploitation du système de modes de couleur natif de Bootstrap 5.3 (`data-bs-theme="dark"` / `data-bs-theme="light"`).
  - Adaptation dynamique de la navbar (`.navbar`), de la barre audio (`.audio-footer-bar`), des lecteurs (`.audio-footer`, `.audio-compact`, `.audio-long`) et des contrastes de texte via les variables CSS (`var(--bs-*)`).
- **Barre de navigation responsive** :
  - Adaptation dynamique selon le statut (connecté / déconnecté).
  - Accès direct aux fonctionnalités : Accueil, Mes playlists, Créer une playlist, Playlist courante, Se déconnecter.
  - Affichage de l'email de l'utilisateur connecté avec icône de profil.
- **Intégration de Bootstrap Icons** :
  - Icônes contextuelles sur chaque action, bouton, élément de menu et badge de métadonnées.
- **Présentation moderne en cartes** :
  - Grille responsive pour les playlists et les pistes audio avec pochettes réelles, badges de type (Album / Podcast), numéro de piste et durée formatée.
- **Centralisation des alertes et retours visuels (`HtmlHelper`)** :
  - Utilitaire pour générer des messages et alertes Bootstrap standardisés (`success`, `danger`, `warning`, `info`) avec titres, messages d'état et boutons d'action configurables via les arguments nommés de PHP 8.

### 4. Sécurité & Robustesse
- **Politique de mot de passe stricte** :
  - Longueur minimale de 10 caractères.
  - Au moins une majuscule, une minuscule, un chiffre et un caractère spécial.
- **Contrôle d'accès & Autorisations (`Authz`)** :
  - Vérification de propriété des playlists (`Authz::checkPlaylistOwner`) pour la consultation, l'ajout de pistes et les suppressions.
  - Support du rôle Administrateur (`role = 100`) pouvant gérer l'ensemble des contenus.
- **Sécurisation des uploads de fichiers audio et images** :
  - Validation du format MP3 (`audio/mpeg` et extension `.mp3`) et des formats d'images autorisés (`image/jpeg`, `image/png`, etc.).
  - Génération de noms de fichiers aléatoires et uniques (`uniqid() + random_bytes()`) pour prévenir les collisions et écrasements.

### 5. Extraction automatique des métadonnées audio (getID3)
- **Analyse automatique des fichiers MP3 (`AddTrackAction`)** :
  - Intégration de la bibliothèque `james-heinrich/getid3`.
  - Extraction automatique de la **durée exacte** du morceau en secondes (évite la saisie manuelle).
  - Récupération du **genre musical**, de l'artiste, de l'album et des tags ID3v2/ID3v1 avec utilisation des valeurs saisies comme solution de secours.
  - Extraction et sauvegarde automatique de la **pochette intégrée** au fichier audio si présente.

### 6. Architecture & Conception logicielle
- **Pattern CQRS (Command Query Responsibility Segregation)** : Séparation claire entre les requêtes de lecture et les commandes d'écriture dans le repository.
- **Factory Pattern** : Création d'objets et gestion des instances audio.
- **Renderer orienté objet & polymorphisme** : Hiérarchie de classes (`AudioTrackRenderer`, `AlbumTrackRenderer`, `PodcastTrackRenderer`, `AudioListRenderer`) avec surcharge des méthodes de rendu selon le contexte (compact ou long).