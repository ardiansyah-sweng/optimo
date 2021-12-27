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

    function __construct($optimizerAlgorithms, $functionsToOptimized, $variableRanges, $populationSize, $variableType, $experimentType)
    {
        $this->optimizerAlgorithms = $optimizerAlgorithms;
        $this->functionsToOptimized = $functionsToOptimized;
        $this->populationSize = $populationSize;
        $this->variableRanges = $variableRanges;
        $this->variableType = $variableType;
        $this->experimentType = $experimentType;
    }

    function generateInitialPopulation()
    {
        # initial population based on optimizer algorithm
        if ($this->variableType === 'random') {
            for ($i = 0; $i < $this->populationSize; $i++) {
                $populations[] = (new Randomizers())::randomVariableValueByRange($this->variableRanges);

                $numOfVariable = count($populations[0]);
                $uniform = new UniformInitialization($this->variableRanges, $this->populationSize, $this->variableType, [], $numOfVariable);
                $population[] = $uniform->initializingPopulation();
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

                echo $this->variableType;
                echo "\n";

                $uniform = new UniformInitialization($this->variableRanges, $this->populationSize, $this->variableType, $population, $numOfVariable);
                $uniform->initializingPopulation();

                die;

                if ($this->experimentType === 'evaluation') {
                    $population = [];
                    foreach ($seedFiles as $seedFile) {
                        $population[] = $result->processingData($seedFile);
                    }
                    return $population;
                }
            }
        }

        return $population;
    }
}
