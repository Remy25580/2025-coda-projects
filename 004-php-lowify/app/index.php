<?php

require_once('inc/page.inc.php');
require_once('inc/database.inc.php');
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

//===========REQUÊTES SQL====================

//artistes les + populaires
$mostPopularArtists = [];
$sqlPopularArtists = <<<SQL
SELECT id, cover, name
FROM artist
ORDER BY monthly_listeners DESC
LIMIT 5;
SQL;

try{
    $mostPopularArtists = $db->executeQuery($sqlPopularArtists);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//albums les plus récents
$mostRecentAlbums = [];
$sqlRecentAlbums = <<<SQL
SELECT id, cover, name, release_date
FROM album
ORDER BY release_date DESC
LIMIT 5;
SQL;

try{
    $mostRecentAlbums = $db->executeQuery($sqlRecentAlbums);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//albums les mieux notés
$mostPopularAlbums = [];
$sqlPopularAlbums = <<<SQL
SELECT album.id AS id, album.cover AS cover, album.name AS name, ROUND(AVG(song.note),2) AS moyenne
FROM album
INNER JOIN song ON song.album_id = album.id
GROUP BY album.id, album.cover, album.name
ORDER BY ROUND(AVG(song.note),2) DESC
LIMIT 5;
SQL;

try{
    $mostPopularAlbums = $db->executeQuery($sqlPopularAlbums);
} catch (PDOException $e) {
    echo $e->getMessage();
}

//============CRÉATION DU CONTENU CSS================
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
h1{
    font-size: 50px;
    margin-top: 50px;
    margin-bottom: 10px;
    margin-left: 40px;
}
aside{
    margin-top: 50px;
    margin-bottom: 50px;
    margin-right: 15px;
    text-align: right;
}
aside form input{
    border-radius: 5px;
}
.tops{
    display: flex;
    gap: 30px;
}
.column{
    width: 400px;
    margin-left: 15px;
    margin-right: 15px;
    font-size: 20px;
    background: rgba(255,255,255,0.05);
    padding: 15px;
    border-radius: 8px;
}
.artist{
    margin-bottom: 5px;
    margin-top: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.artist a{
    display: flex;
    gap: 20px;
}
.album-recent{
    display: flex;
    gap: 30px;
    margin-bottom: 5px;
    margin-top: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}
.album-populaire{
    display: flex;
    gap: 30px;
    margin-bottom: 5px;
    margin-top: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
}

CSS;

//============CRÉATION DU CONTENU HTML=================

$page = new HTMLPage(title: "Lowify - Accueil");
$page->addRawStyle($rawCSS);
$page->addContent(<<<HTML
    <body>
    <h1>Bienvenue sur Lowify!</h1>
    <aside class="search">
        <form action="search.php" method="GET">
            <input type="text" name="search" placeholder="Rechercher...">
            <input type="submit" value="Rechercher">
        </form>
    </aside>
    <div class="tops">
    <div class="top-trending column">
    <h3>Top trendings</h3>
    HTML);

foreach ($mostPopularArtists as $artist) {
    $page->addContent(<<<HTML
        <div class="artist">
            <a href="artist.php?id={$artist['id']}">
                <img src="{$artist['cover']}" alt="cover de {$artist['name']}">
                <p>{$artist['name']}</p>
            </a>
        </div>
        HTML);
}
$page->addContent(<<<HTML
    </div>
    <div class="top-sorties column">
    <h3>Top sorties</h3>
    HTML);

foreach ($mostRecentAlbums as $album) {
    $page->addContent(<<<HTML
        <div class="album-recent">
            <a href="album.php?id={$album['id']}">
                <img src="{$album['cover']}" alt="cover de l'album {$album['name']}">
            </a>
            <div class="infos-album-rec">
                <a href="album.php?id={$album['id']}">{$album['name']}</a>
                <p>{$album['release_date']}</p>
            </div>
        </div>
        HTML);
}

$page->addContent(<<<HTML
    </div>
    <div class="top-albums column">
    <h3>Top albums</h3>
    HTML);

foreach ($mostPopularAlbums as $album) {
    $page->addContent(<<<HTML
        <div class="album-populaire">
            <a href="album.php?id={$album['id']}">
                <img src="{$album['cover']}" alt="cover de l'album {$album['name']}">
            </a>
            <div class="infos-album-pop">
                <a href="album.php?id={$album['id']}">{$album['name']}</a>
                <p>Note moyenne : {$album['moyenne']}</p>
            </div>
        </div>
        HTML);
}

$page->addContent(<<<HTML
    </div>
    </div>
    </body>
    HTML);

echo $page->render();
