<?php
class Ctrl {
    private $worker;

    public function __construct() {
        $this->worker = new Wrk();
    }

    public function getEquipes() {
        $equipes = $this->worker->getEquipes();
        return $equipes;
    }

    public function getJoueurs($equipeId) {
        $joueurs = $this->worker->getJoueursByEquipe($equipeId);
        return $joueurs;
    }
}
