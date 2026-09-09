<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class RegisterAction extends Action {

    #[\Override]
    public function get() : string {
        $require = '<span class="text-danger">*</span>';
        return <<<HTML
        <h1 class="h2 fw-bold mb-3 d-flex align-items-center">
            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/person-plus-fill/ -->
            <i class="bi bi-person-plus-fill text-primary me-2"></i>Inscription
        </h1>
        <form method="post" action="?action=register" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="email" class="form-label">Adresse mail$require</label>
                <input type="email" name="email" class="form-control" id="email" placeholder="nom@exemple.com" aria-describedby="emailHelp" required>
                <div id="emailHelp" class="form-text">Votre adresse e-mail servira d'identifiant de connexion.</div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Mot de passe$require</label>
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
                <label for="password-double" class="form-label">Confirmation du mot de passe$require</label>
                <input type="password" name="password-double" class="form-control" id="password-double" aria-describedby="passwordDoubleHelp" required>
                <div id="passwordDoubleHelp" class="form-text">Ressaisissez votre mot de passe à l'identique.</div>
            </div>
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="connect-auto" name="connect-auto" checked>
                <label class="form-check-label" for="connect-auto">Se connecter automatiquement</label>
            </div>
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/person-plus-fill/ -->
                <i class="bi bi-person-plus-fill me-1"></i>Inscription
            </button>
        </form>
        HTML;
    }

    #[\Override]
    public function post() : string {
        if (!isset($_POST['email'], $_POST['password'], $_POST['password-double'])) {
            return <<<HTML
                <h1>Échec de l'inscription</h1>
                <p>Tous les champs sont obligatoires.</p>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=register">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-counterclockwise/ -->
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Retour au formulaire
                    </a>
                </p>
            HTML;
        }

        if ($_POST['password'] !== $_POST['password-double']) {
            return <<<HTML
                <h1>Échec de l'inscription</h1>
                <p>Les deux mots de passe doivent être identiques.</p>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=register">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-counterclockwise/ -->
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Retour au formulaire
                    </a>
                </p>
            HTML;
        }

        $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

        try {
            AuthnProvider::register($_POST['email'], $_POST['password']);
        } catch (AuthnException $error) {
            return <<<HTML
                <h1>Échec de l'inscription</h1>
                <p>{$error->getMessage()}</p>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=register">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-counterclockwise/ -->
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Retour au formulaire
                    </a>
                </p>
            HTML;
        }

        if (isset($_POST['connect-auto'])) {
            try {
                AuthnProvider::signin($_POST['email'], $_POST['password']);
            } catch (AuthnException $error) {
                return <<<HTML
                    <h1>Échec de la connexion</h1>
                    <p>{$error->getMessage()}</p>
                    <p>
                        <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=register">
                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-counterclockwise/ -->
                            <i class="bi bi-arrow-counterclockwise me-1"></i>Retour au formulaire
                        </a>
                    </p>
                HTML;
            }

            return <<<HTML
                <h1 class="h2 fw-bold text-success mb-3 d-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/check-circle-fill/ -->
                    <i class="bi bi-check-circle-fill text-success me-2"></i>Connexion réussie
                </h1>
                <p>Bienvenue, <strong>{$_POST['email']}</strong> !</p>
                <p>Vous êtes maintenant connecté à Deefy.</p>
                <p>
                    <a class="btn btn-primary d-inline-flex align-items-center" href="?action=playlists">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/collection-play-fill/ -->
                        <i class="bi bi-collection-play-fill me-2"></i>Mes playlists
                    </a>
                    <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="main.php">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/house-door-fill/ -->
                        <i class="bi bi-house-door-fill me-1"></i>Accueil
                    </a>
                </p>
            HTML;
        }

        return <<<HTML
            <h1 class="h2 fw-bold text-success mb-3 d-flex align-items-center">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/check-circle-fill/ -->
                <i class="bi bi-check-circle-fill text-success me-2"></i>Inscription réussie
            </h1>
            <p>Votre compte a bien été créé.</p>
            <p>
                <a class="btn btn-primary d-inline-flex align-items-center" href="?action=signin">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/box-arrow-in-right/ -->
                    <i class="bi bi-box-arrow-in-right me-1"></i>Se connecter
                </a>
            </p>
        HTML;
    }
}