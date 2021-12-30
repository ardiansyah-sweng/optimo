<?php

class UseCasePoints
{
    function getTestDataValue($testData)
    {
        return [
            $testData['simple'],
            $testData['average'],
            $testData['complex']
        ];
    }

    function calculateUseCase($variableValues, $testDataValues)
    {
        foreach ($variableValues as $key => $variableValue) {
            $ret[] = floatval($variableValue) * floatval($testDataValues[$key]);
        }
        return $ret;
    }

    function estimating($variableValues, $testData)
    {
        $useCases = $this->calculateUseCase($variableValues, $this->getTestDataValue($testData));
        $UUCW = array_sum($useCases);
        $UUCP = $UUCW + $testData['uaw'];
        $UCP = $UUCP * $testData['tcf'] * $testData['ecf'];
        $estimatedEffort = $UCP * $this->productivityFactor;
        return $estimatedEffort;
    }
}
