<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\lists\AudioList;

class RendererFactory {
    public static function getRenderer(object $element) : ?Renderer {
        if ($element instanceof AlbumTrack) return new AlbumTrackRenderer($element);
        else if ($element instanceof PodcastTrack) return new PodcastTrackRenderer($element);
        else if ($element instanceof AudioList ) return new AudioListRenderer($element);
        else return null;
    }
}