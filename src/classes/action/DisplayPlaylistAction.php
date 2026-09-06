<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {
    #[\Override]
    public function get() : string {
        if (!isset($_GET['id'])) {
            return <<<HTML
            <h1>Identifiant manquant</h1>
            <p>Veuillez sélectionner une playlist à afficher.</p>
            <a href="?action=playlist">Retour aux playlists</a>
            HTML;
        }

        $r = DeefyRepository::getInstance();
        $playlist = $r->findPlaylistById($_GET['id']);

        if (!$playlist) {
            return <<<HTML
                <h1>Playlist introuvable</h1>
                <p>La playlist demandée n'existe pas.</p>
                <a href="?action=playlist">Retour aux playlists</a>
            HTML;
        } elseif (Authz::checkPlaylistOwner($playlist->id)) {
            return (new AudioListRenderer($playlist))->render(1);
        }

        return <<<HTML
            <h1>Accès refusé</h1>
            <p>Vous n'êtes pas autorisé à consulter cette playlist.</p>
            <a href="?action=playlist">Retour aux playlists</a>
        HTML;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}