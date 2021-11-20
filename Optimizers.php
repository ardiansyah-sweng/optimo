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

    function createIndividu()
    {
        //
    }
}
