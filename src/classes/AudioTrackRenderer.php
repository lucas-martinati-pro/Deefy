<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;
require_once 'AudioTrack.php';
require_once 'Renderer.php';

abstract class AudioTrackRenderer implements Renderer {

    protected AudioTrack $track;

    public function __construct(AudioTrack $track) {
        $this->track = $track;
    }

    #[\Override]
    public function render(int $selector) : string {
        return ($selector === Renderer::LONG) ? $this->renderLong() : $this->renderCompact();
    }

    abstract protected function renderCompact() : string;

    abstract protected function renderLong() : string;
}