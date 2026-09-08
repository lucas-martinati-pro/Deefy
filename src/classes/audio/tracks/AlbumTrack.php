<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

class AlbumTrack extends AudioTrack {

    protected ?string $artist = null;
    protected string $album;
    protected ?int $year = null;
    protected int $trackNumber = 1;

    #[\Override]
    public function set(string $name, mixed $value) : void {
        if (property_exists($this, $name) && $name !== "title" && $name !== "filename" && $name !== "album" && $name !== "trackNumber") {
            if ($name === "duration" && $value < 0) {
                throw new InvalidPropertyValueException("$name : invalid value ($value)");
            } else $this->$name = $value;
        }
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    public function __construct(string $title, string $filename, string $album, int $trackNumber) {
        parent::__construct($title, $filename);
        $this->album = $album;
        $this->trackNumber = $trackNumber;
    }
}