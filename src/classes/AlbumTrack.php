<?php

namespace iutnc\deefy\audio\tracks;

require_once 'AudioTrack.php';
use iutnc\deefy\exception\InvalidPropertyNameException;
require_once 'InvalidPropertyNameException.php';
use iutnc\deefy\exception\InvalidPropertyValueException;
require_once 'InvalidPropertyValueException.php';

class AlbumTrack extends AudioTrack {

    protected string $artist, $album;
    protected int $year, $trackNumber;

    #[\Override]
    public function set(string $name, mixed $value) : void {
        if (property_exists($this, $name) && $name !== "title" && $name !== "path" && $name !== "album" && $name !== "trackNumber") {
            if ($name === "duration" && $value < 0) {
                throw new InvalidPropertyValueException("$name : invalid value ($value)");
            } else $this->$name = $value;
        }
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    public function __construct(string $title, string $path, string $album, int $trackNumber) {
        parent::__construct($title, $path);
        $this->album = $album;
        $this->trackNumber = $trackNumber;
    }
}