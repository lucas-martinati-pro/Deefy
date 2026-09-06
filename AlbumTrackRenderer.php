<?php

require_once 'AlbumTrack.php';
require_once 'AudioTrackRenderer.php';

/**
 * @property AlbumTrack $track
 */
class AlbumTrackRenderer extends AudioTrackRenderer {

    #[\Override]
    protected function renderCompact() : string {
        return "<li><strong>{$this->track->title}</strong> - {$this->track->artist} (<em>{$this->track->album}</em>)<br>" .
               "<audio controls src=\"{$this->track->path}\"></audio></li>";
    }

    #[\Override]
    protected function renderLong() : string {
        return "<li><strong>{$this->track->trackNumber}. {$this->track->title}</strong> - {$this->track->artist} (<em>{$this->track->album}</em>, {$this->track->year}) - {$this->track->duration}s : <em>{$this->track->genre}</em><br>" .
               "<audio controls src=\"{$this->track->path}\"></audio></li>";
    }
}