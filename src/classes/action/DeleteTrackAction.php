<?php

namespace iutnc\deefy\action;

use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class DeleteTrackAction extends Action {
    #[\Override]
    public function get() : string {
        $error = $this->verif();
        if ($error != '') return $error;

        $idTrack = (int) $_GET['id'];
        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        $trackTitle = $track->get('title');

        return <<<HTML
            <h1 class="h2 fw-bold mb-3 d-flex align-items-center">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/trash/ -->
                <i class="bi bi-trash text-danger me-2"></i>Supprimer une piste
            </h1>
            <div class="alert alert-warning" role="alert">
                Êtes-vous sûr de vouloir supprimer le morceau <strong>{$trackTitle}</strong> ?
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
        $trackTitle = $track->get('title');

        $w = DeefyRepository::getInstance();
        $w->deleteTrackById($idTrack);

        return <<<HTML
            <h1 class="h2 fw-bold text-success mb-3 d-flex align-items-center">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/check-circle-fill/ -->
                <i class="bi bi-check-circle-fill text-success me-2"></i>Piste supprimée
            </h1>
            <div class="alert alert-success" role="alert">
                Le morceau <strong>{$trackTitle}</strong> a bien été supprimé.
            </div>
            <p>
                <a class="btn btn-primary d-inline-flex align-items-center" href="?action=playlists">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                    <i class="bi bi-arrow-left me-2"></i>Retour à mes playlists
                </a>
            </p>
        HTML;
    }

    private function verif() : string {
        // Si l'utilisateur n'est pas connecté
        try {
            AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return <<<HTML
                <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/shield-lock-fill/ -->
                    <i class="bi bi-shield-lock-fill text-danger me-2"></i>Accès refusé
                </h1>
                <div class="alert alert-warning" role="alert">
                    Vous devez être connecté pour supprimer une piste.
                </div>
                <p>
                    <a class="btn btn-primary d-inline-flex align-items-center" href="?action=signin">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/box-arrow-in-right/ -->
                        <i class="bi bi-box-arrow-in-right me-1"></i>Se connecter
                    </a>
                    <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="main.php">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/house-door-fill/ -->
                        <i class="bi bi-house-door-fill me-1"></i>Retour à l'accueil
                    </a>
                </p>
            HTML;
        }

        // Si l'identifiant de la piste est manquant
        $idTrack = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($idTrack <= 0) {
            return <<<HTML
                <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/exclamation-triangle-fill/ -->
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Piste non spécifiée
                </h1>
                <div class="alert alert-danger" role="alert">
                    Aucun identifiant de morceau n'a été fourni pour la suppression.
                </div>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=playlists">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                        <i class="bi bi-arrow-left me-2"></i>Retour à mes playlists
                    </a>
                </p>
            HTML;
        }

        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        if (!$track) {
            return <<<HTML
                <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/exclamation-triangle-fill/ -->
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Piste introuvable
                </h1>
                <div class="alert alert-danger" role="alert">
                    Le morceau demandé n'existe pas.
                </div>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=playlists">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                        <i class="bi bi-arrow-left me-2"></i>Retour à mes playlists
                    </a>
                </p>
            HTML;
        }

        if (!Authz::checkTrackOwner($idTrack)) {
            return <<<HTML
                <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/shield-lock-fill/ -->
                    <i class="bi bi-shield-lock-fill text-danger me-2"></i>Accès refusé
                </h1>
                <div class="alert alert-danger" role="alert">
                    Vous n'êtes pas autorisé à supprimer ce morceau car il ne figure dans aucune de vos playlists.
                </div>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=playlists">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                        <i class="bi bi-arrow-left me-2"></i>Retour à mes playlists
                    </a>
                </p>
            HTML;
        }

        return '';
    }
}