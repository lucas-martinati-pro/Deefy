<?php

require_once  __DIR__ . '/src/vendor/autoload.php';

\iutnc\deefy\repository\DeefyRepository::setConfig(__DIR__ . '/src/config/deefy.db.ini');

$repo = \iutnc\deefy\repository\DeefyRepository::getInstance();

$playlists = $repo->findAllPlaylists();
foreach ($playlists as $pl) {
    print "playlist  : " . $pl->name . ":". $pl->id . "\n";
}


$pl = new \iutnc\deefy\audio\lists\PlayList('test');
$pl = $repo->saveEmptyPlaylist($pl);
print "playlist  : " . $pl->name . ":". $pl->id . "\n";
$track = new \iutnc\deefy\audio\tracks\PodcastTrack('test', 'test.mp3', 'auteur', '2021-01-01', 10, 'genre');
$track = $repo->savePodcastTrack($track);
print "track 2 : " . $track->get('title') . ":" . get_class($track) . "\n";
$repo->addTrackToPlaylist($pl->id, $track->get('id'));