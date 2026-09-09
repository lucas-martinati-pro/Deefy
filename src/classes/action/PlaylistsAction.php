<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class PlaylistsAction extends Action {
    #[\Override]
    public function get() : string {
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>{$e->getMessage()}</p>
                <p><a class="btn btn-primary" href="?action=signin">Se connecter</a></p>
            HTML;
        }

        $r = DeefyRepository::getInstance();
        $playlists = $r->findPlaylistsByUserId((int) $user['id']);

        if (empty($playlists)) {
            return <<<HTML
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2 fw-bold mb-0">Mes playlists</h1>
                    <a class="btn btn-primary" href="?action=add-playlist">
                        + Créer une playlist
                    </a>
                </div>
                <div class="alert alert-info" role="alert">
                    Vous ne possédez aucune playlist pour le moment.
                </div>
                <p>
                    <a class="btn btn-primary" href="?action=add-playlist">Créer votre première playlist</a>
                </p>
            HTML;
        }

        $cardsHtml = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">';
        foreach ($playlists as $pl) {
            $trackCount = (int) $pl->trackCount;
            $totalDuration = (int) $pl->totalDuration;

            // Récupérer la première pochette disponible parmi les pistes
            $coverImage = null;
            foreach ($pl->tracks as $t) {
                if ($coverImage === null && !empty($t->get('image'))) {
                    $coverImage = $t->get('image');
                }
            }

            if ($coverImage !== null) {
                $coverHtml = <<<HTML
                    <img src="../image/{$coverImage}" class="card-img-top object-fit-cover w-100 h-100" alt="{$pl->name}">
                HTML;
            } else {
                $coverHtml = <<<HTML
                    <div class="card-img-top bg-light d-flex flex-column align-items-center justify-content-center text-muted border-bottom w-100 h-100">
                        <!-- Icône BootStrap - https://icons.getbootstrap.com/icons/music-note-list/ -->
                        <i class="bi bi-music-note-list fs-1"></i>
                        <span class="small text-secondary">Playlist</span>
                    </div>
                HTML;
            }

            $cardsHtml .= <<<HTML
                <div class="col d-flex align-items-stretch">
                    <div class="card shadow-sm h-100 w-100 border overflow-hidden">
                        <div class="position-relative" style="height: 180px;">
                            {$coverHtml}
                            <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75">
                                {$trackCount} piste(s)
                            </span>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between p-3">
                            <h5 class="card-title fw-bold mb-1 text-truncate" title="{$pl->name}">{$pl->name}</h5>
                            <p class="card-text text-muted small mb-2">
                                Durée : <strong>{$totalDuration}s</strong>
                            </p>
                            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                                <a href="?action=display-playlist&id={$pl->id}" class="btn btn-outline-primary btn-sm stretched-link">
                                    Consulter la playlist
                                </a>
                                <span class="text-muted small">{$trackCount} morceau(x)</span>
                            </div>
                        </div>
                    </div>
                </div>
            HTML;
        }

        return <<<HTML
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h2 fw-bold mb-1">Mes playlists</h1>
                        <p class="text-muted mb-0">Consultez et gérez vos listes de lecture</p>
                    </div>
                    <a class="btn btn-primary" href="?action=add-playlist">
                        + Créer une playlist
                    </a>
                </div>
                {$cardsHtml}
            </div>
        HTML;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}