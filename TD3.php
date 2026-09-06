<?php

// 1
$playlist1 = [
    "nom" => "my personnal best of",
    "genre" => "rock",
    "createur" => "john doe",
    "date" => "28-08-2022",
    "nbpistes" => 56,
    "duree" => 10080,
];

// 2
/*
function display(array $playlist) : void {
    echo "playlist : $playlist[nom] ($playlist[genre])
par $playlist[createur] le $playlist[date]
$playlist[nbpistes] pistes pour une durée totale $playlist[duree]s\n";
}

display($playlist1);
echo "====================\n";
*/

// 3
$track1 = [
    "titre" => "my funny valentine",
    "artiste" => "chet bake",
    "album" => "My Funny Valentine",
    "annee" => 1954,
    "genre" => "classic jazz",
    "numero" => 1,
    "duree" => 187,
];

$track2 = [
    "titre" => "LA MALÉDICTION",
    "artiste" => "VIELUSOS",
    "album" => "LA MALÉDICTION",
    "annee" => 2024,
    "genre" => "Frenchcore / Hardcore",
    "numero" => 6,
    "duree" => 160,
];

$track3 = [
    "titre" => "Un monde à l'autre",
    "artiste" => "GP Explorer, GIMS, La Mano 1.9 & SCH",
    "album" => "Un monde à l'autre",
    "annee" => 2025,
    "genre" => "Rap",
    "numero" => 4,
    "duree" => 156,
];

// 4
/**
 * Fonction d'affichage d'une piste (choix entre 3 formats : court, complet, étendu)
 * @param array $track La piste à afficher
 * @param string $affichage Le format d'affichage (court, complet, étendu)
 */
function display_track(array $track, string $affichage = "court") : void {
    $fin = ")";
    $isNum = False;
    switch ($affichage) {
        case "étendu": {
            $fin = ", $track[annee]) - $track[duree]s : $track[genre]";
            $isNum = True;
            break;
        }
        case "complet": {
            $fin = ") - $track[duree]s";
            $isNum = True;
            break;
        }
        default: $fin = ")";
    };
    $debut = ($isNum) ? "$track[numero]-" : "";
    echo "$debut$track[titre] - by $track[artiste] (from $track[album]$fin\n";
}

display_track($track1, "court");
display_track($track1, "complet");
display_track($track1, "étendu");
echo "====================\n";

// 5
function play_track(array $track) {
    echo "$track[titre]\n";
    $res = "";
    for ($i = 1; $i <= $track["duree"]; $i++) {
        echo "$i.";
    }
    echo "\n";
}

play_track($track1);
echo "====================\n";

// 6
$playlist1["pistes"] = [ $track1 ];

// Le symbole & pour dire que le $playlist est par addresse et non par copie
function add_track(array &$playlist, array $track) : void {
    $playlist["pistes"][] = $track;
}

add_track($playlist1, $track2);
add_track($playlist1, $track3);

// 7
function display(array $playlist) : void {
    echo "playlist : $playlist[nom] ($playlist[genre])
par $playlist[createur] le $playlist[date]
$playlist[nbpistes] pistes pour une durée totale $playlist[duree]s\n";
    $i = 1;
    foreach($playlist["pistes"] as $track) {
        echo $i++ . " - ";
        display_track($track);
    }
}

display($playlist1);
echo "====================\n";

// 8
function play(array $playlist) : void {
    foreach($playlist["pistes"] as $track) {
        play_track($track);
    }
}

play($playlist1);
echo "====================\n";

// 10
function pl_shuffle(array &$playlist) : void {
    shuffle($playlist["pistes"]);
}

pl_shuffle($playlist1);
display($playlist1);