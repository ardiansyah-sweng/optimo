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
        } else {
            return (new VariablesFactory())
                ->initializeVariableFactory($this->functionsToOptimized)
                ->getVariables();
        }
    }

    function getParameters()
    {
        //
    }
}
