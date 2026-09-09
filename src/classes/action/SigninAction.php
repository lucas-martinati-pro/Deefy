<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class SigninAction extends Action {
    public function get() : string {
        $require = '<span class="text-danger">*</span>';
        return <<<HTML
        <form method="post" action="?action=signin" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="email" class="form-label">Adresse mail$require</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="nom@exemple.com" aria-describedby="emailHelp" required>
                <div id="emailHelp" class="form-text">Saisissez l'adresse e-mail associée à votre compte Deefy.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe$require</label>
                <input type="password" name="password" class="form-control" id="password" aria-describedby="passwordHelp" required>
                <div id="passwordHelp" class="form-text">Saisissez le mot de passe associé à votre compte.</div>
            </div>
            <button type="submit" class="btn btn-primary">Connexion</button>
        </form>
        HTML;
    }

    public function post() : string {
        if (!isset($_POST['email'], $_POST['password'])) {
            return <<<HTML
                <h1>Échec de la connexion</h1>
                <p>Tous les champs sont obligatoires.</p>
                <p><a class="btn btn-secondary" href="?action=signin">Retour au formulaire</a></p>
            HTML;
        }

        try {
            AuthnProvider::signin($_POST['email'], $_POST['password']);
        } catch (AuthnException $error) {
            return <<<HTML
                <h1>Échec de la connexion</h1>
                <p>{$error->getMessage()}</p>
                <p><a class="btn btn-secondary" href="?action=signin">Retour au formulaire</a></p>
            HTML;
        }

        $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        return <<<HTML
            <h1>Connexion réussie</h1>
            <p>Bienvenue, <strong>{$_POST['email']}</strong> !</p>
            <p>Vous êtes maintenant connecté à Deefy.</p>
            <p>
                <a class="btn btn-primary" href="?action=playlists">Mes playlists</a>
                <a class="btn btn-secondary" href="main.php">Accueil</a>
            </p>
        HTML;
    }
}