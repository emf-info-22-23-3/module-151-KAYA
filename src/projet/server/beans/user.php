<?php
// Classe représentant un utilisateur du système
class User {
    private $pk;
    private $email;
    private $password;
    private $role;

    // Constructeur pour initialiser les propriétés
    public function __construct($pk, $email, $password, $role) {
        $this->pk = $pk;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    // Getters pour accéder aux propriétés
    public function getPK() { return $this->pk; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }

    // Convertit l'utilisateur en tableau (sans le mot de passe pour des raisons de sécurité)
    public function toArray() {
        return [
            'pk' => $this->pk,
            'email' => $this->email,
            'role' => $this->role
        ];
    }
    
    // Convertit l'utilisateur en XML (mot de passe exclu)
    public function toXML() {
        return '<user>' .
               '<pk>' . htmlspecialchars($this->pk) . '</pk>' .
               '<email>' . htmlspecialchars($this->email) . '</email>' .
               '<role>' . htmlspecialchars($this->role) . '</role>' .
               '</user>';
    }
    
}
