<?php

namespace iutnc\deefy\audio\tracks;

/**
 * Piste audio représentant un épisode de podcast.
 */
class PodcastTrack extends AudioTrack {

    /**
     * Auteur ou créateur du podcast.
     */
    protected ?string $author = null;

    /**
     * Date de sortie du podcast.
     */
    protected ?string $date = null;

    /**
     * Constructeur d'une piste de podcast.
     *
     * @param string $title Titre de l'épisode.
     * @param string $filename Nom du fichier audio.
     * @param string|null $author Auteur ou créateur (optionnel).
     * @param string|null $date Date de diffusion (optionnelle).
     * @param int|null $duration Durée en secondes (optionnelle).
     * @param string|null $genre Genre ou thématique (optionnel).
     */
    public function __construct(
        string $title,
        string $filename,
        ?string $author = null,
        ?string $date = null,
        ?int $duration = null,
        ?string $genre = null
    ) {
        parent::__construct($title, $filename);

        $this->author = $author;
        $this->date = $date;
        if ($duration !== null) $this->duration = $duration;
        if ($genre !== null) $this->genre = $genre;
    }
}