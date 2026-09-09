<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthnException;

class AddPlaylistAction extends Action {
    #[\Override]
    public function get() : string {
        try {
            AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>{$e->getMessage()}</p>
                <p><a class="btn btn-primary" href="?action=signin">Se connecter</a></p>
            HTML;
        }
        return <<<HTML
            <h1 class="h2 fw-bold mb-3 d-flex align-items-center">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/folder-plus/ -->
                <i class="bi bi-folder-plus text-primary me-2"></i>Créer une playlist
            </h1>
            <form method="post" action="?action=add-playlist">
                <div class="mb-3">
                    <label for="title" class="form-label">Nom de la playlist<span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" id="title" placeholder="Ex : Mes favoris" aria-describedby="titleHelp" required>
                    <div id="titleHelp" class="form-text">Choisissez un nom pour votre nouvelle playlist.</div>
                </div>
                <button type="submit" class="btn btn-primary d-inline-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/check-lg/ -->
                    <i class="bi bi-check-lg me-1"></i>Créer la playlist
                </button>
            </form>
        HTML;
    }

    #[\Override]
    public function post() : string {
        if (!isset($_POST['title']) || trim($_POST['title']) === '') {
            return <<<HTML
                <h1>Erreur dans le formulaire</h1>
                <p>Le nom de la playlist est obligatoire.</p>
                <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=add-playlist">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-counterclockwise/ -->
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Retour au formulaire
                </a>
            HTML;
        }

        $user = [];
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>{$e->getMessage()}</p>
                <p><a class="btn btn-primary" href="?action=signin">Se connecter</a></p>
            HTML;
        }

        $w = DeefyRepository::getInstance();

        $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
        $playlist = $w->saveEmptyPlaylist(new Playlist($title, []));

        $w->savePlaylist2User((int) $user['id'], (int) $playlist->id);

        // La playlist créée devient la playlist courante en session
        $_SESSION['playlist'] = $playlist;

        $renderer = RendererFactory::getRenderer($playlist);
        $listRender = $renderer ? $renderer->render(Renderer::LONG) : '';

        return <<<HTML
            {$listRender}
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
}