<?php
require("Ctrl.php");
$ctrl = new Ctrl();

if ($_GET['action'] == "equipe") {
    echo json_encode($ctrl->getEquipes());
}
?>