<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\enums\TypeRender;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\render\HtmlHelper;
use iutnc\deefy\config\Config;

/**
 * Action permettant d'ajouter un morceau d'album ou un podcast à une playlist ou à ses pistes personnelles.
 */
class AddTrackAction extends Action {
    #[\Override]
    public function get() : string {
        $idPlaylist = isset($_GET['id']) ? (int) $_GET['id'] : null;

        $idParam = $idPlaylist !== null ? "&id={$idPlaylist}" : "";
        $backUrl = $idPlaylist !== null ? "?action=display-playlist&id={$idPlaylist}" : "?action=tracks";
        $backLabel = $idPlaylist !== null ? "Retour à la playlist" : "Retour à mes pistes";

        if (!isset($_GET['type'])) {
            $destLabel = $idPlaylist !== null ? "à ma playlist" : "à mes pistes";
            return <<<HTML
                <h2 class="h3 fw-bold mb-3 d-flex align-items-center">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-circle-fill/ -->
                    <i class="bi bi-plus-circle-fill text-primary me-2"></i>Choisir le type de piste à ajouter
                </h2>
                <div class="list-group mb-3" style="max-width: 450px;">
                    <a href="?action=add-track&type=AlbumTrack{$idParam}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/vinyl-fill/ -->
                        <i class="bi bi-vinyl-fill text-primary me-2 fs-5"></i>
                        Ajouter un Album {$destLabel}
                    </a>
                    <a href="?action=add-track&type=PodcastTrack{$idParam}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/mic-fill/ -->
                        <i class="bi bi-mic-fill text-danger me-2 fs-5"></i>
                        Ajouter un Podcast {$destLabel}
                    </a>
                </div>
                <p>
                    <a class="btn btn-secondary d-inline-flex align-items-center" href="{$backUrl}">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                        <i class="bi bi-arrow-left me-2"></i>
                        {$backLabel}
                    </a>
                </p>
            HTML;
        }

        $content = "";

        $retour = <<<HTML
            <p>
                <a class="btn btn-outline-secondary d-inline-flex align-items-center" href="?action=add-track{$idParam}">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                    <i class="bi bi-arrow-left me-2"></i>
                    Retour
                </a>
            </p>
        HTML;
        $require = '<span class="text-danger">*</span>';
        switch ($_GET['type']) {
            case 'AlbumTrack' : {
                $content = <<<HTML
                    $retour
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre de la piste$require</label>
                        <input type="text" name="title" class="form-control" id="title" placeholder="Ex : Bohemian Rhapsody" aria-describedby="titleHelp" required>
                        <div id="titleHelp" class="form-text">Saisissez le titre de la piste.</div>
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
                    <button class="btn btn-primary d-inline-flex align-items-center" type="submit">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-lg/ -->
                        <i class="bi bi-plus-lg me-1"></i>
                        Ajouter l'album
                    </button>
                HTML;
                break;
            }
            case 'PodcastTrack' : {
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
                        <div id="dateHelp" class="form-text">Date de sortie de la piste.</div>
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
                    <button class="btn btn-primary d-inline-flex align-items-center" type="submit">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-lg/ -->
                        <i class="bi bi-plus-lg me-1"></i>Ajouter le podcast
                    </button>
                HTML;
                break;
            }
        }

        $contentId = $idPlaylist !== null ? "<input type=\"hidden\" name=\"id\" value=\"{$idPlaylist}\">" : "";

        // Le enctype="multipart/form-data" est obligatoire pour l'envoie des fichiers
        return <<<HTML
            <form method="post" action="?action=add-track" enctype="multipart/form-data">
                <input type="hidden" name="type" value="{$_GET['type']}">
                {$contentId}
                {$content}
            </form>
            <p class="mt-3">
                <a class="btn btn-secondary d-inline-flex align-items-center" href="{$backUrl}">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                    <i class="bi bi-arrow-left me-2"></i>{$backLabel}
                </a>
            </p>
        HTML;
    }

    #[\Override]
    public function post() : string {
        $idPlaylist = isset($_POST['id']) ? (int) $_POST['id'] : null;

        $error = [];

        $type = $_POST['type'] ?? '';
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

        if (!empty($_POST['date'])) {
            $d = \DateTime::createFromFormat('Y-m-d', trim($_POST['date']));

            if (!$d) {
                $error[] = "La date de sortie est invalide (format attendu : AAAA-MM-JJ).";
            } elseif ($d > new \DateTime()) {
                // refuser une date dans le futur
                $error[] = "La date de sortie ne peut pas être dans le futur.";
            }
        }

        if (!isset($_FILES['userfile']) || $_FILES['userfile']['error'] === UPLOAD_ERR_NO_FILE) {
            $error[] = "Le fichier audio est obligatoire.";
        }

        $idParam = ($idPlaylist !== null) ? "&id={$idPlaylist}" : "";

        // Si des erreurs de formulaire sont présentes, on arrête avant d'écrire le fichier sur le disque
        if (!empty($error)) {
            $typeParam = !empty($type) ? "&type={$type}" : "";
            return HtmlHelper::formError(errors: $error, backUrl: "?action=add-track{$typeParam}{$idParam}");
        }

        // 1. Enregistrement du fichier audio MP3
        $uploadError = null;
        $audioFileName = self::saveAudioFile($_FILES['userfile'], $uploadError);
        if (!$audioFileName) {
            $error[] = $uploadError ?? "Erreur lors de l'envoi du fichier audio.";
            $typeParam = !empty($type) ? "&type={$type}" : "";
            return HtmlHelper::formError(errors: $error, backUrl: "?action=add-track{$typeParam}{$idParam}");
        }

        // Analyse des métadonnées ID3
        $getID3 = new \getID3();
        $fileInfo = $getID3->analyze(Config::getAudioDir() . $audioFileName);

        // Gestion et enregistrement de l'image (ID3 ou upload manuel)
        $coverFile = $_FILES['coverfile'] ?? null;
        $imageName = self::saveCoverImage($fileInfo, $coverFile);

        // Récupération des infos générales
        $title = $fileInfo['tags']['id3v2']['title'][0]
            ?? (!empty($_POST['title']) ? filter_var(trim($_POST['title']), FILTER_SANITIZE_SPECIAL_CHARS) : 'Titre inconnu');

        $genre = $fileInfo['tags']['id3v2']['genre'][0]
            ?? $fileInfo['tags']['id3v1']['genre'][0]
            ?? null;

        $duration = isset($fileInfo['playtime_seconds'])
            ? (int) round($fileInfo['playtime_seconds'])
            : 0;

        // Création de la piste selon le type
        $track = null;
        switch ($type) {
            case 'AlbumTrack' : {
                $artist = $fileInfo['tags']['id3v2']['artist'][0]
                    ?? (!empty($_POST['artist'])
                    ? filter_var(trim($_POST['artist']), FILTER_SANITIZE_SPECIAL_CHARS)
                    : null
                );

                $album = $fileInfo['tags']['id3v2']['album'][0]
                    ?? (!empty($_POST['album'])
                    ? filter_var(trim($_POST['album']), FILTER_SANITIZE_SPECIAL_CHARS)
                    : ''
                );

                $year = $fileInfo['tags']['id3v2']['year'][0]
                    ?? (!empty($_POST['year'])
                    ? (int) $_POST['year']
                    : null
                );

                $trackNumber = $fileInfo['tags']['id3v2']['track_number'][0]
                    ?? (!empty($_POST['trackNumber'])
                    ? (int) $_POST['trackNumber']
                    : 1
                );

                $track = new AlbumTrack($title, $audioFileName, $album, (int) $trackNumber);
                if (!empty($artist)) $track->artist = $artist;
                if ($year !== null) $track->year = (int) $year;
                break;
            }
            case 'PodcastTrack' : {
                $author = !empty($_POST['author'])
                    ? filter_var(trim($_POST['author']), FILTER_SANITIZE_SPECIAL_CHARS)
                    : ($fileInfo['tags']['id3v2']['artist'][0] ?? null);

                $date = !empty($_POST['date']) ? trim($_POST['date']) : null;

                $track = new PodcastTrack($title, $audioFileName);
                if (!empty($author)) $track->author = $author;
                if (!empty($date)) $track->date = $date;
                break;
            }
            default : $error[] = ("Type de piste inconnu : $type");
        }

        if (!empty($error)) {
            $typeParam = !empty($type) ? "&type={$type}" : "";
            return HtmlHelper::formError(errors: $error, backUrl: "?action=add-track{$typeParam}{$idParam}");
        }

        // Propriétés communes
        if ($duration > 0) $track->duration = $duration;
        if (!empty($genre)) $track->genre = $genre;
        if (!empty($imageName)) $track->image = $imageName;

        // Sauvegarde dans la playlist locale
        if (isset($_SESSION['playlist']) && (int) $_SESSION['playlist']->id === $idPlaylist) {
            $_SESSION['playlist']->addTrack($track);
        }

        // Sauvegarde dans le cloud
        $w = DeefyRepository::getInstance();
        $w->saveAudioTrack($track);
        $w->saveTrack2User((int) $this->user['id'], (int) $track->id);
        if ($idPlaylist !== null) {
            $w->addTrackToPlaylist($idPlaylist, (int) $track->id);
        }

        $playlistMsg = "à vos pistes";
        $totalTracksMsg = "";
        $backLink = <<<HTML
            <a class="btn btn-secondary d-inline-flex align-items-center ms-2" href="?action=tracks">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                <i class="bi bi-arrow-left me-2"></i>
                Retour à mes pistes
            </a>
        HTML;

        if ($idPlaylist !== null) {
            $r = DeefyRepository::getInstance();
            $playlist = $r->findPlaylistById($idPlaylist);
            if ($playlist !== null) {
                $playlistMsg = "à la playlist <strong>$playlist->name</strong>";
                $totalTracksMsg = "<p>Nombre total de pistes dans la playlist : <strong>$playlist->trackCount</strong></p>";
                $_SESSION['playlist'] = $playlist;
            }
            $backLink = <<<HTML
                <a class="btn btn-secondary d-inline-flex align-items-center ms-2" href="?action=display-playlist&id={$idPlaylist}">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/arrow-left/ -->
                    <i class="bi bi-arrow-left me-2"></i>
                    Retour à la playlist
                </a>
            HTML;
        }

        $renderer = RendererFactory::getRenderer($track, $idPlaylist);
        $renderTrack = $renderer->render(TypeRender::LONG);
        return <<<HTML
            <h1 class="h2 fw-bold text-success mb-3 d-flex align-items-center">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/check-circle-fill/ -->
                <i class="bi bi-check-circle-fill text-success me-2"></i>Piste ajoutée avec succès
            </h1>
            <p>La piste a été ajoutée avec succès {$playlistMsg} !</p>
            <div class="my-3">
                {$renderTrack}
            </div>
            {$totalTracksMsg}
            <p>
                <a class="btn btn-primary d-inline-flex align-items-center" href="?action=add-track{$idParam}">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-circle-fill/ -->
                    <i class="bi bi-plus-circle-fill me-2"></i>Ajouter une autre piste
                </a>
                {$backLink}
            </p>
        HTML;
    }

    #[\Override]
    protected function check() : ?string {
        $idPlaylist = isset($_POST['id']) ? (int) $_POST['id'] : (isset($_GET['id']) ? (int) $_GET['id'] : null);

        if ($idPlaylist !== null && !Authz::checkPlaylistOwner($idPlaylist)) {
            return HtmlHelper::forbidden(message: "Vous n'êtes pas autorisé à modifier cette playlist.");
        }

        return null;
    }

    /**
     * Enregistre le fichier MP3 téléversé sous un nom aléatoire unique dans le dossier audio.
     *
     * @param array $file Tableau représentant le fichier issu de $_FILES['userfile'].
     * @param string|null $uploadError Référence recevant le libellé de l'erreur en cas d'échec.
     * @return string|null Nom du fichier enregistré sur le serveur ou null en cas d'erreur.
     */
    private static function saveAudioFile(array $file, ?string &$uploadError = null) : ?string {
        if (!isset($file['error'])) {
            $uploadError = "Aucun fichier audio n'a été reçu.";
            return null;
        }

        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $maxSize = ini_get('upload_max_filesize');
                $uploadError = "Le fichier audio est trop volumineux (limite serveur : {$maxSize}).";
                return null;
            case UPLOAD_ERR_PARTIAL:
                $uploadError = "Le fichier n'a été que partiellement téléversé.";
                return null;
            case UPLOAD_ERR_NO_FILE:
                $uploadError = "Veuillez sélectionner un fichier audio MP3.";
                return null;
            default:
                $uploadError = "Erreur lors du téléversement du fichier (code {$file['error']}).";
                return null;
        }

        $audioDir = Config::getAudioDir();

        if (!is_dir($audioDir)) {
            if (!mkdir($audioDir, 0777, true)) {
                $uploadError = "Impossible de créer le dossier de stockage audio.";
                return null;
            }
        }

        $newName = uniqid('track_', true) . bin2hex(random_bytes(4)) . '.mp3';
        $destination = $audioDir . $newName;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $uploadError = "Impossible de déplacer le fichier téléversé vers le dossier audio.";
            return null;
        }

        return $newName;
    }

    /**
     * Enregistre l'image de couverture (priorité aux métadonnées ID3, sinon fichier téléversé) dans le dossier image.
     *
     * @param array $fileInfo Tableau d'analyse des métadonnées getID3.
     * @param array|null $coverFile Fichier image optionnel envoyé via le formulaire ($_FILES['coverfile']).
     * @return string|null Nom du fichier image généré ou null si aucune couverture n'est fournie.
     */
    private static function saveCoverImage(array $fileInfo, ?array $coverFile) : ?string {
        $coverDir = Config::getCoverDir();

        if (!is_dir($coverDir)) {
            mkdir($coverDir, 0777, true);
        }

        // 1. Priorité à la pochette incluse dans les métadonnées ID3 du MP3
        if (!empty($fileInfo['comments']['picture'][0]['data'])) {
            $picture = $fileInfo['comments']['picture'][0];
            $typeImg = $picture['image_mime'] ?? 'image/jpeg';
            $extension = ($typeImg === 'image/png') ? 'png' : 'jpg';

            $imageName = uniqid('cover_', true) . bin2hex(random_bytes(4)) . '.' . $extension;
            if (file_put_contents($coverDir . $imageName, $picture['data']) !== false) {
                return $imageName;
            }
        }

        // 2. Fichier envoyé manuellement dans le formulaire
        if ($coverFile && $coverFile['error'] === UPLOAD_ERR_OK) {
            $extension = strtolower(pathinfo($coverFile['name'], PATHINFO_EXTENSION));
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                $imageName = uniqid('cover_', true) . bin2hex(random_bytes(4)) . '.' . $extension;
                if (move_uploaded_file($coverFile['tmp_name'], $coverDir . $imageName)) {
                    return $imageName;
                }
            }
        }

        return null;
    }
}