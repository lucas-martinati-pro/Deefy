<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\render\AudioListRenderer;

class PlaylistAction extends Action {
    #[\Override]
    public function get() : string {
        $res = "";
        if (isset($_SESSION['playlist'])) {
            $playlist = $_SESSION['playlist'];
            $res .= (new AudioListRenderer($playlist))->render();
        } else {
            $res .= <<<HTML
                <h1>Playlist</h1>
                <p>Aucune playlist n'existe actuellement en session.</p>
            HTML;
        }
        return $res;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}