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
     * Gets all armor names for the dropdown
     * 
     * @return array Array of sets with only id and name
     */
    public function getArmorNames() {
        $sets = $this->dbArticleManager->readSets();
        if($sets){
            $armorNames = array();
            foreach ($sets as $set) {
                $armorNames[] = array(
                    'id' => $set->getPkSet(),
                    'name' => $set->getNom()
                );
            }
        } else {
            return false;
        }
        return $armorNames;
    }

    public function getSet($id) {
        echo ("ArticleManager.getSet called with ID: " . $id);
        $set = $this->dbArticleManager->readSet($id);
        echo ("DBArticleManager.readSet returned: " . ($set ? "not null" : "null"));
        
        if ($set) {
            // Returning a flat structure of the set
            $setData = array(
                'id' => $set->getPkSet(),
                'name' => $set->getNom(),
                'cap_name' => $set->getCapNom(),
                'tunic_name' => $set->getTunicNom(),
                'trousers_name' => $set->getTrousersNom(),
                'description' => $set->getDescription(),
                'effect' => $set->getEffet(),
                'cap_source' => $set->getFkCapSource(),
                'tunic_source' => $set->getFkTunicSource(),
                'trousers_source' => $set->getFkTrousersSource(),
                'image' => $set->getImageSet()
            );
            return $setData;
        } else {
            return false;
        }
    }

    public function getSourceTypes() {
        $sourceTypes = $this->dbArticleManager->readSourceTypes(); // Assuming this returns an array of SourceType objects
    
        if ($sourceTypes) {
            $sourceTypesArray = array();  // Initialize the array to store the formatted source types
            
            foreach ($sourceTypes as $sourceType) {
                $sourceTypesArray[] = array(
                    'pk_type_source' => $sourceType->getPkTypeSource(),  // Using getters for properties
                    'type' => $sourceType->getType()
                );
            }
        } else {
            return false;  // If no source types found
        }
            
        return $sourceTypesArray;
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

    public function addSource($source) {
        return $this->dbArticleManager->addSource($source);
    }

    /**
     * updates a set from the database
     * 
     * @param Set $set The set to add
     * @return int|false The ID of the new set or false if failed
     */
    public function updateSet($set) {
        return $this->dbArticleManager->updateSet($set);
    }

    public function updateSource($source) {
        return $this->dbArticleManager->updateSource($source);
    }

    public function beginTransaction() {
        return $this->dbArticleManager->beginTransaction();
    }

    public function commitTransaction() {
        return $this->dbArticleManager->commitTransaction();
    }

    public function rollbackTransaction() {
        return $this->dbArticleManager->rollbackTransaction();
    }

    /**
     * Deletes a set by ID
     * 
     * @param int $setId The ID of the set to delete
     * @return bool True if successful, false otherwise
     */
    public function deleteSet($idSet, $idCapSource, $idTunicSource, $idTrousersSource) {
        return $this->dbArticleManager->deleteSet($idSet, $idCapSource, $idTunicSource, $idTrousersSource);
    }
}
?>