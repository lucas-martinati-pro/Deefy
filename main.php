<?php

require_once 'AlbumTrack.php';
require_once 'AlbumTrackRenderer.php';
require_once 'PodcastTrack.php';
require_once 'PodcastTrackRenderer.php';

// Création de 2 tracks
$track1 = new AlbumTrack("I'm With You", "audio/01-Im_with_you_BB-King-Lucille.mp3", "Lucille", 5);
$track1->artist = "B.B. King";
$track1->year = 1968;
$track1->genre = "Blues";
$track1->duration = 151;

$track2 = new AlbumTrack("I Need Your Love", "audio/02-I_Need_Your_Love-BB_King-Lucille.mp3", "Lucille", 7);
$track2->artist = "B.B. King";
$track2->year = 1968;
$track2->genre = "Blues";
$track2->duration = 142;

echo "{$track1->title} - {$track1->path}\n";
print "{$track1->title} - {$track1->path}\n";
printf("%s - %s\n", $track1->title, $track1->path);

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
echo $render1->render(1);
echo $render2->render(2);


// Création d'un podcast (avec le 3ème fichier audio)
$podcast = new PodcastTrack("Country Girl", "audio/03-Country_Girl-BB_King-Lucille.mp3");
$podcast->author = "B.B. King";
$podcast->date = "1968-12-01";
$podcast->genre = "Blues";
$podcast->duration = 185;

echo "\n<hr>====================<br>\n";
print_r($podcast);
echo "\n<hr>====================<br>\n";
$renderPodcast = new PodcastTrackRenderer($podcast);
echo $renderPodcast->render(1);
echo $renderPodcast->render(2);