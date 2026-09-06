<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AlbumTrack;

class AddPlaylistAction extends Action {
    #[\Override]
    public function get() : string {
        $html = '<form method="post" action="main.php?action=add-playlist">';
        $html .= '  <button type="submit">Créer la playlist</button>';
        $html .= '</form>';
        return $html;
    }

    #[\Override]
    public function post() : string {
        $track1 = new AlbumTrack("my funny valentine", "../audio/funny-valentine.mp3", "My Funny Valentine", 1);
        $track1->set("artist", "chet bake");
        $track1->set("year", 1954);
        $track1->set("genre", "classic jazz");
        $track1->set("duration", 187);

        $track2 = new AlbumTrack("LA MALÉDICTION", "../audio/la-malediction.mp3", "LA MALÉDICTION", 6);
        $track2->set("artist", "VIELUSOS");
        $track2->set("year", 2024);
        $track2->set("genre", "Frenchcore / Hardcore");
        $track2->set("duration", 160);

        $track3 = new AlbumTrack("Un monde à l'autre", "../audio/un-monde-a-l-autre.mp3", "Un monde à l'autre", 4);
        $track3->set("artist", "GP Explorer, GIMS, La Mano 1.9 & SCH");
        $track3->set("year", 2025);
        $track3->set("genre", "Rap");
        $track3->set("duration", 156);

        $playlist = new Playlist("Sport", [$track1, $track2, $track3]);

        $res = "";

        if (!isset($_SESSION['playlist'])) {
            $_SESSION['playlist'] = $playlist;
            $res .= '<h1>Initialisation de la playlist</h1>';
            $res .= "<p>Votre playlist <strong>" . $playlist->name . "</strong> a été créée avec succès avec <strong>" . count($playlist->tracks) . "</strong> pistes.</p>";
        } else {
            $res .= '<h1>Playlist existante</h1>';
            $res .= "<p>Votre playlist <strong>" . $_SESSION['playlist']->name . "</strong> existe déjà en session (contient <strong>" . count($_SESSION['playlist']->tracks) . "</strong> pistes).</p>";
        }
        return $res;
    }
}