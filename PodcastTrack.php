<?php

require_once 'AudioTrack.php';

class PodcastTrack extends AudioTrack {

    public string $author, $date;

    public function __construct(string $title, string $path) {
        parent::__construct($title, $path);
    }
}