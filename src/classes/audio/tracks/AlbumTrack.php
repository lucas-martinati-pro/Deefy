<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;
use Override;

/**
 * Piste audio représentant un morceau appartenant à un album musical.
 */
class AlbumTrack extends AudioTrack {

    /**
     * Nom de l'artiste ou du groupe musical.
     */
    protected ?string $artist = null;

    /**
     * Nom de l'album contenant le morceau.
     */
    protected string $album;

    /**
     * Année de parution de l'album.
     */
    protected ?int $year = null;

    /**
     * Numéro de piste dans l'album.
     */
    protected int $trackNumber = 1;

    /**
     * Retourne le nom de l'artiste ou du groupe musical.
     *
     * @return string|null Nom de l'artiste, null si non renseigné.
     */
    public function getArtist(): ?string
    {
        return $this->artist;
    }

    /**
     * Retourne le nom de l'album contenant le morceau.
     *
     * @return string Nom de l'album.
     */
    public function getAlbum(): string
    {
        return $this->album;
    }

    /**
     * Retourne l'année de parution de l'album.
     *
     * @return int|null Année de parution, null si non renseignée.
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * Retourne le numéro de piste dans l'album.
     *
     * @return int Numéro de piste dans l'album.
     */
    public function getTrackNumber(): int
    {
        return $this->trackNumber;
    }

    /**
     * Affecte le nom de l'artiste ou du groupe musical.
     *
     * @param string|null $artist Nom de l'artiste, null si non renseigné.
     * @return void
     */
    public function setArtist(?string $artist): void
    {
        $this->artist = $artist;
    }

    /**
     * Affecte l'année de parution de l'album.
     *
     * @param int|null $year Année de parution, null si non renseignée.
     * @return void
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    /**
     * Affecte le numéro de piste dans l'album.
     *
     * @param int $trackNumber Numéro de piste dans l'album.
     * @return void
     */
    public function setTrackNumber(int $trackNumber): void
    {
        $this->trackNumber = $trackNumber;
    }

    #[Override]
    public function __set(string $name, mixed $value) : void {
        if (property_exists($this, $name) && $name !== "title" && $name !== "filename" && $name !== "album" && $name !== "trackNumber") {
            if ($name === "duration" && $value < 0) {
                throw new InvalidPropertyValueException("$name : invalid value ($value)");
            } else $this->$name = $value;
        }
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    /**
     * Constructeur d'un morceau d'album.
     *
     * @param string $title Titre du morceau.
     * @param string $filename Nom du fichier audio.
     * @param string $album Nom de l'album.
     * @param int $trackNumber Numéro de la piste dans l'album.
     */
    public function __construct(string $title, string $filename, string $album, int $trackNumber) {
        parent::__construct($title, $filename);
        $this->album = $album;
        $this->trackNumber = $trackNumber;
    }
}