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
        $result = (new Functions())->initializingFunction($this->function, $testData);

        foreach ($initialPopulation as $individu) {
            if (count($individu) > 3) {
                foreach ($individu as $variables) {
                    $fitness = $result->runFunction($variables, $this->function);
                    $pops[] = [
                        'fitness' => $fitness,
                        'individu' => $individu
                    ];
                }
            } else {
                $fitness = $result->runFunction($individu, $this->function);
                $pops[] = [
                    'fitness' => $fitness,
                    'individu' => $individu
                ];
            }
        }

        //print_r($pops);die;

        $experiment = (new ExperimentFactory())->initializeExperiment($this->experimentType, $this->algorithm, $pops, $this->function, $this->popsize, $testData);
        return $experiment;
    }
}
