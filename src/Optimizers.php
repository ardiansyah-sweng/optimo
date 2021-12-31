<?php
require 'vendor/autoload.php';

class Optimizers
{
    public $algorithm;
    public $parameters;
    public $function;
    public $experimentType;
    public $popsize;

    function updating($initialPopulation, $testData)
    {
        $pops = [];        
        foreach ($initialPopulation as $individu) {
            $result = (new Functions())->initializingFunction($this->function, $testData);
            $fitness = $result->runFunction($individu, $this->function);
            $pops[] = [
                'fitness' => $fitness,
                'individu' => $individu
            ];
        }

        $experiment = (new ExperimentFactory())->initializeExperiment($this->experimentType, $this->algorithm, $pops, $this->function, $this->popsize, $testData);
        return $experiment;
        $pops = [];
    }
}
