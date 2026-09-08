<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\auth\Authz;
use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\repository\DeefyRepository;

class DisplayPlaylistAction extends Action {
    #[\Override]
    public function get() : string {
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>{$e->getMessage()}</p>
                <a href="?action=signin">Se connecter</a>
            HTML;
        }

        $r = DeefyRepository::getInstance();

        // CAS 1 : Aucun ID fourni -> Afficher la liste "Mes playlists"
        if (!isset($_GET['id'])) {
            $playlists = $r->findPlaylistsByUserId((int) $user['id']);

            if (empty($playlists)) {
                return <<<HTML
                    <h1>Mes playlists</h1>
                    <p>Vous ne possédez aucune playlist pour le moment.</p>
                    <a href="?action=add-playlist">Créer une playlist</a>
                HTML;
            }

            $listHtml = "<ul>";
            foreach ($playlists as $pl) {
                $listHtml .= <<<HTML
                    <li>
                        <a href="?action=display-playlist&id={$pl->id}">{$pl->name}</a>
                    </li>
                HTML;
            }
            $listHtml .= "</ul>";

            return <<<HTML
                <h1>Mes playlists</h1>
                {$listHtml}
                <p><a href="?action=add-playlist">Créer une nouvelle playlist</a></p>
            HTML;
        }

        // CAS 2 : Un ID est fourni -> Afficher cette playlist
        $idPlaylist = (int) $_GET['id'];
        $playlist = $r->findPlaylistById($idPlaylist);

        if (!$playlist) {
            return <<<HTML
                <h1>Playlist introuvable</h1>
                <p>La playlist demandée n'existe pas.</p>
                <a href="?action=display-playlist">Retour à mes playlists</a>
            HTML;
        }

        // Vérification du propriétaire (ou admin role 100)
        if (!Authz::checkPlaylistOwner($playlist->id)) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>Vous n'êtes pas autorisé à consulter cette playlist.</p>
                <a href="?action=display-playlist">Retour à mes playlists</a>
            HTML;
        }

        // La playlist affichée devient la playlist courante en session
        $_SESSION['playlist'] = $playlist;

        $renderer = (new AudioListRenderer($playlist))->render(1);

        return <<<HTML
            {$renderer}
            <p>
                <a href="?action=add-track">Ajouter une piste</a>
                 | 
                 <a href="?action=add-album-track">Ajouter un album</a>
                 | 
                <a href="?action=display-playlist">Retour à mes playlists</a>
            </p>
        HTML;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}