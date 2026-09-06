<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\tracks\AlbumTrack;

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
        $query = "INSERT INTO track (titre, duree, filename) VALUES (:title, :duration, :path)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['title' => $track->get('title'), 'duration' => $track->get('duration'), 'path' => $track->get('path')]);
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
                date_posdcast = :date
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
}