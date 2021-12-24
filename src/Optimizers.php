<?php
require 'vendor/autoload.php';

class Optimizers
{
    public $algorithm;
    public $parameters;
    private $variableRanges;
    private $populationSize;
    private $variableType;


    function updating()
    {
        print_r($this->algorithm);
        $parameters = (new LocalParameterFactory())
                ->initializingLocalParameter($this->algorithm)
                ->getLocalParameter();
        echo "\n";
        print_r($parameters);
    }

}