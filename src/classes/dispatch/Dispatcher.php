<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddTrackAction;
use iutnc\deefy\action\DeleteTrackAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\PlaylistsAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\RegisterAction;
use iutnc\deefy\action\SigninAction;
use iutnc\deefy\action\SignoutAction;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

class Dispatcher {
    private string $action;

    public function __construct(string $action) {
        $this->action = $action;
    }

    public function run(): void {
        $html = match ($this->action) {
            "playlists" => (new PlaylistsAction())(),
            "display-playlist" => (new DisplayPlaylistAction())(),
            "add-playlist" => (new AddPlaylistAction())(),
            "add-track" => (new AddTrackAction())(),
            "delete-track" => (new DeleteTrackAction())(),
            "register" => (new RegisterAction())(),
            "signin" => (new SigninAction())(),
            "signout" => (new SignoutAction())(),
            default => (new DefaultAction())(),
        };

        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        $navLinks = '';
        try {
            $user = AuthnProvider::getSignedInUser();

            $navLinks = <<<HTML
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="main.php">
                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/house-door-fill/ -->
                            <i class="bi bi-house-door-fill me-1"></i>Accueil
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=playlists">
                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/collection-play-fill/ -->
                            <i class="bi bi-collection-play-fill me-1"></i>Mes playlists
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=add-playlist">
                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-circle-fill/ -->
                            <i class="bi bi-plus-circle-fill me-1"></i>Créer une playlist
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=display-playlist">
                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-list/ -->
                            <i class="bi bi-music-note-list me-1"></i>Playlist courante
                        </a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/person-circle/ -->
                    <i class="bi bi-person-circle"></i>
                    <span class="navbar-text small text-muted me-2">{$user['email']}</span>
                    <a class="btn btn-outline-danger btn-sm" href="?action=signout">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/box-arrow-right/ -->
                        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                    </a>
                </div>
            HTML;
        } catch (AuthnException $e) {
            $navLinks = <<<HTML
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="main.php">
                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/house-door-fill/ -->
                            <i class="bi bi-house-door-fill me-1"></i>Accueil
                        </a>
                    </li>
                </ul>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-primary btn-sm" href="?action=signin">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/box-arrow-in-right/ -->
                        <i class="bi bi-box-arrow-in-right me-1"></i>Connexion
                    </a>
                    <a class="btn btn-primary btn-sm" href="?action=register">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/person-plus-fill/ -->
                        <i class="bi bi-person-plus-fill me-1"></i>Inscription
                    </a>
                </div>
            HTML;
        }

        $player = "";
        $bottomSpacer = "";
        if (isset($_SESSION['playlist'])) {
            $tracks = $_SESSION['playlist']->tracks;
            if (!empty($tracks)) {
                $audioTrack = $_SESSION['current_track'] ?? $tracks[0];
                if ($audioTrack != null) {
                    $trackTitle = $audioTrack->get('title') ?? 'Piste audio';
                    $image = $audioTrack->get('image');
                    $playlistName = $_SESSION['playlist']->name ?? '';

                    // Sous-titre : Artiste/Album pour un morceau d'album, Auteur pour un podcast
                    $subtitle = "";
                    if ($audioTrack instanceof AlbumTrack) {
                        $artist = $audioTrack->get('artist') ?? 'Artiste inconnu';
                        $album = $audioTrack->get('album') ?? '';
                        $subtitle = !empty($album) ? "{$artist} • {$album}" : $artist;
                    } elseif ($audioTrack instanceof PodcastTrack) {
                        $author = $audioTrack->get('author') ?? 'Auteur inconnu';
                        $subtitle = "Podcast • {$author}";
                    } else {
                        $subtitle = $playlistName;
                    }

                    // Image de couverture à gauche
                    if (!empty($image)) {
                        $coverHtml = <<<HTML
                            <img src="../image/{$image}" class="rounded-2 object-fit-cover shadow-sm flex-shrink-0" style="width: 52px; height: 52px;" alt="{$trackTitle}">
                        HTML;
                    } else {
                        $coverHtml = <<<HTML
                            <div class="rounded-2 bg-light border d-flex align-items-center justify-content-center text-secondary shadow-sm flex-shrink-0" style="width: 52px; height: 52px;">
                                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/vinyl-fill/ -->
                                <i class="bi bi-vinyl-fill fs-4 text-primary"></i>
                            </div>
                        HTML;
                    }

                    $playlistBadge = '';
                    if (!empty($playlistName)) {
                        $playlistBadge = <<<HTML
                            <div class="d-none d-lg-flex align-items-center text-muted small ms-3 flex-shrink-0" style="max-width: 220px;">
                                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/collection-play-fill/ -->
                                <i class="bi bi-collection-play-fill text-primary me-2"></i>
                                <span class="text-truncate" title="{$playlistName}">{$playlistName}</span>
                            </div>
                        HTML;
                    }

                    $player = <<<HTML
                        <footer class="fixed-bottom bg-white border-top shadow-lg py-2 px-3 z-3">
                            <div class="container-fluid d-flex align-items-center justify-content-between gap-3">
                                <!-- Section Gauche : Image + Titre/Artiste (Style YouTube Music) -->
                                <div class="d-flex align-items-center gap-3 flex-shrink-0" style="max-width: 300px;">
                                    {$coverHtml}
                                    <div class="text-truncate">
                                        <div class="fw-semibold text-dark text-truncate small mb-0 d-none d-lg-flex" title="{$trackTitle}">{$trackTitle}</div>
                                        <div class="text-muted text-truncate d-none d-lg-flex" style="font-size: 0.78rem;" title="{$subtitle}">{$subtitle}</div>
                                    </div>
                                </div>

                                <!-- Section Centre : Lecteur Audio -->
                                <div class="flex-grow-1">
                                    <media-theme-tailwind-audio class="audio-footer">
                                        <audio
                                            slot="media"
                                            src="../audio/{$audioTrack->get("filename")}"
                                            crossorigin="anonymous"
                                        ></audio>
                                    </media-theme-tailwind-audio>
                                </div>

                                <!-- Section Droite : Info Playlist -->
                                {$playlistBadge}
                            </div>
                        </footer>
                    HTML;

                    $bottomSpacer = '<div style="height: 85px;"></div>';
                }
            }
        }

        echo <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
                <head>
                    <title>Deefy</title>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <link href="../style/style.css" rel="stylesheet">
                    <!-- css pour Bootstrap -->
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
                    <!-- css pour les icônes Boostrap -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
                    <!-- Script pour l'audio -->
                    <script type="module" src="https://cdn.jsdelivr.net/npm/player.style/tailwind-audio/+esm"></script>
                </head>
                <body class="d-flex flex-column min-vh-100">
                    <div class="flex-grow-1">
                        <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
                            <div class="container">
                                <a class="navbar-brand fw-bold text-primary text-black d-flex align-items-center" href="main.php">
                                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/vinyl-fill/ -->
                                    <i class="bi bi-vinyl-fill text-primary me-2"></i>Deefy
                                </a>
                                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                                <div class="collapse navbar-collapse" id="navbarNav">
                                    {$navLinks}
                                </div>
                            </div>
                        </nav>

                        <div class="container py-4">
                            <main>
                                $html
                            </main>
                        </div>
                    </div>
                    $bottomSpacer
                    $player
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
                </body>
            </html>
        HTML;
    }
}