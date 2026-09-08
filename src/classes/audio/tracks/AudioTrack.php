<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

class AudioTrack {

    protected string $title, $filename;
    protected ?string $genre = null;
    // duration en secondes
    protected int $duration = 0;
    protected ?int $id = null;
    protected ?string $image = null;

    public function get(string $name) : mixed {
        if (property_exists($this, $name)) return $this->$name ?? null;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    public function set(string $name, mixed $value) : void {
        if (property_exists($this, $name) && ($name !== "title" && $name !== "filename"))
            if ($name === "duration" && $value < 0) {
                throw new InvalidPropertyValueException("$name : invalid value ($value)");
            } else $this->$name = $value;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    public function __construct(string $title, string $filename) {
        $this->title = $title;
        $this->filename = $filename;
    }

    // Utiliser json_encore() et get_onject_vars()
    public function __toString() : string {
        return json_encode(get_object_vars($this));
    }
}