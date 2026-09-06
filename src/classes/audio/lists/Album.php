<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;

class Album extends AudioList {
    protected string $artist, $date;

    /**
     * @param AudioTrack[] $tracks
     */
    public function __construct(string $name, array $tracks) {
        parent::__construct($name, $tracks);
    }
}