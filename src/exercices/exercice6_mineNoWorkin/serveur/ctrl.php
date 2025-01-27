<?php
header('Content-Type: text/xml');

require('wrk.php');

// Instantiate Wrk class
$wrk = new Wrk();
$teams = $wrk->getEquipesFromDB();

// Generate XML response
$xml = new SimpleXMLElement('<equipes/>');
foreach ($teams as $team) {
    $equipe = $xml->addChild('equipe');
    $equipe->addChild('id', $team['id']);
    $equipe->addChild('nom', $team['nom']);
}

// Output XML
echo $xml->asXML();
?>
