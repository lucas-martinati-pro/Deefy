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
     * Retourne la valeur d'une propriété de la piste.
     *
     * @param string $name Nom de la propriété à lire.
     * @return mixed Valeur de la propriété demandée.
     * @throws InvalidPropertyNameException Si la propriété n'existe pas.
     */
    public function get(string $name) : mixed {
        if (property_exists($this, $name)) return $this->$name ?? null;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    /**
     * Modifie la valeur d'une propriété modifiable de la piste.
     *
     * Le titre et le nom de fichier ne sont pas modifiables via set().
     * La durée doit être un entier positif ou nul.
     *
     * @param string $name Nom de la propriété à modifier.
     * @param mixed $value Nouvelle valeur à affecter.
     * @return void
     * @throws InvalidPropertyNameException Si la propriété n'existe pas ou n'est pas modifiable.
     * @throws InvalidPropertyValueException Si la valeur fournie est invalide (ex: durée négative).
     */
    public function set(string $name, mixed $value) : void {
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