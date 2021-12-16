<?php

require 'vendor/autoload.php';

interface Experiments
{
    function executeExperiment();
}

class Normal implements Experiments
{
    function executeExperiment()
    {
        return "normal";
    }
}

class Convergence extends Normal implements Experiments
{
    function executeExperiment()
    {
        return "Convergence";
    }
}

class Evaluation extends Normal implements Experiments
{
    function executeExperiment()
    {
        return "evaluation";
    }
}

class ExperimentFactory
{
    function initializeExperiment($type)
    {
        if ($type === 'normal') {
            return (new Normal())->executeExperiment();
        }
        if ($type === 'convergence') {
            return (new Convergence())->executeExperiment();
        }
        if ($type === 'evaluation') {
            return (new Evaluation())->executeExperiment();
        }
    }
}