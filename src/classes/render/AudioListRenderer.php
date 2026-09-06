<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\render\AlbumTrackRenderer;
use iutnc\deefy\render\PodcastTrackRenderer;

class AudioListRenderer implements Renderer {

    protected AudioList $audioList;

    public function __construct(AudioList $audioList) {
        $this->audioList = $audioList;
    }

    #[\Override]
    public function render(int $selector = 0) : string {
        $res = <<<HTML
        <h2>{$this->audioList->name}</h2>
        <ul>
        HTML;

        if (count($this->audioList->tracks) === 0) {
            $res .= "   <li><em>Liste vide</em></li>";
        } else {
            foreach ($this->audioList->tracks as $track) {
                $renderPiste = null;
                if ($track instanceof AlbumTrack) $renderPiste = new AlbumTrackRenderer($track);
                else if ($track instanceof PodcastTrack) $renderPiste = new PodcastTrackRenderer($track);
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