<?php

/**
 * @author Kaya
 */

class DBArticleManager
{
    /**
     * Récupère tous les sets depuis la base de données.
     *
     * @return array Tableau d'objets Set
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

    //needs to return the id based on the sourceName and sourceTypeName
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
     * Ajoute un nouveau set dans la base de données.
     *
     * @param Set $set Le set à ajouter
     * @return int L'ID du set ajouté
     */
    public function addSet($set)
    {
        $db = DBConnection::getInstance();

        $sql = "INSERT INTO t_set (
                    FK_User, Nom, Cap_Nom, Tunic_Nom, Trousers_Nom,
                    Description, Effet, FK_Cap_Source, FK_Tunic_Source,
                    FK_Trousers_Source, Image_Set
                ) VALUES (
                    :fk_user, :nom, :cap_nom, :tunic_nom, :trousers_nom,
                    :description, :effet, :fk_cap_source, :fk_tunic_source,
                    :fk_trousers_source, :image_set
                )";

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

        $db->executeQuery($sql, $params);
        return $db->getLastId("t_set");
    }

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

    public function updateSource($sourcePk, $source, $typeSourceId)
    {
        $db = DBConnection::getInstance();

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
     * Met à jour un set existant dans la base de données.
     *
     * @param Set $set Le set à modifier
     * @return bool true si la mise à jour a réussi, false sinon
     */
    public function updateSet($set)
    {
        $db = DBConnection::getInstance();

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

        // Execute the query
        return $db->executeQuery($sql, $params);
    }

    public function deleteSource($idCapSource, $idTunicSource, $idTrousersSource)
    {
        $db = DBConnection::getInstance();

        // SQL query to retrieve the source ID based on sourceName and sourceTypeName
        $sql = "SELECT s.PK_Source
                ROM t_source s
                JOIN t_type_source ts ON s.FK_Type_Source = ts.PK_type_source
                WHERE s.Source = :source_name AND ts.type = :source_type_name";

        // Parameters to bind
        $params = array(
            'source_name' => $sourceName,
            'source_type_name' => $sourceTypeName
        );

        // Execute the query
        $result = $db->SelectQuery($sql, $params);

        // If result is found, return the source ID
        if (count($result) > 0) {
            return $result[0]['PK_Source'];
        }

        // If no result, return false
        return false;
}

    public function deleteSet($idSet, $idCapSource, $idTunicSource, $idTrousersSource)
    {
        $db = DBConnection::getInstance();

        // First delete the sources
        $sourcesDeleted = $this->deleteSource($idCapSource, $idTunicSource, $idTrousersSource);

        // If source deletion was successful, delete the set
        if ($sourcesDeleted) {
            $sql = "DELETE FROM t_set WHERE PK_Set = :pk_set";
            $rowCount = $db->executeQuery($sql, array('pk_set' => $idSet));
            return $rowCount > 0;
        } else {
            // If source deletion failed, return false
            return false;
        }
    }

    public function beginTransaction() {
        $this->dbConnection = DBConnection::getInstance();
        return $this->dbConnection->beginTransaction();
    }

    public function commitTransaction() {
        $this->dbConnection = DBConnection::getInstance();
        return $this->dbConnection->commitTransaction();
    }

    public function rollbackTransaction() {
        $this->dbConnection = DBConnection::getInstance();
        return $this->dbConnection->rollbackTransaction();
    }


    /**
     * Génère une représentation XML de tous les sets.
     *
     * @return string La liste des sets au format XML
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