<?php

class Wrk {
    private $connection;

    public function __construct() {
        $this->connection = Database::getConnection();
    }

    public function getEquipes() {
        $stmt = $this->connection->prepare("SELECT id, nom FROM equipes");
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $equipes = [];
        foreach ($result as $row) {
            $equipes[] = new Equipe($row['id'], $row['nom']);
        }
        return $equipes;
    }

    public function getJoueursByEquipe($equipeId) {
        $stmt = $this->connection->prepare("SELECT id, nom, points FROM joueurs WHERE equipe_id = :equipe_id");
        $stmt->bindParam(':equipe_id', $equipeId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $joueurs = [];
        foreach ($result as $row) {
            $joueurs[] = new Joueur($row['id'], $row['nom'], $row['points']);
        }
        return $joueurs;
    }
}
