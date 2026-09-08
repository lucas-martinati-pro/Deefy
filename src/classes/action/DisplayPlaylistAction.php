<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {
        #[\Override]
    public function get() : string {
        // CAS 1 : Aucun ID -> on affiche la playlist en session
        if (!isset($_GET['id'])) {
            if (!isset($_SESSION['playlist'])) {
                return <<<HTML
                    <h1>Playlist</h1>
                    <p>Aucune playlist n'existe actuellement en session.</p>
                    <a href="?action=playlists">Retour à mes playlists</a>
                HTML;
            }
            $playlist = $_SESSION['playlist'];
        } else {
            // CAS 2 : Un ID est fourni -> on charge la playlist depuis la BD
            $idPlaylist = (int) $_GET['id'];
            $r = DeefyRepository::getInstance();
            $playlist = $r->findPlaylistById($idPlaylist);

            if (!$playlist) {
                return <<<HTML
                    <h1>Playlist introuvable</h1>
                    <p>La playlist demandée n'existe pas.</p>
                    <a href="?action=playlists">Retour à mes playlists</a>
                HTML;
            }

            $_SESSION['playlist'] = $playlist;
        }

        if (!Authz::checkPlaylistOwner($playlist->id)) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>Vous n'êtes pas autorisé à consulter cette playlist.</p>
                <a href="?action=playlists">Retour à mes playlists</a>
            HTML;
        }

        $renderer = RendererFactory::getRenderer($playlist);
        $playlistHtml = $renderer ? $renderer->render(Renderer::COMPACT) : '';

        return <<<HTML
            {$playlistHtml}
            <p>
                <a class="btn btn-secondary" href="?action=add-track">Ajouter une piste</a>
                <a class="btn btn-secondary" href="?action=playlists">Retour à mes playlists</a>
            </p>
        HTML;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}