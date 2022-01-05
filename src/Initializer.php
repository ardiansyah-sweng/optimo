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
            if ($this->optimizerAlgorithms[0] === 'ucpso') {
                $uniform = new UniformInitialization($this->variableRanges, $this->populationSize, $this->variableType, [], $this->numOfVariable, $this->functionsToOptimized[0]);
                $population = $uniform->initializingPopulation();
            } else {
                for ($i = 0; $i < $this->populationSize; $i++) {
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
                $population = $result->processingData($seedFiles[0]);

                if ($this->experimentType === 'evaluation' && ($this->optimizerAlgorithms[0] === 'pso' || $this->optimizerAlgorithms[0] === 'mypso2' || $this->optimizerAlgorithms[0] === 'ga' || $this->optimizerAlgorithms[0] === 'komodo')) {
                    $population = [];
                    foreach ($seedFiles as $seedFile) {
                        $population[] = $result->processingData($seedFile);
                    }
                    return $population;
                }

                if ($this->experimentType === 'evaluation' && ($this->optimizerAlgorithms[0] === 'ucpso' || $this->optimizerAlgorithms[0] === 'mypso1' || $this->optimizerAlgorithms[0] === 'mypso3')) {
                    $population = [];
                    foreach ($seedFiles as $seedFile) {
                        $pops = $result->processingData($seedFile);
                        $uniform = new UniformInitialization($this->variableRanges, $this->populationSize, $this->variableType, $pops, $this->numOfVariable, $this->functionsToOptimized[0]);
                        $population[] = $uniform->initializingPopulation();
                    }
                    return $population;
                }
            }
        }

        return $population;
    }
}
