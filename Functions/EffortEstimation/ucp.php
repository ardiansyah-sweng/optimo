<?php

class UseCasePoints
{
    function __construct($productivityFactor)
    {
        $this->productivityFactor = $productivityFactor;
    }

    function estimating($variableValues, $testData)
    {
        $simple = $variableValues[0] * floatval($testData['simple']);
        $average = $variableValues[1] * floatval($testData['average']);
        $complex = $variableValues[2] * floatval($testData['complex']);
        $UUCW =  $simple + $average + $complex;
        // $UUCW = $this->calculateUseCase($variableValues, $testData);
        $UUCP = floatval($UUCW) + $testData['uaw'];
        $UCP = $UUCP * $testData['tcf'] * $testData['ecf'];
        return $UCP * $this->productivityFactor;
    }
}
