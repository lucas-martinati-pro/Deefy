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
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-beamed/ -->
                    <i class="bi bi-music-note-beamed  fs-1"></i>
                </div>
            HTML;
        }

        $badge = $this->getBadge();
        $subtitle = $this->getSubtitle();
        $deleteBtn = $this->renderDeleteButton();
        $deleteBtnHtml = !empty($deleteBtn)
        ? "<div class=\"mt-2 text-end\">{$deleteBtn}</div>"
        : '';

        return <<<HTML
            <div>
                <div class="card shadow" style="height:100%">
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
                            {$deleteBtnHtml}
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
        $deleteBtn = $this->renderDeleteButton();

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
                                        <div class="d-flex align-items-center gap-2">
                                            {$badge}
                                            {$deleteBtn}
                                        </div>
                                    </div>
                                    <h6 class="card-subtitle text-muted mb-2">
                                        {$subtitle}
                                    </h6>
                                    {$infosHtml}
                                </div>
                                <div class="mt-auto">
                                    {$this->renderAudioPlayer(Renderer::LONG)}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        HTML;
    }

    /**
     * Bouton de suppression de la piste
     */
    protected function renderDeleteButton() : string {
        $idTrack = $this->track->get('id');
        if (empty($idTrack)) {
            return '';
        }

        return <<<HTML
            <a href="?action=delete-track&id={$idTrack}" class="btn btn-sm btn-outline-danger">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/trash-fill/ -->
                <i class="bi bi-trash-fill"></i>
                Supprimer
            </a>
        HTML;
    }

    protected function renderAudioPlayer(int $selector) : string {
        if ($selector === Renderer::COMPACT) {
            return <<<HTML
                <media-theme-tailwind-audio class="audio-compact">
                    <audio
                        slot="media"
                        src="../audio/{$this->track->get("filename")}"
                        crossorigin="anonymous"
                    ></audio>
                </media-theme-tailwind-audio>
            HTML;
        } else return <<<HTML
            <media-theme-tailwind-audio class="audio-long">
                <audio
                    slot="media"
                    src="../audio/{$this->track->get("filename")}"
                    crossorigin="anonymous"
                ></audio>
            </media-theme-tailwind-audio>
        HTML;
    }
}