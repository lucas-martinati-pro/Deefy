<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;
require_once 'AlbumTrack.php';
require_once 'AudioTrackRenderer.php';
use iutnc\deefy\audio\tracks\AudioTrack;
require_once 'AudioTrack.php';

/**
 * @property AlbumTrack $track
 */
class AlbumTrackRenderer extends AudioTrackRenderer {

    #[\Override]
    protected function renderCompact() : string {
        return "<li><strong>{$this->track->get("title")}</strong> - {$this->track->get("artist")} (<em>{$this->track->get("album")}</em>)<br>" .
               "<audio controls src=\"{$this->track->get("path")}\"></audio></li>";
    }

    #[\Override]
    protected function renderLong() : string {
        return "<li><strong>{$this->track->get("trackNumber")}. {$this->track->get("title")}</strong> - {$this->track->get("artist")} (<em>{$this->track->get("album")}</em>, {$this->track->get("year")}) - {$this->track->get("duration")}s : <em>" . AudioTrack::getGenreAsString($this->track->get("genre")) . "</em><br>" .
               "<audio controls src=\"{$this->track->get("path")}\"></audio></li>";
    }
}