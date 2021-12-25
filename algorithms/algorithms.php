<?php

interface AlgorithmInterface
{
    function execute($population, $function);
}

class Genetic implements AlgorithmInterface
{
    function RouletteWheelSelection()
    {
        //
    }

    function execute($population, $function)
    {
        // 1. Crossover
        $local = new LocalParameterFactory;
        $parameters = $local->initializingLocalParameter('ga')->getLocalParameter();

        $genSize = count($population[0]['individu']);
        $cutPointIndex = rand(0, $genSize-1);
        $crossover = new Crossover($parameters['populationSize'], $cutPointIndex, $genSize);
        $crossover->crossoverRate = $parameters['cr'];
        $offsprings = $crossover->runCrossover($population);

        // 2. Mutation
        $mutation = new Mutation;
        $mutation->numOfGen = $genSize;
        $mutatedChromosomes = $mutation->runMutation($population, $parameters['populationSize'], $function);

        // Jika ada hasil mutasi, maka gabungkan chromosomes offspring dengan hasil chromosome mutasi
        if (count($mutatedChromosomes) > 0) {
            foreach ($mutatedChromosomes as $mutatedChromosome) {
                $offsprings[] = $mutatedChromosome;
            }
        }

        // 2. Selection
    }
}

class ParticleSwarmOptimizer implements AlgorithmInterface
{
    function execute($population, $function)
    {
        $local = new LocalParameterFactory;
        $parameters = $local->initializingLocalParameter('pso')->getLocalParameter();
        print_r($parameters);
    }
}

class Algorithms
{
    function initilizingAlgorithm($type)
    {
        if ($type === 'ga'){
            return new Genetic;
        }
        if ($type === 'pso'){
            return new ParticleSwarmOptimizer;
        }
    }
}