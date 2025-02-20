<?php
class Source {
    private $id;
    private $source;
    private $typeSourceId;

    public function __construct($id, $source, $typeSourceId) {
        $this->id = $id;
        $this->source = $source;
        $this->typeSourceId = $typeSourceId;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getSource() { return $this->source; }
    public function getTypeSourceId() { return $this->typeSourceId; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setSource($source) { $this->source = $source; }
    public function setTypeSourceId($typeSourceId) { $this->typeSourceId = $typeSourceId; }

    public function toArray() {
        return [
            'id' => $this->id,
            'source' => $this->source,
            'typeSourceId' => $this->typeSourceId
        ];
    }
}