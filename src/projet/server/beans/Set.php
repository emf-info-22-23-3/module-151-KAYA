<?php
class Set {
    private $pk_set;
    private $fk_user;
    private $nom;
    private $cap_nom;
    private $tunic_nom;
    private $trousers_nom;
    private $description;
    private $effet;
    private $fk_cap_source;
    private $fk_tunic_source;
    private $fk_trousers_source;
    private $image_set;

    public function __construct($pk_set, $fk_user, $nom, $cap_nom, $tunic_nom, $trousers_nom, 
                              $description, $effet, $fk_cap_source, $fk_tunic_source, 
                              $fk_trousers_source, $image_set = null) {
        $this->pk_set = $pk_set;
        $this->fk_user = $fk_user;
        $this->nom = $nom;
        $this->cap_nom = $cap_nom;
        $this->tunic_nom = $tunic_nom;
        $this->trousers_nom = $trousers_nom;
        $this->description = $description;
        $this->effet = $effet;
        $this->fk_cap_source = $fk_cap_source;
        $this->fk_tunic_source = $fk_tunic_source;
        $this->fk_trousers_source = $fk_trousers_source;
        $this->image_set = $image_set;
    }

    // Getters
    public function getPkSet() { return $this->pk_set; }
    public function getFkUser() { return $this->fk_user; }
    public function getNom() { return $this->nom; }
    public function getCapNom() { return $this->cap_nom; }
    public function getTunicNom() { return $this->tunic_nom; }
    public function getTrousersNom() { return $this->trousers_nom; }
    public function getDescription() { return $this->description; }
    public function getEffet() { return $this->effet; }
    public function getFkCapSource() { return $this->fk_cap_source; }
    public function getFkTunicSource() { return $this->fk_tunic_source; }
    public function getFkTrousersSource() { return $this->fk_trousers_source; }
    public function getImageSet() { return $this->image_set; }

    public function toXML() {
        return '<set>' .
               '<pk_set>' . $this->pk_set . '</pk_set>' .
               '<nom>' . htmlspecialchars($this->nom) . '</nom>' .
               '<cap_nom>' . htmlspecialchars($this->cap_nom) . '</cap_nom>' .
               '<tunic_nom>' . htmlspecialchars($this->tunic_nom) . '</tunic_nom>' .
               '<trousers_nom>' . htmlspecialchars($this->trousers_nom) . '</trousers_nom>' .
               '<description>' . htmlspecialchars($this->description) . '</description>' .
               '<effet>' . htmlspecialchars($this->effet) . '</effet>' .
               '</set>';
    }
}
?>