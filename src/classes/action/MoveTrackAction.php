<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\render\HtmlHelper;

/**
 * Action permettant d'ajouter une piste audio existante à une ou plusieurs playlists de l'utilisateur.
 */
class MoveTrackAction extends Action {

    #[\Override]
    public function get() : string {
        $idTrack = isset($_GET['id']) ? (int) $_GET['id'] : null;

        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        $playlists = $r->findPlaylistsByUserId((int) $this->user['id']);

        $trackRenderer = RendererFactory::getRenderer($track);
        $trackHtml = $trackRenderer->render(Renderer::LONG);
        $trackPreview = !empty($trackHtml)
            ? $trackHtml
            : '';
        // Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-list/
        $title = HtmlHelper::title("Ajouter à une playlist", "bi-music-note-list");

        if (empty($playlists)) {
            $alertHtml = HtmlHelper::alert(
                type: 'warning',
                content: "Vous ne possédez aucune playlist pour le moment. Veuillez d'abord en créer une pour y ajouter cette piste."
            );

            return <<<HTML
                {$title}
                {$trackPreview}
                {$alertHtml}
                <p class="mt-3">
                    <a class="btn btn-primary d-inline-flex align-items-center" href="?action=add-playlist">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-circle-fill/ -->
                        <i class="bi bi-plus-circle-fill me-2"></i>Créer une playlist
                    </a>
                    <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="?action=tracks">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                        <i class="bi bi-arrow-left me-2"></i>Retour à mes pistes
                    </a>
                </p>
            HTML;
        }

        $playlistListHtml = '<div class="list-group mb-4" style="max-width: 600px;">';
        $availableCount = 0;

        foreach ($playlists as $playlist) {
            $isAlreadyIn = $r->isTrackInPlaylist((int) $playlist->id, $idTrack);
            $playlistName = $playlist->name;
            $trackCount = $playlist->trackCount;
            $trackCountLabel = "{$trackCount} piste" . ($trackCount > 1 ? "s" : "");

            if ($isAlreadyIn) {
                $playlistListHtml .= <<<HTML
                    <div class="list-group-item d-flex justify-content-between align-items-center p-3 opacity-75">
                        <div class="form-check d-flex align-items-center gap-2 mb-0">
                            <input class="form-check-input flex-shrink-0" type="checkbox" checked disabled id="playlist-{$playlist->id}">
                            <label class="form-check-label" for="playlist-{$playlist->id}">
                                <span class="fw-semibold">{$playlistName}</span>
                                <span class="d-block small text-muted">
                                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-beamed/ -->
                                    <i class="bi bi-music-note-beamed me-1"></i>
                                    {$trackCountLabel}
                                </span>
                            </label>
                        </div>
                        <span class="badge bg-secondary-subtle text-secondary border d-flex align-items-center gap-1">
                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/check-circle-fill/ -->
                            <i class="bi bi-check-circle-fill text-success"></i>
                            Déjà présente
                        </span>
                    </div>
                HTML;
            } else {
                $availableCount++;
                $playlistListHtml .= <<<HTML
                    <label class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3 gap-3" role="button" for="playlist-{$playlist->id}">
                        <div class="form-check d-flex align-items-center gap-2 mb-0">
                            <!-- Le [] dans le name, ça sert à pouvoir bouler directement sur id_playlists comme un tableau -->
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="id_playlists[]" value="{$playlist->id}" id="playlist-{$playlist->id}">
                            <span class="form-check-label">
                                <span class="fw-bold">{$playlistName}</span>
                                <span class="d-block small text-muted">
                                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-beamed/ -->
                                    <i class="bi bi-music-note-beamed me-1"></i>
                                    {$trackCountLabel}
                                </span>
                            </span>
                        </div>
                    </label>
                HTML;
            }
        }

        $playlistListHtml .= '</div>';

        if ($availableCount === 0) {
            $statusAlert = HtmlHelper::alert(
                type: 'info',
                content: "Cette piste est déjà présente dans l'ensemble de vos playlists."
            );
            $actionForm = <<<HTML
                {$statusAlert}
                <p class="mt-3">
                    <a class="btn btn-primary d-inline-flex align-items-center" href="?action=add-playlist">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-circle-fill/ -->
                        <i class="bi bi-plus-circle-fill me-2"></i>Créer une nouvelle playlist
                    </a>
                    <a class="btn btn-secondary ms-2 d-inline-flex align-items-center" href="?action=tracks">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                        <i class="bi bi-arrow-left me-2"></i>Retour à mes pistes
                    </a>
                </p>
            HTML;
        } else {
            $actionForm = <<<HTML
                <form method="post" action="?action=move-track">
                    <input type="hidden" name="id_track" value="{$idTrack}">
                    <p class="text-secondary mb-3">Sélectionnez la ou les playlists auxquelles vous souhaitez ajouter cette piste :</p>
                    {$playlistListHtml}
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-primary d-inline-flex align-items-center" type="submit">
                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-circle-fill/ -->
                            <i class="bi bi-plus-circle-fill me-2"></i>
                            Ajouter aux playlists sélectionnées
                        </button>
                        <a class="btn btn-secondary d-inline-flex align-items-center" href="?action=tracks">
                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                            <i class="bi bi-arrow-left me-2"></i>
                            Retour à mes pistes
                        </a>
                    </div>
                </form>
            HTML;
        }

        return <<<HTML
            {$title}
            {$trackPreview}
            {$actionForm}
        HTML;
    }

    #[\Override]
    public function post() : string {
        $idTrack = isset($_POST['id_track']) ? (int) $_POST['id_track'] : 0;

        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        if (empty($_POST['id_playlists'])) {
            return HtmlHelper::formError(
                errors: "Veuillez sélectionner au moins une playlist.",
                backUrl: "?action=move-track&id={$idTrack}",
                backLabel: "Retour à la sélection",
                title: "Sélection requise"
            );
        }

        $w = DeefyRepository::getInstance();
        $addedPlaylists = [];

        foreach ($_POST['id_playlists'] as $playlistId) {
            if (!Authz::checkPlaylistOwner((int) $playlistId)) {
                return HtmlHelper::forbidden(
                    message: "Vous n'êtes pas autorisé à la playlist {$playlistId} l'une des playlists sélectionnées.",
                    backUrl: "?action=tracks",
                    backLabel: "Retour à mes pistes"
                );
            }

            $playlist = $r->findPlaylistById($playlistId);

            // Si la pistes est déjà dans la playlist, on l'ignore
            if ($r->isTrackInPlaylist($playlistId, $idTrack)) {
                continue;
            }

            $w->addTrackToPlaylist($playlistId, $idTrack);

            if (isset($_SESSION['playlist']) && (int) $_SESSION['playlist']->id === $playlistId) {
                $_SESSION['playlist']->addTrack($track);
            }

            $updatedPlaylist = $r->findPlaylistById($playlistId);
            $addedPlaylists[] = $updatedPlaylist ?? $playlist;
        }

        if (empty($addedPlaylists)) {
            return HtmlHelper::errorPage(
                title: "Piste non ajoutée",
                message: "La piste est déjà présente dans la ou les playlists sélectionnées.",
                backUrl: "?action=tracks",
                backLabel: "Retour à mes pistes",
                type: "warning"
            );
        }

        $countAdded = count($addedPlaylists);

        if ($countAdded === 1) {
            $playlist = $addedPlaylists[0];
            $countText = "{$playlist->trackCount} piste" . ($playlist->trackCount > 1 ? "s" : "");
            $msgHtml = "<p>La piste <strong>{$track->title}</strong> a été ajoutée avec succès à la playlist <strong>{$playlist->name}</strong> ({$countText}).</p>";

            $actionButtons = <<<HTML
                <a class="btn btn-primary d-inline-flex align-items-center" href="?action=display-playlist&id={$playlist->id}">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-list/ -->
                    <i class="bi bi-music-note-list me-2"></i>
                    Consulter la playlist
                </a>
                <a class="btn btn-secondary d-inline-flex align-items-center ms-2" href="?action=tracks">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                    <i class="bi bi-arrow-left me-2"></i>
                    Retour à mes pistes
                </a>
            HTML;
        } else {
            $listPlaylist = '';
            foreach ($addedPlaylists as $playlist) {
                $countText = "{$playlist->trackCount} piste" . ($playlist->trackCount > 1 ? "s" : "");
                $listPlaylist .= "<li><strong>{$playlist->name}</strong> ({$countText})</li>";
            }

            $msgHtml = <<<HTML
                <p>La piste <strong>{$track->title}</strong> a été ajoutée avec succès aux <strong>{$countAdded}</strong> playlists suivantes :</p>
                <ul class="mb-3">
                    {$listPlaylist}
                </ul>
            HTML;

            $actionButtons = <<<HTML
                <a class="btn btn-primary d-inline-flex align-items-center" href="?action=playlists">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/collection-play-fill/ -->
                    <i class="bi bi-collection-play-fill me-2"></i>Mes playlists
                </a>
                <a class="btn btn-secondary d-inline-flex align-items-center ms-2" href="?action=tracks">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                    <i class="bi bi-arrow-left me-2"></i>Retour à mes pistes
                </a>
            HTML;
        }

        $renderer = RendererFactory::getRenderer($track);
        $renderTrack = $renderer->render(Renderer::LONG);

        return <<<HTML
                <h1 class="h2 fw-bold text-success mb-3 d-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/check-circle-fill/ -->
                    <i class="bi bi-check-circle-fill text-success me-2"></i>Piste ajoutée avec succès
                </h1>
                {$msgHtml}
                <div class="my-3"><ul class="list-unstyled mb-0">
                    {$renderTrack}
                </ul>
            </div>
            <p class="mt-4">
                {$actionButtons}
            </p>
        HTML;
    }

    #[\Override]
    protected function check() : ?string {
        $idTrack = isset($_POST['id_track']) ? (int) $_POST['id_track'] : (isset($_GET['id']) ? (int) $_GET['id'] : 0);

        if ($idTrack < 1) {
            return HtmlHelper::errorPage(
                title: "Piste non spécifiée",
                message: "Aucun identifiant de piste valide n'a été fourni.",
                backUrl: "?action=tracks",
                backLabel: "Retour à mes pistes"
            );
        }

        $r = DeefyRepository::getInstance();
        $track = $r->findTrackById($idTrack);

        if ($track === null) {
            return HtmlHelper::notFound(
                item: "Piste",
                message: "La piste demandée n'existe pas.",
                backUrl: "?action=tracks",
                backLabel: "Retour à mes pistes"
            );
        }

        if (!Authz::checkTrackOwner($idTrack)) {
            return HtmlHelper::forbidden(
                message: "Vous n'êtes pas autorisé à manipuler cette piste.",
                backUrl: "?action=tracks",
                backLabel: "Retour à mes pistes"
            );
        }

        return null;
    }
}