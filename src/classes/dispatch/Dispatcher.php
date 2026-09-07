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
use iutnc\deefy\action\Signout;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\exception\AuthnException;

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
            "signout" => (new Signout())(),
            default => (new DefaultAction())(),
        };

        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        $page = <<<HTML
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <title>Deefy</title>
                <meta charset="utf-8">
            </head>
            <body>
            $html
            <ul>
                <li><a href="main.php">Page d'acceuil</a></li>
        HTML;
        try {
            AuthnProvider::getSignedInUser();

            $page .= <<<HTML
                <li><a href="main.php?action=display-playlist">Afficher mes playlists</a></li>
                <li><a href="main.php?action=playlist">Afficher la playlist de la session</a></li>
                <li><a href="main.php?action=add-playlist">Créer une playlist</a></li>
                <li><a href="main.php?action=add-track">Ajouter une piste</a></li>
                <li><a href="main.php?action=add-album-track">Ajouter un album</a></li>
                <li><a href="main.php?action=signout">Se déconnecter</a></li>
            HTML;
        } catch (AuthnException $e) {
            $page .= <<<HTML
                <li><a href="main.php?action=register">Inscription</a></li>
                <li><a href="main.php?action=signin">Se connecter</a></li>
            HTML;
        }

        $page .= <<<HTML
                </ul>
            </body>
        HTML;

        echo $page;
    }
}