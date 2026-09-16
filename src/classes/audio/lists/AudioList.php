<?php

namespace iutnc\deefy\audio\lists;

use \Iterator;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;

/**
 * Liste ordonnée de pistes audio.
 */
class AudioList implements Iterator {
    /**
     * Nom de la liste audio.
     */
    protected string $name;

    /**
     * Nombre total de morceaux dans la liste.
     */
    protected int $trackCount;

    /**
     * Durée totale de la liste en secondes.
     */
    protected int $totalDuration;

    /**
     * Identifiant de la liste en base de données.
     */
    protected ?int $id = null;

    /**
     * Tableau des pistes audio de la liste.
     */
    protected array $tracks;

    /**
     * Index de la position courante pour l'itération.
     */
    private int $position = 0;

    /**
     * Getter magique pour accéder aux propriétés protégées en lecture seule.
     *
     * @param string $name Nom de la propriété à lire.
     * @return mixed Valeur de la propriété.
     * @throws InvalidPropertyNameException Si la propriété demandée n'existe pas.
     */
    public function __get(string $name) : mixed {
        if (property_exists($this, $name)) return $this->$name;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    /**
     * Constructeur d'une liste audio.
     *
     * @param string $name Nom de la liste audio.
     * @param AudioTrack[] $tracks Tableau initial de pistes audio (optionnel).
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

    /**
     * Modifie une propriété autorisée de la liste audio (id, artist ou date).
     *
     * @param string $name Nom de la propriété à modifier.
     * @param mixed $value Nouvelle valeur.
     * @return void
     * @throws InvalidPropertyNameException Si la propriété n'est pas modifiable.
     */
    public function set(string $name, mixed $value) : void {
        if ($name === "artist" || $name === "date" || $name === "id") $this->$name = $value;
        else throw new InvalidPropertyNameException("cannot modify property : $name");
    }

    /**========================================================================
     *                         METHODES ITERATOR
     *========================================================================**/

    #[\Override]
    public function current() : mixed {
        return $this->tracks[$this->position];
    }

    #[\Override]
    public function key() : mixed {
        return $this->position;
    }

    #[\Override]
    public function next() : void {
        $this->position++;
    }

    #[\Override]
    public function rewind() : void {
        $this->position = 0;
    }

    #[\Override]
    // Vérifier que la variable existe
    public function valid() : bool {
        return isset($this->tracks[$this->position]);
    }
}