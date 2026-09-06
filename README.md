# Mini-Projet Deefy

## Installation des dépendances

Le fichier `composer.json` se trouve dans le dossier `src/`. Pour créer le
dossier `vendor/` avec les versions prévues par le projet :

```bash
cd src
composer install
```

Le dossier `vendor/` est généré automatiquement et ne doit pas être versionné.

## Mise à jour des dépendances

Pour rechercher de nouvelles versions et mettre à jour `composer.lock` :

```bash
cd src
composer update
```