<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class PlaylistsAction extends Action {
    #[\Override]
    public function get() : string {
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>{$e->getMessage()}</p>
                <a href="?action=signin">Se connecter</a>
            HTML;
        }

        $r = DeefyRepository::getInstance();

        $playlists = $r->findPlaylistsByUserId((int) $user['id']);

        if (empty($playlists)) {
            return <<<HTML
                <h1>Mes playlists</h1>
                <p>Vous ne possédez aucune playlist pour le moment.</p>
                <a href="?action=add-playlist">Créer une playlist</a>
            HTML;
        }

        $listHtml = '<ul class="dropdown-menu show position-static">';
        foreach ($playlists as $pl) {
            $listHtml .= <<<HTML
                <li>
                    <a class="dropdown-item" href="?action=display-playlist&id={$pl->id}">{$pl->name}</a>
                </li>
            HTML;
        }
        $listHtml .= "</ul>";

        return <<<HTML
            <h1>Mes playlists</h1>
            {$listHtml}
            <p>
                <a class="btn btn-secondary" href="?action=add-playlist">Créer une nouvelle playlist</a>
            </p>
        HTML;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}