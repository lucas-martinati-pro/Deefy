<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;
require_once 'AudioList.php';
require_once 'Renderer.php';
use iutnc\deefy\render\RendererFactory;
require_once 'RendererFactory.php';

class AudioListRenderer implements Renderer {

    protected AudioList $audioList;

    public function __construct(AudioList $audioList) {
        $this->audioList = $audioList;
    }

    #[\Override]
    public function render(int $selector = 0) : string {
        $res = "<h2>{$this->audioList->name}</h2>\n";
        $res .= "<ul>\n";

        if (count($this->audioList->tracks) === 0) {
            $res .= "   <li><em>Liste vide</em></li>\n";
        } else {
            foreach ($this->audioList as $track) {
                $renderPiste = RendererFactory::getRenderer($track);
                if ($renderPiste !== null) $res .= $renderPiste->render(Renderer::COMPACT);
            }
        }

        $res .= "</ul>\n";
        $res .= "<p><strong>{$this->audioList->trackCount}</strong> piste(s) | Durée totale : <strong>{$this->audioList->totalDuration}s</strong></p>\n";

        return $res;
    }
}