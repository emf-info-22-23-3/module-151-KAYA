<?php

/**
 * @author Kaya
 */

class ArticleManager {
    private $dbManager;

    public function __construct() {
        $this->dbArticleManager = new DBArticleManager();
    }

    public function getArmorNames() {
        $sets = $this->dbArticleManager->readSets();
        $armorNames = array();
        foreach ($sets as $set) {
            $armorNames[] = $set->getNom();
        }
        return $armorNames;
    }

    public function getAnnonces() {
        $sets = $this->dbArticleManager->readSets();
        return $sets;
    }
}
?>