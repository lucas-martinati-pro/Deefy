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