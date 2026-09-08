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
            <ul>
        HTML;
        try {
            AuthnProvider::getSignedInUser();

            $page .= <<<HTML
                <li><a href="?action=playlists">Afficher mes playlists</a></li>
                <li><a href="?action=display-playlist">Afficher la playlist courante</a></li>
                <li><a href="?action=add-playlist">Créer une playlist</a></li>
                <li><a href="?action=signout">Se déconnecter</a></li>
            HTML;
        } catch (AuthnException $e) {
            $page .= <<<HTML
                <li><a href="?action=register">Inscription</a></li>
                <li><a href="?action=signin">Se connecter</a></li>
            HTML;
        }

        $page .= <<<HTML
                </ul>
            </body>
        HTML;

        return $page;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}