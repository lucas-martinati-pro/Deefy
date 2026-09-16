<?php

namespace iutnc\deefy\render;

use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\lists\AudioList;

/**
 * Factory pour l'instanciation du renderer approprié.
 */
class RendererFactory {
    /**
     * Instancie et retourne le renderer adéquat pour l'objet donné en argument.
     *
     * @param object $element L'objet à restituer en HTML (AlbumTrack, PodcastTrack ou AudioList).
     * @return Renderer|null Instance de Renderer adaptée, ou null si l'objet n'est pas pris en charge.
     */
    public static function getRenderer(object $element, ?int $playlistId = null) : ?Renderer {
        if ($element instanceof AlbumTrack) return new AlbumTrackRenderer($element, $playlistId);
        else if ($element instanceof PodcastTrack) return new PodcastTrackRenderer($element, $playlistId);
        else if ($element instanceof AudioList) return new AudioListRenderer($element);
        else return null;
    }
}