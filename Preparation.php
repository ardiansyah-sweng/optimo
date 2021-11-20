<?php

class Preparation
{
    private $experimentType;
    private $optimizerAlgorithms;
    private $functionsToOptimized;

    function __construct($experimentType, $optimizerAlgorithms, $functionsToOptimized)
    {
        $this->experimentType = $experimentType;
        $this->optimizerAlgorithms = $optimizerAlgorithms;
        $this->functionsToOptimized = $functionsToOptimized;
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

    function setup()
    {
        $parameters = $this->getVariableAndParameter()['parameter'];
        $variables = $this->getVariableAndParameter()['variable'];

        if ($this->setupIsAllForAll()) {
            foreach ($parameters as $parameter) {
                foreach ($variables as $variable) {
                    echo (new ExperimentFactory())
                        ->initializeExperiment($this->experimentType);
                    echo "\n";
                    print_r($parameter);
                    echo "\n";
                    print_r($variable);
                }
                echo "\n \n";
            }
        }

        if ($this->setupIsAllForOne()) {
            foreach ($parameters as $parameter) {
                echo (new ExperimentFactory())
                    ->initializeExperiment($this->experimentType);
                echo "\n";
                print_r($parameter);
                echo "\n";
                print_r($variables);
                echo "\n";
            }
        }

        if ($this->setupIsOneForAll()) {
            foreach ($variables as $variable) {
                echo (new ExperimentFactory())
                    ->initializeExperiment($this->experimentType);
                echo "\n";
                print_r($parameters);
                echo "\n";
                print_r($variable);
                echo "\n";
            }
        }

        if ($this->setupIsOneForOne()) {
            echo (new ExperimentFactory())
                ->initializeExperiment($this->experimentType);
            echo "\n";
            print_r($parameters[0]);
            echo "\n";
            print_r($variables[0]);
            echo "\n";
        }
    }
}