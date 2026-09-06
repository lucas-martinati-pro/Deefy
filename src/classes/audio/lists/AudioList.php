<?php

namespace iutnc\deefy\audio\lists;

use \Iterator;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;

class AudioList implements Iterator {
    protected string $name;
    protected int $trackCount, $totalDuration;
    protected ?int $id = null;
    protected array $tracks;
    private int $position = 0;

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
        $this->tracks = array_values($tracks); // Réindexation pour garantir des clés 0, 1, 2...

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

    /**======================
     *    METHODES POUR ITERATOR
     *========================**/
    #[\Override]
    public function current(): mixed {
        return $this->tracks[$this->position];
    }

    #[\Override]
    public function key(): mixed {
        return $this->position;
    }

    #[\Override]
    public function next(): void {
        $this->position++;
    }

    #[\Override]
    public function rewind(): void {
        $this->position = 0;
    }

    #[\Override]
    // Vérifier que la variable existe
    public function valid(): bool {
        return isset($this->tracks[$this->position]);
    }
}