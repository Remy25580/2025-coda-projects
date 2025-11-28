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

if(sizeof($artistInfos) === 0){
    header("Location: error.php?error=unknown-artist");
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
$rawCSS = <<<CSS
body {
    background-color: darkslategrey;
    font-family: Consolas;
    margin: 0;
    padding: 20px;
    color: white;
}

.page-layout {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 40px;
}

.infos-artiste {
    flex: 1;
    max-width: 80%;
}

#monthly-listeners{
    margin-top: 20px;
    margin-bottom: 20px;
}

#bio{
    font-size: 25px;
    margin-top: 20px;
    margin-bottom: 20px;
    margin-left: 30px;
    max-width: 500px;
    padding: 8px;
    border: 2px solid white;
}
.top-titre {
    width: 280px;
    max-width: 20%;
    background: rgba(255,255,255,0.05);
    padding: 15px;
    border-radius: 8px;
    position: absolute;
    top: 20px;
    right: 20px;
}

.titre {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.titre img {
    width: 50px;
    height: 50px;
    border-radius: 6px;
    object-fit: cover;
    margin-right: 10px;
}

.titre .nom {
    font-size: 1rem;
    font-weight: 500;
}

.top-titre h3 {
    margin-top: 0;
    margin-bottom: 15px;
}

.top-titre div {
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

.albums {
    margin-top: 50px;
    max-width: 80%;
}

.albums h3 {
    margin-bottom: 15px;
}

.albums-container {
    display: flex;
    flex-wrap: wrap;
    gap: 70px;
}

.album {
    width: 160px;
}

.album img {
    height: 180px;
    width: auto;
    object-fit: cover;
    border-radius: 6px;
    display: block;
}

.album p {
    margin: 5px 0;
}
CSS;


//===========CRÉATION DU CONTENU HTML==============

$page = new HTMLPage(title: "{$artistInfos[0]["name"]}");
$page->addRawStyle($rawCSS);

//Injection des informations de l'artiste
$monthlyListeners = setMonthlyListeners($artistInfos[0]["monthly_listeners"]);
$page->addContent(<<<HTML
    <body>
    <div class="page-layout">
    <div class="infos-artiste">
    <h1>{$artistInfos[0]["name"]}</h1>
    <div id="monthly-listeners">$monthlyListeners d'auditeurs mensuels</div>
    <article id="bio">{$artistInfos[0]["biography"]}</article>
    </div> 
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
            <section class="titre">
                <img src="{$song["cover"]}" alt="{$song["name"]}">
                <p>{$song["name"]}</p>
            </section>  
            <p>$temps min</p>
            <p>Note: {$song["note"]} / 5</p>
        </div>
        HTML);
}

$page->addContent(<<<HTML
    </aside>
    </div>
    HTML);

//ajout des albums
$page->addContent(<<<HTML
    <div class="albums">
    <h3>Albums de {$artistInfos[0]["name"]} :</h3>
    <div class="albums-container">
    HTML);

foreach($artistAlbums as $album){
    $page->addContent(<<<HTML
        <div class="album">
            <img src="{$album["cover"]}" alt="{$album["name"]}">
            <p>{$album["name"]}</p>
            <p>{$album["release_date"]}</p>
        </div>
        HTML);
}

$page->addContent(<<<HTML
    </div>
    </div>
    HTML);

$page->addContent(<<<HTML
    </body>
    HTML);

echo $page->render();