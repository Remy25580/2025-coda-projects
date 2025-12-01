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
    background: rgba(255,255,255,0.05); 
    border-radius: 5px;
}
.artist{
    display: flex;
    gap: 10px;
    margin-left: 20px;
    margin-top: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    width: 1416.8px;
}
.artist img{
    width: 120px;
    height: auto;
}
.art-name{
    display: flex;
    gap: 10px;
    flex-direction: column; 
}
#nom{
    font-size: 30px;
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
        <a href="artist.php?id={$artist["id"]}"><img src="{$artist["cover"]}" alt="cover de l'artiste {$artist["name"]}"></a>
        <div class="art-name">
            <a id="nom" href="artist.php?id={$artist["id"]}">{$artist["name"]}</a>
            <p><em>{$artist["biography"]}</em></p>
        </div>
        
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
