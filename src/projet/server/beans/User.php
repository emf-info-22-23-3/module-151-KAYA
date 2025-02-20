<?php
class User {
    private $pk;
    private $email;
    private $password;
    private $role;

    public function __construct($pk, $email, $password, $role) {
        $this->pk = $pk;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
    }

    // Getters
    public function getPK() { return $this->pk; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getRole() { return $this->role; }

    public function toArray() {
        return [
            'pk' => $this->pk,
            'email' => $this->email,
            'role' => $this->role
        ];
    }
}