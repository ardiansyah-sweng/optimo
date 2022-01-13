<?php

class PreyHunting
{
    function __construct($varRanges)
    {
        $this->varRanges = $varRanges;
    }

    function hunting($leaderWolf, $population, $C, $A)
    {   
       $ret = [];
        foreach ($population as $wolves){
            foreach ($wolves['individu'] as $key => $pos){
                $D = abs($C * floatval($leaderWolf['individu'][$key]) - floatval($pos));
                $X[] = floatval($leaderWolf['individu'][$key]) - $A * $D;
            }
            $ret[] = $X;
            $X = [];
        }
        return $ret;
    }
}
