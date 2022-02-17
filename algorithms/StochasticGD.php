<?php

class StochasticGD
{
    public $normalizedDataset;
    public $learningRate = 0.004;

    function updateWeights($weights, $error)
    {
        foreach ($weights as $weight) {
            $newWeights[] = $weight - $this->learningRate * $error;
        }
        return $newWeights;
    }

    function optimizing($testData, $weights)
    {
        $saveFile = new FileSaver;
        for ($i = 0; $i < 1000; $i++) {
            foreach ($weights as $key => $weight) {
                if ($key > 0) {
                    $result[] = $weight * $testData[$key];
                }
            }
            $estimated = $weights[0] + array_sum($result);
            $error = $estimated - $testData[0];
            $lastWeights = $weights;
            $weights = null;
            $weights = $this->updateWeights($lastWeights, $error);

            if ($error <= 0.009 && $error >= -0.009){
                echo $i . ' ' . $testData[0] . ' ' . $estimated . ' ' . $error ;
                //$saveFile->saveToFile('results\normalSVM.txt', array($testData[0]), //array($estimated), array($error));
                break;
            }
            $result = [];
            // print_r($weights);
            // echo "\n";
        }
    }

    function dataProcessing()
    {
        foreach ($this->normalizedDataset as $testData) {
            foreach (array_keys($testData) as $key) {
                $weights[] = (new Randomizers())->randomZeroToOneFraction();
            }
            echo "\n";
            $this->optimizing($testData, $weights);
            $weights = [];
        }
    }
}
