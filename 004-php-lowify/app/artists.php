<?php

require_once('inc/page.inc.php');
require_once('inc/database.inc.php');

try {
    $db = new DatabaseManager(
        dsn: "mysql:host=mysql;dbname=lowify;charset=utf8mb4",
        username: "lowify",
        password: "lowifypassword"
    );
} catch (PDOException $e) {
    echo $e->getMessage();
}

$artists = [];
$sql = <<<SQL
SELECT *
FROM artist;
SQL;

try{
    $artists = $db->executeQuery($sql);
} catch (PDOException $e) {
    echo $e->getMessage();
}

$html = <<<HTML
<body>
<h1>Artistes : </h1>
HTML;

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
    border-radius: 5px;
}
h1{
    margin-left: 25px;
}
.artists{
    display: flex;
    flex-wrap: wrap;
}
.artist{
    padding: 8px;
    display: inline-block;
    margin-top: 70px;
    margin-bottom: 70px;
    margin-right: 100px;
    margin-left: 100px;
    
}
.artist img{
    width: 250px;
    height: auto;
}
.art-name{
    font-size: 20px;
}
CSS;



$page = new HTMLpage(title: "Lowify - Artistes");
$page->addContent($html);
$page->addRawStyle($rawCSS);

$page->addContent(<<<HTML
    <div class="artists">
    HTML);
foreach ($artists as $artist) {
    $artistInfo = <<<HTML
    <div class="artist">
        <a href="artist.php?id={$artist["id"]}">
            <img src="{$artist["cover"]}" alt="cover de l'artiste {$artist["name"]}">
            <div class="art-name">{$artist["name"]} </div>
        </a>
    </div>
    <br>
    HTML;

    $page->addContent($artistInfo);
}
$page->addContent(<<<HTML
    </div>
    HTML);

$page->addContent(<<<HTML
    </body>
    HTML);

echo $page->render();
