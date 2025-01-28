<?php
$bdd = new PDO('mysql:host=localhost;dbname=nomDB', 'root', 'pwd');
$reponse = $bdd->query('select pk_jeux_video, titre from jeux_video');


while ($row = $response->fetch())
{

	echo $row['titre'] . $row['pk_jeux_video'];


}
$reponse->closeCursor();
?>