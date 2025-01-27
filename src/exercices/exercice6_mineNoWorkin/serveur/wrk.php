<?php

class Wrk
{
    public function getEquipesFromDB()
    {
        // Associative array with IDs for XML generation
        return [
            ["id" => 1, "nom" => "Gotteron"],
            ["id" => 2, "nom" => "SC Bern"],
            ["id" => 3, "nom" => "Fribourg-Gottéron"],
            ["id" => 4, "nom" => "HC Davos"]
        ];
    }
}
?>
