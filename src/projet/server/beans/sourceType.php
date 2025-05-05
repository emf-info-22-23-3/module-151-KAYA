<?php

// Classe représentant un type de source (ex : livre, site, article...)
class SourceType {
    private $pkTypeSource;
    private $type;
    
    // Constructeur pour initialiser les propriétés
    public function __construct($pkTypeSource, $type) {
        $this->pkTypeSource = $pkTypeSource;
        $this->type = $type;
    }

    // Getter pour l'identifiant du type de source
    public function getPkTypeSource() {
        return $this->pkTypeSource;
    }

    // Getter pour le nom du type de source
    public function getType() {
        return $this->type;
    }

    // Méthode pour convertir l'objet en XML
    public function toXML() {
        return '<sourceType>' .
               '<pkTypeSource>' . htmlspecialchars($this->pkTypeSource) . '</pkTypeSource>' .
               '<type>' . htmlspecialchars($this->type) . '</type>' .
               '</sourceType>';
    }
}
?>
