<?php
require 'vendor/autoload.php';

class Optimizers
{
    private $optimizerAlgorithms;
    private $functionsToOptimized;
    private $variableRanges;
    private $populationSize;
    private $variableType;

    function __construct($optimizerAlgorithms, $functionsToOptimized, $variableRanges, $populationSize, $variableType)
    {
        $this->optimizerAlgorithms = $optimizerAlgorithms;
        $this->functionsToOptimized = $functionsToOptimized;
        $this->populationSize = $populationSize;
        $this->variableRanges = $variableRanges;
        $this->variableType = $variableType;
    }

    function createIndividu()
    {
        # variable type: random OR seed
        if ($this->variableType === 'random'){
            return (new Randomizers())->randomVariableValueByRange($this->variableRanges);
        }
        
        if ($this->variableType === 'seeds') {
           return 'seeds';
        }
    }

    function generateInitialPopulation()
    {
        # initial population based on optimizer algorithm
        for ($i=0; $i<$this->populationSize; $i++){
            $population[] = $this->createIndividu();
        }
        print_r($population);
    }
}