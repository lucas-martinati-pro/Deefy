<?php

namespace iutnc\deefy\dispatch;

use iutnc\deefy\action\AddPlaylistAction;
use iutnc\deefy\action\AddPodcastTrackAction;
use iutnc\deefy\action\DefaultAction;
use iutnc\deefy\action\DisplayPlaylistAction;

class Dispatcher {
    private string $action;

    public function __construct(string $action) {
        $this->action = $action;
    }

    public function run(): void {
        /**
        * $html = "";
        * switch ($this->action) {
        *     case "default" : {
        *         $html = new DefaultAction()->execute();
        *         break;
        *     }
        *     case "playlist" : {
        *         $html = new DisplayPlaylistAction()->execute();
        *         break;
        *     }
        *     case "add-playlist" : {
        *         $html = new AddPlaylistAction()->execute();
        *         break;
        *     }
        *     case "add-track" : {
        *         $html = new AddPodcastTrackAction()->execute();
        *         break;
        *     }
        * }
        */
        $html = match ($this->action) {
            "playlist" => (new DisplayPlaylistAction())(),
            "add-playlist" => (new AddPlaylistAction())(),
            "add-track" => (new AddPodcastTrackAction())(),
            default => (new DefaultAction())(),
        };

        $this->renderPage($html);
    }

    private function renderPage(string $html): void {
        $document = '<!DOCTYPE html>';
        $document .= '<html lang="fr">';
        $document .= '<head>';
        $document .= '    <title>Deefy</title>';
        $document .= '    <meta charset="utf-8">';
        $document .= '</head>';
        $document .= '<body>';
        $document .= $html;
        $document .= '  <nav>';
        $document .= '      <a href="main.php?action=playlist">Afficher la playlist</a> | ';
        $document .= '      <a href="main.php?action=add-playlist">Créer la playlist</a> | ';
        $document .= '      <a href="main.php?action=add-track">Ajouter une piste</a>';
        $document .= '  </nav>';
        $document .= '</body>';
        echo $document;
    }
}