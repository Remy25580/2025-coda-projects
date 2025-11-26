<?php


$useMin = $_POST['minuscules'] ?? "0";
$useMaj = $_POST['majuscules'] ?? "0";
$useNbs = $_POST['chiffres'] ?? "0";
$useSpec = $_POST['speciaux'] ?? "0";
$mdpTaille = $_POST['longueur'] ?? 12;

function generateSelectOptions($selected = 12): string
{
    $html = "";

    $options = range(8, 42);

    foreach ($options as $value) {
        $attribute = "";
        if ((int)$value == (int)$selected) {
            $attribute = "selected";
        }
        $html .= "<option $attribute value=\"$value\">$value</option>";
    }
    return $html;
}

$minChecked = $useMin === "1" ? "checked" : "";
$majChecked = $useMaj === "1" ? "checked" : "";
$nbsChecked = $useNbs === "1" ? "checked" : "";
$specChecked = $useSpec === "1" ? "checked" : "";

function generatePassword(string $min, string $maj, string $nbs, string $spec, int $size): string
{
    if ($min === "0" and $maj === "0" and $nbs === "0" and $spec === "0") {
        return "";
    }
    $minList = ["a", "z", "e", "r", "t", "y", "u", "i", "o", "p", "m", "l", "k", "j", "h", "g", "f", "d", "s", "q", "w", "x", "c", "v", "b", "n"];
    $majList = ["A", "Z", "E", "R", "T", "Y", "U", "I", "O", "P", "M", "L", "K", "J", "H", "G", "F", "D", "S", "Q", "W", "X", "C", "V", "B", "N"];
    $nbsList = ["0", "1", "2", "3", "4", "5", "6", "7", "8", "9"];
    $speciauxList = ["&", "#", "{", "(", "[", "-", "|", "`", "_", ",", "^", "@", ")", "]", "=", "}", "+", "°", "/", "*", ".", ",", "?", ";", ":", "!", "§", "%", "$", "£", "€", "¤"];

    $possibilites = [];
    $mdp = "";
    if ($min === "1") {
        foreach ($minList as $element) {
            $possibilites[] = $element;
        }
    }
    if ($maj === "1") {
        foreach ($majList as $element) {
            $possibilites[] = $element;
        }
    }
    if ($nbs === "1") {
        foreach ($nbsList as $element) {
            $possibilites[] = $element;
        }
    }
    if ($spec === "1") {
        foreach ($speciauxList as $element) {
            $possibilites[] = $element;
        }
    }
    for ($i = 0; $i < $size; $i++) {
        $mdp .= $possibilites[random_int(0, sizeof($possibilites) - 1)];
    }
    return $mdp;
}

$mdp = generatePassword($useMin, $useMaj, $useNbs, $useSpec, $mdpTaille);
$lenght = generateSelectOptions($mdpTaille);
$html = <<<HTML
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Générateur de mot de passe</title>
    <meta name="Générateur de mot de passe" content="Page web permettant de générer un mot de passe d'une longueur définie et en incluant ou non certains caractères">
    <style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f0f2f5;
        color: #333;
        margin: 0;
        padding: 0;
    }
        
    h1 {
        text-align: center;
        margin-top: 40px;
        color: #4a90e2;
    }

    .mdp {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 20px;
        margin: 30px auto;
        font-size: 25px;
    }

    #generated {
        background-color: #e0e0e0;
        min-height: 40px;
        min-width: 250px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 5px 10px;
        box-shadow: 1px 1px 5px rgba(0,0,0,0.1);
        font-weight: bold;
    }

    .taille {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 15px;
        font-size: 20px;
        margin-bottom: 30px;
    }

    select {
        min-width: 60px;
        font-size: 16px;
        padding: 4px 6px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    .options {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
        margin-bottom: 30px;
    }

    .case {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 18px;
    }

    .case input[type="checkbox"] {
        transform: scale(1.2);
        cursor: pointer;
    }

    .bouton {
        display: flex;
        justify-content: center;
        margin-bottom: 50px;
    }

    #generation {
        background-color: #4a90e2;
        color: white;
        border: none;
        padding: 10px 25px;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    #generation:hover {
        background-color: #357ab7;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

</style>
</head>

<form method="POST" action="index.php">
<body>
<h1>Bienvenue dans votre générateur de mot de passe!</h1>

<artcle class="mdp">
    <div>Votre mot de passe : </div>
    <div id="generated">
        <div>$mdp</div>
    </div>
</artcle>

<article class="taille">
    <div>Taille de votre mot de passe : </div>
    <select name="longueur">
        $lenght
    </select>
</article>

<artcile class="options">
    <label class="case"><input type="checkbox" name="minuscules" value="1" {$minChecked}> Inclure des lettres minuscules</input>
    </label>
    <label class="case"><input type="checkbox" name="majuscules" value="1" {$majChecked}> Inclure des lettres majuscules</input>
    </label>
    <label class="case"><input type="checkbox" name="chiffres" value="1" {$nbsChecked}> Inclure des chiffres</input>
    </label>
    <label class="case"><input type="checkbox" name="speciaux" value="1" {$specChecked}> Inclure des caractères spéciaux</input>
    </label>
</artcile>

<article class="bouton">
    <button type="submit" id="generation">Générer</button>
</article>
</body>
</form>
</html>
HTML;

echo $html;




/*
 * <style>
    h1{
        text-align: center;
    }
    .mdp{
        display: flex;
        gap: 20px;
        margin-top: 30px;
        margin-bottom: 30px;
        font-size: 25px;
    }
    #generated{
        background-color: darkgrey;
        min-height: 30px;
        min-width: 220px;
        border-radius: 5px;
    }
    .taille{
        display: flex;
        gap: 15px;
        font-size: 20px;
    }
    select{
        min-width: 50px;
        font-size: 15px;
    }
    .options{
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 30px;
        margin-bottom: 30px;
    }
    .case{
        display: flex;
        align-items: center;
        font-size: 20px;
        gap: 5px;
    }
    #generation{
        font-size: 15px;
    }
    </style>
 */