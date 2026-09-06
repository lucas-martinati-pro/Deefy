<?php

// use loader\Psr4ClassLoader;
// require_once 'loader/Psr4ClassLoader.php';
require_once 'vendor/autoload.php';
use iutnc\deefy\audio\tracks\AlbumTrack;
use iutnc\deefy\render\AlbumTrackRenderer;
use iutnc\deefy\audio\tracks\PodcastTrack;
use iutnc\deefy\render\PodcastTrackRenderer;
use iutnc\deefy\audio\lists\Album;
use iutnc\deefy\audio\lists\Playlist;
use iutnc\deefy\render\AudioListRenderer;
use iutnc\deefy\render\Renderer;
use iutnc\deefy\exception\InvalidPropertyNameException;
use iutnc\deefy\exception\InvalidPropertyValueException;

// $loader = new Psr4ClassLoader("iutnc\\deefy\\", __DIR__ . "/classes");
// $loader->register();

echo "<hr><h1>=== 1. Test Pistes Audio (AlbumTrack & PodcastTrack) ===</h1>\n";

$track1 = new AlbumTrack("I'm With You", "../audio/01-Im_with_you_BB-King-Lucille.mp3", "Lucille", 5);
$track1->set("artist", "B.B. King");
$track1->set("year", 1968);
$track1->set("genre", "Blues");
$track1->set("duration", 151);

$track2 = new AlbumTrack("I Need Your Love", "../audio/02-I_Need_Your_Love-BB_King-Lucille.mp3", "Lucille", 7);
$track2->set("artist", "B.B. King");
$track2->set("year", 1968);
$track2->set("genre", "Blues");
$track2->set("duration", 142);

echo "{$track1->get("title")} - {$track1->get("path")}\n";
print "{$track1->get("title")} - {$track1->get("path")}\n";
printf("%s - %s\n", $track1->get("title"), $track1->get("path"));

echo "\n<hr>====================<br>\n";
echo $track1->__toString();
echo "\n<hr>====================<br>\n";
echo $track1;
echo "\n<hr>====================<br>\n";
print_r($track1);
echo "\n<hr>====================<br>\n";
var_dump($track1);

$render1 = new AlbumTrackRenderer($track1);
$render2 = new AlbumTrackRenderer($track2);
echo $render1->render(Renderer::COMPACT);
echo $render2->render(Renderer::LONG);

// Création d'un podcast
$podcast = new PodcastTrack("Country Girl", "../audio/03-Country_Girl-BB_King-Lucille.mp3");
$podcast->set("author", "B.B. King");
$podcast->set("date", "1968-12-01");
$podcast->set("genre", "Blues");
$podcast->set("duration", 185);

echo "\n<hr>====================<br>\n";
print_r($podcast);
echo "\n<hr>====================<br>\n";
$renderPodcast = new PodcastTrackRenderer($podcast);
echo $renderPodcast->render(Renderer::COMPACT);
echo $renderPodcast->render(Renderer::LONG);

echo "\n<hr><h1>=== 2. Test Exceptions (Exercice 2) ===</h1>\n";

// Test d'une mauvaise valeur de durée (< 0)
try {
    echo "Test durée négative (-50s)\n";
    $track1->set("duration", -50);
} catch (InvalidPropertyValueException $e) {
    echo "Exception capturée avec succès : " . $e->getMessage() . "\n";
}

// Test d'une propriété inconnue ou non modifiable
try {
    echo "Test modification d'une propriété non autorisée (title)\n";
    $track1->set("title", "Nouveau Titre");
} catch (InvalidPropertyNameException $e) {
    echo "Exception capturée avec succès : " . $e->getMessage() . "\n";
}

echo "<hr><h1>=== 3. Test Album & Playlist (Exercice 3) ===</h1>\n";

// Test Album
$album = new Album("Lucille", [$track1, $track2]);
$album->set("artist", "B.B. King");
$album->set("date", "1968-12-01");
echo "Album créé : {$album->name} | Nb pistes : {$album->trackCount} | Durée : {$album->totalDuration}s\n";

// Test Playlist
$playlist = new Playlist("Ma Playlist Blues");
$playlist->addPiste($track1);
$playlist->addPiste($podcast);
echo "Playlist après 2 ajouts : {$playlist->trackCount} pistes, {$playlist->totalDuration}s\n";

// Ajout avec doublon (track1 est déjà présent)
$playlist->addPistes([$track1, $track2]);
echo "Playlist après addPistes : {$playlist->trackCount} pistes, {$playlist->totalDuration}s\n";

// Suppression d'une piste à l'indice 0
$playlist->removePiste(0);
echo "Playlist après suppression index 0 : {$playlist->trackCount} pistes, {$playlist->totalDuration}s\n\n";

echo "<hr><h1>=== 4. Test AudioListRenderer (Exercice 4) ===</h1>\n";

$rendererAlbum = new AudioListRenderer($album);
echo $rendererAlbum->render();

echo "\n";
$rendererPlaylist = new AudioListRenderer($playlist);
echo $rendererPlaylist->render();