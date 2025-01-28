<?php
class Joueur {
    private $id;
    private $nom;
    private $points;

    public function __construct($id, $nom, $points) {
        $this->id = $id;
        $this->nom = $nom;
        $this->points = $points;
    }
}
