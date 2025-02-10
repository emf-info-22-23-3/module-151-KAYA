<?php
session_start(); // Démarrer ou reprendre la session

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['action'] == "connect") {
        // Vérifier si le mot de passe fourni est "emf"
        if ($_POST['password'] === "emf") {
            $_SESSION['logged'] = true; // Enregistrer la connexion
            echo '<result>true</result>';
        } else {
            unset($_SESSION['logged']); // Effacer la variable de session en cas d'échec
            echo '<result>false</result>';
        }
    }

    if ($_POST['action'] == "disconnect") {
        session_unset(); // Effacer toutes les variables de session
        session_destroy(); // Détruire la session
        echo '<result>true</result>';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($_GET['action'] == "getInfos") {
        if ($_SESSION['logged'] === true) {
            echo '<users>
                    <user><name>Victor Legros</name><salaire>9876</salaire></user>
                    <user><name>Marinette Lachance</name><salaire>7540</salaire></user>
                    <user><name>Gustave Latuile</name><salaire>4369</salaire></user>
                    <user><name>Basile Ledisciple</name><salaire>2384</salaire></user>
                  </users>';
        } else {
            echo '<message>DROITS INSUFFISANTS</message>';
        }
    }
}
?>
