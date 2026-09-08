<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class RegisterAction extends Action {

    #[\Override]
    public function get() : string {
        return <<<HTML
        <form method="post" action="?action=register" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="email" class="form-label">Adresse mail</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="nom@exemple.com" aria-describedby="emailHelp" required>
                <div id="emailHelp" class="form-text">Votre adresse e-mail servira d'identifiant de connexion.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" id="password" aria-describedby="passwordHelp" required>
                <div id="passwordHelp" class="form-text">
                    Le mot de passe doit contenir au moins 10 caractères et comporter :
                    <ul class="mb-0 ps-3">
                        <li>Au moins une lettre majuscule</li>
                        <li>Au moins une lettre minuscule</li>
                        <li>Au moins un chiffre</li>
                        <li>Au moins un caractère spécial (ex. @, #, $, %, etc.)</li>
                    </ul>
                </div>
            </div>
            <div class="mb-3">
                <label for="password-double" class="form-label">Confirmation du mot de passe</label>
                <input type="password" name="password-double" class="form-control" id="password-double" aria-describedby="passwordDoubleHelp" required>
                <div id="passwordDoubleHelp" class="form-text">Ressaisissez votre mot de passe à l'identique.</div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="connect-auto" name="connect-auto" checked>
                <label class="form-check-label" for="connect-auto">Se connecter automatiquement</label>
            </div>
            <button type="submit" class="btn btn-primary">Inscription</button>
        </form>
        HTML;
    }

    #[\Override]
    public function post() : string {
        if (!isset($_POST['email'], $_POST['password'], $_POST['password-double'])) {
            return <<<HTML
                <h1>Échec de l'inscription</h1>
                <p>Tous les champs sont obligatoires.</p>
                <p><a class="btn btn-secondary" href="?action=register">Retour au formulaire</a></p>
            HTML;
        }

        if ($_POST['password'] !== $_POST['password-double']) {
            return <<<HTML
                <h1>Échec de l'inscription</h1>
                <p>Les deux mots de passe doivent être identiques.</p>
                <p><a class="btn btn-secondary" href="?action=register">Retour au formulaire</a></p>
            HTML;
        }

        $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        try {
            AuthnProvider::register($_POST['email'], $_POST['password']);
        } catch (AuthnException $error) {
            return <<<HTML
                <h1>Échec de l'inscription</h1>
                <p>{$error->getMessage()}</p>
                <p><a class="btn btn-secondary" href="?action=register">Retour au formulaire</a></p>
            HTML;
        }

        if (isset($_POST['connect-auto'])) {
            try {
                AuthnProvider::signin($_POST['email'], $_POST['password']);
            } catch (AuthnException $error) {
                return <<<HTML
                    <h1>Échec de la connexion</h1>
                    <p>{$error->getMessage()}</p>
                    <p><a class="btn btn-secondary" href="?action=register">Retour au formulaire</a></p>
                HTML;
            }

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

        return <<<HTML
            <h1>Inscription réussie</h1>
            <p>Votre compte a bien été créé.</p>
            <p><a class="btn btn-primary" href="?action=signin">Se connecter</a></p>
        HTML;
    }
}