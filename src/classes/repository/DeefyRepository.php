<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\tracks\AlbumTrack;

// $r = DeefyRepository::getInstance();
// $pl = $r->findPlaylistById( $id );

class DeefyRepository {
    private \PDO $pdo;
    private static ?DeefyRepository $instance = null;
    private static array $config = [];

    private function __construct() {
        $this->pdo = new \PDO(self::$config['dsn'], self::$config['user'], self::$config['pass'],
        [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]);
    }

    public static function setConfig(string $file) : void {
        $conf = parse_ini_file($file);
        if ($conf === false) throw new \Exception("Error reading configuration file");
        self::$config = [
            'dsn' => "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']}",
            'user' => $conf['username'],
            'pass' => $conf['password']
        ];
    }

    public static function getInstance() : DeefyRepository {
        if (is_null(self::$instance)) self::$instance = new DeefyRepository();
        return self::$instance;
    }

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

    public function saveEmptyPlaylist(Playlist $playlist) : Playlist {
        $query = "INSERT INTO playlist (nom) VALUES (:name)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['name' => $playlist->name]);
        $playlist->set('id', $this->pdo->lastInsertId());
        return $playlist;
    }

    public function saveAudioTrack(AudioTrack $track) : AudioTrack {
        $genreVal = $track->get('genre');
        $genre = (!empty($genreVal) && trim($genreVal) !== '') ? trim($genreVal) : null;

        $durationVal = (int) $track->get('duration');
        $duree = ($durationVal > 0) ? $durationVal : null;

        $imageVal = $track->get('image');
        $image = (!empty($imageVal)) ? $imageVal : null;

        $type = null;
        $artist = null;
        $album = null;
        $year = null;
        $trackNumber = null;
        $author = null;
        $date = null;

        if ($track instanceof AlbumTrack) {
            $type = 'A';
            $artistVal = $track->get('artist');
            $artist = (!empty($artistVal) && trim($artistVal) !== '') ? trim($artistVal) : null;

            $albumVal = $track->get('album');
            $album = (!empty($albumVal) && trim($albumVal) !== '') ? trim($albumVal) : null;

            $yearVal = $track->get('year');
            $year = (!empty($yearVal) && (int) $yearVal > 0) ? (int) $yearVal : null;

            $trackNumVal = $track->get('trackNumber');
            $trackNumber = (!empty($trackNumVal) && (int) $trackNumVal > 0) ? (int) $trackNumVal : null;
        } elseif ($track instanceof PodcastTrack) {
            $type = 'P';
            $authorVal = $track->get('author');
            $author = (!empty($authorVal) && trim($authorVal) !== '') ? trim($authorVal) : null;

            $dateVal = $track->get('date');
            $date = (!empty($dateVal) && trim($dateVal) !== '') ? trim($dateVal) : null;
        }

        $query = <<<SQL
            INSERT INTO track (
                titre, genre, duree, filename, type, image,
                artiste_album, titre_album, annee_album, numero_album,
                auteur_podcast, date_podcast
            ) VALUES (
                :title, :genre, :duree, :filename, :type, :image,
                :artist, :album, :year, :trackNumber,
                :author, :date
            )
        SQL;

        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'title' => $track->get('title'),
            'genre' => $genre,
            'duree' => $duree,
            'filename' => $track->get('filename'),
            'type' => $type,
            'image' => $image,
            'artist' => $artist,
            'album' => $album,
            'year' => $year,
            'trackNumber' => $trackNumber,
            'author' => $author,
            'date' => $date
        ]);

        $track->set('id', (int) $this->pdo->lastInsertId());
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

    public function findTrackById(int $id) : ?AudioTrack {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT *
            FROM track
            WHERE id = :id
        SQL);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$res) return null;

        if ($res['type'] === 'A') {
            $track = new AlbumTrack(
                $res['titre'],
                $res['filename'],
                $res['titre_album'] ?? '',
                (!empty($res['numero_album'])) ? (int) $res['numero_album'] : 1
            );
            if (!empty($res['artiste_album'])) {
                $track->set('artist', $res['artiste_album']);
            }
            if (!empty($res['annee_album'])) {
                $track->set('year', (int) $res['annee_album']);
            }
        } else {
            $track = new PodcastTrack(
                $res['titre'],
                $res['filename'],
                $res['auteur_podcast'] ?? null,
                $res['date_podcast'] ?? null
            );
        }

        $track->set('id', (int) $res['id']);
        if (!empty($res['duree']) && (int) $res['duree'] > 0) {
            $track->set('duration', (int) $res['duree']);
        }
        if (!empty($res['genre'])) {
            $track->set('genre', $res['genre']);
        }
        if (!empty($res['image'])) {
            $track->set('image', $res['image']);
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