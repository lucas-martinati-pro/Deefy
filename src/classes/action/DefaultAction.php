<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;

class DefaultAction extends Action {
    #[\Override]
    public function get() : string {
        return <<< HTML
            <h1>Bienvenue sur Deefy !</h1>
            <p>Bienvenue sur Deefy, votre plateforme de musique.</p>
        HTML;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}