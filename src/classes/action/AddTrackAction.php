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
                <a href="?action=playlists">Retour à mes playlists</a>
            HTML;
        }

        if (!Authz::checkPlaylistOwner($_SESSION['playlist']->id)) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>Vous n'êtes pas autorisé à modifier cette playlist.</p>
                <a href="?action=playlists">Retour à mes playlists</a>
            HTML;
        }

        if (!isset($_GET['type'])) {
            return <<<HTML
                    <ul>
                        <li><a href="?action=add-track&type=PodcastTrack">Ajouter un Podcast à ma playlist</a>
                        <li><a href="?action=add-track&type=AlbumTrack">Ajouter un Album à ma playlist</a>
                    </ul>
                HTML;
        }

        $action = "";
        $content = "";
        switch ($_GET['type']) {
            case 'AlbumTrack' : {
                $action = "?action=add-track&type=AlbumTrack";
                $content = <<<HTML
                    <li><input type="text" name="title" placeholder="Titre" required></li>
                    <li><input type="text" name="artist" placeholder="Artiste" required></li>
                    <li><input type="text" name="album" placeholder="Album" required></li>
                    <li><input type="number" name="year" placeholder="Année" required></li>
                    <li><input type="number" name="trackNumber" placeholder="Numéro de piste" required></li>
                    <li>Fichier audio : <input type="file" name="userfile" accept="audio/mpeg" required></li>
                    <button type="submit">Ajouter une piste</button>
                HTML;
                break;
            }
            case 'PodcastTrack' : {
                $action = "?action=add-track&type=PodcastTrack";
                $content = <<<HTML
                    <li><input type="text" name="title" placeholder="nom de la piste" required></li>
                    <li><input type="text" name="author" placeholder="nom de l'auteur" required></li>
                    <li>date de sortie du morceaux : <input type="date" name="date" required></li>
                    <li>Fichier audio : <input type="file" name="userfile" accept="audio/mpeg" required></li>
                    <button type="submit">Ajouter une piste</button>
                HTML;
                break;
            }
        }

        return <<<HTML
            <form method="post" action="{$action}" enctype="multipart/form-data">
                <ul>
                    {$content}
                </ul>
            </form>
            <a href="?action=playlists">Retour à mes playlists</a>
        HTML;
    }

    #[\Override]
    public function post() : string {
        if (!isset($_SESSION['playlist'])) {
            return <<<HTML
                <h1>Erreur</h1>
                <p>Aucune playlist n'a été trouvée en session. Veuillez d'abord initialiser la playlist.</p>
                <a href="?action=add-playlist">Ajouter une playlist</a>
            HTML;
        }

        if (!Authz::checkPlaylistOwner($_SESSION['playlist']->id)) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>Vous n'êtes pas autorisé à modifier cette playlist.</p>
                <a href="?action=playlists">Retour à mes playlists</a>
            HTML;
        }

        $error = [];

        $type = $_GET['type'] ?? '';
        if ($type === 'AlbumTrack') {
            if (empty($_POST['title']) || empty($_POST['artist']) || empty($_POST['album']) || empty($_POST['year']) || empty($_POST['trackNumber'])) {
                $error[] = 'Tous les champs de l\'album sont obligatoires.';
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
                <ul>{$errorList}</ul>
                <a href="?action=add-track">Retour au formulaire</a>
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
                    <a href="?action=add-track">Retour à l'ajout d'une piste</a>
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
                <a href="?action=add-track">Ajouter une autre piste</a>
                 | 
                <a href="?action=playlists">Retour à mes playlists</a>
            </p>
        HTML;
    }
}