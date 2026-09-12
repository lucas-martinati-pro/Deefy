<?php

namespace iutnc\deefy\action;

use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\exception\AuthnException;

class DeletePlaylistAction extends Action {
    #[\Override]
    public function get() : String {
        $error = $this->verif();
        if ($error != '') return $error;

        $idPlaylist = (int) $_GET['id'];
        $r = DeefyRepository::getInstance();
        $playlist = $r->findPlaylistById($idPlaylist);

        return <<<HTML
            <h1 class="h2 fw-bold mb-3 d-flex align-items-center">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/trash/ -->
                <i class="bi bi-trash text-danger me-2"></i>Supprimer une playlist
            </h1>
            <div class="alert alert-warning" role="alert">
                Êtes-vous sûr de vouloir supprimer la playlist <strong>{$playlist->name}</strong> ? Cela entraînera la suppression de <strong>toutes les pistes</strong> qu'elle contient.
            </div>
            <form method="post" action="?action=delete-playlist&id={$idPlaylist}">
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

    #[\Override]
    public function post() : String {
        $error = $this->verif();
        if ($error != '') return $error;

        $idplaylist = (int) ($_POST['id']);
        $r = DeefyRepository::getInstance();
        $playlist = $r->findPlaylistById($idplaylist);
        $playlistName = $playlist ? $playlist->name : '';

        $w = DeefyRepository::getInstance();
        $w->deletePlaylistById($idplaylist);

        if (isset($_SESSION['playlist']) && $_SESSION['playlist']->id === $idplaylist) {
            unset($_SESSION['playlist']);
        }

        return <<<HTML
            <h1 class="h2 fw-bold text-success mb-3 d-flex align-items-center">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/check-circle-fill/ -->
                <i class="bi bi-check-circle-fill text-success me-2"></i>Playlist supprimée
            </h1>
            <div class="alert alert-success" role="alert">
                La playlist <strong>{$playlistName}</strong> a bien été supprimée.
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
                    Vous devez être connecté pour supprimer une playlist.
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

        // Si l'identifiant de la playlist est manquant
        $idPlaylist = (int) ($_POST['id'] ?? $_GET['id'] ?? 0);
        if ($idPlaylist <= 0) {
            return <<<HTML
                <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/exclamation-triangle-fill/ -->
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Playlist non spécifiée
                </h1>
                <div class="alert alert-danger" role="alert">
                    Aucun identifiant de playlist n'a été fourni pour la suppression.
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
        $playlist = $r->findPlaylistById($idPlaylist);

        if (!$playlist) {
            return <<<HTML
                <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/exclamation-triangle-fill/ -->
                    <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Playlist introuvable
                </h1>
                <div class="alert alert-danger" role="alert">
                    La playlist demandée n'existe pas.
                </div>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=playlists">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                        <i class="bi bi-arrow-left me-2"></i>Retour à mes playlists
                    </a>
                </p>
            HTML;
        }

        if (!Authz::checkPlaylistOwner($idPlaylist)) {
            return <<<HTML
                <h1 class="h2 fw-bold text-danger mb-3 d-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/shield-lock-fill/ -->
                    <i class="bi bi-shield-lock-fill text-danger me-2"></i>Accès refusé
                </h1>
                <div class="alert alert-danger" role="alert">
                    Vous n'êtes pas autorisé à supprimer cette playlist.
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