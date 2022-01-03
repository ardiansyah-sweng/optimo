<?php

/**
 * Zhang J, Sheng J, Lu J, Shen L. UCPSO: A Uniform Initialized Particle Swarm Optimization Algorithm with Cosine Inertia Weight. Gastaldo P, editor. Comput Intell Neurosci [Internet]. 2021 Mar 18;2021:1–18. Available from: https://www.hindawi.com/journals/cin/2021/8819333/
 */
class UniformInitialization
{
    function __construct(array $variableRanges, int $popSize, string $generateType, array $population, int $numOfVariable, string $function)
    {
        $this->variableRanges = $variableRanges;
        $this->popSize = $popSize;
        $this->generateType = $generateType;
        $this->population = $population;
        $this->numOfVariable = $numOfVariable;
        $this->function = $function;
    }

    function randomVariables($variableRanges): array
    {
        for ($i = 0; $i < $this->numOfVariable; $i++) {
            if ($this->function === 'ucp') {
                $ret[] = mt_rand($variableRanges[$i]['lowerBound'] * 100, $variableRanges[$i]['upperBound'] * 100) / 100;
            } else {
                foreach ($variableRanges as $range) {
                    $ret[] = mt_rand($range['lowerBound'] * 100, $range['upperBound'] * 100) / 100;
                }
            }
        }
        return $ret;
    }

    function createUniformVariable($X1, $r, $variableRanges)
    {
        for ($i = 0; $i < $this->numOfVariable; $i++) {
            if ($this->function === 'ucp') {
                $uniformVariables[] = $X1[$i] + floatval($r[$i]) / $this->popSize * ($variableRanges[$i]['upperBound'] - $variableRanges[$i]['lowerBound']);
            } else {
                foreach ($variableRanges as $range) {
                    $uniformVariables[] = $X1[$i] + floatval($r[$i]) / $this->popSize * ($range['upperBound'] - $range['lowerBound']);
                }
            }
        }

        if ($this->function === 'ucp') {
            $evaluateVariable = new ExcessLimit;
            return $evaluateVariable->cutVariableLimit($this->function, $uniformVariables);
        }
        return $uniformVariables;
    }

    function adjustingUniformVariables($uniformVariables, $variableRanges)
    {
        foreach ($uniformVariables as $key => $variable) {
            foreach ($variableRanges as $range) {
                if ($variable > $range['upperBound']) {
                    $variables[$key] = $variable - ($range['upperBound'] - $range['lowerBound']);
                } else {
                    $variables[$key] = $variable;
                }
            }
        }

        if ($this->function === 'ucp') {
            $evaluateVariable = new ExcessLimit;
            return $evaluateVariable->cutVariableLimit($this->function, $variables);
        }

        return $variables;
    }

    function initializingPopulation()
    {
        // 1. Bangkitkan satu nilai acak dari rentang variabel
        $X1 = $this->randomVariables($this->variableRanges);

        // 2. Bangkitkan nilai acak sebanyak ukuran populasi
        if ($this->generateType === 'random') {
            for ($i = 1; $i <= $this->popSize; $i++) {
                $R[$i] = $this->randomVariables($this->variableRanges);
            }
        }

        if ($this->generateType === 'seeds') {
            $R = $this->population;
        }

        foreach ($R as $key => $r) {
            $uniformVariables = $this->createUniformVariable($X1, $r, $this->variableRanges);

            $adjustedUniformVariables = $this->adjustingUniformVariables($uniformVariables, $this->variableRanges);

            if (($key - 1) == 0) {
                $ret[] = $X1;
            } else {
                $ret[] = $adjustedUniformVariables;
            }
        }
        
        return $ret;
    }
}
