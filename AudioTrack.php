<?php

class AudioTrack {

    public string $title, $path, $genre;
    // duration en secondes
    public int $duration;

    public function __construct(string $title, string $path) {
        $this->title = $title;
        $this->path = $path;
    }

    // Utiliser json_encore() et get_onject_vars()
    public function __toString() : string {
        // return "{$this->trackNumber}-{$this->title} - by {$this->artist} (from {$this->album}, {$this->year}) - {$this->duration}s : {$this->genre}\n";
        return json_encode(get_object_vars($this));
    }
}