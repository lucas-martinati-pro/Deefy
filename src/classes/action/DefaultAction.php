<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class DefaultAction extends Action {
    #[\Override]
    public function get() : string {
        $page = <<< HTML
            <h1>Bienvenue sur Deefy !</h1>
            <p>Bienvenue sur Deefy, votre plateforme de musique.</p>
            <ul class="dropdown-menu show position-static">
        HTML;
        try {
            AuthnProvider::getSignedInUser();

            $page .= <<<HTML
                <li><a class="dropdown-item" href="?action=playlists">Afficher mes playlists</a></li>
                <li><a class="dropdown-item" href="?action=display-playlist">Afficher la playlist courante</a></li>
                <li><a class="dropdown-item" href="?action=add-playlist">Créer une playlist</a></li>
                <li><a class="dropdown-item" href="?action=signout">Se déconnecter</a></li>
            HTML;
        } catch (AuthnException $e) {
            $page .= <<<HTML
                <li><a class="dropdown-item" href="?action=register">Inscription</a></li>
                <li><a class="dropdown-item" href="?action=signin">Se connecter</a></li>
            HTML;
        }

        $page .= <<<HTML
                </ul>
        HTML;

        return $page;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}