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

    public function readSourceByID($sourceID){
        return $this->dbArticleManager->readSourceByID($sourceID);
    }

    public function getSet($id) {
        return $this->dbArticleManager->readSet($id);
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
    public function addSet($set, $armorCapSource, $armorTunicSource, $armorTrousersSource) {
        return $this->dbArticleManager->addSet($set, $armorCapSource, $armorTunicSource, $armorTrousersSource);
    }    

    /**
     * updates a set from the database
     * 
     * @param Set $set The set to add
     * @return int|false The ID of the new set or false if failed
     */
    public function updateSet($set, $armorCapSourceObj, $armorTunicSourceObj, $armorTrousersSourceObj) {
        return $this->dbArticleManager->updateSet($set, $armorCapSourceObj, $armorTunicSourceObj, $armorTrousersSourceObj);
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