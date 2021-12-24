<?php
require 'vendor/autoload.php';

class Optimizers
{
    public $algorithm;
    public $parameters;
    public $function;
    private $populationSize;
    private $variableType;


    function updating($initialPopulation)
    {
        print_r($this->algorithm);
        $parameters = (new LocalParameterFactory())
                ->initializingLocalParameter($this->algorithm)
                ->getLocalParameter();
        echo "\n";
        print_r($parameters);
        echo "\n";
        print_r($initialPopulation);
        echo "\n";

        foreach ($initialPopulation as $population){
            $result = (new Functions())->initializingFunction($this->function);
            $fitness = $result->runFunction($population, $this->function);
            $pops[] = [
                'fitness' => $fitness,
                'individu' => $population
            ];
        }
        return $pops;
    }
}