<?php

namespace iutnc\deefy\audio\lists;

require_once 'AudioList.php';
use iutnc\deefy\audio\tracks\AudioTrack;
require_once 'AudioTrack.php';

class Playlist extends AudioList {

    public function addPiste(AudioTrack $track) : void {
        $this->tracks[] = $track;
        $this->totalDuration += $track->get("duration");
        $this->trackCount++;
    }

    public function removePiste(int $indice) : void {
        // Le isset permet de vérifier si l'indice existe
        if (isset($this->tracks[$indice])) {
            $this->totalDuration -= $this->tracks[$indice]->get("duration");
            // unset($this->tracks[$indice]);
            // array_splice supprime l'élément ET réindexe le tableau automatiquement
            array_splice($this->tracks, $indice, 1);
            $this->trackCount--;
        }
    }

    /**
     * @param AudioTrack[] $tracks
     */
    public function addPistes(array $tracks) : void {
        foreach ($tracks as $track) {
            if (!in_array($track, $this->tracks, true)) {
                $this->addPiste($track);
            }
        }
    }
}