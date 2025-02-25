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
        // Fetch the user from the database using the email
        $user = $this->dbManager->checkLogin($email);

        if ($user) {
            // Use password_verify to compare the entered password (plaintext) with the stored hash
            if (password_verify($password, $user->getPassword())) {
            return $user;  // Password is valid
            }
        }

        return false;  // Invalid credentials
    }

}
