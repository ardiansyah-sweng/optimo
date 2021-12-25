<?php

require 'vendor/autoload.php';

interface Experiments
{
    function executeExperiment($algorithm, $population, $function);
}

class Normal implements Experiments
{
    function executeExperiment($algorithm, $population, $function)
    {
        $algo = (new Algorithms())->initilizingAlgorithm($algorithm);
        $algo->execute($population, $function);
    }
}

class Convergence extends Normal implements Experiments
{
    function executeExperiment($algorithm, $population, $function)
    {
        return "Convergence";
    }
}

class Evaluation extends Normal implements Experiments
{
    function executeExperiment($algorithm, $population, $function)
    {
        return "evaluation";
    }
}

class ExperimentFactory
{
    function initializeExperiment($type, $algorithm, $population, $function)
    {
        if ($type === 'normal') {
            return (new Normal())->executeExperiment($algorithm, $population, $function);
        }
        if ($type === 'convergence') {
            return (new Convergence())->executeExperiment($algorithm, $population, $function);
        }
        if ($type === 'evaluation') {
            return (new Evaluation())->executeExperiment($algorithm, $population, $function);
        }
    }
}
