<?php
class Ctrl
{
    public function getEquipes()
    {
        require('Wrk.php');
        $wrk = new Wrk();
        $equipes = $wrk->getEquipesFromDB();
        return $equipes;
    }
}
?>