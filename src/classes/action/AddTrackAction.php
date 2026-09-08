<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\audio\tracks\AlbumTrack;

class AddTrackAction extends Action {

    #[\Override]
    public function get() : string {
        if (!isset($_SESSION['playlist'])) {
            return <<<HTML
                <h1>Erreur</h1>
                <p>Aucune playlist n'a été trouvée en session. Veuillez d'abord sélectionner ou créer une playlist.</p>
                <p><a class="btn btn-secondary" href="?action=playlists">Retour à mes playlists</a></p>
            HTML;
        }

        if (!Authz::checkPlaylistOwner($_SESSION['playlist']->id)) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>Vous n'êtes pas autorisé à modifier cette playlist.</p>
                <p><a class="btn btn-secondary" href="?action=playlists">Retour à mes playlists</a></p>
            HTML;
        }

        if (!isset($_GET['type'])) {
            return <<<HTML
                <h2>Choisir le type de piste à ajouter</h2>
                <div class="list-group mb-3" style="max-width: 450px;">
                    <a href="?action=add-track&type=PodcastTrack" class="list-group-item list-group-item-action">
                        Ajouter un Podcast à ma playlist
                    </a>
                    <a href="?action=add-track&type=AlbumTrack" class="list-group-item list-group-item-action">
                        Ajouter un Album à ma playlist
                    </a>
                </div>
                <p>
                    <a class="btn btn-secondary" href="?action=playlists">Retour à mes playlists</a>
                </p>
            HTML;
        }

        $action = "";
        $content = "";
        switch ($_GET['type']) {
            case 'AlbumTrack' : {
                $action = "?action=add-track&type=AlbumTrack";
                $content = <<<HTML
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du morceau</label>
                        <input type="text" name="title" class="form-control" id="title" placeholder="Ex : Bohemian Rhapsody" aria-describedby="titleHelp" required>
                        <div id="titleHelp" class="form-text">Saisissez le titre du morceau.</div>
                    </div>
                    <div class="mb-3">
                        <label for="artist" class="form-label">Artiste</label>
                        <input type="text" name="artist" class="form-control" id="artist" placeholder="Ex : Queen" aria-describedby="artistHelp" required>
                        <div id="artistHelp" class="form-text">Saisissez le nom de l'artiste ou du groupe.</div>
                    </div>
                    <div class="mb-3">
                        <label for="album" class="form-label">Album</label>
                        <input type="text" name="album" class="form-control" id="album" placeholder="Ex : A Night at the Opera" aria-describedby="albumHelp" required>
                        <div id="albumHelp" class="form-text">Saisissez le nom de l'album.</div>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Année</label>
                        <input type="number" name="year" class="form-control" id="year" placeholder="Ex : 1975" aria-describedby="yearHelp" required>
                        <div id="yearHelp" class="form-text">Année de sortie de l'album.</div>
                    </div>
                    <div class="mb-3">
                        <label for="trackNumber" class="form-label">Numéro de piste</label>
                        <input type="number" name="trackNumber" class="form-control" id="trackNumber" placeholder="Ex : 1" min="1" aria-describedby="trackNumberHelp" required>
                        <div id="trackNumberHelp" class="form-text">Position de la piste dans l'album.</div>
                    </div>
                    <div class="mb-3">
                        <label for="userfile" class="form-label">Fichier audio</label>
                        <input type="file" name="userfile" class="form-control" id="userfile" accept="audio/mpeg, .mp3" aria-describedby="fileHelp" required>
                        <div id="fileHelp" class="form-text">Sélectionnez un fichier audio au format MP3.</div>
                    </div>
                    <button class="btn btn-primary" type="submit">Ajouter la piste</button>
                HTML;
                break;
            }
            case 'PodcastTrack' : {
                $action = "?action=add-track&type=PodcastTrack";
                $content = <<<HTML
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du podcast</label>
                        <input type="text" name="title" class="form-control" id="title" placeholder="Ex : Épisode 1 : Les origines" aria-describedby="titleHelp" required>
                        <div id="titleHelp" class="form-text">Saisissez le nom de la piste / épisode.</div>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Auteur</label>
                        <input type="text" name="author" class="form-control" id="author" placeholder="Ex : Jean Dupont" aria-describedby="authorHelp" required>
                        <div id="authorHelp" class="form-text">Nom de l'auteur ou du créateur.</div>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date de sortie</label>
                        <input type="date" name="date" class="form-control" id="date" aria-describedby="dateHelp" required>
                        <div id="dateHelp" class="form-text">Date de sortie du morceau.</div>
                    </div>
                    <div class="mb-3">
                        <label for="userfile" class="form-label">Fichier audio</label>
                        <input type="file" name="userfile" class="form-control" id="userfile" accept="audio/mpeg, .mp3" aria-describedby="fileHelp" required>
                        <div id="fileHelp" class="form-text">Sélectionnez un fichier audio au format MP3.</div>
                    </div>
                    <button class="btn btn-primary" type="submit">Ajouter le podcast</button>
                HTML;
                break;
            }
        }

        return <<<HTML
            <form method="post" action="{$action}" enctype="multipart/form-data">
                {$content}
            </form>
            <p class="mt-3">
                <a class="btn btn-secondary" href="?action=playlists">Retour à mes playlists</a>
            </p>
        HTML;
    }

    #[\Override]
    public function post() : string {
        if (!isset($_SESSION['playlist'])) {
            return <<<HTML
                <h1>Erreur</h1>
                <p>Aucune playlist n'a été trouvée en session. Veuillez d'abord initialiser la playlist.</p>
                <p><a class="btn btn-primary" href="?action=add-playlist">Créer une playlist</a></p>
            HTML;
        }

        if (!Authz::checkPlaylistOwner($_SESSION['playlist']->id)) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>Vous n'êtes pas autorisé à modifier cette playlist.</p>
                <p><a class="btn btn-secondary" href="?action=playlists">Retour à mes playlists</a></p>
            HTML;
        }

        $error = [];

        $type = $_GET['type'] ?? '';
        if ($type === 'AlbumTrack') {
            if (empty($_POST['title']) || empty($_POST['artist']) || empty($_POST['album']) || empty($_POST['year']) || empty($_POST['trackNumber'])) {
                $error[] = "Tous les champs de l'album sont obligatoires.";
            }
        } elseif ($type === 'PodcastTrack') {
            if (empty($_POST['title']) || empty($_POST['author']) || empty($_POST['date'])) {
                $error[] = 'Tous les champs du podcast sont obligatoires.';
            }
        } else {
            $error[] = 'Le type de piste sélectionné est invalide.';
        }

        if (!isset($_FILES['userfile']) || !isset($_FILES['userfile']['name']) || substr($_FILES['userfile']['name'], -4) !== '.mp3') {
            $error[] = 'Le fichier doit être au format MP3.';
        }

        $audioPath = '';

        if (empty($error)) {
            $newName = uniqid('', true) . bin2hex(random_bytes(4)) . '.mp3';
            $audioDir = __DIR__ . '/../../../audio';
            if (!is_dir($audioDir)) {
                // Créer un dossier avec touts les droits pour Apache
                mkdir($audioDir, 0777, true);
            }
            $uploadFile = $audioDir . '/' . basename($newName);

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
                <div class="alert alert-danger" role="alert">
                    <ul class="mb-0 ps-3">{$errorList}</ul>
                </div>
                <p>
                    <a class="btn btn-secondary" href="?action=add-track">Retour au formulaire</a>
                </p>
            HTML;
        }

        $track = "";
        switch ($_GET['type'] ?? '') {
            case 'AlbumTrack' : {
                    $track = new AlbumTrack(
                    filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS),
                    $audioPath,
                    filter_var($_POST['album'], FILTER_SANITIZE_SPECIAL_CHARS),
                    (int) filter_var($_POST['trackNumber'], FILTER_SANITIZE_NUMBER_INT),
                );
                $track->set('artist', filter_var($_POST['artist'], FILTER_SANITIZE_SPECIAL_CHARS));
                $track->set('year', (int) filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT));
                break;
            }
            case 'PodcastTrack' : {
                $track = new PodcastTrack(filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS), $audioPath);
                $track->set('author', filter_var($_POST['author'], FILTER_SANITIZE_SPECIAL_CHARS));
                $track->set('date', $_POST['date']);
                break;
            }
            default : {
                return <<<HTML
                    <h1>Erreur</h1>
                    <p>Le type de piste sélectionné est invalide.</p>
                    <p><a class="btn btn-secondary" href="?action=add-track">Retour à l'ajout d'une piste</a></p>
                HTML;
            }
        }

        // Sauvegarde dans la playlist locale
        $_SESSION['playlist']->addPiste($track);

        // Sauvegarde dans le cloud
        $r = DeefyRepository::getInstance();
        $r->saveAudioTrack($track);
        $r->addTrackToPlaylist($_SESSION['playlist']->id, $track->get('id'));

        $totalTracks = count($_SESSION['playlist']->tracks);
        $renderer = RendererFactory::getRenderer($track);
        $renderTrack = $renderer ? $renderer->render(Renderer::COMPACT) : '';
        return <<<HTML
            <p>La piste {$renderTrack} a été ajoutée avec succès à la playlist <strong>{$_SESSION['playlist']->name}</strong> !</p>
            <p>Nombre total de pistes : <strong>{$totalTracks}</strong></p>
            <p>
                <a class="btn btn-primary" href="?action=add-track">Ajouter une autre piste</a>
                <a class="btn btn-secondary" href="?action=playlists">Retour à mes playlists</a>
            </p>
        HTML;
    }
}