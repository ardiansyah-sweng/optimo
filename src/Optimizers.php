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
    public $variableRanges;
    public $maxIter;
    public $klasterSets;

    function updating($initialPopulation, $testData)
    {
        echo $this->popsize;
        echo "\n";

        $pops = [];
        $result = (new Functions())->initializingFunction($this->function, $testData, $this->klasterSets);

        foreach ($initialPopulation as $individu) {
            $fitness = $result->runFunction($individu, $this->function);
            $pops[] = [
                'fitness' => $fitness,
                'individu' => $individu
            ];
        }
        sort($pops);

        if ($this->experimentType === 'convergence'){
            $maxIter = $this->maxIter;
        } else {
            $maxIter = 3;
        }

        if ($this->function === 'ucpSVMZhou'){
            $experiment = (new ExperimentFactory($this->parameters, $this->variableRanges, $maxIter, $this->klasterSets))->initializeExperiment($this->experimentType, $this->algorithm, $pops, $this->function, $this->popsize, $testData);
        } else {
            $experiment = (new ExperimentFactory($this->parameters, $this->variableRanges, $maxIter,''))->initializeExperiment($this->experimentType, $this->algorithm, $pops, $this->function, $this->popsize, $testData);
        }
        return $experiment;
    }
}
