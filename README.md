# Mini-Projet Deefy

Application web de streaming audio. Elle est codée en PHP. Elle utilise Bootstrap 5 et MySQL.

---

## 👥 Comptes utilisateurs de test

La base de données est dans [`sql/deefy_db.sql`](sql/deefy_db.sql). Les comptes sont prêts pour tester.

| Email | Mot de passe | Rôle | Playlists associées |
|:---|:---:|:---:|:---|
| `user1@mail.com` | `user1` | Standard (`1`) | * *Hardcore & Frenchcore* (3 pistes)<br>* *Hardstyle & Uptempo* (5 pistes) |
| `user2@mail.com` | `user2` | Standard (`1`) | * *Frenchcore & Soirée* (3 pistes) |
| `user3@mail.com` | `user3` | Standard (`1`) | * *Remixes & Bootlegs* (3 pistes) |
| `user4@mail.com` | `user4` | Standard (`1`) | *Aucune playlist* |
| `admin@mail.com` | `admin` | Administrateur (`100`) | *Accès global* |

Vous pouvez créer un compte sur la page d'inscription (`?action=register`). Le mot de passe doit faire 10 caractères minimum. Il faut une majuscule, une minuscule, un chiffre et un caractère spécial.

---

## 🎵 Morceaux et playlists préconfigurés

La base contient 14 pistes. Les fichiers audio sont dans `audio/`. Les pochettes sont dans `image/covers/`. Il y a 4 playlists.

| # | Titre | Artiste(s) | Genre | Durée | Playlist | Utilisateur |
|:---:|:---|:---|:---|:---:|:---|:---|
| 1 | BARBE NOIRE | VIELUSOS | Hardcore | 2:33 | Hardcore & Frenchcore | `user1` |
| 2 | Influenceur (Hard Version) | Dr. Peacock, ascendant vierge | Frenchcore | 3:40 | Hardcore & Frenchcore | `user1` |
| 10 | Tree of Life | Billx | Frenchcore | 5:13 | Hardcore & Frenchcore | `user1` |
| 3 | Kino Der Toten | VIELUSOS | Hardcore | 3:01 | Hardstyle & Uptempo | `user1` |
| 4 | LOUDERRR | RAGETRAIN | Hardstyle | 4:43 | Hardstyle & Uptempo | `user1` |
| 9 | Aria (Hard Techno Edit) | Sandro Cardio, GEWOONRAVES | Hard Techno | 4:34 | Hardstyle & Uptempo | `user1` |
| 11 | Lord Of Chaos | Fantasm | Hard Techno | 2:36 | Hardstyle & Uptempo | `user1` |
| 12 | Ameno (Techno Edit) | Sandro Cardio, GEWOONRAVES, Zentryc, Zensory | Hard Techno | 2:31 | Hardstyle & Uptempo | `user1` |
| 5 | La Strasbourgeoise | Vernex, Toxic Twins, Stirex | Frenchcore | 4:46 | Frenchcore & Soirée | `user2` |
| 6 | MONGOL | vernex | Hardcore | 2:35 | Frenchcore & Soirée | `user2` |
| 14 | AMOUR LÂCHE | helen ka, vernex | Frenchcore | 3:42 | Frenchcore & Soirée | `user2` |
| 7 | Mexico en Janvier (Lushe Remix) | Lushe, Bigflo & Oli | Remix | 2:03 | Remixes & Bootlegs | `user3` |
| 8 | Pennywise (Deadly Guns Remix) | Angerfist, Deadly Guns | Hardcore | 2:32 | Remixes & Bootlegs | `user3` |
| 13 | PROZACZOPIXAN RELOADED | Vald, Vladimir Cauchemar, Todiefor | Remix | 2:14 | Remixes & Bootlegs | `user3` |

---

## Installation des dépendances

Le fichier `composer.json` est dans `src/`. Pour installer le dossier `vendor/` :

```bash
cd src
composer install
```

Le dossier `vendor/` est créé seul. Il ne faut pas le versionner.

## Ajout de la bibliothèque getID3

Si la librairie manque, installez-la à la main :

```bash
cd src
composer require james-heinrich/getid3
```

La librairie lit les fichiers MP3. Elle donne la durée, le genre et la pochette.

## Mise à jour des dépendances

Pour mettre à jour `composer.lock` :

```bash
cd src
composer update
```
