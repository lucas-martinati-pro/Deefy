<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\HtmlHelper;

/**
 * Action permettant d'afficher l'ensemble des pistes de l'utilisateur.
 */
class TracksAction extends Action {
    #[\Override]
    public function get() : string {
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return HtmlHelper::authRequired(message: $e->getMessage());
        }

        $r = DeefyRepository::getInstance();
        $tracks = $r->findTracksByUserId((int) $user['id']);

        $playlist = new Playlist("Mes pistes", $tracks);
        $renderer = RendererFactory::getRenderer($playlist);
        $playlistHtml = $renderer ? $renderer->render(Renderer::COMPACT) : '';

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

    #[\Override]
    public function post() : string {
        return '';
    }
}