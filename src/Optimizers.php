<?php
require 'vendor/autoload.php';

class Optimizers
{
    public $algorithm;
    public $parameters;
    public $function;
    public $experimentType;
    public $popsize;

    function updating($initialPopulation)
    {
        $pops = [];
        foreach ($initialPopulation as $individu) {
            $result = (new Functions())->initializingFunction($this->function);
            $fitness = $result->runFunction($individu, $this->function);
            $pops[] = [
                'fitness' => $fitness,
                'individu' => $individu
            ];
        }
        $experiment = (new ExperimentFactory())->initializeExperiment($this->experimentType, $this->algorithm, $pops, $this->function, $this->popsize);
        return $experiment;
        $pops = [];
    }
}
