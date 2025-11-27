<?php

require_once('inc/page.inc.php');
require_once('inc/database.inc.php');

//Implémentation de la base de données
try {
    $db = new DatabaseManager(
        dsn: "mysql:host=mysql;dbname=lowify;charset=utf8mb4",
        username: "lowify",
        password: "lowifypassword"
    );
} catch (PDOException $e) {
    echo $e->getMessage();
}

//Fonctions
function setMonthlyListeners(int $nb): string{
    $monthlyListeners = "";
    if($nb >= 1000000){
        $monthlyListeners .= (string)round($nb/1000000, 1);
        $monthlyListeners .= "M";
        return $monthlyListeners;
    }
    if($nb >= 1000){
        $monthlyListeners .= (string)round($nb/1000, 1);
        $monthlyListeners .= "K";
        return $monthlyListeners;
    }
    $monthlyListeners .= (string)$nb;
    return $monthlyListeners;
}

function setDuration(int $duration): string{
    $minute = (string)round($duration / 60, 0);
    $seconde = (string)($duration % 60);
    return "$minute:$seconde";
}

$idArtist = (int)$_GET["id"];

//============REQUÊTES SQL============//
//Récupération de informations de l'artiste dans $artistInfos
$artistInfos = [];
$sqlGet = <<<SQL
SELECT *
FROM artist
WHERE id = $idArtist;
SQL;

try{
    $artistInfos = $db->executeQuery($sqlGet);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//Récupération des 5 chansons les mieux notées
$artistFiveSongs = [];
$sqlFiveSongs = <<<SQL
SELECT song.name AS name, song.duration AS duree, song.note AS note, album.cover AS cover
FROM song
INNER JOIN artist ON artist.id = song.artist_id
INNER JOIN album ON album.id = song.album_id
WHERE artist.id = $idArtist
ORDER BY song.note DESC
LIMIT 5;
SQL;

try{
    $artistFiveSongs = $db->executeQuery($sqlFiveSongs);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//Récupération de tout les albums
$artistAlbums = [];
$sqlAlbums = <<<SQL
SELECT album.name, album.cover, album.release_date
FROM album
WHERE album.artist_id = $idArtist
SQL;

try {
    $artistAlbums = $db->executeQuery($sqlAlbums);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//===========CRÉATION DU CONTENU SQL===============
$rawSQL = <<<SQL
body{
    background-color: darkslategrey;
    font-family: Consolas;
}
SQL;


//===========CRÉATION DU CONTENU HTML==============

$page = new HTMLPage(title: "{$artistInfos[0]["name"]}");
$page->addRawStyle($rawSQL);

//Injection des informations de l'artiste
$monthlyListeners = setMonthlyListeners($artistInfos[0]["monthly_listeners"]);
$page->addContent(<<<HTML
    <body>
    <h1>{$artistInfos[0]["name"]}</h1>
    <div id="monthly-listeners">$monthlyListeners d'auditeurs mensuels</div>
    <article id="bio">{$artistInfos[0]["biography"]}</article>
    HTML);

//Injection des top titres
$page->addContent(<<<HTML
    <aside class="top-titre">
    <h3>Les top titres :</h3>
    HTML);

foreach($artistFiveSongs as $song){
    $temps = setDuration($song["duree"]);
    $page->addContent(<<<HTML
        <div id="song-{$song["name"]}">
            <img src="{$song["cover"]}" alt="{$song["name"]}">
            <p>{$song["name"]}</p>
            <p>$temps</p>
            <p>{$song["note"]}</p>
        </div>
        HTML);
}

$page->addContent(<<<HTML
    </aside>
    HTML);

//ajout des albums
$page->addContent(<<<HTML
    <div class="albums">
    <h3>Albums de {$artistInfos[0]["name"]} :</h3>
    HTML);

foreach($artistAlbums as $album){
    $page->addContent(<<<HTML
        <img src="{$album["cover"]}" alt="{$album["name"]}">
        <p>{$album["name"]}</p>
        <p>{$album["release_date"]}</p>
        HTML);
}

$page->addContent(<<<HTML
    </div>
    HTML);

$page->addContent(<<<HTML
    </body>
    HTML);

echo $page->render();