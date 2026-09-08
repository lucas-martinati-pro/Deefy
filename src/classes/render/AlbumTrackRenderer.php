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
            <li>
                <strong>{$this->track->get("title")}</strong> - {$this->track->get("artist")} (<em>{$this->track->get("album")}</em>)<br>
                <audio controls src="../audio/{$this->track->get("filename")}"></audio>
            </li>
        HTML;
    }

    #[\Override]
    protected function renderLong() : string {
        return <<<HTML
            <li>
                <strong>{$this->track->get("trackNumber")} {$this->track->get("title")}</strong> - {$this->track->get("artist")} (<em>{$this->track->get("album")}</em>, {$this->track->get("year")}) - {$this->track->get("duration")}s : <em>{$this->track->get("genre")}</em><br>
                <audio controls src="../audio/{$this->track->get("filename")}"></audio>
            </li>
        HTML;
    }
}