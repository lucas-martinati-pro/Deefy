<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

/**
 * @property AlbumTrack $track
 */
class AlbumTrackRenderer extends AudioTrackRenderer {

    #[\Override]
    protected function getSubtitle() : string {
        $artist = $this->track->get("artist");
        $album = $this->track->get("album");
        $artistStr = !empty($artist) ? $artist : 'Artiste inconnu';
        return "<span class=\"fw-semibold text-dark\">{$artistStr}</span> <span class=\"text-muted\">({$album})</span>";
    }

    #[\Override]
    protected function getBadge() : string {
        $trackNumber = (int) $this->track->get('trackNumber');
        $trackBadge = ($trackNumber > 0) ? "<span class=\"badge text-bg-secondary me-1\">#{$trackNumber}</span>" : '';
        return <<<HTML
            <div class="text-nowrap">{$trackBadge}<span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/vinyl-fill/ -->
                <i class="bi bi-vinyl-fill me-1"></i>Album
            </span></div>
        HTML;
    }

    #[\Override]
    protected function getDetails() : array {
        $details = [];

        $year = $this->track->get('year');
        if (!empty($year)) {
            $details[] = "Année : {$year}";
        }

        return array_merge($details, parent::getDetails());
    }
}