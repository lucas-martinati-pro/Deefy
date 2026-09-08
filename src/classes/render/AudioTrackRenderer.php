<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;

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

    protected function renderAudioPlayer() : string {
        return <<<HTML
            <media-theme-tailwind-audio
                style="
                --media-primary-color: #212529;
                --media-secondary-color: #f8f9fa;
                --media-accent-color: #0d6efd;
                width: 100%;
                max-width: 700px;
                border-radius: 12px;
                border: 2px solid #dee2e6;
                overflow: hidden;">
                <audio
                    slot="media"
                    src="../audio/{$this->track->get("filename")}"
                    playsinline
                    crossorigin="anonymous"
                ></audio>
            </media-theme-tailwind-audio>
        HTML;
    }
}