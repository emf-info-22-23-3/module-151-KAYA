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

    // Setters
    public function setFkUser($fk_user) { $this->fk_user = $fk_user; }
    public function setNom($nom) { $this->nom = $nom; }
    public function setCapNom($cap_nom) { $this->cap_nom = $cap_nom; }
    public function setTunicNom($tunic_nom) { $this->tunic_nom = $tunic_nom; }
    public function setTrousersNom($trousers_nom) { $this->trousers_nom = $trousers_nom; }
    public function setDescription($description) { $this->description = $description; }
    public function setEffet($effet) { $this->effet = $effet; }
    public function setFkCapSource($fk_cap_source) { $this->fk_cap_source = $fk_cap_source; }
    public function setFkTunicSource($fk_tunic_source) { $this->fk_tunic_source = $fk_tunic_source; }
    public function setFkTrousersSource($fk_trousers_source) { $this->fk_trousers_source = $fk_trousers_source; }
    public function setImageSet($image_set) { $this->image_set = $image_set; }

    public function toXML() {
        $xml = '<set>';
        $xml .= '<pk_set>' . htmlspecialchars($this->pk_set ?? '', ENT_XML1, 'UTF-8') . '</pk_set>';
        $xml .= '<nom>' . htmlspecialchars($this->nom ?? '', ENT_XML1, 'UTF-8') . '</nom>';
        $xml .= '<cap_nom>' . htmlspecialchars($this->cap_nom ?? '', ENT_XML1, 'UTF-8') . '</cap_nom>';
        $xml .= '<tunic_nom>' . htmlspecialchars($this->tunic_nom ?? '', ENT_XML1, 'UTF-8') . '</tunic_nom>';
        $xml .= '<trousers_nom>' . htmlspecialchars($this->trousers_nom ?? '', ENT_XML1, 'UTF-8') . '</trousers_nom>';
        $xml .= '<description>' . htmlspecialchars($this->description ?? '', ENT_XML1, 'UTF-8') . '</description>';
        $xml .= '<effet>' . htmlspecialchars($this->effet ?? '', ENT_XML1, 'UTF-8') . '</effet>';
        $xml .= '<fk_cap_source>' . htmlspecialchars($this->fk_cap_source ?? '', ENT_XML1, 'UTF-8') . '</fk_cap_source>';
        $xml .= '<fk_tunic_source>' . htmlspecialchars($this->fk_tunic_source ?? '', ENT_XML1, 'UTF-8') . '</fk_tunic_source>';
        $xml .= '<fk_trousers_source>' . htmlspecialchars($this->fk_trousers_source ?? '', ENT_XML1, 'UTF-8') . '</fk_trousers_source>';
        $xml .= '<image_set>' . htmlspecialchars($this->image_set ?? '', ENT_XML1, 'UTF-8') . '</image_set>';
        $xml .= '</set>';
        return $xml;
    }
    
}

?>