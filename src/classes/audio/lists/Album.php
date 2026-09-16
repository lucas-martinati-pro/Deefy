<?php

namespace iutnc\deefy\audio\lists;

use iutnc\deefy\audio\tracks\AudioTrack;

/**
 * Représente un album musical complet constitué d'un ensemble de pistes audio.
 * Étend AudioList en introduisant les métadonnées d'artiste et de date de sortie.
 */
class Album extends AudioList {
    /**
     * Nom de l'artiste ou du groupe musical.
     */
    protected string $artist;

    /**
     * Date de sortie de l'album.
     */
    protected string $date;

    /**
     * Constructeur d'un album musical.
     *
     * @param string $name Nom de l'album.
     * @param AudioTrack[] $tracks Tableau des pistes audio composant l'album.
     */
    public function __construct(string $name, array $tracks) {
        parent::__construct($name, $tracks);
    }
}