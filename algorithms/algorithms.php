<?php

use Utils\ChaoticFactory;

interface AlgorithmInterface
{
    function execute($population, $function, $popSize);
}

class Genetic implements AlgorithmInterface
{
    function __construct($testData)
    {
        $this->testData = $testData;
    }

    function RouletteWheelSelection($offsprings, $function, $popSize)
    {
        foreach ($offsprings as $individu) {
            $result = (new Functions())->initializingFunction($function, $this->testData);
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
        $parameters = $local->initializingLocalParameter('ga', '')->getLocalParameter();
        //print_r($population);die;
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
    function __construct($iter, $algorithm, $testData)
    {
        $this->iter = $iter;
        $this->algorithm = $algorithm;
        $this->testData = $testData;
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
        $parameters = $local->initializingLocalParameter('pso', '')->getLocalParameter();

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
            $parameters = $local->initializingLocalParameter('pso', '')->getLocalParameter();
        }
        if ($this->algorithm === 'ucpso' || $this->algorithm === 'mypso1') {
            $parameters = $local->initializingLocalParameter('ucpso', null)->getLocalParameter();
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

            if ($this->algorithm === 'ucpso' || $this->algorithm === 'mypso1') {
                $chaoticValue = $chaotic->initializeChaotic('cosine', $this->iter, $I)->chaotic($parameters['maxIteration']);
            }
            if ($this->algorithm === 'mypso3') {
                $chaoticValue = $chaotic->initializeChaotic('gauss', $this->iter, $I)->chaotic($population[0]['chaoticValue']);
            }
            if ($this->algorithm === 'pso' || $this->algorithm === 'mypso2') {
                $chaoticValue = null;
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
        foreach ($updatedParticles as $key => $variables) {
            $result = (new Functions())->initializingFunction($function, '');
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

class Komodo implements AlgorithmInterface
{
    function __construct($parameters, $variableRanges)
    {
        $this->parameters = $parameters;
        $this->variableRanges = $variableRanges;
    }

    function W_ij($bigMaleI, $bigMaleJ)
    {
        $r1 = (new Randomizers())->randomZeroToOneFraction();
        $r2 = (new Randomizers())->randomZeroToOneFraction();
        if ($bigMaleJ['fitness'] < $bigMaleI || $r2 < 0.5) {
            foreach ($bigMaleJ['individu'] as $key => $val) {
                $w_ij[] = $r1 * ($val - $bigMaleI['individu'][$key]);
            }
        } else {
            foreach ($bigMaleI['individu'] as $key => $val) {
                $w_ij[] = $r1 * ($val - $bigMaleJ['individu'][$key]);
            }
        }

        return $w_ij;
    }

    function updatePosition($currentBigMale, $W_ij)
    {
        foreach ($currentBigMale['individu'] as $key => $val) {
            $newPositions[] = $val + $W_ij[$key];
        }
        return $newPositions;
    }

    function execute($population, $function, $popSize)
    {
        ## FIRST PHASE
        // 0. The winner big male komodo
        $winnerBM = $population[0];

        // 1. high quality big males (HQBM)
        $numOfHQBM = floor((1 - $this->parameters['p1']) * $this->parameters['n1']);
        foreach ($population as $key => $individu) {
            if ($key < $numOfHQBM) {
                $bigMales[] = $individu;
            }
        }

        // 2. Female
        $female = $population[$numOfHQBM];

        // 3. Small males
        $smalles = array_slice($population, $numOfHQBM + 1);

        // 4. Movement of big males
        $w_ij = [];
        $lastBigmales = $bigMales;
        $bigMales = [];

        $result = (new Functions())->initializingFunction($function, '');
        foreach ($lastBigmales as $key1 => $bigMaleI) {
            foreach ($lastBigmales as $key2 => $bigMaleJ) {
                if ($key1 !== $key2) {
                    $w_ij[] = $this->W_ij($bigMaleI, $bigMaleJ);
                }
            }

            ## n = 5, q = 2 dulu ya guys...
            if (count($w_ij) === 1){
                foreach ($w_ij as $vals) {
                    $newPositions = $this->updatePosition($bigMaleI, $vals);
                    $fitness = $result->runFunction($newPositions, $function);
                    $bigMales[] = [
                        'fitness' => $fitness,
                        'individu' => $vals
                    ];
                }
            }

            ## n = 5, q = 2 dulu ya guys...
            if (count($w_ij) === 2){
               for ($i = 0; $i < count($w_ij[0]); $i++){
                    $sumRows[] = $w_ij[0][$i] + $w_ij[1][$i];
               }
                $newPositions = $this->updatePosition($bigMaleI, $sumRows);
                $fitness = $result->runFunction($newPositions, $function);
                $bigMales[] = [
                    'fitness' => $fitness,
                    'individu' => $sumRows
                ];
               $sumRows = [];
            }
            $w_ij = [];
        }
        sort($bigMales);
        print_r($bigMales);die;
        die;

        // 5. Female reproduction
        // Fixed probability 0f 0.5 (if 0 = exploitation, if 1 = exploration)
        $prob = rand() & 1;
        if ($prob === 0) {
            // 5.1. Sexual Reproduction (produce two offsprings)
            //      k_il_new = r1 * k_il + (1 - r1) * k_jl
            //      k_jl_new = r1 * k_jl + (1 - r1) * k_il
            //      k_il & k_jl = dimensi ke-l dari komodo big-male terbaik dengan komodo female
            for ($i = 0; $i <= 1; $i++) {
                foreach ($winnerBM['individu'] as $key => $val) {
                    $r1 = (new Randomizers())->randomZeroToOneFraction();
                    $offspring[] = $r1 * $val + (1 - $r1) * $female['individu'][$key];
                }
                $offsprings[] = $offspring;
                $offspring = [];
            }
            $tempOffsprings = $offsprings;
            $offsprings = [];
            foreach ($tempOffsprings as $key => $variables) {
                $result = (new Functions())->initializingFunction($function, '');
                $fitness = $result->runFunction($variables, $function);
                $offsprings[] = [
                    'fitness' => $fitness,
                    'individu' => $variables
                ];
            }

            // 5.2. update the female
            sort($offsprings);
            if ($female['fitness'] > $offsprings[0]['fitness']) {
                $female = $offsprings[0];
            }
        }
        if ($prob === 1) {
            // 5.3. exploration by doing asexual reproduction (parthenogenesis)
            // 5.3.1 appending a small value to teach female dimension
            //       k_ij_new = k_ij + (2r - 1)alpha|ub_j - lb_j|
            //       alpha = the radius of parthenogenesis (0.1)
            $alpha = 0.1;
            foreach ($female['individu'] as $val) {
                $r = (new Randomizers())->randomZeroToOneFraction();
                $vals[] = $val + ((2 * $r) - 1) * $alpha * abs($this->variableRanges[0]['upperBound'] - $this->variableRanges[0]['lowerBound']);
            }
            $result = (new Functions())->initializingFunction($function, '');
            $fitness = $result->runFunction($vals, $function);
            $female = [];
            $female['fitness'] = $fitness;
            $female['individu'] = $vals;
        }

        print_r($female);die;

        // 6. Movement of small males
        // Randomly selecting a part of dimension with a particular probability (mlipir rate)
        $w_ij = [];
        $tempDim = 0;
        foreach ($smalles as $smallMale) {
            foreach ($smallMale['individu'] as $key => $val) {
                $r1 = (new Randomizers())->randomZeroToOneFraction();
                $r2 = (new Randomizers())->randomZeroToOneFraction();
                if ($r2 < $this->parameters['d1']) {
                    foreach ($HQBM as $bigMale) {
                        $tempDim = $tempDim + ($r1 * ($bigMale[$key] - $val));
                    }
                } else {
                    $tempDim = 0;
                }
            }
            $w_ij[] = $tempDim;
            $tempDim = 0;
        }
        print_r($w_ij);
        die;
        die;
    }
}

class UniformCPSO implements AlgorithmInterface
{
    function __construct($iter, $algorithm, $testData)
    {
        $this->iter = $iter;
        $this->algorithm = $algorithm;
        $this->testData = $testData;
    }

    function execute($population, $function, $popSize)
    {
        $pso = new ParticleSwarmOptimizer($this->iter, $this->algorithm, $this->testData);
        return $pso->execute($population, $function, $popSize);
    }
}

## UCPSO + SpBest
class MyPSO1 implements AlgorithmInterface
{
    function __construct($iter, $algorithm, $tesData)
    {
        $this->iter = $iter;
        $this->algorithm = $algorithm;
        $this->testData = $tesData;
    }

    function execute($population, $function, $popSize)
    {
        $mypso = new UniformCPSO($this->iter, $this->algorithm, $this->testData);
        return $mypso->execute($population, $function, $popSize);
    }
}

## PSO + SpBest
class MyPSO2 implements AlgorithmInterface
{
    function __construct($iter, $algorithm, $testData)
    {
        $this->iter = $iter;
        $this->algorithm = $algorithm;
        $this->testData = $testData;
    }

    function execute($population, $function, $popSize)
    {
        $mypso = new ParticleSwarmOptimizer($this->iter, $this->algorithm, $this->testData);
        return $mypso->execute($population, $function, $popSize);
    }
}

## PSO + Chaotic r1
class MyPSO3 implements AlgorithmInterface
{
    function __construct($iter, $algorithm, $testData)
    {
        $this->iter = $iter;
        $this->algorithm = $algorithm;
        $this->testData = $testData;
    }

    function execute($population, $function, $popSize)
    {
        $mypso = new ParticleSwarmOptimizer($this->iter, $this->algorithm, $this->testData);
        return $mypso->execute($population, $function, $popSize);
    }
}

class Algorithms
{
    function __construct($kmaParameters, $kmaVarRanges)
    {
        $this->kmaParameters = $kmaParameters;
        $this->kmaVarRanges = $kmaVarRanges;
    }

    function initilizingAlgorithm($type, $iter, $testData)
    {
        if ($type === 'ga') {
            return new Genetic($testData);
        }
        if ($type === 'pso') {
            return new ParticleSwarmOptimizer($iter, $type, $testData);
        }
        if ($type === 'ucpso') {
            return new UniformCPSO($iter, $type, $testData);
        }
        if ($type === 'mypso1') {
            return new MyPSO1($iter, $type, $testData);
        }
        if ($type === 'mypso2') {
            return new MyPSO2($iter, $type, $testData);
        }
        if ($type === 'mypso3') {
            return new MyPSO3($iter, $type, $testData);
        }
        if ($type === 'komodo') {
            return new Komodo($this->kmaParameters, $this->kmaVarRanges);
        }
    }
}
