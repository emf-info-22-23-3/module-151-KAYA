<?php
include_once('Connexion.php');
include_once('beans/Set.php');

/**
 * Classe SetBDManager
 *
 * Cette classe permet la gestion des sets d'armure dans la base de données
 */
class SetBDManager {
    /**
     * Fonction permettant la lecture de tous les sets.
     * @return array liste de Set
     */
    public function readSets() {
        $count = 0;
        $liste = array();
        $connection = Connexion::getInstance();
        
        $query = "SELECT s.*, cs.Source as cap_source, ts.Source as tunic_source, 
                        trs.Source as trousers_source, u.Email as creator_email
                 FROM t_set s
                 JOIN t_source cs ON s.FK_Cap_Source = cs.PK_Source
                 JOIN t_source ts ON s.FK_Tunic_Source = ts.PK_Source
                 JOIN t_source trs ON s.FK_Trousers_Source = trs.PK_Source
                 JOIN t_user u ON s.FK_User = u.PK_User
                 ORDER BY s.Nom";
        
        $result = $connection->selectQuery($query, array());
        
        foreach($result as $data) {
            $set = new Set(
                $data['PK_Set'],
                $data['FK_User'],
                $data['Nom'],
                $data['Cap_Nom'],
                $data['Tunic_Nom'],
                $data['Trousers_Nom'],
                $data['Description'],
                $data['Effet'],
                $data['FK_Cap_Source'],
                $data['FK_Tunic_Source'],
                $data['FK_Trousers_Source'],
                $data['Image_Set']
            );
            $liste[$count++] = $set;
        }
        return $liste;
    }

    /**
     * Fonction permettant d'ajouter un nouveau set
     * @param Set $set Le set à ajouter
     * @return int L'ID du set ajouté
     */
    public function addSet($set) {
        $connection = Connexion::getInstance();
        
        $query = "INSERT INTO t_set (FK_User, Nom, Cap_Nom, Tunic_Nom, Trousers_Nom,
                                   Description, Effet, FK_Cap_Source, FK_Tunic_Source,
                                   FK_Trousers_Source, Image_Set)
                  VALUES (:fk_user, :nom, :cap_nom, :tunic_nom, :trousers_nom,
                         :description, :effet, :fk_cap_source, :fk_tunic_source,
                         :fk_trousers_source, :image_set)";
        
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
        
        $connection->executeQuery($query, $params);
        return $connection->getLastId("t_set");
    }

    /**
     * Fonction permettant de modifier un set existant
     * @param Set $set Le set à modifier
     * @return bool Succès de la modification
     */
    public function updateSet($set) {
        $connection = Connexion::getInstance();
        
        $query = "UPDATE t_set 
                  SET FK_User = :fk_user, Nom = :nom, Cap_Nom = :cap_nom,
                      Tunic_Nom = :tunic_nom, Trousers_Nom = :trousers_nom,
                      Description = :description, Effet = :effet,
                      FK_Cap_Source = :fk_cap_source, FK_Tunic_Source = :fk_tunic_source,
                      FK_Trousers_Source = :fk_trousers_source, Image_Set = :image_set
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
        
        return $connection->executeQuery($query, $params) > 0;
    }

    /**
     * Fonction permettant de supprimer un set
     * @param int $pk_set L'ID du set à supprimer
     * @return bool Succès de la suppression
     */
    public function deleteSet($pk_set) {
        $connection = Connexion::getInstance();
        $query = "DELETE FROM t_set WHERE PK_Set = :pk_set";
        return $connection->executeQuery($query, array('pk_set' => $pk_set)) > 0;
    }

    /**
     * Fonction permettant de retourner la liste des sets en XML
     * @return String Liste des sets en XML
     */
    public function getInXML() {
        $listSets = $this->readSets();
        $result = '<listeSets>';
        foreach($listSets as $set) {
            $result .= $set->toXML();
        }
        $result .= '</listeSets>';
        return $result;
    }
}
?>