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

        $retour = '<a class="btn btn-secondary" href="?action=add-track">Retour</a>';
        $require = '<span style="color: red;">*</span>';
        switch ($_GET['type']) {
            case 'AlbumTrack' : {
                $action = "?action=add-track&type=AlbumTrack";
                $content = <<<HTML
                    $retour
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du morceau$require</label>
                        <input type="text" name="title" class="form-control" id="title" placeholder="Ex : Bohemian Rhapsody" aria-describedby="titleHelp" required>
                        <div id="titleHelp" class="form-text">Saisissez le titre du morceau.</div>
                    </div>
                    <div class="mb-3">
                        <label for="artist" class="form-label">Artiste$require</label>
                        <input type="text" name="artist" class="form-control" id="artist" placeholder="Ex : Queen" aria-describedby="artistHelp" required>
                        <div id="artistHelp" class="form-text">Saisissez le nom de l'artiste ou du groupe.</div>
                    </div>
                    <div class="mb-3">
                        <label for="album" class="form-label">Album$require</label>
                        <input type="text" name="album" class="form-control" id="album" placeholder="Ex : A Night at the Opera" aria-describedby="albumHelp" required>
                        <div id="albumHelp" class="form-text">Saisissez le nom de l'album.</div>
                    </div>
                    <div class="mb-3">
                        <label for="year" class="form-label">Année</label>
                        <input type="number" name="year" class="form-control" id="year" placeholder="Ex : 1975" aria-describedby="yearHelp">
                        <div id="yearHelp" class="form-text">Année de sortie de l'album.</div>
                    </div>
                    <div class="mb-3">
                        <label for="trackNumber" class="form-label">Numéro de piste</label>
                        <input type="number" name="trackNumber" class="form-control" id="trackNumber" placeholder="Ex : 1" min="1" aria-describedby="trackNumberHelp">
                        <div id="trackNumberHelp" class="form-text">Position de la piste dans l'album.</div>
                    </div>
                    <div class="mb-3">
                        <label for="userfile" class="form-label">Fichier audio$require</label>
                        <input type="file" name="userfile" class="form-control" id="userfile" accept="audio/mpeg, .mp3" aria-describedby="fileHelp" required>
                        <div id="fileHelp" class="form-text">Sélectionnez un fichier audio au format MP3.</div>
                    </div>
                    <div class="mb-3">
                        <label for="coverfile" class="form-label">Pochette de l'album</label>
                        <input type="file" name="coverfile" class="form-control" id="coverfile" accept="image/png, image/jpeg, image/webp" aria-describedby="coverHelp">
                        <div id="coverHelp" class="form-text">Sélectionnez une image (JPG, PNG ou WEBP).</div>
                    </div>
                    <button class="btn btn-primary" type="submit">Ajouter l'album</button>
                HTML;
                break;
            }
            case 'PodcastTrack' : {
                $action = "?action=add-track&type=PodcastTrack";
                $content = <<<HTML
                    $retour
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre du podcast$require</label>
                        <input type="text" name="title" class="form-control" id="title" placeholder="Ex : Épisode 1 : Les origines" aria-describedby="titleHelp" required>
                        <div id="titleHelp" class="form-text">Saisissez le nom de la piste / épisode.</div>
                    </div>
                    <div class="mb-3">
                        <label for="author" class="form-label">Auteur$require</label>
                        <input type="text" name="author" class="form-control" id="author" placeholder="Ex : Jean Dupont" aria-describedby="authorHelp" required>
                        <div id="authorHelp" class="form-text">Nom de l'auteur ou du créateur.</div>
                    </div>
                    <div class="mb-3">
                        <label for="date" class="form-label">Date de sortie</label>
                        <input type="date" name="date" class="form-control" id="date" aria-describedby="dateHelp">
                        <div id="dateHelp" class="form-text">Date de sortie du morceau.</div>
                    </div>
                    <div class="mb-3">
                        <label for="userfile" class="form-label">Fichier audio$require</label>
                        <input type="file" name="userfile" class="form-control" id="userfile" accept="audio/mpeg, .mp3" aria-describedby="fileHelp" required>
                        <div id="fileHelp" class="form-text">Sélectionnez un fichier audio au format MP3.</div>
                    </div>
                    <div class="mb-3">
                        <label for="coverfile" class="form-label">Pochette de l'album</label>
                        <input type="file" name="coverfile" class="form-control" id="coverfile" accept="image/png, image/jpeg, image/webp" aria-describedby="coverHelp">
                        <div id="coverHelp" class="form-text">Sélectionnez une image (JPG, PNG ou WEBP).</div>
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
            if (empty($_POST['title']) || empty($_POST['artist']) || empty($_POST['album'])) {
                $error[] = "Le titre, l'artiste ou le nom de l'album est manquant.";
            }
        } elseif ($type === 'PodcastTrack') {
            if (empty($_POST['title']) || empty($_POST['author'])) {
                $error[] = 'Le titre ou l\'auteur est manquant.';
            }
        } else {
            $error[] = 'Le type de piste sélectionné est invalide.';
        }

        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] !== UPLOAD_ERR_OK) {
            $error[] = "Erreur lors de l'envoi du fichier audio.";
        } elseif (empty($_FILES['userfile']['name']) || strtolower(pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION)) !== 'mp3') {
            $error[] = 'Le fichier doit être au format MP3.';
        }

        $audioPath = '';
        $uploadFile = '';

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

        if (!empty($_POST['date'])) {
            $d = \DateTime::createFromFormat('Y-m-d', trim($_POST['date']));

            if (!$d) {
                $error[] = "La date de sortie est invalide (format attendu : AAAA-MM-JJ).";
            } elseif ($d > new \DateTime()) {
                // refuser une date dans le futur
                $error[] = "La date de sortie ne peut pas être dans le futur.";
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

        $getID3 = new \getID3();
        $fileInfo = $getID3->analyze($uploadFile);

        $genre = $fileInfo['tags']['id3v2']['genre'][0]
            ?? $fileInfo['tags']['id3v1']['genre'][0]
            ?? null;
        $titre = $fileInfo['tags']['id3v2']['title'][0]
            ?? $fileInfo['tags']['id3v2']['title'][0]
            ?? filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
        $duree = isset($fileInfo['playtime_seconds'])
            ? (int) round($fileInfo['playtime_seconds'])
            : 0;

        $imagePath = null;
        $imageDir = __DIR__ . '/../../../image';
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0777, true);
        }

        // Priorité à l'image des métadonnées ID3
        if (!empty($fileInfo['comments']['picture'][0]['data'])) {
            $picture = $fileInfo['comments']['picture'][0];
            $typeImg = $picture['image_mime']
                ?? 'image/jpeg';
            $extension = ($typeImg === 'image/png') ? 'png' : 'jpg';

            $imageName = uniqid('cover_', true) . bin2hex(random_bytes(4)) . '.' . $extension;
            if (file_put_contents($imageDir . '/' . $imageName, $picture['data']) !== false) {
                $imagePath = $imageName;
            }
        } // fichier uploadé manuellement par l'utilisateur
        elseif (isset($_FILES['coverfile']) && $_FILES['coverfile']['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($_FILES['coverfile']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($extension, $allowedExts)) {
                $imageName = uniqid('cover_', true) . bin2hex(random_bytes(4)) . '.' . $extension;
                if (move_uploaded_file($_FILES['coverfile']['tmp_name'], $imageDir . '/' . $imageName)) {
                    $imagePath = $imageName;
                }
            }
        }

        $track = null;
        switch ($_GET['type'] ?? '') {
            case 'AlbumTrack' : {
                $id3Artist = $fileInfo['tags']['id3v2']['artist'][0]
                    ?? $fileInfo['tags']['id3v1']['artist'][0]
                    ?? null;
                $artist = (!empty($id3Artist) && trim($id3Artist) !== '')
                    ? filter_var(trim($id3Artist), FILTER_SANITIZE_SPECIAL_CHARS)
                    : filter_var(trim($_POST['artist'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);

                $id3Album = $fileInfo['tags']['id3v2']['album'][0]
                    ?? $fileInfo['tags']['id3v1']['album'][0]
                    ?? null;
                $album = (!empty($id3Album) && trim($id3Album) !== '')
                    ? filter_var(trim($id3Album), FILTER_SANITIZE_SPECIAL_CHARS)
                    : filter_var(trim($_POST['album'] ?? ''), FILTER_SANITIZE_SPECIAL_CHARS);

                $id3Year = $fileInfo['tags']['id3v2']['year'][0]
                    ?? $fileInfo['tags']['id3v1']['year'][0]
                    ?? null;
                $year = null;
                if (!empty($id3Year) && (int) $id3Year > 0) $year = (int) $id3Year;
                elseif (!empty($_POST['year']) && (int) $_POST['year'] > 0) {
                    $year = (int) filter_var($_POST['year'], FILTER_SANITIZE_NUMBER_INT);
                }

                $id3TrackNum = $fileInfo['tags']['id3v2']['track_number'][0]
                    ?? null;
                $trackNumber = 1;
                if (!empty($id3TrackNum) && (int) $id3TrackNum > 0) $trackNumber = (int) $id3TrackNum;
                elseif (!empty($_POST['trackNumber']) && (int) $_POST['trackNumber'] > 0) {
                    $trackNumber = (int) filter_var($_POST['trackNumber'], FILTER_SANITIZE_NUMBER_INT);
                }

                $track = new AlbumTrack(
                    $titre,
                    $audioPath,
                    $album,
                    $trackNumber
                );
                if (!empty($artist)) $track->set('artist', $artist);
                if ($year !== null) $track->set('year', $year);
                break;
            }
            case 'PodcastTrack' : {
                $id3Artist = $fileInfo['tags']['id3v2']['artist'][0]
                    ?? $fileInfo['tags']['id3v1']['artist'][0]
                    ?? null;
                $author = (!empty($_POST['author']) && trim($_POST['author']) !== '')
                    ? filter_var(trim($_POST['author']), FILTER_SANITIZE_SPECIAL_CHARS)
                    : (!empty($id3Artist)
                    ? filter_var(trim($id3Artist), FILTER_SANITIZE_SPECIAL_CHARS)
                    : null
                );

                $date = (!empty($_POST['date']) && trim($_POST['date']) !== '')
                    ? filter_var(trim($_POST['date']), FILTER_SANITIZE_SPECIAL_CHARS)
                    : null;

                $track = new PodcastTrack($titre, $audioPath);
                if (!empty($author)) $track->set('author', $author);
                if (!empty($date)) $track->set('date', $date);
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

        if ($duree > 0) $track->set('duration', $duree);
        if (!empty($genre)) $track->set('genre', $genre);
        if (!empty($imagePath)) $track->set('image', $imagePath);

        // Sauvegarde dans la playlist locale
        $_SESSION['playlist']->addPiste($track);

        // Sauvegarde dans le cloud
        $r = DeefyRepository::getInstance();
        $r->saveAudioTrack($track);
        $r->addTrackToPlaylist($_SESSION['playlist']->id, $track->get('id'));

        $totalTracks = count($_SESSION['playlist']->tracks);
        $renderer = RendererFactory::getRenderer($track);
        $renderTrack = $renderer ? $renderer->render(Renderer::LONG) : '';
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