<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;

class AudioList {
    protected string $name;
    protected int $trackCount, $totalDuration;
    protected ?int $id = null;
    protected array $tracks;

    public function __get(string $name) : mixed {
        if (property_exists($this, $name)) return $this->$name;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    /**
     * @param AudioTrack[] $tracks
     */
    public function __construct(string $name, array $tracks = []) {
        $this->name = $name;
        $this->tracks = $tracks;

        $this->totalDuration = 0;
        $this->trackCount = 0;
        foreach ($tracks as $track) {
            $this->totalDuration += $track->get("duration");
            $this->trackCount++;
        }
    }

    public function set(string $name, mixed $value) : void {
        if ($name === "artist" || $name === "date") $this->$name = $value;
        else throw new InvalidPropertyNameException("cannot modify property : $name");
    }
}