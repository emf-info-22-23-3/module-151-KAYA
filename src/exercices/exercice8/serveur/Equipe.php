<?php
class Equipe {
    private $id;
    private $nom;

    public function __construct($id, $nom) {
        $this->id = $id;
        $this->nom = $nom;
    }

    public function toXML() {
        return "<equipe><id>{$this->id}</id><nom>{$this->nom}</nom></equipe>";
    }
}
