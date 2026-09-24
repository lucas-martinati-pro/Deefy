<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authz;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\HtmlHelper;
use Override;

/**
 * Action permettant la suppression définitive d'une piste ou son retrait d'une playlist.
 */
class DeleteTrackAction extends Action {
    #[Override]
    public function get() : string {
        $idTrack = (int) $_GET['id'];
        $idPlaylist = isset($_GET['id_pl']) ? (int) $_GET['id_pl'] : null;

        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        // Cas 1 : Retirer d'une playlist
        if ($idPlaylist !== null) {
            $playlist = $r->findPlaylistById($idPlaylist);

            // Icône Bootstrap - https://icons.getbootstrap.com/icons/x-circle/
            $title = HtmlHelper::title("Retirer une piste", "bi-x-circle", "danger");
            return <<<HTML
                {$title}
                <div class="alert alert-warning" role="alert">
                    Êtes-vous sûr de vouloir retirer la piste <strong>{$track->getTitle()}</strong> de la playlist <strong>{$playlist->getName()}</strong> ?
                </div>
                <form method="post" action="?action=delete-track">
                    <input type="hidden" name="id" value="{$idTrack}">
                    <input type="hidden" name="id_pl" value="{$idPlaylist}">
                    <button class="btn btn-danger d-inline-flex align-items-center" type="submit">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/x-circle-fill/ -->
                        <i class="bi bi-x-circle-fill me-1"></i>Confirmer le retrait
                    </button>
                    <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="?action=display-playlist&id={$idPlaylist}">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/x-lg/ -->
                        <i class="bi bi-x-lg me-1"></i>Annuler
                    </a>
                </form>
            HTML;
        }
        // Cas 2 : Supprimer définitivement de mes pistes
        // Icône Bootstrap - https://icons.getbootstrap.com/icons/trash/
        $title = HtmlHelper::title("Supprimer une piste", "bi-trash", "danger");
        return <<<HTML
            {$title}
            <div class="alert alert-warning" role="alert">
                Êtes-vous sûr de vouloir supprimer définitivement la piste <strong>{$track->getTitle()}</strong> de vos pistes ?
            </div>
            <form method="post" action="?action=delete-track">
                <input type="hidden" name="id" value="{$idTrack}">
                <button class="btn btn-danger d-inline-flex align-items-center" type="submit">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/trash-fill/ -->
                    <i class="bi bi-trash-fill me-1"></i>Confirmer la suppression
                </button>
                <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="?action=tracks">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/x-lg/ -->
                    <i class="bi bi-x-lg me-1"></i>Annuler
                </a>
            </form>
        HTML;
    }

    #[Override]
    public function post() : string {
        $idTrack = (int) ($_POST['id']);
        $idPlaylist = isset($_POST['id_pl']) ? (int) $_POST['id_pl'] : null;

        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        $w = DeefyRepository::getInstance();

        // Cas 1 : Retirer de la playlist
        if ($idPlaylist !== null) {
            $w->removeTrackFromPlaylist($idPlaylist, $idTrack);
            return HtmlHelper::successPage(
                title: "Piste retirée",
                message: "La piste <strong>{$track->getTitle()}</strong> a bien été retirée de la playlist.",
                backUrl: "?action=display-playlist&id={$idPlaylist}",
                backLabel: "Retour à la playlist"
            );
        }

        // Cas 2 : Suppression définitive
        $w->deleteTrackById($idTrack);
        if (isset($_SESSION['playerTrack']) && (int) $_SESSION['playerTrack']->getId() === $idTrack) {
            unset($_SESSION['playerTrack']);
        }

        return HtmlHelper::successPage(
            title: "Piste supprimée",
            message: "La piste <strong>{$track->getTitle()}</strong> a bien été supprimée de vos pistes.",
            backUrl: "?action=tracks",
            backLabel: "Retour à mes pistes"
        );
    }

    #[Override]
    protected function check() : ?string {
        $idTrack = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $idPlaylist = isset($_POST['id_pl']) ? (int) $_POST['id_pl'] : null;
        if ($idPlaylist === null) {
            $idPlaylist = isset($_GET['id_pl']) ? (int) $_GET['id_pl'] : null;
        }

        if ($idTrack <= 0) {
            return HtmlHelper::errorPage(
                title: "Piste non spécifiée",
                message: "Aucun identifiant de piste n'a été fourni pour la suppression.",
                backUrl: "?action=playlists",
                backLabel: "Retour à mes playlists"
            );
        }

        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        if (!$track) {
            return HtmlHelper::notFound(item: "Piste", message: "La piste demandée n'existe pas.");
        }

        if ($idPlaylist !== null) {
            if (!Authz::checkPlaylistOwner($idPlaylist)) {
                return HtmlHelper::forbidden(message: "Vous n'êtes pas autorisé à modifier cette playlist.");
            }
        } else {
            if (!Authz::checkTrackOwner($idTrack)) {
                return HtmlHelper::forbidden(message: "Vous n'êtes pas autorisé à supprimer cette piste.");
            }
        }

        return null;
    }
}