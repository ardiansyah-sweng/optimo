<?php
require 'vendor/autoload.php';

class Optimizers
{
    private $optimizerAlgorithms;
    private $functionsToOptimized;

    function __construct($optimizerAlgorithms, $functionsToOptimized)
    {
        $this->optimizerAlgorithms = $optimizerAlgorithms;
        $this->functionsToOptimized = $functionsToOptimized;
    }

    function getVariables()
    {
        if (is_array($this->functionsToOptimized)) {
            foreach ($this->functionsToOptimized as $function) {
                $ret[] = (new VariablesFactory())
                    ->initializeVariableFactory($function)
                    ->getVariables();
            }
            return $ret;
        }
    }

    function getParameters()
    {
        if (is_array($this->optimizerAlgorithms)) {
            foreach ($this->optimizerAlgorithms as $optimizer) {
                $ret[] = (new LocalParameterFactory())
                    ->initializingLocalParameter($optimizer)
                    ->getLocalParameter();
            }
            return $ret;
        } else {
            return (new LocalParameterFactory())
                ->initializingLocalParameter($this->optimizerAlgorithms)
                ->getLocalParameter();
        }
    }

    function createIndividu()
    {
        if (is_array($this->getVariables())) {
                print_r($this->getVariables());
                echo "\n";
        } else {
            return rand($this->functionsToOptimized['ranges']['lowerBound'], $this->functionsToOptimized['ranges']['upperBound']);
        }
    }
}
