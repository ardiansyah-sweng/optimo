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
    function __construct($parameters, $variableRanges, $testData)
    {
        $this->parameters = $parameters;
        $this->variableRanges = $variableRanges;
        $this->testData = $testData;
    }

    function W_ij($bigMaleI, $bigMaleJ)
    {
        if (count($bigMaleI) === 1) {
            $bigMaleI = $bigMaleI[0];
        }
        if (count($bigMaleJ) === 1) {
            $bigMaleJ = $bigMaleJ[0];
        }
        $r1 = (new Randomizers())->randomZeroToOneFraction();
        $r2 = (new Randomizers())->randomZeroToOneFraction();
        if ($bigMaleJ['fitness'] < $bigMaleI || $r2 < 0.5) {
            foreach ($bigMaleJ['individu'] as $key => $val) {
                $w_ij[] = $r1 * (floatval($val) - floatval($bigMaleI['individu'][$key]));
            }
        } else {
            foreach ($bigMaleI['individu'] as $key => $val) {
                $w_ij[] = $r1 * ($val - $bigMaleJ['individu'][$key]);
            }
        }

        return $w_ij;
    }

    function W_ij_SmallMale($smallMaleI, $smallMaleJ)
    {
        if (count($smallMaleI) === 1) {
            $smallMaleI = $smallMaleI[0];
        }
        if (count($smallMaleJ) === 1) {
            $smallMaleJ = $smallMaleJ[0];
        }

        $r1 = (new Randomizers())->randomZeroToOneFraction();
        $r2 = (new Randomizers())->randomZeroToOneFraction();
        if ($r2 < $this->parameters['d1']) {
            foreach ($smallMaleJ['individu'] as $key => $valJ) {
                $temp[] = $r1 * (floatval($valJ) - floatval($smallMaleI['individu'][$key]));
            }
            $w_ij = array_sum($temp);
        } else {
            $w_ij = 0;
        }
        return $w_ij;
    }

    function updatePosition($currentBigMale, $W_ij)
    {
        if (count($currentBigMale) === 1) {
            $currentBigMale = $currentBigMale[0];
        }

        foreach ($currentBigMale['individu'] as $key => $val) {
            $newPositions[] = floatval($val) + floatval($W_ij[$key]);
        }
        return $newPositions;
    }

    function updatePositionSmall($currentSmallMale, $W_ij)
    {
        foreach ($currentSmallMale['individu'] as $val) {
            $newPositions[] = floatval($val) + $W_ij;
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
        $female[] = $population[$numOfHQBM];

        // 3. Small males
        $smalles = array_slice($population, $numOfHQBM + 1);

        // 4. Movement of big males
        $w_ij = [];
        $lastBigmales = $bigMales;

        $bigMales = [];
        $evaluateVariable = new ExcessLimit;

        if ($function === 'ucp') {
            $result = (new Functions())->initializingFunction($function, $this->testData);
        } else {
            $result = (new Functions())->initializingFunction($function, '');
        }

        foreach ($lastBigmales as $key1 => $bigMaleI) {
            foreach ($lastBigmales as $key2 => $bigMaleJ) {
                if ($key1 !== $key2) {
                    $w_ij[] = $this->W_ij($bigMaleI, $bigMaleJ);
                }
            }

            if (count($w_ij) === 1) {
                foreach ($w_ij as $vals) {
                    $newPositions = $this->updatePosition($bigMaleI, $vals);

                    if ($function === 'ucp') {
                        $tempVar = $newPositions;
                        $newPositions = [];
                        $newPositions = $evaluateVariable->cutVariableLimit($function, $tempVar);
                        $tempVar = [];
                    }

                    $fitness = $result->runFunction($newPositions, $function);

                    $bigMales[] = [
                        'fitness' => $fitness,
                        'individu' => $vals
                    ];
                }
            }

            if (count($w_ij) === 2) {
                for ($i = 0; $i < count($w_ij[0]); $i++) {
                    $sumRows[] = $w_ij[0][$i] + $w_ij[1][$i];
                }

                if ($function === 'ucp') {
                    $tempVar = $sumRows;
                    $sumRows = [];
                    $sumRows = $evaluateVariable->cutVariableLimit($function, $tempVar);
                }

                $newPositions = $this->updatePosition($bigMaleI, $sumRows);
                $fitness = $result->runFunction($newPositions, $function);
                //print_r($newPositions);
                $bigMales[] = [
                    'fitness' => $fitness,
                    'individu' => $sumRows
                ];
                $sumRows = [];
            }

            if (count($w_ij) > 2) {
                echo count($w_ij) . ' Not available yet...';
                die;
            }

            $w_ij = [];
        }
        sort($bigMales);

        // 5. Female reproduction
        // Fixed probability 0f 0.5 (if 0 = exploitation, if 1 = exploration)
        $prob = rand() & 1;
        if ($prob === 0) {
            if (count($winnerBM) === 1) {
                $winnerBM = $winnerBM[0];
            }
            // 5.1. Sexual Reproduction (produce two offsprings)
            //      k_il_new = r1 * k_il + (1 - r1) * k_jl
            //      k_jl_new = r1 * k_jl + (1 - r1) * k_il
            //      k_il & k_jl = dimensi ke-l dari komodo big-male terbaik dengan komodo female
            for ($i = 0; $i <= 1; $i++) {
                foreach ($winnerBM['individu'] as $key => $val) {
                    $r1 = (new Randomizers())->randomZeroToOneFraction();
                    $offspring[] = $r1 * floatval($val) + (1 - $r1) * floatval($female[0]['individu'][$key]);
                }
                $offsprings[] = $offspring;
                $offspring = [];
            }

            $tempOffsprings = $offsprings;
            $offsprings = [];
            foreach ($tempOffsprings as $key => $variables) {
                if ($function === 'ucp') {
                    $result = (new Functions())->initializingFunction($function, $this->testData);

                    $tempVar = $variables;
                    $variables = [];
                    $variables = $evaluateVariable->cutVariableLimit($function, $tempVar);
                    $tempVar = [];
                } else {
                    $result = (new Functions())->initializingFunction($function, '');
                }

                $fitness = $result->runFunction($variables, $function);
                $offsprings[] = [
                    'fitness' => $fitness,
                    'individu' => $variables
                ];
            }

            // 5.2. update the female
            sort($offsprings);

            $lastFemale = $female;
            $female = null;

            if ($lastFemale[0]['fitness'] > $offsprings[0]['fitness']) {
                $female[] = $offsprings[0];
            } else {
                $female[] = $lastFemale;
            }
        }
        if ($prob === 1) {
            // 5.3. exploration by doing asexual reproduction (parthenogenesis)
            // 5.3.1 appending a small value to teach female dimension
            //       k_ij_new = k_ij + (2r - 1)alpha|ub_j - lb_j|
            //       alpha = the radius of parthenogenesis (0.1)
            $alpha = 0.1;
            foreach ($female[0]['individu'] as $key => $val) {
                $r = (new Randomizers())->randomZeroToOneFraction();
                $results[] = floatval($val) + ((2 * $r) - 1) * $alpha * abs($this->variableRanges[0]['upperBound'] - $this->variableRanges[0]['lowerBound']);
            }
            if ($function === 'ucp') {
                $result = (new Functions())->initializingFunction($function, $this->testData);

                $tempVar = $results;
                $results = [];
                $results = $evaluateVariable->cutVariableLimit($function, $tempVar);
            } else {
                $result = (new Functions())->initializingFunction($function, '');
            }
            $fitness = $result->runFunction($results, $function);
            $female = [];
            $female[] = [
                'fitness' => $fitness,
                'individu' => $results
            ];
        }

        // 6. Movement of small males
        // Randomly selecting a part of dimension with a particular probability (mlipir rate)
        $w_ij = [];

        $lastSmalles = $smalles;
        $smalles = [];
        foreach ($lastSmalles as $key1 => $smallMaleI) {
            foreach ($lastSmalles as $key2 => $smallMaleJ) {
                if ($key1 !== $key2) {
                    $w_ij = $this->W_ij_SmallMale($smallMaleI, $smallMaleJ);
                }
            }
            $newPositions = $this->updatePositionSmall($smallMaleI, $w_ij);
            if ($function === 'ucp') {
                $tempVars = $newPositions;
                $newPositions = [];
                $newPositions = $evaluateVariable->cutVariableLimit($function, $tempVars);
            }

            $fitness = $result->runFunction($newPositions, $function);
            $smalles[] = [
                'fitness' => $fitness,
                'individu' => $newPositions
            ];
        }
        sort($smalles);

        $population = [];

        $population = array_merge($bigMales, $female, $smalles);
        sort($population);


        return $population;
    }
}

class Reptile implements AlgorithmInterface
{
    function __construct($parameters, $iter, $varRanges, $testData)
    {
        $this->parameters = $parameters;
        $this->iter = $iter;
        $this->varRanges = $varRanges;
        $this->testData = $testData;
    }

    function execute($population, $function, $popSize)
    {
        // 1. Find the best solution so far
        $bestReptile = $population[0];

        // 2. Update ES (evolutionary sense)
        $ES = (new EvolutionarySense())->es($this->parameters['maxIteration']);

        // 3. Update
        $huntingOperator = new HuntingOperator($this->varRanges[0]);
        $eta = $huntingOperator->eta($bestReptile, $population);

        // 4. Update Reduce function
        $reduction = (new HuntingOperator($this->varRanges[0]))->reduce($bestReptile, $population);

        // 5. High walking
        if ($this->iter <= ($this->parameters['maxIteration'] / 4)) {
            $positions = (new WalkingMovementStrategy())->highWalking($bestReptile, $eta, $reduction);
        }

        // 5. Belly walking
        else if ($this->iter <= 2 * ($this->parameters['maxIteration'] / 4) && $this->iter > ($this->parameters['maxIteration'] / 4)) {
            $positions = (new WalkingMovementStrategy())->bellyWalking($bestReptile, $population, $ES);
        }

        // 5. Hunting coordination
        else if ($this->iter <= 3 * ($this->parameters['maxIteration'] / 4) && $this->iter > 2 * ($this->parameters['maxIteration']) / 4) {
            $percentageDiff = $huntingOperator->percentageDiffefence($bestReptile, $population);
            $positions = (new WalkingMovementStrategy())->huntingCoordination($bestReptile, $percentageDiff);
        }

        // 5. Hunting cooperation
        else {
            $positions = (new WalkingMovementStrategy())->huntingCooperation($bestReptile, $eta, $reduction);
        }

        if ($function === 'ucp') {
            $result = (new Functions())->initializingFunction($function, $this->testData);
        } else {
            $result = (new Functions())->initializingFunction($function, '');
        }

        $population = [];
        foreach ($positions as $position) {
            $population[] = [
                'fitness' => $result->runFunction($position, $function),
                'individu' => $position
            ];
        }

        sort($population);
        return $population;
    }
}

class Wolf implements AlgorithmInterface
{
    function __construct($parameters, $iter, $varRanges, $testData)
    {
        $this->parameters = $parameters;
        $this->iter = $iter;
        $this->varRanges = $varRanges;
        $this->testData = $testData;
    }

    function execute($population, $function, $popSize)
    {
        $alphaWolf = $population[0];
        $betaWolf = $population[1];
        $deltaWolf = $population[2];
        $omegaWolf = array_slice($population, 3);

        // 1. Encircling prey
        $a = 2 - (2 * $this->iter) / $this->parameters['maxIteration'];
        
        $A_alphaWolf = $alphaWolf['individu'];
        $A_betaWolf = $betaWolf['individu'];
        $A_deltaWolf = $deltaWolf['individu'];

        $alphaWolfPositions = (new PreyHunting($this->varRanges))->hunting($alphaWolf, $omegaWolf, $A_alphaWolf, $a);
        $betaWolfPositions = (new PreyHunting($this->varRanges))->hunting($betaWolf, $omegaWolf, $A_betaWolf, $a);
        $deltaWolfPositions = (new PreyHunting($this->varRanges))->hunting($deltaWolf, $omegaWolf, $A_deltaWolf, $a);

        if ($function === 'ucp') {
            $result = (new Functions())->initializingFunction($function, $this->testData);
        } else {
            $result = (new Functions())->initializingFunction($function, '');
        }

        $population = [];
        foreach ($alphaWolfPositions as $key1 => $position) {
            foreach ($position as $key2 => $val) {
                $positions[] = ($val + $betaWolfPositions[$key1][$key2] + $deltaWolfPositions[$key1][$key2]) / 3;
            }
            $population[] = [
                'fitness' => $result->runFunction($positions, $function),
                'individu' => $positions
            ];
            $positions = [];
        }
        sort($population);
        print_r($population);
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
    function __construct($parameters, $kmaVarRanges)
    {
        $this->parameters = $parameters;
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
            return new Komodo($this->parameters, $this->kmaVarRanges, $testData);
        }
        if ($type === 'reptile') {
            return new Reptile($this->parameters, $iter, $this->kmaVarRanges, $testData);
        }
        if ($type === 'wolf') {
            return new Wolf($this->parameters, $iter, $this->kmaVarRanges, $testData);
        }
    }
}
