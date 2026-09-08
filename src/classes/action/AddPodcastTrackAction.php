<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\render\PodcastTrackRenderer;

class AddPodcastTrackAction extends Action {

    #[\Override]
    public function get() : string {
        return <<<HTML
        <form method="post" action="?action=add-track" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="nom de la piste">
            <input type="text" name="author" placeholder="nom de l'auteur">
            <input type="text" name="date" placeholder="date de sortie du morceaux">
            <input type="file" name="userfile" accept="audio/mpeg">
            <button type="submit">Ajouter une piste</button>
        </form>
        HTML;
    }

    #[\Override]
    public function post() : string {
        $error = [];

        if (!isset($_SESSION['playlist'])) {
            return <<<HTML
                <h1>Erreur</h1>
                <p>Aucune playlist n'a été trouvée en session. Veuillez d'abord initialiser la playlist.</p>
                <a href="?action=add-playlist">Ajouter une playlist</a>
            HTML;
        }

        if (!isset($_POST['title']) || $_POST['title'] === '') {
            $error[] = 'Le titre est obligatoire.';
        }
        if (!isset($_POST['author']) || $_POST['author'] === '') {
            $error[] = "L'auteur est obligatoire.";
        }
        if (!isset($_POST['date']) || $_POST['date'] === '') {
            $error[] = 'La date est obligatoire.';
        }

        if (!isset($_FILES['userfile'])) {
            $error[] = 'Le fichier audio est obligatoire.';
        } elseif ($_FILES['userfile']['name'] === '') {
            $error[] = 'Le fichier audio est obligatoire.';
        } elseif (substr($_FILES['userfile']['name'], -4) !== '.mp3') {
            $error[] = 'Le fichier doit être au format MP3.';
        }

        $audioPath = '';

        if ($error == [] && isset($_FILES['userfile'])) {
            $newName = uniqid('', true) . bin2hex(random_bytes(4)) . '.mp3';
            $uploadFile = __DIR__ . '/../../../audio/' . basename($newName);

            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
                $audioPath = basename($newName);
            } else {
                $error[] = "Le fichier n'a pas pu être enregistré.";
            }
        }

        if ($error != []) {
            $errorList = '';
            foreach ($error as $message) {
                $errorList .= "<li>{$message}</li>";
            }

            return <<<HTML
                <h1>Erreur dans le formulaire</h1>
                <ul>{$errorList}</ul>
                <a href="?action=add-track">Retour au formulaire</a>
            HTML;
        }

        $track = new PodcastTrack(filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS), $audioPath);
        $track->set('author', filter_var($_POST['author'], FILTER_SANITIZE_SPECIAL_CHARS));
        $track->set('date', filter_var($_POST['date'], FILTER_SANITIZE_SPECIAL_CHARS));

        $_SESSION['playlist']->addPiste($track);
        $totalTracks = count($_SESSION['playlist']->tracks);
        $renderTrack = (new PodcastTrackRenderer($track))->render(0);
        return <<<HTML
            <p>La piste {$renderTrack} a été ajoutée avec succès à la playlist <strong>{$_SESSION['playlist']->name}</strong> !</p>
            <p>Nombre total de pistes : <strong>{$totalTracks}</strong></p>
            <p>
                <a href="?action=add-track">Ajouter une autre piste</a>
                 | 
                 <a href="?action=add-album-track">Ajouter un album</a>
                 | 
                <a href="?action=display-playlist">Retour à mes playlists</a>
            </p>
        HTML;
    }
}