<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

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