# Tableau de bord des fonctionnalités — Projet Deefy

**Ressource :** Développement Web (BUT Informatique S3 — IUT Nancy-Charlemagne)

**Application :** Deefy (Plateforme de streaming audio)

---

## 📋 Tableau de bord des fonctionnalités

| N° | Fonctionnalité demandée | Description | Autorisation requise | Action / Classe | Statut |
|:--:|:---|:---|:---|:---|:---:|
| **1** | **Mes playlists** | Affiche les playlists de l'utilisateur connecté sous forme de cartes. | Utilisateur authentifié | `PlaylistsAction` | ✅ Réalisé |
| **2** | **Consulter une playlist** | Affiche le détail et les pistes d'une playlist, qui devient la playlist courante. | Utilisateur authentifié propriétaire | `DisplayPlaylistAction` | ✅ Réalisé |
| **3** | **Ajouter une piste** | Formulaire d'ajout d'une piste (Album ou Podcast) avec upload MP3 et extraction automatique des métadonnées (durée, genre, pochette). | Utilisateur authentifié propriétaire | `AddTrackAction` | ✅ Réalisé |
| **4** | **Créer une playlist vide** | Formulaire pour créer une nouvelle playlist rattachée à l'utilisateur et la définir comme playlist courante. | Utilisateur authentifié | `AddPlaylistAction` | ✅ Réalisé |
| **5** | **Afficher la playlist courante** | Affiche directement la playlist actuellement mémorisée en session. | Utilisateur authentifié propriétaire | `DisplayPlaylistAction` | ✅ Réalisé |
| **6** | **S'inscrire** | Création de compte avec mot de passe sécurisé et connexion automatique optionnelle. | Visiteur (Tous) | `RegisterAction` | ✅ Réalisé |
| **7** | **S'authentifier (Connexion)** | Connexion par email et mot de passe vérifié par hash sécurisé (`password_hash`). | Visiteur (Tous) | `SigninAction` | ✅ Réalisé |
| **8** | **Supprimer une playlist** | Suppression complète d'une playlist et de ses liaisons avec contrôle de propriété. | Utilisateur authentifié propriétaire | `DeletePlaylistAction` | ✅ Réalisé |
| **9** | **Supprimer une piste d'une playlist** | Retrait d'une piste d'une playlist avec recalcul de la durée et retrait du lecteur si elle était en cours d'écoute. | Utilisateur authentifié propriétaire | `DeleteTrackAction` | ✅ Réalisé |
| **10** | **Changement de thème (Dark / Light)** | Bouton dans la barre de navigation permettant de basculer instantanément entre mode sombre et mode clair. | Visiteur (Tous) | `script.js` / Bootstrap 5.3 | ✅ Réalisé |

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
- **Thème Sombre / Clair** : Bascule dynamique avec adaptation automatique de tous les composants et des lecteurs audio.
- **Barre de navigation responsive** : Menu adapté au statut de connexion (avec affichage de l'email du compte connecté).
- **Affichage moderne en cartes** : Présentation visuelle claire avec pochettes, badges (Album / Podcast) et durée formatée.
- **Gestion uniforme des alertes (`HtmlHelper`)** : Retours visuels clairs et cohérents (succès, erreurs, confirmations).

### 4. Sécurité & Fichiers
- **Exigences de mot de passe** : 10 caractères minimum avec majuscule, minuscule, chiffre et caractère spécial.
- **Contrôle d'accès (`Authz`)** : Vérification stricte que l'utilisateur est bien propriétaire des playlists qu'il consulte ou modifie (avec support du rôle Administrateur).
- **Upload sécurisé** : Contrôle des types MIME (audio MP3, images) et génération de noms de fichiers uniques pour éviter les écrasements.

### 5. Extraction automatique des métadonnées (getID3)
- **Analyse automatique des fichiers MP3** : Détection et remplissage automatique de la durée exacte, du genre musical et extraction de la pochette intégrée dans le fichier audio.