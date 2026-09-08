<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;

/**
 * @property AlbumTrack $track
 */
class AlbumTrackRenderer extends AudioTrackRenderer {

    #[\Override]
    protected function renderCompact() : string {
        return <<<HTML
            <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <strong>{$this->track->get("title")}</strong> - {$this->track->get("artist")} <span class="text-muted">({$this->track->get("album")})</span>
                </div>
                {$this->renderAudioPlayer()}
            </li>
        HTML;
    }

    #[\Override]
    protected function renderLong() : string {
        return <<<HTML
            <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <span class="badge text-bg-secondary me-1">#{$this->track->get("trackNumber")}</span>
                    <strong>{$this->track->get("title")}</strong> - {$this->track->get("artist")} <span class="text-muted">({$this->track->get("album")}, {$this->track->get("year")})</span>
                    <div class="small text-muted">Durée : {$this->track->get("duration")}s | Genre : {$this->track->get("genre")}</div>
                </div>
                {$player = $this->renderAudioPlayer()}
            </li>
        HTML;
    }
}