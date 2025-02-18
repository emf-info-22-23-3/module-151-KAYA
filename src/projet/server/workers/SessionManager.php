<?php
/**
 * @author Kaya
 */

// Quand même vérifier si une session n'existe pas déjà afin de ne pas générer d'erreurs ou d'avertissements 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class SessionManager
{
    /**
     * Enregistre l'utilisateur en session.
     *
     * @param User $user L'objet utilisateur qui vient d'être authentifié.
     */
    public function login($user)
    {
        $_SESSION['logged'] = $user->getEmail();
        $_SESSION['isAdmin'] = $user->getRole();
    }

    /**
     * Déconnecte l'utilisateur en supprimant les informations de session.
     */
    public function logout()
    {
        // Vider le tableau des variables de la session
        $_SESSION = array();

        // Détruire la session
        session_destroy();
    }

    /**
     * Vérifie si l'utilisateur est connecté.
     *
     * @return bool
     */
    public function isLogged()
    {
        return isset($_SESSION['logged']);
    }

    /**
     * Retourne l'email et le role de l'utilisateur de la session.
     *
     * @return string
     */
    public function getAuthor()
    {
        $email = $_SESSION['email'];
        return $email;
    }

        /**
     * Retourne l'id de l'utilisateur de la session.
     *
     * @return int
     */
    public function getId()
    {
        $id = $_SESSION['id'];

        return $id;
    }
}
?>