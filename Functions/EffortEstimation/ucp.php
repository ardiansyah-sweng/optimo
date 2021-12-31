<?php

class UseCasePoints
{
    function __construct($productivityFactor)
    {
        $this->productivityFactor = $productivityFactor;
    }

    function estimating($variableValues, $testData)
    {
        //var_dump($testData);die;
        //print_r(is_int(intval($testData['simple'])));die;
        $simple = $variableValues[0] * $testData['simple'];
        $average = $variableValues[1] * $testData['average'];
        $complex = $variableValues[2] * $testData['complex'];
        $UUCW =  $simple + $average + $complex;
        // $UUCW = $this->calculateUseCase($variableValues, $testData);
        $UUCP = $UUCW + $testData['uaw'];
        $UCP = $UUCP * $testData['tcf'] * $testData['ecf'];
        return $UCP * $this->productivityFactor;
    }
}
