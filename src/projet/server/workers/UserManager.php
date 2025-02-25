<?php

/**
 * @author Kaya
 */

class UserManager
{
    private $dbManager;

    public function __construct()
    {
        $this->dbManager = new DBUserManager();
    }

    /**
     * Vérifie les identifiants de l'utilisateur.
     *
     * @param string $email
     * @param string $password
     * @return User|false Retourne l'objet User si les identifiants sont valides, false sinon.
     */
    public function checkCredentials($email, $password)
    {
        $user = $this->dbManager->checkLogin($email);
        if ($user) {
            if ($password === password_hash($user->getPassword(), PASSWORD_DEFAULT)) {
                return $user;
            }
        }
        return false;
    }
}