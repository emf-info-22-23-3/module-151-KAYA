<?php

/**
 * @author Kaya
 */
 
class DBConfig
{
    private $type;
    private $host;
    private $name;
    private $user;
    private $pass;
 
    public function __construct(
        $type = 'mysql',
        $host = 'localhost',
        $name = 'kayae_db_zelda_set',
        $user = 'kayae_elif',
        $pass = 'K&Pm0P6;r.V!'
    ) {
        $this->type = $type;
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->pass = $pass;
    }
 
    public function getType()
    {
        return $this->type;
    }
 
    public function getHost()
    {
        return $this->host;
    }
 
    public function getName()
    {
        return $this->name;
    }
 
    public function getUser()
    {
        return $this->user;
    }
 
    public function getPass()
    {
        return $this->pass;
    }
}
?>
