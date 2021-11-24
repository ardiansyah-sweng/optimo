<?php

class Preparation
{
    private $experimentType;
    private $optimizerAlgorithms;
    private $functionsToOptimized;
    private $variableType;

    function __construct($experimentType, $optimizerAlgorithms, $functionsToOptimized, $variableType)
    {
        $this->experimentType = $experimentType;
        $this->optimizerAlgorithms = $optimizerAlgorithms;
        $this->functionsToOptimized = $functionsToOptimized;
        $this->variableType = $variableType;
    }

    function getVariableAndParameter()
    {
        foreach ($this->functionsToOptimized as $function) {
            $variables[] = (new VariablesFactory())
                ->initializeVariableFactory($function)
                ->getVariables();
        }
        foreach ($this->optimizerAlgorithms as $optimizer) {
            $parameters[] = (new LocalParameterFactory())
                ->initializingLocalParameter($optimizer)
                ->getLocalParameter();
        }
        return [
            'parameter' => $parameters,
            'variable' => $variables
        ];
    }

    function setupIsAllForAll()
    {
        if (count($this->optimizerAlgorithms) > 1 && count($this->functionsToOptimized) > 1) {
            return true;
        }
    }

    function setupIsAllForOne()
    {
        if (count($this->optimizerAlgorithms) > 1 && count($this->functionsToOptimized) === 1) {
            return true;
        }
    }

    function setupIsOneForAll()
    {
        if (count($this->optimizerAlgorithms) === 1 && count($this->functionsToOptimized) > 1) {
            return true;
        }
    }

    function setupIsOneForOne()
    {
        if (count($this->optimizerAlgorithms) === 1 && count($this->functionsToOptimized) === 1) {
            return true;
        }
    }

    function variableIsMoreThanTwo($numOfVariable)
    {
        if ($numOfVariable > 2){
            return true;
        }
    }

    function setup()
    {
        $parameters = $this->getVariableAndParameter()['parameter'];
        $variables = $this->getVariableAndParameter()['variable'];
        print_r($this->getVariableAndParameter());die;

        if ($this->setupIsAllForAll()) {
            foreach ($parameters as $parameter) {
                foreach ($variables as $variable) {
                    // echo (new ExperimentFactory())
                    //     ->initializeExperiment($this->experimentType);
                    // echo "\n";
                    // print_r($parameter);
                    // echo "\n";
                    // print_r($variable);

                    if ($this->variableIsMoreThanTwo($variables['numOfVariables'])){
                        ## fixed one variable, and three variables
                    }

                    // $optimizer = new Optimizers(
                    //     $this->optimizerAlgorithms,
                    //     $this->functionsToOptimized,
                    //     $variable['ranges'],
                    //     $parameters[0]['populationSize'],
                    //     $this->variableType
                    // );
                    // $optimizer->generateInitialPopulation();
                }
                echo "\n \n";
            }
        }

        ## All Optimizer for One Function
        if ($this->setupIsAllForOne()) {
            foreach ($parameters as $parameter) {
                $optimizer = new Optimizers(
                    $this->optimizerAlgorithms,
                    $this->functionsToOptimized,
                    $variables[0]['ranges'],
                    $parameter['populationSize'],
                    $this->variableType
                );
                $optimizer->generateInitialPopulation();
            }
        }

        ## One Optimizer for All Functions
        if ($this->setupIsOneForAll()) {
            foreach ($variables as $variable) {
                $optimizer = new Optimizers(
                    $this->optimizerAlgorithms,
                    $this->functionsToOptimized,
                    $variable['ranges'],
                    $parameters[0]['populationSize'],
                    $this->variableType
                );
                $optimizer->generateInitialPopulation();
            }
        }

        ## One Optimizer for One Function
        if ($this->setupIsOneForOne()) {
            $optimizer = new Optimizers(
                $this->optimizerAlgorithms, 
                $this->functionsToOptimized, 
                $variables[0]['ranges'], 
                $parameters[0]['populationSize'], 
                $this->variableType
            );
            $optimizer->generateInitialPopulation();
        }
    }
}