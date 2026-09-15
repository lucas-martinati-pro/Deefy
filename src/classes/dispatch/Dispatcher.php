<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\DeletePlaylistAction;
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
use iutnc\deefy\render\AudioTrackRenderer;
use iutnc\deefy\repository\DeefyRepository;

class Dispatcher {
    private string $action;

    public function __construct(string $action) {
        $this->action = $action;
    }

    public function run(): void {
        $this->handleUserActions();

        $html = match ($this->action) {
            "playlists" => (new PlaylistsAction())(),
            "display-playlist" => (new DisplayPlaylistAction())(),
            "add-playlist" => (new AddPlaylistAction())(),
            "delete-playlist" => (new DeletePlaylistAction())(),
            "add-track" => (new AddTrackAction())(),
            "delete-track" => (new DeleteTrackAction())(),
            "register" => (new RegisterAction())(),
            "signin" => (new SigninAction())(),
            "signout" => (new SignoutAction())(),
            default => (new DefaultAction())(),
        };

        $this->renderPage($html);
    }

    private function handleUserActions() {
        // Si l'utilisateur clique sur "Lire" pour écouter une piste spécifique dans le lecteur principal
        if (isset($_POST['add-player-track'])) {
            $r = DeefyRepository::getInstance();
            $track = $r->findTrackById((int) $_POST['add-player-track']);
            if ($track != null) $_SESSION['playerTrack'] = $track;

            // Pour recharger là page là où en était
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }

        // Si l'utilisateur clique sur "x" pour supprimer la piste du lecteur principal
        if (isset($_POST['delete-player-track'])) {
            unset($_SESSION['playerTrack']);

            // Pour recharger là page là où en était
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit;
        }
    }

    private function renderPage(string $html): void {
        $navbar = $this->renderNavbar();
        $player = $this->renderFooterPlayer();

        echo <<<HTML
            <!DOCTYPE html>
            <html lang="fr" data-bs-theme="dark" id="html">
                <head>
                    <title>Deefy</title>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <!-- css pour Bootstrap -->
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
                    <!-- css pour les icônes Boostrap -->
                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
                    <link href="../css/style.css" rel="stylesheet">
                    <!-- Icône personnalisée pour le site -->
                    <link rel="icon" type="image/x-icon" href="../image/favicon.ico">
                    <!-- Script pour l'audio -->
                    <script type="module" src="https://cdn.jsdelivr.net/npm/player.style/tailwind-audio/+esm"></script>
                </head>
                <body class="d-flex flex-column min-vh-100">
                    <div class="flex-grow-1">
                        <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
                            <div class="container">
                                <a class="navbar-brand fw-bold text-primary d-flex align-items-center" href="main.php">
                                    <img src="../image/favicon.ico" alt="Logo" width="24" height="24" class="d-inline-block me-2 logo">
                                    Deefy
                                </a>
                                <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                                    <span class="navbar-toggler-icon"></span>
                                </button>
                                <div class="collapse navbar-collapse" id="navbarNav">
                                    {$navbar}
                                </div>
                            </div>
                        </nav>

                        <div class="container py-4">
                            <main>
                                $html
                            </main>
                        </div>
                    </div>
                    $player
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
                    <script src="../js/script.js"></script>
                </body>
            </html>
        HTML;
    }

    private function renderNavbar(): string {
        $toggleThemeButton = <<<HTML
            <button id="toggle-theme" class="btn btn-outline-secondary btn-sm d-flex align-items-center" title="Changer de thème">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/circle-half/ -->
                <i class="bi bi-circle-half"></i>
            </button>
        HTML;

        try {
            $user = AuthnProvider::getSignedInUser();

            $currentPlaylist = "";

            if (isset($_SESSION['playlist'])) {
                $currentPlaylist = <<<HTML
                    <li class="nav-item">
                        <a class="nav-link" href="?action=display-playlist">
                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-list/ -->
                            <i class="bi bi-music-note-list me-1"></i>Playlist courante
                        </a>
                    </li>
                HTML;
            }

            return <<<HTML
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
                    {$currentPlaylist}
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/person-circle/ -->
                    <i class="bi bi-person-circle"></i>
                    <span class="navbar-text small text-muted me-2">{$user['email']}</span>
                    <a class="btn btn-outline-danger btn-sm" href="?action=signout">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/box-arrow-right/ -->
                        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
                    </a>
                    {$toggleThemeButton}
                </div>
            HTML;
        } catch (AuthnException $e) {
            return <<<HTML
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
                    {$toggleThemeButton}
                </div>
            HTML;
        }
    }

    private function renderFooterPlayer() : string {
        if (isset($_SESSION['playerTrack'])) {
            $track = $_SESSION['playerTrack'];
            if (!empty($track)) {
                if ($track != null) {
                    $trackTitle = $track->get('title') ?? 'Piste audio';
                    $image = $track->get('image');

                    // Sous-titre : Artiste/Album pour un morceau d'album, Auteur pour un podcast
                    $subtitle = "";
                    if ($track instanceof AlbumTrack) {
                        $artist = $track->get('artist') ?? 'Artiste inconnu';
                        $album = $track->get('album') ?? '';
                        $subtitle = !empty($album) ? "{$artist} • {$album}" : $artist;
                    } elseif ($track instanceof PodcastTrack) {
                        $author = $track->get('author') ?? 'Auteur inconnu';
                        $subtitle = "Podcast • {$author}";
                    }

                    $imagePath = AudioTrackRenderer::IMAGE_PATH;

                    // Image de couverture à gauche
                    if (!empty($image)) {
                        $coverHtml = <<<HTML
                            <img src="{$imagePath}{$image}" class="rounded-2 object-fit-cover shadow-sm flex-shrink-0" style="width: 52px; height: 52px;" alt="{$trackTitle}">
                        HTML;
                    } else {
                        $coverHtml = <<<HTML
                            <div class="rounded-2 bg-body-tertiary border border-secondary-subtle d-flex align-items-center justify-content-center text-secondary shadow-sm flex-shrink-0" style="width: 52px; height: 52px;">
                                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/vinyl-fill/ -->
                                <i class="bi bi-vinyl-fill fs-4 text-primary"></i>
                            </div>
                        HTML;
                    }

                    $audioPath = AudioTrackRenderer::AUDIO_PATH;

                    return <<<HTML
                        <div style="height: 85px;"></div>
                        <footer class="fixed-bottom audio-footer-bar border-top shadow-lg py-2 px-3 z-3">
                            <div class="container-fluid d-flex align-items-center justify-content-between gap-3">
                                <!-- Section Gauche : Image + Titre/Artiste -->
                                <div class="d-flex align-items-center gap-3 flex-shrink-0" style="max-width: 300px;">
                                    {$coverHtml}
                                    <div class="text-truncate">
                                        <div class="fw-semibold text-body text-truncate small mb-0 d-none d-lg-flex" title="{$trackTitle}">{$trackTitle}</div>
                                        <div class="text-body-secondary text-truncate d-none d-lg-flex" style="font-size: 0.78rem;" title="{$subtitle}">{$subtitle}</div>
                                    </div>
                                </div>

                                <!-- Section Centre : Lecteur Audio -->
                                <div class="flex-grow-1">
                                    <media-theme-tailwind-audio class="audio-footer">
                                        <audio
                                            slot="media"
                                            src="{$audioPath}{$track->get("filename")}"
                                            crossorigin="anonymous"
                                            autoplay
                                        ></audio>
                                    </media-theme-tailwind-audio>
                                </div>

                                <!-- Section Droit : Supprimer le track en cours de lecture -->
                                <div>
                                    <form method="post" action="">
                                        <button class="btn btn-outline-danger btn-sm" name="delete-player-track" title="Fermer le lecteur">
                                            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/x-circle/ -->
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </footer>
                    HTML;
                }
            }
        }

        return "";
    }
}