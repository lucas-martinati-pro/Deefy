<?php

namespace iutnc\deefy\action;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\HtmlHelper;
use iutnc\deefy\enums\TypeRender;
use Override;

/**
 * Action permettant la création d'une nouvelle playlist.
 */
class AddPlaylistAction extends Action {

    #[Override]
    public function get() : string {
        // Icône Bootstrap - https://icons.getbootstrap.com/icons/folder-plus/
        $title = HtmlHelper::title("Créer une playlist", "bi-folder-plus");
        return <<<HTML
            {$title}
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

    #[Override]
    public function post() : string {
        if (!isset($_POST['title']) || trim($_POST['title']) === '') {
            return HtmlHelper::formError(errors: "Le nom de la playlist est obligatoire.", backUrl: "?action=add-playlist");
        }

        $w = DeefyRepository::getInstance();

        $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
        $playlist = $w->saveEmptyPlaylist(new Playlist($title, []));

        $w->savePlaylist2User((int) $this->user['id'], (int) $playlist->getId());

        // La playlist créée devient la playlist courante en session
        $_SESSION['playlist'] = $playlist;

        $renderer = RendererFactory::getRenderer($playlist);
        $listRender = $renderer ? $renderer->render(TypeRender::LONG) : '';

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