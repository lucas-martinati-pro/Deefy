<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class DeefyReadRepository extends DeefyRepository {
    /**============================================
     *               GESTION DES PLAYLISTS
     *=============================================**/
    /**
     * @return Playlist[]
     */
    public function findAllPlaylists() : array {
        $query = <<<SQL
            SELECT *
            FROM playlist;
        SQL;
        $stmt = $this->pdo->prepare($query);
        $stmt->execute();
        // fetchAll pour récupéré toutes les lignes et le \PDO::FETCH_ASSOC sert à indexés par le nom des colonnes
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $playlist = [];
        foreach ($rows as $row) {
            $list = new Playlist($row['nom']);
            $list->set('id', $row['id']);
            $playlist[] = $list;
        }
        return $playlist;
    }

    public function findTrackById(int $id) : ?AudioTrack {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT *
            FROM track
            where id = :id
        SQL);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$res) return null;
        // Si ces 2 attributs sont null, alors il s'agit d'un album
        if ($res['auteur_podcast'] === null && $res['date_podcast'] === null) {
            $track = new AlbumTrack($res['titre'], $res['filename'], $res['titre_album'], (int) $res['numero_album']);
            $track->set('artist', $res['artiste_album']);
            $track->set('year', (int) $res['annee_album']);
        } else {
            $track = new PodcastTrack($res['titre'], $res['filename'], $res['auteur_podcast'], $res['date_podcast']);
        }

        $track->set('id', (int) $res['id']);
        $track->set('duration', (int) ($res['duree'] ?? 0));
        if (!empty($res['genre'])) {
            $track->set('genre', $res['genre']);
        }

        return $track;
    }

    public function findPlaylistById(int $idPlaylist) : ?Playlist {
        $stmtPlaylist = $this->pdo->prepare(<<<SQL
            SELECT *
            FROM playlist
            WHERE id = :id;
        SQL);
        $stmtPlaylist->execute(['id' => $idPlaylist]);
        $res = $stmtPlaylist->fetch(\PDO::FETCH_ASSOC);

        if (!$res) return null;

        $stmtTracks = $this->pdo->prepare(<<<SQL
            SELECT track.*
            FROM track
            INNER JOIN playlist2track ON track.id = playlist2track.id_track
            WHERE playlist2track.id_pl = :idPlaylist
            ORDER BY playlist2track.no_piste_dans_liste;
        SQL);
        $stmtTracks->execute(['idPlaylist' => $idPlaylist]);
        $rows = $stmtTracks->fetchAll(\PDO::FETCH_ASSOC);

        $tracks = [];
        foreach ($rows as $row) {
            $track = $this->findTrackById((int) $row['id']);
            if ($track !== null) {
                $tracks[] = $track;
            }
        }

        $playlist = new Playlist($res['nom'], $tracks);
        $playlist->set('id', (int) $res['id']);

        return $playlist;
    }

    public function findPlaylistsIdsByUserId(int $id) : array {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT id_pl
            from user2playlist
            where id_user = :id
        SQL);

        $stmt->execute(['id' => $id]);

        // FETCH_COLUMN pour récupérer directement la liste des identifiants de playlists
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * @return Playlist[]
     */
    public function findPlaylistsByUserId(int $idUser) : array {
        $idPlaylists = $this->findPlaylistsIdsByUserId($idUser);
        $playlists = [];

        foreach ($idPlaylists as $idPlaylist) {
            $playlist = $this->findPlaylistById((int) $idPlaylist);
            if ($playlist !== null) {
                $playlists[] = $playlist;
            }
        }

        return $playlists;
    }

    /**============================================
     *               GESTION DE L'AUTH
     *=============================================**/
    public function findByEmail(string $email) : ?array {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT id, email, passwd, role
            FROM User
            WHERE email = :email
        SQL);

        $stmt->execute(['email' => $email]);

        $res = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$res) return null;

        return $res;
    }

    public function existByEmail(string $email) : bool {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT id
            FROM User
            WHERE email = :email
        SQL);

        $stmt->execute(['email' => $email]);

        return $stmt->fetch() !== false;
    }
}