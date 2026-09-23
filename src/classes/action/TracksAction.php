<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;

/**
 * Action permettant d'afficher l'ensemble des pistes de l'utilisateur.
 */
class TracksAction extends Action {
    #[Override]
    public function get() : string {
        $r = DeefyRepository::getInstance();
        $tracks = $r->findTracksByUserId((int) $this->user['id']);

        $playlist = new Playlist("Mes pistes", $tracks);
        $renderer = RendererFactory::getRenderer($playlist);
        $playlistHtml = $renderer ? $renderer->render() : '';

        return <<<HTML
            {$playlistHtml}
            <p>
                <a class="btn btn-primary d-inline-flex align-items-center" href="?action=add-track">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-circle-fill/ -->
                    <i class="bi bi-plus-circle-fill me-2"></i>Ajouter une piste
                </a>
            </p>
        HTML;
    }

    #[Override]
    public function post() : string {
        return '';
    }
}