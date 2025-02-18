<?php
/**
 * @author Kaya
 */

class DBUserManager
{
    private $dbUserManager;
    private $sessionManager;

    public function __construct()
    {
        $this->dbUserManager = new DBUserManager();
        $this->sessionManager = new SessionManager();
    }

    /**
     * Vérifie les identifiants de l'utilisateur.
     *
     * @param string $login
     * @param string $password
     * @return User|false Retourne l'objet User si les identifiants sont valides, false sinon.
     */
    public function checkCredentials($login, $password)
    {
        $user = $this->dbUserManager->getUserByLogin($login);
        if ($user) {
            $pepper = SecretPepper::getSecretPepper();
            if (password_verify($pepper . $password, $user->getPassword())) {
                return $user;
            }
        }
        return false;
    }

    /**
     * Tente de connecter l'utilisateur.
     *
     * @param string $login
     * @param string $password
     * @return bool true si la connexion est réussie, false sinon.
     */
    public function login($login, $password)
    {
        $user = $this->checkCredentials($login, $password);

        if ($user) {
            $this->sessionManager->login($user);
            return true;
        }

        return false;
    }

    /**
     * Tente de déconnecter l'utilisateur.
     *
     * @return bool true si la déconnexion est réussie, false sinon.
     */
    public function logout()
    {

        if ($this->isLogged()) {
            $this->sessionManager->logout();
            return true;
        }

        return false;
    }

    public function isLogged()
    {
        return $this->sessionManager->isLogged();
    }

    /**
     * Tente de créer un nouvel utilisateur.
     *
     * @param string $name
     * @param string $fullname
     * @param string $login
     * @param string $password
     * @return bool true si l'ajout est réussi, false sinon.
     */
    public function newUser($name, $fullname, $login, $password)
    {
        $isLogged = $this->sessionManager->isLogged();

        if ($isLogged) {
            $user = $this->dbUserManager->addUser($name, $fullname, $login, $password);
        }

        if ($user) {
            return true;
        }

        return false;
    }

    /**
     * Retourne le nom et le prénom de l'utilisateur de la session.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->sessionManager->getAuthor();
    }
}
?>