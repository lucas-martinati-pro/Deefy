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
        <li>
            <strong>{$this->track->get("title")}</strong> - par {$this->track->get("author")}<br>
            <audio controls src="../audio/{$this->track->get("filename")}"></audio>
        </li>
        HTML;
    }

    #[\Override]
    protected function renderLong() : string {
        return <<<HTML
            <li>
                <strong>{$this->track->get("title")}</strong> - par {$this->track->get("author")}<br>
                <small>Date : {$this->track->get("date")} | Genre : {$this->track->get("genre")} | Durée : {$this->track->get("duration")}s</small><br>
                <audio controls src="../audio/{$this->track->get("filename")}"></audio>
            </li>
        HTML;
    }
}