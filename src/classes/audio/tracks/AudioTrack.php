<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

/**
 * Classe représentant une piste audio générique.
 */
class AudioTrack {

    /**
     * Titre de la piste.
     */
    protected string $title;

    /**
     * Nom du fichier audio stocké sur le serveur.
     */
    protected string $filename;

    /**
     * Genre musical ou thématique de la piste.
     */
    protected ?string $genre = null;

    /**
     * Durée de la piste en secondes.
     */
    protected int $duration = 0;

    /**
     * Identifiant unique en base de données.
     */
    protected ?int $id = null;

    /**
     * Nom du fichier image de couverture (pochette).
     */
    protected ?string $image = null;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string|null
     */
    public function getGenre(): ?string
    {
        return $this->genre;
    }

    /**
     * @return string|null
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param int|null $id
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string|null $image
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * @param int $duration
     * @throws InvalidPropertyValueException
     */
    public function setDuration(int $duration) : void {
        if ($duration < 0) {
            throw new InvalidPropertyValueException("duration : invalid value ($duration)");
        }
        $this->duration = $duration;
    }

    /**
     * @param string|null $genre
     */
    public function setGenre(?string $genre): void
    {
        $this->genre = $genre;
    }

    /**
     * Retourne la valeur d'une propriété de la piste.
     *
     * Je sais utiliser les getters/setters magiques, mais j'ai décidé de ne pas les utiliser dans mon projet
     * car je trouve cette utilisation pas très utilisables dans de réel projets
     * Le commit c8c2c9ca0c9cc24e00866cf67b10d748135d8126 comporte encore l'utilisation des getters/setters magiques
     *
     * @param string $name Nom de la propriété à lire.
     * @return mixed Valeur de la propriété demandée.
     * @throws InvalidPropertyNameException Si la propriété n'existe pas.
     */
    public function __get(string $name) : mixed {
        if (property_exists($this, $name)) return $this->$name ?? null;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    /**
     * Modifie la valeur d'une propriété modifiable de la piste.
     *
     * Le titre et le nom de fichier ne sont pas modifiables via __set().
     * La durée doit être un entier positif ou nul.
     *
     * Je sais utiliser les getters/setters magiques, mais j'ai décidé de ne pas les utiliser dans mon projet
     * car je trouve cette utilisation pas très utilisables dans de réel projets
     * Le commit c8c2c9ca0c9cc24e00866cf67b10d748135d8126 comporte encore l'utilisation des getters/setters magiques
     *
     * @param string $name Nom de la propriété à modifier.
     * @param mixed $value Nouvelle valeur à affecter.
     * @return void
     * @throws InvalidPropertyNameException Si la propriété n'existe pas ou n'est pas modifiable.
     * @throws InvalidPropertyValueException Si la valeur fournie est invalide (ex: durée négative).
     */
    public function __set(string $name, mixed $value) : void {
        if (property_exists($this, $name) && ($name !== "title" && $name !== "filename"))
            if ($name === "duration" && $value < 0) {
                throw new InvalidPropertyValueException("$name : invalid value ($value)");
            } else $this->$name = $value;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    /**
     * Constructeur d'une piste audio.
     *
     * @param string $title Titre de la piste.
     * @param string $filename Nom du fichier audio associé.
     */
    public function __construct(string $title, string $filename) {
        $this->title = $title;
        $this->filename = $filename;
    }

    /**
     * Retourne la représentation JSON de l'objet piste audio.
     *
     * @return string Représentation JSON des propriétés de l'objet.
     */
    public function __toString() : string {
        return json_encode(get_object_vars($this));
    }
}