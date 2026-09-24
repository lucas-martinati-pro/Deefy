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
     * Retourne l'identifiant unique de la piste en base de données.
     *
     * @return int|null Identifiant de la piste, null si non persistée.
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Retourne la durée de la piste en secondes.
     *
     * @return int Durée de la piste en secondes.
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Retourne le nom du fichier audio stocké sur le serveur.
     *
     * @return string Nom du fichier audio.
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Retourne le genre musical ou thématique de la piste.
     *
     * @return string|null Genre de la piste, null si non renseigné.
     */
    public function getGenre(): ?string
    {
        return $this->genre;
    }

    /**
     * Retourne le nom du fichier image de couverture (pochette).
     *
     * @return string|null Nom du fichier image, null si aucune couverture.
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * Retourne le titre de la piste.
     *
     * @return string Titre de la piste.
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Affecte l'identifiant unique de la piste en base de données.
     *
     * @param int|null $id Identifiant à affecter, null si non persistée.
     * @return void
     */
    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     * Affecte le fichier image de couverture (pochette) de la piste.
     *
     * @param string|null $image Nom du fichier image, null pour retirer la couverture.
     * @return void
     */
    public function setImage(?string $image): void
    {
        $this->image = $image;
    }

    /**
     * Affecte la durée de la piste en secondes.
     *
     * @param int $duration Durée en secondes, doit être positive ou nulle.
     * @return void
     * @throws InvalidPropertyValueException Si la durée est négative.
     */
    public function setDuration(int $duration) : void {
        if ($duration < 0) {
            throw new InvalidPropertyValueException("duration : invalid value ($duration)");
        }
        $this->duration = $duration;
    }

    /**
     * Affecte le genre musical ou thématique de la piste.
     *
     * @param string|null $genre Genre à affecter, null si non renseigné.
     * @return void
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