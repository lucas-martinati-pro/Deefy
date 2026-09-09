<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class DefaultAction extends Action {
    #[\Override]
    public function get() : string {
        $page = <<< HTML
            <h1 class="h2 fw-bold mb-2">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/vinyl-fill/ -->
                <i class="bi bi-vinyl-fill text-primary me-2"></i>Bienvenue sur Deefy !
            </h1>
            <p class="text-muted">Bienvenue sur Deefy, votre plateforme de musique.</p>
            <div class="list-group mb-3" style="max-width: 450px;">
        HTML;
        try {
            AuthnProvider::getSignedInUser();

            $page .= <<<HTML
                <a class="list-group-item list-group-item-action d-flex align-items-center" href="?action=playlists">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/collection-play-fill/ -->
                    <i class="bi bi-collection-play-fill text-primary me-2 fs-5"></i>
                    Afficher mes playlists
                </a>
                <a class="list-group-item list-group-item-action d-flex align-items-center" href="?action=display-playlist">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-list/ -->
                    <i class="bi bi-music-note-list text-info me-2 fs-5"></i>
                    Afficher la playlist courante
                </a>
                <a class="list-group-item list-group-item-action d-flex align-items-center" href="?action=add-playlist">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-circle-fill/ -->
                    <i class="bi bi-plus-circle-fill text-success me-2 fs-5"></i>
                    Créer une playlist
                </a>
                <a class="list-group-item list-group-item-action list-group-item-danger d-flex align-items-center" href="?action=signout">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/box-arrow-right/ -->
                    <i class="bi bi-box-arrow-right text-danger me-2 fs-5"></i>
                    Se déconnecter
                </a>
            HTML;
        } catch (AuthnException $e) {
            $page .= <<<HTML
                <a class="list-group-item list-group-item-action d-flex align-items-center" href="?action=signin">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/box-arrow-in-right/ -->
                    <i class="bi bi-box-arrow-in-right text-primary me-2 fs-5"></i>
                    Se connecter
                </a>
                <a class="list-group-item list-group-item-action d-flex align-items-center" href="?action=register">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/person-plus-fill/ -->
                    <i class="bi bi-person-plus-fill text-success me-2 fs-5"></i>
                    Inscription
                </a>
            HTML;
        }

        $page .= <<<HTML
            </div>
        HTML;

        return $page;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}