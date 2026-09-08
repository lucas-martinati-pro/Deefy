<?php

namespace iutnc\deefy\audio\tracks;

class PodcastTrack extends AudioTrack {

    protected ?string $author = null;
    protected ?string $date = null;

    public function __construct(
        string $title,
        string $filename,
        ?string $author = null,
        ?string $date = null,
        ?int $duration = null,
        ?string $genre = null
    ) {
        parent::__construct($title, $filename);

        $this->author = $author;
        $this->date = $date;
        if ($duration !== null) $this->duration = $duration;
        if ($genre !== null) $this->genre = $genre;
    }
}