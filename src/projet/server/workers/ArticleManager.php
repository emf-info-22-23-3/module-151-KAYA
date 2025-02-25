<?php

/**
 * @author Kaya
 */

class ArticleManager {
    private $dbArticleManager;

    /**
     * Constructor for ArticleManager
     * Initializes the DBArticleManager instance to interact with the database.
     */
    public function __construct() {
        $this->dbArticleManager = new DBArticleManager();
    }

    /**
     * Retrieves all armor names for the dropdown in a selection.
     * 
     * @return array Array of sets with only id and name, or false if no sets are found
     */
    public function getArmorNames() {
        $sets = $this->dbArticleManager->readSets(); // Retrieves all sets from the database
        if($sets) {
            $armorNames = array();
            foreach ($sets as $set) {
                // Collects each set's ID and name for dropdown options
                $armorNames[] = array(
                    'id' => $set->getPkSet(),
                    'name' => $set->getNom()
                );
            }
        } else {
            return false; // Returns false if no sets are found
        }
        return $armorNames; // Returns the armor names array
    }

    /**
     * Retrieves a source by its ID.
     *
     * @param int $sourceID The ID of the source to retrieve
     * @return Source|false A Source object if found, otherwise false
     */
    public function readSourceByID($sourceID){
        return $this->dbArticleManager->readSourceByID($sourceID);
    }

    /**
     * Retrieves a specific set by its ID.
     * 
     * @param int $id The ID of the set to retrieve
     * @return Set The set object or false if not found
     */
    public function getSet($id) {
        return $this->dbArticleManager->readSet($id); // Retrieves the set based on the ID
    }

    /**
     * Retrieves all source types from the database.
     *
     * @return array Array of source types with their ID and type, or false if no types are found
     */
    public function getSourceTypes() {
        $sourceTypes = $this->dbArticleManager->readSourceTypes(); // Retrieves all source types from the DB
    
        if ($sourceTypes) {
            $sourceTypesArray = array();  // Initializes an array to store formatted source types
            
            foreach ($sourceTypes as $sourceType) {
                // Collects the source type's ID and type for use in the frontend
                $sourceTypesArray[] = array(
                    'pk_type_source' => $sourceType->getPkTypeSource(),  // Using getter methods for properties
                    'type' => $sourceType->getType()
                );
            }
        } else {
            return false;  // If no source types are found
        }
            
        return $sourceTypesArray;  // Returns the array of source types
    }

    /**
     * Adds a new set to the database.
     *
     * @param Set $set The set to add
     * @param Source $armorCapSource The cap source object
     * @param Source $armorTunicSource The tunic source object
     * @param Source $armorTrousersSource The trousers source object
     * @return int|false The ID of the new set, or false if the addition failed
     */
    public function addSet($set, $armorCapSource, $armorTunicSource, $armorTrousersSource) {
        return $this->dbArticleManager->addSet($set, $armorCapSource, $armorTunicSource, $armorTrousersSource);
    }

    /**
     * Updates an existing set in the database.
     *
     * @param Set $set The set to update
     * @param Source $armorCapSourceObj The updated cap source object
     * @param Source $armorTunicSourceObj The updated tunic source object
     * @param Source $armorTrousersSourceObj The updated trousers source object
     * @return bool True if the update was successful, false otherwise
     */
    public function updateSet($set, $armorCapSourceObj, $armorTunicSourceObj, $armorTrousersSourceObj) {
        return $this->dbArticleManager->updateSet($set, $armorCapSourceObj, $armorTunicSourceObj, $armorTrousersSourceObj);
    }

    /**
     * Deletes a set by its ID.
     *
     * @param int $idSet The ID of the set to delete
     * @param int $idCapSource The ID of the cap source to delete
     * @param int $idTunicSource The ID of the tunic source to delete
     * @param int $idTrousersSource The ID of the trousers source to delete
     * @return bool True if the deletion was successful, false otherwise
     */
    public function deleteSet($idSet, $idCapSource, $idTunicSource, $idTrousersSource) {
        return $this->dbArticleManager->deleteSet($idSet, $idCapSource, $idTunicSource, $idTrousersSource);
    }
}
?>
