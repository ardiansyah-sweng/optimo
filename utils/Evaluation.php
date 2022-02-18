<?php

class EvaluationMeasure
{
    function __construct($testData, $predictedCluster)
    {
        $this->testData = $testData;
        $this->predictedCluster = $predictedCluster;
    }

    function accuracyCalc()
    {
        foreach ($this->predictedCluster as $tuple){
            if ($this->testData === $tuple){
                return 1;
            }
        }
    }
}