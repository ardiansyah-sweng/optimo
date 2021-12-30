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
        // 1. read test dataset
        $data = (new DataProcessor())->initializeDataprocessor('silhavy', 50);
        $testDataset = $data->processingData('Dataset\EffortEstimation\Public\ucp_silhavy.txt');
        
        foreach ($initialPopulation as $individu) {
            $result = (new Functions())->initializingFunction($this->function, $testDataset[0]);
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
