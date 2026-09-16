<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

/**
 * Service d'autorisation et de contrôle d'accès aux ressources.
 */
class Authz {
    /**
     * Vérifie si l'utilisateur actuellement connecté possède le rôle spécifié.
     *
     * @param int $role Valeur du rôle requis (ex: 100 pour administrateur, 1 pour standard).
     * @return bool True si l'utilisateur est connecté et possède le rôle requis, false sinon.
     */
    public static function checkRole(int $role) : bool {
        $user = [];
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return false;
        }
        return $user['role'] === $role;
    }

    /**
     * Vérifie si l'utilisateur connecté est propriétaire de la playlist spécifiée.
     *
     * @param int $idPlaylist Identifiant unique de la playlist.
     * @return bool True si l'utilisateur est autorisé à manipuler cette playlist, false sinon.
     */
    public static function checkPlaylistOwner(int $idPlaylist) : bool {
        $user = [];
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return false;
        }

        if ((int) $user['role'] === 100) return true;

        $r = DeefyRepository::getInstance();
        $listPlaylistId = $r->findPlaylistsIdsByUserId((int) $user['id']);

        if (!$listPlaylistId) return false;

        return in_array($idPlaylist, $listPlaylistId);
    }

    /**
     * Vérifie si l'utilisateur connecté est propriétaire de la piste audio spécifiée.
     *
     * @param int $idTrack Identifiant unique de la piste audio.
     * @return bool True si la piste appartient à l'utilisateur, false sinon.
     */
    public static function checkTrackOwner(int $idTrack) : bool {
        $user = [];
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return false;
        }

        if ((int) $user['role'] === 100) return true;

        $r = DeefyRepository::getInstance();
        $listTrackId = $r->findTracksIdsByUserId((int) $user['id']);

        if (!$listTrackId) return false;

        return in_array($idTrack, $listTrackId);
    }
}