<?php

namespace iutnc\deefy\repository;

use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\audio\tracks\AudioTrack;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\exception\ConfigIOException;
use PDO;

/**
 * Repository centralisant les opérations d'accès à la base de données.
 */
class DeefyRepository {
    private PDO $pdo;

    /**
     * Instance unique du repository (patron Singleton).
     */
    private static ?DeefyRepository $instance = null;

    /**
     * Configuration de connexion à la base de données extraite du fichier .ini.
     */
    private static array $config = [];

    /**
     * Constructeur privé empêchant l'instanciation directe.
     */
    private function __construct() {
        $this->pdo = new PDO(self::$config['dsn'], self::$config['user'], self::$config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    /**
     * Définit la configuration de connexion à la base de données à partir d'un fichier .ini.
     *
     * @param string $file Chemin absolu vers le fichier de configuration INI.
     * @return void
     * @throws ConfigIOException Si le fichier INI ne peut pas être lu ou analysé.
     */
    public static function setConfig(string $file) : void {
        $conf = parse_ini_file($file);
        if ($conf === false) throw new ConfigIOException("Error reading configuration file");
        self::$config = [
            'dsn' => "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']}",
            'user' => $conf['username'],
            'pass' => $conf['password']
        ];
    }

    /**
     * Retourne l'instance unique du repository.
     *
     * @return DeefyRepository Instance du repository.
     */
    public static function getInstance() : DeefyRepository {
        if (is_null(self::$instance)) self::$instance = new DeefyRepository();
        return self::$instance;
    }

    /**========================================================================
     *                           READER
     *========================================================================**/

    /**============================================
     *               GESTION DES PLAYLISTS
     *=============================================**/
    /**
     * Récupère l'ensemble des playlists enregistrées en base de données.
     *
     * @return Playlist[] Liste de toutes les playlists.
     */
    public function findAllPlaylists() : array {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT *
            FROM playlist;
        SQL);
        $stmt->execute();
        // fetchAll pour récupéré toutes les lignes et le \PDO::FETCH_ASSOC sert à indexés par le nom des colonnes
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $playlists = [];
        foreach ($rows as $row) {
            $list = new Playlist($row['nom']);
            $list->id = $row['id'];
            $playlists[] = $list;
        }
        return $playlists;
    }

    /**
     * Recherche et retourne une piste audio par son identifiant.
     *
     * @param int $id Identifiant de la piste.
     * @return AudioTrack|null L'objet piste audio correspondant, ou null si introuvable.
     */
    public function findTrackById(int $id) : ?AudioTrack {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT *
            FROM track
            WHERE id = :id
        SQL);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$res) return null;

        return $this->rowToTrack($res);
    }

    /**
     * Recherche et retourne une playlist complète (avec ses pistes ordonnées) par son identifiant.
     *
     * @param int $idPlaylist Identifiant unique de la playlist.
     * @return Playlist|null L'objet Playlist avec toutes ses pistes, ou null si introuvable.
     */
    public function findPlaylistById(int $idPlaylist) : ?Playlist {
        $stmtPlaylist = $this->pdo->prepare(<<<SQL
            SELECT *
            FROM playlist
            WHERE id = :id;
        SQL);
        $stmtPlaylist->execute(['id' => $idPlaylist]);
        $res = $stmtPlaylist->fetch(PDO::FETCH_ASSOC);

        if (!$res) return null;

        $stmtTracks = $this->pdo->prepare(<<<SQL
            SELECT track.*
            FROM track
            INNER JOIN playlist2track ON playlist2track.id_track = track.id
            WHERE playlist2track.id_pl = :idPlaylist
            ORDER BY playlist2track.no_piste_dans_liste;
        SQL);
        $stmtTracks->execute(['idPlaylist' => $idPlaylist]);
        $rows = $stmtTracks->fetchAll(PDO::FETCH_ASSOC);

        $tracks = [];
        foreach ($rows as $row) {
            $tracks[] = $this->rowToTrack($row);
        }

        $playlist = new Playlist($res['nom'], $tracks);
        $playlist->id = (int) $res['id'];

        return $playlist;
    }

    /**
     * Crée une instance d'AudioTrack (AlbumTrack ou PodcastTrack) à partir d'une requête SQL.
     *
     * @param array $res Tableau associatif représentant la ligne de la table `track`.
     * @return AudioTrack Instance d'AlbumTrack ou de PodcastTrack selon le champ `type`.
     */
    private function rowToTrack(array $res) : AudioTrack {
        if ($res['type'] === 'A') {
            $track = new AlbumTrack(
                $res['titre'],
                $res['filename'],
                $res['titre_album'] ?? '',
                (!empty($res['numero_album'])) ? (int) $res['numero_album'] : 1
            );
            if (!empty($res['artiste_album'])) {
                $track->artist = $res['artiste_album'];
            }
            if (!empty($res['annee_album'])) {
                $track->year = (int) $res['annee_album'];
            }
        } else {
            $track = new PodcastTrack(
                $res['titre'],
                $res['filename'],
                $res['auteur_podcast'] ?? null,
                $res['date_podcast'] ?? null
            );
        }

        $track->id = (int) $res['id'];
        if (!empty($res['duree']) && (int) $res['duree'] > 0) {
            $track->duration = (int) $res['duree'];
        }
        if (!empty($res['genre'])) {
            $track->genre = $res['genre'];
        }
        if (!empty($res['image'])) {
            $track->image = $res['image'];
        }

        return $track;
    }

    /**
     * Récupère la liste des identifiants des playlists appartenant à un utilisateur.
     *
     * @param int $idUser Identifiant de l'utilisateur.
     * @return int[] Tableau des identifiants de playlists.
     */
    public function findPlaylistsIdsByUserId(int $idUser) : array {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT id_pl
            FROM user2playlist
            WHERE id_user = :id
        SQL);

        $stmt->execute(['id' => $idUser]);

        // FETCH_COLUMN pour récupérer directement la liste des identifiants de playlists
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Récupère la liste des identifiants des pistes d'un utilisateur dans la table `user2track`.
     *
     * @param int $idUser Identifiant de l'utilisateur.
     * @return int[] Liste des identifiants de pistes.
     */
    public function findTracksIdsByUserId(int $idUser) : array {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT id_track
            FROM user2track
            WHERE id_user = :id
        SQL);

        $stmt->execute(['id' => $idUser]);

        // FETCH_COLUMN pour récupérer directement la liste des identifiants de playlists
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Récupère toutes les playlists complètes d'un utilisateur donné.
     *
     * @param int $idUser Identifiant de l'utilisateur.
     * @return Playlist[] Tableau des playlists de l'utilisateur.
     */
    public function findPlaylistsByUserId(int $idUser) : array {
        $idPlaylists = $this->findPlaylistsIdsByUserId($idUser);
        $playlists = [];

        foreach ($idPlaylists as $idPlaylist) {
            $playlist = $this->findPlaylistById($idPlaylist);
            if ($playlist !== null) {
                $playlists[] = $playlist;
            }
        }

        return $playlists;
    }

    /**
     * Récupère toutes les pistes audio appartenant à un utilisateur.
     *
     * @param int $idUser Identifiant de l'utilisateur.
     * @return AudioTrack[] Tableau des pistes audio.
     */
    public function findTracksByUserId(int $idUser) : array {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT DISTINCT track.*
            FROM track
            INNER JOIN user2track ON user2track.id_track = track.id
            WHERE user2track.id_user = :id
        SQL);

        $stmt->execute(['id' => $idUser]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tracks = [];
        foreach ($rows as $row) {
            $tracks[] = $this->rowToTrack($row);
        }

        return $tracks;
    }

    /**============================================
     *               GESTION DE L'AUTH
     *=============================================**/

    /**
     * Recherche un utilisateur par son adresse email.
     *
     * @param string $email Adresse email recherchée.
     * @return array|null Données de l'utilisateur ou null si introuvable.
     */
    public function findUserByEmail(string $email) : ?array {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT id, email, passwd, role
            FROM User
            WHERE email = :email
        SQL);

        $stmt->execute(['email' => $email]);

        $res = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$res) return null;

        return $res;
    }

    /**
     * Vérifie si un compte utilisateur existe déjà avec l'adresse email spécifiée.
     *
     * @param string $email Adresse email à vérifier.
     * @return bool True si l'email existe déjà, false sinon.
     */
    public function existsByEmail(string $email) : bool {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT id
            FROM User
            WHERE email = :email
        SQL);

        $stmt->execute(['email' => $email]);

        return $stmt->fetch() !== false;
    }

    /**========================================================================
     *                           WRITER
     *========================================================================**/

    /**============================================
     *               GESTION DES PLAYLISTS
     *=============================================**/

    /**
     * Enregistre une nouvelle playlist vide en base de données et lui affecte son identifiant généré.
     *
     * @param Playlist $playlist Instance de la playlist à insérer.
     * @return Playlist La playlist avec son identifiant (id) mis à jour.
     */
    public function saveEmptyPlaylist(Playlist $playlist) : Playlist {
        $stmt = $this->pdo->prepare(<<<SQL
            INSERT INTO playlist (nom)
            VALUES (:name)
        SQL);
        $stmt->execute(['name' => $playlist->name]);
        $playlist->id = $this->pdo->lastInsertId();
        return $playlist;
    }

    /**
     * Enregistre une piste audio (morceau d'album ou podcast) dans la table `track`.
     *
     * @param AudioTrack $track Piste audio à persister.
     * @return AudioTrack La piste avec son identifiant généré.
     */
    public function saveAudioTrack(AudioTrack $track) : AudioTrack {
        $genreVal = $track->genre;
        $genre = (!empty($genreVal) && trim($genreVal) !== '') ? trim($genreVal) : null;

        $durationVal = $track->duration;
        $duree = ($durationVal > 0) ? $durationVal : null;

        $imageVal = $track->image;
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
            $artistVal = $track->artist;
            $artist = (!empty($artistVal) && trim($artistVal) !== '') ? trim($artistVal) : null;

            $albumVal = $track->album;
            $album = (!empty($albumVal) && trim($albumVal) !== '') ? trim($albumVal) : null;

            $yearVal = $track->year;
            $year = (!empty($yearVal) && (int) $yearVal > 0) ? (int) $yearVal : null;

            $trackNumVal = $track->trackNumber;
            $trackNumber = (!empty($trackNumVal) && (int) $trackNumVal > 0) ? (int) $trackNumVal : null;
        } elseif ($track instanceof PodcastTrack) {
            $type = 'P';
            $authorVal = $track->author;
            $author = (!empty($authorVal) && trim($authorVal) !== '') ? trim($authorVal) : null;

            $dateVal = $track->date;
            $date = (!empty($dateVal) && trim($dateVal) !== '') ? trim($dateVal) : null;
        }

        $stmt = $this->pdo->prepare(<<<SQL
            INSERT INTO track (
                titre, genre, duree, filename, type, image,
                artiste_album, titre_album, annee_album, numero_album,
                auteur_podcast, date_podcast
            ) VALUES (
                :title, :genre, :duree, :filename, :type, :image,
                :artist, :album, :year, :trackNumber,
                :author, :date
            )
        SQL);
        $stmt->execute([
            'title' => $track->title,
            'genre' => $genre,
            'duree' => $duree,
            'filename' => $track->filename,
            'type' => $type,
            'image' => $image,
            'artist' => $artist,
            'album' => $album,
            'year' => $year,
            'trackNumber' => $trackNumber,
            'author' => $author,
            'date' => $date
        ]);

        $track->id = (int) $this->pdo->lastInsertId();
        return $track;
    }

    /**
     * Associe une piste audio à un utilisateur.
     *
     * @param int $idUser Identifiant de l'utilisateur.
     * @param int $idTrack Identifiant de la piste audio.
     * @return void
     */
    public function saveTrack2User(int $idUser, int $idTrack) : void {
        $stmt = $this->pdo->prepare(<<<SQL
            INSERT INTO user2track (id_user, id_track)
            VALUES (:idUser, :idTrack)
        SQL);
        $stmt->execute(['idUser' => $idUser, 'idTrack' => $idTrack]);
    }

    /**
     * Vérifie si une piste audio est déjà associée à une playlist.
     *
     * @param int $idPlaylist Identifiant de la playlist.
     * @param int $idTrack Identifiant de la piste audio.
     * @return bool True si la piste est déjà dans la playlist, false sinon.
     */
    public function isTrackInPlaylist(int $idPlaylist, int $idTrack) : bool {
        $stmt = $this->pdo->prepare(<<<SQL
            SELECT COUNT(*)
            FROM playlist2track
            WHERE id_pl = :idPlaylist AND id_track = :idTrack
        SQL);
        $stmt->execute(['idPlaylist' => $idPlaylist, 'idTrack' => $idTrack]);
        return ((int) $stmt->fetchColumn()) > 0;
    }

    /**
     * Associe une piste audio à une playlist en calculant son numéro d'ordre dans la liste.
     *
     * @param int $idPlaylist Identifiant de la playlist.
     * @param int $idTrack Identifiant de la piste audio à associer.
     * @return void
     */
    public function addTrackToPlaylist(int $idPlaylist, int $idTrack) : void {
        if ($this->isTrackInPlaylist($idPlaylist, $idTrack)) {
            return;
        }

        $stmt = $this->pdo->prepare(<<<SQL
            SELECT COALESCE(MAX(no_piste_dans_liste), 0) + 1
            FROM playlist2track
            WHERE id_pl = :idPlaylist
        SQL);

        $stmt->execute(['idPlaylist' => $idPlaylist]);

        $noPiste = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare(<<<SQL
            INSERT INTO playlist2track (id_pl, id_track, no_piste_dans_liste)
            VALUES (:idPlaylist, :idTrack, :noPiste)
        SQL);
        $stmt->execute(['idPlaylist' => $idPlaylist, 'idTrack' => $idTrack, 'noPiste' => $noPiste]);
    }

    /**
     * Associe une playlist à un utilisateur propriétaire dans la table de liaison `user2playlist`.
     *
     * @param int $idUser Identifiant de l'utilisateur.
     * @param int $idPlaylist Identifiant de la playlist.
     * @return void
     */
    public function savePlaylist2User(int $idUser, int $idPlaylist) : void {
        // Le IGNGORE INTO pour ignorer si le track est déjà présent dans la playlist (doublon)
        $stmt = $this->pdo->prepare(<<<SQL
            INSERT IGNORE INTO user2playlist (id_user, id_pl)
            VALUES (:idUser, :idPlaylist)
        SQL);
        $stmt->execute([
            'idUser' => $idUser,
            'idPlaylist' => $idPlaylist
        ]);
    }

    /**
     * Retire une piste audio d'une playlist spécifique dans la table de liaison `playlist2track`.
     *
     * @param int $idPlaylist Identifiant de la playlist.
     * @param int $idTrack Identifiant de la piste audio à retirer.
     * @return void
     */
    public function removeTrackFromPlaylist(int $idPlaylist, int $idTrack) : void {
        // Supprimer les références de ce morceau dans toutes les playlists
        $stmt1 = $this->pdo->prepare(<<<SQL
            DELETE FROM playlist2track
            WHERE id_track = :idTrack
            AND id_pl = :idPlaylist
        SQL);
        $stmt1->execute(['idTrack' => $idTrack, 'idPlaylist' => $idPlaylist]);
    }

    /**
     * Supprime définitivement une piste audio de la table `track` ainsi que de `playlist2track` et `user2track`.
     *
     * @param int $idTrack Identifiant de la piste audio à supprimer.
     * @return void
     */
    public function deleteTrackById(int $idTrack) : void {
        // Supprimer les références de ce morceau dans toutes les playlists
        $stmt1 = $this->pdo->prepare(<<<SQL
            DELETE FROM playlist2track
            WHERE id_track = :idTrack
        SQL);
        $stmt1->execute(['idTrack' => $idTrack]);

        // Supprimer le track de l'utilisateur
        $stmt1 = $this->pdo->prepare(<<<SQL
            DELETE FROM user2track
            WHERE id_track = :idTrack
        SQL);
        $stmt1->execute(['idTrack' => $idTrack]);

        // Supprimer la piste dans la table track
        $stmt2 = $this->pdo->prepare(<<<SQL
            DELETE FROM track
            WHERE id = :idTrack
        SQL);
        $stmt2->execute(['idTrack' => $idTrack]);
    }

    /**
     * Supprime une playlist complète ainsi que ses pistes associées et les liaisons utilisateurs en cascade.
     *
     * @param int $idPlaylist Identifiant de la playlist à supprimer.
     * @return void
     */
    public function deletePlaylistById(int $idPlaylist) : void {
        // Supprimer les tracks de cette playlist :
        $stmt1 = $this->pdo->prepare(<<<SQL
            SELECT track.id
            FROM track
            INNER JOIN playlist2track ON playlist2track.id_track = track.id
            WHERE id_pl = :id
        SQL);

        $stmt1->execute(['id' => $idPlaylist]);

        $idTracks = $stmt1->fetchAll(PDO::FETCH_COLUMN);

        foreach ($idTracks as $id) {
            $this->deleteTrackById($id);
        }

        // Supprimer l'association entre la playlist et les utilisateurs
        $stmt2 = $this->pdo->prepare(<<<SQL
            DELETE FROM user2playlist
            WHERE id_pl = :idPlaylist
        SQL);
        $stmt2->execute(['idPlaylist' => $idPlaylist]);

        // Supprimer les associations des pistes liées à cette playlist
        $stmt3 = $this->pdo->prepare(<<<SQL
            DELETE FROM playlist2track
            WHERE id_pl = :idPlaylist
        SQL);
        $stmt3->execute(['idPlaylist' => $idPlaylist]);

        // Supprimer définitivement la playlist de la table playlist
        $stmt4 = $this->pdo->prepare(<<<SQL
            DELETE FROM playlist
            WHERE id = :idPlaylist
        SQL);
        $stmt4->execute(['idPlaylist' => $idPlaylist]);
    }

    /**============================================
     *               GESTION DE L'AUTH
     *=============================================**/

    /**
     * Crée un nouvel utilisateur en base de données avec un mot de passe haché (bcrypt).
     *
     * @param string $email Adresse email nettoyée de l'utilisateur.
     * @param string $password Mot de passe en clair à hacher.
     * @return void
     */
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