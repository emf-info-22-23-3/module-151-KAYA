<?php
// Classe représentant une source avec son identifiant, son contenu et le type de source associé
class Source {
    private $id;
    private $source;
    private $typeSourceId;
    
    // Constructeur de la classe
    public function __construct($id, $source, $typeSourceId) {
        $this->id = $id;
        $this->source = $source;
        $this->typeSourceId = $typeSourceId;
    }

    // Getters : méthodes pour récupérer les valeurs des attributs privés
    public function getId() { return $this->id; }
    public function getSource() { return $this->source; }
    public function getTypeSourceId() { return $this->typeSourceId; }

    // Setters : méthodes pour modifier les valeurs des attributs privés
    public function setId($id) { $this->id = $id; }
    public function setSource($source) { $this->source = $source; }
    public function setTypeSourceId($typeSourceId) { $this->typeSourceId = $typeSourceId; }

    // Convertit l'objet Source en chaîne XML
    public function toXML() {
        return '<source>' .
               '<id>' . htmlspecialchars($this->id) . '</id>' .
               '<source>' . htmlspecialchars($this->source) . '</source>' .
               '<typeSourceId>' . htmlspecialchars($this->typeSourceId) . '</typeSourceId>' .
               '</source>';
    }
    
}
