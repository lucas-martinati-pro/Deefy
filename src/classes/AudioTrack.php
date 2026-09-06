<?php

namespace iutnc\deefy\audio\tracks;

use iutnc\deefy\exception\InvalidPropertyNameException;
require_once 'InvalidPropertyNameException.php';
use iutnc\deefy\exception\InvalidPropertyValueException;
require_once 'InvalidPropertyValueException.php';

class AudioTrack {
    // Genres standards ID3v1 (0 à 79)
    public const int BLUES = 0;
    public const int CLASSIC_ROCK = 1;
    public const int COUNTRY = 2;
    public const int DANCE = 3;
    public const int DISCO = 4;
    public const int FUNK = 5;
    public const int GRUNGE = 6;
    public const int HIP_HOP = 7;
    public const int JAZZ = 8;
    public const int METAL = 9;
    public const int NEW_AGE = 10;
    public const int OLDIES = 11;
    public const int OTHER = 12;
    public const int POP = 13;
    public const int RNB = 14;
    public const int RAP = 15;
    public const int REGGAE = 16;
    public const int ROCK = 17;
    public const int TECHNO = 18;
    public const int INDUSTRIAL = 19;
    public const int ALTERNATIVE = 20;
    public const int SKA = 21;
    public const int DEATH_METAL = 22;
    public const int PRANKS = 23;
    public const int SOUNDTRACK = 24;
    public const int EURO_TECHNO = 25;
    public const int AMBIENT = 26;
    public const int TRIP_HOP = 27;
    public const int VOCAL = 28;
    public const int JAZZ_FUNK = 29;
    public const int FUSION = 30;
    public const int TRANCE = 31;
    public const int CLASSICAL = 32;
    public const int INSTRUMENTAL = 33;
    public const int ACID = 34;
    public const int HOUSE = 35;
    public const int GAME = 36;
    public const int SOUND_CLIP = 37;
    public const int GOSPEL = 38;
    public const int NOISE = 39;
    public const int ALTERNATIVE_ROCK = 40;
    public const int BASS = 41;
    public const int SOUL = 42;
    public const int PUNK = 43;
    public const int SPACE = 44;
    public const int MEDITATIVE = 45;
    public const int INSTRUMENTAL_POP = 46;
    public const int INSTRUMENTAL_ROCK = 47;
    public const int ETHNIC = 48;
    public const int GOTHIC = 49;
    public const int DARKWAVE = 50;
    public const int TECHNO_INDUSTRIAL = 51;
    public const int ELECTRONIC = 52;
    public const int POP_FOLK = 53;
    public const int EURODANCE = 54;
    public const int DREAM = 55;
    public const int SOUTHERN_ROCK = 56;
    public const int COMEDY = 57;
    public const int CULT = 58;
    public const int GANGSTA = 59;
    public const int TOP_40 = 60;
    public const int CHRISTIAN_RAP = 61;
    public const int POP_FUNK = 62;
    public const int JUNGLE = 63;
    public const int NATIVE_AMERICAN = 64;
    public const int CABARET = 65;
    public const int NEW_WAVE = 66;
    public const int PSYCHEDELIC = 67;
    public const int RAVE = 68;
    public const int SHOWTUNES = 69;
    public const int TRAILER = 70;
    public const int LO_FI = 71;
    public const int TRIBAL = 72;
    public const int ACID_PUNK = 73;
    public const int ACID_JAZZ = 74;
    public const int POLKA = 75;
    public const int RETRO = 76;
    public const int THEATRE = 77;
    public const int ROCK_AND_ROLL = 78;
    public const int HARD_ROCK = 79;

    // Extensions Winamp (80 à 125)
    public const int FOLK = 80;
    public const int FOLK_ROCK = 81;
    public const int NATIONAL_FOLK = 82;
    public const int SWING = 83;
    public const int FAST_FUSION = 84;
    public const int BEBOP = 85;
    public const int LATIN = 86;
    public const int REVIVAL = 87;
    public const int CELTIC = 88;
    public const int BLUEGRASS = 89;
    public const int AVANTGARDE = 90;
    public const int GOTHIC_ROCK = 91;
    public const int PROGRESSIVE_ROCK = 92;
    public const int PSYCHEDELIC_ROCK = 93;
    public const int SYMPHONIC_ROCK = 94;
    public const int SLOW_ROCK = 95;
    public const int BIG_BAND = 96;
    public const int CHORUS = 97;
    public const int EASY_LISTENING = 98;
    public const int ACOUSTIC = 99;
    public const int HUMOUR = 100;
    public const int SPEECH = 101;
    public const int CHANSON = 102;
    public const int OPERA = 103;
    public const int CHAMBER_MUSIC = 104;
    public const int SONATA = 105;
    public const int SYMPHONY = 106;
    public const int BOOTY_BASS = 107;
    public const int PRIMUS = 108;
    public const int PORN_GROOVE = 109;
    public const int SATIRE = 110;
    public const int SLOW_JAM = 111;
    public const int CLUB = 112;
    public const int TANGO = 113;
    public const int SAMBA = 114;
    public const int FOLKLORE = 115;
    public const int BALLAD = 116;
    public const int POWER_BALLAD = 117;
    public const int RHYTHMIC_SOUL = 118;
    public const int FREESTYLE = 119;
    public const int DUET = 120;
    public const int PUNK_ROCK = 121;
    public const int DRUM_SOLO = 122;
    public const int A_CAPPELLA = 123;
    public const int EURO_HOUSE = 124;
    public const int DANCEHALL = 125;

    protected string $title, $path;
    // duration en secondes
    protected int $duration, $genre;

    public function get(string $name) : mixed {
        if (property_exists($this, $name)) return $this->$name;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    public function set(string $name, mixed $value) : void {
        if (property_exists($this, $name) && ($name !== "title" && $name !== "path"))
            if ($name === "duration" && $value < 0) {
                throw new InvalidPropertyValueException("$name : invalid value ($value)");
            } else $this->$name = $value;
        else throw new InvalidPropertyNameException("$name : invalid property");
    }

    public function __construct(string $title, string $path) {
        $this->title = $title;
        $this->path = $path;
    }

    // Utiliser json_encore() et get_onject_vars()
    public function __toString() : string {
        return json_encode(get_object_vars($this));
    }

    public static function getGenreAsString(int $genre) : String {
        return match ($genre) {
    AudioTrack::BLUES => 'Blues',
    AudioTrack::CLASSIC_ROCK => 'Classic Rock',
    AudioTrack::COUNTRY => 'Country',
    AudioTrack::DANCE => 'Dance',
    AudioTrack::DISCO => 'Disco',
    AudioTrack::FUNK => 'Funk',
    AudioTrack::GRUNGE => 'Grunge',
    AudioTrack::HIP_HOP => 'Hip-Hop',
    AudioTrack::JAZZ => 'Jazz',
    AudioTrack::METAL => 'Metal',
    AudioTrack::NEW_AGE => 'New Age',
    AudioTrack::OLDIES => 'Oldies',
    AudioTrack::OTHER => 'Other',
    AudioTrack::POP => 'Pop',
    AudioTrack::RNB => 'R&B',
    AudioTrack::RAP => 'Rap',
    AudioTrack::REGGAE => 'Reggae',
    AudioTrack::ROCK => 'Rock',
    AudioTrack::TECHNO => 'Techno',
    AudioTrack::INDUSTRIAL => 'Industrial',
    AudioTrack::ALTERNATIVE => 'Alternative',
    AudioTrack::SKA => 'Ska',
    AudioTrack::DEATH_METAL => 'Death Metal',
    AudioTrack::PRANKS => 'Pranks',
    AudioTrack::SOUNDTRACK => 'Soundtrack',
    AudioTrack::EURO_TECHNO => 'Euro-Techno',
    AudioTrack::AMBIENT => 'Ambient',
    AudioTrack::TRIP_HOP => 'Trip-Hop',
    AudioTrack::VOCAL => 'Vocal',
    AudioTrack::JAZZ_FUNK => 'Jazz-Funk',
    AudioTrack::FUSION => 'Fusion',
    AudioTrack::TRANCE => 'Trance',
    AudioTrack::CLASSICAL => 'Classical',
    AudioTrack::INSTRUMENTAL => 'Instrumental',
    AudioTrack::ACID => 'Acid',
    AudioTrack::HOUSE => 'House',
    AudioTrack::GAME => 'Game',
    AudioTrack::SOUND_CLIP => 'Sound Clip',
    AudioTrack::GOSPEL => 'Gospel',
    AudioTrack::NOISE => 'Noise',
    AudioTrack::ALTERNATIVE_ROCK => 'Alternative Rock',
    AudioTrack::BASS => 'Bass',
    AudioTrack::SOUL => 'Soul',
    AudioTrack::PUNK => 'Punk',
    AudioTrack::SPACE => 'Space',
    AudioTrack::MEDITATIVE => 'Meditative',
    AudioTrack::INSTRUMENTAL_POP => 'Instrumental Pop',
    AudioTrack::INSTRUMENTAL_ROCK => 'Instrumental Rock',
    AudioTrack::ETHNIC => 'Ethnic',
    AudioTrack::GOTHIC => 'Gothic',
    AudioTrack::DARKWAVE => 'Darkwave',
    AudioTrack::TECHNO_INDUSTRIAL => 'Techno-Industrial',
    AudioTrack::ELECTRONIC => 'Electronic',
    AudioTrack::POP_FOLK => 'Pop-Folk',
    AudioTrack::EURODANCE => 'Eurodance',
    AudioTrack::DREAM => 'Dream',
    AudioTrack::SOUTHERN_ROCK => 'Southern Rock',
    AudioTrack::COMEDY => 'Comedy',
    AudioTrack::CULT => 'Cult',
    AudioTrack::GANGSTA => 'Gangsta',
    AudioTrack::TOP_40 => 'Top 40',
    AudioTrack::CHRISTIAN_RAP => 'Christian Rap',
    AudioTrack::POP_FUNK => 'Pop/Funk',
    AudioTrack::JUNGLE => 'Jungle',
    AudioTrack::NATIVE_AMERICAN => 'Native American',
    AudioTrack::CABARET => 'Cabaret',
    AudioTrack::NEW_WAVE => 'New Wave',
    AudioTrack::PSYCHEDELIC => 'Psychedelic',
    AudioTrack::RAVE => 'Rave',
    AudioTrack::SHOWTUNES => 'Showtunes',
    AudioTrack::TRAILER => 'Trailer',
    AudioTrack::LO_FI => 'Lo-Fi',
    AudioTrack::TRIBAL => 'Tribal',
    AudioTrack::ACID_PUNK => 'Acid Punk',
    AudioTrack::ACID_JAZZ => 'Acid Jazz',
    AudioTrack::POLKA => 'Polka',
    AudioTrack::RETRO => 'Retro',
    AudioTrack::THEATRE => 'Theatre',
    AudioTrack::ROCK_AND_ROLL => "Rock 'n' Roll",
    AudioTrack::HARD_ROCK => 'Hard Rock',
    AudioTrack::FOLK => 'Folk',
    AudioTrack::FOLK_ROCK => 'Folk Rock',
    AudioTrack::NATIONAL_FOLK => 'National Folk',
    AudioTrack::SWING => 'Swing',
    AudioTrack::FAST_FUSION => 'Fast Fusion',
    AudioTrack::BEBOP => 'Bebop',
    AudioTrack::LATIN => 'Latin',
    AudioTrack::REVIVAL => 'Revival',
    AudioTrack::CELTIC => 'Celtic',
    AudioTrack::BLUEGRASS => 'Bluegrass',
    AudioTrack::AVANTGARDE => 'Avantgarde',
    AudioTrack::GOTHIC_ROCK => 'Gothic Rock',
    AudioTrack::PROGRESSIVE_ROCK => 'Progressive Rock',
    AudioTrack::PSYCHEDELIC_ROCK => 'Psychedelic Rock',
    AudioTrack::SYMPHONIC_ROCK => 'Symphonic Rock',
    AudioTrack::SLOW_ROCK => 'Slow Rock',
    AudioTrack::BIG_BAND => 'Big Band',
    AudioTrack::CHORUS => 'Chorus',
    AudioTrack::EASY_LISTENING => 'Easy Listening',
    AudioTrack::ACOUSTIC => 'Acoustic',
    AudioTrack::HUMOUR => 'Humour',
    AudioTrack::SPEECH => 'Speech',
    AudioTrack::CHANSON => 'Chanson',
    AudioTrack::OPERA => 'Opera',
    AudioTrack::CHAMBER_MUSIC => 'Chamber Music',
    AudioTrack::SONATA => 'Sonata',
    AudioTrack::SYMPHONY => 'Symphony',
    AudioTrack::BOOTY_BASS => 'Booty Bass',
    AudioTrack::PRIMUS => 'Primus',
    AudioTrack::PORN_GROOVE => 'Porn Groove',
    AudioTrack::SATIRE => 'Satire',
    AudioTrack::SLOW_JAM => 'Slow Jam',
    AudioTrack::CLUB => 'Club',
    AudioTrack::TANGO => 'Tango',
    AudioTrack::SAMBA => 'Samba',
    AudioTrack::FOLKLORE => 'Folklore',
    AudioTrack::BALLAD => 'Ballad',
    AudioTrack::POWER_BALLAD => 'Power Ballad',
    AudioTrack::RHYTHMIC_SOUL => 'Rhythmic Soul',
    AudioTrack::FREESTYLE => 'Freestyle',
    AudioTrack::DUET => 'Duet',
    AudioTrack::PUNK_ROCK => 'Punk Rock',
    AudioTrack::DRUM_SOLO => 'Drum Solo',
    AudioTrack::A_CAPPELLA => 'A Cappella',
    AudioTrack::EURO_HOUSE => 'Euro-House',
    AudioTrack::DANCEHALL => 'Dancehall',
    default => 'Inconnu',
};
    }
}