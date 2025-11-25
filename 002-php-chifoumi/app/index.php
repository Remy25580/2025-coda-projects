<?php

$playerChoice = $_GET['choice'] ?? "none";
function resultat(string $player, string $php): string
{
    $resultat = "";
    if ($player === "none") {
        return "Personne n'a joué";
    }
    if ($player === $php) {
        $resultat = "Égalité!";
    } elseif ($player === "pierre") {
        if ($php === "feuille") {
            $resultat = "Victoire de l'ordinateur . . . ";
        } else {
            $resultat = "Vous avez gagné !";
        }
    } elseif ($player === "feuille") {
        if ($php === "ciseaux") {
            $resultat = "Victoire de l'ordinateur . . . ";
        } else {
            $resultat = "Vous avez gagné !";
        }
    } elseif ($player === "ciseaux") {
        if ($php === "pierre") {
            $resultat = "Victoire de l'ordinateur . . . ";
        } else {
            $resultat = "Vous avez gagné !";
        }
    }

    return $resultat;
}

function php(): string
{
    $random = random_int(0, 2);
    if ($random === 0) {
        return "pierre";
    } elseif ($random === 1) {
        return "ciseaux";
    } else {
        return "feuille";
    }
}

$phpChoice = php();
$victoire = resultat($playerChoice, $phpChoice);
$html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jeu de Chifoumi</title>
    <meta name="Chifoumi" content="Page php html d'un jeu de chifoumi">
    <style>
    body{
        text-align: center;
    }
    
    .ligne{
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 15px;  
        margin-top: 15px;
    }
    
    #victoire{
        margin-bottom: 15px;  
        margin-top: 15px;
    }
    </style>
</head>

<body>
<h1 id="titre">Jeu de Pierre, Feuilles, Ciseaux</h1>
<div class="ligne">
    <div id="player">Votre choix: $playerChoice
    </div>


    <div id="php">Choix de l'ordinateur: $phpChoice
    </div>
</div>

<article id="victoire">$victoire
</article>


<div id="button">
    <a href="?choice=pierre">
        <button type="submit" id="pierre">Pierre</button>
    </a>
    <a href="?choice=feuille">
        <button type="submit" id="feuille">Feuille</button>
    </a>
    <a href="?choice=ciseaux">
        <button type="submit" id="ciseaux">Ciseaux</button>
    </a>
</div>

</body>
</html>
HTML;


echo $html;
