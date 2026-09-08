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

    /**
     * Retourne le sous-titre de la piste (ex: auteur pour un podcast, artiste/album pour un album).
     */
    abstract protected function getSubtitle() : string;

    /**
     * Retourne le badge HTML identifiant le type de piste (Podcast, Album/numéro de piste, etc.).
     */
    abstract protected function getBadge() : string;

    /**
     * Retourne la liste des métadonnées sous forme de tableau de chaînes.
     * Les sous-classes peuvent l'enrichir via array_merge(..., parent::getDetails()).
     *
     * @return string[]
     */
    protected function getDetails() : array {
        $details = [];

        $duration = (int) $this->track->get('duration');
        if ($duration > 0) {
            $details[] = "Durée : {$duration}s";
        }

        $genre = trim($this->track->get('genre') ?? '');
        if (!empty($genre)) {
            $details[] = "Genre : {$genre}";
        }

        return $details;
    }

    /**
     * Rendu compact : card verticale
     */
    protected function renderCompact() : string {
        $details = $this->getDetails();
        $infos = implode(' | ', $details);
        $infosHtml = !empty($infos) ? "<div class=\"small text-body-secondary mb-2\">{$infos}</div>" : '';

        $hasImage = !empty($this->track->get('image'));
        $imageHtml = '';
        if ($hasImage) {
            $imageHtml = <<<HTML
                <img src="../image/{$this->track->get('image')}" class="card-img-top object-fit-cover" style="height: 180px;" alt="{$this->track->get('title')}">
            HTML;
        } else {
            $imageHtml = <<<HTML
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center text-secondary border-bottom" style="height: 180px;">
                    <!-- Icône Bootstrap -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor" class="bi bi-music-note-beamed" viewBox="0 0 16 16">
                        <path d="M6 13c0 1.105-1.12 2-2.5 2S1 14.105 1 13s1.12-2 2.5-2 2.5.896 2.5 2m9-2c0 1.105-1.12 2-2.5 2s-2.5-.895-2.5-2 1.12-2 2.5-2 2.5.895 2.5 2"/>
                        <path fill-rule="evenodd" d="M14 11V2h1v9zM6 3v10H5V3z"/>
                        <path d="M5 2.905a1 1 0 0 1 .9-.995l8-.8a1 1 0 0 1 1.1.995V3L5 4z"/>
                    </svg>
                </div>
            HTML;
        }

        $badge = $this->getBadge();
        $subtitle = $this->getSubtitle();

        return <<<HTML
            <div>
                <div class="card shadow">
                    {$imageHtml}
                    <div class="card-body d-flex flex-column justify-content-between p-3">
                        <div>
                            <div class="d-flex justify-content-between align-items-start gap-1 mb-1">
                                <h5 class="card-title fw-bold fs-6 mb-0 text-truncate" title="{$this->track->get('title')}">{$this->track->get("title")}</h5>
                                {$badge}
                            </div>
                            <h6 class="card-subtitle text-muted small mb-2 text-truncate">
                                {$subtitle}
                            </h6>
                            {$infosHtml}
                        </div>
                        <div class="mt-auto pt-2">
                            {$this->renderAudioPlayer(Renderer::COMPACT)}
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }

    /**
     * Rendu long : carte horizontale
     */
    protected function renderLong() : string {
        $details = $this->getDetails();
        $infos = implode(' | ', $details);
        $infosHtml = !empty($infos) ? "<p class=\"card-text text-body-secondary small mb-3\">{$infos}</p>" : '';

        $hasImage = !empty($this->track->get('image'));
        $imageHtml = '';
        $colContent = 'col-12';

        if ($hasImage) {
            $imageHtml = <<<HTML
                <div class="col-md-3 col-lg-2">
                    <img src="../image/{$this->track->get('image')}" class="img-fluid rounded-start w-100 h-100 object-fit-cover" style="min-height: 140px; max-height: 200px;" alt="{$this->track->get('title')}">
                </div>
            HTML;
            $colContent = 'col-md-9 col-lg-10';
        }

        $badge = $this->getBadge();
        $subtitle = $this->getSubtitle();

        return <<<HTML
            <li class="list-group-item p-0 border-0 mb-3 bg-transparent">
                <div class="card shadow-sm border overflow-hidden">
                    <div class="row g-0">
                        {$imageHtml}
                        <div class="{$colContent}">
                            <div class="card-body d-flex flex-column justify-content-between h-100 p-3">
                                <div>
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                        <h5 class="card-title fw-bold mb-0">{$this->track->get("title")}</h5>
                                        {$badge}
                                    </div>
                                    <h6 class="card-subtitle text-muted mb-2">
                                        {$subtitle}
                                    </h6>
                                    {$infosHtml}
                                </div>
                                <div class="mt-auto">
                                    {$this->renderAudioPlayer(Renderer::COMPACT)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        HTML;
    }

    protected function renderAudioPlayer(int $selector) : string {
        if ($selector === Renderer::COMPACT) {
            return <<<HTML
                <media-theme-tailwind-audio
                    style="
                    --media-primary-color: #212529;
                    --media-secondary-color: #f8f9fa;
                    --media-accent-color: #0d6efd;
                    width: 100%;
                    max-width: 1500px;
                    border-radius: 5px;
                    overflow: hidden;">
                    <audio
                        slot="media"
                        src="../audio/{$this->track->get("filename")}"
                        playsinline
                        crossorigin="anonymous"
                    ></audio>
                </media-theme-tailwind-audio>
            HTML;
        } else return <<<HTML
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