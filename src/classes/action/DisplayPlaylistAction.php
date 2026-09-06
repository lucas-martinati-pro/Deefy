<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\render\AudioListRenderer;

class DisplayPlaylistAction extends Action {
    #[\Override]
    public function get() : string {
        $res = "";
        if (isset($_SESSION['playlist'])) {
            if (empty($_SESSION['playlist']->tracks)) {
                $res .= <<<HTML
                <h1>Playlist : {$_SESSION['playlist']->name}</h1>
                <p>La playlist est vide (aucune piste enregistrée).</p>
                HTML;
            }
            $playlist = $_SESSION['playlist'];
            $nbTracks = count($playlist->tracks);
            $res .= <<<HTML
                <h1>Playlist : {$playlist->name}</h1>
                <p>Nombre de pistes : <strong>{$nbTracks}</strong></p>
            HTML;
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