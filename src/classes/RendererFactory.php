<?php

namespace iutnc\deefy\render;


require_once 'Renderer.php';
require_once 'AlbumTrackRenderer.php';
require_once 'PodcastTrackRenderer.php';
require_once 'AudioListRenderer.php';
use iutnc\deefy\audio\tracks\AlbumTrack;
require_once 'AlbumTrack.php';
use iutnc\deefy\audio\tracks\PodcastTrack;
require_once 'PodcastTrack.php';
use iutnc\deefy\audio\lists\AudioList;
require_once 'AudioList.php';

class RendererFactory {
    public static function getRenderer(object $element) : ?Renderer { // Le ? dans le retour pour précisé que ça peut être null
        if ($element instanceof AlbumTrack) return new AlbumTrackRenderer($element);
        else if ($element instanceof PodcastTrack) return new PodcastTrackRenderer($element);
        else if ($element instanceof AudioList ) return new AudioListRenderer($element);
        else return null;
    }
}