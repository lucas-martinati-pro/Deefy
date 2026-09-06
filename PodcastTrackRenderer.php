<?php

require_once 'PodcastTrack.php';
require_once 'AudioTrackRenderer.php';

/**
 * @property PodcastTrack $track
 */
class PodcastTrackRenderer extends AudioTrackRenderer {

    #[\Override]
    protected function renderCompact() : string {
        return "<li><strong>{$this->track->title}</strong> - par {$this->track->author}<br>" .
               "<audio controls src=\"{$this->track->path}\"></audio></li>";
    }

    #[\Override]
    protected function renderLong() : string {
        return "<li><strong>{$this->track->title}</strong> - par {$this->track->author} ({$this->track->date}) - {$this->track->duration}s : <em>{$this->track->genre}</em><br>" .
               "<audio controls src=\"{$this->track->path}\"></audio></li>";
    }
}