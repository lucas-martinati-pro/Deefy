# Mini-Projet Deefy

## Installation des dépendances

Le fichier `composer.json` se trouve dans le dossier `src/`. Pour créer le
dossier `vendor/` avec les versions prévues par le projet :

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