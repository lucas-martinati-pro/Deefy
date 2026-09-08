<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;

class DeefyWriteRepository extends DeefyRepository {
    /**============================================
     *               GESTION DES PLAYLISTS
     *=============================================**/
    public function saveEmptyPlaylist(Playlist $playlist) : Playlist {
        $query = "INSERT INTO playlist (nom) VALUES (:name)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['name' => $playlist->name]);
        $playlist->set('id', $this->pdo->lastInsertId());
        return $playlist;
    }

    public function saveAudioTrack(AudioTrack $track) : AudioTrack {
        $query = "INSERT INTO track (titre, duree, filename) VALUES (:title, :duration, :filename)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['title' => $track->get('title'), 'duration' => $track->get('duration'), 'filename' => $track->get('filename')]);
        $track->set('id', $this->pdo->lastInsertId());
        if ($track instanceof AlbumTrack) {
            $query = <<<SQL
                UPDATE track SET
                artiste_album = :artist,
                titre_album = :album,
                annee_album = :year,
                numero_album = :trackNumber
            WHERE ID = :id
            SQL;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'artist' => $track->get('artist'),
                'album' => $track->get('album'),
                'year' => $track->get('year'),
                'trackNumber' => $track->get('trackNumber'),
                'id' => $track->get('id')
            ]);
        } elseif ($track instanceof PodcastTrack) {
            $query = <<<SQL
                UPDATE track SET
                auteur_podcast = :author,
                date_podcast = :date
            WHERE ID = :id
            SQL;
            $stmt = $this->pdo->prepare($query);
            $stmt->execute([
                'author' => $track->get('author'),
                'date' => $track->get('date'),
                'id' => $track->get('id')
            ]);
        }
        return $track;
    }

    public function savePodcastTrack(PodcastTrack $track) : PodcastTrack {
        return $this->saveAudioTrack($track);
    }

    public function addTrackToPlaylist(int $idPlaylist, int $idTrack) : void {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1
            FROM playlist2track
            WHERE id_pl = :idPlaylist
        SQL);

        $stmt->execute(['idPlaylist' => $idPlaylist]);

        $noPiste = $stmt->fetchColumn();

        $query = "INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste) VALUES (:idPlaylist, :idTrack, :noPiste)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['idPlaylist' => $idPlaylist, 'idTrack' => $idTrack, 'noPiste' => $noPiste]);
    }

    public function savePlaylist2User(int $idUser, int $idPlaylist) : void {
        $stmt = $this->pdo->prepare(<<< SQL
            SELECT 1
            FROM User
            where id = :id
        SQL);
        $stmt->execute(['id' => $idUser]);
        if (!$stmt->fetch()) return;

        $stmt = $this->pdo->prepare(<<< SQL
            SELECT 1
            FROM playlist
            where id = :id
        SQL);
        $stmt->execute(['id' => $idPlaylist]);
        if (!$stmt->fetch()) return;

        $stmt = $this->pdo->prepare(<<< SQL
            INSERT INTO user2playlist (id_user, id_pl) VALUES (:idUser, :idPlaylist)
        SQL);
        $stmt->execute(['idUser' => $idUser, 'idPlaylist' => $idPlaylist]);
    }

    /**============================================
     *               GESTION DE L'AUTH
     *=============================================**/
    public function addUser(string $email, string $password) : void {
        // Double par précaution;
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);

        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->pdo->prepare(<<<SQL
            INSERT INTO User (email, passwd, role) VALUES (:email, :password, 1)
        SQL);

        $stmt->execute(['email' => $email, 'password' => $password]);
    }
}