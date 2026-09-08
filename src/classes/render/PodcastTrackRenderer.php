<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

/**
 * @property PodcastTrack $track
 */
class PodcastTrackRenderer extends AudioTrackRenderer {

    #[\Override]
    protected function renderCompact() : string {
        return <<<HTML
            <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <strong>{$this->track->get("title")}</strong> <span class="text-muted">- par {$this->track->get("author")}</span>
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
                    <strong>{$this->track->get("title")}</strong> <span class="text-muted">- par {$this->track->get("author")}</span>
                    <div class="small text-muted">Date : {$this->track->get("date")} | Genre : {$this->track->get("genre")} | Durée : {$this->track->get("duration")}s</div>
                </div>
                {$this->renderAudioPlayer()}
            </li>
        HTML;
    }
}