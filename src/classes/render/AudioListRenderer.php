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
        <ul class="list-group mb-3">
        HTML;

        if (count($this->audioList->tracks) === 0) {
            $res .= '   <li class="list-group-item text-muted fst-italic">Cette liste est vide.</li>';
        } else {
            foreach ($this->audioList as $track) {
                $renderPiste = RendererFactory::getRenderer($track);
                if ($renderPiste !== null) $res .= $renderPiste->render($selector);
            }
        }

        $res .= <<<HTML
            </ul>
            <p class="text-muted"><strong>{$this->audioList->trackCount}</strong> piste(s) | Durée totale : <strong>{$this->audioList->totalDuration}s</strong></p>
        HTML;

        return $res;
    }
}