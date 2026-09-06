<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;
require_once 'PodcastTrack.php';
require_once 'AudioTrackRenderer.php';
use iutnc\deefy\audio\tracks\AudioTrack;
require_once 'AudioTrack.php';

/**
 * @property PodcastTrack $track
 */
class PodcastTrackRenderer extends AudioTrackRenderer {

    #[\Override]
    protected function renderCompact() : string {
        return "<li><strong>{$this->track->get("title")}</strong> - par {$this->track->get("author")}<br>" .
               "<audio controls src=\"{$this->track->get("path")}\"></audio></li>";
    }

    #[\Override]
    protected function renderLong() : string {
        return "<li><strong>{$this->track->get("title")}</strong> - par {$this->track->get("author")} ({$this->track->get("date")}) - {$this->track->get("duration")}s : <em>" . AudioTrack::getGenreAsString($this->track->get("genre")) . "</em><br>" .
               "<audio controls src=\"{$this->track->get("path")}\"></audio></li>";
    }
}