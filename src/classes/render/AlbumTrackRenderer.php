<?php

namespace iutnc\deefy\render;
use Override;

/**
 * Moteur de rendu spécifique pour les morceaux d'album (AlbumTrack).
 */
class AlbumTrackRenderer extends AudioTrackRenderer {

    #[Override]
    protected function getSubtitle() : string {
        $artist = $this->track->getArtist();
        $album = $this->track->getAlbum();
        $artistStr = !empty($artist) ? $artist : 'Artiste inconnu';
        return "<span class=\"fw-semibold text-body\">{$artistStr}</span> <span class=\"text-muted\">({$album})</span>";
    }

    #[Override]
    protected function getBadge() : string {
        $trackNumber = $this->track->getTrackNumber();
        $trackBadge = ($trackNumber > 0) ? "<span class=\"badge text-bg-secondary me-1\">#{$trackNumber}</span>" : '';
        return <<<HTML
            <div class="text-nowrap">{$trackBadge}<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/vinyl-fill/ -->
                <i class="bi bi-vinyl-fill me-1"></i>Album
            </span></div>
        HTML;
    }

    #[Override]
    protected function getDetails() : array {
        $details = [];

        $year = $this->track->getYear();
        if (!empty($year)) {
            $details[] = "Année : {$year}";
        }

        return array_merge($details, parent::getDetails());
    }
}