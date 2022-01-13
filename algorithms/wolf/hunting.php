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
                $D = abs($C * $leaderWolf['individu'][$key] - $pos);
                $X[] = $leaderWolf['individu'][$key] - $A * $D;
            }
            $ret[] = $X;
            $X = [];
        }
        return $ret;
    }
}
