<?php

class PreyHunting
{
    function __construct($varRanges)
    {
        $this->varRanges = $varRanges;
    }

    function hunting($leaderWolf, $population, $A, $a)
    {
        $C = 2 * (new Randomizers())->randomZeroToOneFraction();
        
        foreach ($A as $val) {
            $coefA[] = 2 * $val * rand(0, 1) - $a;
        }

        foreach ($population as $wolves) {
            foreach ($wolves['individu'] as $key => $position) {
                $D = abs($C * $leaderWolf['individu'][$key] - $position);
                $positions[] = $leaderWolf['individu'][$key] - $coefA[$key] * $D;
            }
            $ret[] = $positions;
            $positions = [];
        }
        return $ret;
    }
}
