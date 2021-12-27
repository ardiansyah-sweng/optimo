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
        if ($this->variableType === 'random'){
            for ($i = 0; $i < $this->populationSize; $i++) {
                $population[] = (new Randomizers())::randomVariableValueByRange($this->variableRanges);
            }
        }

        if ($this->variableType === 'seeds') {
            foreach ($this->functionsToOptimized as $function){
                $scan = new DirectoryScanner;
                $path = (new Variables())->initializeVariableFactory($function);
                $scan->pathToDirectory = $path->getVariables('seeds');
                $seedFiles = $scan->getFileNames();

                $dataProcessor = new DataProcessor;
                $result = $dataProcessor->initializeDataprocessor('seeds', $this->populationSize);
                $population = $result->processingData($seedFiles[1]);

                if ($this->experimentType === 'evaluation'){
                    $population=[];
                    foreach($seedFiles as $seedFile){
                        $population[] = $result->processingData($seedFile);
                    }
                    return $population;
                }
            }   
        }

        # for uniform swarm initialization
        // foreach ($this->optimizerAlgorithms as $optimizer) {
        //     if ($optimizer === 'ucpso' || $optimizer === 'mucpso') {
        //         echo (new UniformFactory())->initializingUniform($optimizer);
        //     }
        // }
        return $population;
    }
}