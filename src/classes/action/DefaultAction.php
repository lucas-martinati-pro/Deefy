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
            <p class="text-muted">Bienvenue sur Deefy, votre plateforme de musique.</p>
            <div class="list-group mb-3" style="max-width: 450px;">
        HTML;
        try {
            AuthnProvider::getSignedInUser();

            $page .= <<<HTML
                <a class="list-group-item list-group-item-action" href="?action=playlists">Afficher mes playlists</a>
                <a class="list-group-item list-group-item-action" href="?action=display-playlist">Afficher la playlist courante</a>
                <a class="list-group-item list-group-item-action" href="?action=add-playlist">Créer une playlist</a>
                <a class="list-group-item list-group-item-action list-group-item-danger" href="?action=signout">Se déconnecter</a>
            HTML;
        } catch (AuthnException $e) {
            $page .= <<<HTML
                <a class="list-group-item list-group-item-action" href="?action=signin">Se connecter</a>
                <a class="list-group-item list-group-item-action" href="?action=register">Inscription</a>
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