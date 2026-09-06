<?php

namespace iutnc\deefy\auth;

use iutnc\deefy\exception\AuthnException;
use iutnc\deefy\repository\DeefyRepository;

class Authz {
    public static function checkRole(int $role) : bool {
        $user = [];
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return false;
        }
        return $user['role'] === $role;
    }

    public static function checkPlaylistOwner(int $idPlaylist) : bool {
        $user = [];
        try {
            $user = AuthnProvider::getSignedInUser();
        } catch (AuthnException $e) {
            return false;
        }

        if ((int) $user['role'] === 100) return true;

        $r = DeefyRepository::getInstance();
        $listPlaylistId = $r->findPlaylistIdsByUserId((int) $user['id']);

        if (!$listPlaylistId) return false;

        return in_array($idPlaylist, $listPlaylistId);
    }
}