<?php

require 'vendor/autoload.php';

interface Experiments
{
    function executeExperiment($algorithm, $population, $function, $popSize, $testData);
}

class Normal implements Experiments
{
    function __construct($kmaParameters, $variableRanges, $maxIter, $klasterSets)
    {
        $this->kmaParameters = $kmaParameters;
        $this->kmaVarRanges = $variableRanges;
        $this->maxIter = $maxIter;
        $this->klasterSets = $klasterSets;
    }

    function run($algorithm, $population, $function, $popSize, $testData)
    {
        $stop = new Stopper;

        for ($iter = 0; $iter < $this->maxIter; $iter++) {
            $minFitness = max(array_column($population, 'fitness'));
            $indexIndividu = array_search($minFitness, array_column($population, 'fitness'));

            // jika fitness kurang dari sama dengan 0
            if ($minFitness <= 0) {
                return $population[$indexIndividu];
            }

            // jika fitness lebih besar dari 0
            $bests[] = $population[$indexIndividu];
            $stop->numOfLastResult = 10;

            if (count($population[$indexIndividu]) === 1) {
                $lastResults[] = $population[0][$indexIndividu]['fitness'];
            } else {
                $lastResults[] = $population[$indexIndividu]['fitness'];
            }

            if ($stop->evaluation($iter, $lastResults) && $algorithm !== 'komodo') {
                break;
            }

            $lastPopulation = $population;
            $population = null;
            sort($lastPopulation);

            // print_r($lastPopulation);
            // echo " last \n";

            if ($stop->evaluationKMA($iter, $lastResults) === 'dec' && $algorithm === 'komodo') {
                for ($i = 0; $i < 5; $i++) {
                    array_pop($lastPopulation);
                }

                if (count($lastPopulation) === 0) {
                    break;
                }
            }

            if ($stop->evaluationKMA($iter, $lastResults) === 'add' && $algorithm === 'komodo') {
                for ($i = 0; $i < 5; $i++) {
                    $additionalVars = (new Randomizers())::randomVariableValueByRange($this->kmaVarRanges);
                    if ($function === 'ucpSVMZhou') {
                        $result = (new Functions())->initializingFunction($function, '', $this->klasterSets);
                    } else {
                        $result = (new Functions())->initializingFunction($function, '', '');
                    }
                    $fitness = $result->runFunction($additionalVars, $function);
                    $additionalIndividus[] = [
                        'fitness' => $fitness,
                        'individu' => $additionalVars
                    ];
                }
                $lastPopulation = array_merge($lastPopulation, $additionalIndividus);
            }

            $algo = (new Algorithms($this->kmaParameters, $this->kmaVarRanges, $this->klasterSets))->initilizingAlgorithm($algorithm, $iter, $testData);
            $population = $algo->execute($lastPopulation, $function, $popSize);
        }

        $minFitness = max(array_column($bests, 'fitness'));
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
        $result = $this->run($algorithm, $population, $function, $popSize, $testData);
        if (count($result) === 1) {
            return $result[0]['fitness'];
        }
        return $result['fitness'];
    }
}

class Evaluation extends Normal implements Experiments
{
    function executeExperiment($algorithm, $population, $function, $popSize, $testData)
    {
        $result = $this->run($algorithm, $population, $function, $popSize, $testData);
        if ($function === 'ucpSVMZhou') {
            if (count($result) === 1) {
                return $result[0];
            }
            return $result;
        } else {
            if (count($result) === 1) {
                return $result[0]['fitness'];
            }
            return $result['fitness'];
        }
    }
}

class ExperimentFactory
{
    function __construct($kmaParameters, $variableRanges, $maxIter, $klasterSets)
    {
        $this->kmaParameters = $kmaParameters;
        $this->kmaVarRanges = $variableRanges;
        $this->maxIter = $maxIter;
        $this->klasterSets = $klasterSets;
    }

    function initializeExperiment($type, $algorithm, $population, $function, $popSize, $testData)
    {
        if ($type === 'normal') {
            return (new Normal($this->kmaParameters, $this->kmaVarRanges, $this->maxIter, $this->klasterSets))->executeExperiment($algorithm, $population, $function, $popSize, $testData);
        }
        if ($type === 'convergence') {
            return (new Convergence($this->kmaParameters, $this->kmaVarRanges, $this->maxIter, $this->klasterSets))->executeExperiment($algorithm, $population, $function, $popSize, $testData);
        }
        if ($type === 'evaluation') {
            return (new Evaluation($this->kmaParameters, $this->kmaVarRanges, $this->maxIter, $this->klasterSets))->executeExperiment($algorithm, $population, $function, $popSize, $testData);
        }
    }
}
