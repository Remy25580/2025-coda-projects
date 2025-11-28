<?php

require_once('inc/page.inc.php');
require_once('inc/database.inc.php');
$idAlbum = $_GET['id'] ?? "none";
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

//=================FONCTIONS===================
function setDuration(int $duration): string{
    $minute = (string)round($duration / 60, 0);
    if ($duration % 60 < 10){
        $seconde = "0" . (string)($duration % 60);
    } else {
        $seconde = (string)($duration % 60);
    }
    return "$minute:$seconde";
}

//=================REQUÊTES SQL================

$albumInfos = [];
$sqlGet = <<<SQL
SELECT *
FROM album
WHERE id = $idAlbum;
SQL;

try{
    $albumInfos = $db->executeQuery($sqlGet);
} catch (PDOException $e) {
    echo $e->getMessage();
}

if (sizeof($albumInfos) === 0) {
    header("Location: error.php?error=unknown-album");
}

//Récupération des informations de l'artiste
$artist = [];
$sqlArtist = <<<SQL
SELECT artist.id AS id,
       artist.name AS name,
       artist.biography AS biography,
       artist.cover AS cover,
       artist.monthly_listeners AS monthlyListeners
FROM artist
JOIN album ON artist.id = album.artist_id
WHERE album.id = $idAlbum;
SQL;

try{
    $artist = $db->executeQuery($sqlArtist);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//Récupération des titres de l'album
$songs = [];
$sqlSongs = <<<SQL
SELECT *
FROM song
WHERE album_id = $idAlbum
ORDER BY id ASC;
SQL;

try{
    $songs = $db->executeQuery($sqlSongs);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//==============CRÉATION DU CONTENU CSS==============
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
.album-container{
    display: flex;
    margin-left: 15px;
    margin-right: 15px;
    margin-top: 20px;
    margin-bottom: 5px;
    padding-bottom: 40px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.cover{
    margin-right: 10px;
    margin-left: 10px;
}
.cover img{
    height: 280px;
    width: auto;
}
.nom{
    font-size: 40px;
}
.artiste, .date{
    font-size: 20px;
}
.songs{
    margin-left: 15px;
    margin-right: 15px;
    font-size: 20px;
    background: rgba(255,255,255,0.05);
    padding: 15px;
    border-radius: 8px;
}
.song-container{
    display: flex;
    max-height: 50px;
    gap: 50px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    justify-content: space-between;
}
.infos-song{
    display: flex;
    gap: 20px;
}
CSS;

//===============CRÉATION DU CONTENU HTML============
$page = new HTMLPage(title: "Lowify - {$albumInfos[0]["name"]}");
$page->addRawStyle($rawCSS);

$html = <<<HTML
<body>
<div class="album-container">
    <div class="cover"> <img src="{$albumInfos[0]["cover"]}" alt="cover de l'album"> </div>
    <div class="infos">
        <h2 class="nom"><em>{$albumInfos[0]["name"]}</em></h2>
        <p class="artiste"><a href="artist.php?id={$artist[0]["id"]}">{$artist[0]["name"]}</a></p>
        <p class="date">{$albumInfos[0]["release_date"]}</p>
    </div>
</div>
<div class="songs">
HTML;

$page->addContent($html);

foreach ($songs as $song) {
    $temps = setDuration($song["duration"]);
    $page->addContent(<<<HTML
        <div class="song-container">
            <p>{$song["name"]}</p>
            <div class="infos-song">
                <p>Durée : $temps</p>
                <p>Note : {$song["note"]} / 5</p>
            </div>
        </div>
        HTML);
}

$page->addContent(<<<HTML
    </div>
    </body>
    HTML);

echo $page->render();