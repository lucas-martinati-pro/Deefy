<?php

namespace iutnc\deefy\config;

/**
 * Fournit les chemins d'accès aux répertoires de médias (audio, couvertures).
 */
class Config {
    /**
     * Retourne le chemin système vers le répertoire des fichiers audio.
     *
     * @return string Chemin système du répertoire audio.
     */
    public static function getAudioDir(): string {
        return 'audio' . DIRECTORY_SEPARATOR;
    }

    /**
     * Retourne le chemin système vers le répertoire des images de couverture.
     *
     * @return string Chemin système du répertoire des couvertures.
     */
    public static function getCoverDir(): string {
        return 'image' . DIRECTORY_SEPARATOR . 'covers' . DIRECTORY_SEPARATOR;
    }

    // Chemins web (navigateur : attributs src dans le HTML)
    /**
     * Retourne le chemin web vers le répertoire des fichiers audio.
     *
     * @return string Chemin web du répertoire audio.
     */
    public static function getAudioWebPath(): string {
        return 'audio/';
    }

    /**
     * Retourne le chemin web vers le répertoire des images de couverture.
     *
     * @return string Chemin web du répertoire des couvertures.
     */
    public static function getCoverWebPath(): string {
        return 'image/covers/';
    }
}