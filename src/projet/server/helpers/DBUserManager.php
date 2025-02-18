<?php
/**
 * @author Lexkalli
 */

class DBUserManager
{
    /**
     * Récupère un utilisateur par son login.
     *
     * @param string $login
     * @return User|false
     */
    public function checkLogin($login){
        $connection = Connection::getInstance();
        $sql = "SELECT PK_User, Email, Password, FK_Role FROM t_User WHERE login = ?";
        $result = $connection->selectSingleQuery($sql, array($login));
        if ($result) {
            return new User(
                $result['PK_User'],
                $result['Email'],
                $result['Password'],
                $result['FK_Role'],
            );
        }
        return false;
    }
}
?>