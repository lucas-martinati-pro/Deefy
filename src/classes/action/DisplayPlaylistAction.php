<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\HtmlHelper;

class DisplayPlaylistAction extends Action {
        #[\Override]
    public function get() : string {
        // CAS 1 : Aucun ID -> on affiche la playlist en session
        if (!isset($_GET['id'])) {
            if (!isset($_SESSION['playlist'])) {
                return HtmlHelper::errorPage(
                    title: "Playlist",
                    message: "Aucune playlist n'existe actuellement en session.",
                    backUrl: "?action=playlists",
                    backLabel: "Retour à mes playlists"
                );
            }
            $playlist = $_SESSION['playlist'];
        } else {
            // CAS 2 : Un ID est fourni -> on charge la playlist depuis la BD
            $idPlaylist = (int) $_GET['id'];
            $r = DeefyRepository::getInstance();
            $playlist = $r->findPlaylistById($idPlaylist);

            if (!$playlist) {
                return HtmlHelper::notFound(item: "Playlist", message: "La playlist demandée n'existe pas.");
            }

            $_SESSION['playlist'] = $playlist;
        }

        if (!Authz::checkPlaylistOwner($playlist->id)) {
            return HtmlHelper::forbidden(message: "Vous n'êtes pas autorisé à consulter cette playlist.");
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
                <a class="btn btn-outline-danger d-inline-flex align-items-center ms-2" href="?action=delete-playlist&id={$playlist->id}">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/trash-fill/ -->
                    <i class="bi bi-trash-fill me-1"></i>Supprimer la playlist
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