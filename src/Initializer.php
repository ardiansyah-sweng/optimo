<?php
require 'vendor/autoload.php';

class Initializer
{
    private $optimizerAlgorithms;
    private $functionsToOptimized;
    private $variableRanges;
    private $populationSize;
    private $variableType;
    private $experimentType;
    public $dimension;

    function __construct($optimizerAlgorithms, $functionsToOptimized, $variableRanges, $populationSize, $variableType, $experimentType, int $numOfVariable)
    {
        $this->optimizerAlgorithms = $optimizerAlgorithms;
        $this->functionsToOptimized = $functionsToOptimized;
        $this->populationSize = $populationSize;
        $this->variableRanges = $variableRanges;
        $this->variableType = $variableType;
        $this->experimentType = $experimentType;
        $this->numOfVariable = $numOfVariable;
    }

    function generateInitialPopulation()
    {
        # initial population based on optimizer algorithm
        if ($this->variableType === 'random') {
            for ($i = 0; $i < $this->populationSize; $i++) {
                if ($this->optimizerAlgorithms[0] === 'ucpso') {
                    $uniform = new UniformInitialization($this->variableRanges, $this->populationSize, $this->variableType, [], $this->numOfVariable);
                    $population[] = $uniform->initializingPopulation();
                } else {
                    $population[] = (new Randomizers())::randomVariableValueByRange($this->variableRanges);
                }
            }
        }

        if ($this->variableType === 'seeds') {
            foreach ($this->functionsToOptimized as $function) {
                $scan = new DirectoryScanner;
                $path = (new Variables())->initializeVariableFactory($function);
                $scan->pathToDirectory = $path->getVariables('seeds');
                $seedFiles = $scan->getFileNames();

                $dataProcessor = new DataProcessor;
                $result = $dataProcessor->initializeDataprocessor('seeds', $this->populationSize);
                $population = $result->processingData($seedFiles[1]);

                $uniform = new UniformInitialization($this->variableRanges, $this->populationSize, $this->variableType, $population, $this->numOfVariable);
                $uniform->initializingPopulation();

                if ($this->experimentType === 'evaluation') {
                    $population = [];
                    foreach ($seedFiles as $seedFile) {
                        $population[] = $result->processingData($seedFile);
                    }
                    return $population;
                }
            }
        }
        //print_r($population);die;
        return $population;
    }
}
