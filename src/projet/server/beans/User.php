<?php
class User {
    private $id;
    private $email;
    private $password;
    private $roleId;

    public function __construct($id, $email, $password, $roleId) {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->roleId = $roleId;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getRoleId() { return $this->roleId; }

    public function toArray() {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'roleId' => $this->roleId
        ];
    }
}