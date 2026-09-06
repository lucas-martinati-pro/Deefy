<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\PlaylistAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\AddAlbumTrackAction;
use iutnc\deefy\action\RegisterAction;
use iutnc\deefy\action\Signin;

class Dispatcher {
    private string $action;

    public function __construct(string $action) {
        $this->action = $action;
    }

    public function run(): void {
        $html = match ($this->action) {
            "playlist" => (new PlaylistAction())(),
            "display-playlist" => (new DisplayPlaylistAction())(),
            "add-playlist" => (new AddPlaylistAction())(),
            "add-track" => (new AddPodcastTrackAction())(),
            "add-album-track" => (new AddAlbumTrackAction())(),
            "register" => (new RegisterAction())(),
            "signin" => (new Signin())(),
            default => (new DefaultAction())(),
        };

        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        echo <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <title>Deefy</title>
                <meta charset="utf-8">
            </head>
            <body>
            $html
            <ul>
                <li><a href="main.php?action=playlist">Afficher la playlist</a></li>
                <li><a href="main.php?action=add-playlist">Créer une playlist</a></li>
                <li><a href="main.php?action=add-track">Ajouter une piste</a></li>
                <li><a href="main.php?action=add-album-track">Ajouter un album</a></li>
                <li><a href="main.php?action=register">Inscription</a></li>
                <li><a href="main.php?action=signin">Se connecter</a></li>
                <li><a href="main.php">Page d'acceuil</a></li>
            </ul>
            </body>
        HTML;
    }
}