<?php

require_once('inc/page.inc.php');

$error = $_GET["error"] ?? "none"; // erreur "unknown-artist" ou "unknown-album"

//==============CRÉATION DU CONTENU CSS====================
$rawCSS = <<<CSS
body{
    background-color: darkslategrey;
    font-family: Consolas;
    margin: 0;
    padding: 20px;
    color: white;
    text-align: center;
}
p{
    font-size: 25px;
}
CSS;


//==============CRÉATION DU CONTENU HTML====================
$page = new HTMLPage(title: "Erreur");
$page->addRawStyle($rawCSS);
$page->addContent(<<<HTML
    <body>
    <h1>ERREUR</h1>
    HTML);

switch ($error) {
    case "unknown-artist":
        $page->addContent(<<<HTML
            <p>L'artiste que vous avez tenté de chercher n'est pas répertorié sur notre site :(</p>
            <p>Nous vous prions de retenter votre recherche où de retourner à la <a href="index.php">page d'accueil</a></p>
            HTML);
        break;
    case "unknown-album":
        $page->addContent(<<<HTML
            <p>L'album que vous avez tenté de chercher n'est pas répertorié sur notre site :(</p>
            <p>Nous vous prions de retenter votre recherche où de retourner à la <a href="index.php">page d'accueil</a></p>
            HTML);
        break;
    default: break;
}

$page->addContent(<<<HTML
    </body>
    HTML);

echo $page->render();