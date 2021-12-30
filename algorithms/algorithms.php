<?php

use Utils\ChaoticFactory;

interface AlgorithmInterface
{
    function execute($population, $function, $popSize);
}

class Genetic implements AlgorithmInterface
{
    function RouletteWheelSelection($offsprings, $function, $popSize)
    {
        $data = (new DataProcessor())->initializeDataprocessor('silhavy', 50);
        $testDataset = $data->processingData('Dataset\EffortEstimation\Public\ucp_silhavy.txt');

        foreach ($offsprings as $individu) {
            $result = (new Functions())->initializingFunction($function, $testDataset[0]);
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
        $crossover = new Crossover($parameters['populationSize'], $cutPointIndex, $genSize, $function);
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
    function __construct($iter, $algorithm)
    {
        $this->iter = $iter;
        $this->algorithm = $algorithm;
    }

    function createInitialVelocities($population)
    {
        foreach ($population as $individu) {
            foreach ($individu['individu'] as $var) {
                $velocities[] = floatval($var) * (new Randomizers())->randomZeroToOneFraction();
            }
            $ret[] = $velocities;
            $velocities = [];
        }
        return $ret;
    }

    function updateVelocity($individu, $velocities, $pBest, $gBest, $population, $particles, $chaoticValue)
    {
        $local = new LocalParameterFactory;
        $parameters = $local->initializingLocalParameter('pso')->getLocalParameter();

        $r1 = (new Randomizers())->randomZeroToOneFraction();
        $r2 = (new Randomizers())->randomZeroToOneFraction();

        if ($this->algorithm === 'mypso3') {
            $r1 = $chaoticValue;
            $r2 = $chaoticValue;
        }
        
        // 1. Calculate inertia weight
        $inertia = $parameters['inertiaMax'] - (($parameters['inertiaMax'] - $parameters['inertiaMin'] * $this->iter) / $parameters['maxIteration']);

        if ($this->algorithm === 'ucpso' || $this->algorithm === 'mypso1' || $this->algorithm === 'mypso3') {
            $rankBasedInertia = new RankBased($chaoticValue, $population, $particles, $parameters['populationSize']);
            $parameterSet = [
                'inertiaMax' => $parameters['inertiaMax'],
                'inertiaMin' => $parameters['inertiaMin']
            ];
            $inertia = $rankBasedInertia->inertiaWeighting($parameterSet, $this->iter, $parameters['maxIteration']);
        }

        foreach ($individu as $key => $var) {
            $ret[] = ($inertia * $velocities[$key]) +
                (
                    ($parameters['c1'] * $r1) * (floatval($pBest[$key]) - floatval($var)) +
                    ($parameters['c2'] * $r2) * (floatval($gBest[$key]) - floatval($var))
                );
        }
        return $ret;
    }

    function execute($population, $function, $popSize)
    {
        $local = new LocalParameterFactory;
        if ($this->algorithm === 'pso' || $this->algorithm === 'mypso2' || $this->algorithm === 'mypso3') {
            $parameters = $local->initializingLocalParameter('pso')->getLocalParameter();
        }
        if ($this->algorithm === 'ucpso' || $this->algorithm === 'mypso1') {
            $parameters = $local->initializingLocalParameter('ucpso')->getLocalParameter();
        }

        if ($this->iter === 0) {
            $minFitness = min(array_column($population, 'fitness'));
            $indexIndividu = array_search($minFitness, array_column($population, 'fitness'));
            $gBest = $population[$indexIndividu];

            $velocities = $this->createInitialVelocities($population);
            // 0. Push velocities into population
            foreach ($population as $key => $particles) {
                $particles[] = $velocities[$key];
                $population[$key] = $particles;
            }
            foreach ($population as &$individu) {
                $individu['velocities'] = $individu[0];
                unset($individu[0]);
            }

            // 1. Prepare initial pBest
            foreach ($population as $key => $particles) {
                $particles[] = [
                    'fitness' => $particles['fitness'],
                    'individu' => $particles['individu']
                ];
                $population[$key] = $particles;
            }
            foreach ($population as &$individu) {
                $individu['pBest'] = $individu[1];
                unset($individu[1]);
            }

            // Rank Based inertia chaotic value (cosine)
            $I = 0;
            $chaotic = new ChaoticFactory;
            $chaoticValue = $chaotic->initializeChaotic('cosines', $this->iter, $I)->chaotic($parameters['maxIteration']);

            // r1 chaotic gauss
            if ($this->algorithm === 'mypso3') {
                $chaoticValue = 0.7;
            }

        } else {
            $minFitness = min(array_column($population, 'pBest'));
            $indexIndividu = array_search($minFitness, array_column($population, 'pBest'));
            $gBest = $population[$indexIndividu];

            //updated I
            $rankBasedInertia = new RankBased($population[0]['I'], $population, '', $popSize);
            $I = $rankBasedInertia->aConstant($parameters['maxIteration']);

            $chaotic = new ChaoticFactory;

            if ($this->algorithm === 'ucpso' || $this->algorithm ==='mypso1'){
                $chaoticValue = $chaotic->initializeChaotic('cosine', $this->iter, $I)->chaotic($parameters['maxIteration']);
            }
            if ($this->algorithm === 'mypso3') {
                $chaoticValue = $chaotic->initializeChaotic('chebyshev', $this->iter, $I)->chaotic($population[0]['chaoticValue']);
            }
        }

        // 2. Update velocity
        foreach ($population as $key => $particles) {
            $vels[] = $this->updateVelocity($particles['individu'], $particles['velocities'], $particles['pBest']['individu'], $gBest['individu'],  $population, $particles, $chaoticValue);
        }

        // 2. Update particles
        foreach ($vels as $key1 => $vel) {
            foreach ($vel as $key2 => $velValue) {
                $vars[] = $velValue + floatval($population[$key1]['individu'][$key2]);
            }
            $updatedParticles[] = $vars;
            $vars = [];
        }

        // 3. Update population
        $data = (new DataProcessor())->initializeDataprocessor('silhavy', 50);
        $testDataset = $data->processingData('Dataset\EffortEstimation\Public\ucp_silhavy.txt');

        foreach ($updatedParticles as $key => $variables) {
            $result = (new Functions())->initializingFunction($function, $testDataset[0]);
            $fitness = $result->runFunction($variables, $function);
            $pops[] = [
                'fitness' => $fitness,
                'individu' => $variables
            ];
        }

        // 4. Update pBest
        foreach ($population as $key => $particles) {
            $particles[] = $I;
            $particles[] = $chaoticValue;
            $population[$key] = $particles;
            if ($particles['fitness'] > $pops[$key]['fitness']) {
                $population[$key]['pBest']['fitness'] = $pops[$key]['fitness'];
                $population[$key]['pBest']['individu'] = $pops[$key]['individu'];
            }
        }
        if ($this->iter === 0) {
            foreach ($population as &$individu) {
                $individu['I'] = $individu[2];
                $individu['chaoticValue'] = $individu[3];
                unset($individu[2]);
                unset($individu[3]);
            }
        }

        if ($this->algorithm === 'mypso1' || $this->algorithm === 'mypso2') {
            $spbest = new SPBest;
            return $spbest->getSPBest($population);
        }

        return $population;
    }
}

class UniformCPSO implements AlgorithmInterface
{
    function __construct($iter, $algorithm)
    {
        $this->iter = $iter;
        $this->algorithm = $algorithm;
    }

    function execute($population, $function, $popSize)
    {
        $pso = new ParticleSwarmOptimizer($this->iter, $this->algorithm);
        return $pso->execute($population, $function, $popSize);
    }
}

## UCPSO + SpBest
class MyPSO1 implements AlgorithmInterface
{
    function __construct($iter, $algorithm)
    {
        $this->iter = $iter;
        $this->algorithm = $algorithm;
    }

    function execute($population, $function, $popSize)
    {
        $mypso = new UniformCPSO($this->iter, $this->algorithm);
        return $mypso->execute($population, $function, $popSize);
    }
}

## PSO + SpBest
class MyPSO2 implements AlgorithmInterface
{
    function __construct($iter, $algorithm)
    {
        $this->iter = $iter;
        $this->algorithm = $algorithm;
    }

    function execute($population, $function, $popSize)
    {
        $mypso = new ParticleSwarmOptimizer($this->iter, $this->algorithm);
        return $mypso->execute($population, $function, $popSize);
    }
}

## PSO + Chaotic r1
class MyPSO3 implements AlgorithmInterface
{
    function __construct($iter, $algorithm)
    {
        $this->iter = $iter;
        $this->algorithm = $algorithm;
    }

    function execute($population, $function, $popSize)
    {
        $mypso = new ParticleSwarmOptimizer($this->iter, $this->algorithm);
        return $mypso->execute($population, $function, $popSize);
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
            return new ParticleSwarmOptimizer($iter, $type);
        }
        if ($type === 'ucpso') {
            return new UniformCPSO($iter, $type);
        }
        if ($type === 'mypso1') {
            return new MyPSO1($iter, $type);
        }
        if ($type === 'mypso2') {
            return new MyPSO2($iter, $type);
        }
        if ($type === 'mypso3') {
            return new MyPSO3($iter, $type);
        }
    }
}
