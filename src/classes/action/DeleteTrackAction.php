<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\Authz;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\HtmlHelper;

/**
 * Action permettant la suppression définitive d'une piste ou son retrait d'une playlist.
 */
class DeleteTrackAction extends Action {
    #[\Override]
    public function get() : string {
        $error = $this->verif();
        if ($error != '') return $error;

        $idTrack = (int) $_GET['id'];
        $idPlaylist = isset($_GET['id_pl']) ? (int) $_GET['id_pl'] : null;

        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        // Cas 1 : Retirer d'une playlist
        if ($idPlaylist !== null) {
            $playlist = $r->findPlaylistById($idPlaylist);
            $playlistName = $playlist ? htmlspecialchars($playlist->name) : 'la playlist';
            return <<<HTML
                <h1 class="h2 fw-bold mb-3 d-flex align-items-center">
                    <i class="bi bi-x-circle text-danger me-2"></i>Retirer une piste
                </h1>
                <div class="alert alert-warning" role="alert">
                    Êtes-vous sûr de vouloir retirer le morceau <strong>{$track->get('title')}</strong> de la playlist <strong>{$playlistName}</strong> ?
                </div>
                <form method="post" action="?action=delete-track&id={$idTrack}&id_pl={$idPlaylist}">
                    <input type="hidden" name="id" value="{$idTrack}">
                    <input type="hidden" name="id_pl" value="{$idPlaylist}">
                    <button class="btn btn-danger d-inline-flex align-items-center" type="submit">
                        <i class="bi bi-x-circle-fill me-1"></i>Confirmer le retrait
                    </button>
                    <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="?action=display-playlist&id={$idPlaylist}">
                        <i class="bi bi-x-lg me-1"></i>Annuler
                    </a>
                </form>
            HTML;
        }
        // Cas 2 : Supprimer définitivement de mes morceaux
        return <<<HTML
            <h1 class="h2 fw-bold mb-3 d-flex align-items-center">
                <i class="bi bi-trash text-danger me-2"></i>Supprimer une piste
            </h1>
            <div class="alert alert-warning" role="alert">
                Êtes-vous sûr de vouloir supprimer définitivement le morceau <strong>{$track->get('title')}</strong> de vos pistes ?
            </div>
            <form method="post" action="?action=delete-track&id={$idTrack}">
                <input type="hidden" name="id" value="{$idTrack}">
                <button class="btn btn-danger d-inline-flex align-items-center" type="submit">
                    <i class="bi bi-trash-fill me-1"></i>Confirmer la suppression
                </button>
                <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="?action=tracks">
                    <i class="bi bi-x-lg me-1"></i>Annuler
                </a>
            </form>
        HTML;
    }

    #[\Override]
    public function post() : string {
        $error = $this->verif();
        if ($error != '') return $error;

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
                message: "Le morceau <strong>{$track->get('title')}</strong> a bien été retiré de la playlist.",
                backUrl: "?action=display-playlist&id={$idPlaylist}",
                backLabel: "Retour à la playlist"
            );
        }

        // Cas 2 : Suppression définitive
        $w->deleteTrackById($idTrack);
        if (isset($_SESSION['playerTrack']) && (int) $_SESSION['playerTrack']->get('id') === $idTrack) {
            unset($_SESSION['playerTrack']);
        }

        return HtmlHelper::successPage(
            title: "Piste supprimée",
            message: "Le morceau <strong>{$track->get('title')}</strong> a bien été supprimé de vos pistes.",
            backUrl: "?action=tracks",
            backLabel: "Retour à mes pistes"
        );
    }

    /**
     * Valide l'existence de l'identifiant de piste et la propriété du morceau.
     *
     * @return string Message d'erreur HTML ou chaîne vide si toutes les vérifications sont validées.
     */
    private function verif() : string {
        $idTrack = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        $idPlaylist = isset($_POST['id_pl']) ? (int) $_POST['id_pl'] : null;
        if ($idPlaylist === null) {
            $idPlaylist = isset($_GET['id_pl']) ? (int) $_GET['id_pl'] : null;
        }

        if ($idTrack <= 0) {
            return HtmlHelper::errorPage(
                title: "Piste non spécifiée",
                message: "Aucun identifiant de morceau n'a été fourni pour la suppression.",
                backUrl: "?action=playlists",
                backLabel: "Retour à mes playlists"
            );
        }

        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        if (!$track) {
            return HtmlHelper::notFound(item: "Piste", message: "Le morceau demandé n'existe pas.");
        }

        if ($idPlaylist !== null) {
            if (!Authz::checkPlaylistOwner($idPlaylist)) {
                return HtmlHelper::forbidden(message: "Vous n'êtes pas autorisé à modifier cette playlist.");
            }
        } else {
            if (!Authz::checkTrackOwner($idTrack)) {
                return HtmlHelper::forbidden(message: "Vous n'êtes pas autorisé à supprimer ce morceau.");
            }
        }

        return '';
    }
}