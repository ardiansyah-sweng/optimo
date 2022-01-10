<?php

class PreyEncircling
{
    function __construct($varRanges)
    {
        $this->varRanges = $varRanges;
    }

    function encircling($alphaWolf, $population, $A)
    {
        $C = 2 * rand(0,1);
        foreach ($population as $wolves){
            foreach ($wolves['individu'] as $key => $val){
                $D = abs($C * $alphaWolf['individu'][$key] - $val);
                //$positions[] = $alphaWolf['individu'][$key] - 
            }
            //print_r($positions);die;
        }
    }
}