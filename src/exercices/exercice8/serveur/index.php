<?php
require_once 'Ctrl.php';
require_once 'Wrk.php';
require_once 'Database.php';
require_once 'Equipe.php';
require_once 'Joueur.php';

$ctrl = new Ctrl();

if ($_GET['action'] === 'equipe') {
    echo json_encode($ctrl->getEquipes());
} elseif ($_GET['action'] === 'joueur' && isset($_GET['equipeId'])) {
    echo json_encode($ctrl->getJoueurs($_GET['equipeId']));
}
