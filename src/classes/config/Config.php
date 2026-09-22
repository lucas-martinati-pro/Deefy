<?php

namespace iutnc\deefy\config;

class Config {
    public const ROOT_DIR = '..' . DIRECTORY_SEPARATOR;

    public static function getAudioDir(): string {
        return self::ROOT_DIR . 'audio' . DIRECTORY_SEPARATOR;
    }

    public static function getCoverDir(): string {
        return self::ROOT_DIR . 'image' . DIRECTORY_SEPARATOR . 'covers' . DIRECTORY_SEPARATOR;
    }

    // Chemins web (navigateur : attributs src dans le HTML)
    public static function getAudioWebPath(): string {
        return '../audio/';
    }

    public static function getCoverWebPath(): string {
        return '../image/covers/';
    }
}