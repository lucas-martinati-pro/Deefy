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
     * @return string|null
     */
    public function getArtist(): ?string
    {
        return $this->artist;
    }

    /**
     * @return string
     */
    public function getAlbum(): string
    {
        return $this->album;
    }

    /**
     * @return int|null
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @return int
     */
    public function getTrackNumber(): int
    {
        return $this->trackNumber;
    }

    /**
     * @param string|null $artist
     */
    public function setArtist(?string $artist): void
    {
        $this->artist = $artist;
    }

    /**
     * @param string $album
     */
    public function setAlbum(string $album): void
    {
        $this->album = $album;
    }

    /**
     * @param int|null $year
     */
    public function setYear(?int $year): void
    {
        $this->year = $year;
    }

    /**
     * @param int $trackNumber
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