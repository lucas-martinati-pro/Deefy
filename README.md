# Mini-Projet Deefy

Application web de streaming et de gestion de playlists audio développée en PHP (Architecture MVC / Actions, Bootstrap 5, Bootswatch, MySQL).

---

## 👥 Comptes utilisateurs de test (Base de données)

Le script d'initialisation de la base de données se trouve dans [`sql/deefy_db.sql`](sql/deefy_db.sql). Les comptes suivants sont préconfigurés et immédiatement utilisables pour tester l'application :

| Email | Mot de passe | Rôle | Playlists associées |
|:---|:---:|:---:|:---|
| `user1@mail.com` | `user1` | Standard (`1`) | * *Hardcore & Frenchcore* (2 pistes)<br>* *Hardstyle & Uptempo* (1 piste) |
| `user2@mail.com` | `user2` | Standard (`1`) | * *Frenchcore & Soirée* (3 pistes) |
| `user3@mail.com` | `user3` | Standard (`1`) | * *Remixes & Bootlegs* (2 pistes) |
| `user4@mail.com` | `user4` | Standard (`1`) | *Aucune playlist* |
| `admin@mail.com` | `admin` | Administrateur (`100`) | *Accès global* |

> Vous pouvez également créer un nouveau compte à tout moment via la page d'inscription (`?action=register`). Une politique de sécurité exige alors un mot de passe robuste (minimum 10 caractères avec majuscule, minuscule, chiffre et caractère spécial).

---

## Installation des dépendances

Le fichier `composer.json` se trouve dans le dossier `src/`. Pour créer le dossier `vendor/` avec les versions prévues par le projet :

```bash
cd src
composer install
```

Le dossier `vendor/` est généré automatiquement et ne doit pas être versionné.

## Ajout de la bibliothèque getID3 (Analyse métadonnées audio)

Si la dépendance n'est pas encore présente dans votre environnement ou si vous devez l'installer manuellement :

```bash
cd src
composer require james-heinrich/getid3
```

Cette bibliothèque permet d'extraire automatiquement la durée, le genre et la pochette d'album depuis les fichiers MP3 uploadés.

## Mise à jour des dépendances

Pour rechercher de nouvelles versions et mettre à jour `composer.lock` :

```bash
cd src
composer update
```