<?php

namespace iutnc\deefy\audio\tracks;

require_once 'AudioTrack.php';

class PodcastTrack extends AudioTrack {

    protected string $author, $date;

    public function __construct(string $title, string $path) {
        parent::__construct($title, $path);
    }
}