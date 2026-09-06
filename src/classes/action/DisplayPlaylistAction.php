<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;

class DisplayPlaylistAction extends Action {
    #[\Override]
    public function get() : string {
        $res = "";
        if (isset($_SESSION['playlist']) && !empty($_SESSION['playlist']->tracks)) {
            $playlist = $_SESSION['playlist'];
            $nbTracks = count($playlist->tracks);
            $totalDuree = $playlist->totalDuration;

            $res .= "<h1>Playlist : " . $playlist->name . "</h1>";
            $res .= "<p>Nombre de pistes : <strong>" . $nbTracks . "</strong></p>";

            $res .= "<ol>";
            $res .= (new AudioListRenderer($playlist))->render(Renderer::LONG);
            $res .= "</ol>";

            $res .= "<p><strong>Durée totale :</strong> " . $totalDuree . " secondes</p>";
        } elseif (isset($_SESSION['playlist'])) {
            $res .= "<h1>Playlist : " . $_SESSION['playlist']->name . "</h1>";
            $res .= "<p>La playlist est vide (aucune piste enregistrée).</p>";
        } else {
            $res .= "<h1>Playlist</h1>";
            $res .= "<p>Aucune playlist n'existe actuellement en session.</p>";
        }
        return $res;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}