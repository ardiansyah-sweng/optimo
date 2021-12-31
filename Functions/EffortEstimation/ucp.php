<?php

class UseCasePoints
{
    function __construct($productivityFactor)
    {
        $this->productivityFactor = $productivityFactor;
    }

    function estimating($variableValues, $testData)
    {
        $ret = [];
        foreach ((array) $testData as $key => $val){
            if ($key === 'simple'){
                $ret[] = intval($val);
            }
            if ($key === 'average') {
                $ret[] = intval($val);
            }
            if ($key === 'complex') {
                $ret[] = intval($val);
            }
            if ($key === 'uaw') {
                $ret[] = floatval($val);
            }
            if ($key === 'tcf'){
                $ret[] = floatval($val);
            }
            if ($key === 'ecf') {
                $ret[] = floatval($val);
            }
            if ($key === 'actualEffort') {
                $ret[] = floatval($val);
            }
        }
        $tes = [];
        foreach ($ret as $key => $val){
            if ($key <= 2){
                $tes[] = $variableValues[$key] * $val;
            }
        }
        
        $UUCW =  array_sum($tes);

        foreach ($ret as $key => $val) {
            if ($key == 3) {
                $UUCP = $UUCW + $val;
            }
        }

        foreach ($ret as $key => $val) {
            if ($key == 4) {
                $temp = $UUCP * $val;
            }
        }

        $UCP = null;
        foreach ($ret as $key => $val) {
            if ($key == 5) {
                $UCP = $temp * $val;
            }
        }
        $estimatedEffort = $UCP * $this->productivityFactor;

        foreach ($ret as $key => $val) {
            if ($key == 6) {
                return abs($estimatedEffort - $val);
            }
        }
    }
}
