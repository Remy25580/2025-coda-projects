<?php

require_once('inc/page.inc.php');
require_once('inc/database.inc.php');
$search = $_GET['query'] ?? null;
//Initialisation de la base de données

try {
    $db = new DatabaseManager(
        dsn: "mysql:host=mysql;dbname=lowify;charset=utf8mb4",
        username: "lowify",
        password: "lowifypassword"
    );
} catch (PDOException $e) {
    echo $e->getMessage();
}


//==========FONCTIONS=================
function setDuration(int $duration): string{
    $minute = (string)round($duration / 60, 0);
    if ($duration % 60 < 10){
        $seconde = "0" . (string)($duration % 60);
    } else {
        $seconde = (string)($duration % 60);
    }
    return "$minute:$seconde";
}

//==========REQUÊTES SQL==============
$artistes = [];
$sqlArtists = <<<SQL
SELECT id, name, cover
FROM artist
WHERE LOWER(name) LIKE LOWER('%{$search}%');
SQL;

try{
    $artistes = $db->executeQuery($sqlArtists);
} catch (PDOException $e) {
    echo $e->getMessage();
}

$albums = [];
$sqlAlbums = <<<SQL
SELECT album.id AS id, album.name AS name, album.cover AS cover, album.release_date AS release_date, artist.name AS artist, artist.id as idArtist
FROM album
INNER JOIN artist ON album.artist_id = artist.id
WHERE LOWER(album.name) LIKE LOWER('%{$search}%');
SQL;

try{
    $albums = $db->executeQuery($sqlAlbums);
} catch (PDOException $e) {
    echo $e->getMessage();
}

$songs = [];
$sqlSongs = <<<SQL
SELECT album.id AS id, song.name AS name, song.duration AS duree, song.note AS note, album.cover AS cover, album.name AS album, artist.name AS artist, artist.id as idArtist
FROM song
INNER JOIN album ON song.album_id = album.id
INNER JOIN artist ON song.artist_id = artist.id
WHERE LOWER(song.name) LIKE LOWER('%{$search}%');
SQL;

try{
    $songs = $db->executeQuery($sqlSongs);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//============CRÉATION DU CONTENU CSS===========
$rawCSS = <<<CSS
body {
    background-color: darkslategrey;
    font-family: Consolas;
    margin: 0;
    padding: 20px;
    color: white;
}
a{
    color: #658d8d;
}
img{
    width: 80px;
    height: auto;
    border-radius: 5px;
}
.artists, .albums, .songs{
    margin-left: 8px;
    margin-right: 8px;
    font-size: 20px;
    background: rgba(255,255,255,0.05);
    padding: 15px;
    border-radius: 8px;
    margin-top: 15px;
    margin-bottom: 15px;
}
.artist, .album, .song{
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    gap: 15px;
    padding-bottom: 15px;
    padding-top: 15px;
}
.infos-album p, .infos-song p{
    margin-top: 7px;
    margin-bottom: 7px;
}
CSS;

//===========CRÉATION DU CONTENU HTML

$page = new HTMLPage(title: "Recherche: {$search}");
$page->addRawStyle($rawCSS);

if (sizeof($artistes) === 0 and sizeof($songs) === 0 and sizeof($albums) === 0) {
    $page->addContent(<<<HTML
        <h2>Aucun résultat contenant {$search} . . .</h2>
        <a href="index.php">Retour à l'accueil</a>
        HTML);
}
else{
    $page->addContent(<<<HTML
        <h2>Résultats contenant {$search} : </h2>
        HTML);

    if (sizeof($artistes) > 0) {
        $page->addContent(<<<HTML
            <div class="artists">
            <h3>Artistes:</h3>
            HTML);
        foreach ($artistes as $artist) {
            $page->addContent(<<<HTML
                <div class="artist">
                    <a href="artist.php?id={$artist['id']}"><img src="{$artist['cover']}"></a>
                    <a href="artist.php?id={$artist['id']}"><p>{$artist['name']}</p></a>
                </div>
                HTML);
        }
        $page->addContent(<<<HTML
            </div>
            HTML);
    }

    if (sizeof($albums) > 0) {
        $page->addContent(<<<HTML
            <div class="albums">
            <h3>Albums:</h3>
            HTML);
        foreach ($albums as $album) {
            $page->addContent(<<<HTML
                <div class="album">
                    <a href="album.php?id={$album['id']}"><img src="{$album['cover']}"></a>
                    <div class="infos-album">
                        <p><a href="album.php?id={$album['id']}">{$album['name']}</a>, de <a href="artiste.php?id={$album['idArtist']}">{$album['artist']}</a></p>
                        <p>{$album['release_date']}</p>
                    </div>
                </div>
                HTML);
        }
        $page->addContent(<<<HTML
            </div>
            HTML);
    }

    if (sizeof($songs) > 0) {
        $page->addContent(<<<HTML
            <div class="songs">
            <h3>Chansons:</h3>
            HTML);
        foreach ($songs as $song) {
            $temps = setDuration($song['duree']);
            $page->addContent(<<<HTML
                <div class="song">
                    <a href="album.php?id={$song['id']}"><img src="{$song['cover']}"></a>
                    <div class="infos-song">
                        <p>{$song['name']}, de <a href="artist.php?id={$song['idArtist']}">{$song['artist']}</a>  -  <a href="album.php?id={$song['id']}">{$song['album']}</a></p>
                        <p>Duree : $temps // Note : {$song['note']} / 5</p>
                    </div>
                </div>
                HTML);
        }
        $page->addContent(<<<HTML
            </div>
            HTML);
    }
}

echo $page->render();