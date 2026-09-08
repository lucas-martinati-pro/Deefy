# Tableau de bord des fonctionnalités — Projet Deefy

**Ressource :** Développement Web (BUT Informatique S3 — IUT Nancy-Charlemagne)

**Application :** Deefy (Plateforme de streaming audio)

---

## 📋 Tableau de bord des fonctionnalités

| N° | Fonctionnalité demandée | Description détaillée | Autorisation requise | Action / Classe | Statut |
|:--:|:---|:---|:---|:---|:---:|
| **1** | **Mes playlists** | Affiche la liste de toutes les playlists appartenant à l'utilisateur actuellement authentifié. | Utilisateur authentifié | `PlaylistsAction` | ✅ Réalisé |
| **2** | **Consulter une playlist** | Chaque élément de la liste est cliquable et affiche le détail de la playlist (pistes, métadonnées, lecteur audio) qui devient la playlist courante en session. | Utilisateur authentifié propriétaire | `DisplayPlaylistAction` | ✅ Réalisé |
| **3** | **Ajouter une piste** | Formulaire accessible depuis l'affichage d'une playlist pour ajouter une nouvelle piste (Album ou Podcast) avec upload sécurisé de fichier MP3. | Utilisateur authentifié propriétaire | `AddTrackAction` | ✅ Réalisé |
| **4** | **Créer une playlist vide** | Formulaire permettant de saisir le nom d'une playlist. À la validation, la playlist est créée en BD, rattachée à l'utilisateur et définie comme playlist courante. | Utilisateur authentifié | `AddPlaylistAction` | ✅ Réalisé |
| **5** | **Afficher la playlist courante** | Permet d'afficher directement la playlist actuellement mémorisée dans la session utilisateur. | Utilisateur authentifié propriétaire | `DisplayPlaylistAction` | ✅ Réalisé |
| **6** | **S'inscrire** | Création d'un compte utilisateur avec le rôle `STANDARD`, vérification de mot de passe complexe et option de connexion automatique. | Visiteur (Tous) | `RegisterAction` | ✅ Réalisé |
| **7** | **S'authentifier (Connexion)** | Authentification par email et mot de passe vérifié via hash sécurisé, initialisation de la session utilisateur. | Visiteur (Tous) | `Signin` | ✅ Réalisé |

---

## 🚀 Fonctionnalités complémentaires & Améliorations

### 1. Gestion de session et Déconnexion
- **Action de déconnexion (`Signout`)** : Page de confirmation de déconnexion, nettoyage des variables de session (`$_SESSION['user']`, `$_SESSION['playlist']`).
- **Connexion automatique post-inscription** : Checkbox cochée par défaut proposant d'authentifier directement l'utilisateur dès son inscription validée.

### 2. Interface Utilisateur & Ergonomie (Bootstrap 5)
- **Barre de navigation responsive** (`Dispatcher`) :
  - Adaptation dynamique selon le statut (connecté / déconnecté).
  - Accès direct aux fonctionnalités : Accueil, Mes playlists, Créer une playlist, Playlist courante, Se déconnecter.
  - Affichage de l'email de l'utilisateur connecté.
- **Formulaires accessibles et documentés** :
  - Classes Bootstrap.
  - Textes d'aide détaillant les contraintes (complexité des mots de passe).
  - Différenciation visuelle des actions principales (`btn-primary`), secondaires (`btn-secondary`) et les danger (`btn-danger`).

### 3. Sécurité & Robustesse
- **Politique de mot de passe stricte** :
  - Longueur minimale de 10 caractères.
  - Au moins une majuscule, une minuscule, un chiffre et un caractère spécial.
- **Contrôle d'accès & Autorisations (`Authz`)** :
  - Vérification de propriété des playlists (`Authz::checkPlaylistOwner`).
  - Support du rôle Administrateur (`role = 100`) pouvant consulter l'ensemble des playlists.
- **Sécurisation des uploads de fichiers** :
  - Validation du format MP3 obligatoire (`audio/mpeg` et extension `.mp3`).
  - Génération d'un nom de fichier aléatoire et unique (`uniqid() + random_bytes()`) pour éviter tout conflit ou écrasement.

### 4. Comception
- **Utilisation de Factory et du Pattern CQRS pour les repository**.