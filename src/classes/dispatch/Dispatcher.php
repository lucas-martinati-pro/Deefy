<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddTrackAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\PlaylistsAction;
use iutnc\deefy\action\DisplayPlaylistAction;
use iutnc\deefy\action\RegisterAction;
use iutnc\deefy\action\Signin;
use iutnc\deefy\action\Signout;

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
            "signin" => (new Signin())(),
            "signout" => (new Signout())(),
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
                <h1><a href="main.php">Deefy</a></h1>
                $html
            </body>
        HTML;
    }
}