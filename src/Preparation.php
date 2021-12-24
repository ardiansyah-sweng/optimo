<?php

class Preparation
{
    private $experimentType;
    private $optimizerAlgorithms;
    private $functionsToOptimized;
    private $variableType;

    function __construct(string $experimentType, array $optimizerAlgorithms, array $functionsToOptimized, string $variableType)
    {
        $this->experimentType = $experimentType;
        $this->optimizerAlgorithms = $optimizerAlgorithms;
        $this->functionsToOptimized = $functionsToOptimized;
        $this->variableType = $variableType;
    }

    function getVariableAndParameter():array
    {
        foreach ($this->functionsToOptimized as $function) {
            $variables[] = (new Variables())
                ->initializeVariableFactory($function)
                ->getVariables('');
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

        # All Optimizer for All Functions
        if ($this->setupIsAllForAll()) {
            foreach ($parameters as $parameter) {
                foreach ($variables as $variable) {
                    $optimizer = new Initializer(
                        $this->optimizerAlgorithms,
                        $this->functionsToOptimized,
                        $variable['ranges'],
                        $parameters[0]['populationSize'],
                        $this->variableType
                    );

                }
            }
        }

        ## All Optimizer for One Function
        if ($this->setupIsAllForOne()) {
            foreach ($parameters as $parameter) {
                $optimizer = new Initializer(
                    $this->optimizerAlgorithms,
                    $this->functionsToOptimized,
                    $variables[0]['ranges'],
                    $parameter['populationSize'],
                    $this->variableType
                );
                $ret[] = $optimizer->generateInitialPopulation();
            }
            return $ret;
        }

        ## One Optimizer for All Functions
        if ($this->setupIsOneForAll()) {
            foreach ($variables as $variable) {
                $optimizer = new Initializer(
                    $this->optimizerAlgorithms,
                    $this->functionsToOptimized,
                    $variable['ranges'],
                    $parameters[0]['populationSize'],
                    $this->variableType
                );
                $ret[] = $optimizer->generateInitialPopulation();
            }
            return $ret;
        }

        ## One Optimizer for One Function
        if ($this->setupIsOneForOne()) {
            $initializer = new Initializer(
                $this->optimizerAlgorithms, 
                $this->functionsToOptimized, 
                $variables[0]['ranges'], 
                $parameters[0]['populationSize'], 
                $this->variableType
            );
            $optimizer = new Optimizers;
            $optimizer->algorithm = $this->optimizerAlgorithms[0];
            $optimizer->function = $this->functionsToOptimized[0];
            $optimizer->updating($initializer->generateInitialPopulation());
        }
    }
}