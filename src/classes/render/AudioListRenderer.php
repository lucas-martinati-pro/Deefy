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
        <h2 class="mb-3">{$this->audioList->name}</h2>
        HTML;

        if (count($this->audioList->tracks) === 0) {
            $res .= <<<HTML
                <div class="alert alert-secondary">Cette liste est vide.</div>
            HTML;
        } else {
            $divClass = ($selector === Renderer::LONG)
                ? 'd-flex flex-column gap-3 mb-4'
                : 'row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3 mb-4';

            $res .= "<div class=\"{$divClass}\">";
            foreach ($this->audioList as $track) {
                $renderPiste = RendererFactory::getRenderer($track);
                if ($renderPiste !== null) $res .= $renderPiste->render($selector);
            }
            $res .= '</div>';
        }

        $res .= <<<HTML
            <p class="text-muted"><strong>{$this->audioList->trackCount}</strong> piste(s) | Durée totale : <strong>{$this->audioList->totalDuration}s</strong></p>
        HTML;

        return $res;
    }
}