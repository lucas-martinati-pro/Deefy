<?php

namespace iutnc\deefy\action;

class Signout extends Action {
    public function get() : string {
        // Si l'utilisateur n'est même pas connecté
        if (!isset($_SESSION['user'])) {
            return <<<HTML
                <h1>Déconnexion</h1>
                <p>Vous n'êtes pas connecté.</p>
                <a href="main.php">Retour à l'accueil</a>
            HTML;
        }

        return <<<HTML
            <h1>Déconnexion</h1>
            <p>Êtes-vous sûr de vouloir vous déconnecter ?</p>
            <form method="post" action="?action=signout">
                <button type="submit">Confirmer la déconnexion</button>
                <a href="main.php">Annuler</a>
            </form>
        HTML;
    }

    public function post() : string {
        unset($_SESSION['user']);
        unset($_SESSION['playlist']);
        return <<<HTML
            <h1>Déconnexion réussie</h1>
            <p>Vous avez été déconnecté avec succès.</p>
            <p>
                <a href="main.php?action=signin">Se reconnecter</a>
                 | 
                <a href="main.php">Retour à l'accueil</a>
            </p>
        HTML;
    }
}