<?php
 
$bdd = new PDO('mysql:host=localhost;dbname=sys', 'root', 'Pa$$w0rd');
 
$reponse = $bdd->query('SELECT titre FROM jeux_video');
 
while ($donnees = $reponse->fetch()) {
    echo $donnees['titre'] . '<br>'; 
}
 
$reponse->closeCursor();
 
?>