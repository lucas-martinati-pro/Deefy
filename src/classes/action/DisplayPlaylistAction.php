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
                    <p><a class="btn btn-secondary" href="?action=playlists">Retour à mes playlists</a></p>
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
                    <p><a class="btn btn-secondary" href="?action=playlists">Retour à mes playlists</a></p>
                HTML;
            }

            $_SESSION['playlist'] = $playlist;
        }

        // Si l'utilisateur clique sur "Lire" pour écouter une piste spécifique dans le lecteur principal
        if (isset($_GET['track_id'])) {
            $trackId = (int) $_GET['track_id'];
            foreach ($playlist->tracks as $track) {
                if ((int) $track->get('id') === $trackId) {
                    $_SESSION['current_track'] = $track;
                    break;
                }
            }
        }

        if (!Authz::checkPlaylistOwner($playlist->id)) {
            return <<<HTML
                <h1 class="h2 fw-bold text-danger mb-3">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/shield-lock-fill/ -->
                    <i class="bi bi-shield-lock-fill me-2"></i>Accès refusé
                </h1>
                <p>Vous n'êtes pas autorisé à consulter cette playlist.</p>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=playlists">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                        <i class="bi bi-arrow-left me-2"></i>Retour à mes playlists
                    </a>
                </p>
            HTML;
        }

        $renderer = RendererFactory::getRenderer($playlist);
        $playlistHtml = $renderer ? $renderer->render(Renderer::COMPACT) : '';

        return <<<HTML
            {$playlistHtml}
            <p>
                <a class="btn btn-primary d-inline-flex align-items-center" href="?action=add-track">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-circle-fill/ -->
                    <i class="bi bi-plus-circle-fill me-2"></i>Ajouter une piste
                </a>
                <a class="btn btn-secondary d-inline-flex align-items-center ms-2" href="?action=playlists">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                    <i class="bi bi-arrow-left me-2"></i>Retour à mes playlists
                </a>
            </p>
        HTML;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}