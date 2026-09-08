<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;

class AudioListRenderer implements Renderer {

    protected AudioList $audioList;

    public function __construct(AudioList $audioList) {
        $this->audioList = $audioList;
    }

    #[\Override]
    public function render(int $selector = 0) : string {
        $res = <<<HTML
        <h2>{$this->audioList->name}</h2>
        <ul class="dropdown-menu show position-static">
        HTML;

        if (count($this->audioList->tracks) === 0) {
            $res .= '   <li><em class="dropdown-item">Liste vide</em></li>';
        } else {
            foreach ($this->audioList as $track) {
                $renderPiste = RendererFactory::getRenderer($track);
                if ($renderPiste !== null) $res .= $renderPiste->render($selector);
            }
        }

        $res .= <<<HTML
            </ul>
            <p><strong>{$this->audioList->trackCount}</strong> piste(s) | Durée totale : <strong>{$this->audioList->totalDuration}s</strong></p>
        HTML;

        return $res;
    }
}