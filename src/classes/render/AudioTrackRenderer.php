<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\enums\TypeRender;
use iutnc\deefy\config\Config;

/**
 * Classe abstraite de base pour le rendu HTML des pistes audio.
 */
abstract class AudioTrackRenderer implements Renderer {

    /**
     * Piste audio à afficher.
     */
    protected AudioTrack $track;

    /**
     * Identifiant de la playlist parente si la piste est rendue dans le contexte d'une playlist.
     */
    protected ?int $playlistId;

    /**
     * Initialise le renderer avec la piste audio à afficher et son contexte de playlist optionnel.
     *
     * @param AudioTrack $track Piste audio à restituer en HTML.
     * @param int|null $playlistId Identifiant optionnel de la playlist parente.
     */
    public function __construct(AudioTrack $track, ?int $playlistId = null) {
        $this->track = $track;
        $this->playlistId = $playlistId;
    }

    #[Override]
    public function render(TypeRender $selector = TypeRender::COMPACT) : string {
        return ($selector === TypeRender::LONG) ? $this->renderLong() : $this->renderCompact();
    }

    /**
     * Retourne le sous-titre de la piste (ex: auteur pour un podcast, artiste/album pour un album).
     *
     * @return string Balises HTML du sous-titre.
     */
    abstract protected function getSubtitle() : string;

    /**
     * Retourne le badge HTML identifiant le type de piste (Podcast, Album/numéro de piste, etc.).
     *
     * @return string Balises HTML du badge.
     */
    abstract protected function getBadge() : string;

    /**
     * Retourne la liste des métadonnées sous forme de tableau de chaînes.
     *
     * @return string[]
     */
    protected function getDetails() : array {
        $details = [];

        $duration = $this->track->duration;
        if ($duration > 0) {
            $details[] = "Durée : {$duration}s";
        }

        $genre = trim($this->track->genre ?? '');
        if (!empty($genre)) {
            $details[] = "Genre : {$genre}";
        }

        return $details;
    }

    /**
     * Rendu compact : carte verticale pour affichage en grille.
     *
     * @return string Balises HTML de la carte compacte.
     */
    protected function renderCompact() : string {
        $details = $this->getDetails();
        $infos = implode(' | ', $details);
        $infosHtml = !empty($infos) ? "<div class=\"small text-body-secondary mb-2\">{$infos}</div>" : '';

        $image = $this->track->image;
        $hasImage = (!empty($image));
        if ($hasImage) {
            $coverDir = Config::getCoverWebPath();
            $imageHtml = <<<HTML
                <img src="{$coverDir}{$image}" class="card-img-top object-fit-cover" style="height: 180px;" alt="{$this->track->title}">
            HTML;
        } else {
            $imageHtml = <<<HTML
                <div class="card-img-top d-flex align-items-center justify-content-center text-secondary border-bottom" style="height: 180px;">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-beamed/ -->
                    <i class="bi bi-music-note-beamed  fs-1"></i>
                </div>
            HTML;
        }

        $badge = $this->getBadge();
        $subtitle = $this->getSubtitle();
        $playBtn = $this->renderPlayButton();
        $deleteBtn = $this->renderDeleteButton();
        $addToPlaylist = $this->renderAddToPlaylist();
        $actionsHtml = (!empty($playBtn) || !empty($deleteBtn))
            ? "<div class=\"mt-2 d-flex justify-content-between align-items-center gap-2\"><div>{$playBtn}</div><div>{$deleteBtn}</div></div>"
            : '';

        return <<<HTML
            <div class="col">
                <div class="card h-100">
                    {$imageHtml}
                    <div class="card-body d-flex flex-column justify-content-between p-3">
                        <div>
                            <div class="d-flex justify-content-between align-items-start gap-1 mb-1">
                                <h5 class="card-title fw-bold fs-6 mb-0 text-truncate">{$this->track->title}</h5>
                                {$badge}
                            </div>
                            <h6 class="card-subtitle text-muted small mb-2 text-truncate">
                                {$subtitle}
                            </h6>
                            {$infosHtml}
                        </div>
                        <div class="mt-auto pt-2">
                            {$this->renderAudioPlayer(TypeRender::COMPACT)}
                            {$actionsHtml}
                            {$addToPlaylist}
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }

    /**
     * Rendu long : carte horizontale détaillée avec informations complètes et lecteur audio.
     *
     * @return string Balises HTML de la carte longue.
     */
    protected function renderLong() : string {
        $details = $this->getDetails();
        $infos = implode(' | ', $details);
        $infosHtml = !empty($infos) ? "<p class=\"card-text text-body-secondary small mb-3\">{$infos}</p>" : '';

        $image = $this->track->image;
        $hasImage = (!empty($image));

        if ($hasImage) {
            $coverDir = Config::getCoverWebPath();
            $imageHtml = <<<HTML
                <div class="col-md-3 col-lg-2">
                    <img src="{$coverDir}{$image}" class="img-fluid rounded-start w-100 h-100 object-fit-cover" style="min-height: 140px; max-height: 200px;" alt="{$this->track->title}">
                </div>
            HTML;
            $colContent = 'col-md-9 col-lg-10';
        } else {
            $imageHtml = <<<HTML
                <div class="col-md-3 col-lg-2">
                    <div class="d-flex align-items-center justify-content-center text-secondary rounded-start w-100 h-100 border-end" style="min-height: 140px;">
                        <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/music-note-beamed/ -->
                        <i class="bi bi-music-note-beamed fs-1"></i>
                    </div>
                </div>
            HTML;
            $colContent = 'col-md-9 col-lg-10';
        }

        $badge = $this->getBadge();
        $subtitle = $this->getSubtitle();
        $playBtn = $this->renderPlayButton();
        $deleteBtn = $this->renderDeleteButton();

        return <<<HTML
            <div class="card mb-3">
                <div class="row g-0">
                    {$imageHtml}
                    <div class="{$colContent}">
                        <div class="card-body d-flex flex-column justify-content-between h-100 p-3">
                            <div>
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-1">
                                    <h5 class="card-title fw-bold mb-0">{$this->track->title}</h5>
                                    <div class="d-flex align-items-center gap-2">
                                        {$badge}
                                        {$playBtn}
                                        {$deleteBtn}
                                    </div>
                                </div>
                                <h6 class="card-subtitle text-muted mb-2">
                                    {$subtitle}
                                </h6>
                                {$infosHtml}
                            </div>
                            <div>
                                {$this->renderAudioPlayer(TypeRender::LONG)}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        HTML;
    }

    /**
     * Génère le formulaire avec le bouton pour écouter la piste dans le lecteur principal persistant.
     *
     * @return string Balises HTML du bouton de lecture.
     */
    protected function renderPlayButton() : string {
        $idTrack = $this->track->id;
        if (empty($idTrack)) return '';

        return <<<HTML
            <form method="post" action="" class="d-inline">
                <input type="hidden" name="add-player-track" value="{$idTrack}">
                <button class="btn btn-outline-primary btn-sm" title="Ajouter le track {$idTrack} au lecteur">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/play-fill/ -->
                <i class="bi bi-play-fill me-1"></i>Lire
                </button>
            </form>
        HTML;
    }

    /**
     * Génère le lien/bouton de suppression de la piste.
     *
     * @return string Balises HTML du bouton de suppression.
     */
    protected function renderDeleteButton() : string {
        $idTrack = $this->track->id;
        if (empty($idTrack)) {
            return '';
        }

        // Si on a un id de playlist -> on affiche "Retirer" avec id_pl
        if ($this->playlistId !== null) {
            return <<<HTML
                <a href="?action=delete-track&id={$idTrack}&id_pl={$this->playlistId}" class="btn btn-sm btn-outline-danger">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/x-circle-fill/ -->
                    <i class="bi bi-x-circle-fill me-1"></i>
                    Retirer de la playlist
                </a>
            HTML;
        }
        // Sinon (Mes pistes) -> on affiche "Supprimer"
        return <<<HTML
            <a href="?action=delete-track&id={$idTrack}" class="btn btn-sm btn-outline-danger">
                <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/trash-fill/ -->
                <i class="bi bi-trash-fill me-1"></i>
                Supprimer
            </a>
        HTML;
    }

    /**
     * Génère le composant bouton pour ajouter la piste à une playlist.
     *
     * @return string Balises HTML du bouton d'ajout à une playlist.
     */
    protected function renderAddToPlaylist() : string {
        $idTrack = $this->track->id;
        if (empty($idTrack)) {
            return '';
        }
        if ($this->playlistId !== null) return '';

        return <<<HTML
            <div class="mt-2">
                <a href="?action=move-track&id={$idTrack}" class="btn btn-sm btn-outline-primary w-100 d-inline-flex align-items-center justify-content-center" title="Ajouter cette piste à une playlist">
                    <!-- Icône Bootstrap - https://icons.getbootstrap.com/icons/plus-circle/ -->
                    <i class="bi bi-plus-circle me-1"></i>
                    Ajouter à une playlist
                </a>
            </div>
        HTML;
    }

    /**
     * Génère le composant lecteur audio.
     *
     * @param TypeRender $selector Mode d'affichage souhaité (TypeRender::COMPACT ou TypeRender::LONG).
     * @return string Balises HTML du lecteur audio.
     */
    protected function renderAudioPlayer(TypeRender $selector) : string {
        $audioDir = Config::getAudioWebPath();

        if ($selector === TypeRender::COMPACT) {
            return <<<HTML
                <media-theme-tailwind-audio class="audio-compact">
                    <audio
                        slot="media"
                        src="{$audioDir}{$this->track->filename}"
                        crossorigin="anonymous"
                    ></audio>
                </media-theme-tailwind-audio>
            HTML;
        } else return <<<HTML
            <media-theme-tailwind-audio class="audio-long">
                <audio
                    slot="media"
                    src="{$audioDir}{$this->track->filename}"
                    crossorigin="anonymous"
                ></audio>
            </media-theme-tailwind-audio>
        HTML;
    }
}