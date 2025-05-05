<?php

/**
 * @author Kaya
 */

class DBArticleManager
{
    /**
     * Retrieves all sets from the database.
     *
     * @return array Array of Set objects
     */
    public function readSets()
    {
        $db = DBConnection::getInstance();

        $sql = "SELECT 
                    s.*, 
                    cs.Source as cap_source, 
                    ts.Source as tunic_source,
                    trs.Source as trousers_source, 
                    u.Email as creator_email
                FROM t_set s
                JOIN t_source cs ON s.FK_Cap_Source = cs.PK_Source
                JOIN t_source ts ON s.FK_Tunic_Source = ts.PK_Source
                JOIN t_source trs ON s.FK_Trousers_Source = trs.PK_Source
                JOIN t_user u ON s.FK_User = u.PK_User
                ORDER BY s.Nom";

        $result = $db->selectQuery($sql, array());

        $sets = array();
        foreach ($result as $row) {
            $sets[] = new Set(
                $row['PK_Set'],
                $row['FK_User'],
                $row['Nom'],
                $row['Cap_Nom'],
                $row['Tunic_Nom'],
                $row['Trousers_Nom'],
                $row['Description'],
                $row['Effet'],
                $row['FK_Cap_Source'],
                $row['FK_Tunic_Source'],
                $row['FK_Trousers_Source'],
                $row['Image_Set']
            );
        }
        return $sets;
    }

    /**
     * Retrieves a source by its ID from the database.
     *
     * @param int $sourceID The ID of the source to retrieve
     * @return Source|false A Source object if found, otherwise false
     */
    public function readSourceByID($sourceID)
    {
        $db = DBConnection::getInstance();

        $sql = "SELECT * FROM t_source WHERE PK_Source = :sourceID";

        $result = $db->SelectQuery($sql, array(':sourceID' => $sourceID));

        if (count($result) > 0) {
            $row = $result[0];
            $source = new Source(
                $row['PK_Source'], 
                $row['Source'], 
                $row['FK_type_source']
            );
            return $source;
        }

        return false;
    }

    /**
     * Retrieves a specific set by its ID from the database.
     *
     * @param int $id The ID of the set to retrieve
     * @return Set|false A Set object if found, otherwise false
     */
    public function readSet($id) 
    {
        $db = DBConnection::getInstance();
    
        $sql = "SELECT 
                    s.*, 
                    cs.Source as cap_source, 
                    ts.Source as tunic_source,
                    trs.Source as trousers_source, 
                    u.Email as creator_email
                FROM t_set s
                JOIN t_source cs ON s.FK_Cap_Source = cs.PK_Source
                JOIN t_source ts ON s.FK_Tunic_Source = ts.PK_Source
                JOIN t_source trs ON s.FK_Trousers_Source = trs.PK_Source
                JOIN t_user u ON s.FK_User = u.PK_User
                WHERE s.PK_Set = :id";
    
        $result = $db->SelectQuery($sql, array(':id' => $id));
    
        if (count($result) > 0) {
            $row = $result[0];  // Get the first row
            return new Set(
                $row['PK_Set'],
                $row['FK_User'],
                $row['Nom'],
                $row['Cap_Nom'],
                $row['Tunic_Nom'],
                $row['Trousers_Nom'],
                $row['Description'],
                $row['Effet'],
                $row['FK_Cap_Source'],
                $row['FK_Tunic_Source'],
                $row['FK_Trousers_Source'],
                $row['Image_Set']
            );
        }
    
        return false;
    }

    /**
     * Retrieves all source types from the database.
     *
     * @return array|false Array of SourceType objects if found, otherwise false
     */
    public function readSourceTypes() {
        $db = DBConnection::getInstance();
        $sql = "SELECT PK_type_source, type FROM t_type_source";
        $result = $db->SelectQuery($sql, array());
    
        $sourceTypes = array();
        
        if (!empty($result) && is_array($result)) {
            foreach ($result as $row) {
                if (isset($row['PK_type_source']) && isset($row['type'])) {
                    $sourceTypes[] = new SourceType(
                        $row['PK_type_source'], 
                        $row['type']);
                }
            }
            return $sourceTypes;
        }
    
        return false;
    }
    
    /**
     * Adds a new set to the database.
     *
     * @param Set $set The set object to add
     * @param string $armorCapSource The source for the cap armor
     * @param string $armorTunicSource The source for the tunic armor
     * @param string $armorTrousersSource The source for the trousers armor
     * @return int|false The ID of the added set if successful, otherwise false
     */
    public function addSet($set, $armorCapSource, $armorTunicSource, $armorTrousersSource)
    {
        $db = DBConnection::getInstance();

        try {
            $db->beginTransaction();

            // Insert sources and get their IDs
            $armorCapSourceId = $this->addSource($armorCapSource);
            $armorTunicSourceId = $this->addSource($armorTunicSource);
            $armorTrousersSourceId = $this->addSource($armorTrousersSource);

            // Now, set the source IDs in the set object
            $set->setFkCapSource($armorCapSourceId);
            $set->setFkTunicSource($armorTunicSourceId);
            $set->setFkTrousersSource($armorTrousersSourceId);

            // SQL query to insert into t_set table
            $sql = "INSERT INTO t_set (
                        FK_User, Nom, Cap_Nom, Tunic_Nom, Trousers_Nom,
                        Description, Effet, FK_Cap_Source, FK_Tunic_Source,
                        FK_Trousers_Source, Image_Set
                    ) VALUES (
                        :fk_user, :nom, :cap_nom, :tunic_nom, :trousers_nom,
                        :description, :effet, :fk_cap_source, :fk_tunic_source,
                        :fk_trousers_source, :image_set
                    )";

            // Parameters to bind, including the newly inserted source IDs
            $params = array(
                'fk_user' => $set->getFkUser(),
                'nom' => $set->getNom(),
                'cap_nom' => $set->getCapNom(),
                'tunic_nom' => $set->getTunicNom(),
                'trousers_nom' => $set->getTrousersNom(),
                'description' => $set->getDescription(),
                'effet' => $set->getEffet(),
                'fk_cap_source' => $set->getFkCapSource(),
                'fk_tunic_source' => $set->getFkTunicSource(),
                'fk_trousers_source' => $set->getFkTrousersSource(),
                'image_set' => $set->getImageSet()
            );

            // Execute the query to insert the set
            $db->executeQuery($sql, $params);

            // Commit the transaction
            $db->commitTransaction();

            return $db->getLastId("t_set");

        } catch (Exception $e) {
            // Rollback transaction if an error occurs
            $db->rollbackTransaction();
            return false;
        }
    }
    
    /**
     * Adds a new source to the database.
     *
     * This function inserts a new record into the t_source table with the provided source name 
     * and the foreign key reference to the source type.
     *
     * @param Source $source The source object containing the data to be inserted.
     * @return int The last inserted ID from the t_source table.
     */
    public function addSource($source)
    {
        $db = DBConnection::getInstance();

        // SQL query to insert into t_source table
        $sql = "INSERT INTO t_source (
                    source, FK_Type_Source
                ) VALUES (
                    :source, :fk_type_source
                )";

        // Parameters to bind
        $params = array(
            'source' => $source->getSource(),
            'fk_type_source' => $source->getTypeSourceId()
        );

        // Execute the query
        $db->executeQuery($sql, $params);

        // Return the last inserted ID for the new source
        return $db->getLastId("t_source");
    }

    /**
     * Updates an existing source in the database.
     *
     * This function updates the source record in the t_source table identified by the 
     * primary key with the provided new data.
     *
     * @param Source $sourceObject The source object containing the new data to be updated.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateSource($sourceObject)
    {
        $db = DBConnection::getInstance();

        $sourcePk = $sourceObject->getId(); 
        $source = $sourceObject->getSource(); 
        $typeSourceId = $sourceObject->getTypeSourceId(); 

        // SQL query to update the source in t_source table
        $sql = "UPDATE t_source SET
                    source = :source,
                    FK_Type_Source = :fk_type_source
                WHERE pk_source = :pk_source";

        // Parameters to bind
        $params = array(
            'source' => $source,
            'fk_type_source' => $typeSourceId,
            'pk_source' => $sourcePk
        );

        // Execute the query
        return $db->executeQuery($sql, $params);
    }

    /**
     * Updates an existing set in the database.
     *
     * This function updates the details of a set (including the associated source records) 
     * in the t_set table. It ensures that the source records are updated first before updating 
     * the set itself.
     *
     * @param Set $set The set object containing the updated data.
     * @param Source $armorCapSourceObj The updated source for the cap.
     * @param Source $armorTunicSourceObj The updated source for the tunic.
     * @param Source $armorTrousersSourceObj The updated source for the trousers.
     * @return bool True if the update was successful, false otherwise.
     */
    public function updateSet($set, $armorCapSourceObj, $armorTunicSourceObj, $armorTrousersSourceObj)
    {
        $db = DBConnection::getInstance();

        try {
            $db->beginTransaction();

            // First delete the sources
            $updatedCapSource = $this->updateSource($armorCapSourceObj);
            $updatedTunicSource = $this->updateSource($armorTunicSourceObj);
            $updatedTrousersSource = $this->updateSource($armorTrousersSourceObj);

            // If source deletion was successful, delete the set
            if ($updatedCapSource && $updatedTunicSource &&  $updatedTrousersSource) {
                // SQL query to update the set in t_set table
                $sql = "UPDATE t_set SET
                    FK_User = :fk_user,
                    Nom = :nom,
                    Cap_Nom = :cap_nom,
                    Tunic_Nom = :tunic_nom,
                    Trousers_Nom = :trousers_nom,
                    Description = :description,
                    Effet = :effet,
                    FK_Cap_Source = :fk_cap_source,
                    FK_Tunic_Source = :fk_tunic_source,
                    FK_Trousers_Source = :fk_trousers_source,
                    Image_Set = :image_set
                WHERE pk_set = :pk_set";

                // Parameters to bind
                $params = array(
                    'fk_user' => $set->getFkUser(),
                    'nom' => $set->getNom(),
                    'cap_nom' => $set->getCapNom(),
                    'tunic_nom' => $set->getTunicNom(),
                    'trousers_nom' => $set->getTrousersNom(),
                    'description' => $set->getDescription(),
                    'effet' => $set->getEffet(),
                    'fk_cap_source' => $set->getFkCapSource(),
                    'fk_tunic_source' => $set->getFkTunicSource(),
                    'fk_trousers_source' => $set->getFkTrousersSource(),
                    'image_set' => $set->getImageSet(),
                    'pk_set' => $set->getPkSet() 
                );

                $rowCount = $db->executeQuery($sql, $params);
            } 
            $db->commitTransaction();
            return $rowCount > 0;

        } catch (Exception $e) {
            $db->rollbackTransaction();
            return false;
        }
    }

    /**
     * Deletes source records from the database.
     *
     * This function deletes the source records identified by the provided array of source IDs 
     * from the t_source table.
     *
     * @param array $sourceIds Array of source IDs to be deleted.
     * @return bool True if at least one source was deleted, false otherwise.
     */
    public function deleteSource($sourceIds)
    {
        $db = DBConnection::getInstance();

        if (empty($sourceIds) || !is_array($sourceIds)) {
            return false;
        }

        $deletedCount = 0;

        foreach ($sourceIds as $id) {
            $sql = "DELETE FROM t_source WHERE PK_Source = :source_id";
            $rowCount = $db->executeQuery($sql, array('source_id' => $id));
            $deletedCount += $rowCount; // Count how many rows were deleted
        }

        // If at least one source was deleted, return true
        return $deletedCount > 0;
    }

    /**
     * Deletes a set and its associated sources from the database.
     *
     * This function deletes the set record and the associated source records from the t_source table 
     * using a transaction to ensure both are deleted or none at all.
     *
     * @param int $idSet The ID of the set to be deleted.
     * @param int $idCapSource The ID of the cap source to be deleted.
     * @param int $idTunicSource The ID of the tunic source to be deleted.
     * @param int $idTrousersSource The ID of the trousers source to be deleted.
     * @return bool True if the set and sources were deleted successfully, false otherwise.
     */
    public function deleteSet($idSet, $idCapSource, $idTunicSource, $idTrousersSource)
    {
        $db = DBConnection::getInstance();

        try {
            $db->beginTransaction();

            $sql = "DELETE FROM t_set WHERE PK_Set = :pk_set";
            $rowCount  = $db->executeQuery($sql, array('pk_set' => $idSet));

            if ($rowCount ) {
                $sourcesDeleted = $this->deleteSource([$idCapSource, $idTunicSource, $idTrousersSource]);
            } else {
                $sourcesDeleted = false;
            }

            if ($sourcesDeleted) {
                $db->commitTransaction();
                return true;
            } else {
                $db->rollbackTransaction();
                return false;
            }
        } catch (Exception $e) {
            $db->rollbackTransaction();
            return false;
        }
    }

    /**
     * Generates an XML representation of all sets.
     *
     * This function converts the list of all sets into an XML format for export or other uses.
     *
     * @return string The list of sets in XML format.
     */
    public function getInXML()
    {
        $sets = $this->readSets();
        $result = '<listeSets>';
        foreach ($sets as $set) {
            $result .= $set->toXML();
        }
        $result .= '</listeSets>';
        return $result;
    }
}
?>
