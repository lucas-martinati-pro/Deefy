<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class SigninAction extends Action {
    public function get() : string {
        $require = '<span class="text-danger">*</span>';
        return <<<HTML
        <h1 class="h2 fw-bold mb-3 d-flex align-items-center">
            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/box-arrow-in-right/ -->
            <i class="bi bi-box-arrow-in-right text-primary me-2"></i>Connexion
        </h1>
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
            <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/box-arrow-in-right/ -->
                <i class="bi bi-box-arrow-in-right me-1"></i>Connexion
            </button>
        </form>
        HTML;
    }

    public function post() : string {
        if (!isset($_POST['email'], $_POST['password'])) {
            return <<<HTML
                <h1>Échec de la connexion</h1>
                <p>Tous les champs sont obligatoires.</p>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=signin">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-counterclockwise/ -->
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Retour au formulaire
                    </a>
                </p>
            HTML;
        }

        try {
            AuthnProvider::signin($_POST['email'], $_POST['password']);
        } catch (AuthnException $error) {
            return <<<HTML
                <h1>Échec de la connexion</h1>
                <p>{$error->getMessage()}</p>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=signin">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-counterclockwise/ -->
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Retour au formulaire
                    </a>
                </p>
            HTML;
        }

        $_POST['email'] = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

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
}