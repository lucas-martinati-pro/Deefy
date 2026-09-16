<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

/**
 * Liste de lecture modifiable de pistes audio.
 */
class Playlist extends AudioList {

    /**
     * Ajoute une piste audio à la fin de la playlist et met à jour les totaux.
     *
     * @param AudioTrack $track Piste audio à ajouter.
     * @return void
     */
    public function addPiste(AudioTrack $track) : void {
        $this->tracks[] = $track;
        $this->totalDuration += $track->get("duration");
        $this->trackCount++;
    }

    /**
     * Supprime une piste de la playlist à l'indice spécifié et réindexe les pistes.
     *
     * @param int $indice Index de la piste à retirer dans le tableau.
     * @return void
     */
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
     * Ajoute un lot de pistes audio à la playlist en ignorant les pistes déjà présentes (sans doublon).
     *
     * @param AudioTrack[] $tracks Tableau de pistes à ajouter.
     * @return void
     */
    public function addPistes(array $tracks) : void {
        foreach ($tracks as $track) {
            if (!in_array($track, $this->tracks, true)) {
                $this->addPiste($track);
            }
        }
    }
}