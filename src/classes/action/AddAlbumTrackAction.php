<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\render\AlbumTrackRenderer;

class AddAlbumTrackAction extends Action {

    #[\Override]
    public function get() : string {
        return <<<HTML
        <form method="post" action="?action=add-album-track" enctype="multipart/form-data">
            <input type="text" name="title" placeholder="Titre">
            <input type="text" name="artist" placeholder="Artiste">
            <input type="text" name="album" placeholder="Album">
            <input type="number" name="year" placeholder="Année">
            <input type="number" name="trackNumber" placeholder="Numéro de piste">
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
        if (!isset($_POST['artist']) || $_POST['artist'] === '') {
            $error[] = "L'artiste est obligatoire.";
        }
        if (!isset($_POST['album']) || $_POST['album'] === '') {
            $error[] = "Le nom de l'album est obligatoire.";
        }
        if (!isset($_POST['year']) || $_POST['year'] === '') {
            $error[] = "L'année est obligatoire.";
        }
        if (!isset($_POST['trackNumber']) || $_POST['trackNumber'] === '') {
            $error[] = 'Le numéro de piste est obligatoire.';
        }

        if (!isset($_FILES['userfile'])) {
            $error[] = 'Le fichier audio est obligatoire.';
        } elseif ($_FILES['userfile']['name'] === '') {
            $error[] = 'Le fichier audio est obligatoire.';
        } elseif (substr($_FILES['userfile']['name'], -4) !== '.mp3') {
            $error[] = 'Le fichier doit être au format MP3.';
        }

        $audiofilename = '';

        if ($error == [] && isset($_FILES['userfile'])) {
            $newName = uniqid('', true) . bin2hex(random_bytes(4)) . '.mp3';
            $uploadFile = __DIR__ . '/../../../audio/' . basename($newName);

            if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
                $audiofilename = basename($newName);
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
                <a href="?action=add-album-track">Retour au formulaire</a>
            HTML;
        }

        $track = new AlbumTrack(
            filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS),
            $audiofilename,
            filter_var($_POST['album'], FILTER_SANITIZE_SPECIAL_CHARS),
            (int) filter_var($_POST['trackNumber'], FILTER_SANITIZE_NUMBER_INT),
        );
        $track->set('artist', filter_var($_POST['artist'], FILTER_SANITIZE_SPECIAL_CHARS));
        $track->set('year', (int) filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT));

        $_SESSION['playlist']->addPiste($track);
        $totalTracks = count($_SESSION['playlist']->tracks);
        $renderTrack = (new AlbumTrackRenderer($track))->render(0);
        return <<<HTML
            <p>La piste {$renderTrack} a été ajoutée avec succès à la playlist <strong>{$_SESSION['playlist']->name}</strong> !</p>
            <p>Nombre total de pistes : <strong>{$totalTracks}</strong></p>
            <p>
                <a href="?action=add-track">Ajouter une piste</a>
                 | 
                 <a href="?action=add-album-track">Ajouter encore un album</a>
                 | 
                <a href="?action=display-playlist">Retour à mes playlists</a>
            </p>
        HTML;
    }
}