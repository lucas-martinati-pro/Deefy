<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddTrackAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\PlaylistsAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\RegisterAction;
use iutnc\deefy\action\SigninAction;
use iutnc\deefy\action\SignoutAction;
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
                        <a class="nav-link" href="main.php">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=playlists">Mes playlists</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=add-playlist">+ Créer une playlist</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="?action=display-playlist">Playlist courante</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <span class="navbar-text small text-muted me-2">{$user['email']}</span>
                    <a class="btn btn-outline-danger btn-sm" href="?action=signout">Déconnexion</a>
                </div>
            HTML;
        } catch (AuthnException $e) {
            $navLinks = <<<HTML
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="main.php">Accueil</a>
                    </li>
                </ul>
                <div class="d-flex gap-2">
                    <a class="btn btn-outline-primary btn-sm" href="?action=signin">Connexion</a>
                    <a class="btn btn-primary btn-sm" href="?action=register">Inscription</a>
                </div>
            HTML;
        }

        $player = "";
        if (isset($_SESSION['playlist'])) {
            $r = \iutnc\deefy\repository\DeefyRepository::getInstance();
            $track = $_SESSION['playlist']->tracks;
            if (!empty($track)) {
                $audioTrack = $track[0];
                if ($audioTrack != null) {
                    $player = <<<HTML
                        <media-theme-tailwind-audio class="audio-footer">
                            <audio
                                slot="media"
                                src="../audio/{$audioTrack->get("filename")}"
                                crossorigin="anonymous"
                            ></audio>
                        </media-theme-tailwind-audio>
                    HTML;
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
                    <!-- Script pour Bootstrap -->
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
                    <!-- Script pour l'audio -->
                    <script type="module" src="https://cdn.jsdelivr.net/npm/player.style/tailwind-audio/+esm"></script>
                </head>
                <body class="d-flex flex-column min-vh-100">
                    <div class="flex-grow-1">
                        <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom">
                            <div class="container">
                                <a class="navbar-brand fw-bold text-primary text-black" href="main.php">Deefy</a>
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
                    $player
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
                </body>
            </html>
        HTML;
    }
}