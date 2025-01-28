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

    public function toXML() {
        return "<joueur><id>{$this->id}</id><nom>{$this->nom}</nom><points>{$this->points}</points></joueur>";
    }
}
