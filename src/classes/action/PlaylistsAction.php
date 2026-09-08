<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\RepositoryFactory;

class PlaylistsAction extends Action {
    #[\Override]
    public function get() : string {
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>{$e->getMessage()}</p>
                <p><a class="btn btn-primary" href="?action=signin">Se connecter</a></p>
            HTML;
        }

        $r = RepositoryFactory::getReader();

        $playlists = $r->findPlaylistsByUserId((int) $user['id']);

        if (empty($playlists)) {
            return <<<HTML
                <h1>Mes playlists</h1>
                <div class="alert alert-info" role="alert">
                    Vous ne possédez aucune playlist pour le moment.
                </div>
                <p>
                    <a class="btn btn-primary" href="?action=add-playlist">Créer une playlist</a>
                </p>
            HTML;
        }

        $listHtml = '<div class="list-group mb-3" style="max-width: 500px;">';
        foreach ($playlists as $pl) {
            $listHtml .= <<<HTML
                <a class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" href="?action=display-playlist&id={$pl->id}">
                    <span>{$pl->name}</span>
                    <span class="badge text-bg-primary rounded-pill">Afficher</span>
                </a>
            HTML;
        }
        $listHtml .= "</div>";

        return <<<HTML
            <h1>Mes playlists</h1>
            {$listHtml}
            <p>
                <a class="btn btn-primary" href="?action=add-playlist">Créer une nouvelle playlist</a>
            </p>
        HTML;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}