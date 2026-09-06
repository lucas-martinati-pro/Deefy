<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

class AudioTrack {

    protected string $title, $path, $genre;
    // duration en secondes
    protected int $duration;
    protected ?int $id = null;

    public function get(string $name) : mixed {
        if (property_exists($this, $name)) return $this->$name;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    public function set(string $name, mixed $value) : void {
        if (property_exists($this, $name) && ($name !== "title" && $name !== "path"))
            if ($name === "duration" && $value < 0) {
                throw new InvalidPropertyValueException("$name : invalid value ($value)");
            } else $this->$name = $value;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    public function __construct(string $title, string $path) {
        $this->title = $title;
        $this->path = $path;
    }

    // Utiliser json_encore() et get_onject_vars()
    public function __toString() : string {
        return json_encode(get_object_vars($this));
    }
}