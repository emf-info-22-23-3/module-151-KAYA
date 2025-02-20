<?php

/**
 * @author Kaya
 */

class DBUserManager
{
    /**
     * Récupère un utilisateur par son email.
     *
     * @param string $email
     * @return User|false
     */
    public function checkLogin($email)
    {
        $db = DBConnection::getInstance();
        $sql = "SELECT u.PK_User, u.Email, u.Password, u.FK_Role, r.Role as RoleName 
                FROM t_user u
                JOIN t_role r ON u.FK_Role = r.PK_Role 
                WHERE u.Email = ?";
        
        $result = $db->selectSingleQuery($sql, array($email));
        
        if ($result) {
            return new User(
                $result['PK_User'],
                $result['Email'],
                $result['Password'],
                $result['RoleName']
            );
        }
        return false;
    }
}
?>