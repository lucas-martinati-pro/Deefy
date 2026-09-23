<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\HtmlHelper;

/**
 * Action gérant la suppression d'une playlist et de son contenu en cascade.
 */
class DeletePlaylistAction extends Action {
    #[Override]
    public function get() : string {
        $idPlaylist = (int) $_GET['id'];
        $r = DeefyRepository::getInstance();
        $playlist = $r->findPlaylistById($idPlaylist);

        // Icône Bootstrap - https://icons.getbootstrap.com/icons/trash/
        $title = HtmlHelper::title("Supprimer une playlist", "bi-trash", "danger");
        return <<<HTML
            {$title}
            <div class="alert alert-warning" role="alert">
                Êtes-vous sûr de vouloir supprimer la playlist <strong>{$playlist->name}</strong> ? Cela entraînera la suppression de <strong>toutes les pistes</strong> qu'elle contient.
            </div>
            <form method="post" action="?action=delete-playlist">
                <input type="hidden" name="id" value="{$idPlaylist}">
                <button class="btn btn-danger d-inline-flex align-items-center" type="submit">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/trash-fill/ -->
                    <i class="bi bi-trash-fill me-1"></i>Confirmer la suppression
                </button>
                <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="?action=display-playlist&id={$idPlaylist}">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/x-lg/ -->
                    <i class="bi bi-x-lg me-1"></i>Annuler
                </a>
            </form>
        HTML;
    }

    #[Override]
    public function post() : string {
        $idplaylist = (int) ($_POST['id']);
        $r = DeefyRepository::getInstance();
        $playlist = $r->findPlaylistById($idplaylist);
        $playlistName = $playlist ? $playlist->name : '';

        $w = DeefyRepository::getInstance();
        $w->deletePlaylistById($idplaylist);

        if (isset($_SESSION['playlist']) && $_SESSION['playlist']->id === $idplaylist) {
            unset($_SESSION['playlist']);
        }

        return HtmlHelper::successPage(
            title: "Playlist supprimée",
            message: "La playlist <strong>{$playlistName}</strong> a bien été supprimée."
        );
    }

    #[Override]
    protected function check() : ?string {
        $idPlaylist = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($idPlaylist <= 0) {
            return HtmlHelper::errorPage(
                title: "Playlist non spécifiée",
                message: "Aucun identifiant de playlist n'a été fourni pour la suppression.",
                backUrl: "?action=playlists",
                backLabel: "Retour à mes playlists"
            );
        }

        $r = DeefyRepository::getInstance();
        $playlist = $r->findPlaylistById($idPlaylist);

        if (!$playlist) {
            return HtmlHelper::notFound(item: "Playlist", message: "La playlist demandée n'existe pas.");
        }

        if (!Authz::checkPlaylistOwner($idPlaylist)) {
            return HtmlHelper::forbidden(message: "Vous n'êtes pas autorisé à supprimer cette playlist.");
        }

        return null;
    }
}