<?php

/**
 * @author Kaya
 */

class ArticleManager {
    private $dbArticleManager;

    public function __construct() {
        $this->dbArticleManager = new DBArticleManager();
    }

    /**
     * Gets all sets from the database
     * 
     * @return array Array of Set objects
     */
    public function getAllSets() {
        return $this->dbArticleManager->readSets();
    }

    /**
     * Gets all armor names for the dropdown
     * 
     * @return array Array of sets with only id and name
     */
    public function getArmorNames() {
        $sets = $this->getAllSets();
        $armorNames = array();
        foreach ($sets as $set) {
            $armorNames[] = array(
                'id' => $set->getPkSet(),
                'name' => $set->getNom()
            );
        }
        return $armorNames;
    }

    public function getSet($id) {
        error_log("ArticleManager.getSet called with ID: " . $id);
        $set = $this->dbArticleManager->readSet($id);
        error_log("DBArticleManager.readSet returned: " . ($set ? "not null" : "null"));
        return $set ?: false;
    }

    /**
     * Adds a new set to the database
     * 
     * @param Set $set The set to add
     * @return int|false The ID of the new set or false if failed
     */
    public function addSet($set) {
        return $this->dbArticleManager->addSet($set);
    }

    /**
     * Updates an existing set
     * 
     * @param Set $set The set to update
     * @return bool True if successful, false otherwise
     */
    public function updateSet($set) {
        return $this->dbArticleManager->updateSet($set);
    }

    /**
     * Deletes a set by ID
     * 
     * @param int $setId The ID of the set to delete
     * @return bool True if successful, false otherwise
     */
    public function deleteSet($setId) {
        return $this->dbArticleManager->deleteSet($setId);
    }
}
?>