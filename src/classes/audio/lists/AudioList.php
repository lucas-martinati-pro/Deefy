<?php

namespace iutnc\deefy\audio\lists;

use Iterator;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\exception\InvalidPropertyNameException;
use Override;

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
     * @var AudioTrack[]
     */
    protected array $tracks;

    /**
     * Index de la position courante pour l'itération.
     */
    private int $position = 0;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getTotalDuration(): int
    {
        return $this->totalDuration;
    }

    /**
     * @return int
     */
    public function getTrackCount(): int
    {
        return $this->trackCount;
    }

    /**
     * @return array
     */
    public function getTracks(): array
    {
        return $this->tracks;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Constructeur d'une liste audio.
     *
     * @param string $name Nom de la liste audio.
     * @param AudioTrack[] $tracks Tableau initial de pistes audio (optionnel).
     */
    public function __construct(string $name, array $tracks = []) {
        $this->name = $name;
        $this->tracks = array_values($tracks); // Réindexation pour garantir des clés 0, 1, 2...

        $this->totalDuration = 0;
        $this->trackCount = 0;
        foreach ($tracks as $track) {
            $this->totalDuration += $track->getDuration();
            $this->trackCount++;
        }
    }

    /**
     * Getter magique pour accéder aux propriétés protégées en lecture seule.
     *
     * Je sais utiliser les getters/setters magiques, mais j'ai décidé de ne pas les utiliser dans mon projet
     * car je trouve cette utilisation pas très utilisables dans de réel projets
     * Le commit c8c2c9ca0c9cc24e00866cf67b10d748135d8126 comporte encore l'utilisation des getters/setters magiques
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
     * Modifie une propriété autorisée de la liste audio (id, artist ou date).
     *
     * Je sais utiliser les getters/setters magiques, mais j'ai décidé de ne pas les utiliser dans mon projet
     * car je trouve cette utilisation pas très utilisables dans de réel projets
     * Le commit c8c2c9ca0c9cc24e00866cf67b10d748135d8126 comporte encore l'utilisation des getters/setters magiques
     *
     * @param string $name Nom de la propriété à modifier.
     * @param mixed $value Nouvelle valeur.
     * @return void
     * @throws InvalidPropertyNameException Si la propriété n'est pas modifiable.
     */
    public function __set(string $name, mixed $value) : void {
        if ($name === "artist" || $name === "date" || $name === "id") $this->$name = $value;
        else throw new InvalidPropertyNameException("cannot modify property : $name");
    }

    /**========================================================================
     *                         METHODES ITERATOR
     *========================================================================**/

    #[Override]
    public function current() : mixed {
        return $this->tracks[$this->position];
    }

    #[Override]
    public function key() : int {
        return $this->position;
    }

    #[Override]
    public function next() : void {
        $this->position++;
    }

    #[Override]
    public function rewind() : void {
        $this->position = 0;
    }

    #[Override]
    // Vérifier que la variable existe
    public function valid() : bool {
        return isset($this->tracks[$this->position]);
    }
}