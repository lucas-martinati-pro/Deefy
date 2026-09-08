<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class RegisterAction extends Action {

    #[\Override]
    public function get() : string {
        return <<<HTML
        <form method="post" action="?action=register" enctype="multipart/form-data">
            <input type="text" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="password" name="password-double" placeholder="Ressaisissez le mot de passe" required>
            <button type="submit">Inscription</button>
        </form>
        HTML;
    }

    #[\Override]
    public function post() : string {
        if (!isset($_POST['email'], $_POST['password'], $_POST['password-double'])) {
            return <<<HTML
                <h1>Échec de l'inscription</h1>
                <p>Tous les champs sont obligatoires.</p>
                <a href="?action=register">Retour au formulaire</a>
            HTML;
        }

        if ($_POST['password'] !== $_POST['password-double']) {
            return <<<HTML
                <h1>Échec de l'inscription</h1>
                <p>Les deux mots de passe doivent être identiques.</p>
                <a href="?action=register">Retour au formulaire</a>
            HTML;
        }
        try {
            AuthnProvider::register($_POST['email'], $_POST['password']);
        } catch (AuthnException $error) {
            return <<<HTML
                <h1>Échec de l'inscription</h1>
                <p>{$error->getMessage()}</p>
                <a href="?action=register">Retour au formulaire</a>
            HTML;
        }

        return <<<HTML
            <h1>Inscription réussie</h1>
            <p>Votre compte a bien été créé.</p>
            <p><a href="?action=signin">Se connecter</a></p>
        HTML;
    }
}