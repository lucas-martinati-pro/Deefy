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
     * Retourne l'auteur ou le créateur du podcast.
     *
     * @return string|null Auteur du podcast, null si non renseigné.
     */
    public function getAuthor(): ?string
    {
        return $this->author;
    }

    /**
     * Retourne la date de sortie du podcast.
     *
     * @return string|null Date de sortie, null si non renseignée.
     */
    public function getDate(): ?string
    {
        return $this->date;
    }

    /**
     * Affecte l'auteur ou le créateur du podcast.
     *
     * @param string|null $author Auteur du podcast, null si non renseigné.
     * @return void
     */
    public function setAuthor(?string $author): void
    {
        $this->author = $author;
    }

    /**
     * Affecte la date de sortie du podcast.
     *
     * @param string|null $date Date de sortie, null si non renseignée.
     * @return void
     */
    public function setDate(?string $date): void
    {
        $this->date = $date;
    }

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