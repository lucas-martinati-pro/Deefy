<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\audio\tracks\PodcastTrack;

class AddPodcastTrackAction extends Action {

    #[\Override]
    public function get() : string {
        $html = '<form method="post" action="main.php?action=add-track">';
        $html .= '  <button type="submit">Ajouter une piste</button>';
        $html .= '</form>';
        return $html;
    }

    #[\Override]
    public function post() : string {
        $track = new PodcastTrack("OMG", "../audio/omg.mp3");
        $track->set("author", "mwa");
        $track->set("date", "2026");
        $track->set("genre", "ROCKKKKKK");
        $track->set("duration", 5);

        $res = "";

        if (isset($_SESSION['playlist'])) {
            $_SESSION['playlist']->addPiste($track);
            $res .= "<p>La piste <strong>" . $track->get('title') . "</strong> de <em>" . $track->get('artist') . "</em> a été ajoutée avec succès à la playlist <strong>" . $_SESSION['playlist']->name . "</strong> !</p>";
            $res .= "<p>Nombre total de pistes : <strong>" . count($_SESSION['playlist']->tracks) . "</strong></p>";
        } else {
            $res .= "<h1>Erreur</h1>";
            $res .= "<p>Aucune playlist n'a été trouvée en session. Veuillez d'abord initialiser la playlist.</p>";
        }
        return $res;
    }
}