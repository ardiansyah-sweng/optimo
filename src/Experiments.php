<?php

require 'vendor/autoload.php';

interface Experiments
{
    function executeExperiment($algorithm, $population, $function, $popSize, $testData);
}

class Normal implements Experiments
{
    function __construct($kmaParameters, $variableRanges)
    {
        $this->kmaParameters = $kmaParameters;
        $this->kmaVarRanges = $variableRanges;
    }

    function run($algorithm, $population, $function, $popSize, $testData)
    {
        $stop = new Stopper;

        for ($iter = 0; $iter < 1000; $iter++) {

            $minFitness = min(array_column($population, 'fitness'));
            $indexIndividu = array_search($minFitness, array_column($population, 'fitness'));

            // jika fitness kurang dari sama dengan 0
            if ($minFitness <= 0) {
                return $population[$indexIndividu];
            }

            // jika fitness lebih besar dari 0
            $bests[] = $population[$indexIndividu];
            $stop->numOfLastResult = 10;
            $lastResults[] = $population[$indexIndividu]['fitness'];

            if ($stop->evaluation($iter, $lastResults)) {
                break;
            }

            $lastPopulation = $population;
            $population = null;

            $algo = (new Algorithms($this->kmaParameters, $this->kmaVarRanges))->initilizingAlgorithm($algorithm, $iter, $testData);
            $population = $algo->execute($lastPopulation, $function, $popSize);
        }

        $minFitness = min(array_column($bests, 'fitness'));
        $indexIndividu = array_search($minFitness, array_column($bests, 'fitness'));

        return $bests[$indexIndividu];
    }

    function executeExperiment($algorithm, $population, $function, $popSize, $testData)
    {
        return $this->run($algorithm, $population, $function, $popSize, $testData);
    }
}

class Convergence extends Normal implements Experiments
{
    function executeExperiment($algorithm, $population, $function, $popSize, $testData)
    {
        return "Convergence";
    }
}

class Evaluation extends Normal implements Experiments
{
    function executeExperiment($algorithm, $population, $function, $popSize, $testData)
    {
        return $this->run($algorithm, $population, $function, $popSize, $testData)['fitness'];
    }
}

class ExperimentFactory
{
    function __construct($kmaParameters, $variableRanges)
    {
        $this->kmaParameters = $kmaParameters;
        $this->kmaVarRanges = $variableRanges;
    }

    function initializeExperiment($type, $algorithm, $population, $function, $popSize, $testData)
    {
        if ($type === 'normal') {
            return (new Normal($this->kmaParameters, $this->kmaVarRanges))->executeExperiment($algorithm, $population, $function, $popSize, $testData);
        }
        if ($type === 'convergence') {
            return (new Convergence($this->kmaParameters, $this->kmaVarRanges))->executeExperiment($algorithm, $population, $function, $popSize, $testData);
        }
        if ($type === 'evaluation') {
            return (new Evaluation($this->kmaParameters, $this->kmaVarRanges))->executeExperiment($algorithm, $population, $function, $popSize, $testData);
        }
    }
}
