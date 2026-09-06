<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;
require_once 'AudioList.php';
use iutnc\deefy\exception\InvalidPropertyNameException;
require_once 'InvalidPropertyNameException.php';

class Album extends AudioList {
    protected string $artist, $date;

    public function set(string $name, mixed $value) : void {
        if ($name === "artist" || $name === "date") $this->$name = $value;
        else throw new InvalidPropertyNameException("cannot modify property : $name");
    }

    /**
     * @param AudioTrack[] $tracks
     */
    public function __construct(string $name, array $tracks) {
        parent::__construct($name, $tracks);
    }
}