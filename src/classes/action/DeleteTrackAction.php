<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\HtmlHelper;

class DeleteTrackAction extends Action {
    #[\Override]
    public function get() : string {
        $error = $this->verif();
        if ($error != '') return $error;

        $idTrack = (int) $_GET['id'];
        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        return <<<HTML
            <h1 class="h2 fw-bold mb-3 d-flex align-items-center">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/trash/ -->
                <i class="bi bi-trash text-danger me-2"></i>Supprimer une piste
            </h1>
            <div class="alert alert-warning" role="alert">
                Êtes-vous sûr de vouloir supprimer le morceau <strong>{$track->get('title')}</strong> ?
            </div>
            <form method="post" action="?action=delete-track&id={$idTrack}">
                <input type="hidden" name="id" value="{$idTrack}">
                <button class="btn btn-danger d-inline-flex align-items-center" type="submit">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/trash-fill/ -->
                    <i class="bi bi-trash-fill me-1"></i>Confirmer la suppression
                </button>
                <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="?action=playlists">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/x-lg/ -->
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
        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        $w = DeefyRepository::getInstance();
        $w->deleteTrackById($idTrack);

        return HtmlHelper::successPage(
            title: "Piste supprimée",
            message: "Le morceau <strong>{$track->get('title')}</strong> a bien été supprimé."
        );
    }

    private function verif() : string {
        try {
            AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return HtmlHelper::authRequired(message: "Vous devez être connecté pour supprimer une piste.");
        }

        $idTrack = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
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

        if (!Authz::checkTrackOwner($idTrack)) {
            return HtmlHelper::forbidden(message: "Vous n'êtes pas autorisé à supprimer ce morceau car il ne figure dans aucune de vos playlists.");
        }

        return '';
    }
}