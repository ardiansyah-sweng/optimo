<?php

class HuntingOperator
{
    function __construct($varRanges)
    {
        $this->varRanges = $varRanges;
    }

    function es($maxIteration)
    {
        return 2 * rand(-1, 1) * (1 - (1 / $maxIteration));
    }

    function percentageDiffefence($bestReptile, $population)
    {
        foreach ($population as $pop){
            $M_xi = array_sum($pop['individu']) / count($pop['individu']);
            foreach ($pop['individu'] as $key => $val){
                $res = ($val - $M_xi) / ($bestReptile['individu'][$key] * ($this->varRanges['upperBound'] - $this->varRanges['lowerBound']) + 0.0000001);
                $results[] = 0.1 + $res;             
            }
            $ret[] = $results;
            $results = [];
        }
        return $ret;
    }

    function eta($bestReptile, $population)
    {
        $percentDiffs = $this->percentageDiffefence($bestReptile, $population);
        foreach ($percentDiffs as $individu){
            foreach ($individu as $key => $val){
                $results[] = $bestReptile['individu'][$key] * $val;
            }
            $ret[] = $results;
            $results = [];
        }
        return $ret;
    }
}
