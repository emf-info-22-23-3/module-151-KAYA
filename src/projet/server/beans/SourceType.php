<?php
class SourceType {
    private $pkTypeSource;
    private $type;

    public function __construct($pkTypeSource, $type) {
        $this->pkTypeSource = $pkTypeSource;
        $this->type = $type;
    }

    public function getPkTypeSource() {
        return $this->pkTypeSource;
    }

    public function getType() {
        return $this->type;
    }

    public function toXML() {
        return '<sourceType>' .
               '<pkTypeSource>' . htmlspecialchars($this->pkTypeSource) . '</pkTypeSource>' .
               '<type>' . htmlspecialchars($this->type) . '</type>' .
               '</sourceType>';
    }
}
?>
