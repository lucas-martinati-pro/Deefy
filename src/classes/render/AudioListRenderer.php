<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\lists\AudioList;
use iutnc\deefy\enums\TypeRender;
use Override;

/**
 * Moteur de rendu HTML pour les listes audio (AudioList, Playlist, Album).
 */
class AudioListRenderer implements Renderer {

    /**
     * Liste audio à afficher.
     */
    protected AudioList $audioList;

    /**
     * Initialise le renderer avec la liste audio à afficher.
     *
     * @param AudioList $audioList La liste audio à restituer.
     */
    public function __construct(AudioList $audioList) {
        $this->audioList = $audioList;
    }

    #[Override]
    public function render(TypeRender $selector = TypeRender::COMPACT) : string {
        $res = <<<HTML
        <h2 class="mb-3 d-flex align-items-center">
            <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-list/ -->
            <i class="bi bi-music-note-list text-primary me-2"></i>{$this->audioList->getName()}
        </h2>
        HTML;

        if (count($this->audioList->getTracks()) === 0) {
            $res .= HtmlHelper::alert(type: 'secondary', content: 'Cette liste est vide.');
        } else {
            $divClass = ($selector === TypeRender::LONG)
                ? 'd-flex flex-column gap-3 mb-4'
                : 'row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3 mb-4';

            $res .= "<div class=\"{$divClass}\">";
            foreach ($this->audioList as $track) {
                $renderPiste = RendererFactory::getRenderer($track, $this->audioList->getId() ?? null);
                if ($renderPiste !== null) $res .= $renderPiste->render($selector);
            }
            $res .= '</div>';
        }

        $res .= <<<HTML
            <p class="text-muted d-flex align-items-center flex-wrap gap-2">
                <span>
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-beamed/ -->
                    <i class="bi bi-music-note-beamed me-1"></i><strong>{$this->audioList->getTrackCount()}</strong> piste(s)
                </span>
                <span>|</span>
                <span>
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/clock/ -->
                    <i class="bi bi-clock me-1"></i>Durée totale : <strong>{$this->audioList->getTotalDuration()}s</strong>
                </span>
            </p>
        HTML;

        return $res;
    }
}