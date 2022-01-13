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
        foreach ($population as $pop) {
            $M_xi = array_sum($pop['individu']) / count($pop['individu']);
            foreach ($pop['individu'] as $key => $val) {
                $res = (floatval($val) - $M_xi) / (floatval($bestReptile['individu'][$key]) * ($this->varRanges['upperBound'] - $this->varRanges['lowerBound']) + 0.0000001);
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
        foreach ($percentDiffs as $individu) {
            foreach ($individu as $key => $val) {
                $results[] = floatval($bestReptile['individu'][$key]) * floatval($val);
            }
            $ret[] = $results;
            $results = [];
        }
        return $ret;
    }

    function reduce($bestReptile, $population)
    {
        foreach ($population as $pop){
            $r2 = (new Randomizers())->getRandomIndexOfIndividu(count($population));
            $x_r2j = $population[$r2];

            foreach ($x_r2j['individu'] as $key => $val){
                $results[] = (floatval($bestReptile['individu'][$key]) - floatval($val)) / (floatval($bestReptile['individu'][$key]) + 0.0000001);
            }
            $ret[] = $results;
            $results = [];
        }
        return $ret;
    }
}
