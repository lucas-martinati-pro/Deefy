<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class Signin extends Action {
    public function get() : string {
        return <<<HTML
        <form method="post" action="?action=signin" enctype="multipart/form-data">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <button type="submit">Connexion</button>
        </form>
        HTML;
    }

    public function post() : string {
        if (!isset($_POST['email'], $_POST['password'])) {
            return <<<HTML
                <h1>Échec de la connexion</h1>
                <p>Tous les champs sont obligatoires.</p>
                <a href="?action=signin">Retour au formulaire</a>
            HTML;
        }

        try {
            AuthnProvider::signin($_POST['email'], $_POST['password']);
        } catch (AuthnException $error) {
            return <<<HTML
                <h1>Échec de la connexion</h1>
                <p>{$error->getMessage()}</p>
                <a href="?action=signin">Retour au formulaire</a>
            HTML;
        }

        $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        return <<<HTML
            <h1>Connexion réussie</h1>
            <p>Bienvenue, <strong>{$_POST['email']}</strong> !</p>
            <p>Vous êtes maintenant connecté à Deefy.</p>
        HTML;
    }
}