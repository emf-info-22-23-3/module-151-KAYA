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
        
        $result = $db->SelectQuery($sql, array($email));
        
        if ($result && isset($result[0])) {
            return new User(
                $result[0]['PK_User'],
                $result[0]['Email'],
                $result[0]['Password'],
                $result[0]['RoleName']
            );
        }
        return false;
    }
}
?>