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
        $details = [];

        $date = $this->track->get('date');
        if (!empty($date)) {
            $timestamp = strtotime($date);
            $formattedDate = ($timestamp !== false) ? date('d/m/Y', $timestamp) : $date;
            $details[] = "Date : {$formattedDate}";
        }

        $duration = (int) $this->track->get('duration');
        if ($duration > 0) {
            $details[] = "Durée : {$duration}s";
        }

        $genre = trim($this->track->get('genre') ?? '');
        if (!empty($genre)) {
            $details[] = "Genre : {$genre}";
        }

        $infos = implode(' | ', $details);

        return <<<HTML
            <li class="list-group-item d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                <div>
                    <strong>{$this->track->get("title")}</strong> <span class="text-muted">- par {$this->track->get("author")}</span>
                    <div class="small text-muted">{$infos}</div>
                </div>
                {$this->renderAudioPlayer()}
            </li>
        HTML;
    }
}