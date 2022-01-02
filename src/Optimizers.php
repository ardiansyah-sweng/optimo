<?php
require 'vendor/autoload.php';

class Optimizers
{
    public $algorithm;
    public $parameters;
    public $function;
    public $experimentType;
    public $popsize;
    public $variableType;

    function updating($initialPopulation, $testData)
    {
        $pops = [];
        $result = (new Functions())->initializingFunction($this->function, $testData);

        foreach ($initialPopulation as $individu) {
            $fitness = $result->runFunction($individu, $this->function);
            $pops[] = [
                'fitness' => $fitness,
                'individu' => $individu
            ];
        }

        sort($pops);

        $experiment = (new ExperimentFactory())->initializeExperiment($this->experimentType, $this->algorithm, $pops, $this->function, $this->popsize, $testData);
        return $experiment;
    }
}
