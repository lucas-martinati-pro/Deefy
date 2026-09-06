<?php

namespace iutnc\deefy\action;

use iutnc\deefy\action\Action;

class DefaultAction extends Action {
    #[\Override]
    public function get() : string {
        $html = '<h1>Deefy</h1>';
        $html .= '<p>Bienvenue sur Deefy, votre plateforme de musique.</p>';
        return $html;
    }

    #[\Override]
    public function post() : string {
        return '';
    }
}