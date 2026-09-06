<?php

require_once 'AudioTrack.php';

class AlbumTrack extends AudioTrack {

    public string $artist, $album;
    public int $year, $trackNumber;

    public function __construct(string $title, string $path, string $album, int $trackNumber) {
        parent::__construct($title, $path);
        $this->album = $album;
        $this->trackNumber = $trackNumber;
    }
}