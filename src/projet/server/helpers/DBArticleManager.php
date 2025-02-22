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

    public function readSet($id) {
        error_log("DBArticleManager.readSet called with ID: " . $id);
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
    
        try {
            $result = $db->SelectQuery($sql, array(':id' => $id));
        } catch (Exception $e) {
            echo("Error executing query: " . $e->getMessage());
            return false;
        }
    
        if (count($result) > 0) {
            $row = $result[0];  // Get the first row
            $set = new Set(
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
    
            return $set;
        }
    
        echo("No set found for ID: " . $id);
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

    /**
     * Met à jour un set existant dans la base de données.
     *
     * @param Set $set Le set à modifier
     * @return bool true si la mise à jour a réussi, false sinon
     */
    public function updateSet($set)
    {
        $db = DBConnection::getInstance();

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
                WHERE PK_Set = :pk_set";

        $params = array(
            'pk_set' => $set->getPkSet(),
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

        $rowCount = $db->executeQuery($sql, $params);
        return $rowCount > 0;
    }

    /**
     * Supprime un set de la base de données.
     *
     * @param int $pk_set L'ID du set à supprimer
     * @return bool true si la suppression a réussi, false sinon
     */
    public function deleteSet($pk_set)
    {
        $db = DBConnection::getInstance();
        $sql = "DELETE FROM t_set WHERE PK_Set = :pk_set";
        $rowCount = $db->executeQuery($sql, array('pk_set' => $pk_set));
        return $rowCount > 0;
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