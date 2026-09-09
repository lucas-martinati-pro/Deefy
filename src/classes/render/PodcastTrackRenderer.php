<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\PodcastTrack;

/**
 * @property PodcastTrack $track
 */
class PodcastTrackRenderer extends AudioTrackRenderer {

    #[\Override]
    protected function getSubtitle() : string {
        $author = $this->track->get("author") ?? 'Auteur inconnu';
        return "par <span class=\"fw-semibold text-dark\">{$author}</span>";
    }

    #[\Override]
    protected function getBadge() : string {
        return <<<HTML
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/mic-fill/ -->
                <i class="bi bi-mic-fill me-1"></i>Podcast
            </span>
        HTML;
    }

    #[\Override]
    protected function getDetails() : array {
        $details = [];

        $date = $this->track->get('date');
        if (!empty($date)) {
            $timestamp = strtotime($date);
            $formattedDate = ($timestamp !== false) ? date('d/m/Y', $timestamp) : $date;
            $details[] = "Date : {$formattedDate}";
        }

        return array_merge($details, parent::getDetails());
    }
}