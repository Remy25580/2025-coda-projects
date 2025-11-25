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

function php(string $player): string
{
    if ($player === "none") {
        return "none";
    }
    $random = random_int(0, 2);
    if ($random === 0) {
        return "pierre";
    } elseif ($random === 1) {
        return "ciseaux";
    } else {
        return "feuille";
    }
}

$phpChoice = php($playerChoice);
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
    body {
    text-align: center;
    background: linear-gradient(135deg, #5a7d2f, #8db255);
    font-family: Arial, sans-serif;
    color: #fff;
    margin: 0;
    padding: 20px;
}

/* Titre */
#titre {
    font-size: 2.4rem;
    margin-bottom: 25px;
    text-shadow: 2px 2px 4px #000;
}

/* Ligne player vs ordi */
.ligne {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin: 20px 0;
}

#player, #php {
    background: rgba(0,0,0,0.2);
    padding: 15px 25px;
    border-radius: 10px;
    font-size: 1.2rem;
    box-shadow: 0 3px 6px rgba(0,0,0,0.2);
    min-width: 200px;
}

/* Résultat */
#victoire {
    font-size: 1.8rem;
    font-weight: bold;
    padding: 15px;
    background: rgba(255,255,255,0.25);
    border-radius: 12px;
    display: inline-block;
    backdrop-filter: blur(5px);
    margin: 20px 0;
}

/* Boutons */
#button {
    margin-top: 25px;
}

#button a {
    text-decoration: none;
}

button {
    padding: 12px 25px;
    font-size: 1.1rem;
    font-weight: bold;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    margin: 0 10px;
    color: #fff;
    transition: 0.2s;
    margin-top: 15px;
    margin-bottom: 15px;
}

/* Couleurs des boutons */
#pierre {
    background-color: #444;
}
#pierre:hover {
    background-color: #222;
}

#feuille {
    background-color: #2a7f62;
}
#feuille:hover {
    background-color: #1d5e49;
}

#ciseaux {
    background-color: #7f2a2a;
}
#ciseaux:hover {
    background-color: #5e1d1d;
}
#retour-bouton {
    background-color: #1e3a8a
}
#retour-bouton:hover {
    background-color: #152a63;
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

HTML;
echo $html;

$retour = <<<HTML
<a href="index.php" id="retour">
    <button type="submit" id="retour-bouton">Retour à la page principale</button>
</a>
HTML;

if ($playerChoice !== "none") {
    echo $retour;
}

echo <<<HTML
</body>
</html>
HTML;

