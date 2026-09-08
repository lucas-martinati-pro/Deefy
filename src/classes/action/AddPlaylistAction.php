<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\auth\AuthnProvider;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\render\RendererFactory;
use iutnc\deefy\repository\DeefyRepository;
use iutnc\deefy\exception\AuthnException;

class AddPlaylistAction extends Action {
    #[\Override]
    public function get() : string {
        try {
            AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return <<<HTML
                <h1>Accès refusé</h1>
                <p>{$e->getMessage()}</p>
                <a href="?action=signin">Se connecter</a>
            HTML;
        }
        return <<<HTML
            <form method="post" action="?action=add-playlist">
                <input type="text" name="title" placeholder="nom de la playlist">
                <button type="submit">Créer la playlist</button>
            </form>
        HTML;
    }

    #[\Override]
    public function post() : string {
        if (!isset($_POST['title']) || trim($_POST['title']) === '') {
            return <<<HTML
                <h1>Erreur dans le formulaire</h1>
                <p>Le nom de la playlist est obligatoire.</p>
                <a href="?action=add-playlist">Retour au formulaire</a>
            HTML;
        }

        $user = [];
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

        $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
        $playlist = $r->saveEmptyPlaylist(new Playlist($title, []));

        $r->savePlaylist2User((int) $user['id'], (int) $playlist->id);

        // La playlist créée devient la playlist courante en session
        $_SESSION['playlist'] = $playlist;

        $renderer = RendererFactory::getRenderer($playlist);
        $listRender = $renderer ? $renderer->render(Renderer::COMPACT) : '';

        return <<<HTML
            {$listRender}
            <p>
                <a href="?action=add-track">Ajouter une piste</a>
                 | 
                <a href="?action=playlists">Retour à mes playlists</a>
            </p>
        HTML;
    }
}