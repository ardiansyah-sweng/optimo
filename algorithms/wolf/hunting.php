<?php

class PreyHunting
{
    function __construct($varRanges)
    {
        $this->varRanges = $varRanges;
    }

    function calculateCoef_A($last_A, $a)
    {
        foreach ($last_A as $val){
            $coefA[] = 2 * $val * (new Randomizers())::randomZeroToOneFraction() - $a;
        }
        return $coefA;
    }

    function hunting($leaderWolf, $population, $C, $A)
    {   
       $ret = [];
        foreach ($population as $wolves){
            foreach ($wolves['individu'] as $key => $pos){
                $D = abs($C * $leaderWolf['individu'][$key] - $pos);
                $X[] = $leaderWolf['individu'][$key] - $A[$key] - $D;
            }
            $ret[] = $X;
            $X = [];
        }
        return $ret;
    }
}
