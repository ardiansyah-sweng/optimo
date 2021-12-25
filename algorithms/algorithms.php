<?php

interface AlgorithmInterface
{
    function execute($population, $function, $popSize);
}

class Genetic implements AlgorithmInterface
{
    function RouletteWheelSelection($offsprings, $function, $popSize)
    {
        foreach ($offsprings as $individu) {
            $result = (new Functions())->initializingFunction($function);
            $fitness = $result->runFunction($individu, $function);
            $population[] = [
                'fitness' => $fitness,
                'individu' => $individu
            ];
        }
        // 1. sum of fitness value
        $sumOfFitness = array_sum(array_column($population, 'fitness'));

        // 2. calculate individu probability
        foreach ($population as $individu) {
            $probs[] = $individu['fitness'] / $sumOfFitness;
        }

        // 3. calculate cummulative probability
        $probCumm = 0;
        foreach ($probs as $prob) {
            $probCumm = $prob + $probCumm;
            $probsCumm[] = $probCumm;
        }

        // 4. Turn the roullete wheel
        for ($i = 0; $i < $popSize; $i++) {
            $r = (new Randomizers())->randomZeroToOneFraction();
            foreach ($probsCumm as $key => $prob) {
                if ($r <= $prob) {
                    $key;
                    break;
                }
            }
            $newPopulations[] = $population[$key];
        }
        return $newPopulations;
    }

    function execute($population, $function, $popSize)
    {
        // 1. Crossover
        $local = new LocalParameterFactory;
        $parameters = $local->initializingLocalParameter('ga')->getLocalParameter();

        $genSize = count($population[0]['individu']);
        $cutPointIndex = rand(0, $genSize - 1);
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
                $offsprings[] = $mutatedChromosome['individu'];
            }
        }

        // 2. Selection
        return $this->RouletteWheelSelection($offsprings, $function, $popSize);
    }
}

class ParticleSwarmOptimizer implements AlgorithmInterface
{
    function __construct($iter)
    {
        $this->iter = $iter;
    }

    function createInitialVelocities($population)
    {
        foreach ($population as $individu){
            foreach ($individu['individu'] as $key2 => $var){
                $velocities[] = $var * (new Randomizers())->randomZeroToOneFraction();
            }
            $ret[] = $velocities;
            $velocities = [];
        }
        return $ret;
    }

    function execute($population, $function, $popSize)
    {
        $local = new LocalParameterFactory;
        $parameters = $local->initializingLocalParameter('pso')->getLocalParameter();
        
        $r1 = (new Randomizers())->randomZeroToOneFraction();
        $r2 = (new Randomizers())->randomZeroToOneFraction();

        // 1. Calculate inertia weight
        $inertia = $parameters['inertiaMax'] - (( $parameters['inertiaMax'] - $parameters['inertiaMin'] * $this->iter) / $parameters['maxIteration']);

        // 2. Calculate velocity
        $pBests = $population;
        $minFitness = min(array_column($population, 'fitness'));
        $indexIndividu = array_search($minFitness, array_column($population, 'fitness'));
        $gBest = $population[$indexIndividu];

        $velocities = $this->createInitialVelocities($population);
        foreach ($population as $key1 => $individu){
            foreach ($individu['individu'] as $key2 => $var){
                $vels[] = ($inertia * $velocities[$key1][$key2]) + 
                (
                    ($parameters['c1'] * $r1) * ($pBests[$key1]['individu'][$key2] - $var) + ($parameters['c2'] * $r2) * ($gBest['individu'][$key2] - $var)
                );
            }
            $temps[] = $vels;
            $vels = [];
        }
        print_r($temps);die;

        echo $inertia;die;
    }
}

class Algorithms
{
    function initilizingAlgorithm($type, $iter)
    {
        if ($type === 'ga') {
            return new Genetic;
        }
        if ($type === 'pso') {
            return new ParticleSwarmOptimizer($iter);
        }
    }
}
