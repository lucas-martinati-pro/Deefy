<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;

class AddPlaylistAction extends Action {
    #[\Override]
    public function get() : string {
        return <<<HTML
        <form method="post" action="?action=add-playlist">
          <input type="text" name="title" placeholder="nom de la playlist">
          <button type="submit">Créer la playlist</button>
        </form>
        HTML;
    }

    #[\Override]
    public function post() : string {
        $error = [];

        if (!isset($_POST['title']) || $_POST['title'] === '') {
            $error[] = 'Le nom de la playlist est obligatoire.';
        }

        if ($error != []) {
            $errorList = '';
            foreach ($error as $message) {
                $errorList .= "<li>{$message}</li>";
            }

            return <<<HTML
                <h1>Erreur dans le formulaire</h1>
                <ul>{$errorList}</ul>
                <a href="?action=add-playlist">Retour au formulaire</a>
            HTML;
        }

        if (!isset($_SESSION['playlist'])) {
            $_SESSION['playlist'] = new Playlist(filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS), []);
            $totalTracks = count($_SESSION['playlist']->tracks);

            $res = <<<HTML
                <h1>Initialisation de la playlist</h1>
                <p>Votre playlist <strong>{$_SESSION['playlist']->name}</strong> a été créée avec succès avec <strong>{$totalTracks}</strong> pistes.</p>
            HTML;
        } else {
            $totalTracks = count($_SESSION['playlist']->tracks);

            $res = <<<HTML
                <h1>Playlist existante</h1>
                <p>Votre playlist <strong>{$_SESSION['playlist']->name}</strong> existe déjà en session (contient <strong>{$totalTracks}</strong> pistes).</p>
            HTML;
        }

        $listRender = (new AudioListRenderer($_SESSION['playlist']))->render();

        $res .= <<<HTML
            {$listRender}
            <a href="?action=add-track">Ajouter une piste</a>
        HTML;

        return $res;
    }
}